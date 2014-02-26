<?php
class my_calendar_today_widget extends WP_Widget {

function my_calendar_today_widget() {
	parent::WP_Widget( false,$name=__('My Calendar: Today\'s Events','my-calendar') );
}

function widget($args, $instance) {
	extract($args);
	$the_title = apply_filters('widget_title',$instance['my_calendar_today_title']);
	$the_template = $instance['my_calendar_today_template'];
	$the_substitute = $instance['my_calendar_no_events_text'];
	$the_category = ($instance['my_calendar_today_category']=='')?'default':esc_attr($instance['my_calendar_today_category']);
	$author = ( !isset($instance['my_calendar_today_author']) || $instance['my_calendar_today_author']=='')?'all':esc_attr($instance['my_calendar_today_author']);
	$host = ( !isset($instance['mc_host']) || $instance['mc_host']=='')?'all':esc_attr($instance['mc_host']);
	$widget_link = (!empty($instance['my_calendar_today_linked']) && $instance['my_calendar_today_linked']=='yes')?get_option('mc_uri'):'';
	$widget_link = ( !empty($instance['mc_link']) )?esc_url($instance['mc_link']):$widget_link;
	$widget_title = empty($the_title) ? '' : $the_title;
	$offset = (60*60*get_option('gmt_offset'));
	if ( strpos($widget_title,'{date}') !== false ) { $widget_title = str_replace( '{date}',date_i18n(get_option('mc_date_format'),time()+$offset),$widget_title ); }	
	$widget_title = ($widget_link=='') ? $widget_title : "<a href='$widget_link'>$widget_title</a>";	
	$widget_title = ($widget_title!='') ? $before_title . $widget_title . $after_title : '';
	$the_events = my_calendar_todays_events($the_category,$the_template,$the_substitute,$author, $host);
		if ($the_events != '') {
		  echo $before_widget;
		  echo $widget_title;
		  echo $the_events;
		  echo $after_widget;
		}
}

function form($instance) {
	global $default_template;
	$widget_title = (isset($instance['my_calendar_today_title']))?esc_attr($instance['my_calendar_today_title']):'';
	$widget_template = (isset($instance['my_calendar_today_template']))?esc_attr($instance['my_calendar_today_template']):'';
	if (!$widget_template) { $widget_template = $default_template; }
	$widget_text = (isset($instance['my_calendar_no_events_text']))?esc_attr($instance['my_calendar_no_events_text']):'';
	$widget_category = (isset($instance['my_calendar_today_category']))?esc_attr($instance['my_calendar_today_category']):'';
	$widget_linked = (isset($instance['my_calendar_today_linked']))?esc_attr($instance['my_calendar_today_linked']):'';
	if ( $widget_linked == 'yes' ) { $default_link = get_option('mc_uri'); } else { $default_link = ''; }
	$widget_link = (!empty($instance['mc_link']))?esc_url($instance['mc_link']):$default_link;
	$widget_author = (isset($instance['my_calendar_today_author']))?esc_attr($instance['my_calendar_today_author']):'';
	$widget_host = (isset($instance['mc_host']))?esc_attr($instance['mc_host']):'';
	
?>
	<p>
	<label for="<?php echo $this->get_field_id('my_calendar_today_title'); ?>"><?php _e('Title','my-calendar'); ?>:</label><br />
	<input class="widefat" type="text" id="<?php echo $this->get_field_id('my_calendar_today_title'); ?>" name="<?php echo $this->get_field_name('my_calendar_today_title'); ?>" value="<?php echo $widget_title; ?>"/>
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('my_calendar_today_template'); ?>"><?php _e('Template','my-calendar'); ?></label><br />
	<textarea class="widefat" rows="8" cols="20" id="<?php echo $this->get_field_id('my_calendar_today_template'); ?>" name="<?php echo $this->get_field_name('my_calendar_today_template'); ?>"><?php echo $widget_template; ?></textarea>
	</p>
	<?php if ( get_option('mc_uri') == '' ) { $disabled = " disabled='disabled'"; $warning = _e('Add calendar URL to use this option.','my-calendar');  } else { $disabled = $warning = ""; } ?>
	<?php /* <p>
	<label for="<?php echo $this->get_field_id('my_calendar_today_linked'); ?>"><?php _e('Link widget title to calendar:','my-calendar'); ?></label> <select<?php echo $disabled; ?> id="<?php echo $this->get_field_id('my_calendar_today_linked'); ?>" name="<?php echo $this->get_field_name('my_calendar_today_linked'); ?>">
	<option value="no" <?php echo ($widget_linked == 'no')?'selected="selected"':''; ?>><?php _e('Not Linked','my-calendar') ?></option>
	<option value="yes" <?php echo ($widget_linked == 'yes')?'selected="selected"':''; ?>><?php _e('Linked','my-calendar') ?></option>
	</select>
	</p> */ ?>
	<p>
	<label for="<?php echo $this->get_field_id('mc_link'); ?>"><?php _e('Widget title links to:','my-calendar'); ?></label><br />
	<input class="widefat" type="text" id="<?php echo $this->get_field_id('mc_link'); ?>" name="<?php echo $this->get_field_name('mc_link'); ?>" value="<?php echo $widget_link; ?>" /></textarea>
	</p>	
	<p>
	<label for="<?php echo $this->get_field_id('my_calendar_no_events_text'); ?>"><?php _e('Show this text if there are no events today:','my-calendar'); ?></label><br />
	<input class="widefat" type="text" id="<?php echo $this->get_field_id('my_calendar_no_events_text'); ?>" name="<?php echo $this->get_field_name('my_calendar_no_events_text'); ?>" value="<?php echo $widget_text; ?>" /></textarea>
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('my_calendar_today_category'); ?>"><?php _e('Category or categories to display:','my-calendar'); ?></label><br />
	<input class="widefat" type="text" id="<?php echo $this->get_field_id('my_calendar_today_category'); ?>" name="<?php echo $this->get_field_name('my_calendar_today_category'); ?>" value="<?php echo $widget_category; ?>" /></textarea>
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('my_calendar_today_author'); ?>"><?php _e('Author or authors to show:','my-calendar'); ?></label><br />
	<input class="widefat" type="text" id="<?php echo $this->get_field_id('my_calendar_today_author'); ?>" name="<?php echo $this->get_field_name('my_calendar_today_author'); ?>" value="<?php echo $widget_author; ?>" /></textarea>
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('mc_host'); ?>"><?php _e('Host or hosts to show:','my-calendar'); ?></label><br />
	<input class="widefat" type="text" id="<?php echo $this->get_field_id('mc_host'); ?>" name="<?php echo $this->get_field_name('mc_host'); ?>" value="<?php echo $widget_host; ?>" /></textarea>
	</p>	
	<?php
}  

	function update($new_instance,$old_instance) {
		$instance = $old_instance;
		$instance['my_calendar_today_title'] = strip_tags($new_instance['my_calendar_today_title']);
		$instance['my_calendar_today_template'] = $new_instance['my_calendar_today_template'];
		$instance['my_calendar_no_events_text'] = strip_tags($new_instance['my_calendar_no_events_text']);
		$instance['my_calendar_today_category'] = strip_tags($new_instance['my_calendar_today_category']);
		$instance['my_calendar_today_linked'] = strip_tags($new_instance['my_calendar_today_linked']);
		$instance['mc_link'] = esc_url($new_instance['mc_link']);
		$instance['my_calendar_today_author'] = strip_tags($new_instance['my_calendar_today_author']);
		$instance['mc_host'] = strip_tags($new_instance['mc_host']);
		return $instance;
	}

}

class my_calendar_upcoming_widget extends WP_Widget {

function my_calendar_upcoming_widget() {
	parent::WP_Widget( false,$name=__('My Calendar: Upcoming Events','my-calendar') );
}

function widget($args, $instance) {
	extract($args);
	$the_title = apply_filters('widget_title',$instance['my_calendar_upcoming_title']);
	$the_template = $instance['my_calendar_upcoming_template'];
	$the_substitute = $instance['my_calendar_no_events_text'];
	$before = ($instance['my_calendar_upcoming_before']!='')?esc_attr($instance['my_calendar_upcoming_before']):3;
	$after = ($instance['my_calendar_upcoming_after']!='')?esc_attr($instance['my_calendar_upcoming_after']):3;
	$skip = ($instance['my_calendar_upcoming_skip']!='')?esc_attr($instance['my_calendar_upcoming_skip']):0;
	$show_today = ($instance['my_calendar_upcoming_show_today']=='no')?'no':'yes';
	$type = esc_attr($instance['my_calendar_upcoming_type']);
	$order = esc_attr($instance['my_calendar_upcoming_order']);
	$the_category = ($instance['my_calendar_upcoming_category']=='')?'default':esc_attr($instance['my_calendar_upcoming_category']);
	$author = ( !isset($instance['my_calendar_upcoming_author']) || $instance['my_calendar_upcoming_author']=='')?'default':esc_attr($instance['my_calendar_upcoming_author']);
	$host = ( !isset($instance['mc_host']) || $instance['mc_host']=='')?'default':esc_attr($instance['mc_host']);
	$widget_link = ($instance['my_calendar_upcoming_linked']=='yes')?get_option('mc_uri'):'';
	$widget_link = ( !empty($instance['mc_link']) )?esc_url($instance['mc_link']):$widget_link;
	$widget_title = empty($the_title) ? '' : $the_title;
	$widget_title = ($widget_link=='') ? $widget_title : "<a href='$widget_link'>$widget_title</a>";
	$widget_title = ($widget_title!='') ? $before_title . $widget_title . $after_title : '';
	$the_events = my_calendar_upcoming_events($before,$after,$type,$the_category,$the_template,$the_substitute, $order,$skip, $show_today,$author, $host);
		if ($the_events != '') {
			echo $before_widget;
			echo $widget_title;
			echo $the_events;
			echo $after_widget;
		}
}


function form($instance) {
	global $default_template;
	
	$widget_title = (isset($instance['my_calendar_upcoming_title']) )?esc_attr($instance['my_calendar_upcoming_title']):'';
	$widget_template = (isset($instance['my_calendar_upcoming_template']) )?esc_attr($instance['my_calendar_upcoming_template']):'';
	if (!$widget_template) { $widget_template = $default_template; }
	$widget_text = (isset($instance['my_calendar_no_events_text']) )?esc_attr($instance['my_calendar_no_events_text']):'';
	$widget_category = (isset($instance['my_calendar_upcoming_category']) )?esc_attr($instance['my_calendar_upcoming_category']):'';
	$widget_author = (isset($instance['my_calendar_upcoming_author']) )?esc_attr($instance['my_calendar_upcoming_author']):'';
	$widget_host = (isset($instance['mc_host']) )?esc_attr($instance['mc_host']):'';
	$widget_before = (isset($instance['my_calendar_upcoming_before']) )?esc_attr($instance['my_calendar_upcoming_before']):'';
	$widget_after = (isset($instance['my_calendar_upcoming_after']) )?esc_attr($instance['my_calendar_upcoming_after']):'';
	$widget_show_today = (isset($instance['my_calendar_upcoming_show_today']) )?esc_attr($instance['my_calendar_upcoming_show_today']):'';
	$widget_type = (isset($instance['my_calendar_upcoming_type']) )?esc_attr($instance['my_calendar_upcoming_type']):'';
	$widget_order = (isset($instance['my_calendar_upcoming_order']) )?esc_attr($instance['my_calendar_upcoming_order']):'';
	$widget_linked = (isset($instance['my_calendar_upcoming_linked']) )?esc_attr($instance['my_calendar_upcoming_linked']):'';
	if ( $widget_linked == 'yes' ) { $default_link = get_option('mc_uri'); } else { $default_link = ''; }
	$widget_link = (!empty($instance['mc_link']))?esc_url($instance['mc_link']):$default_link;
	$widget_skip = (isset($instance['my_calendar_upcoming_skip']) )?esc_attr($instance['my_calendar_upcoming_skip']):'';
	
?>
	<p>
	<label for="<?php echo $this->get_field_id('my_calendar_upcoming_title'); ?>"><?php _e('Title','my-calendar'); ?>:</label><br />
	<input class="widefat" type="text" id="<?php echo $this->get_field_id('my_calendar_upcoming_title'); ?>" name="<?php echo $this->get_field_name('my_calendar_upcoming_title'); ?>" value="<?php echo $widget_title; ?>"/>
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('my_calendar_upcoming_template'); ?>"><?php _e('Template','my-calendar'); ?></label><br />
	<textarea class="widefat" rows="6" cols="20" id="<?php echo $this->get_field_id('my_calendar_upcoming_template'); ?>" name="<?php echo $this->get_field_name('my_calendar_upcoming_template'); ?>"><?php echo $widget_template; ?></textarea>
	</p>
	<fieldset>
	<legend><?php _e('Widget Options','my-calendar'); ?></legend>
	<?php $config_url = admin_url("admin.php?page=my-calendar-config"); ?>
	<?php if ( get_option('mc_uri') == '' ) { $disabled = " disabled='disabled'";  _e('Add <a href="'.$config_url.'#mc_uri" target="_blank" title="Opens in new window">calendar URL in settings</a> to use this option.','my-calendar');  } else { $disabled=""; } ?>
	<?php /* <p>
	<label for="<?php echo $this->get_field_id('my_calendar_upcoming_linked'); ?>"><?php _e('Link widget title to calendar:','my-calendar'); ?></label> <select<?php echo $disabled; ?> id="<?php echo $this->get_field_id('my_calendar_upcoming_linked'); ?>" name="<?php echo $this->get_field_name('my_calendar_upcoming_linked'); ?>">
	<option value="no" <?php echo ($widget_linked == 'no')?'selected="selected"':''; ?>><?php _e('Not Linked','my-calendar') ?></option>
	<option value="yes" <?php echo ($widget_linked == 'yes')?'selected="selected"':''; ?>><?php _e('Linked','my-calendar') ?></option>
	</select>
	</p> */ ?>
	<p>
	<label for="<?php echo $this->get_field_id('mc_link'); ?>"><?php _e('Widget title links to:','my-calendar'); ?></label><br />
	<input class="widefat" type="text" id="<?php echo $this->get_field_id('mc_link'); ?>" name="<?php echo $this->get_field_name('mc_link'); ?>" value="<?php echo $widget_link; ?>" /></textarea>
	</p>		
	<p>
	<label for="<?php echo $this->get_field_id('my_calendar_upcoming_type'); ?>"><?php _e('Display upcoming events by:','my-calendar'); ?></label> <select id="<?php echo $this->get_field_id('my_calendar_upcoming_type'); ?>" name="<?php echo $this->get_field_name('my_calendar_upcoming_type'); ?>">
	<option value="events" <?php echo ($widget_type == 'events')?'selected="selected"':''; ?>><?php _e('Events (e.g. 2 past, 3 future)','my-calendar') ?></option>
	<option value="days" <?php echo ($widget_type == 'days')?'selected="selected"':''; ?>><?php _e('Dates (e.g. 4 days past, 5 forward)','my-calendar') ?></option>
	</select>
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('my_calendar_upcoming_skip'); ?>"><?php _e('Skip the first <em>n</em> events','my-calendar'); ?></label> <input type="text" id="<?php echo $this->get_field_id('my_calendar_upcoming_skip'); ?>" name="<?php echo $this->get_field_name('my_calendar_upcoming_skip'); ?>" value="<?php echo $widget_skip; ?>" />
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('my_calendar_upcoming_order'); ?>"><?php _e('Events sort order:','my-calendar'); ?></label> <select id="<?php echo $this->get_field_id('my_calendar_upcoming_order'); ?>" name="<?php echo $this->get_field_name('my_calendar_upcoming_order'); ?>">
	<option value="asc" <?php echo ($widget_order == 'asc')?'selected="selected"':''; ?>><?php _e('Ascending (near to far)','my-calendar') ?></option>
	<option value="desc" <?php echo ($widget_order == 'desc')?'selected="selected"':''; ?>><?php _e('Descending (far to near)','my-calendar') ?></option>
	</select>
	</p>	
	<p>
	<input type="text" id="<?php echo $this->get_field_id('my_calendar_upcoming_after'); ?>" name="<?php echo $this->get_field_name('my_calendar_upcoming_after'); ?>" value="<?php echo $widget_after; ?>" size="1" maxlength="3" /> <label for="<?php echo $this->get_field_id('my_calendar_upcoming_after'); ?>"><?php _e("$widget_type into the future;",'my-calendar'); ?></label><br />
	<input type="text" id="<?php echo $this->get_field_id('my_calendar_upcoming_before'); ?>" name="<?php echo $this->get_field_name('my_calendar_upcoming_before'); ?>" value="<?php echo $widget_before; ?>" size="1" maxlength="3" /> <label for="<?php echo $this->get_field_id('my_calendar_upcoming_after'); ?>"><?php _e("$widget_type from the past",'my-calendar'); ?></label>
	</p>
	<p>
	<input type="checkbox" id="<?php echo $this->get_field_id('my_calendar_upcoming_show_today'); ?>" name="<?php echo $this->get_field_name('my_calendar_upcoming_show_today'); ?>" value="yes"<?php echo ($widget_show_today =='yes' || $widget_show_today == '' )?' checked="checked"':''; ?> /> <label for="<?php echo $this->get_field_id('my_calendar_upcoming_show_today'); ?>"><?php _e("Include today's events",'my-calendar'); ?></label>
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('my_calendar_no_events_text'); ?>"><?php _e('Show this text if there are no events meeting your criteria:','my-calendar'); ?></label><br />
	<input class="widefat" type="text" id="<?php echo $this->get_field_id('my_calendar_no_events_text'); ?>" name="<?php echo $this->get_field_name('my_calendar_no_events_text'); ?>" value="<?php echo $widget_text; ?>" /></textarea>
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('my_calendar_upcoming_category'); ?>"><?php _e('Category or categories to display:','my-calendar'); ?></label><br />
	<input class="widefat" type="text" id="<?php echo $this->get_field_id('my_calendar_upcoming_category'); ?>" name="<?php echo $this->get_field_name('my_calendar_upcoming_category'); ?>" value="<?php echo $widget_category; ?>" /></textarea>
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('my_calendar_upcoming_author'); ?>"><?php _e('Author or authors to show:','my-calendar'); ?></label><br />
	<input class="widefat" type="text" id="<?php echo $this->get_field_id('my_calendar_upcoming_author'); ?>" name="<?php echo $this->get_field_name('my_calendar_upcoming_author'); ?>" value="<?php echo $widget_author; ?>" /></textarea>
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('mc_host'); ?>"><?php _e('Host or hosts to show:','my-calendar'); ?></label><br />
	<input class="widefat" type="text" id="<?php echo $this->get_field_id('mc_host'); ?>" name="<?php echo $this->get_field_name('mc_host'); ?>" value="<?php echo $widget_host; ?>" /></textarea>
	</p>	
	<?php
}  

	function update($new_instance,$old_instance) {
		$instance = $old_instance;
		$instance['my_calendar_upcoming_title'] = strip_tags($new_instance['my_calendar_upcoming_title']);
		$instance['my_calendar_upcoming_template'] = $new_instance['my_calendar_upcoming_template'];
		$instance['my_calendar_no_events_text'] = strip_tags($new_instance['my_calendar_no_events_text']);
		$instance['my_calendar_upcoming_category'] = strip_tags($new_instance['my_calendar_upcoming_category']);		
		$instance['my_calendar_upcoming_author'] = strip_tags($new_instance['my_calendar_upcoming_author']);		
		$instance['mc_host'] = strip_tags($new_instance['mc_host']);		
		$instance['my_calendar_upcoming_before'] = strip_tags($new_instance['my_calendar_upcoming_before']);
		$instance['my_calendar_upcoming_after'] = strip_tags($new_instance['my_calendar_upcoming_after']);
		$instance['my_calendar_upcoming_show_today'] = ($new_instance['my_calendar_upcoming_show_today']=='yes')?'yes':'no';		
		$instance['my_calendar_upcoming_type'] = strip_tags($new_instance['my_calendar_upcoming_type']);
		$instance['my_calendar_upcoming_order'] = strip_tags($new_instance['my_calendar_upcoming_order']);
		$instance['my_calendar_upcoming_linked'] = strip_tags($new_instance['my_calendar_upcoming_linked']);
		$instance['my_calendar_upcoming_skip'] = strip_tags($new_instance['my_calendar_upcoming_skip']);
		$instance['mc_link'] = esc_url($new_instance['mc_link']);
		return $instance;
	}
}

// Widget upcoming events
function my_calendar_upcoming_events($before='default',$after='default',$type='default',$category='default',$template='default',$substitute='',$order='asc',$skip=0, $show_today='yes',$author='default',$host='default' ) {
  global $wpdb,$default_template,$defaults;
  $mcdb = $wpdb;
  if ( get_option( 'mc_remote' ) == 'true' && function_exists('mc_remote_db') ) { $mcdb = mc_remote_db(); }
  $output = '';
  $date_format = ( get_option('mc_date_format') != '' )?get_option('mc_date_format'):get_option('date_format');
  // This function cannot be called unless calendar is up to date
	check_my_calendar();
	$offset = (60*60*get_option('gmt_offset'));	
    $widget_defaults = get_option('mc_widget_defaults');
	if ( !is_array($widget_defaults) ) { $widget_defaults = array(); } // get globals; check on mc_widget_des
	$display_upcoming_type = ($type == 'default')?$widget_defaults['upcoming']['type']:$type;
	if ($display_upcoming_type == '') { $display_upcoming_type = 'event'; }
    // Get number of units we should go into the future
	$after = ($after == 'default')?$widget_defaults['upcoming']['after']:$after;
	if ($after == '') { $after = 10; }
	// Get number of units we should go into the past
	$before = ($before == 'default')?$widget_defaults['upcoming']['before']:$before;
	if ($before == '') { $before = 0; }
	$category = ($category == 'default')?'':$category;
	$template = ($template == 'default')?$widget_defaults['upcoming']['template']:$template;
	if ($template == '' ) { $template = "$default_template"; };
	$no_event_text = ($substitute == '')?$widget_defaults['upcoming']['text']:$substitute;
    $day_count = -($before);
	$header = "<ul id='upcoming-events'>";
	$footer = "</ul>";
	$output ='';
	if ($display_upcoming_type == "days") {
		$temp_array = array();
		$event_array = array();
			$from = date('Y-m-d',strtotime("-$before days") );
			$to = date('Y-m-d',strtotime("+$after days") );
			$events = my_calendar_grab_events( $from, $to, $category,'','','upcoming',$author, $host );			
			if ( !get_option('mc_skip_holidays_category') || get_option('mc_skip_holidays_category') == '' ) { 
				$holidays = array();
			} else {
				$holidays = my_calendar_grab_events( $from, $to, get_option('mc_skip_holidays_category'),'','', 'upcoming', $author, $host );
				$holiday_array = mc_set_date_array( $holidays );
			}
			// get events into an easily parseable set, keyed by date.
			if ( is_array( $events ) && !empty($events) ) {
				$no_events = false;
				$event_array = mc_set_date_array( $events );
				if ( is_array( $holidays ) && count($holidays) > 0 ) {
					$event_array = mc_holiday_limit( $event_array, $holiday_array ); // if there are holidays, rejigger.
				}			
			}			
			if (count($event_array) != 0) {
				foreach( $event_array as $key=>$value) {
					if ( is_array($value) ) {
						foreach ( $value as $k => $v ) {
							$event = event_as_array( $v );
							$temp_array[] = $event;
						}
					}
				}
			}
			$i = 0;
			foreach ( reverse_array($temp_array, true, $order) as $details ) {
				if ( $i < $skip && $skip != 0 ) {
					$i++;
				} else {
					$output .= "<li>".jd_draw_template($details,$template)."</li>";		  
				}
			}		
	} else {
		$caching = ( get_option('mc_caching_enabled') == 'true' )?true:false;
		if ( $caching ) { 
			$cache = get_transient( 'mc_cache_upcoming' ); 
			$output .= "<!-- Cached -->";
			if ( $cache ) {
				if (isset($cache[$category]) ) {
					$events = $cache[$category];
					$cache = false; // take cache out of memory
				} else {
					$events = mc_get_all_events($category, $before, $after, $show_today, $author, $host);
					$cache[$category] = $events;
					set_transient( 'mc_cache_upcoming', $cache, 60*30 );
				}
			} else {
				$events = mc_get_all_events($category, $before, $after, $show_today, $author, $host);
				$cache[$category] = $events;
				set_transient( 'mc_cache_upcoming', $cache, 60*30 );			
			}
		} else {
			$events = mc_get_all_events($category, $before, $after, $show_today, $author, $host);	 // grab all events within reasonable proximity
		}
		if ( !get_option('mc_skip_holidays_category') || get_option('mc_skip_holidays_category') == '' ) { 
			$holidays = array();
		} else {
			$holidays = mc_get_all_holidays( $before, $after, $show_today );
			$holiday_array = mc_set_date_array( $holidays );
		}
			if ( is_array( $events ) && !empty($events) ) {
				$no_events = false;
				$event_array = mc_set_date_array( $events );
				if ( is_array( $holidays ) && count($holidays) > 0 ) {
					$event_array = mc_holiday_limit( $event_array, $holiday_array ); // if there are holidays, rejigger.
				}
			}
		$output .= mc_produce_upcoming_events( $event_array,$template,'list',$order,$skip,$before, $after );
	}
	if ($output != '') {
		$output = $header.$output.$footer;
		return $output;
	} else {
		return stripcslashes( $no_event_text );
	}	
}
function mc_span_time( $group_id ) {
global $wpdb;
  $mcdb = $wpdb;
  if ( get_option( 'mc_remote' ) == 'true' && function_exists('mc_remote_db') ) { $mcdb = mc_remote_db(); }

$group_id = (int) $group_id;
	$sql = "SELECT event_begin, event_time, event_end, event_endtime FROM ".my_calendar_table()." WHERE event_group_id = $group_id ORDER BY event_begin ASC";
	$dates = $mcdb->get_results( $sql );
	$count = count($dates);
	$last = $count - 1;
	$begin = $dates[0]->event_begin . ' ' . $dates[0]->event_time;
	$end = $dates[$last]->event_end . ' ' . $dates[$last]->event_endtime;
	return array( $begin, $end );
}
// make this function time-sensitive, not date-sensitive.
function mc_produce_upcoming_events($events,$template,$type='list',$order='asc',$skip=0,$before, $after, $hash=false) {
	// $events has +5 before and +5 after if those values are non-zero.
	// $events equals array of events based on before/after queries. Nothing has been skipped, order is not set, holidays are removed.
		$output = '';$near_events = $temp_array = array();$past = $future = 1;
		$offset = (60*60*get_option('gmt_offset'));
		$today = date('Y',time()+($offset)).'-'.date('m',time()+($offset)).'-'.date('d',time()+($offset));		
         @usort( $e, "my_calendar_timediff_cmp" );// sort all events by proximity to current date
	     $count = count($events);
		 $skip = false;
		 $group = array();
		 $spans = array();
			$i = 0; 
			// create near_events array
			if ( is_array( $events ) ) {
			foreach ( $events as $k=>$event ) {
				if ( $i < $count ) {
					if ( is_array( $event ) ) {
						foreach ( $event as $e ) {
							if ( $e->category_private == 1 && !is_user_logged_in() ) {
							} else {
							// if the beginning of an event is after the current time, it is in the future
								$beginning = $e->occur_begin;
								$date = date('Y-m-d', strtotime($beginning));
							// if the end of an event is before the current time, it is in the past.
							if ( date('H:i:s',strtotime($e->occur_end) ) == '00:00:00' ) { $endtime = date('H:i:s',strtotime($e->occur_begin) ); } else { $endtime = date('H:i:s',strtotime($e->occur_end) ); }
								$end = $e->occur_end;
								// store span time in an array to avoid repeating database query
								if ( $e->event_span == 1 && ( !isset($spans[ $e->occur_group_id ]) ) ) {
									// this is a multi-day event: treat each event as if it spanned the entire range of the group.
									$span_time = mc_span_time($e->occur_group_id);
									$beginning = $span_time[0];
									$end = $span_time[1];
									$spans[ $e->occur_group_id ] = $span_time;
								} else if  ( $e->event_span == 1 && ( isset($spans[ $e->occur_group_id ]) ) ) {
									$span_time = $spans[ $e->occur_group_id ];
									$beginning = $span_time[0];
									$end = $span_time[1];
								}
								$current = date('Y-m-d H:i',time()+$offset);
								if ( $e ) { 
									if ( $e->occur_group_id != 0 && $e->event_span == 1 && in_array( $e->occur_group_id, $group ) ) { 
										$skip = true; 
									} else { 
										$group[] = $e->occur_group_id; $skip=false; 
									}
									if ( !$skip ) {
										if ( ( $past<=$before && $future<=$after ) ) {
											$near_events[] = $e; // if neither limit is reached, split off freely
											$i++;
										} else if ( $past <= $before && ( my_calendar_date_comp( $beginning,$current ) ) ) {
											$near_events[] = $e; // split off another past event
											$i++;
										} else if ( $future <= $after && ( !my_calendar_date_comp( $end,$current ) ) ) {
											$near_events[] = $e; // split off another future event
											$i++;
										} 
										if ( my_calendar_date_comp( $beginning,$current ) ) { 			$past++;
										} else if ( my_calendar_date_equal( $beginning,$current ) ) {  $present = 1;
										} else { $future++; }
									}
									if ($past > $before && $future > $after) {
										break;
									}
								}
							}
						}
					}
				}
			}
			$e = false;
			}
		$events = $near_events;
		@usort( $events, "my_calendar_datetime_cmp" ); // sort split events by date
		// If more items in the list than there should be (possible, due to handling of current-day's events), pop off.
		$intended = $before + $after;
		$actual = count($events);
		if ( $actual > $intended ) {
			for ( $i=0;$i<($actual-$intended);$i++ ) {
				array_pop($events);
			}
		}
		if ( is_array( $events ) ) {
			foreach( array_keys($events) as $key ) {
				$event =& $events[$key];
				//echo $event->event_title . " " . $event->event_group_id."<br />";
				$event_details = event_as_array( $event );
					if ( get_option( 'mc_event_approve' ) == 'true' ) {
						if ( $event->event_approved != 0 ) { $temp_array[] = $event_details; }
					} else {
						$temp_array[] = $event_details;
					}
			}
			$i = 0;
			$groups = array();
			$skips = array();
			
			foreach( reverse_array($temp_array, true, $order) as $details ) {
				if ( !in_array( $details['group'], $groups ) ) {
					$date = date('Y-m-d',strtotime($details['dtstart']));
					$class = (my_calendar_date_comp( $date,$today )===true)?"past-event":"future-event";
					if ( my_calendar_date_equal( $date,$today ) ) {
						$class = "today";
					}
					if ( $details['event_span'] == 1 ) {
						$class = "multiday";
					}
					if ($type == 'list') {
						$prepend = "\n<li class=\"$class\">";
						$append = "</li>\n";
					} else {
						$prepend = $append = '';
					}
					if ( $i < $skip && $skip != 0 ) {
						$i++;
					} else {
						if ( !in_array( $details['dateid'], $skips ) ) {
							$output .= apply_filters('mc_event_upcoming',"$prepend".jd_draw_template($details,$template,$type)."$append",$event); 	  
							$skips[] = $details['dateid'];
						}
					}
					if ( $details['event_span'] == 1 ) {
						$groups[] = $details['group'];
					}
				}
			}
		} else {
			$output .= '';
		}
	return $output;
}

// Widget todays events
function my_calendar_todays_events($category='default',$template='default',$substitute='',$author='all', $host='all') {
	$caching = ( get_option('mc_caching_enabled') == 'true' )?true:false;
	$todays_cache = ($caching)? get_transient('mc_todays_cache') :'';

if ( $caching && is_array($todays_cache) && @$todays_cache[$category] ) { return @$todays_cache[$category]; }
	global $wpdb, $default_template;
	$mcdb = $wpdb;
	if ( get_option( 'mc_remote' ) == 'true' && function_exists('mc_remote_db') ) { $mcdb = mc_remote_db(); }
	$output = '';
	$offset = (60*60*get_option('gmt_offset'));  
	// This function cannot be called unless calendar is up to date
	check_my_calendar();
    $defaults = get_option('mc_widget_defaults');
	$template = ($template == 'default')?$defaults['today']['template']:$template;
	if ($template == '' ) { $template = "$default_template"; };	

	$category = ($category == 'default')?$defaults['today']['category']:$category;
	$no_event_text = ($substitute == '')?$defaults['today']['text']:$substitute;

	$from = $to = date( 'Y-m-d',time()+$offset );
    $events = my_calendar_grab_events($from, $to,$category,'','','upcoming',$author, $host);
	$header = "<ul id='todays-events'>";
	$footer = "</ul>";		
	$holiday_exists = false;
    @usort($events, "my_calendar_time_cmp");
	$groups = array();
	// quick loop through all events today to check for holidays
	if (is_array($events) ) {
		foreach( array_keys($events) as $key ) {
			$event =& $events[$key];
			if ( $event->event_category == get_option('mc_skip_holidays_category') ) {	$holiday_exists = true;	}
		}
        foreach( array_keys($events) as $key ) {
			$event =& $events[$key];
			if ( $event->category_private == 1 && !is_user_logged_in() ) {
			} else {
			if ( !in_array( $event->event_group_id, $groups ) )	{	
				$event_details = event_as_array($event);
				$date = date_i18n(get_option('mc_date_format'),time()+$offset);

				$this_event = '';
				if ( $event->event_holiday == 0 ) {
					if ( get_option( 'mc_event_approve' ) == 'true' ) {
						if ( $event->event_approved != 0 ) {$this_event = "<li>".jd_draw_template($event_details,$template)."</li>";}
					} else {
						$this_event = "<li>".jd_draw_template($event_details,$template)."</li>";
					}
				} else {
					// if we found a holiday earlier, then we know there is one today.
					if ( !$holiday_exists || ( $holiday_exists && $event->event_category == get_option('mc_skip_holidays_category') ) ) {
						if ( get_option( 'mc_event_approve' ) == 'true' ) {
							if ( $event->event_approved != 0 ) {$this_event = "<li>".jd_draw_template($event_details,$template)."</li>";}
						} else {
							$this_event = "<li>".jd_draw_template($event_details,$template)."</li>";
						}
					}
				}
				$output .= apply_filters( 'mc_event_today',$this_event,$event );
			}
			}
        }

		if (count($events) != 0) {
			$return = $header.$output.$footer;
		} else {
			$return = stripcslashes( $no_event_text );
		}
		$time =  strtotime( date( 'Y-m-d H:m:s',time()+$offset ) ) - strtotime( date( 'Y-m-d',time()+$offset ) );
		$time_remaining = 24*60*60 - $time;
		$todays_cache[$category] = ($caching)?$return:'';
		if ( $caching ) set_transient( 'mc_todays_cache', $todays_cache, $time_remaining );
	} else {
		$return = stripcslashes( $no_event_text );
	}
	return $return;
}

class my_calendar_mini_widget extends WP_Widget {

function my_calendar_mini_widget() {
	parent::WP_Widget( false,$name=__('My Calendar: Mini Calendar','my-calendar') );
}

function widget($args, $instance) {
	extract($args);
	if ( !empty($instance) ) {
		$the_title = apply_filters('widget_title',$instance['my_calendar_mini_title']);
		$name = $format = 'mini';
		$category = ($instance['my_calendar_mini_category']=='')?'all':esc_attr($instance['my_calendar_mini_category']);
		$showkey = ($instance['my_calendar_mini_showkey']=='')?'no':esc_attr($instance['my_calendar_mini_showkey']);
		$showjump = ($instance['my_calendar_mini_showjump']=='')?'no':esc_attr($instance['my_calendar_mini_showjump']);		
		$shownav = ($instance['my_calendar_mini_shownav']=='')?'no':esc_attr($instance['my_calendar_mini_shownav']);
		$time = ($instance['my_calendar_mini_time']=='')?'month':esc_attr($instance['my_calendar_mini_time']);
		$widget_link = ( !isset($instance['mc_link']) || $instance['mc_link']=='')?'':esc_url($instance['mc_link']);
	} else {
		$the_title = '';
		$name = 'mini';
		$category = '';
		$showkey = '';
		$shownav = '';
		$time = '';
		$widget_link = '';
	}

	$widget_title = empty($the_title) ? __('Calendar','my-calendar') : $the_title;
	$widget_title = ($widget_link!='') ? "<a href='$widget_link'>$widget_title</a>" : $widget_title;
	$widget_title = ($widget_title!='') ? $before_title . $widget_title . $after_title : '';
	$the_events = my_calendar( $name,$format,$category,$showkey,$shownav,$showjump,'no',$time );
		if ($the_events != '') {
		  echo $before_widget;
		  echo $widget_title;
		  echo $the_events;
		  echo $after_widget;
		}
}

function form($instance) {
	$widget_title = esc_attr($instance['my_calendar_mini_title']);
	$widget_key = esc_attr($instance['my_calendar_mini_showkey']);
	$widget_jump = esc_attr($instance['my_calendar_mini_showjump']);	
	$widget_nav = esc_attr($instance['my_calendar_mini_shownav']);
	$widget_time = esc_attr($instance['my_calendar_mini_time']);
	$widget_category = esc_attr($instance['my_calendar_mini_category']);
	$widget_link = ( isset($instance['mc_link']) )?esc_url($instance['mc_link']):'';
?>
	<p>
	<label for="<?php echo $this->get_field_id('my_calendar_mini_title'); ?>"><?php _e('Title','my-calendar'); ?>:</label><br />
	<input class="widefat" type="text" id="<?php echo $this->get_field_id('my_calendar_mini_title'); ?>" name="<?php echo $this->get_field_name('my_calendar_mini_title'); ?>" value="<?php echo $widget_title; ?>"/>
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('mc_link'); ?>"><?php _e('Widget Title Link','my-calendar'); ?>:</label><br />
	<input class="widefat" type="text" id="<?php echo $this->get_field_id('mc_link'); ?>" name="<?php echo $this->get_field_name('mc_link'); ?>" value="<?php echo $widget_link; ?>"/>
	</p>	
	<p>
	<label for="<?php echo $this->get_field_id('my_calendar_mini_category'); ?>"><?php _e('Category or categories to display:','my-calendar'); ?></label><br />
	<input class="widefat" type="text" id="<?php echo $this->get_field_id('my_calendar_mini_category'); ?>" name="<?php echo $this->get_field_name('my_calendar_mini_category'); ?>" value="<?php echo $widget_category; ?>" /></textarea>
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('my_calendar_mini_shownav'); ?>"><?php _e('Show Next/Previous Navigation:','my-calendar'); ?></label> <select id="<?php echo $this->get_field_id('my_calendar_mini_shownav'); ?>" name="<?php echo $this->get_field_name('my_calendar_mini_shownav'); ?>">
	<option value="yes" <?php echo ($widget_nav == 'yes')?'selected="selected"':''; ?>><?php _e('Yes','my-calendar') ?></option>
	<option value="no" <?php echo ($widget_nav == 'no')?'selected="selected"':''; ?>><?php _e('No','my-calendar') ?></option>
	</select>
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('my_calendar_mini_showjump'); ?>"><?php _e('Show Jumpbox','my-calendar'); ?></label> <select id="<?php echo $this->get_field_id('my_calendar_mini_showjump'); ?>" name="<?php echo $this->get_field_name('my_calendar_mini_showjump'); ?>">
	<option value="yes" <?php echo ($widget_jump == 'yes')?'selected="selected"':''; ?>><?php _e('Yes','my-calendar') ?></option>
	<option value="no" <?php echo ($widget_jump == 'no')?'selected="selected"':''; ?>><?php _e('No','my-calendar') ?></option>
	</select>
	</p>	
	<p>
	<label for="<?php echo $this->get_field_id('my_calendar_mini_showkey'); ?>"><?php _e('Show Category Key:','my-calendar'); ?></label> <select id="<?php echo $this->get_field_id('my_calendar_mini_showkey'); ?>" name="<?php echo $this->get_field_name('my_calendar_mini_showkey'); ?>">
	<option value="yes" <?php echo ($widget_key == 'yes')?'selected="selected"':''; ?>><?php _e('Yes','my-calendar') ?></option>
	<option value="no" <?php echo ($widget_key == 'no')?'selected="selected"':''; ?>><?php _e('No','my-calendar') ?></option>
	</select>
	</p>	
	<p>
	<label for="<?php echo $this->get_field_id('my_calendar_mini_time'); ?>"><?php _e('Mini-Calendar Timespan:','my-calendar'); ?></label> <select id="<?php echo $this->get_field_id('my_calendar_mini_time'); ?>" name="<?php echo $this->get_field_name('my_calendar_mini_time'); ?>">
	<option value="month" <?php echo ($widget_time == 'month')?'selected="selected"':''; ?>><?php _e('Month','my-calendar') ?></option>
	<option value="week" <?php echo ($widget_time == 'week')?'selected="selected"':''; ?>><?php _e('Week','my-calendar') ?></option>
	</select>
	</p>	
	<?php
}  

	function update($new_instance,$old_instance) {
		$instance = $old_instance;
		$instance['my_calendar_mini_title'] = strip_tags($new_instance['my_calendar_mini_title']);
		$instance['my_calendar_mini_showkey'] = $new_instance['my_calendar_mini_showkey'];
		$instance['my_calendar_mini_shownav'] = strip_tags($new_instance['my_calendar_mini_shownav']);
		$instance['my_calendar_mini_showjump'] = strip_tags($new_instance['my_calendar_mini_showjump']);		
		$instance['my_calendar_mini_time'] = strip_tags($new_instance['my_calendar_mini_time']);		
		$instance['my_calendar_mini_category'] = strip_tags($new_instance['my_calendar_mini_category']);		
		return $instance;		
	}

}

?>