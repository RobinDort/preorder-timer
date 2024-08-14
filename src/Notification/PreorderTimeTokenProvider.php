<?php

namespace RobinDort\PreorderTimer\Notification;
use Isotope\Model\ProductCollection\Order;

class PreorderTimeTokenProvider {

    public function __invoke(Order $order, array $tokens): array
    {
        // Assuming the preorder_time is stored as a timestamp in the order
        if ($order->preorder_time && !empty($order->preorder_time)) {
            // Create a DateTime object from the timestamp in UTC
            $dateTime = \DateTime::createFromFormat('U', $order->preorder_time, new \DateTimeZone('UTC'));

            // Adjust to German timezone (Europe/Berlin) by adding 2 hours
            $dateTime->modify('+2 hours');

            // Format the adjusted date and time for the token
            $tokens['preorder_time'] = $dateTime->format('d.m.Y H:i');
        } else {
            $tokens['preorder_time'] = '';
        }

        return $tokens;
    }
}

?>