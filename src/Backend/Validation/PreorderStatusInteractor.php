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

    public function selectShopNormalClosingDays() {
        $stmt ="SELECT a.date AS closing_date, b.status AS closing_status, c.time AS special_date_time
                FROM tl_shop_closed_date AS a
                INNER JOIN 
                tl_shop_closed_status AS b
                ON a.fk_status_id = b.id
                LEFT JOIN 
                tl_shop_closed_special_date_time AS c
                ON c.fk_closed_date_id = a.id;";

        $rslt = Database::getInstance()->execute($stmt)->fetchAllAssoc();

        $entries = [];
        foreach ($rslt as $row) {
            $entries[] = [
                'date'      => $row['closing_date'],
                'status'    => $row['closing_status'],
                'time'      => $row['special_date_time']
            ];
        }

        $splittedSpecialDays = $this->splitSpecialClosedDays($entries);
        return $splittedSpecialDays;
    }

    private function selectClosingShopDayID($date) {
        $selectStmt = "SELECT id FROM tl_shop_closed_date WHERE date = '" . $date . "'";
        $selectRslt = Database::getInstance()->execute($selectStmt)->fetchAssoc();

        return $selectRslt ? $selectRslt['id'] : -1;
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

       $db->beginTransaction();

        try {
            if ($closingDayExists) {
                // update the closing day
                $presentDateID = $closingDayExists['id'];
                $updateResult = $this->updateShopClosingDay($presentDateID, $date, '4');

                if ($updateResult->affectedRows > 0) {
                    // update the special date time
                    $updateSpecialTimeResult = $this->updateShopClosingSpecialTime($presentDateID, $selectedTimes);

                    if ($updateSpecialTimeResult->affectedRows > 0) {
                        $response['success'] = true;
                        $response['message'] = "Row mit id: " . $presentDateID . ", Datum: " . $date . " und Status: " . 4 . " wurde erfolgreich geupdated.";
                    } else {
                        $response['message'] = "Fehler während des Versuchs Row mit id: " . $presentDateID . " und Zeitspanne: " . $specialTimes . " zu überschreiben!";
                        throw new \Exception("Failed to update special date time: " . $selectedTimes . " with parent date ID: " . $presentDateID);
                    }

                } else {
                    $response['message'] = "Fehler während des Versuchs Row mit id: " . $presentDateID . " zu überschreiben!";
                    throw new \Exception("Failed to update date: " . $date . " with id: " . $presentDateID);
                }

            } else {
                $insertResult = $this->insertShopClosingDayQuery($date, '4');
            

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
                    throw new \Exception("Failed to insert date: " . $date);
                }
            }
        } catch (\Exception $e) {
            $db->rollbackTransaction();
            \System::log("Transaction failed while trying to insert special date with time: " . $e->getMessage(), __METHOD__, "TL_ERROR");
            $response['message'] = $e->getMessage();
        }
        return $response;
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


    private function updateShopClosingSpecialTime($dateID, $selectedTimes) {
        $tstamp = time();
        $updateStmt = "UPDATE tl_shop_closed_special_date_time SET tstamp ='" . $tstamp . "', time='" . $selectedTimes . "' WHERE fk_closed_date_id =" . $dateID;
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
            $insertStmt = "INSERT INTO tl_shop_closed_special_date_time (tstamp, time, fk_closed_date_id) VALUES (" . $tstamp . ",'" . $specialTimes . "'," . $dateQueryID . ")";
            $insertResult = Database::getInstance()->execute($insertStmt);

            return $insertResult;
        }


    public function deleteNormalShopClosingDay($date, $status) {
        $statusConvert = [
            'fullyClosed'       => '1',
            'closedAtMorning'   => '2',
            'closedAtEvening'   => '3',
            'closedIndividual'  => '4',
        ];
        $convertedStatus =  $statusConvert[$status];

        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            // Check if status is individual and delete special time when true.
            if ($convertedStatus === '4') {
                $closingDateID = $this->selectClosingShopDayID($date);
                // select the id
                if ($closingDateID === -1) {
                    throw new \Exception("Error while trying to select present ID of date: " . $date);
                }
                $deleteSpecialTimeStmt = "DELETE FROM tl_shop_closed_special_date_time WHERE fk_closed_date_id = " . $closingDateID;
                $deleteSpecialTimeRslt = $db->execute($deleteSpecialTimeStmt);

                if ($deleteSpecialTimeRslt->affectedRows === 0) {
                    throw new \Exception("Error while trying to delete special date time with parent ID: " . $closingDateID);
                }
            }

            $stmt = "DELETE FROM tl_shop_closed_date WHERE date='" . $date . "' AND fk_status_id='" . $convertedStatus . "'";
            $result =$db->execute($stmt);

            // Check how many rows were affected
            if ($result->affectedRows > 0) {
                // Rows were deleted
                $db->commitTransaction();
                return 1;
            } else {
                // No rows were deleted
                throw new \Exception("Error while trying to delete closing date row");
            }
        } catch (\Exception $e) {
            System::log($e->getMessage(),__METHOD__,"TL_ERROR");
            $db->rollbackTransaction();
        }
    }


    private function splitSpecialClosedDays($entries) {
        $specialDays = [
            'fullyClosed' => [],
            'closedAtMorning' => [],
            'closedAtEvening' => [],
            'closedIndividual' => []
        ];

        foreach ($entries as $entry) {
            $date = $entry['date'];
            $status = $entry['status'];
            $time = $entry['time'];

            // shop is closed the whole day
            if ($status === '1') {
                $specialDays['fullyClosed'][] = $date;

            // shop is closed at morning
            } else if ($status === '2') {
                $specialDays['closedAtMorning'][] = $date;

            // shop is closed at evening 
            } else if ($status === '3') {
                $specialDays['closedAtEvening'][] = $date;
            
            // shop is closed to individual times
            } else if ($status === '4') {
                $specialDays['closedIndividual'][] = $date;
            } 
        }
        return $specialDays;
    }
}
?>