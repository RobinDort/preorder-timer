<?php
namespace RobinDort\PreorderTimer\Backend\Validation;

use Isotope\Model\Order;
use Contao\Database;
use Umulmrum\Holiday\HolidayCalculator;
use Umulmrum\Holiday\Provider\Germany\Saarland;


enum OrderPeriod {
    case NoOrderTime;
	case MorningTime;
    case PreOrderTime;
    case FirstOrderTime;
    case AfterFirstOrderTime;
    case SecondOrderTime;
}

class DayValidator {


    private $holidays;
	private OrderPeriod $orderPeriod;
	
	
	public function __construct() {
		$this->initHolidaysForYear();
		$this->checkOrderTime();
	}



    public function checkOrderTime() {
    	$current_time = time();
    	$current_day_of_week = $this->getWeekDay();

		$morning_start_time = strtotime('00:00');
		$morning_end_time = strtotime('09:59');
    	$preorder_start_time = strtotime('10:00');
    	$preorder_end_time = strtotime('11:59');
    	$pavillion_start_time = strtotime('12:00');
    	$pavillion_end_time = strtotime('14:00');
    	$after_pavillion_start_time = strtotime('14:01');
    	$after_pavillion_end_time = strtotime('16:59');
    	$roden_start_time = strtotime('17:00');
    	$roden_end_time = $current_day_of_week === 6 || $current_day_of_week === 0 || $this->isHolidayToday() === true ? strtotime('22:00') : strtotime('21:00') ;
    	$after_roden_start_time = $current_day_of_week === 6 || $current_day_of_week === 0 || $this->isHolidayToday() === true ? strtotime('22:01') : strtotime('21:01') ;
    	$after_roden_end_time = strtotime('23:59');
    	
    		//24 day format
    	$current_hour_minute = date('H:i', $current_time);
    	
    	//timestamp format
	    $current_time_stamp = strtotime($current_hour_minute);
	    
	    if ($current_time_stamp >= $morning_start_time && $current_time_stamp <= $morning_end_time) {
            $this->orderPeriod = OrderPeriod::MorningTime;
	    
        } elseif ($current_time_stamp >= $preorder_start_time && $current_time_stamp <= $preorder_end_time) {
            $this->orderPeriod = OrderPeriod::PreOrderTime;
            
        } elseif ($current_time_stamp >= $pavillion_start_time && $current_time_stamp <= $pavillion_end_time) {
            $this->orderPeriod = OrderPeriod::FirstOrderTime;
            
        } elseif ($current_time_stamp >= $after_pavillion_start_time && $current_time_stamp <= $after_pavillion_end_time) {
            $this->orderPeriod = OrderPeriod::AfterFirstOrderTime;
            
        } elseif ($current_time_stamp >= $roden_start_time && $current_time_stamp <= $roden_end_time) {
            $this->orderPeriod = OrderPeriod::SecondOrderTime;
            
        } elseif ($current_time_stamp >= $after_roden_start_time || $current_time_stamp <= $after_roden_end_time) {
            $this->orderPeriod = OrderPeriod::NoOrderTime;
            
        } 
	    
    }

    
    
    private function initHolidaysForYear() {
    	$currentYear = (int) date('Y');
    	$holidayCalculator = new HolidayCalculator();
		$this->holidays = $holidayCalculator->calculate(Saarland::class, $currentYear);
    }
    
    private function isHolidayToday() {
    	$currentDay = date('Y-m-d');
    	return $this->holidays->isHoliday(new \DateTime($currentDay));
    }

    public function isChristmasDay() {
    	$date = date('d.m');
    	
    	if ($date === '25.12' || $date === '26.12') {
    		return true;
    	}
    	
    	return false;
    }
    


    public function getWeekDay() {
    	$current_day_of_week = date("w"); // get as short number -> 1,2,3,4,5,6,0. 0 is sunday.
    	return $current_day_of_week;
    }


    public function getOrderTime() {
    	return $this->orderPeriod;
    }
    
    public function setOrderTime(OrderPeriod $period) {
    	$this->orderPeriod = $period;
    }

}
?>