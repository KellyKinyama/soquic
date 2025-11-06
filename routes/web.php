<?php

use App\Livewire\AdminDashboard;
use App\Livewire\FundPurchase;
use App\Livewire\FundTransfer;
use App\Livewire\Home;
use App\Livewire\ProfileSettings;
use App\Livewire\RewardsWithdrawal;
use App\Livewire\UserDashboard;
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


// Protected routes that require authentication
Route::middleware(['auth'])->group(function () {


    // Route::get('/', function () {
    //     return view('welcome');
    // });

    Route::get('/', Home::class);

    Route::get('/home', Home::class);

    // NEW ADMIN ROUTE: Loads the Admin Dashboard component
    Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard');

// Route to render the ProfileSettings component
Route::get('/profile', ProfileSettings::class)->name('profile');

// Route for the new Fund Transfer component
Route::get('/transfer', FundTransfer::class)->name('transfer');

// Route for the new Fund Transfer component
Route::get('/purchase', FundPurchase::class)->name('purchase');
Route::get('/rewards-withdrawal', RewardsWithdrawal::class)->name('rewards-withdrawal');


Route::get('/dashboard', UserDashboard::class)->name('dashboard');
    // The main user dashboard
    // Route::get('/dashboard', function () {
    //     return view('dashboard');
    // })->name('dashboard');

    // Fortify's generated routes (like profile update, password change, etc.)
    // These are typically included here if you are using Fortify without Breeze.
    // However, since Fortify handles its own routes, we only need the view link above.

});