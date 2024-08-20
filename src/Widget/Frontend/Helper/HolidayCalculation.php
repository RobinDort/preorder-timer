<?php
namespace RobinDort\PreorderTimer\Widget\Frontend\Helper;
use Umulmrum\Holiday\HolidayCalculator;

// This must be switched according to the region.
use Umulmrum\Holiday\Provider\Germany\Saarland;


class HolidayCalculation {

    private $holidays;

    public function __construct() {
        $this->initHolidaysForYear();
	}

    private function initHolidaysForYear() {
        $currentYear = (int) date('Y');
    	$holidayCalculator = new HolidayCalculator();
		$this->holidays = $holidayCalculator->calculate(Saarland::class, $currentYear);
    }
    

    public function isHolidayToday() {
        $currentDay = date('Y-m-d');
    	return $this->holidays->isHoliday(new \DateTime('2024-01-01'));
    }

}

?>