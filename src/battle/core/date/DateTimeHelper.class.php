<?php
class DateTimeHelper{
	public static function get_remaining_time_formated($to_date){
		$now = new DateTime();
		$datetime2 = $to_date;
		$interval = $now->diff($datetime2);
		/*return $interval->format('%R%a days');*/
		/*return $interval->format('%a days');*/
		/*return $interval->format("%d days, %h hours, %i minutes");*/
		//return $interval->format("J-%d, H-%h, M-%i");
		/*return $interval->format("J-%a H-%h M-%i");*/
		$sign = ($interval->format("%R") === "-") ? "+" : "-"; 
		return $interval->format("J {$sign} %a");
	}
}
?>