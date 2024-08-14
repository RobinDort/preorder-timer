<?php

namespace RobinDort\PreorderTimer\Notification;

class PreorderTimeTokenProvider {

    public function __invoke(Order $order, array $tokens): array
    {
        // Assuming the preorder_time is stored as a timestamp in the order
        if ($order->preorder_time) {
            \System::log("preorder_time hook " . $order->preorder_time,__METHOD__,TL_ERROR);
            throw new \Exception("order" . $order);
            $dateTime = \DateTime::createFromFormat('U', $order->preorder_time);
            $tokens['preorder_time'] = $dateTime->format('d.m.Y H:i');
        }

        return $tokens;
    }
}

?>