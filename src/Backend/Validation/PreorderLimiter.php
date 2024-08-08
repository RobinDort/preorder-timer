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

		if (!empty($preordersResult) && isset($preordersResult['total_count'])) {
			return (int) $preordersResult['total_count'];
		}

		return -1;
	}
}
?>