<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrganizerRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public ?string $reason;

    public function __construct(?string $reason = null)
    {
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

public function toMail($notifiable)
{
    return (new MailMessage)
        ->subject('Your Organizer Application Status')
        ->line('Your request to become an organizer has been rejected.')
        ->line('Reason: ' . $this->reason)
        ->action('View Your Account', url('/profile'))
        ->line('Thank you for your interest!');
}

    public function toArray($notifiable)
    {
        return [
            'message' => 'Your organizer application has been rejected',
            'reason' => $this->reason,
            'action' => url('/contact'),
        ];
    }
}