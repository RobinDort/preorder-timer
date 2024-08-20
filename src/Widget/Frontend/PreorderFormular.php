<?php
namespace RobinDort\PreorderTimer\Widget\Frontend;

use RobinDort\PreorderTimer\Backend\Validation\PreorderLimiter;
use Umulmrum\Holiday\HolidayCalculator;

// This must be switched according to the region.
use Umulmrum\Holiday\Provider\Germany\Saarland;

use Haste\DateTime\DateTime;
use Contao\Widget;
use Contao\Date;
use Contao\Input;


class PreorderFormular extends Widget {
    protected $blnSubmitInput = true;
    protected $blnForAttribute = true;
    protected $strTemplate = 'iso_checkout_preorder_time_formular';
    protected $strPrefix = 'widget widget-preorder-formular';
    protected $strName;


	private $holidays;
    private $preorderLimiter;


	/**
	 * Value
	 * @var mixed
	 */
	protected $varValue;

    private const MAX_AMOUNT_SHIPPING_ORDERS = 2;

    public function __construct() {
		$this->preorderLimiter = new PreorderLimiter();
        $this->initHolidaysForYear();
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
       
        if ($varInput === null || $varInput === "" || empty($varInput || !isset($varInput))) {
            // If the input is empty, return immediately without validation
            return $varInput;
        }

        $expectedFormat = 'd.m.Y H:i';

        $dateTime = \DateTime::createFromFormat($expectedFormat, $varInput);
        $errors = \DateTime::getLastErrors();

        if ($errors !== false) { // ensures the function did not return false and thus an error of errors
            if ($dateTime === false || $errors['warning_count'] > 0 || $errors['error_count'] > 0) {
                $errorMessage = 'Invalid date or time format. Please use the format: ' . $expectedFormat;
                throw new \Exception($errorMessage);
            }
        }   

        //datetime is in valid format
        $dateTimeTimestamp = $dateTime->getTimestamp();
        $preorderCountForDateTime = $this->preorderLimiter->countPreordersForDateTime($dateTimeTimestamp);

        if($preorderCountForDateTime >= self::MAX_AMOUNT_SHIPPING_ORDERS) {
            $errorMessage = "Wir bedauern, Ihnen mitteilen zu müssen, dass für den von Ihnen gewünschten Zeitraum bereits zu viele Vorbestellungen eingegangen sind. Wir bitten Sie daher einen anderen Zeitraum für Ihre Bestellung auszuwählen.";
            $this->addError($errorMessage);
        }

        return $dateTime->getTimestamp();
    }


    public function validate()
    {
       
        $dateValue = $this->getPost("date-input");
        $timeValue = $this->getPost("time-input");
        $combinedValue = $dateValue . ' ' . $timeValue;
        if ($combinedValue === "" || $combinedValue === null) {
            $this->varValue = "";
            return;
        }

        try {
            // Call the validator to perform validation and store the result
            $varValue = $this->validator($combinedValue);
    
            // If there are any errors, handle them
            if ($this->hasErrors()) {
                $this->blnSubmitInput = false;
                $this->class = 'error';
            } else {
                // Set the validated value
                $this->varValue = $varValue;
            }
        } catch (\Exception $e) {
            // Log the exception and prevent form submission
            $this->blnSubmitInput = false;
            $this->class = 'error';
        }
    }


    private function initHolidaysForYear() {
        $currentYear = (int) date('Y');
    	$holidayCalculator = new HolidayCalculator();
		$this->holidays = $holidayCalculator->calculate(Saarland::class, $currentYear);
    }
    

    public function isHolidayToday() {
        $currentDay = date('Y-m-d');
    	return $this->holidays->isHoliday(new \DateTime('2024-01-01'));
    }

}
?>