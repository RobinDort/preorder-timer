<?php
namespace RobinDort\PreorderTimer\Widget\Frontend;

use RobinDort\PreorderTimer\Backend\Validation\PreorderLimiter;
use Haste\DateTime\DateTime;
use Contao\Widget;
use Contao\Date;
use Contao\Input;


class PreorderFormular extends Widget {
    protected $blnSubmitInput = true;
    protected $blnForAttribute = true;
    protected $strTemplate = 'iso_checkout_preorder_time_formular';
    protected $strPrefix = 'widget widget-preorder-formular';

    private $preorderLimiter;

    private const MAX_AMOUNT_SHIPPING_ORDERS = 2;

    public function __construct() {
		$this->preorderLimiter = new PreorderLimiter();
	}

    /**
     *  {@inheritdoc}.
     */
    public function generate(): string
    {
        // Not actually used
        return '';
    }


     /**
     * {@inheritdoc}.
     */
    protected function validator($varInput) {
        $dateValue = Input::post("date-input");
        $timeValue = Input::post("time-input");

        $dateTimeString = $dateValue . ' ' . $timeValue;
        $expectedFormat = 'd.m.Y H:i';

        $dateTime = \DateTime::createFromFormat($expectedFormat, $dateTimeString);
        $errors = \DateTime::getLastErrors();

        if ($errors !== false) { // ensures the function did not return false and thus an error of errors
            if ($dateTime === false || $errors['warning_count'] > 0 || $errors['error_count'] > 0) {
                $errorMessage = 'Invalid date or time format. Please use the format: ' . $expectedFormat;
                \System::log($errorMessage . ' - Date input: ' . $dateValue . ', Time input: ' . $timeValue, __METHOD__, TL_ERROR);
                return $varInput;
            }
        }   

        //datetime is in valid format
        $dateTimeTimestamp = $dateTime->getTimestamp();
        $preorderCountForDateTime = $this->preorderLimiter->countPreordersForDateTime($dateTimeTimestamp);

        if($preorderCountForDateTime > self::MAX_AMOUNT_SHIPPING_ORDERS) {
            $errorMessage = "Wir bedauern, Ihnen mitteilen zu müssen, dass für den von Ihnen gewünschten Zeitraum bereits zu viele Vorbestellungen eingegangen sind. Wir bitten Sie daher einen anderen Zeitraum für Ihre Bestellung auszuwählen.";
            $this->addError($errorMessage);
            return $varInput;
        }

        return $dateTime->getTimestamp();
    }

}

?>