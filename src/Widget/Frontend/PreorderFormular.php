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
    protected $strName;

    public $shippingId;


    private $preorderLimiter;
    private $validatedValue;


	/**
	 * Value
	 * @var mixed
	 */
	protected $varValue;

    private const MAX_AMOUNT_SHIPPING_ORDERS = 1;
    private const MAX_AMOUNT_PICK_UP_ORDERS = 2;

    public function __construct($arrAttributes = null) {
        parent::__construct($arrAttributes);
        $this->mandatory = true;

        $this->shippingId = $arrAttributes['shippingId'] ?? null;
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

        if ($varInput === null || $varInput === "" || empty($varInput) || !isset($varInput)) {
            // If the input is empty, return immediately without validation
            $errorMessage = "Für einen reibungslosen Ablauf unserer Shop-Bestellungen, bitten wir Sie, Ihre gewünschte Bestellzeit (Datum und Uhrzeit) anzugeben.";
            $this->addError($errorMessage);
            return;
        }

        $expectedFormat = 'd.m.Y H:i';

        $timezone = new \DateTimeZone('Europe/Berlin');
        $dateTime = \DateTime::createFromFormat($expectedFormat, $varInput, $timezone);
        $errors = \DateTime::getLastErrors();

        if ($dateTime === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            $errorMessage = 'Invalides Datum oder Zeitformat. Bitte geben Sie sowohl ein gültiges Datum als auch eine gültige Zeit an.';
            $this->addError($errorMessage);
            return;
        }
         

        //datetime is in valid format
        $dateTimeTimestamp = $dateTime->getTimestamp();

        // Check if the selected time is at least 30 minutes away from now to prevent the preorder from being exactly the same time.
        $currentTimestamp = time();
        $thirtyMinutesLater = $currentTimestamp + 1800;  //30 minutes later (1800 seconds)

        if ($dateTimeTimestamp < $thirtyMinutesLater) {
            $errorMessage = "Um die Effizienz der von Ihnen getätigten Vorbestellungen zu optimieren, bitten wir Sie, einen Zeitraum zu wählen, welcher mindestens 30 Minuten nach dem aktuellen Zeitpunkt liegt.";
            $this->addError($errorMessage);
            return;
        } else {
        
            if ($this->shippingId !== null && $this->shippingId !== 28) { // shipping ID 28 is a pickup order
                $preorderShippingCountForDateTime = $this->preorderLimiter->countPreordersForDateTime($dateTimeTimestamp, true);

                if($preorderShippingCountForDateTime >= self::MAX_AMOUNT_SHIPPING_ORDERS) {
                
                    $nextPossibleBookingTime =  $this->preorderLimiter->findNextAvailableBookingTime($dateTimeTimestamp, true);
                    $formatedBookingTime = date('d.m.Y H:i', $nextPossibleBookingTime);
                    $errorMessage = "Wir bedauern, Ihnen mitteilen zu müssen, dass für den von Ihnen gewünschten Zeitraum bereits zu viele Vorbestellungen zur Lieferung eingegangen sind. Wir bitten Sie daher einen anderen Zeitraum für Ihre Bestellung auszuwählen. Der nächstmögliche Bestellzeitraum ist: " . $formatedBookingTime;
                    $this->addError($errorMessage);
                    return;
                }
            }

            
            if ($this->shippingId === 28) { // pickup order
                $preorderPickupCountForDateTime = $this->preorderLimiter->countPreordersForDateTime($dateTimeTimestamp, false);

                if($preorderPickupCountForDateTime >= self::MAX_AMOUNT_PICK_UP_ORDERS) {
                    $nextPossibleBookingTime =  $this->preorderLimiter->findNextAvailableBookingTime($dateTimeTimestamp, false);
                    $formatedBookingTime = date('d.m.Y H:i', $nextPossibleBookingTime);
                    $errorMessage = "Wir bedauern, Ihnen mitteilen zu müssen, dass für den von Ihnen gewünschten Zeitraum bereits zu viele Vorbestellungen zur Abholung eingegangen sind. Wir bitten Sie daher einen anderen Zeitraum für Ihre Bestellung auszuwählen. Der nächstmögliche Bestellzeitraum ist: " . $formatedBookingTime;
                    $this->addError($errorMessage);
                    return;
                }
            }
        }

        return $dateTime->getTimestamp();
    }


    public function validate()
    {  
        $dateValue = $this->getPost("date-input");
        $timeValue = $this->getPost("time-input");
        $combinedValue = "";
        if ($dateValue && $timeValue) {
            $combinedValue = $dateValue . ' ' . $timeValue;
        }

        // Call the validator to perform validation and store the result
        $varValue = $this->validator($combinedValue);

        // If there are any errors, handle them
        if ($varValue === null || $this->hasErrors()) {
            $this->blnSubmitInput = false;
            $this->class = 'error';
        } else {
            // Set the validated value
            $this->varValue = $varValue;
            $this->validatedValue = $varValue;
        }
    }


    public function getValidatedValue() {
        return $this->validatedValue;
    }
}
?>