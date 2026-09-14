<?php
namespace RobinDort\PreorderTimer\Backend\ProductCollection;

use Isotope\Backend\ProductCollection\Callback;

class CollectionLabelCallback extends Callback {

    /**
     * @inheritdoc 
     */
    public function getOrderLabel($row, $label, \DataContainer $dc, $args):array {
        $args = parent::getOrderLabel($row, $label, $dc, $args);
        $fields = $GLOBALS['TL_DCA'][$dc->table]['list']['label']['fields'];
        $preorderColumn = array_search('preorder_time', $fields, true);

        if ($preorderColumn === false) {
            return $args;
        }

        // Use the database row supplied to the list callback, not a cached model.
        $preorderTime = $row['preorder_time'] ?? null;
        $labelMarkup = '<span style="display: block; text-align: center;"><img src="system/themes/flexible/icons/ICONNAME.svg" width="16" height="16"></span>';
        if (!empty($preorderTime)) {
            // Create a DateTime object from the Unix timestamp
            $date = new \DateTime('@' . $preorderTime);

            // Set the timezone to Germany (Berlin)
            $date->setTimezone(new \DateTimeZone('Europe/Berlin'));

            // Format the date to 'd.m.Y H:i'
            $formattedDate = $date->format('d.m.Y H:i');

            $args[$preorderColumn] = $formattedDate;
        }
        else {
            $args[$preorderColumn] = str_replace('ICONNAME', 'delete', $labelMarkup);
        }

        return $args;

    }
}

?>
