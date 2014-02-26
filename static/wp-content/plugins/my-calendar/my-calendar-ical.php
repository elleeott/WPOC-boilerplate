<?php
function my_calendar_ical() {

$p = ( isset($_GET['span']) )?'year':false;
$y = ( isset($_GET['yr']) )?$_GET['yr']:date('Y');
$m = ( isset($_GET['month']) )?$_GET['month']:date('n');

if ( $p ) {
	$from = "$y-1-1";
	$to = "$y-12-31";
} else {
	$d = date( 't',mktime( 0,0,0,$m,1,$y ) );
	$from = "$y-$m-1";
	$to = "$y-$m-$d";
}

global $mc_version;
// establish template
	$template = "BEGIN:VEVENT
UID:{dateid}-{id}
LOCATION:{ical_location}
SUMMARY:{title}
DTSTAMP:{ical_start}
ORGANIZER;CN={host}:MAILTO:{host_email}
DTSTART:{ical_start}
DTEND:{ical_end}
URL;VALUE=URI:{link}
DESCRIPTION:{ical_desc}
CATEGORIES:{category}
END:VEVENT";
// add ICAL headers
$output = 'BEGIN:VCALENDAR
VERSION:2.0
METHOD:PUBLISH
PRODID:-//Accessible Web Design//My Calendar//http://www.mywpcal.com//v'.$mc_version.'//EN';

	$events = my_calendar_grab_events( $from, $to );
	if ( is_array($events) && !empty($events) ) {
		foreach ( array_keys($events) as $key) {
			$event =& $events[$key];
			if ( is_object($event) ) {
				if ( !( $event->category_private == 1 && !is_user_logged_in() ) ) {
				$array = event_as_array($event);
				$output .= "\n".jd_draw_template($array,$template,'ical');
				}
			}
		}
	}
$output .= "\nEND:VCALENDAR";
$output = html_entity_decode(preg_replace("~(?<!\r)\n~","\r\n",$output));
	header("Content-Type: text/calendar; charset=".get_bloginfo('charset'));
	header("Pragma: no-cache");
	header("Expires: 0");		
	header("Content-Disposition: inline; filename=my-calendar.ics");
	echo $output;
}
?>