<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\SendOtpMail;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\OtpVerification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password as RulesPassword;

class NewPasswordController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user) {

            $arr = [0,1,2,3,4,5,6,7,8,9];
            $arr = implode('', Arr::random($arr, 6));

            $otp_codes = OtpVerification::create([
                'email' => $request->email,
                'otp_code' =>  $arr,
                'user_id' => $user->id
            ]);

            Mail::to($request->email)->send(new SendOtpMail($otp_codes, $user->name));

            return response()->json([
                'message' => 'OTP code sent successfully',
                'user' => $user
            ], 201);
        }
    }
    public function verifOtp(Request $request, $id)
    {
        $user = User::with('otpCodes')->find($id);
        $otpVerify = otpVerification::orderBy('created_at', "desc")->firstWhere('user_id', $id);
        if ($otpVerify->otp_code == $request->otp) {
            return response()->json([
                'message' => 'OTP match',
                'valid' => true
            ], 200);
        } else{
            return response()->json([
                'message' => 'OTP is not match',
                'valid' => false
            ], 403);
        }
        // $validator = Validator::make($request->all(), [
        //     'email' => 'required',
        //     'token' => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'error' => $validator->errors(),
        //         'message' => 'Reset password link not valid'
        //     ]);
        // }

        // return response()->json([
        //     'email' => $request->email,
        //     'token' => $request->token
        // ]);
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::find($id);

        try {
            $user->update([
                'password' => Hash::make($request->password)
            ]);
            return response()->json([
                'message' => 'Password successfully changed!'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        // $request->validate([
        //     'token' => 'required',
        //     'email' => 'required|email',
        //     'password' => 'required|confirmed',
        // ]);

        // $status = Password::reset(
        //     $request->only('email', 'password', 'password_confirmation', 'token'),
        //     function ($user) use ($request) {
        //         $user->forceFill([
        //             'password' => Hash::make($request->password),
        //             'remember_token' => Str::random(60),
        //         ])->save();

        //         $user->tokens()->delete();

        //         event(new PasswordReset($user));
        //     }
        // );

        // if ($status == Password::PASSWORD_RESET) {
        //     return response([
        //         'message' => 'Password reset successfully'
        //     ]);
        // }

        // return response([
        //     'message' => __($status)
        // ], 500);
    }
}
