<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function redirectToKeycloak()
    {
        // Keycloak below v3.2 requires no scopes to be set.
        // Later versions require the openid scope for all requests.
        // e.g return Socialite::driver('keycloak')->scopes(['openid'])->redirect();
        return Socialite::driver('keycloak')
            // ->with(['redirect_uri' => 'http://localhost:8000/ok'])
            ->scopes(['openid', 'profile', 'email', 'offline_access'])
            ->redirect();

    }

    public function handleKeycloakCallback()
    {
        $keycloakUser = Socialite::driver('keycloak')->user();
//        dd($keycloakUser);


        $user = User::firstOrNew(['keycloak_id' => $keycloakUser->id]);
        $user->name = $keycloakUser->name;
        $user->email = $keycloakUser->email;
        $user->keycloak_id = $keycloakUser->id;
        $user->password = ''; // You may want to handle password logic appropriately
        $user->save();



        Auth::login($user);

        return redirect('/dashboard');




        //             scope:  # last one for refresh tokens



        // this line will be needed if you have an exist Eloquent database User
        // then you can user user data gotten from keycloak to query such table
        // and proceed
        //$existingUser = User::where('email', $user->email)->first();

        // ... your desire implementation comes here

        return redirect()->intended('/whatever-your-route-look-like');
    }
}
