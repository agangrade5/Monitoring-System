<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('settings')->insert([
            [
                'type' => 'twilio',
                'value' => '{"twilio_account_sid":"","twilio_auth_token":"","twilio_from_number":""}',
            ],
            [
                'type' => 'email',
                'value' => '{"mail_mailer":"smtp","mail_host":"","mail_port":"","mail_encryption":"tls","mail_username":"","mail_password":"","mail_from_address":"","mail_from_name":"Monitoring System"}',
            ],
            [
                'type' => 'aws',
                'value' => '{"aws_access_key_id":"","aws_secret_access_key":"","aws_default_region":"us-east-1","aws_bucket":"Monitoring System"}',
            ],
            [
                'type' => 'otp',
                'value' => '{"max_time":60,"otp_length":6,"is_default":true,"default":"999999"}',
            ],
        ]);
    }
}