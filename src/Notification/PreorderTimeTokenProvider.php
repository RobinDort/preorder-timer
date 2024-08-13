<?php

namespace RobinDort\PreorderTimer\Notification;

use Isotope\Model\ProductCollection\Order;

class PreorderTimeTokenProvider {

    
    public function __invoke(array $arrTokens, Order $objOrder, array $arrConfig)
    {
        // Assuming preorder_time is stored as a timestamp
        if ($objOrder->preorder_time) {
            $arrTokens['preorder_time'] = date('d.m.Y H:i', $objOrder->preorder_time);
        } else {
            $arrTokens['preorder_time'] = ''; // or a default value if not set
        }

        return $arrTokens;
    }
}

?>