<?php
namespace RobinDort\PreorderTimer\Backend\Validation;

use Isotope\Model\Order;
use Contao\Database;


class PreorderLimiter {

	public function countPreordersForDateTime($dateTime) {
		$preorderStmt = "SELECT COUNT(*) FROM `tl_iso_product_collection` AS total_count WHERE type='order' AND shipping_id != 28 AND preorder_time = ?";

		
		$preordersResult = Database::getInstance()
		->prepare($preorderStmt)
		->execute($dateTime);
		
		
		$preorderCount = $preordersResult->fetchField();

		\System::log("preorderCount" . $preorderCount, __METHOD__, TL_ERROR);


		throw new \Exception("preordersResult" . $preordersResult);
	
		// Return 0 if no result is found
		return $preorderCount;
	}
}
?>