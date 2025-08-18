<?php
//  php artisan queue:work database --queue=emails
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class QueuedResetPassword extends Notification implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public $resetUrl;
    public $token;
    public $tries = 3;
    public $timeout = 120;

    /**
     * Create a new notification instance.
     */
    public function __construct($resetUrl, $token)
    {
        $this->resetUrl = $resetUrl;
        $this->token = $token;
        
        // Set queue untuk Database
        $this->onQueue('emails');
        $this->onConnection('database');
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $logoBase64 = $this->getLogoBase64();

        return (new MailMessage)
            ->subject('Reset Password - SATU DATA TERNATE')
            ->view('emails.reset-password', [
                'actionUrl' => $this->resetUrl,
                'user' => $notifiable,
                'logoBase64' => $logoBase64,
                'token' => $this->token
            ]);
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

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [];
    }
}