<?php
namespace RobinDort\PreorderTimer\Backend\Validation;

use Isotope\Model\Order;
use Contao\Database;


class PreorderLimiter {

	public function countPreordersForDateTime($dateTime) {
		$preorderStmt = "SELECT COUNT(*) FROM `tl_iso_product_collection` AS total_count WHERE type='order' AND shipping_id != 28 AND preorder_time = ?";

		\System::log("dateTime" . $dateTime, __METHOD__, TL_ERROR);
		
		$preordersResult = Database::getInstance()
		->prepare($preorderStmt)
		->execute($dateTime)
		->fetchField();

		\System::log("preordersResult" . $preordersResult, __METHOD__, TL_ERROR);


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