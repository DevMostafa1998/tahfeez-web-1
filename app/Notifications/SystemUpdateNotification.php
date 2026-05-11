<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemUpdateNotification extends Notification
{
    use Queueable;

    public $title;
    public $body;

    public function __construct($title, $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    // تحديد أننا سنستخدم قاعدة البيانات فقط
    public function via($notifiable)
    {
        return ['database'];
    }

    // البيانات التي ستخزن في جدول الإشعارات
    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'body'  => $this->body,
        ];
    }
}
