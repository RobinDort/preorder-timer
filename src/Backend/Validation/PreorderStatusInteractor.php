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

        $splittedSpecialDays = $this->splitSpecialClosedDays($entries);
        return $splittedSpecialDays;
    }


    private function splitSpecialClosedDays($entries) {
        $specialDays = [
            'fullyClosed' => [],
            'closedAtMorning' => [],
            'closedAtEvening' => []
        ];

        foreach ($entries as $entry) {
            $date = $entry['date'];
            $status = $entry['status'];

            // shop is closed the whole day
            if ($status === '1') {
                $specialDays['fullyClosed'][] = $date;

            // shop is closed at morning
            } else if ($status === '2') {
                $specialDays['closedAtMorning'][] = $date;

            // shop is closed at evening 
            } else if ($status === '3') {
                $specialDays['closedAtEvening'][] = $date;
            }
        }
        return $specialDays;
    }
}
?>