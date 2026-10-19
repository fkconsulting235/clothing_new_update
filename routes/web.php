<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

// Tienda pública
Route::get('/', [CatalogController::class, 'home'])->name('catalog.home');
Route::get('/buscar', [CatalogController::class, 'search'])->name('catalog.search');
Route::get('/tallas', [CatalogController::class, 'bySize'])->name('catalog.by-size');
Route::get('/categoria/{category:slug}', [CatalogController::class, 'category'])->name('catalog.category');
Route::get('/prenda/{product:slug}', [CatalogController::class, 'product'])->name('catalog.product');

// Reservas
Route::match(['get', 'post'], '/reservar/{variant}', [ReservationController::class, 'create'])->name('reservations.create');
Route::get('/reservas/confirmacion/{reservation}', [ReservationController::class, 'confirmation'])->name('reservations.confirmation');
Route::get('/reservas/buscar', [ReservationController::class, 'lookup'])->name('reservations.lookup');

// Pedidos / pago / carrito
Route::match(['get', 'post'], '/comprar/{variant}', [OrderController::class, 'checkout'])->name('orders.checkout');
Route::match(['get', 'post'], '/pedidos/{order}/pagar', [OrderController::class, 'pay'])->name('orders.pay');
Route::get('/pedidos/{order}/en-revision', [OrderController::class, 'pendingReview'])->name('orders.pending-review');
Route::match(['get', 'post'], '/carrito', [OrderController::class, 'cartDetail'])->name('orders.cart');
Route::post('/carrito/agregar/{variant}', [OrderController::class, 'cartAdd'])->name('orders.cart.add');
Route::post('/carrito/actualizar/{variant}', [OrderController::class, 'cartUpdate'])->name('orders.cart.update');
Route::post('/carrito/quitar/{variant}', [OrderController::class, 'cartRemove'])->name('orders.cart.remove');

require __DIR__.'/admin.php';
