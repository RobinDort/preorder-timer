<?php
namespace RobinDort\PreorderTimer\EventListener\Isotope;

use Isotope\Model\ProductCollection\Order;
use Isotope\Model\OrderStatus;


class PreOrderStatusUpdateListener
{
    const PRE_ORDER_OBJ_STATUS_NAME = "Vorbestellung";
    const PENDING_ORDER_OBJ_STATUS_NAME = "Ausstehend";
 
    public function __invoke(Order $order, OrderStatus $newStatus, array $updates): bool
    {

        $pendingOrderStatus = OrderStatus::findOneBy('name', self::PENDING_ORDER_OBJ_STATUS_NAME);

        // Check if preorder_time is set
        if ($order->preorder_time) {
            // Find the "Vorbestellung" order status
            $preorderStatus = OrderStatus::findOneBy('name', self::PRE_ORDER_OBJ_STATUS_NAME);        
            
            if ($preorderStatus !== null && $order->preorder_time) {

                if ($newStatus->id === $pendingOrderStatus->id) {
                    // Update the order status to "Vorbestellung" when preorder time is set.
                    $order->order_status = $preorderStatus->id;
                } else {
                    // Update the order status to the new selected status
                    $order->order_status = $newStatus->id;
                }
                
                $order->save();
                return true;
            } 

        }

        return false;
    }
}

?>