<?php

namespace RobinDort\PreorderTimer\Notification;
use Isotope\Model\ProductCollection\Order;

class PreorderTimeTokenProvider {

    public function __invoke(Order $order, array $tokens): array
    {
        // Assuming the preorder_time is stored as a timestamp in the order
        if ($order->preorder_time) {
            $timezone = new \DateTimeZone('Europe/Berlin');
            $dateTime = \DateTime::createFromFormat('U', $order->preorder_time, $timezone);
            $tokens['preorder_time'] = $dateTime->format('d.m.Y H:i');
        }

        return $tokens;
    }
}

?>