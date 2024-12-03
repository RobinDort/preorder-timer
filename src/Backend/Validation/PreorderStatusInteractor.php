<?php
namespace RobinDort\PreorderTimer\Backend\Validation;

use Contao\Database;

class PreorderStatusInteractor {

    public function __construct() {}



    public function initTLShopClosedStatusTable() {
        $defaultStatuses = [
            ['id' => 1, 'status' => 1],
            ['id' => 2, 'status' => 2],
            ['id' => 3, 'status' => 3],
            ['id' => 4, 'status' => 4]
        ];

        foreach ($defaultStatuses as $status) {
            // Check if the record already exists
            $exists = "SELECT COUNT(*) AS count FROM tl_shop_closed_status WHERE id = " . $status["id"];
            $result = Database::getInstance()->execute($exists)->fetchAssoc();
                                

            // Insert if it does not exist
            if (!$result["count"]) {
                $insertStmt = "INSERT INTO tl_shop_closed_status (id, status) VALUES (" . $status["id"] . "," . $status["status"] .")";
                $insertResult = Database::getInstance()->execute($insertStmt);
            }
        }
    }


    //@TODO REMOVE LATER! OLD COLD WORKING FOR tl_preorder_settings TABLE! 
    // public function extractShopNormalClosingDays() {
    //     $stmt = "SELECT shop_closed_date, shop_closed_status from tl_preorder_settings;";
    //     $rslt = Database::getInstance()->execute($stmt)->fetchAllAssoc();

    //     $entries = [];
    //     foreach ($rslt as $row) {
    //         $entries[] = [
    //             'date' => $row['shop_closed_date'],
    //             'status' => $row['shop_closed_status']
    //         ];
    //     }

    //     $splittedSpecialDays = $this->splitSpecialClosedDays($entries);
    //     return $splittedSpecialDays;
    // }

    public function selectShopNormalClosingDays() {
        $stmt = "SELECT a.date AS closing_date,b.status AS closing_status
                 FROM tl_shop_closed_date AS a
                 INNER JOIN tl_shop_closed_status AS b
                 ON a.fk_status_id = b.id";

        $rslt = Database::getInstance()->execute($stmt)->fetchAllAssoc();

        $entries = [];
        foreach ($rslt as $row) {
            $entries[] = [
                'date' => $row['closing_date'],
                'status' => $row['closing_status']
            ];
        }

        $splittedSpecialDays = $this->splitSpecialClosedDays($entries);
        return $splittedSpecialDays;
    }


    public function insertNormalClosedShopDay($date, $status) {
       $closingDateExists = $this->selectShopClosingDayByDate($date);

        $response = [
            'success' => false,
            'message' => ""
        ];

        if ($closingDateExists) {
            $id = $closingDateExists['id'];
            $updateResult = $this->updateShopClosingDay($id, $date, $status);

            if ($updateResult->affectedRows > 0) {
                $response['success'] = true;
                $response['message'] = "Row mit id: " . $id . ", Datum: " . $date . " und Status: " . $status . " wurde erfolgreich geupdated.";
            } else {
                $response['message'] = "Fehler während des Versuchs Row mit id: " . $id . " zu überschreiben!";
            }
        } else {
            $insertResult = $this->insertShopClosingDayQuery($date, $status);

            if ($insertResult->affectedRows > 0) {
                $response['success'] = true;
                $response['message'] = "Row mit Datum: " . $date . " und Status: " . $status . " wurde erfolgreich gespeichert";
            } else {
                $response['message'] = "Fehler während des Versuchs Row mit Datum: " . $date . " und Status: " . $status . " zu speichern!";
            }
        }
        return $response;
    }


    public function insertSpecialClosedShopDay($date, $selectedTimes) {
       $closingDayExists = $this->selectShopClosingDayByDate($date);
       $db = Database::getInstance();

       $response = [
        'success' => false,
        'message' => ""
       ];

       if ($closingDayExists) {
            // update the closing day
            $presentDateID = $closingDayExists['id'];
            $updateResult = $this->updateShopClosingDay($presentDateID, $date, 4);

            if ($updateResult->affectedRows > 0) {
                $response['success'] = true;
                $response['message'] = "Row mit id: " . $presentDateID . ", Datum: " . $date . " und Status: " . 4 . " wurde erfolgreich geupdated.";
            } else {
                $response['message'] = "Fehler während des Versuchs Row mit id: " . $presentDateID . " zu überschreiben!";
            }

       } else {
            try {
                $db->beginTransaction();
                $insertResult = $this->insertShopClosingDayQuery($date, 4);

                if ($insertResult->affectedRows > 0) {
                    $dateQueryID = $insertResult->insertId;
                    $insertSpecialTimeResult = $this->insertShopSpecialTime($dateQueryID, $selectedTimes);

                    if ($insertSpecialTimeResult->affectedRows > 0) {
                        $response['success'] = true;
                        $response['message'] = "Transaktion erfolgreich. Alle Rows wurden fehlerfrei eingefügt.";
                        $db->commitTransaction();

                    } else {
                        $response['message'] = "Fehler während des Versuchs Row mit Datum: " . $date . " und Status: " . 4 . " zu speichern!";
                        throw new \Exception("Failed to insert special date time: " . $selectedTimes . " with parent date ID: " . $dateQueryID);
                    }

                } else {
                    $response['message'] = "Fehler während des Versuchs Row mit spezieller Zeitspanne: " . $selectedTimes . " zu speichern!";
                    throw new \Exception("Failed to insert date: $date.");
                }

            } catch (\Exception $e) {
                $db->rollbackTransaction();
                \System::log("Transaction failed while trying to insert special date with time: " . $e->getMessage(), __METHOD__, "TL_ERROR");
                $response['message'] = $e->getMessage();
            }

            return $response;
       }

    }


    private function selectShopClosingDayByDate($date) {
        $selectStmt = "SELECT id FROM tl_shop_closed_date WHERE date='" . $date . "'";
       
        // Check if entry with date and status exists. Update when existent.
        $selectResult = Database::getInstance()->execute($selectStmt)->fetchAssoc();

        return $selectResult;

    }

    private function updateShopClosingDay($id, $date, $status) {
        $tstamp = time();
        $updateStmt = "UPDATE tl_shop_closed_date SET tstamp='" . $tstamp . "', date='" . $date . "', fk_status_id ='" . $status . "' WHERE id=" . $id;
        $updateResult = Database::getInstance()->execute($updateStmt);

        return $updateResult;
    }

    private function insertShopClosingDayQuery($date, $status) {
        $tstamp = time();
        $insertStmt = "INSERT INTO tl_shop_closed_date (tstamp, date, fk_status_id) VALUES (" . $tstamp . ",'" . $date . "','" . $status . "')";
        $insertResult = Database::getInstance()->execute($insertStmt);

        return $insertResult;
    }

    private function insertShopSpecialTime($dateQueryID, $specialTimes) {
        $tstamp = time();
        $insertStmt = "INSERT INTO tl_shop_closed_special_date_time (tstamp, time, fk_closed_date_id) VALUES (" . $tstamp . ",'" . $selectedTimes . "'," . $dateQueryID;
        $insertResult = Database::getInstance()->execute($insertStmt);

        return $insertResult;
    }


    //@TODO REMOVE LATER! OLD COLD WORKING FOR tl_preorder_settings TABLE! 
    // public function insertSpecialClosedDay($time, $date, $status) {
    //     $selectStmt = "SELECT id FROM tl_preorder_settings WHERE shop_closed_date='" . $date . "'";
       
    //     // Check if entry with date and status exists. Update when existent.
    //     $selectResult = Database::getInstance()->execute($selectStmt)->fetchAssoc();

    //     $response = [
    //         'success' => false,
    //         'message' => ""
    //     ];

    //     if ($selectResult) {
    //         $id = $selectResult['id'];
    //         $updateStmt = "UPDATE tl_preorder_settings SET shop_closed_date='" . $date . "', shop_closed_status='" . $status . "' WHERE id=" . $id;
    //         $updateResult = Database::getInstance()->execute($updateStmt);

    //         if ($updateResult->affectedRows > 0) {
    //             $response['success'] = true;
    //             $response['message'] = "Row mit id: " . $id . ", Datum: " . $date . " und Status: " . $status . " wurde erfolgreich geupdated.";
    //         } else {
    //             $response['message'] = "Fehler während des Versuchs Row mit id: " . $id . " zu überschreiben!";
    //         }
    //     } else {
    //         $insertStmt = "INSERT INTO tl_preorder_settings (tstamp, shop_closed_date, shop_closed_status) VALUES ('" . $time . "','" . $date . "','" . $status . "')";
    //         $insertResult = Database::getInstance()->execute($insertStmt);

    //         if ($insertResult->affectedRows > 0) {
    //             $response['success'] = true;
    //             $response['message'] = "Row mit Datum: " . $date . " und Status: " . $status . " wurde erfolgreich gespeichert";
    //         } else {
    //             $response['message'] = "Fehler während des Versuchs Row mit Datum: " . $date . " und Status: " . $status . " zu überschreiben!";
    //         }
    //     }
    //     return $response;
    // }


    public function deleteNormalShopClosingDay($date, $status) {
        $statusConvert = [
            'fullyClosed'       => '1',
            'closedAtMorning'   => '2',
            'closedAtEvening'   => '3'
        ];
        $convertedStatus =  $statusConvert[$status];

        $stmt = "DELETE FROM tl_shop_closed_date WHERE date='" . $date . "' AND fk_status_id='" . $convertedStatus . "'";
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


    //@TODO REMOVE LATER! OLD COLD WORKING FOR tl_preorder_settings TABLE! 
    // public function deleteSpecialClosedDay($date, $status) {
    //     $statusConvert = [
    //         'fullyClosed'       => '1',
    //         'closedAtMorning'   => '2',
    //         'closedAtEvening'   => '3'
    //     ];
    //     $convertedStatus =  $statusConvert[$status];

    //     $stmt = "DELETE FROM tl_preorder_settings WHERE shop_closed_date='" . $date . "' AND shop_closed_status='" . $convertedStatus . "'";
    //     $result = Database::getInstance()->execute($stmt);

    //     // Check how many rows were affected
    //     if ($result->affectedRows > 0) {
    //         // Rows were deleted
    //         return 1;
    //     } else {
    //         // No rows were deleted
    //         return 0;
    //     }
    // }


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