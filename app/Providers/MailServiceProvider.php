<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class MailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Override email verification template
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            $logoBase64 = $this->getLogoBase64();

            return (new MailMessage)
                ->subject('Verifikasi Email - SATU DATA TERNATE')
                ->view('emails.verify-email', [
                    'actionUrl' => $url,
                    'user' => $notifiable,
                    'logoBase64' => $logoBase64
                ]);
        });

        // Override reset password email template
        ResetPassword::toMailUsing(function ($notifiable, $token) {
            $logoBase64 = $this->getLogoBase64();

            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('Reset Password - SATU DATA TERNATE')
                ->view('emails.reset-password', [
                    'actionUrl' => $url,
                    'user' => $notifiable,
                    'logoBase64' => $logoBase64,
                    'token' => $token
                ]);
        });
    }

    /**
     * Get logo as base64 string
     */
    private function getLogoBase64(): string
    {
        $logoPath = public_path('assets/img/logo_kota.png');
        
        if (file_exists($logoPath)) {
            return 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }
        
        return '';
    }
}