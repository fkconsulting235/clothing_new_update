<?php

namespace App\Http\Controllers;

use App\Mail\NewOrderNotification;
use App\Models\BankAccount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Reservation;
use App\Services\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function checkout(Request $request, ProductVariant $variant)
    {
        $reservationId = $request->input('reservation_id');
        $reservation = null;
        if ($reservationId) {
            $reservation = Reservation::where('id', $reservationId)
                ->where('variant_id', $variant->id)
                ->where('status', 'active')
                ->firstOrFail();
        }

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'customer_name' => 'required|string|max:120',
                'customer_phone' => 'required|string|max:30',
                'customer_email' => 'nullable|email',
                'quantity' => 'nullable|integer|min:1',
            ]);
            $quantity = $reservation ? $reservation->quantity : (int) ($data['quantity'] ?? 1);

            return DB::transaction(function () use ($data, $quantity, $variant, $reservation) {
                $locked = ProductVariant::where('id', $variant->id)->lockForUpdate()->first();

                if (! $reservation && $quantity > $locked->available_stock) {
                    return back()->withErrors(['quantity' => 'Ya no hay suficiente disponibilidad para esa cantidad.'])->withInput();
                }

                $order = Order::create([
                    'customer_name' => $data['customer_name'],
                    'customer_phone' => $data['customer_phone'],
                    'customer_email' => $data['customer_email'] ?? null,
                    'total' => number_format(round((float) $locked->price * $quantity, 2), 2, '.', ''),
                    'reservation_id' => $reservation?->id,
                ]);

                OrderItem::create([
                    'order_id' => $order->id,
                    'variant_id' => $locked->id,
                    'quantity' => $quantity,
                    'unit_price' => $locked->price,
                ]);

                return redirect()->route('orders.pay', $order);
            });
        }

        return view('orders.checkout', compact('variant', 'reservation'));
    }

    public function pay(Request $request, Order $order)
    {
        $bankAccounts = BankAccount::where('is_active', true)->orderBy('order')->orderBy('bank_name')->get();

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'payment_proof' => 'required|image|max:5120',
            ]);

            $path = $data['payment_proof']->store('comprobantes/'.now()->format('Y/m'), 'public');
            $order->update(['payment_proof_path' => $path]);

            $notifyEmail = config('store.notification_email');
            if ($notifyEmail) {
                try {
                    Mail::to($notifyEmail)->send(new NewOrderNotification($order));
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            return redirect()->route('orders.pending-review', $order);
        }

        return view('orders.pay', compact('order', 'bankAccounts'));
    }

    public function pendingReview(Order $order)
    {
        return view('orders.pending-review', compact('order'));
    }

    public function cartAdd(Request $request, ProductVariant $variant, Cart $cart)
    {
        $quantity = max((int) $request->input('quantity', 1), 1);

        if ($quantity > $variant->available_stock) {
            return back()->with('error', 'Ya no hay suficiente disponibilidad para esa cantidad.');
        }

        $cart->add($variant->id, $quantity);

        return redirect()->route('catalog.product', $variant->product)
            ->with('success', "{$variant->product->name} ({$variant->color}/{$variant->size}) agregado al carrito.");
    }

    public function cartDetail(Request $request, Cart $cart)
    {
        if ($request->isMethod('post')) {
            $data = $request->validate([
                'customer_name' => 'required|string|max:120',
                'customer_phone' => 'required|string|max:30',
                'customer_email' => 'nullable|email',
            ]);

            $lines = $cart->lines();
            if (empty($lines)) {
                return back()->with('error', 'Tu carrito está vacío.');
            }

            return DB::transaction(function () use ($data, $lines, $cart) {
                $variantIds = collect($lines)->pluck('variant.id')->sort()->values();
                $locked = ProductVariant::whereIn('id', $variantIds)->lockForUpdate()->get()->keyBy('id');

                $insufficient = collect($lines)->filter(
                    fn ($line) => $line['quantity'] > $locked[$line['variant']->id]->available_stock
                );

                if ($insufficient->isNotEmpty()) {
                    $names = $insufficient->map(fn ($l) => $l['variant']->product->name)->implode(', ');

                    return back()->with('error', "Ya no hay suficiente disponibilidad para: {$names}.");
                }

                $order = Order::create([
                    'customer_name' => $data['customer_name'],
                    'customer_phone' => $data['customer_phone'],
                    'customer_email' => $data['customer_email'] ?? null,
                    'total' => $cart->total(),
                ]);

                foreach ($lines as $line) {
                    $lockedVariant = $locked[$line['variant']->id];
                    OrderItem::create([
                        'order_id' => $order->id,
                        'variant_id' => $lockedVariant->id,
                        'quantity' => $line['quantity'],
                        'unit_price' => $lockedVariant->price,
                    ]);
                }

                $cart->clear();

                return redirect()->route('orders.pay', $order);
            });
        }

        return view('orders.cart', ['lines' => $cart->lines(), 'total' => $cart->total()]);
    }

    public function cartUpdate(Request $request, int $variantId, Cart $cart)
    {
        $quantity = (int) $request->input('quantity', 1);
        $cart->update($variantId, $quantity);

        return redirect()->route('orders.cart');
    }

    public function cartRemove(int $variantId, Cart $cart)
    {
        $cart->remove($variantId);

        return redirect()->route('orders.cart');
    }
}
