<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ]);
        } else {
            $user = request(['email', 'password']);
            if (Auth::attempt($user)) {
                $user = User::where('email', $request->email)->first();
                $token = $user->createToken('authtoken');

                return response()->json([
                    'status' => 'Success',
                    'massage' => 'Login Successful',
                    'token' => $token->plainTextToken
                ]);
            } else {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Login Failed'
                ]);
            }
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $user->currentAccessToken()->delete();

        return response()->json([
            'status' => 'Success',
            'message' => 'logout success'
        ]);
    }
}
