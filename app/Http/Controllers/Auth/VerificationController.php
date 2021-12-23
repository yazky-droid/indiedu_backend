<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Email already verified'
            ]);
        } elseif ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));

            return response()->json([
                'status' => 'Success',
                'message' => 'Email has been verified'
            ]);
        } else {
            return response()->json([
                'status' => 'Error',
                'message' => 'verification link not valid or expired'
            ]);
        }
    }

    public function sendVerificationEmail(Request $request)
    {

        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Already Verified'
            ]);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'status' => 'Success',
            'message' => 'verification-link-sent'
        ]);
    }
}
