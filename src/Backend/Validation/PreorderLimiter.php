<?php
namespace RobinDort\PreorderTimer\Backend\Validation;

use Isotope\Model\Order;
use Contao\Database;


class PreorderLimiter {

	private const PREORDER_STMT = "SELECT COUNT(*) FROM `tl_iso_product_collection` AS total_count WHERE type='order' AND shipping_id != 28 AND preorder_time = ?";

	public function countPreordersForDateTime($dateTime) {
		$preordersResult = Database::getInstance()
		->prepare(self::PREORDER_STMT)
		->execute($dateTime)
		->fetchAssoc();

		\System::log("preordersResultInt" . $preordersResult['total_count'], __METHOD__, TL_ERROR);
		\System::log("preordersResult" . $preordersResult[0], __METHOD__, TL_ERROR);

		throw new \Exception("preordersResult" . $preordersResult);

		// Check if the result is not empty and retrieve the count
		if (!empty($preordersResult) && isset($preordersResult['total_count'])) {
			return (int) $preordersResult['total_count'];
		}
	
		// Return 0 if no result is found
		return 0;
	}
}
?>