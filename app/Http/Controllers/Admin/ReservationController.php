<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $reservations = Reservation::with('variant.product');

        $status = $request->get('status', '');
        if ($status) {
            $reservations->where('status', $status);
        }

        $reservations = $reservations->orderByDesc('created_at')->get();

        $statusChoices = [
            'active' => 'Activa',
            'expired' => 'Vencida',
            'converted' => 'Convertida en pedido',
            'cancelled' => 'Cancelada',
        ];

        return view('admin.reservations.index', [
            'reservations' => $reservations,
            'statusChoices' => $statusChoices,
            'selectedStatus' => $status,
        ]);
    }

    public function cancel(Reservation $reservation)
    {
        $reservation->update(['status' => 'cancelled']);

        return redirect()->route('admin.reservations.index')
            ->with('success', "Reserva de {$reservation->customer_name} cancelada. La prenda ya vuelve a estar disponible.");
    }
}
