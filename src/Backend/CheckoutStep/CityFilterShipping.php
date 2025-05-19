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

        $address = Isotope::getCart()->getShippingAddress();

        \System::log("system log: " . $postcode . $city, __METHOD__,"TL_ERROR");
        throw new \Exception("Logg: " . $postcode . $city);

        if (!$address instanceof Address || empty($address->postal) || empty($address->city)) {
            return false;
        }

        $postcode = trim($address->postal);
        $city     = trim($address->city);

        $validLines = array_filter(array_map('trim', explode("\n", (string) $this->postalCity)));

        foreach ($validLines as $line) {
            if (strpos($line, ':') !== false) {
                [$allowedPostcode, $citiesString] = array_map('trim', explode(':', $line, 2));

                if ($allowedPostcode !== $postcode) {
                    continue;
                }

                $allowedCities = array_map('trim', explode(',', $citiesString));

                foreach ($allowedCities as $allowedCity) {
                    if (strcasecmp($allowedCity, $city) === 0) {
                        return true;
                    }
                }

                // Postcode matches, but city doesn't
                return false;
            } else {
                // Only postcode provided, allow all cities
                if ($line === $postcode) {
                    return true;
                }
            }
        }

        return false;
    }

}

?>