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
        array_pop($args);

        $objOrder = Order::findByPk($row['id']);
        $labelMarkup = '<span style="display: block; text-align: center;"><img src="system/themes/flexible/icons/ICONNAME.svg" width="16" height="16"></span>';
        if (!empty($objOrder->preorder_time && $objOrder->preorder_time !== NULL)) {
            \System::log("preorder time: " . $objOrder->preorder_time,__METHOD__,TL_ERROR);
            throw new \Exception("objOrderTime: " . $objOrder->preorder_time);
            $args[] = str_replace('ICONNAME', 'ok', $labelMarkup);
        }
        else {
            $args[] = str_replace('ICONNAME', 'delete', $labelMarkup);
        }

        return $args;

    }
}

?>