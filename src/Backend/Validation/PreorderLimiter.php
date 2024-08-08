<?php
namespace RobinDort\PreorderTimer\Backend\Validation;

use Isotope\Model\Order;
use Contao\Database;


class PreorderLimiter {

	public function countPreordersForDateTime($dateTime) {
		
		$preordersResult = Database::getInstance()
		->prepare("SELECT COUNT(*) AS total_count FROM `tl_iso_product_collection` WHERE type='order' AND shipping_id != 28 AND preorder_time = " . $dateTime)
		->execute()
		->fetchAssoc();
			
		// Return 0 if no result is found
		return (int) $preordersResult['total_count'];
	}
}
?>	