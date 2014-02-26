<?php 

function mc_dateclass( $now, $current ) {
	if ( date("Ymd",$now) == date("Ymd", $current ) ) {
		$dateclass = 'current-day';
	} else if ( my_calendar_date_comp( date('Y-m-d',$now), date('Y-m-d',$current) ) ) {
		$dateclass = 'future-day';
	} else {
		$dateclass = 'past-day past-date'; // stupid legacy classes.
	}
	return $dateclass;
}

// receives: time string, amount to add; returns: timestamp
function my_calendar_add_date($givendate,$day=0,$mth=0,$yr=0) {
	$cd = strtotime($givendate);
	$newdate = mktime(date('H',$cd),date('i',$cd), date('s',$cd), date('m',$cd)+$mth,date('d',$cd)+$day, date('Y',$cd)+$yr);
	return $newdate;
}
//returns true if the date is before or equal,
function my_calendar_date_comp($early,$late) {
	$firstdate = strtotime($early);
	$lastdate = strtotime($late);
	if ($firstdate <= $lastdate) {
		return true;
	} else {
		return false;
	}
}
// true if first date before second date
function my_calendar_date_xcomp($early,$late) {
	$firstdate = strtotime($early);
	$lastdate = strtotime($late);
	if ($firstdate < $lastdate) {
		return true;
	} else {
		return false;
	}
}
// true if dates are the same
function my_calendar_date_equal($early,$late) {
	$firstdate = strtotime($early);
	$lastdate = strtotime($late);
	if ($early == $late) {
		return true;
	} else {
		return false;
	}	
}

// Function to compare time in event objects
function my_calendar_time_cmp($a, $b) {
	if ( $a->occur_begin == $b->occur_begin ) {
		return 0;
	}
	return ( $a->occur_begin < $b->occur_begin ) ? -1 : 1;
}

// Function to compare datetime in event objects
function my_calendar_datetime_cmp($a, $b) {
	$event_dt_a = strtotime( $a->occur_begin );
	$event_dt_b = strtotime( $b->occur_begin );
	if ( $event_dt_a == $event_dt_b ) {
	// this should sub-sort by title if date is the same. But it doesn't seem to...
		$ta = $a->event_title;
		$tb = $b->event_title;
		return strcmp( $ta, $tb );
	}
	return ( $event_dt_a < $event_dt_b ) ? -1 : 1;
}

// reverse Function to compare datetime in event objects
function my_calendar_reverse_datetime_cmp($b, $a) {
	$event_dt_a = strtotime($a->occur_begin);
	$event_dt_b = strtotime($b->occur_begin);
  if ($event_dt_a == $event_dt_b ) {
    return 0;
  }
  return ( $event_dt_a < $event_dt_b ) ? -1 : 1;
}

function my_calendar_timediff_cmp($a, $b) {
	$event_dt_a = strtotime($a->occur_begin);
	$event_dt_b = strtotime($b->occur_begin);
	$diff_a = jd_date_diff_precise($event_dt_a);
	$diff_b = jd_date_diff_precise($event_dt_b);
	
	if ( $diff_a == $diff_b ) {
		return 0;
	}
	return ( $diff_a < $diff_b ) ? -1 : 1;
}

function jd_date_diff_precise($start,$end="NOW") {
        if ($end == "NOW") {
			$end = strtotime("NOW");
		}
		$sdate = $start;
        $edate = $end;

        $time = $edate - $sdate;
		
		return abs($time);
}

function jd_date_diff($start, $end="NOW") {
        $sdate = strtotime($start);
        $edate = strtotime($end);

        $time = $edate - $sdate;		
		if ($time < 86400 && $time > -86400) {
			return false;
		} else {
            $pday = ($edate - $sdate) / 86400;
            $preday = explode('.',$pday);		
			return $preday[0];
		}
}
// @param integer $date_of_event The current month's date;
// @return integer $week_of_event The week of the month this date falls in;
function week_of_month($date_of_event) {
					switch ($date_of_event) {
						case ($date_of_event>=1 && $date_of_event <8):
						$week_of_event = 0;
						break;
						case ($date_of_event>=8 && $date_of_event <15):
						$week_of_event = 1;
						break;
						case ($date_of_event>=15 && $date_of_event <22):
						$week_of_event = 2;
						break;
						case ($date_of_event>=22 && $date_of_event <29):
						$week_of_event = 3;
						break;		
						case ($date_of_event>=29):
						$week_of_event = 4;
						break;
					}
					return $week_of_event;
}

/**
 * Function to find the start date of a week in a year
 * @param integer $week The week number of the year
 * @param integer $year The year of the week we need to calculate on
 * @param string  $start_of_week The start day of the week you want returned
 * @return integer The unix timestamp of the date is returned
 */
function get_week_date( $week, $year ) {
	// Get the target week of the year with reference to the starting day of
	// the year
	$start_of_week = (get_option('start_of_week')==1||get_option('start_of_week')==0)?get_option('start_of_week'):0;
	$week_adjustment = ($start_of_week == 0)?0:1;

	$target_week = strtotime("$week week", strtotime("1 January $year"));
	$date_info = getdate($target_week);
	$day_of_week = $date_info['wday'];
	// normal start day of the week is Monday
	$adjusted_date = $day_of_week - $start_of_week;
	// Get the timestamp of that day
	$first_day = strtotime("-$adjusted_date day",$target_week);
	return $first_day;
}

function add_days_to_date( $givendate,$day=0 ) {
    $cd = strtotime($givendate);
    $newdate = date('Y-m-d h:i:s',
		mktime(
			date('h',$cd),
			date('i',$cd), 
			date('s',$cd), 
			date('m',$cd),
			date('d',$cd)+$day, 
			date('Y',$cd)
		) );
      return $newdate;
}

function mc_checkdate($date) {
	$time = strtotime($date);
	$m = date('n',$time);
	$d = date('j',$time);
	$y = date('Y',$time);
	return checkdate($m,$d,$y);
}

function first_day_of_week() {
	$start_of_week = (get_option('start_of_week')==1||get_option('start_of_week')==0)?get_option('start_of_week'):0;
	$today = date('w',current_time('timestamp'));
	$now = date('Y-m-d',current_time('timestamp'));
	$month = 0; // don't change month
	switch ($today) {
		case 1:	$sub = ($start_of_week == 1)?0:1;break; // mon
		case 2:	$sub = ($start_of_week == 1)?1:2;break; // tues
		case 3:	$sub = ($start_of_week == 1)?2:3;break; // wed
		case 4:	$sub = ($start_of_week == 1)?3:4;break; // thu
		case 5:	$sub = ($start_of_week == 1)?4:5;break; // fri
		case 6:	$sub = ($start_of_week == 1)?5:6;break; // sat
		case 0:	$sub = ($start_of_week == 1)?6:0;break; // sun
	}
	$day = date('j',strtotime( $now . ' -'.$sub.' day'));
	if ( $sub != 0 ) {
		if ( date('n',strtotime( $now . ' -'.$sub.' day') ) != date( 'n',strtotime($now) ) ) {
			$month = -1;
		} else {
			$month = 0;
		}
	}
	return array( $day, $month );
}

function mc_ordinal($number) {
    // when fed a number, adds the English ordinal suffix. Works for any
    // number, even negatives
    if ($number % 100 > 10 && $number %100 < 14) {
        $suffix = "th";
    } else {
        switch($number % 10) {
            case 0:$suffix = "th";break;
            case 1:$suffix = "st";break;
            case 2:$suffix = "nd";break;
            case 3:$suffix = "rd";break;
            default:$suffix = "th";break;
        }
    }
	return "${number}$suffix";
}

?>