<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;

class MailService
{
    /**
     * Apply SMTP settings from the database to Laravel's mail config at runtime.
     * Call this before sending any email.
     */
    public static function configure(): void
    {
        $host = Setting::get('smtp_host');

        // Only override if SMTP is configured in the admin panel
        if (empty($host)) {
            return;
        }

        Config::set('mail.mailers.smtp.host',       $host);
        Config::set('mail.mailers.smtp.port',       (int) Setting::get('smtp_port', 587));
        Config::set('mail.mailers.smtp.encryption', Setting::get('smtp_encryption', 'tls') ?: null);
        Config::set('mail.mailers.smtp.username',   Setting::get('smtp_username'));
        Config::set('mail.mailers.smtp.password',   self::decryptPassword(Setting::get('smtp_password')));
        Config::set('mail.default',                 'smtp');

        $fromAddress = Setting::get('smtp_from_address') ?: Setting::get('smtp_username');
        $fromName    = Setting::get('smtp_from_name', 'Mai Cafe');

        Config::set('mail.from.address', $fromAddress);
        Config::set('mail.from.name',    $fromName);
    }

    /**
     * Encrypt a password for safe storage in the settings table.
     */
    public static function encryptPassword(string $password): string
    {
        return encrypt($password);
    }

    /**
     * Decrypt a stored SMTP password. Returns null if decryption fails.
     */
    public static function decryptPassword(?string $encrypted): ?string
    {
        if (empty($encrypted)) {
            return null;
        }

        try {
            return decrypt($encrypted);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Check whether SMTP has been configured in admin settings.
     */
    public static function isConfigured(): bool
    {
        return !empty(Setting::get('smtp_host')) && !empty(Setting::get('smtp_username'));
    }
}
