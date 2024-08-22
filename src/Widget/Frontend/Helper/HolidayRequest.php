<?php

use RobinDort\PreorderTimer\Widget\Frontend\Helper\HolidayCalculation;

if (isset($_POST["date"])) {
    $selectedDate = $_POST["date"];
    $holidayHelper = new HolidayCalculation();
    $response = ["isHoliday" => false];
    if ($holidayHelper->isHolidayForDate($selectedDate) === 1) {
        $response["isHoliday"] = true;
    }
    header('Content-Type: application/json');
    echo json_encode($response);
}

?>