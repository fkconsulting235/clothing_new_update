<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('items.variant.product');

        $status = $request->get('status', '');
        if ($status) {
            $orders->where('status', $status);
        }

        $orders = $orders->orderByDesc('created_at')->get();

        $statusChoices = [
            'pending' => 'Pendiente',
            'paid' => 'Pagado',
            'cancelled' => 'Cancelado',
        ];

        return view('admin.orders.index', [
            'orders' => $orders,
            'statusChoices' => $statusChoices,
            'selectedStatus' => $status,
        ]);
    }

    public function markPaid(Order $order)
    {
        self::doMarkPaid($order);

        return redirect()->route('admin.orders.index')->with('success', "Pedido #{$order->id} marcado como pagado.");
    }

    public static function doMarkPaid(Order $order): void
    {
        if ($order->status === 'paid') {
            return;
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'paid']);

            foreach ($order->items as $item) {
                $variant = ProductVariant::where('id', $item->variant_id)->lockForUpdate()->first();
                $variant->update(['stock' => max($variant->stock - $item->quantity, 0)]);
            }

            if ($order->reservation_id) {
                Reservation::where('id', $order->reservation_id)->update(['status' => 'converted']);
            }
        });
    }

    public function undoPaid(Order $order)
    {
        if ($order->status !== 'paid') {
            return redirect()->route('admin.orders.index');
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'pending']);

            foreach ($order->items as $item) {
                $variant = ProductVariant::where('id', $item->variant_id)->lockForUpdate()->first();
                $variant->update(['stock' => $variant->stock + $item->quantity]);
            }

            if ($order->reservation_id) {
                Reservation::where('id', $order->reservation_id)->where('status', 'converted')->update(['status' => 'active']);
            }
        });

        return redirect()->route('admin.orders.index')->with('success', "Pedido #{$order->id} regresado a pendiente.");
    }

    public function reject(Order $order)
    {
        $order->update(['status' => 'cancelled']);

        return redirect()->route('admin.orders.index')->with('success', "Pedido #{$order->id} rechazado.");
    }
}
