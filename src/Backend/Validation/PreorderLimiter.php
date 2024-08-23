<?php
namespace RobinDort\PreorderTimer\Backend\Validation;

use Isotope\Model\Order;
use Contao\Database;
use RobinDort\PreorderTimer\Widget\Frontend\Helper\HolidayCalculation;


class PreorderLimiter {

	/**
	 * These constant define on which time the shop shall be closed. For the given numbers a shop would be closed for 14:01 PM to 16:59 
	 * because the condition will check for the timespan between the given numbers -> >14 && <17.
	 */
	private const FIRST_SHOP_CLOSING_START_TIME_DECIMAL = 14; // e.g 14:00 PM
	private const FIRST_SHOP_CLOSING_END_TIME_DECIMAL = 17; // e.g 17:00 PM 
	private const SECOND_SHOP_CLOSING_START_TIME_DECIMAL = 21; // e.g 21:00 PM
	private const SECOND_SHOP_CLOSING_START_TIME_HOLIDAY_DECIMAL = 21.5; // e.g 22:00 PM
	private const SECOND_SHOP_CLOSING_END_TIME_DECIMAL = 12; // e.g 12:00 PM THE NEXT DAY
	private const CLOSING_SHOP_DAY = 1; // the shop is closed on mondays. Numbers are used to present the days e.g 1 = monday, 2=tuesday...0=sunday


	public function countPreordersForDateTime($dateTime, $isShippingOrder) {

		$dateTimeBeforeSevenMinutes = $dateTime - 420;
		$dateTimeAfterSevenMinutes = $dateTime + 420; //Unixtime so 7*60 = 420

		$shippingStmt = "SELECT COUNT(*) AS total_count FROM `tl_iso_product_collection` WHERE type='order' AND shipping_id != 28 AND preorder_time BETWEEN " . $dateTimeBeforeSevenMinutes . " AND " . $dateTimeAfterSevenMinutes;
		$pickupStmt = "SELECT COUNT(*) AS total_count FROM `tl_iso_product_collection` WHERE type='order' AND shipping_id = 28 AND preorder_time BETWEEN " . $dateTimeBeforeSevenMinutes . " AND " . $dateTimeAfterSevenMinutes;
		$statement = null;

		if ($isShippingOrder === true) {
			$statement = $shippingStmt;
		} else {
			$statement = $pickupStmt;
		}

		\System::log("statement: " . $statement,__METHOD__,TL_ERROR);
		throw new \Exception("statement: ". $statement);
		
		$preordersResult = Database::getInstance()->execute($statement)->fetchAssoc();
			
		// Return 0 if no result is found
		return (int) $preordersResult['total_count'];
	}

	public function findNextAvailableBookingTime($dateTime, $isShippingOrder) {
		$nextPossibleBookingSlot = $dateTime + 900;

		// check if the new unixtime is in range of the shops order time. If not set the timestamp to the next available order time of the shop.

		// Get the hour and minute of the new time
		$hour = (int) date('H', $nextPossibleBookingSlot);
		$minute = (int) date('i', $nextPossibleBookingSlot);

		// check for possible holidays because in this case the shops closing time changes depending on that.
		$holidayHelper = new HolidayCalculation();
		$selectedDate = date("Y-m-d", $dateTime);
		$shopClosingTime = self::SECOND_SHOP_CLOSING_START_TIME_DECIMAL;

		if ($holidayHelper->isHolidayForDate($dateTime) || (int)date("w", $dateTime) === 0 || (int)date("w", $dateTime) === 6) {
			$shopClosingTime = self::SECOND_SHOP_CLOSING_START_TIME_HOLIDAY_DECIMAL;
		}
		

		// Convert the time into a decimal hour (e.g., 14:30 becomes 14.5)
		$decimalTime = $hour + $minute / 60;

		// Check if the time falls within the range 14:01 to 16:59 (first time shop is closed).
		if ($decimalTime > self::FIRST_SHOP_CLOSING_START_TIME_DECIMAL && $decimalTime < self::FIRST_SHOP_CLOSING_END_TIME_DECIMAL) {
			// Set the time to 17:00 on the same day
			$nextPossibleBookingSlot = strtotime(date('Y-m-d', $nextPossibleBookingSlot) . ' 17:30');
		}

		// Check if the time falls within the range 21:01 (or 21:31 on holidays, sundays and saturdays) to the next day 11:59 (second time shop is closed)
		if ($decimalTime > $shopClosingTime || $decimalTime < self::SECOND_SHOP_CLOSING_END_TIME_DECIMAL) {
			// Set the time to 12:00 on the next day
			$nextPossibleBookingSlot = strtotime('+1 day', strtotime(date('Y-m-d', $nextPossibleBookingSlot) . ' 12:30'));

			// Get the date in "Y-m-d" format
			$newDate = date('Y-m-d', $nextPossibleBookingSlot);
		
			// check if the new date is a saturday or sunday so the shop in only open at 17:30.  
			if ((int)date("w", $nextPossibleBookingSlot) === 0 || (int)date("w", $nextPossibleBookingSlot) === 6) { // 0 = Sunday, 6 = Saturday
				$nextPossibleBookingSlot = strtotime(date('Y-m-d', $nextPossibleBookingSlot) . ' 17:30');
			}

			// check if the new date is a holiday or a monday. (shop is closed on mondays).
			if ((int)date("w", $nextPossibleBookingSlot) === self::CLOSING_SHOP_DAY) {
				$nextPossibleBookingSlot = strtotime('+1 day', $nextPossibleBookingSlot);
			}
		}

		$amountPreorders = 0;

		if ($isShippingOrder) {
			$amountPreorders = $this->countPreordersForDateTime($nextPossibleBookingSlot, true);

			if ($amountPreorders > 1) {
				return $this->findNextAvailableBookingTime($nextPossibleBookingSlot, true);
			}

		} else {
			$amountPreorders = $this->countPreordersForDateTime($nextPossibleBookingSlot, false);

			if ($amountPreorders > 2) {
				return $this->findNextAvailableBookingTime($nextPossibleBookingSlot, false);
			}
		}
		
		return $nextPossibleBookingSlot;
	}
}
?>