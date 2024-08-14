<?php
namespace RobinDort\PreorderTimer\Module;
use Isotope\Module\OrderDetails as IsotopeOrderDetails;

class OrderDetails extends IsotopeOrderDetails {
    protected function compile() {
        $order = $this->getCollection();

        if (null === $order) {
            return;
        }

        parent::compile();

        // Format the preorder_time if it exists
        if ($order->preorder_time) {
            $this->Template->preorder_time = date('d.m.Y H:i', $order->preorder_time);
        } else {
            $this->Template->preorder_time = '-';
        }
    }
}
?>