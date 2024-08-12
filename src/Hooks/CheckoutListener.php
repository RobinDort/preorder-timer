<?php

namespace RobinDort\PreorderTimer\Hooks;
use Isotope\Model\OrderStatus;
use Isotope\Model\ProductCollection\Order;

class CheckoutListener {
    const PRE_ORDER_OBJ_STATUS_NAME = "Vorbestellung";
    
    public function onPostCheckout(Order $objOrder) {
        // Check if preorder_time is set
        if ($objOrder->preorder_time) {
            // Find the "Vorbestellung" order status
            $preorderStatus = OrderStatus::findOneBy('name', self::PRE_ORDER_OBJ_STATUS_NAME);
            
            if ($preorderStatus !== null) {
                // Update the order status
                $objOrder->order_status = $preorderStatus->id;
                $objOrder->save();
            }
        }
    }
}

?>