<?php

namespace RobinDort\PreorderTimer\EventListener\Isotope;

use Isotope\Model\ProductCollection\Order;
use Isotope\Model\OrderStatus;
use Isotope\ServiceAnnotation\IsotopeHook;


/**
 * @IsotopeHook("postCheckout")
 */
class PostCheckoutListener
{
    const PRE_ORDER_OBJ_STATUS_NAME = "Vorbestellung";
 
    public function __invoke(Order $order, array $tokens): void
    {
        // Check if preorder_time is set
        if ($order->preorder_time) {
            // Find the "Vorbestellung" order status
            $preorderStatus = OrderStatus::findOneBy('name', self::PRE_ORDER_OBJ_STATUS_NAME);
            
            if ($preorderStatus !== null) {
                // Update the order status to "Vorbestellung"
                $order->order_status = $preorderStatus->id;
                $order->save();
            }
        }
    }
}

?>