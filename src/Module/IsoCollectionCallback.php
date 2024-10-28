<?php
namespace RobinDort\PreorderTimer\Module;
use Contao\Backend;

class IsoCollectionCallback extends Backend {

    public static function formatPreorderTime($value, \Contao\DataContainer $dc)
    {
        if ($value) {
            // Create a DateTime object from the timestamp
            $dateTime = \DateTime::createFromFormat('U', $value, new \DateTimeZone('UTC'));

            // Set the timezone to Europe/Berlin
            $dateTime->setTimezone(new \DateTimeZone('Europe/Berlin'));
            
            // Check if the DateTime object was created successfully
            if ($dateTime) {
                // Return the formatted date and time
                return $dateTime->format('d.m.Y H:i');
            } else {
                return 'Ungültiges Datum';
            }
        }
        return 'Keine Vorbestellung';
    }
}
?>