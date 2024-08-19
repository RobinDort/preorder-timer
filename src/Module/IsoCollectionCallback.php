<?php
namespace RobinDort\PreorderTimer\Module;
use Contao\Backend;

class IsoCollectionCallback extends Backend {

    public function formatPreorderTime($value, \Contao\DataContainer $dc)
    {
        if ($value) {
            // Format Unix timestamp to 'dd-mm-Y H:i'
            return \Date::parse('d.m.Y H:i', $value);
        }
        return $value;
    }
}
?>