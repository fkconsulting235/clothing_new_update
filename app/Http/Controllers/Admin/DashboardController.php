<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Reservation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $paidOrders = Order::where('status', 'paid');
        $today = now()->toDateString();

        $pendingOrders = Order::where('status', 'pending')
            ->whereNotNull('payment_proof_path')
            ->with('items.variant.product')
            ->latest()
            ->take(5)
            ->get();

        $expiringReservations = Reservation::where('status', 'active')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<', now()->addHours(6))
            ->with('variant.product')
            ->orderBy('expires_at')
            ->take(5)
            ->get();

        $lowStock = ProductVariant::whereHas('product', fn ($q) => $q->where('is_active', true))
            ->where('stock', '<=', 2)
            ->with('product')
            ->orderBy('stock')
            ->take(6)
            ->get();

        $week = collect(range(6, 0))->map(function ($daysAgo) {
            $day = now()->subDays($daysAgo);

            return [
                'label' => $day->translatedFormat('D'),
                'total' => (float) Order::where('status', 'paid')->whereDate('created_at', $day->toDateString())->sum('total'),
            ];
        });

        return view('admin.dashboard', [
            'pendingProofCount' => Order::where('status', 'pending')->whereNotNull('payment_proof_path')->count(),
            'pendingOrders' => $pendingOrders,
            'expiringReservations' => $expiringReservations,
            'lowStock' => $lowStock,
            'week' => $week,
            'totalProducts' => Product::where('is_active', true)->count(),
            'totalCategories' => Category::where('is_active', true)->count(),
            'activeReservations' => Reservation::where('status', 'active')->where('expires_at', '>', now())->count(),
            'paidOrdersCount' => (clone $paidOrders)->count(),
            'salesToday' => (clone $paidOrders)->whereDate('created_at', $today)->sum('total'),
            'salesMonth' => (clone $paidOrders)->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->sum('total'),
            'salesTotal' => (clone $paidOrders)->sum('total'),
        ]);
    }

    public function sales(Request $request)
    {
        $orders = Order::where('status', 'paid')->with('items.variant.product');

        $period = $request->get('period', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        $today = now();

        if ($dateFrom) {
            $orders->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $orders->whereDate('created_at', '<=', $dateTo);
        }

        if (! $dateFrom && ! $dateTo) {
            if ($period === 'today') {
                $orders->whereDate('created_at', $today->toDateString());
            } elseif ($period === 'week') {
                $orders->where('created_at', '>=', $today->copy()->startOfWeek());
            } elseif ($period === 'month') {
                $orders->whereYear('created_at', $today->year)->whereMonth('created_at', $today->month);
            }
        }

        $orders = $orders->orderByDesc('created_at')->get();

        return view('admin.sales', [
            'orders' => $orders,
            'period' => $period,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'totalAmount' => $orders->sum('total'),
            'totalCount' => $orders->count(),
        ]);
    }
}
