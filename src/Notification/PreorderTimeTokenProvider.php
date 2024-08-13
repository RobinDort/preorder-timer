<?php

namespace RobinDort\PreorderTimer\Notification;

use Isotope\Model\ProductCollection\Order;

class PreorderTimeTokenProvider {


    public function __invoke(Order $order, array $tokens)
    {
        if ($order->preorder_time) {
            // Convert the timestamp to a formatted date
            $tokens['preorder_time'] = \Date::parse('d.m.Y H:i', $order->preorder_time);
        } else {
            $tokens['preorder_time'] = ''; // Or set to a default value
        }

        return $tokens;
    }
}

?>