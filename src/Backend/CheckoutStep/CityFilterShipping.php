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

        if (!$address instanceof Address || empty($address->postal) || empty($address->city)) {
            return false;
        }

        $postcode = trim($address->postal);
        $city     = trim($address->city);

        $validLines = array_filter(array_map('trim', explode("\n", (string) $this->postalCity)));

        \System::log("valid lines:" . print_r($valid_lines), __METHOD__, "TL_ERROR");
        throw new Exception(print_r($validLines));

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