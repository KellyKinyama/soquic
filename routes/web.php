<?php

use App\Livewire\AdminDashboard;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// NEW ADMIN ROUTE: Loads the Admin Dashboard component
Route::get('/admin/dashboard', AdminDashboard::class);


// Protected routes that require authentication
Route::middleware(['auth'])->group(function () {

    // The main user dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Fortify's generated routes (like profile update, password change, etc.)
    // These are typically included here if you are using Fortify without Breeze.
    // However, since Fortify handles its own routes, we only need the view link above.

});