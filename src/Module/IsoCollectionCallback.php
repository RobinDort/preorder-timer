<?php
namespace RobinDort\PreorderTimer\Module;
use Contao\Backend;

class IsoCollectionCallback extends Backend {

    public static function formatPreorderTime($value, \Contao\DataContainer $dc)
    {
        if ($value) {
            $dateTime = \DateTime::createFromFormat('U', $value);
            return $dateTime ? $dateTime->format('d.m.Y H:i') : 'Ungültiges Datum';
        }
        return 'Keine Vorbestellung';
    }
}
?>