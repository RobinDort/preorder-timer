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
            // Create a DateTime object from the Unix timestamp
            $date = new \DateTime('@' . $objOrder->preorder_time);

            // Set the timezone to Germany (Berlin)
            $date->setTimezone(new \DateTimeZone('Europe/Berlin'));

            // Add 2 hours to the DateTime object to get UTC+2
            $date->modify('+2 hours');

            // Format the date to 'd.m.Y H:i'
            $formattedDate = $date->format('d.m.Y H:i');
            
            //$args[] = str_replace('ICONNAME', 'ok', $labelMarkup);
            $args[] = $formattedDate;
        }
        else {
            $args[] = str_replace('ICONNAME', 'delete', $labelMarkup);
        }

        return $args;

    }
}

?>