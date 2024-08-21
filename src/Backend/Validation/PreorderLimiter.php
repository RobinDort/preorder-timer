<?php
namespace RobinDort\PreorderTimer\Backend\Validation;

use Isotope\Model\Order;
use Contao\Database;


class PreorderLimiter {

	public function countPreordersForDateTime($dateTime) {

		$dateTimeBeforeSevenMinutes = $dateTime - 420;
		$dateTimeAfterSevenMinutes = $dateTime + 420; //Unixtime so 7*60 = 420
		
		$preordersResult = Database::getInstance()
		->prepare("SELECT COUNT(*) AS total_count FROM `tl_iso_product_collection` WHERE type='order' AND shipping_id != 28 AND preorder_time BETWEEN " . $dateTimeBeforeSevenMinutes . " AND " . $dateTimeAfterSevenMinutes)
		->execute()
		->fetchAssoc();
			
		// Return 0 if no result is found
		return (int) $preordersResult['total_count'];
	}

	public function findNextAvailableBookingTime($dateTime) {
		$fifteenMinutesAfter = $dateTime + 900;

		$amountPreorders = $this->countPreordersForDateTime($fifteenMinutesAfter);
		if ($amountPreorders > 1) {
			return $this->findNextAvailableBookingTime($fifteenMinutesAfter);
		}
		
		return $fifteenMinutesAfter;
	}
}
?>