<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    public $order_item;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($order_item)
    {
        $this->order_item = $order_item;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('Your Order item ' . $this->order_item->item->product_name . 'Has be updated')
            ->line('It\'s new status is \' ' . $this->order_item->status)
            ->action('Click here to login to your dashboard and see details', route('dashboard'))
            ->line('Thank you for using ' . env('APP_NAME') . '!');
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
            'title' => 'Order Item Update - ' . $this->order_item->status,
            'message' => 'Your Order item ' . $this->order_item->item->product_name . 'Has be updated to',
            'icon' => 'bx-check-square'
        ];
    }
}
