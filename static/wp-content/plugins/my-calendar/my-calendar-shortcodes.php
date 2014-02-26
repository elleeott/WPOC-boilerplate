<?php
function my_calendar_insert($atts,$content=null) {
	extract(shortcode_atts(array(
				'name' => 'all',
				'format' => 'calendar',
				'category' => 'all',
				'showkey' => 'yes',
				'shownav' => 'yes',
				'showjump'=> '',
				'toggle' => 'no',
				'time' => 'month',
				'ltype' => '',
				'lvalue' => '',
				'author' => 'all',
				'host' => 'all',
				'id' => 'jd-calendar',
				'template' => ''
			), $atts));
	if ( $format != 'mini' ) {
		if ( isset($_GET['format']) ) {
			$format = mysql_real_escape_string($_GET['format']);
		}
	}
	//apply_filters( 'mc_filter_calendar_name',$all_styles,$styles );
	return my_calendar($name,$format,$category,$showkey,$shownav,$showjump,$toggle,$time, $ltype, $lvalue, $id, $template,$content,$author,$host );
}

function my_calendar_insert_upcoming($atts) {
	extract(shortcode_atts(array(
				'before' => 'default',
				'after' => 'default',
				'type' => 'default',
				'category' => 'default',
				'template' => 'default',
				'fallback' => '',
				'order' => 'asc',
				'skip' => '0',
				'show_today' => 'yes',
				'author' => 'default',
				'host' => 'default'
			), $atts));
	return my_calendar_upcoming_events($before, $after, $type, $category, $template, $fallback, $order, $skip, $show_today, $author );
}

function my_calendar_insert_today($atts) {
	extract(shortcode_atts(array(
				'category' => 'default',
				'author' => 'default',
				'host' => 'default',
				'template' => 'default',
				'fallback' => ''
			), $atts));
	return my_calendar_todays_events($category, $template, $fallback, $author);
}

function my_calendar_locations($atts) {
	extract(shortcode_atts(array(
				'show' => 'list',
				'type' => 'saved',
				'datatype' => 'name'
			), $atts));
	return my_calendar_locations_list($show,$type,$datatype);
}

function my_calendar_show_locations_list($atts) {
	extract(shortcode_atts(array(
				'show' => 'list',
				'datatype' => 'name',
				'template' => ''
			), $atts));
	return my_calendar_show_locations($show,$datatype,$template);
}

function my_calendar_categories($atts) {
	extract(shortcode_atts(array(
				'show' => 'list'
			), $atts));
	return my_calendar_categories_list( $show );
}

function my_calendar_show_event($atts) {
	extract(shortcode_atts(array(
				'event' => '',
				'template' => '<h3>{title}</h3>{description}',
				'list' => '<li>{date}, {time}</li>',
				'before' => '<ul>',
				'after' => '</ul>'
			), $atts));
	return mc_instance_list( $event, false, $template, $list, $before, $after );
}