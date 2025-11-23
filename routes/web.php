<?php

use App\Livewire\Auth\ForgotPasswordPage;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ResetPasswordPage;
use App\Livewire\CancelPage;
use App\Livewire\CartPage;
use App\Livewire\CategoriesPage;
use App\Livewire\CheckoutPage;
use App\Livewire\Homepage;
use App\Livewire\MyOrderDetailPage;
use App\Livewire\MyOrdersPage;
use App\Livewire\ProductDetailPage;
use App\Livewire\ProductsPage;
use App\Livewire\SuccessPage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;



Route::get('/', Homepage::class);
Route::get('/categories', CategoriesPage::class);
Route::get('/products', ProductsPage::class);
Route::get('/product/{slug}', ProductDetailPage::class);

Route::middleware('guest')->group(function () {
Route::get('/login', LoginPage::class)->name('login');
Route::get('/register', RegisterPage::class);
Route::get('/forgot-password', ForgotPasswordPage::class)->name('password.request');
Route::get('/reset-password/{token}', ResetPasswordPage::class)->name('password.reset');
});


Route::middleware('auth')->group(function () {
Route::post('/logout', function () {
        Auth::logout();
        return redirect('/');
    })->name('logout');
Route::get('/cart', CartPage::class);
Route::get('/checkout', CheckoutPage::class);
Route::get('/my-orders', MyOrdersPage::class);
Route::get('/my-orders/{order}', MyOrderDetailPage::class);

Route::get('/success', SuccessPage::class);
Route::get('/cancel', CancelPage::class);
});

