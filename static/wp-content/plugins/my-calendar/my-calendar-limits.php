<?php
function mc_select_category($category, $type='event', $group='events' ) {
$category = urldecode($category);
global $wpdb;
	$mcdb = $wpdb;
	  if ( get_option( 'mc_remote' ) == 'true' && function_exists('mc_remote_db') ) { $mcdb = mc_remote_db(); }
	$select_category = '';
	$data = ($group=='category')?'category_id':'event_category';
	if ( isset( $_GET['mcat'] ) ) { $category = $_GET['mcat']; }
	if ( preg_match('/^all$|^all,|,all$|,all,/i', $category) > 0 ) {
		return '';
	} else {
 	if ( strpos( $category, "|" ) || strpos( $category, "," ) ) {
		if ( strpos($category, "|" ) ) {
			$categories = explode( "|", $category );
		} else {
			$categories = explode( ",", $category );		
		}
		$numcat = count($categories);
		$i = 1;
		foreach ($categories as $key) {
			if ( is_numeric($key) ) {
				$key = (int) $key;
				if ($i == 1) { $select_category .= ($type=='all')?" WHERE (":' ('; }				
				$select_category .= " $data = $key";
				if ($i < $numcat) {
					$select_category .= " OR ";
				} else if ($i == $numcat) {
					$select_category .= ($type=='all')?") ":' ) AND';
				}
			$i++;
			} else {
				$key = esc_sql(trim($key));
				$cat = $mcdb->get_row("SELECT category_id FROM " . my_calendar_categories_table() . " WHERE category_name = '$key'");
				$category_id = $cat->category_id;
				if ($i == 1) {	$select_category .= ($type=='all')?" WHERE (":' (';	}
				$select_category .= " $data = $category_id";
				if ($i < $numcat) {
					$select_category .= " OR ";
				} else if ($i == $numcat) {
					$select_category .= ($type=='all')?") ":' ) AND';
				}
				$i++;						
			}
		}
	} else {
		if ( is_numeric( $category ) ) {
			$select_category = ($type=='all')?" WHERE $data = $category":" event_category = $category AND";
		} else {
		$cat = $mcdb->get_row("SELECT category_id FROM " . my_calendar_categories_table() . " WHERE category_name = '$category'");
			if ( is_object($cat) ) {
				$category_id = $cat->category_id;
				$select_category = ($type=='all')?" WHERE $data = $category_id":" $data = $category_id AND";
			} else {
				$select_category = '';
			}
		}
	}
	return $select_category;
	}
}

function mc_select_author( $author, $type='event' ) {
$author = urldecode($author);
$key = '';
if ( $author == '' || $author == 'all' || $author == 'default' || $author == null ) { return; }
global $wpdb;
	$mcdb = $wpdb;
	if ( get_option( 'mc_remote' ) == 'true' && function_exists('mc_remote_db') ) { $mcdb = mc_remote_db(); }
	$select_author = '';
	$data = 'event_author';
	if ( isset( $_GET['mc_auth'] ) ) { $author = $_GET['mc_auth']; }
	if ( preg_match( '/^all$|^all,|,all$|,all,/i', $author ) > 0 ) {
		return '';
	} else {
 	if ( strpos( $author, "|" ) || strpos( $author, "," ) ) {
		if ( strpos($author, "|" ) ) {
			$authors = explode( "|", $author );
		} else {
			$authors = explode( ",", $author );		
		}
		$numauth = count($authors);
		$i = 1;
		foreach ($authors as $key) {
			if ( is_numeric($key) ) {
				$key = (int) $key;
				if ($i == 1) { $select_author .= ($type=='all')?" WHERE (":' ('; }				
				$select_author .= " $data = $key";
				if ($i < $numauth) {
					$select_author .= " OR ";
				} else if ($i == $numauth) {
					$select_author .= ($type=='all')?") ":' ) AND';
				}
			$i++;
			} else {
				$key = esc_sql(trim($key));
				$author = get_user_by( 'login', $key ); // get author by username
				$author_id = $author->ID;
				if ($i == 1) {	$select_author .= ($type=='all')?" WHERE (":' (';	}
				$select_author .= " $data = $author_id";
				if ($i < $numauth) {
					$select_author .= " OR ";
				} else if ($i == $numauth) {
					$select_author .= ($type=='all')?") ":' ) AND';
				}
				$i++;						
			}
		}
	} else {
		if ( is_numeric( $author ) ) {
			$select_author = ($type=='all')?" WHERE $data = $author":" event_author = $author AND";
		} else {
			$author = esc_sql(trim($author));
			$author = get_user_by( 'login', $author ); // get author by username
			
			if ( is_object($author) ) {
				$author_id = $author->ID;
				$select_author = ($type=='all')?" WHERE $data = $author_id":" $data = $author_id AND";
			} else {
				$select_author = '';
			}
		}
	}
	return $select_author;
	}
}

function mc_select_host( $host, $type='event' ) {
$host = urldecode($host);
$key = '';
if ( $host == '' || $host == 'all' || $host == 'default' || $host == null ) { return; }
global $wpdb;
	$mcdb = $wpdb;
	if ( get_option( 'mc_remote' ) == 'true' && function_exists('mc_remote_db') ) { $mcdb = mc_remote_db(); }
	$select_author = '';
	$data = 'event_host';
	if ( isset( $_GET['mc_auth'] ) ) { $host = $_GET['mc_host']; }
	if ( preg_match( '/^all$|^all,|,all$|,all,/i', $host ) > 0 ) {
		return '';
	} else {
 	if ( strpos( $host, "|" ) || strpos( $host, "," ) ) {
		if ( strpos($host, "|" ) ) {
			$hosts = explode( "|", $host );
		} else {
			$hosts = explode( ",", $host );		
		}
		$numhost = count($hosts);
		$i = 1;
		foreach ($hosts as $key) {
			if ( is_numeric($key) ) {
				$key = (int) $key;
				if ($i == 1) { $select_host .= ($type=='all')?" WHERE (":' ('; }				
				$select_host .= " $data = $key";
				if ($i < $numhost) {
					$select_host .= " OR ";
				} else if ($i == $numhost) {
					$select_host .= ($type=='all')?") ":' ) AND';
				}
			$i++;
			} else {
				$key = esc_sql(trim($key));
				$host = get_user_by( 'login', $key ); // get host by username
				$host_id = $host->ID;
				if ($i == 1) {	$select_host .= ($type=='all')?" WHERE (":' (';	}
				$select_host .= " $data = $host_id";
				if ($i < $numhost) {
					$select_host .= " OR ";
				} else if ($i == $numhost) {
					$select_host .= ($type=='all')?") ":' ) AND';
				}
				$i++;						
			}
		}
	} else {
		if ( is_numeric( $host ) ) {
			$select_host = ($type=='all')?" WHERE $data = $host":" event_host = $host AND";
		} else {
			$host = esc_sql(trim($host));
			$host = get_user_by( 'login', $host ); // get author by username
			
			if ( is_object($host) ) {
				$host_id = $host->ID;
				$select_host = ($type=='all')?" WHERE $data = $host_id":" $data = $host_id AND";
			} else {
				$select_host = '';
			}
		}
	}
	return $select_host;
	}
}

function mc_limit_string($type='',$ltype='',$lvalue='') {
global $user_ID;
	 $user_settings = get_option('mc_user_settings');
	 $limit_string = "";
	 if ( get_option('mc_user_settings_enabled') == 'true' && $user_settings['my_calendar_location_default']['enabled'] == 'on' || isset($_GET['loc']) && isset($_GET['ltype']) || ( $ltype !='' && $lvalue != '' )  ) {
		if ( !isset($_GET['loc']) && !isset($_GET['ltype']) ) {
			if (  $ltype == '' && $lvalue == '' ) {
				if ( is_user_logged_in() ) {
					get_currentuserinfo();
					$current_settings = get_user_meta( $user_ID, 'my_calendar_user_settings' );
					$current_location = $current_settings['my_calendar_location_default'];
					$location = get_option('mc_location_type');
				}
			} else if ( $ltype !='' && $lvalue != '' ) {	
				$location = $ltype;
				$current_location = esc_sql( $lvalue );
			}
		} else {
			$current_location = urldecode($_GET['loc']);
			$location = urldecode($_GET['ltype']);	
		}
				switch ($location) {
					case "name":$location_type = "event_label";
					break;
					case "city":$location_type = "event_city";
					break;
					case "state":$location_type = "event_state";
					break;
					case "zip":$location_type = "event_postcode";
					break;
					case "country":$location_type = "event_country";
					break;
					case "region":$location_type = "event_region";
					break;
					default:$location_type = $location;
					break;
				}			
		if ($current_location != 'all' && $current_location != '') {
				$limit_string = "$location_type='$current_location' AND";
				//$limit_string .= ($type=='all')?' AND':"";
		}
	 }
	 if ( $limit_string != '' ) {
		if ( isset($_GET['loc2']) && isset($_GET['ltype2']) ) {
			$limit_string .= mc_secondary_limit( $_GET['ltype2'],$_GET['loc2'] );
		}
	 }
	 return $limit_string;
}

// set up a secondary limit on location
function mc_secondary_limit($ltype='',$lvalue='') {
	$limit_string = "";
	$current_location = urldecode( $lvalue );
	$location = urldecode( $ltype );
	switch ($location) {
		case "name":$location_type = "event_label";
		break;
		case "city":$location_type = "event_city";
		break;
		case "state":$location_type = "event_state";
		break;
		case "zip":$location_type = "event_postcode";
		break;
		case "country":$location_type = "event_country";
		break;
		case "region":$location_type = "event_region";
		break;
		default:$location_type = "event_label";
		break;
	}	
	if ($current_location != 'all' && $current_location != '') {
			$limit_string = " $location_type='$current_location' AND ";
			//$limit_string .= ($type=='all')?' AND':"";
	}
	return $limit_string;
}