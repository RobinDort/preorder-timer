<?php
namespace RobinDort\PreorderTimer\EventListener\Isotope;

use Isotope\Model\ProductCollection\Order;
use Isotope\Model\OrderStatus;


class PreOrderStatusUpdateListener
{
    const PRE_ORDER_OBJ_STATUS_NAME = "Vorbestellung";
 
    public function __invoke(Order $order, OrderStatus $newStatus, array $updates): bool
    {

        \System::log("Updates array: " $updates,__METHOD__,TL_ERROR);
        throw new \Exception("updates: " .$updates);

        // Check if preorder_time is set
        if ($order->preorder_time) {
            // Find the "Vorbestellung" order status
            $preorderStatus = OrderStatus::findOneBy('name', self::PRE_ORDER_OBJ_STATUS_NAME);
            $isManualUpdate = !empty($updates['isManual']);
        
            
            if ($preorderStatus !== null && $order->preorder_time && !$isManualUpdate) {
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