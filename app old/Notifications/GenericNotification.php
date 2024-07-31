<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GenericNotification extends Notification
{
    use Queueable;

    public $title;
    public $greeting;
    public $line;
    public $actionText;
    public $actionURL;
    public $thanks;

    public function __construct($title, $greeting, $line, $actionText = null, $actionURL = null, $thanks = 'Thank you!')
    {
        $this->title = $title;
        $this->greeting = $greeting;
        $this->line = $line;
        $this->actionText = $actionText;
        $this->actionURL = $actionURL;
        $this->thanks = $thanks;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->subject($this->title)
            ->greeting($this->greeting)
            ->line($this->line);

        if ($this->actionText && $this->actionURL) {
            $mailMessage->action($this->actionText, $this->actionURL);
        }

        $mailMessage->line($this->thanks);

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->line,
            'icon' => 'bx-check-square'
        ];
    }
}
