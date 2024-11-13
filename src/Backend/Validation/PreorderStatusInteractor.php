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

    public function insertSpecialClosedDay($time, $date, $status) {
        $selectStmt = "SELECT id FROM tl_preorder_settings WHERE shop_closed_date='" . $date . "' AND shop_closed_status='" . $status . "'";
       
        // Check if entry with date and status exists. Update when existent.
        $selectResult = Database::getInstance()->execute($selectStmt)->fetchAssoc();

        $response = [
            'success' => false,
            'message' => ""
        ];

        if ($selectResult) {
            $id = $selectResult['id'];
            $updateStmt = "UPDATE tl_preorder_settings SET shop_closed_date='" . $date . "', shop_closed_status='" . $status . "' WHERE id=" . $id;
            $updateResult = Database::getInstance()->execute($updateStmt);

            if ($updateResult->affectedRows > 0) {
                $response['success'] = true;
                $response['message'] = "Row mit id: " . $id . ", Datum: " . $date . " und Status: " . $status . " wurde erfolgreich geupdated.";
            } else {
                $response['message'] = "Fehler während des Versuchs Row mit id: " . $id . " zu überschreiben!";
            }
        } else {
            $insertStmt = "INSERT INTO tl_preorder_settings (tstamp, shop_closed_date, shop_closed_status) VALUES ('" . $time . "','" . $date . "','" . $status . "')";
            $insertResult = Database::getInstance()->execute($insertStmt);

            if ($insertResult->affectedRows > 0) {
                $response['success'] = true;
                $response['message'] = "Row mit Datum: " . $date . " und Status: " . $status . " wurde erfolgreich gespeichert";
            } else {
                $response['message'] = "Fehler während des Versuchs Row mit Datum: " . $date . " und Status: " . $status . " zu überschreiben!";
            }
        }
        return $response;
    }

    public function deleteSpecialClosedDay($date, $status) {
        $statusConvert = [
            'fullyClosed'       => '1',
            'closedAtMorning'   => '2',
            'closedAtEvening'   => '3'
        ];
        $convertedStatus =  $statusConvert[$status];

        $stmt = "DELETE FROM tl_preorder_settings WHERE shop_closed_date='" . $date . "' AND shop_closed_status='" . $convertedStatus . "'";
        $result = Database::getInstance()->execute($stmt);

        // Check how many rows were affected
        if ($result->affectedRows > 0) {
            // Rows were deleted
            return 1;
        } else {
            // No rows were deleted
            return 0;
        }
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