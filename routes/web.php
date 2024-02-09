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
//    return view('welcome');
    $user = auth()->user();
    return view('home', [ 'user' => $user]);
});

// Routes for Keycloak login and callback:
Route::get('login/keycloak', 'App\Http\Controllers\Auth\LoginController@redirectToKeycloak')->name('login.keycloak');
Route::get('login/keycloak/callback', 'App\Http\Controllers\Auth\LoginController@handleKeycloakCallback');
// Routes for Keycloak logout
Route::post('logout/keycloak', 'App\Http\Controllers\Auth\LogoutController@logout')->name('logout.keycloak');;


// Apply Middleware
Route::middleware(['auth', 'check.role:ROLE_ADMIN'])->group(function () {
    Route::get('/admin', function () {
        $user = auth()->user();
        return view('admin', [ 'user' => $user]);
    });
});
Route::middleware(['auth', 'check.role:ROLE_USER'])->group(function () {
    Route::get('/user', function () {
        $user = auth()->user();
        return view('user', [ 'user' => $user]);
    });
});

// Define No Access Route
Route::get('/no-access', function () {
    return "You don't have access to this resource.";
})->name('no-access');
