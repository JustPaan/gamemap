<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrganizerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $organizer;

    public function __construct(User $organizer)
    {
        $this->organizer = $organizer;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Organizer Registration - Approval Required')
            ->line('A new organizer has registered and requires your approval.')
            ->line('Organizer Name: ' . $this->organizer->name)
            ->line('Email: ' . $this->organizer->email)
            ->action('Review Organizer', route('admin.dashboard'))
            ->line('Please review and approve or reject this organizer account.');
    }

    public function toArray($notifiable)
    {
        return [
            'organizer_id' => $this->organizer->id,
            'organizer_name' => $this->organizer->name,
            'message' => 'New organizer registration requires approval',
            'action_url' => route('admin.dashboard'),
        ];
    }
}