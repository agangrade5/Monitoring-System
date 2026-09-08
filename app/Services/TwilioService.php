<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.auth_token')
        );
    }

    public function sendOtp(
        string $phone,
        string $otp
    ): void {

        $this->client->messages->create(
            $phone,
            [
                'from' => config('services.twilio.from'),
                'body' => "Your login OTP is {$otp}. "
                    . "It is valid for "
                    . config('otp.otp.max_time')
                    . " minutes.",
            ]
        );
    }
}
