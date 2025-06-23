<?php
namespace RobinDort\PreorderTimer\Widget\Frontend\Helper;
use Umulmrum\Holiday\HolidayCalculator;

// This must be switched according to the region.
use Umulmrum\Holiday\Provider\Germany\Saarland;
use Umulmrum\Holiday\Filter\IncludeTypeFilter;
use Umulmrum\Holiday\Constant\HolidayType;



class HolidayCalculation {

    private $holidays;

    public function __construct() {
        $this->initHolidaysForYear();
	}

    private function initHolidaysForYear() {
        $currentYear = (int) date('Y');
    	$holidayCalculator = new HolidayCalculator();
		$this->holidays = $holidayCalculator->calculate(Saarland::class, $currentYear);
        $this->holidays = $this->holidays->filter(new IncludeTypeFilter(HolidayType::DAY_OFF));
    }
    

    public function isHolidayToday() {
        $currentDay = date('Y-m-d');
        return $this->holidays->isHoliday(new \DateTime($currentDay));
    }

    public function isHolidayForDate($date) {
        return $this->holidays->isHoliday(new \DateTime($date));
    }

    public function getHolidays() {
        return $this->holidays;
    }
}

?>