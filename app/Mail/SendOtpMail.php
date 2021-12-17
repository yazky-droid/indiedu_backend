<?php

namespace App\Mail;

use App\Models\OtpVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $otp_code;
    protected $user_name;

    public function __construct(OtpVerification $otp, $user)
    {
        $this->otp_code = $otp;
        $this->user_name = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.otpVerification')
                            ->with([
                                'otp_code' => $this->otp_code->otp_code,
                                'name' => $this->user_name
                            ]);
    }

}
