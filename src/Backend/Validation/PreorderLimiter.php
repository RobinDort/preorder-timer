<?php
namespace RobinDort\PreorderTimer\Backend\Validation;

use Isotope\Model\Order;
use Contao\Database;

class PreorderLimiter {

	/**
	 * These constant define on which time the shop shall be closed. For the given numbers a shop would be closed for 14:01 PM to 16:59 
	 * because the condition will check for the timespan between the given numbers -> >14 && <17.
	 */
	private const FIRST_SHOP_CLOSING_START_TIME_DECIMAL = 14; // e.g 14:00 PM
	private const FIRST_SHOP_CLOSING_END_TIME_DECIMAL = 17; // e.g 17:00 PM 
	private const SECOND_SHOP_CLOSING_START_TIME_DECIMAL = 21; // e.g 21:00 PM
	private const SECOND_SHOP_CLOSING_END_TIME_DECIMAL = 12; // e.g 12:00 PM THE NEXT DAY
	private const CLOSING_SHOP_DAY = 1; // the shop is closed on mondays. Numbers are used to present the days e.g 1 = monday, 2=tuesday...0=sunday


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
		$nextPossibleBookingSlot = $dateTime + 900;

		// check if the new unixtime is in range of the shops order time. If not set the timestamp to the next available order time of the shop.

		// Get the hour and minute of the new time
		$hour = (int) date('H', $nextPossibleBookingSlot);
		$minute = (int) date('i', $nextPossibleBookingSlot);

		// Convert the time into a decimal hour (e.g., 14:30 becomes 14.5)
		$decimalTime = $hour + $minute / 60;

		// Check if the time falls within the range 14:01 to 16:59 (first time shop is closed).
		if ($decimalTime > self::FIRST_SHOP_CLOSING_START_TIME_DECIMAL && $decimalTime < self::FIRST_SHOP_CLOSING_END_TIME_DECIMAL) {
			// Set the time to 17:00 on the same day
			$nextPossibleBookingSlot = strtotime(date('Y-m-d', $nextPossibleBookingSlot) . ' 17:00');
		}

		// Check if the time falls within the range 21:01 to the next day 11:50 (second time shop is closed)
		if ($decimalTime > self::SECOND_SHOP_CLOSING_START_TIME_DECIMAL || $decimalTime < self::SECOND_SHOP_CLOSING_END_TIME_DECIMAL) {
			// Set the time to 12:00 on the next day
			$nextPossibleBookingSlot = strtotime('+1 day', strtotime(date('Y-m-d', $nextPossibleBookingSlot) . ' 12:00'));

			// Get the date in "Y-m-d" format
			$newDate = date('Y-m-d', $nextPossibleBookingSlot);

			// check if the new date is a holiday or a monday. (shop is closed on mondays).
			if ((int)date("w", $nextPossibleBookingSlot) === self::CLOSING_SHOP_DAY) {
				$nextPossibleBookingSlot = strtotime('+1 day', $nextPossibleBookingSlot);
			}
		}

		$amountPreorders = $this->countPreordersForDateTime($nextPossibleBookingSlot);
		if ($amountPreorders > 1) {
			return $this->findNextAvailableBookingTime($nextPossibleBookingSlot);
		}
		
		return $nextPossibleBookingSlot;
	}
}
?>