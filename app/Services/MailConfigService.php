<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class MailConfigService
{
    public function apply(): void
    {
        $setting = DB::table('settings')
            ->where('type', 'email')
            ->first();

        if (!$setting) {
            return;
        }

        $data = json_decode($setting->value, true);

        $mailPassword = $data['mail_password'] ?? null;
        if (!empty($mailPassword)) {
            try {
                $mailPassword = Crypt::decryptString($mailPassword);
            } catch (DecryptException $e) {
                // If not encrypted or plain text fallback
            }
        }

        config([
                'mail.default' => !empty($data['mail_mailer'])? $data['mail_mailer']: 'smtp',
                'mail.mailers.smtp.host' => $data['mail_host'] ?? null,
                'mail.mailers.smtp.port' => (int) ($data['mail_port'] ?? 587),
                'mail.mailers.smtp.encryption' => $data['mail_encryption'] ?? 'tls',
                'mail.mailers.smtp.username' => $data['mail_username'] ?? null,
                'mail.mailers.smtp.password' => $mailPassword,
                'mail.from.address' => $data['mail_from_address'] ?: $data['mail_username'],
                'mail.from.name' => $data['mail_from_name'] ?? 'Monitoring System',
                ]);
    }
}