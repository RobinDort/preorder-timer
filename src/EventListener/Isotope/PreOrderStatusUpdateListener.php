<?php
namespace RobinDort\PreorderTimer\EventListener\Isotope;

use Isotope\Model\ProductCollection\Order;
use Isotope\Model\OrderStatus;


class PreOrderStatusUpdateListener
{
    const PRE_ORDER_OBJ_STATUS_NAME = "Vorbestellung";
 
    public function __invoke(Order $order, OrderStatus $newStatus, array $updates): bool
    {

        // Check if preorder_time is set
        if ($order->preorder_time) {
            // Find the "Vorbestellung" order status
            $preorderStatus = OrderStatus::findOneBy('name', self::PRE_ORDER_OBJ_STATUS_NAME);
        
            
            if ($preorderStatus !== null && $order->preorder_time) {
                // Update the order status
                $order->order_status = $preorderStatus->id;
                $order->save();

                return true;
            } 

        }

        return false;
    }
}

?>