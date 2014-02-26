<?php
// used to generate upcoming events lists
function mc_get_all_events( $category, $before, $after, $today, $author, $host ) {
global $wpdb;
	$mcdb = $wpdb;
if ( get_option( 'mc_remote' ) == 'true' && function_exists('mc_remote_db') ) { $mcdb = mc_remote_db(); }
	$select_category = ( $category!='default' )?mc_select_category($category):'';
	$limit_string = mc_limit_string();
	$select_author = ( $author != 'default' )?mc_select_author($author):'';
	$select_host = ( $host != 'default' )?mc_select_host($host):'';
	
	$date = date('Y', current_time('timestamp')).'-'.date('m', current_time('timestamp')).'-'.date('d', current_time('timestamp'));
	// if a value is non-zero, I'll grab a handful of extra events so I can throw out holidays and others like that.
	if ( $before > 0 ) {
		$before = $before + 5;
		$events1 = $mcdb->get_results("SELECT * 
		FROM " . MY_CALENDAR_EVENTS_TABLE . " 
		JOIN " . MY_CALENDAR_TABLE . " 
		ON (event_id=occur_event_id) 
		JOIN " . MY_CALENDAR_CATEGORIES_TABLE . " 
		ON (event_category=category_id) WHERE $select_category $select_author $select_host $limit_string event_approved = 1 AND event_flagged <> 1 
		AND DATE(occur_begin) < '$date' ORDER BY occur_begin DESC LIMIT 0,$before");
	} else { $events1 = array(); }
	if ( $today == 'yes' ) {
		$events3 = $mcdb->get_results("SELECT * 
		FROM " . MY_CALENDAR_EVENTS_TABLE . " 
		JOIN " . MY_CALENDAR_TABLE . " 
		ON (event_id=occur_event_id) 
		JOIN " . MY_CALENDAR_CATEGORIES_TABLE . " 
		ON (event_category=category_id) WHERE $select_category $select_author $select_host $limit_string event_approved = 1 AND event_flagged <> 1 
		AND DATE(occur_begin) = '$date'");	
	} else {
		$events3 = array();
	}
	if ( $after > 0 ) {
		$after = $after + 5;
		$events2 = $mcdb->get_results("SELECT * 
		FROM " . MY_CALENDAR_EVENTS_TABLE . " 
		JOIN " . MY_CALENDAR_TABLE . " 
		ON (event_id=occur_event_id) 
		JOIN " . MY_CALENDAR_CATEGORIES_TABLE . " 
		ON (event_category=category_id) WHERE $select_category $select_author $select_host $limit_string event_approved = 1 AND event_flagged <> 1 
		AND DATE(occur_begin) > '$date' ORDER BY occur_begin ASC LIMIT 0,$after");
	} else { $events2 = array(); }
	$arr_events = array();
    if (!empty($events1) || !empty($events2) || !empty($events3) ) {
		$arr_events = array_merge( $events1, $events3, $events2);
	} 
	return $arr_events;
}

function mc_get_all_holidays( $before, $after, $today ) {
	if ( !get_option('mc_skip_holidays_category') ) { return array(); }
	global $wpdb;
	$mcdb = $wpdb;
if ( get_option( 'mc_remote' ) == 'true' && function_exists('mc_remote_db') ) { $mcdb = mc_remote_db(); }
	$holiday = get_option('mc_skip_holidays_category');
	$date = date('Y', current_time('timestamp') ).'-'.date('m', current_time('timestamp') ).'-'.date('d', current_time('timestamp') );
	// if a value is non-zero, I'll grab a handful of extra events so I can throw out holidays and others like that.
	if ( $before > 0 ) {
		$before = $before + 5;
		$events1 = $mcdb->get_results("SELECT * 
		FROM " . MY_CALENDAR_EVENTS_TABLE . " 
		JOIN " . MY_CALENDAR_TABLE . " 
		ON (event_id=occur_event_id) 
		JOIN " . MY_CALENDAR_CATEGORIES_TABLE . " 
		ON (event_category=category_id) WHERE event_category = $holiday AND event_approved = 1 AND event_flagged <> 1 
		AND DATE(occur_begin) < '$date' ORDER BY occur_begin DESC LIMIT 0,$before");
	} else { $events1 = array(); }
	if ( $today == 'yes' ) {
		$events3 = $mcdb->get_results("SELECT * 
		FROM " . MY_CALENDAR_EVENTS_TABLE . " 
		JOIN " . MY_CALENDAR_TABLE . " 
		ON (event_id=occur_event_id) 
		JOIN " . MY_CALENDAR_CATEGORIES_TABLE . " 
		ON (event_category=category_id) WHERE event_category = $holiday AND event_approved = 1 AND event_flagged <> 1 
		AND DATE(occur_begin) = '$date'");	
	} else {
		$events3 = array();
	}
	if ( $after > 0 ) {
		$after = $after + 5;
		$events2 = $mcdb->get_results("SELECT * 
		FROM " . MY_CALENDAR_EVENTS_TABLE . " 
		JOIN " . MY_CALENDAR_TABLE . " 
		ON (event_id=occur_event_id) 
		JOIN " . MY_CALENDAR_CATEGORIES_TABLE . " 
		ON (event_category=category_id) WHERE event_category = $holiday AND  event_approved = 1 AND event_flagged <> 1 
		AND DATE(occur_begin) > '$date' ORDER BY occur_begin ASC LIMIT 0,$after");
	} else { $events2 = array(); }
	$arr_events = array();
    if (!empty($events1) || !empty($events2) || !empty($events3) ) {
		$arr_events = array_merge( $events1, $events3, $events2);
	} 
	return $arr_events;
}

function mc_get_rss_events( $cat_id=false) { // JCD TODO: figure out how to output in RSS given new event circumstances...
	global $wpdb;
	$mcdb = $wpdb;
	if ( get_option( 'mc_remote' ) == 'true' && function_exists('mc_remote_db') ) { $mcdb = mc_remote_db(); }
	if ( $cat_id ) { $cat = "WHERE event_category = $cat_id AND event_approved = 1"; } else { $cat = 'WHERE event_approved = 1'; }
	$events = $mcdb->get_results("SELECT * FROM " .  MY_CALENDAR_EVENTS_TABLE . " JOIN " . MY_CALENDAR_TABLE . " ON (event_id=occur_event_id) JOIN " . MY_CALENDAR_CATEGORIES_TABLE . " ON (event_category=category_id) $cat ORDER BY event_added DESC LIMIT 0,30" );
	foreach ( array_keys($events) as $key ) {
		$event =& $events[$key];	
		$output[] = $event;
	}
	return $output;
}

// get event basic info
function mc_get_event_core( $id ) {
	global $wpdb;
	$mcdb = $wpdb;
	if ( get_option( 'mc_remote' ) == 'true' && function_exists('mc_remote_db') ) { $mcdb = mc_remote_db(); }
    $event = $mcdb->get_row("SELECT * FROM " . MY_CALENDAR_TABLE . " JOIN " . MY_CALENDAR_CATEGORIES_TABLE . " ON (event_category=category_id) WHERE event_id=$id");
	return $event;
}

// get event instance (object or html)
function mc_get_event( $id,$type='object' ) {
// indicates whether you want a specific instance, or a general event
	global $wpdb;
	$mcdb = $wpdb;
	if ( get_option( 'mc_remote' ) == 'true' && function_exists('mc_remote_db') ) { $mcdb = mc_remote_db(); }
    $event = $mcdb->get_row("SELECT * FROM " .  MY_CALENDAR_EVENTS_TABLE . " JOIN " . MY_CALENDAR_TABLE . " ON (event_id=occur_event_id) JOIN " . MY_CALENDAR_CATEGORIES_TABLE . " ON (event_category=category_id) WHERE occur_id=$id");
	$date = date('Y-m-d',strtotime($event->occur_begin) );
	if ( $type == 'object' ) {
	return $event;
	} else {
		$value = "	<div id='mc_event'>".my_calendar_draw_event( $event,'single',"$date",'single' )."</div>\n";
		return $value;
	}
}

// Grab all events for the requested date from calendar
function my_calendar_grab_events($from, $to,$category=null,$ltype='',$lvalue='',$source='calendar',$author=null, $host=null) {
	if ( isset($_GET['mcat']) ) { $ccategory = $_GET['mcat']; } else { $ccategory = $category; }
	if ( isset($_GET['ltype']) ) { $cltype = $_GET['ltype']; } else { $cltype = $ltype; }
	if ( isset($_GET['loc']) ) { $clvalue = $_GET['loc']; } else { $clvalue = $lvalue; }
	if ( isset($_GET['mc_auth']) ) { $clauth = $_GET['mc_auth']; } else { $clauth = $author; }
	if ( isset($_GET['mc_host']) ) { $clhost = $_GET['mc_host']; } else { $clhost = $host; }
	
	if ( $ccategory == '' ) { $ccategory = 'all'; }
	if ( $clvalue == '' ) { $clvalue = 'all';  }			
	if ( $cltype == '' ) { $cltype = 'all'; }
	if ( $clvalue == 'all' ) { $cltype = 'all'; }
	if ( $clauth == '' ) { $clauth = 'all'; }
	if ( $clhost == '' ) { $clhost = 'all'; }

	if ( !mc_checkdate($from) || !mc_checkdate($to) ) { return; } // not valid dates
	$caching = ( get_option('mc_caching_enabled') == 'true' )?true:false;
	$hash = md5($from.$to.$ccategory.$cltype.$clvalue.$clauth.$clhost);
	if ( $source != 'upcoming' ) { // no caching on upcoming events by days widgets or lists
		if ( $caching ) {
			$output = mc_check_cache( $ccategory, $cltype, $clvalue, $clauth, $clhost, $hash );
			if ( $output && $output != 'empty' ) { return $output; }
			if ( $output == 'empty' ) { return; }
		}
	}
    global $wpdb;
	$mcdb = $wpdb;
	if ( get_option( 'mc_remote' ) == 'true' && function_exists('mc_remote_db') ) { $mcdb = mc_remote_db(); }
	$select_category = ( $category != null )?mc_select_category($category):'';
	$select_author = ( $author != null )?mc_select_author($author):'';	
	$select_host = ( $author != null )?mc_select_host($host):'';		
	$select_location = mc_limit_string( 'grab', $ltype, $lvalue );

	if ( $caching && $source != 'upcoming' ) { $select_category = ''; $select_location = ''; $select_author = ''; $select_host = ''; } 
	// if caching, then need all categories/locations in cache. UNLESS this is an upcoming events list

    $arr_events = array();
	$limit_string = "event_flagged <> 1 AND event_approved = 1";

	$event_query = "SELECT * 
					FROM " . MY_CALENDAR_EVENTS_TABLE . " 
					JOIN " . MY_CALENDAR_TABLE . "
					ON (event_id=occur_event_id) 					
					JOIN " . MY_CALENDAR_CATEGORIES_TABLE . " 
					ON (event_category=category_id) 
					WHERE $select_category $select_location $select_author $select_host $limit_string 
					AND ( DATE(occur_begin) BETWEEN '$from 00:00:00' AND '$to 23:59:59' 
						OR DATE(occur_end) BETWEEN '$from 00:00:00' and '$to 23:59:59' 
						OR ( DATE('$from') BETWEEN DATE(occur_begin) AND DATE(occur_end) ) 
						OR ( DATE('$to') BETWEEN DATE(occur_begin) AND DATE(occur_end) )) 
						ORDER BY occur_begin";
	$events = $mcdb->get_results( $event_query );
	if (!empty($events)) {
			foreach( array_keys($events) as $key) {
			$event =& $events[$key];
				$arr_events[] = $event;
			}
     	}
	if ( $source != 'upcoming' && $caching ) { 
		$new_cache = mc_create_cache( $arr_events, $hash );
		if ( $new_cache ) {
			$output = mc_check_cache( $ccategory, $cltype, $clvalue, $clauth, $clhost, $hash );
			return $output; 
		} else { 
			// need to clean cache if the cache is maxed.
			return mc_clean_cache( $arr_events, $ccategory, $cltype, $clvalue, $clauth, $clhost ); 
		}		
	} else {
		return $arr_events;
	}
}

function mc_check_cache( $category, $ltype, $lvalue, $auth, $host, $hash) {
	$caching = ( get_option('mc_caching_enabled') == 'true' )?true:false;
	if ( $caching == true ) {
		$cache = get_transient("mc_cache");
		if ( isset( $cache[$hash] ) ) {
			$value = $cache[$hash];
		} else {
			return false;
		}
		if ( $value ) { return mc_clean_cache($value, $category,$ltype,$lvalue,$auth,$host); } else { return false; }
	} else {
		return false;
	}
}

function mc_clean_cache( $cache, $category, $ltype, $lvalue, $auth, $host ) {
global $wpdb;
	$mcdb = $wpdb;
	// process cache to strip events which do not meet current restrictions
	if ( $cache == 'empty' ) return false;
	$type = ($ltype != 'all')?"event_$ltype":"event_state";
	$return = false;
	if ( is_array($cache) ) {
			if ( strpos( $category, ',' ) !== false ) {
				$cats = explode(',',$category);
			} else if ( strpos( $category, '|' ) !== false ) {
				$cats = explode('|',$category);
			} else {
				$cats = array( $category );
			}
			if ( strpos( $auth, ',' ) !== false ) {
				$authors = explode(',',$auth);
			} else if ( strpos( $auth, '|' ) !== false ) {
				$authors = explode('|',$auth);
			} else {
				$authors = array($auth);
			}
			if ( strpos( $host, ',' ) !== false ) {
				$authors = explode(',',$host);
			} else if ( strpos( $host, '|' ) !== false ) {
				$authors = explode('|',$host);
			} else {
				$hosts = array($host);
			}			
			foreach ( $authors as $k=>$v ) {
				if ( !is_numeric($v) && $v != 'all' ) { 
					$u = get_user_by('login',$v);
					$id = $u->ID;
					$authors[$k]= $id;
				}
			}
			foreach ( $hosts as $k=>$v ) {
				if ( !is_numeric($v) && $v != 'all' ) { 
					$u = get_user_by('login',$v);
					$id = $u->ID;
					$hosts[$k]= $id;
				}
			}			
		foreach ( $cache as $key=>$value ) {
			foreach ( $cats as $cat ) {
				if ( is_numeric($cat) ) { $cat = (int) $cat; }
				if ( ( $value->event_category == $cat || $category == 'all' || $value->category_name == $cat ) 
						&& ( $value->event_author == $auth || $auth == 'all' || in_array( $value->event_author,$authors ) )
						&& ( $value->event_host == $host || $host == 'all' || in_array( $value->event_host,$hosts ) )
						&& ( $value->$type == $lvalue || ( $ltype == 'all' && $lvalue == 'all' ) ) ) {				
					$return[$key]=$value;
				} 
			}
		}
		return $return;
	}
}

function mc_create_cache($arr_events, $hash ) {
	$caching = ( get_option('mc_caching_enabled') == 'true' )?true:false;
	if ( $arr_events == false ) { $arr_events = 'empty'; }
	if ( $caching == true ) {
		$before = memory_get_usage();
		$ret = get_transient("mc_cache");
		$after = memory_get_usage();
		$mem_limit = mc_allocated_memory( $before, $after );
		if ( $mem_limit ) { return false; } // if cache is maxed, don't add additional references. Cache expires every two days.
		$cache = get_transient("mc_cache");		
		$cache[$hash] = $arr_events;
		set_transient( "mc_cache",$cache, 60*60*48 );
		return true;
	}
	return false;
}

function mc_allocated_memory($before, $after) {
    $size = ($after - $before);
	$total_allocation = str_replace('M','',ini_get('memory_limit'))*1048576; // CONVERT TO BYTES
	$limit =  $total_allocation/64;
	// limits each cache to occupying 1/64 of allowed PHP memory (usually will be between 125K and 1MB). 
	if ( $size > $limit ) { return true; } else { return false; }
}

function mc_delete_cache() {
	delete_transient( 'mc_cache' );
	delete_transient( 'mc_todays_cache' );
	delete_transient( 'mc_cache_upcoming' );
}