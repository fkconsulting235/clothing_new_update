<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function create(Request $request, ProductVariant $variant)
    {
        if ($request->isMethod('post')) {
            $data = $request->validate([
                'customer_name' => 'required|string|max:120',
                'customer_phone' => 'required|string|max:30',
                'quantity' => 'required|integer|min:1',
            ]);

            return DB::transaction(function () use ($data, $variant) {
                $locked = ProductVariant::where('id', $variant->id)->lockForUpdate()->first();

                if ($data['quantity'] > $locked->available_stock) {
                    return back()->withErrors(['quantity' => 'Ya no hay suficiente disponibilidad para esa cantidad.'])->withInput();
                }

                $reservation = Reservation::create([
                    'variant_id' => $locked->id,
                    'customer_name' => $data['customer_name'],
                    'customer_phone' => $data['customer_phone'],
                    'quantity' => $data['quantity'],
                ]);

                return redirect()->route('reservations.confirmation', $reservation);
            });
        }

        return view('reservations.form', compact('variant'));
    }

    public function confirmation(Reservation $reservation)
    {
        $reservation->load('variant.product');

        return view('reservations.confirmation', compact('reservation'));
    }

    public function lookup(Request $request)
    {
        $phone = trim((string) $request->get('phone', ''));
        $reservations = collect();
        $searched = $phone !== '';

        if ($searched) {
            $reservations = Reservation::where('customer_phone', $phone)
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->with('variant.product')
                ->get();
        }

        return view('reservations.lookup', compact('phone', 'reservations', 'searched'));
    }
}
