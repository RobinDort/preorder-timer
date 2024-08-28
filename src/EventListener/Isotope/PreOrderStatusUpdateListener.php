<?php
namespace RobinDort\PreorderTimer\EventListener\Isotope;

use Isotope\Model\ProductCollection\Order;
use Isotope\Model\OrderStatus;


class PreOrderStatusUpdateListener
{
    const PRE_ORDER_OBJ_STATUS_NAME = "Vorbestellung";
 
    public function __invoke(Order $order, OrderStatus $newStatus, array $updates): bool
    {

        \System::log("new status name: " . $newStatus->name);
        \System::log("new status id: " . $newStatus->id);
        throw new \Exception("newStatus:" .$newStatus);

        // Check if preorder_time is set
        if ($order->preorder_time) {
            // Find the "Vorbestellung" order status
            $preorderStatus = OrderStatus::findOneBy('name', self::PRE_ORDER_OBJ_STATUS_NAME);        
            
            if ($preorderStatus !== null && $order->preorder_time) {

                if ($newStatus->id === $preorderStatus->id) {
                    // Update the order status to "Vorbestellung"
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