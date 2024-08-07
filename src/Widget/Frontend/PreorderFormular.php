<?php
namespace RobinDort\PreorderTimer\Widget\Frontend;
use Contao\Widget;
use Haste\DateTime\DateTime;
use Contao\Date;

class PreorderFormular extends Widget {
    protected $blnSubmitInput = true;
    protected $blnForAttribute = true;
    protected $strTemplate = 'iso_checkout_preorder_time_formular';
    protected $strPrefix = 'widget widget-preorder-formular';

    public function generate(): string
    {
        // Not actually used
        return '';
    }
}

?>