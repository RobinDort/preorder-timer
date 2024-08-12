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
            
            \System::log($preorderStatus->id,__METHOD__,TL_ERROR);
            \System::log(print_r($objOrder),__METHOD__,TL_ERROR);
            \System::log(print_r($objOrder->order_status),__METHOD__,TL_ERROR);
            throw new \Exception("ObjOrder " . $objOrder);
            
            if ($preorderStatus !== null) {
                // Update the order status
                $objOrder->order_status = $preorderStatus->id;
                $objOrder->save();
            } else {
                error_log("Not found preorder status");
            }
        }
    }
}

?>