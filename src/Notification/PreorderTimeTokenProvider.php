<?php

namespace RobinDort\PreorderTimer\Notification;

use Terminal42\NotificationCenterBundle\NotificationCenter;

class PreorderTimeTokenProvider {

    public function __construct(private NotificationCenter $notificationCenter) {}

    public function sendMessage(): void
    {
        $notificationId = 1; // Usually, some module setting of yours where the user can select the desired notification
        $tokens = [
            'TestToken' => 'TestValue',
        ];
        
        $receipts = $this->notificationCenter->sendNotification($notificationId, $tokens);
    }
}

?>