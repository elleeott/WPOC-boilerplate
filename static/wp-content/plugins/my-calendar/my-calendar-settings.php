<?php
// Display the admin configuration page
function my_calendar_import() {
	if ( get_option('ko_calendar_imported') != 'true' ) {
	global $wpdb;
	$mcdb = $wpdb;
		define('KO_CALENDAR_TABLE', $mcdb->prefix . 'calendar');
		define('KO_CALENDAR_CATS', $mcdb->prefix . 'calendar_categories');
		$events = $mcdb->get_results("SELECT * FROM " . KO_CALENDAR_TABLE, 'ARRAY_A');
		$event_ids = array();
		foreach ($events as $key) {
			if ( $key['event_time'] == '00:00:00' ) {
				$endtime = '00:00:00';
			} else {
				$endtime = date('H:i:s',strtotime( "$key[event_time] +1 hour" ) );
			}
			$data = array(
				'event_title'=>$key['event_title'],
				'event_desc'=>$key['event_desc'], 
				'event_begin'=>$key['event_begin'], 
				'event_end'=>$key['event_end'], 
				'event_time'=>$key['event_time'], 
				'event_endtime'=>$endtime,
				'event_recur'=>$key['event_recur'], 
				'event_repeats'=>$key['event_repeats'], 
				'event_author'=>$key['event_author'], 
				'event_category'=>$key['event_category'],
				'event_hide_end'=>1,
				'event_link'=>( isset($key['event_link']) )?$key['event_link']:'' );
			$format = array( '%s','%s','%s','%s','%s','%s','%s','%d','%d','%d','%d','%s' );
			$update = $mcdb->insert( my_calendar_table(), $data, $format );
			$event_ids[] = $mcdb->insert_id;
		}

		foreach ( $event_ids as $value ) { // propagate event instances.
				$sql = "SELECT event_begin, event_time, event_end, event_endtime FROM ".my_calendar_table()." WHERE event_id = $value";
				$event = $wpdb->get_results($sql);
				$dates = array( 'event_begin'=>$event->event_begin,'event_end'=>$event->event_end,'event_time'=>$event->event_time,'event_endtime'=>$event->event_endtime );
				$event = mc_increment_event( $value, $dates );				
		}
		$cats = $mcdb->get_results("SELECT * FROM " . KO_CALENDAR_CATS, 'ARRAY_A');	
		$catsql = "";
		foreach ($cats as $key) {
			$name = mysql_real_escape_string($key['category_name']);
			$color = mysql_real_escape_string($key['category_colour']);
			$id = (int) $key['category_id'];
			$catsql = "INSERT INTO " . my_calendar_categories_table() . " SET 
				category_id='".$id."',
				category_name='".$name."', 
				category_color='".$color."' 
				ON DUPLICATE KEY UPDATE 
				category_name='".$name."', 
				category_color='".$color."';
				";	
			$cats_results = $mcdb->query($catsql);
			//$mcdb->print_error(); 			
		}			
		$message = ( $cats_results !== false )?__('Categories imported successfully.','my-calendar'):__('Categories not imported.','my-calendar');
		$e_message = ( $events_results !== false )?__('Events imported successfully.','my-calendar'):__('Events not imported.','my-calendar');
		$return = "<div id='message' class='updated fade'><ul><li>$message</li><li>$e_message</li></ul></div>";
		echo $return;
		if ( $cats_results !== false && $events_results !== false ) {
			update_option( 'ko_calendar_imported','true' );
		}
	} 
}

function mc_drop_table( $table ) {
	global $wpdb;
	$sql = "DROP TABLE ".$table();
	$wpdb->query($sql);
}

function edit_my_calendar_config() {
	global $wpdb,$default_user_settings;
	$mcdb = $wpdb;
	// We can't use this page unless My Calendar is installed/upgraded
	check_my_calendar();
	if (!empty($_POST)) {
		$nonce=$_REQUEST['_wpnonce'];
		if (! wp_verify_nonce($nonce,'my-calendar-nonce') ) die("Security check failed"); 
		if ( isset($_POST['remigrate']) ) { 
			echo "<div class='updated fade'><ol>";
			echo "<li>".__('Dropping occurrences database table','my-calendar')."</li>";
			mc_drop_table( 'my_calendar_event_table' );
			sleep(1);
			echo "<li>".__('Reinstalling occurrences database table.','my-calendar')."</li>";
			mc_upgrade_db(); 
			sleep(1);
			echo "<li>".__('Generating event occurrences.','my-calendar')."</li>";
			mc_migrate_db();
			echo "<li>".__('Event generation completed.','my-calendar')."</li>";
			echo "</ol></div>";
		}
	}
   if (isset($_POST['mc_manage'])) {
		// management
		$clear = '';
		$mc_event_approve = ( !empty($_POST['mc_event_approve']) && $_POST['mc_event_approve']=='on')?'true':'false';
		$mc_remote = ( !empty($_POST['mc_remote']) && $_POST['mc_remote']=='on')?'true':'false';
		$mc_caching_enabled = ( !empty($_POST['mc_caching_enabled']) && $_POST['mc_caching_enabled']=='on')?'true':'false';
		if ( $mc_remote == 'true' ) { $mc_caching_enabled = 'false'; }
		if ( isset($_POST['mc_clear_cache']) && $_POST['mc_clear_cache'] == 'clear' ) { mc_delete_cache(); $clear = __('My Calendar Cache cleared','my-calendar'); }
		update_option('mc_event_approve',$mc_event_approve);
		update_option('mc_remote',$mc_remote);
		update_option('mc_caching_enabled',$mc_caching_enabled);
		update_option('mc_default_sort',$_POST['mc_default_sort']);
		update_option('mc_num_per_page',(int) $_POST['mc_num_per_page']);
		
		if ( get_site_option('mc_multisite') == 2 ) {
			$mc_current_table = (int) $_POST['mc_current_table'];
			update_option('mc_current_table',$mc_current_table);
		}
		echo "<div class='updated'><p><strong>".__('My Calendar Management Settings saved','my-calendar').". $clear</strong></p></div>";
	}
	if ( isset($_POST['mc_permissions'] ) ) {
		$perms = $_POST['mc_caps'];
		$caps = array( 
					'mc_add_events'=>__('Add Events','my-calendar'),
					'mc_approve_events'=>__('Approve Events','my-calendar'),
					'mc_manage_events'=>__('Manage Events','my-calendar'),
					'mc_edit_cats'=>__('Edit Categories','my-calendar'),
					'mc_edit_locations'=>__('Edit Locations','my-calendar'),
					'mc_edit_styles'=>__('Edit Styles','my-calendar'),
					'mc_edit_behaviors'=>__('Edit Behaviors','my-calendar'),
					'mc_edit_templates'=>__('Edit Templates','my-calendar'),
					'mc_edit_settings'=>__('Edit Settings','my-calendar'),
					'mc_view_help'=>__('View Help','my-calendar')
					);
		foreach ( $perms as $key => $value ) {
			$role = get_role( $key );
			if ( is_object( $role ) ) {
				foreach( $caps as $k=>$v ) {
					if ( isset($value[$k]) ) {
						$role->add_cap( $k );
					} else {
						$role->remove_cap( $k );
					}
				}
			}
		}
		echo "<div class='updated'><p><strong>".__('My Calendar Permissions Updated','my-calendar')."</strong></p></div>";
	}
	// output
	if (isset($_POST['mc_show_months']) ) {

		$mc_open_day_uri = ( !empty($_POST['mc_open_day_uri']) )?$_POST['mc_open_day_uri']:'';
		update_option('mc_uri',$_POST['mc_uri'] );
		update_option('mc_open_uri',( !empty($_POST['mc_open_uri']) && $_POST['mc_open_uri']=='on' && get_option('mc_uri') != '')?'true':'false');
		update_option('mc_day_uri',$_POST['mc_day_uri'] );
		update_option('mc_mini_uri',$_POST['mc_mini_uri'] );
		update_option('mc_open_day_uri', $mc_open_day_uri );
		update_option('mc_skip_holidays_category',(int) $_POST['mc_skip_holidays_category']);
		update_option('mc_skip_holidays',( !empty($_POST['mc_skip_holidays']) && $_POST['mc_skip_holidays']=='on')?'true':'false');
		update_option('mc_display_author',( !empty($_POST['mc_display_author']) && $_POST['mc_display_author']=='on')?'true':'false');
		update_option('mc_show_event_vcal',( !empty($_POST['mc_show_event_vcal']) && $_POST['mc_show_event_vcal']=='on')?'true':'false');		
		update_option('mc_display_jump',( !empty($_POST['mc_display_jump']) && $_POST['mc_display_jump']=='on')?'true':'false');
		update_option('mc_show_list_info',( !empty($_POST['mc_show_list_info']) && $_POST['mc_show_list_info']=='on')?'true':'false');		
		update_option('mc_show_months',(int) $_POST['mc_show_months']);
		update_option('mc_show_map',( !empty($_POST['mc_show_map']) && $_POST['mc_show_map']=='on')?'true':'false');
		update_option('mc_show_address',( !empty($_POST['mc_show_address']) && $_POST['mc_show_address']=='on')?'true':'false'); 
		update_option('mc_hide_icons',( !empty($_POST['mc_hide_icons']) && $_POST['mc_hide_icons']=='on')?'false':'true');
		update_option('mc_event_link_expires',( !empty($_POST['mc_event_link_expires']) && $_POST['mc_event_link_expires']=='on')?'true':'false');
		update_option('mc_apply_color',$_POST['mc_apply_color']);
		update_option('mc_event_registration',( !empty($_POST['mc_event_registration']) && $_POST['mc_event_registration']=='on')?'true':'false');
		update_option('mc_short',( !empty($_POST['mc_short']) && $_POST['mc_short']=='on')?'true':'false');
		update_option('mc_desc',( !empty($_POST['mc_desc']) && $_POST['mc_desc']=='on')?'true':'false');
		update_option('mc_process_shortcodes',( !empty($_POST['mc_process_shortcodes']) && $_POST['mc_process_shortcodes']=='on')?'true':'false');
		update_option('mc_details',( !empty($_POST['mc_details']) && $_POST['mc_details']=='on')?'true':'false');
		update_option('mc_event_link',( !empty($_POST['mc_event_link']) && $_POST['mc_event_link']=='on')?'true':'false');		
		update_option('mc_show_weekends',( !empty($_POST['mc_show_weekends']) && $_POST['mc_show_weekends']=='on')?'true':'false');
		update_option('mc_convert',( !empty($_POST['mc_convert']) && $_POST['mc_convert']=='on')?'true':'false');
		update_option('mc_no_fifth_week',( !empty($_POST['mc_no_fifth_week']) && $_POST['mc_no_fifth_week']=='on')?'true':'false');
			$mc_show_rss = ( !empty($_POST['mc_show_rss']) && $_POST['mc_show_rss']=='on')?'true':'false';
			$mc_show_ical = ( !empty($_POST['mc_show_ical']) && $_POST['mc_show_ical']=='on')?'true':'false';
			$mc_ical_utc = ( !empty($_POST['mc_ical_utc']) && $_POST['mc_ical_utc']=='on')?'true':'false';
			$mc_show_print = ( !empty($_POST['mc_show_print']) && $_POST['mc_show_print']=='on')?'true':'false';
			// just paste 'em together as a string. All that matters is whether any of them have changed.
			$prev_show = get_option('mc_show_rss').'-'.get_option('mc_show_ical').'-'.get_option('mc_show_print');
			$curr_show = "$mc_show_rss-$mc_show_ical-$mc_show_print";
		update_option('mc_show_rss',$mc_show_rss);
		update_option('mc_show_ical',$mc_show_ical);
		update_option('mc_ical_utc',$mc_ical_utc);
		update_option('mc_show_print',$mc_show_print);
		if ( $prev_show != $curr_show ) { $update_text = " ".sprintf(__('Visit your <a href="%s">permalinks settings</a> and re-save them.','my-calendar'),admin_url('options-permalink.php')); } else { $update_text = ''; } 
		echo "<div class=\"updated\"><p><strong>".__('Output Settings saved','my-calendar').".$update_text</strong></p></div>";
	}
	// input
	if ( isset($_POST['mc_dates']) ) {
		update_option('mc_date_format',stripslashes($_POST['mc_date_format']));
		update_option('mc_week_format',stripslashes($_POST['my_calendar_week_format']));
		update_option('mc_time_format',stripslashes($_POST['mc_time_format']));
		update_option('mc_month_format',stripslashes($_POST['mc_month_format']));
		echo "<div class=\"updated\"><p><strong>".__('Date/Time Format Settings saved','my-calendar')."</strong></p></div>";		
	}
	if (isset($_POST['mc_input'])) {
		$mc_input_options_administrators = ( !empty($_POST['mc_input_options_administrators']) && $_POST['mc_input_options_administrators']=='on')?'true':'false'; 
		$mc_input_options = array(
			'event_short'=>( !empty($_POST['mci_event_short']) && $_POST['mci_event_short'])?'on':'',
			'event_desc'=>( !empty($_POST['mci_event_desc']) && $_POST['mci_event_desc'])?'on':'',
			'event_category'=>( !empty($_POST['mci_event_category']) && $_POST['mci_event_category'])?'on':'',
			'event_image'=>( !empty($_POST['mci_event_image']) && $_POST['mci_event_image'])?'on':'',
			'event_link'=>( !empty($_POST['mci_event_link']) && $_POST['mci_event_link'])?'on':'',
			'event_recurs'=>( !empty($_POST['mci_event_recurs']) && $_POST['mci_event_recurs'])?'on':'',
			'event_open'=>( !empty($_POST['mci_event_open']) && $_POST['mci_event_open'])?'on':'',
			'event_location'=>( !empty($_POST['mci_event_location']) && $_POST['mci_event_location'])?'on':'',
			'event_location_dropdown'=>( !empty($_POST['mci_event_location_dropdown']) && $_POST['mci_event_location_dropdown'])?'on':'',
			'event_use_editor'=>( !empty($_POST['mci_event_use_editor']) && $_POST['mci_event_use_editor'])?'on':''
			);
		update_option('mc_input_options',$mc_input_options);
		update_option('mc_input_options_administrators',$mc_input_options_administrators);	
		echo "<div class=\"updated\"><p><strong>".__('Input Settings saved','my-calendar').".</strong></p></div>";
	}
	if ( current_user_can('manage_network') ) {
		if ( isset($_POST['mc_network']) ) {
			$mc_multisite = (int) $_POST['mc_multisite'];
			update_site_option('mc_multisite',$mc_multisite );
			$mc_multisite_show = (int) $_POST['mc_multisite_show'];
			update_site_option('mc_multisite_show',$mc_multisite_show );			
			echo "<div class=\"updated\"><p><strong>".__('Multisite settings saved','my-calendar').".</strong></p></div>";
		}
	}
	// custom text
	if (isset( $_POST['mc_previous_events'] ) ) {
		$mc_title_template = $_POST['mc_title_template'];
		$mc_details_label = $_POST['mc_details_label'];
		$mc_link_label = $_POST['mc_link_label'];
		$mc_notime_text = $_POST['mc_notime_text'];
		$mc_previous_events = $_POST['mc_previous_events'];
		$mc_next_events = $_POST['mc_next_events'];
		$mc_event_open = $_POST['mc_event_open'];
		$mc_event_closed = $_POST['mc_event_closed'];
		$mc_week_caption = $_POST['mc_week_caption'];
		$my_calendar_caption = $_POST['my_calendar_caption'];
		$templates = get_option('mc_templates');
		$templates['title'] = $mc_title_template;
		$templates['label'] = $mc_details_label;
		$templates['link'] = $mc_link_label;	
		update_option('mc_templates',$templates);

		update_option('mc_notime_text',$mc_notime_text);
		update_option('mc_week_caption',$mc_week_caption);
		update_option('mc_next_events',$mc_next_events);
		update_option('mc_previous_events',$mc_previous_events);	
		update_option('mc_caption',$my_calendar_caption);
		update_option('mc_event_open',$mc_event_open);
		update_option('mc_event_closed',$mc_event_closed);
		echo "<div class=\"updated\"><p><strong>".__('Custom text settings saved','my-calendar').".</strong></p></div>";	 
	}
	// Mail function by Roland
	if (isset($_POST['mc_email']) ) {
		$mc_event_mail = ( !empty($_POST['mc_event_mail']) && $_POST['mc_event_mail']=='on')?'true':'false';
		$mc_event_mail_to = $_POST['mc_event_mail_to'];
		$mc_event_mail_subject = $_POST['mc_event_mail_subject'];
		$mc_event_mail_message = $_POST['mc_event_mail_message'];
		update_option('mc_event_mail_to',$mc_event_mail_to);
		update_option('mc_event_mail_subject',$mc_event_mail_subject);
		update_option('mc_event_mail_message',$mc_event_mail_message);
		update_option('mc_event_mail',$mc_event_mail);
		echo "<div class=\"updated\"><p><strong>".__('Email notice settings saved','my-calendar').".</strong></p></div>";
	}
	// Custom User Settings
	if (isset($_POST['mc_user'])) {
		$mc_user_settings_enabled = ( !empty($_POST['mc_user_settings_enabled']) && $_POST['mc_user_settings_enabled']=='on')?'true':'false';
		$mc_location_type = $_POST['mc_location_type'];
		$mc_user_settings = $_POST['mc_user_settings'];
		$mc_user_settings['my_calendar_tz_default']['values'] = csv_to_array($mc_user_settings['my_calendar_tz_default']['values']);
		$mc_user_settings['my_calendar_location_default']['values'] = csv_to_array($mc_user_settings['my_calendar_location_default']['values']);
		$mc_location_control = ( isset( $_POST['mc_location_control'] ) && $_POST['mc_location_control'] == 'on' )?'on':'';
		update_option( 'mc_location_control',$mc_location_control );
		update_option( 'mc_location_type',$mc_location_type );
		update_option( 'mc_user_settings_enabled',$mc_user_settings_enabled );
		update_option( 'mc_user_settings',$mc_user_settings );  
		echo "<div class=\"updated\"><p><strong>".__('User custom settings saved','my-calendar').".</strong></p></div>";
	}
	
	apply_filters('mc_save_settings','', $_POST );
	
	// Pull known values out of the options table
	$allowed_group = get_option('mc_can_manage_events');
	$mc_show_months = get_option('mc_show_months');
	$mc_show_map = get_option('mc_show_map');
	$mc_show_address = get_option('mc_show_address');
	$disp_author = get_option('mc_display_author');
	$mc_event_link_expires = get_option('mc_event_link_expires');
	$mc_event_mail = get_option('mc_event_mail');
	$mc_event_mail_to = get_option('mc_event_mail_to');
	$mc_event_mail_subject = get_option('mc_event_mail_subject');
	$mc_event_mail_message = get_option('mc_event_mail_message');
	$mc_event_approve = get_option('mc_event_approve');
	$mc_event_approve_perms = get_option('mc_event_approve_perms');
	$disp_jump = get_option('mc_display_jump');
	$mc_no_fifth_week = get_option('mc_no_fifth_week');
	$templates = get_option('mc_templates');
	$mc_title_template = $templates['title'];
	$mc_details_label = $templates['label'];
	$mc_link_label = $templates['link'];
	$mc_uri = get_option('mc_uri');
	$mc_day_uri = get_option('mc_day_uri');
	$mc_mini_uri = get_option('mc_mini_uri');
	$mc_num_per_page = ( get_option('mc_num_per_page') == '' )?50:get_option('mc_num_per_page');
?> 

<div class="wrap jd-my-calendar" id="mc_settings">
<?php my_calendar_check_db();?>
    <div id="icon-options-general" class="icon32"><br /></div>
	<h2><?php _e('My Calendar Options','my-calendar'); ?></h2>
<div class="postbox-container" style="width: 70%">
<div class="metabox-holder">
  <?php
update_option( 'ko_calendar_imported','false' );
if (isset($_POST['import']) && $_POST['import'] == 'true') {
	$nonce=$_REQUEST['_wpnonce'];
    if (! wp_verify_nonce($nonce,'my-calendar-nonce') ) die("Security check failed");
	my_calendar_import();
}
if ( get_option( 'ko_calendar_imported' ) != 'true' ) {
  	if (function_exists('check_calendar')) {
?>
	<div class='import upgrade-db'>
	<p>
	<?php _e('My Calendar has identified that you have the Calendar plugin by Kieran O\'Shea installed. You can import those events and categories into the My Calendar database. Would you like to import these events?','my-calendar'); ?>
	</p>
		<form method="post" action="<?php echo admin_url("admin.php?page=my-calendar-config"); ?>">
		<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" /></div>		
		<div>
		<input type="hidden" name="import" value="true" />
		<input type="submit" value="<?php _e('Import from Calendar','my-calendar'); ?>" name="import-calendar" class="button-primary" />
		</div>
		</form>
	</div>
<?php
	}
}
?>

<div class="ui-sortable meta-box-sortables">   
<div class="postbox">
	<h3><?php _e('My Calendar Settings','my-calendar'); ?></h3>
	<div class="inside">
	<ul class="mc-settings">
		<li><a href="#my-calendar-manage"><?php _e('Management','my-calendar'); ?></a></li>
		<li><a href="#my-calendar-text"><?php _e('Customizable Text','my-calendar'); ?></a></li>
		<li><a href="#my-calendar-output"><?php _e('Output','my-calendar'); ?></a></li>
		<li><a href="#my-calendar-time"><?php _e('Date/Time','my-calendar'); ?></a></li>
		<li><a href="#my-calendar-input"><?php _e('Input','my-calendar'); ?></a></li>
		<?php if ( current_user_can('manage_network') ) { ?>
		<li><a href="#my-calendar-multisite"><?php _e('Multi-site','my-calendar'); ?></a></li>		
		<?php } ?>
		<li><a href="#my-calendar-permissions"><?php _e('Permissions','my-calendar'); ?></a></li>
		<li><a href="#my-calendar-email"><?php _e('Email Notifications','my-calendar'); ?></a></li>
		<li><a href="#my-calendar-user"><?php _e('Individual Users','my-calendar'); ?></a></li>
	</ul>
	</div>
</div>
</div>

<div class="ui-sortable meta-box-sortables">   
<div class="postbox" id="my-calendar-manage">
	<h3><?php _e('Calendar Management Settings','my-calendar'); ?></h3>
	<div class="inside">
	<?php if ( current_user_can('administrator') ) { ?>
    <form method="post" action="<?php echo admin_url("admin.php?page=my-calendar-config"); ?>">
	<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" /></div> 	
	<fieldset>
    <legend><?php _e('Calendar Options: Management','my-calendar'); ?></legend>
    <ul>
	<li><input type="checkbox" id="mc_remote" name="mc_remote" <?php mc_is_checked('mc_remote','true'); ?> /> <label for="mc_remote"><?php _e('Get data (events, categories and locations) from a remote database.','my-calendar'); ?></label></li>
	<?php if ( get_option('mc_remote') == 'true' ) { ?>
	<li><?php _e('Add this code to your theme\'s <code>functions.php</code> file:','my-calendar'); ?>
<pre>function mc_remote_db() {
	$mcdb = new wpdb('DB_NAME','DB_PASSWORD','DB_USER','DB_ADDRESS');
	return $mcdb;
}</pre>
		<?php _e('You will need to allow remote connections from this site to the site hosting your My Calendar events. Replace the above placeholders with the host-site information. The two sites must have the same WP table prefix. While this option is enabled, you may not enter or edit events through this installation.','my-calendar'); ?>
	</li>	
	<?php } ?>
	<li><input type="checkbox" id="mc_event_approve" name="mc_event_approve" <?php mc_is_checked('mc_event_approve','true'); ?> /> <label for="mc_event_approve"><?php _e('Enable approval options.','my-calendar'); ?></label>	</li>
	<li><input type="checkbox" id="mc_caching_enabled" name="mc_caching_enabled"<?php echo ( get_option('mc_remote') == 'true' )?" disabled='disabled'":''; ?> <?php mc_is_checked('mc_caching_enabled','true'); ?> /> <label for="mc_caching_enabled"><?php _e('Enable caching.','my-calendar'); ?></label><?php echo ( get_option('mc_remote') == 'true' )?__('<em>Cannot use caching while accessing a remote database.</em>','my-calendar'):''; ?>	</li>
	<?php if ( get_option('mc_caching_enabled') == 'true' ) { ?>
	<li><input type="checkbox" id="mc_clear_cache" name="mc_clear_cache" value="clear" /> <label for="mc_clear_cache"><?php _e('Clear current cache. (Necessary if you edit shortcodes to change displayed categories, for example.)','my-calendar'); ?></label>
	</li>	
	<?php } ?>
	<li>	
	<label for="mc_default_sort"><?php _e('Default Sort order for Admin Events List','my-calendar'); ?></label>
	<select id="mc_default_sort" name="mc_default_sort">
		<option value='1' <?php mc_is_selected( 'mc_default_sort','1'); ?>><?php _e('Event ID','my-calendar'); ?></option>
		<option value='2' <?php mc_is_selected( 'mc_default_sort','2'); ?>><?php _e('Title','my-calendar'); ?></option>
		<option value='3' <?php mc_is_selected( 'mc_default_sort','3'); ?>><?php _e('Description','my-calendar'); ?></option>
		<option value='4' <?php mc_is_selected( 'mc_default_sort','4'); ?>><?php _e('Start Date','my-calendar'); ?></option>
		<option value='5' <?php mc_is_selected( 'mc_default_sort','5'); ?>><?php _e('Author','my-calendar'); ?></option>
		<option value='6' <?php mc_is_selected( 'mc_default_sort','6'); ?>><?php _e('Category','my-calendar'); ?></option>
		<option value='7' <?php mc_is_selected( 'mc_default_sort','7'); ?>><?php _e('Location Name','my-calendar'); ?></option>
	</select>	
	</li>
	<li>
	<label for='mc_num_per_page'><?php _e('Number of events per page in admin events list','my-calendar'); ?></label> <input type='text' name='mc_num_per_page' id='mc_num_per_page' value='<?php echo $mc_num_per_page; ?>' />
	</li>
		<?php if ( get_site_option('mc_multisite') == 2 && MY_CALENDAR_TABLE != MY_CALENDAR_GLOBAL_TABLE ) { ?>
	<li>
	<input type="radio" name="mc_current_table" id="mc0" value="0"<?php echo jd_option_selected(get_option('mc_current_table'),0); ?> /> <label for="mc0"><?php _e('Currently editing my local calendar','my-calendar'); ?></label>
	</li>
	<li>
	<input type="radio" name="mc_current_table" id="mc1" value="1"<?php echo jd_option_selected(get_option('mc_current_table'),1); ?> /> <label for="mc1"><?php _e('Currently editing the network calendar','my-calendar'); ?></label>
	</li>
	<?php } else { ?>
		<?php if ( get_option('mc_remote') != 'true' ) { ?>
	<li><?php _e('You are currently working in the primary site for this network; your local calendar is also the global table.','my-calendar'); ?></li>
		<?php } ?>
	<?php } ?>
	<li><input type="checkbox" id="remigrate" name="remigrate" value="migrate" /> <label for="remigrate"><?php _e('Re-generate event occurrences table.','my-calendar'); ?></label>
	</li>
	</ul>
	</fieldset>
		<p>
		<input type="submit" name="mc_manage" class="button-primary" value="<?php _e('Save Management Settings','my-calendar'); ?>" />
		</p>
	</form>
	<?php } else { ?>
		<?php _e('My Calendar management settings are only available to administrators.','my-calendar'); ?>
	<?php } ?>
	</div>
</div>
</div>

<div class="ui-sortable meta-box-sortables">   
<div class="postbox" id="my-calendar-text">
	<h3><?php _e('Calendar Text Settings','my-calendar'); ?></h3>
	<div class="inside">
	    <form method="post" action="<?php echo admin_url("admin.php?page=my-calendar-config"); ?>">
	<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" /></div>		
<fieldset>
	<legend><?php _e('Calendar Options: Customizable Text Fields','my-calendar'); ?></legend>
	<ul>
	<li>
	<label for="mc_notime_text"><?php _e('Label for events without a set time','my-calendar'); ?></label> <input type="text" id="mc_notime_text" name="mc_notime_text" value="<?php if ( get_option('mc_notime_text') == "") { _e('N/A','my-calendar'); } else { echo esc_attr( stripslashes( get_option('mc_notime_text') ) ); } ?>" />
	</li>
	<li>
	<label for="mc_previous_events"><?php _e('Previous events link','my-calendar'); ?></label> <input type="text" id="mc_previous_events" name="mc_previous_events" value="<?php if ( get_option('mc_previous_events') == "") { _e('Previous Events','my-calendar'); } else { echo esc_attr( stripslashes( get_option('mc_previous_events') ) ); } ?>" /> <?php _e('Use <code>{date}</code> to display the appropriate date in navigation.','my-calendar'); ?>
	</li>
	<li>
	<label for="mc_next_events"><?php _e('Next events link','my-calendar'); ?></label> <input type="text" id="mc_next_events" name="mc_next_events" value="<?php if ( get_option('mc_next_events') == "") { _e('Next Events','my-calendar'); } else { echo esc_attr(  stripslashes( get_option('mc_next_events') ) ); } ?>" /> <?php _e('Use <code>{date}</code> to display the appropriate date in navigation.','my-calendar'); ?>
	</li>
	<li>
	<label for="mc_event_open"><?php _e('If events are open','my-calendar'); ?></label> <input type="text" id="mc_event_open" name="mc_event_open" value="<?php if ( get_option('mc_event_open') == "") { _e('Registration is open','my-calendar'); } else { echo esc_attr( stripslashes( get_option('mc_event_open') ) ); } ?>" />
	</li>
	<li>
	<label for="mc_event_closed"><?php _e('If events are closed','my-calendar'); ?></label> <input type="text" id="mc_event_closed" name="mc_event_closed" value="<?php if ( get_option('mc_event_closed') == "") { _e('Registration is closed','my-calendar'); } else { echo esc_attr( stripslashes( get_option('mc_event_closed') ) ); } ?>" />
	</li>	
	<li>
	<label for="mc_week_caption"><?php _e('Week view caption:','my-calendar'); ?></label> <input type="text" id="mc_week_caption" name="mc_week_caption" value="<?php echo esc_attr( stripslashes( get_option('mc_week_caption') ) ); ?>" />
	</li>
	<li>
	<label for="my_calendar_caption"><?php _e('Extended caption:','my-calendar'); ?></label> <input type="text" id="my_calendar_caption" name="my_calendar_caption" value="<?php echo esc_attr( stripslashes( get_option('mc_caption') ) ); ?>" /><br /><small><?php _e('The calendar caption shows month and year in list and grid formats. This text is displayed after the month/year.','my-calendar'); ?></small>
	</li>
	<li>
	<label for="mc_title_template"><?php _e('Event title template','my-calendar'); ?></label> 
	<input type="text" name="mc_title_template" id="mc_title_template" size="30" value="<?php echo stripslashes(esc_attr($mc_title_template)); ?>" /> <small><a href="<?php echo admin_url("admin.php?page=my-calendar-help#templates"); ?>"><?php _e("Templating Help",'my-calendar'); ?></a> <?php _e('All template tags are available.','my-calendar'); ?></small>
	</li>
	<li>
	<label for="mc_details_label"><?php _e('Event details link text','my-calendar'); ?></label>
	<input type="text" name="mc_details_label" id="mc_details_label" size="30" value="<?php echo stripslashes(esc_attr($mc_details_label)); ?>" />
	<br /><small><?php _e('Available tags: <code>{title}</code>, <code>{location}</code>, <code>{color}</code>, <code>{icon}</code>, <code>{date}</code>, <code>{time}</code>.','my-calendar'); ?></small>
	</li>
	<li>
	<label for="mc_link_label"><?php _e('Event URL link text','my-calendar'); ?></label>
	<input type="text" name="mc_link_label" id="mc_link_label" size="30" value="<?php echo stripslashes(esc_attr($mc_link_label)); ?>" />
	<small><a href="<?php echo admin_url("admin.php?page=my-calendar-help#templates"); ?>"><?php _e("Templating Help",'my-calendar'); ?></a> <?php _e('All template tags are available.','my-calendar'); ?></small>
	</li>	</ul>
	</fieldset>	
		<p>
		<input type="submit" name="save" class="button-primary" value="<?php _e('Save Custom Text Settings','my-calendar'); ?>" />
	</p>
	</form>
</div>
</div>
</div>

<div class="ui-sortable meta-box-sortables">   
<div class="postbox" id="my-calendar-output">
	<h3><?php _e('Calendar Output Settings','my-calendar'); ?></h3>
	<div class="inside">
 <form method="post" action="<?php echo admin_url("admin.php?page=my-calendar-config"); ?>">
	<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" /></div>
		<p><input type="submit" name="save" class="button-primary" value="<?php _e('Save Output Settings','my-calendar'); ?>" /></p>
	<fieldset>
	<legend><?php _e('Calendar Options: Customize the Output of your Calendar','my-calendar'); ?></legend>
	<fieldset>
	<legend><?php _e('Calendar Link Targets','my-calendar'); ?></legend>
	<ul>
	<li>
	<label for="mc_uri"><?php _e('Target <abbr title="Uniform resource locator">URL</abbr> for event details links:','my-calendar'); ?></label> 
	<input type="text" name="mc_uri" id="mc_uri" size="60" value="<?php echo esc_url($mc_uri); ?>" /><br /><small><?php _e('Can be any Page or Post which includes the <code>[my_calendar]</code> shortcode.','my-calendar'); ?> <?php mc_guess_calendar(); ?></small>
	</li>
	<li>
	<label for="mc_day_uri"><?php _e('Target <abbr title="Uniform resource locator">URL</abbr> for single day\'s timeline links.','my-calendar'); ?></label> 
	<input type="text" name="mc_day_uri" id="mc_day_uri" size="60" value="<?php echo esc_url($mc_day_uri); ?>" /><br /><small><?php _e('Can be any Page or Post with the <code>[my_calendar time="day"]</code> shortcode.','my-calendar'); ?></small>
	</li>
		<li>
	<label for="mc_mini_uri"><?php _e('Target <abbr title="Uniform resource locator">URL</abbr> for mini calendar in-page anchors:','my-calendar'); ?></label> 
	<input type="text" name="mc_mini_uri" id="mc_mini_uri" size="60" value="<?php echo esc_url($mc_mini_uri); ?>" /><br /><small><?php _e('Can be any Page or Post with the <code>[my_calendar]</code> shortcode using format selected below','my-calendar'); ?></small>
	</li>
	<li><strong><?php _e('Modify date and event link behaviors:','my-calendar'); ?></strong></li>
	<li>
	<input type="checkbox" id="mc_open_uri" name="mc_open_uri"<?php if ( $mc_uri == '' ) { echo ' disabled="disabled"'; } ?> <?php mc_is_checked('mc_open_uri','true'); ?> /> <label for="mc_open_uri"><?php _e('Open calendar links to event details URL','my-calendar'); ?></label> <small><?php _e('Replaces pop-up in grid view.','my-calendar'); ?></small>
	</li>
	<li>
	<label for="mc_open_day_uri"><?php _e('Mini calendar widget date links to:','my-calendar'); ?></label> <select id="mc_open_day_uri" name="mc_open_day_uri"<?php if ( !$mc_day_uri && !$mc_mini_uri ) { echo ' disabled="disabled"'; } ?>>
	<option value='false'<?php echo jd_option_selected(get_option('mc_open_day_uri'),'false','option'); ?>><?php _e('jQuery pop-up view','my-calendar'); ?></option>	
	<option value='true'<?php echo jd_option_selected(get_option('mc_open_day_uri'),'true','option'); ?>><?php _e('daily view page (above)','my-calendar'); ?></option>
	<option value='listanchor'<?php echo jd_option_selected(get_option('mc_open_day_uri'),'listanchor','option'); ?>><?php _e('in-page anchor on main calendar page (list)','my-calendar'); ?></option>
	<option value='calendaranchor'<?php echo jd_option_selected(get_option('mc_open_day_uri'),'calendaranchor','option'); ?>><?php _e('in-page anchor on main calendar page (grid)','my-calendar'); ?></option>	
	</select>
	<small><?php _e('Replaces pop-up in mini calendar','my-calendar'); ?></small>
	</li>
	<li><strong><?php _e('Show links to alternate formats:','my-calendar'); ?></strong></li>
	<li>
	<input type="checkbox" id="mc_show_rss" name="mc_show_rss" <?php mc_is_checked('mc_show_rss','true'); ?> /> <label for="mc_show_rss"><?php _e('Show link to My Calendar RSS feed.','my-calendar'); ?></label> <small><?php _e('RSS feed shows recently added events.','my-calendar'); ?></small>
	</li>
	<li>
	<input type="checkbox" id="mc_show_ical" name="mc_show_ical" <?php mc_is_checked('mc_show_ical','true'); ?> /> <label for="mc_show_ical"><?php _e('Show link to iCal format download.','my-calendar'); ?></label> <small><?php _e('iCal outputs events occurring in the current calendar month.','my-calendar'); ?></small> <input type="checkbox" id="mc_ical_utc" name="mc_ical_utc" <?php mc_is_checked('mc_ical_utc','true'); ?> /> <label for="mc_ical_utc"><?php _e('iCal times are UTC','my-calendar'); ?></label>

	</li>
	<li>
	<input type="checkbox" id="mc_show_print" name="mc_show_print" <?php mc_is_checked('mc_show_print','true'); ?> /> <label for="mc_show_print"><?php _e('Show link to print-formatted view of calendar','my-calendar'); ?></label>
	</li>
	</ul>
	<?php // End General Options // ?>
	</fieldset>
	
	<fieldset>
	<legend><?php _e('Grid Layout Options','my-calendar'); ?></legend>
	<ul>
	<li>
	<input type="checkbox" id="mc_show_weekends" name="mc_show_weekends" <?php mc_is_checked('mc_show_weekends','true'); ?> /> <label for="mc_show_weekends"><?php _e('Show Weekends on Calendar','my-calendar'); ?></label>
	</li>
	<li>
	<input type="checkbox" id="mc_convert" name="mc_convert" <?php mc_is_checked('mc_convert','true'); ?> /> <label for="mc_convert"><?php _e('Switch to list view on mobile devices','my-calendar'); ?></label>
	</li>	
	</ul>	
	<?php // End Grid Options // ?>
	</fieldset>	
	
	<fieldset>
	<legend><?php _e('List Layout Options','my-calendar'); ?></legend>
	<ul>
	<li>
	<label for="mc_show_months"><?php _e('How many months of events to show at a time:','my-calendar'); ?></label> <input type="text" size="3" id="mc_show_months" name="mc_show_months" value="<?php echo $mc_show_months; ?>" />
	</li>
	<li>
	<input type="checkbox" id="mc_show_list_info" name="mc_show_list_info" <?php mc_is_checked( 'mc_show_list_info','true' ); ?> /> <label for="mc_show_list_info"><?php _e('Show the first event\'s title and the number of events that day next to the date.','my-calendar'); ?></label>
	</li>	
	</ul>	
	<?php // End List Options // ?>
	</fieldset>	

	<fieldset>
	<legend><?php _e('Event Details Options','my-calendar'); ?></legend>
	<ul class="columns">
	<li>
	<input type="checkbox" id="mc_display_author" name="mc_display_author" <?php mc_is_checked('mc_display_author','true'); ?> /> <label for="mc_display_jump"><?php _e('Show author\'s name','my-calendar'); ?></label>
	</li>
	<li>
	<input type="checkbox" id="mc_show_event_vcal" name="mc_show_event_vcal" <?php mc_is_checked('mc_show_event_vcal','true'); ?> /> <label for="mc_show_ical"><?php _e('Show link to single event iCal download','my-calendar'); ?></label> 
	</li>		
	<li>
	<input type="checkbox" id="mc_hide_icons" name="mc_hide_icons" <?php mc_is_checked('mc_hide_icons','false'); ?> /> <label for="mc_hide_icons"><?php _e('Show category icons','my-calendar'); ?></label>
	</li>
	<li>
	<input type="checkbox" id="mc_show_map" name="mc_show_map" <?php mc_is_checked('mc_show_map','true'); ?> /> <label for="mc_show_map"><?php _e('Show Link to Google Map','my-calendar'); ?></label>
	</li>
	<li>
	<input type="checkbox" id="mc_show_address" name="mc_show_address" <?php mc_is_checked('mc_show_address','true'); ?> /> <label for="mc_show_address"><?php _e('Show Event Address','my-calendar'); ?></label>
	</li>
	<li>
	<input type="checkbox" id="mc_short" name="mc_short" <?php mc_is_checked('mc_short','true'); ?> /> <label for="mc_short"><?php _e('Show short description','my-calendar'); ?></label>
	</li>
	<li>
	<input type="checkbox" id="mc_desc" name="mc_desc" <?php mc_is_checked('mc_desc','true'); ?> /> <label for="mc_desc"><?php _e('Show full description','my-calendar'); ?></label>
	</li>
	<li>
	<input type="checkbox" id="mc_process_shortcodes" name="mc_process_shortcodes" <?php mc_is_checked('mc_process_shortcodes','true'); ?> /> <label for="mc_process_shortcodes"><?php _e('Process WordPress shortcodes in description fields','my-calendar'); ?></label>
	</li>	
	<li>
	<input type="checkbox" id="mc_details" name="mc_details" <?php mc_is_checked('mc_details','true'); ?> /> <label for="mc_details"><?php _e('Show link to single-event details (requires <a href=\'#mc_uri\'>URL</a>)','my-calendar'); ?></label>
	</li>
	<li>
	<input type="checkbox" id="mc_event_link" name="mc_event_link" <?php mc_is_checked('mc_event_link','true'); ?> /> <label for="mc_event_link"><?php _e('Show external link','my-calendar'); ?></label>
	</li>
	<li>
	<input type="checkbox" id="mc_event_link_expires" name="mc_event_link_expires" <?php mc_is_checked('mc_event_link_expires','true'); ?> /> <label for="mc_event_link_expires"><?php _e('Event links expire after event passes.','my-calendar'); ?></label>
	</li>
	<li>
	<input type="checkbox" id="mc_event_registration" name="mc_event_registration" <?php mc_is_checked('mc_event_registration','true'); ?> /> <label for="mc_event_registration"><?php _e('Show availability status','my-calendar'); ?></label>
	</li>
	<li>
    <input type="radio" id="mc_apply_color_default" name="mc_apply_color" value="default" <?php if ( get_option('mc_apply_color' ) == '' ) { echo 'checked="checked"'; } else { mc_is_checked('mc_apply_color','default'); } ?> /> <label for="mc_apply_color_default"><?php _e('Default usage of category colors.','my-calendar'); ?></label><br />
    <input type="radio" id="mc_apply_color_to_titles" name="mc_apply_color" value="font" <?php mc_is_checked('mc_apply_color','font'); ?> /> <label for="mc_apply_color_to_titles"><?php _e('Event titles are category colors.','my-calendar'); ?></label><br />
	<input type="radio" id="mc_apply_bgcolor_to_titles" name="mc_apply_color" value="background" <?php mc_is_checked('mc_apply_color','background'); ?> /> <label for="mc_apply_bgcolor_to_titles"><?php _e('Event titles have category color as background.','my-calendar'); ?></label>	
	</li>	
	</ul>	
	<?php // End Event Options // ?>
	</fieldset>
	<fieldset>
	<legend><?php _e('Event Scheduling Defaults','my-calendar'); ?></legend>
	<ul>
	<li>
	<input type="checkbox" id="mc_no_fifth_week" name="mc_no_fifth_week" value="on" <?php mc_is_checked('mc_no_fifth_week','true'); ?> /> <label for="mc_no_fifth_week"><?php _e('Default setting for event input: If a recurring event is scheduled for a date which doesn\'t exist (such as the 5th Wednesday in February), move it back one week.','my-calendar'); ?></label>	
	</li>
	<li>
	<label for="mc_skip_holidays_category"><?php _e('Holiday Category','my-calendar'); ?></label>
	<select id="mc_skip_holidays_category" name="mc_skip_holidays_category">
			<option value=''> -- <?php _e('None','my-calendar'); ?> -- </option>
			<?php
			// Grab all the categories and list them
			$sql = "SELECT * FROM " . my_calendar_categories_table();
			$cats = $mcdb->get_results($sql);
				foreach($cats as $cat) {
					echo '<option value="'.$cat->category_id.'"';
						if ( get_option('mc_skip_holidays_category') == $cat->category_id ){
						 echo ' selected="selected"';
						}
					echo '>'.stripslashes($cat->category_name)."</option>\n";
				}
			?>
			</select>
    </li>
	<li>
	<input type="checkbox" id="mc_skip_holidays" name="mc_skip_holidays" <?php mc_is_checked('mc_skip_holidays','true'); ?> /> <label for="mc_skip_holidays"><?php _e('Default setting for event input: If an event coincides with an event in the designated "Holiday" category, do not show the event.','my-calendar'); ?></label>
	</li>
	</ul>	
	<?php // End Scheduling Options // ?>
	</fieldset>
	</fieldset>
		<p>
		<input type="submit" name="save" class="button-secondary" value="<?php _e('Save Output Settings','my-calendar'); ?>" />
		</p>
</form>
</div>
</div>
</div>

<div class="ui-sortable meta-box-sortables">
<div class="postbox" id="my-calendar-time">
	<h3><?php _e('Calendar Time Formats','my-calendar'); ?></h3>
	<div class="inside">
	<form method="post" action="<?php echo admin_url("admin.php?page=my-calendar-config"); ?>">
	<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" /></div>
	<fieldset>
	<legend><?php _e('Set default date/time formats','my-calendar'); ?></legend>
	<div><input type='hidden' name='mc_dates' value='true' /></div>
	<ul class="two-columns">	
	<li class='mc-month-format'>
	<label for='mc_month_format'><?php _e('Month format (calendar headings)','my-calendar'); ?></label><br /><input type="text" id="mc_month_format" name="mc_month_format" value="<?php if ( get_option('mc_month_format')  == "") { echo ''; } else { echo esc_attr( get_option( 'mc_month_format') ); } ?>" /> <code><?php _e('Now:','my-calendar'); ?> <?php if ( get_option('mc_month_format') == '') { echo date_i18n( 'F Y' ); } else { echo date_i18n( get_option('mc_month_format') ); } ?></code>
	<li class='mc-time-format'>
	<label for="mc_time_format"><?php _e('Time format','my-calendar'); ?></label><br /><input type="text" id="mc_time_format" name="mc_time_format" value="<?php if ( get_option('mc_time_format')  == "") { echo ''; } else { echo esc_attr( get_option( 'mc_time_format') ); } ?>" /> <code><?php _e('Now:','my-calendar'); ?> <?php if ( get_option('mc_time_format') == '') { echo date_i18n( get_option('time_format') ); } else { echo date_i18n( get_option('mc_time_format') ); } ?></code>
	</li>	
	<li class='mc-week-format'>
	<label for="mc_week_format"><?php _e('Date in grid mode, week view','my-calendar'); ?></label><br /><input type="text" id="mc_week_format" name="my_calendar_week_format" value="<?php if ( get_option('mc_week_format')  == "") { echo ''; } else { echo esc_attr( get_option( 'mc_week_format') ); } ?>" /> <code><?php _e('Now:','my-calendar'); ?> <?php if ( get_option('mc_week_format') == '') { echo date_i18n('M j, \'y'); } else { echo date_i18n( get_option('mc_week_format') ); } ?></code>
	</li>	
	<li class='mc-date-format'>
	<label for="mc_date_format"><?php _e('Date Format in other views','my-calendar'); ?></label><br /><input type="text" id="mc_date_format" name="mc_date_format" value="<?php if ( get_option('mc_date_format')  == "") { echo esc_attr( get_option('date_format') ); } else { echo esc_attr(  get_option( 'mc_date_format') ); } ?>" /> <code><?php _e('Now:','my-calendar'); ?> <?php if ( get_option('mc_date_format') == '') { echo date_i18n(get_option('date_format')); } else { echo date_i18n( get_option('mc_date_format') ); } ?></code>
	</li>
	<li>
	<?php _e('Date formats use the same syntax as the <a href="http://php.net/date">PHP <code>date()</code> function</a>. Save options to update sample output.','my-calendar'); ?>
	</li>	
	</ul>
	</fieldset>
		<p>
		<input type="submit" name="save" class="button-primary" value="<?php _e('Save Date/Time Settings','my-calendar'); ?>" />
	</p>
	</form>	
	</div>
</div>
</div>


<div class="ui-sortable meta-box-sortables">   
<div class="postbox" id="my-calendar-input">
	<h3><?php _e('Calendar Input Settings','my-calendar'); ?></h3>
	<div class="inside">
<form method="post" action="<?php echo admin_url("admin.php?page=my-calendar-config"); ?>">
	<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" /></div>
	<fieldset>
	<legend><?php _e('Select which input fields will be available when adding or editing events.','my-calendar'); ?></legend>
	<div><input type='hidden' name='mc_input' value='true' /></div>
	<ul class="columns">
	<?php 
		$input_options = get_option('mc_input_options');
		$input_labels = array('event_location_dropdown'=>__('Show Event Location Dropdown Menu','my-calendar'),'event_short'=>__('Show Event Short Description field','my-calendar'),'event_desc'=>__('Show Event Description Field','my-calendar'),'event_category'=>__('Show Event Category field','my-calendar'),'event_image'=>__('Show Event Image field','my-calendar'),'event_link'=>__('Show Event Link field','my-calendar'),'event_recurs'=>__('Show Event Recurrence Options','my-calendar'),'event_open'=>__('Show Event Registration options','my-calendar'),'event_location'=>__('Show Event Location fields','my-calendar'),'event_use_editor'=>__('Use HTML Editor in Event Description Field','my-calendar'),'event_specials'=>__('Set Special Scheduling options','my-calendar') );
		$output = '';
		// if input options isn't an array, we'll assume that this plugin wasn't upgraded properly, and reset them to the default.
		if ( !is_array($input_options) ) {
			update_option( 'mc_input_options',array('event_short'=>'on','event_desc'=>'on','event_category'=>'on','event_image'=>'on','event_link'=>'on','event_recurs'=>'on','event_open'=>'on','event_location'=>'on','event_location_dropdown'=>'on','event_use_editor'=>'on','event_specials'=>'on' ) );	
		}
	foreach ($input_options as $key=>$value) {
			$checked = ($value == 'on')?"checked='checked'":'';
			$output .= "<li><input type=\"checkbox\" id=\"mci_$key\" name=\"mci_$key\" $checked /> <label for=\"mci_$key\">$input_labels[$key]</label></li>";
		}
		echo $output;
	?>
	<li>
	<input type="checkbox" id="mc_input_options_administrators" name="mc_input_options_administrators" <?php mc_is_checked('mc_input_options_administrators','true'); ?> /> <label for="mc_input_options_administrators"><strong><?php _e('Administrators see all input options','my-calendar'); ?></strong></label>
	</li>
	</ul>
	</fieldset>
		<p>
		<input type="submit" name="save" class="button-primary" value="<?php _e('Save Input Settings','my-calendar'); ?>" />
	</p>
</form>
</div>
</div>
</div>

<?php if ( current_user_can('manage_network') ) { ?>
<div class="ui-sortable meta-box-sortables">   
<div class="postbox" id="my-calendar-multisite">
	<h3><?php _e('Multisite Settings (Network Administrators only)','my-calendar'); ?></h3>
	<div class="inside">
	<p><strong><?php _e('Multisite support is a beta feature - use with caution.','my-calendar'); ?></strong></p>
	<form method="post" action="<?php echo admin_url("admin.php?page=my-calendar-config"); ?>">
	<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" /></div>	
	<div><input type='hidden' name='mc_network' value='true' /></div>	
	<fieldset>
	<legend><?php _e('Settings for WP MultiSite configurations','my-calendar'); ?></legend>
	<p><?php _e('The central calendar is the calendar associated with the primary site in your WordPress Multisite network.','my-calendar'); ?></p>	
	<ul>
	<li><input type="radio" value="0" id="ms0" name="mc_multisite"<?php echo jd_option_selected(get_site_option('mc_multisite'),'0'); ?> /> <label for="ms0"><?php _e('Site owners may only post to their local calendar','my-calendar'); ?></label></li>
	<li><input type="radio" value="1" id="ms1" name="mc_multisite"<?php echo jd_option_selected(get_site_option('mc_multisite'),'1'); ?> /> <label for="ms1"><?php _e('Site owners may only post to the central calendar','my-calendar'); ?></label></li>
	<li><input type="radio" value="2" id="ms2" name="mc_multisite"<?php echo jd_option_selected(get_site_option('mc_multisite'),2); ?> /> <label for="ms2"><?php _e('Site owners may manage either calendar','my-calendar'); ?></label></li>
	</ul>
	<p class="notice"><strong>*</strong> <?php _e('Changes only effect input permissions. Public-facing calendars will be unchanged.','my-calendar'); ?></p>
	<ul>
	<li><input type="radio" value="0" id="mss0" name="mc_multisite_show"<?php echo jd_option_selected(get_site_option('mc_multisite_show'),'0'); ?> /> <label for="mss0"><?php _e('Sub-site calendars show events from their local calendar.','my-calendar'); ?></label></li>
	<li><input type="radio" value="1" id="mss1" name="mc_multisite_show"<?php echo jd_option_selected(get_site_option('mc_multisite_show'),'1'); ?> /> <label for="mss1"><?php _e('Sub-site calendars show events from the central calendar.','my-calendar'); ?></label></li>
	</ul>
	</fieldset>
		<p>
		<input type="submit" name="save" class="button-primary" value="<?php _e('Save Multisite Settings','my-calendar'); ?>" />
		</p>
</form>	
	</div>
</div>
</div>
<?php } ?>

<div class="ui-sortable meta-box-sortables">   
<div class="postbox" id="my-calendar-permissions">
	<h3><?php _e('My Calendar Permissions','my-calendar'); ?></h3>
	<div class="inside">	
	<?php if ( current_user_can('administrator') ) { ?>

    <form method="post" action="<?php echo admin_url("admin.php?page=my-calendar-config"); ?>">
	<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" /></div> 	
	<fieldset>
    <legend><?php _e('Calendar Options: Permissions','my-calendar'); ?></legend>
	<?php 
		function mc_check_caps($role,$cap) {
			$role = get_role($role);
			if ( $role->has_cap($cap) ) { return " checked='checked'"; }
		}
		function mc_cap_checkbox( $role, $cap, $name ) {
			return "<li><input type='checkbox' id='mc_caps_{$role}_$cap' name='mc_caps[$role][$cap]' value='on'".mc_check_caps($role,$cap)." /> <label for='mc_caps_{$role}_$cap'>$name</label></li>";
		}
		global $wp_roles;
		$roles = $wp_roles->get_names();
		$caps = array( 
					'mc_add_events'=>__('Add Events','my-calendar'),
					'mc_approve_events'=>__('Approve Events','my-calendar'),
					'mc_manage_events'=>__('Manage Events','my-calendar'),
					'mc_edit_cats'=>__('Edit Categories','my-calendar'),
					'mc_edit_locations'=>__('Edit Locations','my-calendar'),
					'mc_edit_styles'=>__('Edit Styles','my-calendar'),
					'mc_edit_behaviors'=>__('Edit Behaviors','my-calendar'),
					'mc_edit_templates'=>__('Edit Templates','my-calendar'),
					'mc_edit_settings'=>__('Edit Settings','my-calendar'),
					'mc_view_help'=>__('View Help','my-calendar')
					);
		foreach ( $roles as $role=>$rolename ) {
			if ( $role == 'administrator' ) continue;
			echo "<fieldset id='mc_$role' class='roles'><legend>$rolename</legend><ul>";
			echo "<li><input type='hidden' value='none' name='mc_caps[".$role."][none]' /></li>";
			foreach( $caps as $cap=>$name ) {
				echo mc_cap_checkbox( $role, $cap,$name );
			}
			echo "</ul></fieldset>";
		}
	
	?>	
	</fieldset>
		<p>
		<input type="submit" name="mc_permissions" class="button-primary" value="<?php _e('Save Permissions','my-calendar'); ?>" />
		</p>
	</form>
	<?php } else { ?>
		<?php _e('My Calendar permission settings are only available to administrators.','my-calendar'); ?>
	<?php } ?>	
	</div>
</div>
</div>

<div class="ui-sortable meta-box-sortables">   
<div class="postbox" id="my-calendar-email">
	<h3><?php _e('Calendar Email Settings','my-calendar'); ?></h3>
	<div class="inside">
<form method="post" action="<?php echo admin_url("admin.php?page=my-calendar-config"); ?>">
	<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" /></div>
	<fieldset>
	<legend><?php _e('Calendar Options: Email Notifications','my-calendar'); ?></legend>
<div><input type='hidden' name='mc_email' value='true' /></div>
	<ul>
	<li>
	<input type="checkbox" id="mc_event_mail" name="mc_event_mail" <?php mc_is_checked('mc_event_mail','true'); ?> /> <label for="mc_event_mail"><strong><?php _e('Send Email Notifications when new events are scheduled or reserved.','my-calendar'); ?></strong></label>
	</li>
	<li>
	<label for="mc_event_mail_to"><?php _e('Notification messages are sent to: ','my-calendar'); ?></label> <input type="text" id="mc_event_mail_to" name="mc_event_mail_to" size="40"  value="<?php if ( get_option('mc_event_mail_to') == "") { bloginfo('admin_email'); } else { echo stripslashes(esc_attr( get_option('mc_event_mail_to')) ); } ?>" />
	</li>	
	<li>
	<label for="mc_event_mail_subject"><?php _e('Email subject','my-calendar'); ?></label> <input type="text" id="mc_event_mail_subject" name="mc_event_mail_subject" size="60" value="<?php if ( get_option('mc_event_mail_subject') == "") { bloginfo('name'); echo ': '; _e('New event Added','my-calendar'); } else { echo stripslashes(esc_attr( get_option('mc_event_mail_subject') ) ); } ?>" />
	</li>
	<li>
	<label for="mc_event_mail_message"><?php _e('Message Body','my-calendar'); ?></label><br /> <textarea rows="6" cols="80"  id="mc_event_mail_message" name="mc_event_mail_message"><?php if ( get_option('mc_event_mail_message') == "") { _e('New Event:','my-calendar'); echo "\n{title}: {date}, {time} - {event_status}"; } else { echo stripslashes( esc_attr( get_option('mc_event_mail_message') ) ); } ?></textarea><br />
	<a href="<?php echo admin_url("admin.php?page=my-calendar-help#templates"); ?>"><?php _e("Shortcode Help",'my-calendar'); ?></a> <?php _e('All template shortcodes are available.','my-calendar'); ?>
	</li>
	</ul>
	</fieldset>
		<p>
		<input type="submit" name="save" class="button-primary" value="<?php _e('Save Email Settings','my-calendar'); ?>" />
		</p>
</form>
</div>
</div>
</div>

<div class="ui-sortable meta-box-sortables">   
<div class="postbox" id="my-calendar-user">
	<h3><?php _e('Calendar User Settings','my-calendar'); ?></h3>
	<div class="inside">
	<?php if ( current_user_can('edit_users') ) { ?>
<form method="post" action="<?php echo admin_url("admin.php?page=my-calendar-config"); ?>">
<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" /></div>
<div><input type='hidden' name='mc_user' value='true' /></div>

	<fieldset>
	<legend><?php _e('Settings which can be configured in registered user\'s accounts','my-calendar'); ?></legend>
	<p>
	<input type="checkbox" id="mc_user_settings_enabled" name="mc_user_settings_enabled" value="on" <?php mc_is_checked('mc_user_settings_enabled','true'); ?> /> <label for="mc_user_settings_enabled"><strong><?php _e('Allow registered users to provide timezone or location presets in their user profiles.','my-calendar'); ?></strong></label>
	</p>

<?php

$mc_user_settings = get_option('mc_user_settings'); 
if (!is_array($mc_user_settings)) {
	update_option( 'mc_user_settings', $default_user_settings );
	$mc_user_settings = get_option('mc_user_settings');
}
?>
<fieldset>
<legend><?php _e('Timezone Settings','my-calendar'); ?></legend>
<p><?php _e('These settings provide registered users with the ability to select a time zone in their user profile. When they view your calendar, the times for events will display the time the event happens in their time zone as well as the entered value.','my-calendar'); ?></p>
	<p>
	<input type="checkbox" id="tz_enabled" name="mc_user_settings[my_calendar_tz_default][enabled]" <?php mc_is_checked('mc_user_settings','on','my_calendar_tz_default'); ?> /> <label for="tz_enabled"><?php _e('Enable Timezone','my-calendar'); ?></label>
	</p>
	<p>
	<label for="tz_label"><?php _e('Select Timezone Label','my-calendar'); ?></label> <input type="text" name="mc_user_settings[my_calendar_tz_default][label]" id="tz_label" value="<?php echo stripslashes(esc_attr($mc_user_settings['my_calendar_tz_default']['label'])); ?>" size="40" />
	</p>
	<p>
	<label for="tz_values"><?php _e('Timezone Options','my-calendar'); ?> (<?php _e('Value, Label; one per line','my-calendar'); ?>)</label><br />
 	<?php 
	$timezones = '';
foreach ( $mc_user_settings['my_calendar_tz_default']['values'] as $key=>$value ) {
$timezones .= stripslashes("$key,$value")."\n";
}
	?>	
	<textarea name="mc_user_settings[my_calendar_tz_default][values]" id="tz_values" cols="80" rows="6"><?php echo trim($timezones); ?></textarea>
	</p>
</fieldset>

<fieldset>
<legend><?php _e('Location Settings','my-calendar'); ?></legend>
<p><?php _e('These settings provide registered users with the ability to select a location in their user profile. When they view your calendar, their initial view will be limited to locations which include that location parameter. These values can also be used to generate custom location filtering options using the <code>my_calendar_locations</code> shortcode. It is not necessary to enable these settings for users to use the custom filtering options.','my-calendar'); ?></p>
	<p>
	<input type="checkbox" id="loc_enabled" name="mc_user_settings[my_calendar_location_default][enabled]" <?php mc_is_checked('mc_user_settings','on','my_calendar_location_default'); ?> /> <label for="loc_enabled"><?php _e('Enable Location','my-calendar'); ?></label>
	</p>
	<p>
	<input type="checkbox" id="loc_control" name="mc_location_control" <?php mc_is_checked('mc_location_control','on' ); ?> /> <label for="loc_control"><?php _e('Use this location list as input control','my-calendar'); ?></label> <small><?php _e('The normal text entry for this location type will be replaced by a drop down containing these choices.','my-calendar'); ?></small>
	</p>
	<p>
	<label for="loc_label"><?php _e('Select Location Label','my-calendar'); ?></label> <input type="text" name="mc_user_settings[my_calendar_location_default][label]" id="loc_label" value="<?php echo stripslashes( esc_attr( $mc_user_settings['my_calendar_location_default']['label'] ) ); ?>" size="40" />
	</p>
	<p>
	<label for="loc_values"><?php _e('Location Options','my-calendar'); ?> (<?php _e('Value, Label; one per line','my-calendar'); ?>)</label><br />
	<?php 
	$locations = '';
foreach ( $mc_user_settings['my_calendar_location_default']['values'] as $key=>$value ) {
$locations .= stripslashes("$key,$value")."\n";
}
?>
	<textarea name="mc_user_settings[my_calendar_location_default][values]" id="loc_values" cols="80" rows="6"><?php echo trim($locations); ?></textarea>
	</p>
	<p>
	<label for="loc_type"><?php _e('Location Type','my-calendar'); ?></label><br />
	<select id="loc_type" name="mc_location_type">
	<option value="event_label" <?php mc_is_selected( 'mc_location_type','event_label' ); ?>><?php _e('Location Name','my-calendar'); ?></option>
	<option value="event_city" <?php mc_is_selected( 'mc_location_type','event_city' ); ?>><?php _e('City','my-calendar'); ?></option>
	<option value="event_state" <?php mc_is_selected( 'mc_location_type','event_state'); ?>><?php _e('State/Province','my-calendar'); ?></option>
	<option value="event_country" <?php mc_is_selected( 'mc_location_type','event_country'); ?>><?php _e('Country','my-calendar'); ?></option>
	<option value="event_postcode" <?php mc_is_selected( 'mc_location_type','event_postcode'); ?>><?php _e('Postal Code','my-calendar'); ?></option>
	<option value="event_region" <?php mc_is_selected( 'mc_location_type','event_region'); ?>><?php _e('Region','my-calendar'); ?></option>	
	</select>
	</p>
</fieldset>
	</fieldset>
	<p>
		<input type="submit" name="save" class="button-primary" value="<?php _e('Save User Settings','my-calendar'); ?>" /> <input type="submit" name="save" class="button-secondary" value="<?php _e('Save User Settings','my-calendar'); ?>" />
	</p>
  </form>  
<?php } else { ?>
	<?php _e('Only users with the ability to edit user accounts may modify user settings.','my-calendar'); ?>
<?php } ?>
	</div>
</div>

<?php $mc_settings = apply_filters( 'mc_after_settings','' ); echo $mc_settings; ?>

</div>
</div>
</div>

	<?php jd_show_support_box(); ?>

</div>
<?php
}
?>