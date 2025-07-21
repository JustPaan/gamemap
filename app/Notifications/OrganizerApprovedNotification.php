<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrganizerApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Organizer Account Has Been Approved')
            ->line('Congratulations! Your organizer account has been approved.')
            ->line('You can now login and start creating events on our platform.')
            ->action('Login Now', route('login'))
            ->line('Thank you for joining our community!');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Your organizer account has been approved',
            'action_url' => route('login'),
        ];
    }
}