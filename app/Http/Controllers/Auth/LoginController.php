<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public const DEFAULT_LANGUAGE = 'en';

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
        $kcUser = Socialite::driver('keycloak')->user();

        $user = $this->getUser($kcUser);
        $user->name = $kcUser->name;
        $user->email = $kcUser->email;
        $user->password = ''; // You may want to handle password logic appropriately

        $user->kc_authorities = $this->getUserAuthorities($kcUser);

        $this->saveUser($user);

        Auth::login($user);

        return redirect('/');

        //             scope:  # last one for refresh tokens



        // this line will be needed if you have an exist Eloquent database User
        // then you can user user data gotten from keycloak to query such table
        // and proceed
        //$existingUser = User::where('email', $user->email)->first();

        // ... your desire implementation comes here

        return redirect()->intended('/whatever-your-route-look-like');
    }

    /**
     * Get user from Laravel Socialite Contract
     * @param \Laravel\Socialite\Contracts\User $details
     * @return mixed
     * @see UserService.java
     *
     */
    private function getUser(\Laravel\Socialite\Contracts\User $details) {
        $user = User::firstOrNew(['kc_id' => $details->id]);
        $activated = true;
        $sub = strval($details["sub"]);
        $username = null;
        if (isset($details["preferred_username"])) {
            $username = strtolower(strval($details["preferred_username"]));
        }
        // handle resource server JWT, where sub claim is email and uid is ID
        if (isset($details["uid"])) {
            $user->kc_id = (strval($details["uid"]));
            $user->kc_login = ($sub);
        } else {
            $user->kc_id = ($sub);
        }
        if ($username != null) {
            $user->kc_login = ($username);
        } else if ($user->kc_login == null) {
            $user->kc_login = ($user->kc_id);
        }
        if (isset($details["given_name"])) {
            $user->kc_first_name = (strval($details["given_name"]));
        } else if (isset($details["name"])) {
            $user->kc_first_name = (strval($details["name"]));
        }
        if (isset($details["family_name"])) {
            $user->kc_last_name = (strval($details["family_name"]));
        }
        if (isset($details["email_verified"])) {
            $activated = (bool) $details["email_verified"];
        }
        if (isset($details["email"])) {
            $user->kc_email = (strtolower(strval($details["email"])));
        } else if (strpos($sub, "|") !== false && ($username != null && strpos($username, "@") !== false)) {
            // special handling for Auth0
            $user->kc_email = ($username);
        } else {
            $user->kc_email = ($sub);
        }
        if (isset($details["langKey"])) {
            $user->kc_lang_key = (strval($details["langKey"]));
        } else if (isset($details["locale"])) {
            // trim off country code if it exists
            $locale = strval($details["locale"]);
            if (strpos($locale, "_") !== false) {
                $locale = substr($locale, 0, strpos($locale, "_"));
            } else if (strpos($locale, "-") !== false) {
                $locale = substr($locale, 0, strpos($locale, "-"));
            }
            $user->kc_lang_key = (strtolower($locale));
        } else {
            // set langKey to default if not specified by IdP
            $user->kc_lang_key = ($this::DEFAULT_LANGUAGE);
        }
        if (isset($details["picture"])) {
            $user->kc_image_url = (strval($details["picture"]));
        }
        $user->kc_activated = ($activated);

        return $user;
    }

    /**
     * Store user roles as authorities in the kc_users table.
     *
     * @param \Laravel\Socialite\Contracts\User $details
     * @return string
     */
    private function getUserAuthorities(\Laravel\Socialite\Contracts\User $details)
    {
        // Extract roles from the SocialiteProviders\Manager\OAuth2\User object
        $roles = $details->user['roles'] ?? [];

        // Convert roles array to a string
        return json_encode($roles);
    }

    private function saveUser(mixed $user)
    {
        if ($user->kc_created_by == null) {
            $user->kc_created_by = $user->kc_login;
        }
        $user->save();
    }
}
