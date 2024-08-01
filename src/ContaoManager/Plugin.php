<?php

namespace RobinDort\PreorderTimer\ContaoManager;

use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\CoreBundle\ContaoCoreBundle;
use Somevendor\ContaoExampleBundle\ContaoExampleBundle;

class Plugin extends BundlePluginInterface {
    public function getBundles(ParserInterface $parser): array {
        return [
            BundleConfig::create(IsotopePreorderTimerBundle::class)
                ->setLoadAfter([
                    ContaoCoreBundle::class,
                    'isotope',
                ]),
            ];
    }
}

?>