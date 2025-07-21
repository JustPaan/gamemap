<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['mail']; // Choose your notification channels
    }

    public function toMail($notifiable)
    {
        $updatedFields = implode(', ', array_keys($this->data)); // Get updated fields
        return (new MailMessage)
            ->subject('Your Account Has Been Updated')
            ->line('Hello ' . $notifiable->name . ',') // Personalization
            ->line('Your account information has been updated. The following fields were changed: ' . $updatedFields . '.')
            ->action('View Account', url('/account')) // Adjust URL accordingly
            ->line('Thank you for using our application!');
    }
}