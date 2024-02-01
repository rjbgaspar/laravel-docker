<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

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

// Routes for Keycloak login and callback:
Route::get('login/keycloak', 'App\Http\Controllers\Auth\LoginController@redirectToKeycloak')->name('login.keycloak');
Route::get('login/keycloak/callback', 'App\Http\Controllers\Auth\LoginController@handleKeycloakCallback');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        return view('Dashboard', [ 'user' => $user]);
    });
});

Route::get('/ok', function () {
    $user = Socialite::driver('keycloak')->user();
    return view('ok',  [ 'user' => $user]);
});
