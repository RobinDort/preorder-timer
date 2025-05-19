<?php
namespace RobinDort\PreorderTimer\Backend\CheckoutStep;

use Contao\StringUtil;
use Isotope\Isotope;
use Isotope\Model\ProductCollection\Address;
use Isotope\Model\Shipping\Flat;

class CityFilterShipping extends Flat {
    
    public function isAvailable(): bool {
        if (!parent::isAvailable()) {
            return false;
        }

        // No city/postal restrictions defined — allow availability
        if (empty($this->postalCity)) {
            return true;
        }

        $address = Isotope::getCart()->getShippingAddress();

        if ($address->postal === null || $address->city === null) {
            throw new \Exception("No valid postal or city access");
        }

        $postcode = trim((string) $address->postal);
        $city     = trim((string) $address->city);

        // Parse restrictions
        $restrictions = [];
        $validLines = array_filter(array_map('trim', preg_split('/[\n;]+/', (string) $this->postalCity)));

        foreach ($validLines as $line) {
            if (strpos($line, ':') !== false) {
                [$pc, $citiesString] = array_map('trim', explode(':', $line, 2));
                $restrictions[$pc] = array_map('trim', explode(',', $citiesString));
            } else {
                // Only postcode listed — allow all cities for this postcode
                $restrictions[$line] = [];
            }
        }

        // If postcode not listed → allow
        if (!array_key_exists($postcode, $restrictions)) {
            return true;
        }

        // If postcode listed but no cities specified → allow
        if (empty($restrictions[$postcode])) {
            return true;
        }

        // Postcode is listed and has city restrictions → match one
        foreach ($restrictions[$postcode] as $allowedCity) {
            if (strcasecmp($allowedCity, $city) === 0) {
                return true;
            }
        }

        // No city matched
        return false;
    }
}

?>