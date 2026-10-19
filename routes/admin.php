<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReservationController;
use Illuminate\Support\Facades\Route;

Route::prefix('panel')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('staff')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('home');
        Route::get('/ventas', [DashboardController::class, 'sales'])->name('sales');

        Route::get('/prendas', [ProductController::class, 'index'])->name('products.index');
        Route::get('/prendas/nueva', [ProductController::class, 'create'])->name('products.create');
        Route::post('/prendas/nueva', [ProductController::class, 'store'])->name('products.store');
        Route::get('/prendas/{product}/editar', [ProductController::class, 'edit'])->name('products.edit');
        Route::post('/prendas/{product}/editar', [ProductController::class, 'update'])->name('products.update');
        Route::get('/prendas/{product}/eliminar', [ProductController::class, 'confirmDelete'])->name('products.confirm-delete');
        Route::post('/prendas/{product}/eliminar', [ProductController::class, 'destroy'])->name('products.destroy');

        Route::get('/categorias', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categorias/nueva', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categorias/nueva', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categorias/{category}/editar', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::post('/categorias/{category}/editar', [CategoryController::class, 'update'])->name('categories.update');
        Route::get('/categorias/{category}/eliminar', [CategoryController::class, 'confirmDelete'])->name('categories.confirm-delete');
        Route::post('/categorias/{category}/eliminar', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/reservas', [ReservationController::class, 'index'])->name('reservations.index');
        Route::post('/reservas/{reservation}/cancelar', [ReservationController::class, 'cancel'])->name('reservations.cancel');

        Route::get('/pedidos', [OrderController::class, 'index'])->name('orders.index');
        Route::post('/pedidos/{order}/marcar-pagado', [OrderController::class, 'markPaid'])->name('orders.mark-paid');
        Route::post('/pedidos/{order}/deshacer-pagado', [OrderController::class, 'undoPaid'])->name('orders.undo-paid');
        Route::post('/pedidos/{order}/rechazar', [OrderController::class, 'reject'])->name('orders.reject');

        Route::get('/cuentas-bancarias', [BankAccountController::class, 'index'])->name('bank-accounts.index');
        Route::get('/cuentas-bancarias/nueva', [BankAccountController::class, 'create'])->name('bank-accounts.create');
        Route::post('/cuentas-bancarias/nueva', [BankAccountController::class, 'store'])->name('bank-accounts.store');
        Route::get('/cuentas-bancarias/{bankAccount}/editar', [BankAccountController::class, 'edit'])->name('bank-accounts.edit');
        Route::post('/cuentas-bancarias/{bankAccount}/editar', [BankAccountController::class, 'update'])->name('bank-accounts.update');
        Route::get('/cuentas-bancarias/{bankAccount}/eliminar', [BankAccountController::class, 'confirmDelete'])->name('bank-accounts.confirm-delete');
        Route::post('/cuentas-bancarias/{bankAccount}/eliminar', [BankAccountController::class, 'destroy'])->name('bank-accounts.destroy');
    });
});
