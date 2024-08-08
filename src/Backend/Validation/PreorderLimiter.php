<?php
namespace RobinDort\PreorderTimer\Backend\Validation;

use Isotope\Model\Order;
use Contao\Database;


class PreorderLimiter {

	private const PREORDER_STMT = "SELECT COUNT(*) FROM `tl_iso_product_collection` WHERE type='order' AND shipping_id != 28 AND preorder_time = ?";

	public function countPreordersForDateTime($dateTime) {
		$preordersResult = Database::getInstance()
		->prepare(self::PREORDER_STMT)
		->execute($dateTime)
		->fetchAssoc();

		return $preordersResult;
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