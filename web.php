<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Livewire\User\Usertable;
use Filament\Http\Controllers\AssetController;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/




Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::view('/', 'dashboard')->name('dashboard');
});

Route::prefix('auth')->group(function () {
    Route::get('ms365', [LoginController::class, 'redirectToMS']);
    Route::get('ms365/callback', [LoginController::class, 'handleMsCallback']);
    Route::post('logout', [LoginController::class, 'destroy'])
        ->name('logout');
});
