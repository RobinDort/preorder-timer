<?php

namespace RobinDort\PreorderTimer\EventListener\Isotope;

use Isotope\Model\ProductCollection\Order;
use Isotope\Model\OrderStatus;


class PreOrderStatusUpdateListener
{
    const PRE_ORDER_OBJ_STATUS_NAME = "Vorbestellung";
 
    public function __invoke(Order $order, OrderStatus $newStatus, array $updates): bool
    {

        if (!$order->isPaid()) {
            return true;
        }


        // Check if preorder_time is set
        if ($order->preorder_time) {
            // Find the "Vorbestellung" order status
            $preorderStatus = OrderStatus::findBy('name', self::PRE_ORDER_OBJ_STATUS_NAME);
            
            if ($preorderStatus !== null && $order->preorder_time) {
                $newStatus = $preorderStatus;
                // Update the order status
                $order->order_status = $newStatus;
                $order->save();
                \System::log($newStatus,__METHOD__,TL_ERROR);
                \System::log($order->order_status,__METHOD__,TL_ERROR);
                \System::log($updates,__METHOD__,TL_ERROR);

                throw new \Exception("order status: " . $newStatus);
            } 

        }

        return false;
    }
}

?>