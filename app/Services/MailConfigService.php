<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Repositories\Contracts\SettingRepositoryInterface;

class MailConfigService
{
    /**
     * Constructor
     *
     * @param SettingRepositoryInterface $settingRepository
     *
     * @return void
     */
    public function __construct(
        private readonly SettingRepositoryInterface $settingRepository,
    ) {
    }

    /**
     * Apply mail config
     *
     * @return void
     */
    public function apply(): void
    {
        $mailDetails =
            $this->settingRepository->getSettingArray('mail');

        if (!$mailDetails) {
            return;
        }

        $mailPassword = $mailDetails['mail_password'] ?? null;
        if (!empty($mailPassword)) {
            try {
                $mailPassword = Crypt::decryptString($mailPassword);
            } catch (DecryptException $e) {
                // If not encrypted or plain text fallback
            }
        }

        config([
            'mail.default' => $mailDetails['mail_mailer'] ?? 'smtp',
            'mail.mailers.smtp.host' => $mailDetails['mail_host'] ?? null,
            'mail.mailers.smtp.port' => (int) ($mailDetails['mail_port'] ?? 587),
            'mail.mailers.smtp.encryption' => $mailDetails['mail_encryption'] ?? 'tls',
            'mail.mailers.smtp.username' => $mailDetails['mail_username'] ?? null,
            'mail.mailers.smtp.password' => $mailPassword,
            'mail.from.address' => $mailDetails['mail_from_address'] ?? null,
            'mail.from.name' => $mailDetails['mail_from_name'] ?? config('app.name'),
        ]);
    }
}
