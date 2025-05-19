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

        $cart = Isotope::getCart();
        \System::log("Cart log:" . $cart, __METHOD__, "TL_ERROR");
        throw new \Exception("Logg:" . $cart);

        $address = Isotope::getCart()->getShippingAddress();

        if (!$address instanceof Address) {
            throw new \Exception("No valid shipping address");
        }

        $postcode = trim((string) $address->postal);
        $city     = trim((string) $address->city);

        $validLines = array_filter(array_map('trim', preg_split('/[\n;]+/', (string) $this->postalCity)));

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