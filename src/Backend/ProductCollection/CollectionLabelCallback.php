<?php

namespace RobinDort\PreorderTimer\Backend\ProductCollection;

use Isotope\Backend\ProductCollection\Callback;
use Isotope\Model\ProductCollection\Order;

class CollectionLabelCallback extends Callback {

    /**
     * @inheritdoc 
     */
    public function getOrderLabel($row, $label, \DataContainer $dc, $args):array {
        $args = parent::getOrderLabel($row, $label, $dc, $args);

        $objOrder = Order::findByPk($row['id']);
        $labelMarkup = '<span style="display: block; text-align: center;"><img src="system/themes/flexible/icons/ICONNAME.svg" width="16" height="16"></span>';
        if (!empty($objOrder->preorder_time && $objOrder->preorder_time !== NULL)) {
            $args[] = str_replace('ICONNAME', 'ok', $labelMarkup);
        }
        else {
            $args[] = str_replace('ICONNAME', 'delete', $labelMarkup);
        }

        return $args;

    }
}

?>