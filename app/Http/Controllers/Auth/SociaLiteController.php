<?php

namespace App\Http\Controllers\Auth;

use App\Models\SocialAccount;
use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;


class SociaLiteController extends Controller
{
    protected function validateProvider($provider)
    {
        if (!in_array($provider, ['facebook', 'github', 'google'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please login using facebook, github or google'
            ], 422);
        }
    }

    public function redirectToProvider($provider)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }
        return Socialite::driver($provider)->stateless()->redirect();
    }

    /**
     * @param $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProvideCallback($provider)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }
        try {
            $user = Socialite::driver($provider)->stateless()->user();
        } catch (ClientException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials provided.'
            ], 422);
        }

        $authUser = $this->findOrCreateUser($user, $provider);

        $token = $authUser->createToken('userToken')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login Successfuly',
            'email' => $authUser->email,
            'token' => $token

        ]);
    }

    /**
     * @param $socialUser
     * @param $provider
     * @return mixed
     */
    public function findOrCreateUser($socialUser, $provider)
    {
        $socialAccount = SocialAccount::where('provider_id', $socialUser->getId())
            ->where('provider_name', $provider)
            ->first();

        if ($socialAccount) {

            return $socialAccount->user;
        } else {

            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name'  => $socialUser->getName(),
                    'email' => $socialUser->getEmail()
                ]);
            }

            $user->socialAccounts()->create([
                'provider_id'   => $socialUser->getId(),
                'provider_name' => $provider
            ]);

            return $user;
        }
    }
}
