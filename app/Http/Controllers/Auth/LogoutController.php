<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout() {
        // Logout of your app.
        Auth::logout();

        // The user will not be redirected back.
        //return redirect(Socialite::driver('keycloak')->getLogoutUrl()); // "http://keycloak:9080/realms/jhipster/protocol/openid-connect/logout"



        // The URL the user is redirected to after logout.
        $redirectUri = \Illuminate\Support\Facades\Config::get('app.url');
        // Keycloak v18+ does support a post_logout_redirect_uri in combination with a
        // client_id or an id_token_hint parameter or both of them.
        // NOTE: You will need to set valid post logout redirect URI in Keycloak.
        return redirect(Socialite::driver('keycloak')->getLogoutUrl($redirectUri, env('KEYCLOAK_CLIENT_ID')));
        return redirect(Socialite::driver('keycloak')->getLogoutUrl($redirectUri, null, 'YOUR_ID_TOKEN_HINT'));
        return redirect(Socialite::driver('keycloak')->getLogoutUrl($redirectUri, env('KEYCLOAK_CLIENT_ID'), 'YOUR_ID_TOKEN_HINT'));

        // You may add additional allowed parameters as listed in
        // https://openid.net/specs/openid-connect-rpinitiated-1_0.html
        return redirect(Socialite::driver('keycloak')->getLogoutUrl($redirectUri, CLIENT_ID, null, ['state' => '...'], ['ui_locales' => 'de-DE']));

        // Keycloak before v18 does support a redirect URL
        // to redirect back to Keycloak.
        return redirect(Socialite::driver('keycloak')->getLogoutUrl($redirectUri));
    }
}
