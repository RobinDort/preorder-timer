<?php

namespace RobinDort\PreorderTimer\EventListener\Isotope;

use Isotope\Model\ProductCollection\Order;
use Isotope\Model\OrderStatus;


class PostCheckoutListener
{
    const PRE_ORDER_OBJ_STATUS_NAME = "Vorbestellung";
 
    public function __invoke(Order $objOrder, array $tokens): void
    {
        // Check if preorder_time is set
        if ($objOrder->preorder_time) {
            // Find the "Vorbestellung" order status
            $preorderStatus = OrderStatus::findOneBy('name', self::PRE_ORDER_OBJ_STATUS_NAME);
            
            if ($preorderStatus !== null && $objOrder->preorder_time) {
                // Update the order status
                $objOrder->order_status = $preorderStatus->id;
                $objOrder->updateOrderStatus($objOrder);
            } 
        }
    }
}

?>