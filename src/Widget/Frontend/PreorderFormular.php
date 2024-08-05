<?php
namespace RobinDort\PreorderTimer\Widget\Frontend;
use Contao\Widget;
use Haste\DateTime\DateTime;

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


    protected function validator($varInput) {
        $this->blnSubmitInput = false;

        // Validate if the input is a valid datetime
        $date = DateTime::createFromFormat('Y-m-d\TH:i', $varInput);
        if (!$date || $date->format('Y-m-d\TH:i') !== $varInput) {
            $this->addError('Please enter a valid date and time.');
        }

        $varInput = parent::validator($varInput);

        if (!$this->hasErrors()) {
            $this->blnSubmitInput = true;

            return $varInput;
        }

        return '';
    }
}

?>