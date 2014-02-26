<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'my-calendar-group-manager.php' == basename($_SERVER['SCRIPT_FILENAME'])) {
	die ('Please do not load this page directly. Thanks!');
}

function edit_my_calendar_groups() {
    global $current_user, $wpdb, $users_entries;
	$mcdb = $wpdb;
	
// First some quick cleaning up 
$edit = $save = false;

$action = !empty($_POST['event_action']) ? $_POST['event_action'] : '';
$event_id = !empty($_POST['event_id']) ? $_POST['event_id'] : '';
$group_id = !empty($_POST['group_id']) ? $_POST['group_id'] : '';


if ( isset( $_GET['mode'] ) ) {
	if ( $_GET['mode'] == 'edit' ) {
		$action = "edit";
		$event_id = (int) $_GET['event_id'];
		$group_id = (int) $_GET['group_id'];
	}
}

// Lets see if this is first run and create us a table if it is!
check_my_calendar();

if ( isset( $_POST['event_action'] ) ) {
	global $mc_output;
	$nonce=$_REQUEST['_wpnonce'];
    if (! wp_verify_nonce($nonce,'my-calendar-nonce') ) die("Security check failed");
	$proceed = false;
	switch ( $_POST['event_action'] ) {
	case 'edit':
		if ( isset( $_POST['apply'] ) && is_array($_POST['apply']) ) {
			$mc_output = mc_check_group_data( $action,$_POST );
			foreach ( $_POST['apply'] as $event_id ) {
				$response = my_calendar_save_group($action,$mc_output,$event_id);	
				echo $response;
			}
		}
	break;
	case 'break':
		foreach ( $_POST['break'] as $event_id ) {
			$update = array( 'event_group_id'=>0 );
			$formats = array( '%d' );
			//$mcdb->show_errors();
			$result = $mcdb->update( 
					my_calendar_table(), 
					$update, 
					array( 'event_id'=>$event_id ),
					$formats, 
					'%d' );
			//$mcdb->print_error();
			$url = ( get_option('mc_uri') != '' )?' '.sprintf(__('View <a href="%s">your calendar</a>.','my-calendar'),get_option('mc_uri') ):'';
				if ( $result === false ) {
					$message = "<div class='error'><p><strong>".__('Error','my-calendar').":</strong>".__('Event not updated.','my-calendar')."$url</p></div>";
				} else if ( $result === 0 ) {
					$message = "<div class='updated'><p>".__('Nothing was changed in that update.','my-calendar')."$url</p></div>";
				} else {
					$message = "<div class='updated'><p>".__('Event updated successfully','my-calendar').".$url</p></div>";
				}
		}
	break;
	case 'group':
		if ( isset($_POST['group']) && is_array( $_POST['group']) ) {
			$events = $_POST['group'];
			sort($events);
		}
		foreach ( $events as $event_id ) {
			$group_id = $events[0];
			$update = array( 'event_group_id'=>$group_id );
			$formats = array( '%d' );
			//$mcdb->show_errors();
			$result = $mcdb->update( 
					my_calendar_table(), 
					$update, 
					array( 'event_id'=>$event_id ),
					$formats, 
					'%d' );
			//$mcdb->print_error();
				if ( $result === false ) {
					$message = "<div class='error'><p><strong>".__('Error','my-calendar').":</strong>".__('Event not grouped.','my-calendar')."</p></div>";
				} else if ( $result === 0 ) {
					$message = "<div class='updated'><p>".__('Nothing was changed in that update.','my-calendar')."</p></div>";
				} else {
					$message = "<div class='updated'><p>".__('Event grouped successfully','my-calendar')."</p></div>";
				}
		}	
	break;
	}
}

?>

<div class="wrap jd-my-calendar" id="my-calendar">
<?php my_calendar_check_db();?>
	<?php
	if ( $action == 'edit' || ($action == 'edit' && $error_with_saving == 1) ) {
		?>
<div id="icon-edit" class="icon32"></div>		
		<h2><?php _e('Edit Event Group','my-calendar'); ?></h2>
		<?php
		if ( empty($event_id) || empty($group_id) ) {
			echo "<div class=\"error\"><p>".__("You must provide an event group id in order to edit it",'my-calendar')."</p></div>";
		} else {
			jd_groups_edit_form('edit', $event_id, $group_id );
		}	
	} else {
	?>	
<div id="icon-edit" class="icon32"></div>	
		<h2><?php _e('Manage Event Groups','my-calendar'); ?></h2>
		<p><?php _e('Grouped events can be edited simultaneously. When you choose a group of events to edit, the form will be pre-filled with the content applicable to the member of the event group you started from. (e.g., if you click on the "Edit Group" link for the 3rd of a set of events, the boxes will use the content applicable to that event.). You will also receive a set of checkboxes which will indicate which events in the group should have these changes applied. (All grouped events can also be edited individually.)','my-calendar'); ?></p>
		<p><?php _e('The following fields will never be changed when editing groups: registration availability, event publication status, spam flagging, event recurrence, event repetitions, and the start and end dates and times for that event.','my-calendar'); ?></p>
	<?php } ?>
	
	<?php jd_show_support_box(); ?>
	<?php jd_groups_display_list(); ?>
</div>
<?php
} 

function my_calendar_save_group( $action,$output,$event_id=false ) {
global $wpdb,$event_author;
	$mcdb = $wpdb;
	$proceed = $output[0];
	$message = '';
	if ( $action == 'edit' && $proceed == true ) {
		$event_author = (int) ($_POST['event_author']);
		if ( mc_can_edit_event( $event_author ) ) {	
			$update = $output[2];
			$formats = array( 
						'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',
						'%d','%d','%d','%d','%d',
						'%f','%f'
						);
			//$mcdb->show_errors();
			$result = $mcdb->update( 
					my_calendar_table(), 
					$update, 
					array( 'event_id'=>$event_id ),
					$formats, 
					'%d' );
			//$mcdb->print_error();
			$url = ( get_option('mc_uri') != '' )?' '.sprintf(__('View <a href="%s">your calendar</a>.','my-calendar'),get_option('mc_uri') ):'';
				if ( $result === false ) {
					$message = "<div class='error'><p><strong>".__('Error','my-calendar').":</strong>".__('Your event was not updated.','my-calendar')."$url</p></div>";
				} else if ( $result === 0 ) {
					$message = "<div class='updated'><p>".__('Nothing was changed in that update.','my-calendar')."$url</p></div>";
				} else {
					$message = "<div class='updated'><p>".__('Event updated successfully','my-calendar').".$url</p></div>";
					mc_delete_cache();
				}
		} else {
			$message = "<div class='error'><p><strong>".__('You do not have sufficient permissions to edit that event.','my-calendar')."</strong></p></div>";
		}			
	}
	$message = $message ."\n". $output[3];
	return $message;
}

function jd_acquire_group_data($event_id=false) {
global $wpdb,$users_entries;
	$mcdb = $wpdb;
	if ( $event_id !== false ) {
		if ( intval($event_id) != $event_id ) {
			return "<div class=\"error\"><p>".__('Sorry! That\'s an invalid event key.','my-calendar')."</p></div>";
		} else {
			$data = $mcdb->get_results("SELECT * FROM " . my_calendar_table() . " WHERE event_id='" . (int) $event_id . "' LIMIT 1");
			if ( empty($data) ) {
				return "<div class=\"error\"><p>".__("Sorry! We couldn't find an event with that ID.",'my-calendar')."</p></div>";
			}
			$data = $data[0];
		}
		// Recover users entries if they exist; in other words if editing an event went wrong
		if (!empty($users_entries)) {
		    $data = $users_entries;
		}
	} else {
	  // Deal with possibility that form was submitted but not saved due to error - recover user's entries here
	  $data = $users_entries;
	}
	return $data;

}

function mc_compare_group_members( $group_id, $field=false ) {
	global $wpdb;
	$mcdb = $wpdb;
	if ( !$field ) {
		$query = "SELECT event_title, event_desc, event_short, event_link, event_label, 
					event_street, event_street2, event_city, event_state, event_postcode, 
					event_region, event_country, event_url, event_image, event_category, 
					event_link_expires, event_zoom, event_phone, event_open, event_host, event_longitude, event_latitude 
			  FROM ".my_calendar_table()." WHERE event_group_id = $group_id";
	} else {
		// just comparing a single field
		$query = "SELECT $field FROM ".my_calendar_table()." WHERE event_group_id = $group_id";
	}
	$results = $mcdb->get_results( $query, ARRAY_N );
	$count = count($results);
	for($i=0;$i<$count;$i++) {
		$n = (($i+1)>$count-1)?0:$i+1;
		if ( md5(implode('',$results[$i])) != md5(implode('',$results[$n])) ) {
			return false;
		}
	}
	return true;
}

function mc_group_form( $group_id, $type='break' ) {
	global $wpdb;
	$mcdb = $wpdb;
	$event_id = (int) $_GET['event_id'];
	$nonce = wp_create_nonce('my-calendar-nonce');
	$query = "SELECT event_id, event_begin, event_time FROM ".my_calendar_table()." WHERE event_group_id = $group_id";
	$results = $mcdb->get_results($query);
	if ( $type == 'apply' ) {
		$warning = (!mc_compare_group_members($group_id))?"<p class='warning'>".__('<strong>NOTE:</strong> The group editable fields for the events in this group do not match','my-calendar')."</p>":'<p>'.__('The group editable fields for the events in this group match.','my-calendar').'</p>';
	} else {
		$warning = '';
	}
	$class = ($type == 'break')?'break':'apply';
	$group = "<div class='group $class'>";
	$group .= $warning;
	$group .= ($type == 'apply')?"<fieldset><legend>".__('Apply these changes to:','my-calendar')."</legend>":'';
	$group .= ($type == 'break')?"<form method='post' action='".admin_url("admin.php?page=my-calendar-groups&amp;mode=edit&amp;event_id=$event_id&amp;group_id=$group_id")."'>
	<div><input type='hidden' value='$group_id' name='group_id' /><input type='hidden' value='$type' name='event_action' /><input type='hidden' name='_wpnonce' value='$nonce' />
</div>":'';
	$group .= "<ul class='columns'>";
	$checked = ( $type=='apply' )?' checked="checked"':'';
	foreach ( $results as $result ) {
		$date = date_i18n( 'D, j M, Y', strtotime( $result->event_begin ) );
		$time = date_i18n( 'g:i a', strtotime( $result->event_time ) );
		$group .= "<li><input type='checkbox' name='$type"."[]' value='$result->event_id' id='$type$result->event_id'$checked /> <label for='break$result->event_id'><a href='#event$result->event_id'>#$result->event_id</a>: $date; $time</label></li>\n";
	}
	$group .= "<li><input type='checkbox' class='selectall' id='$type'$checked /> <label for='$type'><b>".__('Check/Uncheck all','my-calendar')."</b></label></li>\n</ul>";
	$group .= ($type == 'apply')?"</fieldset>":'';
	$group .= ($type == 'break')?"<p><input type='submit' class='button' value='".__('Remove checked events from this group','my-calendar')."' /></p></form>":'';
	$group .= "</div>";
	return $group;
}

// The event edit form for the manage events admin page
function jd_groups_edit_form( $mode='edit', $event_id=false, $group_id=false ) {
	global $wpdb,$users_entries,$user_ID, $output;
	$mcdb = $wpdb;
	$message = '';
	if ($event_id != false) {
		$data = jd_acquire_group_data( $event_id );
	} else {
		$data = $users_entries;
	}
	if ( $group_id != false) {
		$group = mc_group_form( $group_id, 'break' );
	} else {
		$message .= __('You must provide a group ID to edit groups','my-calendar'); 
	}
	
?>

	<?php echo ($message != '')?"<div class='error'><p>$message</p></div>":''; ?>
	<?php echo $group; ?>
	<?php my_calendar_print_group_fields($data,$mode,$event_id, $group_id); ?>

<?php
}

function my_calendar_print_group_fields( $data,$mode,$event_id,$group_id='' ) {
	global $user_ID, $wpdb;
	$mcdb = $wpdb;
	get_currentuserinfo();
	$user = get_userdata($user_ID);		
	$mc_input_administrator = (get_option('mc_input_options_administrators')=='true' && current_user_can('manage_options'))?true:false;
	$mc_input = get_option('mc_input_options');
?>

<div class="postbox-container" style="width: 70%">
<div class="metabox-holder">
<form method="post" action="<?php echo admin_url("admin.php?page=my-calendar-groups&amp;mode=edit&amp;event_id=$event_id&amp;group_id=$group_id"); ?>">
<div>
<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" />
<input type="hidden" name="group_id" value="<?php if ( !empty( $data->event_group_id ) ) { echo $data->event_group_id; } else { echo mc_group_id(); } ?>" />
<input type="hidden" name="event_action" value="<?php echo $mode; ?>" />
<input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
<input type="hidden" name="event_author" value="<?php echo $user_ID; ?>" />
<input type="hidden" name="event_nonce_name" value="<?php echo wp_create_nonce('event_nonce'); ?>" />
</div>
<div class="ui-sortable meta-box-sortables">
<div class="postbox">	
	<h3><?php _e('Manage Event Groups','my-calendar'); ?></h3>
	<div class="inside">
			<p>
                <input type="submit" name="save" class="button-primary" value="<?php _e('Edit Event Group','my-calendar'); ?>" />
			</p>
        <fieldset>
		<legend><?php _e('Enter your Event Information','my-calendar'); ?></legend>
		<p>
		<label for="event_title"><?php _e('Event Title','my-calendar'); ?> <span><?php _e('(required)','my-calendar'); ?></span><?php if ( !mc_compare_group_members( $group_id,'event_title' ) ) { echo " <span>".__('Fields do not match','my-calendar')."</span>"; } ?></label><br /><input type="text" id="event_title" name="event_title" class="input" size="60" value="<?php if ( !empty($data) ) echo stripslashes(esc_attr($data->event_title)); ?>" />
		</p>
			<?php 
			$apply = mc_group_form( $group_id, 'apply' ); 
			echo $apply; 
			?>		
		<?php if ( $data->event_repeats == 0 && $data->event_recur == 'S' ) { ?>
		<p>
		<input type="checkbox" value="1" id="event_span" name="event_span"<?php if ( !empty($data) && $data->event_span == '1' ) { echo " checked=\"checked\""; } else if ( !empty($data) && $data->event_span == '0' ) { echo ""; } else if ( get_option( 'mc_event_span' ) == 'true' ) { echo " checked=\"checked\""; } ?> /> <label for="event_span"><?php _e('Selected dates are a single multi-day event.','my-calendar'); ?><?php if ( !mc_compare_group_members( $group_id,'event_span' ) ) { echo " <span>".__('Fields do not match','my-calendar')."</span>"; } ?></label>
		</p>
		<?php } ?>
		<?php if ($mc_input['event_desc'] == 'on' || $mc_input_administrator ) { ?>
		<div id="group_description">
		<?php if ( !empty($data) ) { $description = $data->event_desc; } else { $description = ''; } ?>
		<label for="content"><?php _e('Event Description (<abbr title="hypertext markup language">HTML</abbr> allowed)','my-calendar'); ?><?php if ( !mc_compare_group_members( $group_id,'event_desc' ) ) { echo " <span>".__('Fields do not match','my-calendar')."</span>"; } ?></label><br /><?php if ( $mc_input['event_use_editor'] == 'on' ) {  if ( version_compare( get_bloginfo( 'version' ) , '3.3' , '>=' ) ) { wp_editor( stripslashes($description), 'content', array( 'textarea_rows'=>10 ) ); } else { the_editor( stripslashes($description) ); } }  else { ?><textarea id="content" name="content" class="event_desc" rows="5" cols="80"><?php echo stripslashes(esc_attr($description)); ?></textarea><?php if ( $mc_input['event_use_editor'] == 'on' ) { ?></div><?php } } ?>
		</div>		
		<?php } ?>
		<?php 
		// If the editor is enabled, shouldn't display the image uploader. 
		// It restricts use of the image uploader to a single image and forces it to be in 
		// the event image field, rather than the event description.
		if ( !isset($mc_input['event_image']) ) { $mc_input['event_image'] = 'off'; }	
		if ( ( $mc_input['event_image'] == 'on' && $mc_input['event_use_editor'] != 'on' ) || ( $mc_input_administrator && $mc_input['event_use_editor'] != 'on' ) ) { ?>
		<p>
		<?php if ( !empty($data->event_image) ) { ?>
		<div class="event_image"><?php _e("This event's image:",'my-calendar'); ?><br /><img src="<?php if ( !empty($data) ) echo esc_attr($data->event_image); ?>" alt="" /></div>
		<?php } ?>
		<label for="event_image"><?php _e("Add an image:",'my-calendar'); ?><?php if ( !mc_compare_group_members( $group_id,'event_image' ) ) { echo " <span>".__('Fields do not match','my-calendar')."</span>"; } ?></label> <input type="text" name="event_image" id="event_image" size="60" value="<?php if ( !empty($data) ) echo esc_attr($data->event_image); ?>" /> <input id="upload_image_button" type="button" class="button" value="<?php _e('Upload Image','my-calendar'); ?>" /><br /><?php _e('Include your image URL or upload an image.','my-calendar'); ?>
		</p>
		<?php } else { ?>
		<div>
		<input type="hidden" name="event_image" value="<?php if ( !empty($data) ) echo esc_attr($data->event_image); ?>" />
		<?php if ( !empty($data->event_image) ) { ?>
		<div class="event_image"><?php _e("This event's image:",'my-calendar'); ?><br /><img src="<?php echo esc_attr($data->event_image); ?>" alt="" /></div>
		<?php } ?>
		</div>
		<?php } ?>		
		<?php if ($mc_input['event_short'] == 'on' || $mc_input_administrator ) { ?>
		<p>
		<label for="event_short"><?php _e('Event Short Description (<abbr title="hypertext markup language">HTML</abbr> allowed)','my-calendar'); ?><?php if ( !mc_compare_group_members( $group_id,'event_short' ) ) { echo " <span>".__('Fields do not match','my-calendar')."</span>"; } ?></label><br /><textarea id="event_short" name="event_short" class="input" rows="2" cols="80"><?php if ( !empty($data) ) echo stripslashes(esc_attr($data->event_short)); ?></textarea>
		</p>
		<?php } ?>
	<p>
	<label for="event_host"><?php _e('Event Host','my-calendar'); ?><?php if ( !mc_compare_group_members( $group_id,'event_host' ) ) { echo " <span>".__('Fields do not match','my-calendar')."</span>"; } ?></label>
	<select id="event_host" name="event_host">
		<?php 
			 // Grab all the categories and list them
			$userList = my_calendar_getUsers();				 
			foreach($userList as $u) {
			 echo '<option value="'.$u->ID.'"';
					if (  is_object($data) && $data->event_host == $u->ID ) {
					 echo ' selected="selected"';
					} else if(  is_object($u) && $u->ID == $user->ID && empty($data->event_host) ) {
				    echo ' selected="selected"';
					}
				echo '>'.$u->display_name."</option>\n";
			}
		?>
	</select>
	</p>		
		<?php if ($mc_input['event_category'] == 'on' || $mc_input_administrator ) { ?>
        <p>
		<label for="event_category"><?php _e('Event Category','my-calendar'); ?><?php if ( !mc_compare_group_members( $group_id,'event_category' ) ) { echo " <span>".__('Fields do not match','my-calendar')."</span>"; } ?></label>
		<select id="event_category" name="event_category">
			<?php echo mc_category_select( $data ); ?>
		</select>
            </p>
			<?php } else { ?>
			<div>
			<input type="hidden" name="event_category" value="1" />
			</div>
			<?php } ?>
			<?php if ($mc_input['event_link'] == 'on' || $mc_input_administrator ) { ?>
			<p>
			<label for="event_link"><?php _e('Event Link (Optional)','my-calendar'); ?><?php if ( !mc_compare_group_members( $group_id,'event_link' ) ) { echo " <span>".__('Fields do not match','my-calendar')."</span>"; } ?></label> <input type="text" id="event_link" name="event_link" class="input" size="40" value="<?php if ( !empty($data) ) { echo esc_url($data->event_link); } ?>" /> <input type="checkbox" value="1" id="event_link_expires" name="event_link_expires"<?php if ( !empty($data) && $data->event_link_expires == '1' ) { echo " checked=\"checked\""; } else if ( !empty($data) && $data->event_link_expires == '0' ) { echo ""; } else if ( get_option( 'mc_event_link_expires' ) == 'true' ) { echo " checked=\"checked\""; } ?> /> <label for="event_link_expires"><?php _e('This link will expire when the event passes.','my-calendar'); ?></label>
			</p>
			<?php } ?>
			</fieldset>
</div>
</div>
</div>
<?php if ($mc_input['event_open'] == 'on' || $mc_input_administrator ) { 
// add a "don't change" option here ?>		
<div class="ui-sortable meta-box-sortables">
<div class="postbox">
<h3><?php _e('Event Registration Options','my-calendar'); ?></h3>
<div class="inside">
			<fieldset>
			<legend><?php _e('Event Registration Status','my-calendar'); ?><?php if ( !mc_compare_group_members( $group_id,'event_open' ) ) { echo " <span>".__('Fields do not match','my-calendar')."</span>"; } ?></legend>
			<p><em><?php _e('My Calendar does not manage event registrations. Use this for information only.','my-calendar'); ?></em></p>
			<p>
			<input type="radio" id="event_open" name="event_open" value="1" <?php if (!empty($data)) { echo jd_option_selected( $data->event_open,'1'); } ?> /> <label for="event_open"><?php _e('Open','my-calendar'); ?></label> 
			<input type="radio" id="event_closed" name="event_open" value="0" <?php if (!empty($data)) {  echo jd_option_selected( $data->event_open,'0'); } ?> /> <label for="event_closed"><?php _e('Closed','my-calendar'); ?></label>
			<input type="radio" id="event_none" name="event_open" value="2" <?php if (!empty($data)) { echo jd_option_selected( $data->event_open, '2' ); } else { echo " checked='checked'"; } ?> /> <label for="event_none"><?php _e('Does not apply','my-calendar'); ?></label>	
			</p>	
			<p>
			<input type="checkbox" name="event_group" id="event_group" <?php if (  is_object($data) ) { echo jd_option_selected( $data->event_group,'1'); } ?> /> <label for="event_group"><?php _e('If this event recurs, it can only be registered for as a complete series.','my-calendar'); ?></label>
			</p>				
			</fieldset>
</div>
</div>	
</div>		
			<?php } else { ?>
			<div>
			<input type="hidden" name="event_open" value="2" />
			</div>

			<?php } ?>

			<?php if ( ($mc_input['event_location'] == 'on' || $mc_input['event_location_dropdown'] == 'on') || $mc_input_administrator ) { ?>

<div class="ui-sortable meta-box-sortables">
<div class="postbox">
<h3><?php _e('Event Location','my-calendar'); ?></h3>
<div class="inside">
			<fieldset>
			<legend><?php _e('Event Location','my-calendar'); ?></legend>
			<?php } ?>
			<?php if ($mc_input['event_location_dropdown'] == 'on' || $mc_input_administrator ) { ?>
			<?php $locations = $mcdb->get_results("SELECT location_id,location_label FROM " . my_calendar_locations_table() . " ORDER BY location_label ASC");
				if ( !empty($locations) ) {
			?>				
			<p>
			<label for="location_preset"><?php _e('Choose a preset location:','my-calendar'); ?></label> <select name="location_preset" id="location_preset">
				<option value="none"> -- </option>
				<?php
				foreach ( $locations as $location ) {
					echo "<option value=\"".$location->location_id."\">".stripslashes($location->location_label)."</option>";
				}
?>
			</select>
			</p>
<?php
				} else {
				?>
				<input type="hidden" name="location_preset" value="none" />
				<p><a href="<?php echo admin_url("admin.php?page=my-calendar-locations"); ?>"><?php _e('Add recurring locations for later use.','my-calendar'); ?></a></p>
				<?php
				}
			?>
			<?php } else { ?>
				<input type="hidden" name="location_preset" value="none" />			
			<?php } ?>
			<?php if ($mc_input['event_location'] == 'on' || $mc_input_administrator ) { ?>			
			<p>
			<?php _e('All location fields are optional: <em>insufficient information may result in an inaccurate map</em>.','my-calendar'); ?>
			</p>
			<p>
			<label for="event_label"><?php _e('Name of Location (e.g. <em>Joe\'s Bar and Grill</em>)','my-calendar'); ?><?php if ( !mc_compare_group_members( $group_id,'event_label' ) ) { echo " <span>".__('Fields do not match','my-calendar')."</span>"; } ?></label> <input type="text" id="event_label" name="event_label" class="input" size="40" value="<?php if ( !empty($data) ) esc_attr_e(stripslashes($data->event_label)); ?>" />
			</p>
			<p>
			<label for="event_street"><?php _e('Street Address','my-calendar'); ?><?php if ( !mc_compare_group_members( $group_id,'event_street' ) ) { echo " <span>".__('Fields do not match','my-calendar')."</span>"; } ?></label> <input type="text" id="event_street" name="event_street" class="input" size="40" value="<?php if ( !empty($data) ) esc_attr_e(stripslashes($data->event_street)); ?>" />
			</p>
			<p>
			<label for="event_street2"><?php _e('Street Address (2)','my-calendar'); ?><?php if ( !mc_compare_group_members( $group_id,'event_street2' ) ) { echo " <span>".__('Fields do not match','my-calendar')."</span>"; } ?></label> <input type="text" id="event_street2" name="event_street2" class="input" size="40" value="<?php if ( !empty($data) ) esc_attr_e(stripslashes($data->event_street2)); ?>" />
			</p>
			<p>
			<label for="event_phone"><?php _e('Phone','my-calendar'); ?><?php if ( !mc_compare_group_members( $group_id,'event_phone' ) ) { echo " <span>".__('Fields do not match','my-calendar')."</span>"; } ?></label> <input type="text" id="event_phone" name="event_phone" class="input" size="32" value="<?php if ( !empty($data) ) esc_attr_e(stripslashes($data->event_phone)); ?>" />
			</p>			
			<p>
			<label for="event_city"><?php _e('City','my-calendar'); ?><?php if ( !mc_compare_group_members( $group_id,'event_city' ) ) { echo " <span>".__('Fields do not match','my-calendar')."</span>"; } ?></label> <input type="text" id="event_city" name="event_city" class="input" size="40" value="<?php if ( !empty($data) ) esc_attr_e(stripslashes($data->event_city)); ?>" /> <label for="event_state"><?php _e('State/Province','my-calendar'); ?><?php if ( !mc_compare_group_members( $group_id,'event_state' ) ) { echo " <span>".__('Fields do not match','my-calendar')."</span>"; } ?></label> <input type="text" id="event_state" name="event_state" class="input" size="10" value="<?php if ( !empty($data) ) esc_attr_e(stripslashes($data->event_state)); ?>" /> 
			</p>
			<p>
			<label for="event_postcode"><?php _e('Postal Code','my-calendar'); ?><?php if ( !mc_compare_group_members( $group_id,'event_postcode' ) ) { echo " <span>".__('Fields do not match','my-calendar')."</span>"; } ?></label> <input type="text" id="event_postcode" name="event_postcode" class="input" size="10" value="<?php if ( !empty($data) ) esc_attr_e(stripslashes($data->event_postcode)); ?>" />
			<label for="event_region"><?php _e('Region','my-calendar'); ?><?php if ( !mc_compare_group_members( $group_id,'event_region' ) ) { echo " <span>".__('Fields do not match','my-calendar')."</span>"; } ?></label> <input type="text" id="event_region" name="event_region" class="input" size="40" value="<?php if ( !empty( $data ) ) esc_attr_e(stripslashes($data->event_region)); ?>" />
			</p>
			<p>
			<label for="event_country"><?php _e('Country','my-calendar'); ?><?php if ( !mc_compare_group_members( $group_id,'event_country' ) ) { echo " <span>".__('Fields do not match','my-calendar')."</span>"; } ?></label> <input type="text" id="event_country" name="event_country" class="input" size="10" value="<?php if ( !empty($data) ) esc_attr_e(stripslashes($data->event_country)); ?>" />
			</p>
			<p>
			<label for="event_zoom"><?php _e('Initial Zoom','my-calendar'); ?><?php if ( !mc_compare_group_members( $group_id,'event_zoom' ) ) { echo " <span>".__('Fields do not match','my-calendar')."</span>"; } ?></label> 
				<select name="event_zoom" id="event_zoom">
				<option value="16"<?php if ( !empty( $data ) && ( $data->event_zoom == 16 ) ) { echo " selected=\"selected\""; } ?>><?php _e('Neighborhood','my-calendar'); ?></option>
				<option value="14"<?php if ( !empty( $data ) && ( $data->event_zoom == 14 ) ) { echo " selected=\"selected\""; } ?>><?php _e('Small City','my-calendar'); ?></option>
				<option value="12"<?php if ( !empty( $data ) && ( $data->event_zoom == 12 ) ) { echo " selected=\"selected\""; } ?>><?php _e('Large City','my-calendar'); ?></option>
				<option value="10"<?php if ( !empty( $data ) && ( $data->event_zoom == 10 ) ) { echo " selected=\"selected\""; } ?>><?php _e('Greater Metro Area','my-calendar'); ?></option>
				<option value="8"<?php if ( !empty( $data ) && ( $data->event_zoom == 8 ) ) { echo " selected=\"selected\""; } ?>><?php _e('State','my-calendar'); ?></option>
				<option value="6"<?php if ( !empty( $data ) && ( $data->event_zoom == 6 ) ) { echo " selected=\"selected\""; } ?>><?php _e('Region','my-calendar'); ?></option>
				</select>
			</p>
			<p>
			<label for="event_url"><?php _e('Location URL','my-calendar'); ?><?php if ( !mc_compare_group_members( $group_id,'event_url' ) ) { echo " <span>".__('Fields do not match','my-calendar')."</span>"; } ?></label> <input type="text" id="event_url" name="event_url" class="input" size="40" value="<?php if ( !empty( $data ) ) esc_attr_e(stripslashes($data->event_url)); ?>" />
			</p>
			<fieldset>
			<legend><?php _e('GPS Coordinates (optional)','my-calendar'); ?></legend>
			<p>
			<small><?php _e('If you supply GPS coordinates for your location, they will be used in place of any other address information to provide your map link.','my-calendar'); ?></small>
			</p>
			<p>
			<label for="event_latitude"><?php _e('Latitude','my-calendar'); ?><?php if ( !mc_compare_group_members( $group_id,'event_latitude' ) ) { echo " <span>".__('Fields do not match','my-calendar')."</span>"; } ?><?php if ( !mc_compare_group_members( $group_id,'event_longitude' ) ) { echo " <span>".__('Fields do not match','my-calendar')."</span>"; } ?></label> <input type="text" id="event_latitude" name="event_latitude" class="input" size="10" value="<?php if ( !empty( $data ) ) esc_attr_e(stripslashes($data->event_latitude)); ?>" /> <label for="event_longitude"><?php _e('Longitude','my-calendar'); ?></label> <input type="text" id="event_longitude" name="event_longitude" class="input" size="10" value="<?php if ( !empty( $data ) ) esc_attr_e(stripslashes($data->event_longitude)); ?>" />
			</p>
			</fieldset>	
			<?php } ?>
			<?php if ( ( $mc_input['event_location'] == 'on' || $mc_input['event_location_dropdown'] == 'on' ) || $mc_input_administrator ) { ?>
			</fieldset>
		</div>
		</div>
	</div>
			<?php } ?>	
			<p>
                <input type="submit" name="save" class="button-secondary" value="<?php _e('Edit Event Group','my-calendar'); ?>" />
			</p>
	</form>		

</div>
</div>
<?php }

function mc_check_group_data( $action,$post ) {
	global $wpdb, $current_user, $users_entries;
	$mcdb = $wpdb;

	$url_ok = 0;
	$title_ok = 0;
	$submit=array();

	if ( get_magic_quotes_gpc() ) {
		$post = array_map( 'stripslashes_deep', $post );
	}

	if (!wp_verify_nonce($post['event_nonce_name'],'event_nonce')) {
		return;
	}

$errors = "";
if ( $action == 'add' || $action == 'edit' || $action == 'copy' ) {
	$title = !empty($post['event_title']) ? trim($post['event_title']) : '';
	$desc = !empty($post['content']) ? trim($post['content']) : '';
	$short = !empty($post['event_short']) ? trim($post['event_short']) : '';
	$repeats = ( empty($post['event_repeats']) || trim($post['event_repeats'])=='' ) ? 0 : trim($post['event_repeats']);
	$host = !empty($post['event_host']) ? $post['event_host'] : $current_user->ID;
	$category = !empty($post['event_category']) ? $post['event_category'] : '';
    $linky = !empty($post['event_link']) ? trim($post['event_link']) : '';
    $expires = !empty($post['event_link_expires']) ? $post['event_link_expires'] : '0';
	$location_preset = !empty($post['location_preset']) ? $post['location_preset'] : '';
	$event_open = !empty($post['event_open']) ? $post['event_open'] : '2';
	$event_image = esc_url_raw( $post['event_image'] );
	$event_span = !empty($post['event_span']) ? 1 : 0;
	// set location
		if ($location_preset != 'none') {
			$sql = "SELECT * FROM " . my_calendar_locations_table() . " WHERE location_id = $location_preset";
			$location = $mcdb->get_row($sql);
			$event_label = $location->location_label;
			$event_street = $location->location_street;
			$event_street2 = $location->location_street2;
			$event_city = $location->location_city;
			$event_state = $location->location_state;
			$event_postcode = $location->location_postcode;
			$event_region = $location->location_region;
			$event_country = $location->location_country;
			$event_url = $location->location_url;			
			$event_longitude = $location->location_longitude;
			$event_latitude = $location->location_latitude;
			$event_zoom = $location->location_zoom;
			$event_phone = $location->location_phone;
		} else {
			$event_label = !empty($post['event_label']) ? $post['event_label'] : '';
			$event_street = !empty($post['event_street']) ? $post['event_street'] : '';
			$event_street2 = !empty($post['event_street2']) ? $post['event_street2'] : '';
			$event_city = !empty($post['event_city']) ? $post['event_city'] : '';
			$event_state = !empty($post['event_state']) ? $post['event_state'] : '';
			$event_postcode = !empty($post['event_postcode']) ? $post['event_postcode'] : '';
			$event_region = !empty($post['event_region']) ? $post['event_region'] : '';
			$event_country = !empty($post['event_country']) ? $post['event_country'] : '';
			$event_url = !empty($post['event_url']) ? $post['event_url'] : '';			
			$event_longitude = !empty($post['event_longitude']) ? $post['event_longitude'] : '';	
			$event_latitude = !empty($post['event_latitude']) ? $post['event_latitude'] : '';	
			$event_zoom = !empty($post['event_zoom']) ? $post['event_zoom'] : '';	
			$event_phone = !empty($post['event_phone']) ? $post['event_phone'] : '';
	    }
	
		// We check to make sure the URL is acceptable (blank or starting with http://)                                                        
		if ($linky == '') {
			$url_ok = 1;
		} else if ( preg_match('/^(http)(s?)(:)\/\//',$linky) ) {
			$url_ok = 1;
		} else {
			$linky = "http://" . $linky;
		}
	}
	// The title must be at least one character in length and no more than 255 - only basic punctuation is allowed
	$title_length = strlen($title);
	if ( $title_length > 1 && $title_length <= 255 ) {
	    $title_ok =1;
	} else {
		$errors .= "<div class='error'><p><strong>".__('Error','my-calendar').":</strong> ".__('The event title must be between 1 and 255 characters in length.','my-calendar')."</p></div>";
	}

	if ( $url_ok == 1 && $title_ok == 1 ) {
		$proceed = true;
		$submit = array(
		// strings
			'event_title'=>$title, 
			'event_desc'=>$desc, 
			'event_short'=>$short,
			'event_link'=>$linky,
			'event_label'=>$event_label, 
			'event_street'=>$event_street, 
			'event_street2'=>$event_street2, 
			'event_city'=>$event_city, 
			'event_state'=>$event_state, 
			'event_postcode'=>$event_postcode,
			'event_region'=>$event_region,
			'event_country'=>$event_country,
			'event_url'=>$event_url,	
			'event_image'=>$event_image,
			'event_phone'=>$event_phone,
		// integers
			'event_category'=>$category, 		
			'event_link_expires'=>$expires, 				
			'event_zoom'=>$event_zoom,
			'event_open'=>$event_open,
			'event_host'=>$host,
			'event_span'=>$event_span,
		// floats
			'event_longitude'=>$event_longitude,
			'event_latitude'=>$event_latitude			
			);
		if ($action == 'edit') { unset( $submit['event_author'] ); }
	} else {
	    // The form is going to be rejected due to field validation issues, so we preserve the users entries here
		$users_entries->event_title = $title;
		$users_entries->event_desc = $desc;
		$users_entries->event_host = $host;
		$users_entries->event_category = $category;
		$users_entries->event_link = $linky;
		$users_entries->event_link_expires = $expires;
		$users_entries->event_label = $event_label;
		$users_entries->event_street = $event_street;
		$users_entries->event_street2 = $event_street2;
		$users_entries->event_city = $event_city;
		$users_entries->event_state = $event_state;
		$users_entries->event_postcode = $event_postcode;
		$users_entries->event_country = $event_country;	
		$users_entries->event_region = $event_region;
		$users_entries->event_url = $event_url;
		$users_entries->event_longitude = $event_longitude;		
		$users_entries->event_latitude = $event_latitude;		
		$users_entries->event_zoom = $event_zoom;
		$users_entries->event_phone = $event_phone;
		$users_entries->event_open = $event_open;
		$users_entries->event_short = $short;
		$users_entries->event_image = $event_image;
		$users_entries->event_span = $event_span;
		$proceed = false;
	}
	$data = array($proceed, $users_entries, $submit,$errors);
	return $data;
}


// Used on the manage events admin page to display a list of events
function jd_groups_display_list() {
	global $wpdb;
	$mcdb = $wpdb;
	
		$sortby = ( isset( $_GET['sort'] ) )?(int) $_GET['sort']:get_option('mc_default_sort');

		if ( isset( $_GET['order'] ) ) {
			$sortdir = ( isset($_GET['order']) && $_GET['order'] == 'ASC' )?'ASC':'default';
		} else {
			$sortdir = 'default';
		}
	
	if ( empty($sortby) ) {
		$sortbyvalue = 'event_begin';
	} else {
		switch ($sortby) {
		    case 1:$sortbyvalue = 'event_ID';
			break;
			case 2:$sortbyvalue = 'event_title';
			break;
			case 3:$sortbyvalue = 'event_desc';
			break;
			case 4:$sortbyvalue = 'event_begin';
			break;
			case 5:$sortbyvalue = 'event_author';
			break;
			case 6:$sortbyvalue = 'event_category';
			break;
			case 7:$sortbyvalue = 'event_label';
			break;
			case 8:$sortbyvalue = 'group_id';
			break;
			default:$sortbyvalue = 'event_begin';
		}
	}
	if ($sortdir == 'default') {
		$sortbydirection = 'DESC';
	} else {
		$sortbydirection = $sortdir;
	}
	
	if ($sortbydirection == 'DESC') {
		$sorting = "&amp;order=ASC";
	} else {
		$sorting = '';
	}
	$limit = ( isset($_GET['limit']) )?$_GET['limit']:'all';
	switch ( $limit ) {
		case 'all':$limit = '';break;
		case 'grouped':$limit = 'WHERE event_group_id <> 0';break;
		case 'ungrouped':$limit = 'WHERE event_group_id = 0';break;
		default:$limit = '';
	}
	$events = $mcdb->get_results("SELECT * FROM " . my_calendar_table() . " $limit ORDER BY $sortbyvalue $sortbydirection");
	?>
	<h2 class="mc-clear"><?php _e('Create/Modify Groups','my-calendar'); ?></h2>
	<?php if ( get_option('mc_event_approve') == 'true' ) { ?>
		<ul class="links">
		<li><a <?php echo ( isset($_GET['limit']) && $_GET['limit']=='groupeed' )?' class="active-link"':''; ?> href="<?php echo admin_url('admin.php?page=my-calendar-groups&amp;limit=grouped#my-calendar-admin-table'); ?>"><?php _e('Grouped Events','my-calendar'); ?></a></li>
		<li><a <?php echo ( isset($_GET['limit']) && $_GET['limit']=='ungrouped')?' class="active-link"':''; ?>  href="<?php echo admin_url('admin.php?page=my-calendar-groups&amp;limit=ungrouped#my-calendar-admin-table'); ?>"><?php _e('Ungrouped Events','my-calendar'); ?></a></li> 
		<li><a <?php echo ( isset($_GET['limit']) && $_GET['limit']=='all' || !isset($_GET['limit']))?' class="active-link"':''; ?>  href="<?php echo admin_url('admin.php?page=my-calendar-groups#my-calendar-admin-table'); ?>"><?php _e('All','my-calendar'); ?></a></li>
		</ul>
	<?php } ?>
	
	<p><?php _e('Check a set of events to group them for mass editing.','my-calendar'); ?></p>
	<?php
	if ( !empty($events) ) {
		?>
		<form action="<?php echo admin_url("admin.php?page=my-calendar-groups"); ?>" method="post">
		<div>
		<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" />
		<input type="hidden" name="event_action" value="group" />
		</div>
		<p style="position:relative;">
		<input type="submit" class="button-primary group" value="<?php _e('Group checked events for mass editing','my-calendar'); ?>" />
		</p>		
<table class="widefat page fixed" id="my-calendar-admin-table">
	<thead>
	<tr>
		<th class="manage-column" scope="col"><a href="<?php echo admin_url("admin.php?page=my-calendar-groups&amp;sort=1$sorting"); ?>"><?php _e('ID','my-calendar') ?></a></th>
		<th class="manage-column" scope="col"><a href="<?php echo admin_url("admin.php?page=my-calendar-groups&amp;sort=8$sorting"); ?>"><?php _e('Group','my-calendar') ?></a></th>
		<th class="manage-column" scope="col"><a href="<?php echo admin_url("admin.php?page=my-calendar-groups&amp;sort=2$sorting"); ?>"><?php _e('Title','my-calendar') ?></a></th>
		<th class="manage-column" scope="col"><a href="<?php echo admin_url("admin.php?page=my-calendar-groups&amp;sort=7$sorting"); ?>"><?php _e('Where','my-calendar') ?></a></th>
		<th class="manage-column" scope="col"><a href="<?php echo admin_url("admin.php?page=my-calendar-groups&amp;sort=3$sorting"); ?>"><?php _e('Description','my-calendar') ?></a></th>
		<th class="manage-column" scope="col"><a href="<?php echo admin_url("admin.php?page=my-calendar-groups&amp;sort=4$sorting"); ?>"><?php _e('Starts','my-calendar') ?></a></th>
		<th class="manage-column" scope="col"><?php _e('Recurs','my-calendar') ?></th>
		<th class="manage-column" scope="col"><a href="<?php echo admin_url("admin.php?page=my-calendar-groups&amp;sort=5$sorting"); ?>"><?php _e('Author','my-calendar') ?></a></th>
		<th class="manage-column" scope="col"><a href="<?php echo admin_url("admin.php?page=my-calendar-groups&amp;sort=6$sorting"); ?>"><?php _e('Category','my-calendar') ?></a></th>
		<th class="manage-column" scope="col"><?php _e('Edit','my-calendar') ?></th>
	</tr>
	</thead>
		<?php
		$class = '';
		$sql = "SELECT * FROM " . my_calendar_categories_table() ;
        $categories = $mcdb->get_results($sql);
			
		foreach ( $events as $event ) {
			$class = ($class == 'alternate') ? '' : 'alternate';
			$spam = ($event->event_flagged == 1) ? ' spam' : '';
			$spam_label = ($event->event_flagged == 1) ? '<strong>Possible spam:</strong> ' : '';
			$author = ( $event->event_author != 0 )?get_userdata($event->event_author):'Public Submitter';
			if ($event->event_link != '') { 
			$title = "<a href='".esc_attr($event->event_link)."'>$event->event_title</a>";
			} else {
			$title = $event->event_title;
			}
			?>
			<tr class="<?php echo $class; echo $spam; ?>" id="event<?php echo $event->event_id; ?>">
				<th scope="row"><input type="checkbox" value="<?php echo $event->event_id; ?>" name="group[]" id="mc<?php echo $event->event_id; ?>" <?php echo (mc_event_is_grouped( $event->event_group_id ))?' disabled="disabled"':''; ?> /> <label for="mc<?php echo $event->event_id; ?>"><?php echo $event->event_id; ?></label></th>
				<th scope="row"><?php echo ($event->event_group_id == 0)?'-':$event->event_group_id; ?></th>
				<td><?php echo $spam_label; echo stripslashes($title); ?></td>
				<td><?php echo stripslashes($event->event_label); ?></td>
				<td><?php echo substr(strip_tags(stripslashes($event->event_desc)),0,60); ?>&hellip;</td>
				<?php if ($event->event_time != "00:00:00") { $eventTime = date_i18n(get_option('mc_time_format'), strtotime($event->event_time)); } else { $eventTime = get_option('mc_notime_text'); } ?>
				<td><?php echo "$event->event_begin, $eventTime"; ?></td>
				<?php /* <td><?php echo $event->event_end; ?></td> */ ?>
				<td>
				<?php 
					// Interpret the DB values into something human readable
					if ($event->event_recur == 'S') { _e('Never','my-calendar'); } 
					else if ($event->event_recur == 'D') { _e('Daily','my-calendar'); }
					else if ($event->event_recur == 'E') { _e('Weekdays','my-calendar'); }
					else if ($event->event_recur == 'W') { _e('Weekly','my-calendar'); }
					else if ($event->event_recur == 'B') { _e('Bi-Weekly','my-calendar'); }
					else if ($event->event_recur == 'M') { _e('Monthly (by date)','my-calendar'); }
					else if ($event->event_recur == 'U') { _e('Monthly (by day)','my-calendar'); }
					else if ($event->event_recur == 'Y') { _e('Yearly','my-calendar'); }
				?>&ndash;<?php
					if ($event->event_recur == 'S') { _e('N/A','my-calendar'); }
					else if ( mc_event_repeats_forever( $event->event_recur, $event->event_repeats ) ) { _e('Forever','my-calendar'); }
					else if ( $event->event_repeats > 0 ) { printf(__('%d Times','my-calendar'),$event->event_repeats ); }					
				?>				
				</td>
				<td><?php echo ( is_object($author) )?$author->display_name:$author; ?></td>
                                <?php
								$this_category = $event->event_category;
								foreach ($categories as $key=>$value) {
									if ($value->category_id == $this_category) {
										$this_cat = $categories[$key];
									} 
								}
                                ?>
				<td><div class="category-color" style="background-color:<?php echo (strpos($this_cat->category_color,'#') !== 0)?'#':''; echo $this_cat->category_color;?>;"> </div> <?php echo stripslashes($this_cat->category_name); ?></td>
				<?php unset($this_cat); ?>
				<td>
				<?php if ( mc_can_edit_event( $event->event_author ) ) { ?>
				<a href="<?php echo admin_url("admin.php?page=my-calendar&amp;mode=edit&amp;event_id=$event->event_id"); ?>" class='edit'><?php _e('Edit Event','my-calendar'); ?></a> &middot; 
					<?php if ( mc_event_is_grouped( $event->event_group_id ) ) { ?>
					<a href="<?php echo admin_url("admin.php?page=my-calendar-groups&amp;mode=edit&amp;event_id=$event->event_id&amp;group_id=$event->event_group_id"); ?>" class='edit group'><?php _e('Edit Group','my-calendar'); ?></a>
					<?php } else { ?>
					<em><?php _e('Ungrouped','my-calendar'); ?></em>
					<?php } ?>
				<?php } else { _e("Not editable.",'my-calendar'); } ?>				
				</td>	
			</tr>
<?php
		}
?>
		</table>
		<p>
		<input type="submit" class="button-secondary group" value="<?php _e('Group checked events for mass editing','my-calendar'); ?>" />
		</p>
		</form>
<?php
	} else {
?>
		<p><?php _e("There are no events in the database!",'my-calendar') ?></p>
<?php	
	}
}


?>