<?php
namespace RobinDort\PreorderTimer\Backend\Validation;

use Contao\Database;

class PreorderStatusInteractor {

    public function __construct() {}

    public function extractSpecialClosedDays() {
        $stmt = "SELECT shop_closed_date, shop_closed_status from tl_preorder_settings;";
        $rslt = Database::getInstance()->execute($stmt)->fetchAllAssoc();

        $entries = [];
        foreach ($rslt as $row) {
            $entries[] = [
                'date' => $row['shop_closed_date'],
                'status' => $row['shop_closed_status']
            ];
        }

        return $entries;
    }
}
?>