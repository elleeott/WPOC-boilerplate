<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'my-calendar-event-manager.php' == basename($_SERVER['SCRIPT_FILENAME'])) {
	die ('Please do not load this page directly. Thanks!');
}

function edit_my_calendar() {
    global $current_user, $wpdb, $users_entries;
	$mcdb = $wpdb;
	if ( get_option('ko_calendar_imported') != 'true' ) {  
		if (function_exists('check_calendar')) {
		echo "<div id='message' class='updated'>";
		echo "<p>";
		_e('My Calendar has identified that you have the Calendar plugin by Kieran O\'Shea installed. You can import those events and categories into the My Calendar database. Would you like to import these events?','my-calendar');
		echo "</p>";
		?>
			<form method="post" action="<?php echo admin_url('admin.php?page=my-calendar-config'); ?>">
			<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" /></div>
			<div>			
			<input type="hidden" name="import" value="true" />
			<input type="submit" value="<?php _e('Import from Calendar','my-calendar'); ?>" name="import-calendar" class="button-primary" />
			</div>
			</form>
		<?php
		echo "<p>";
		_e('Although it is possible that this import could fail to import your events correctly, it should not have any impact on your existing Calendar database. If you encounter any problems, <a href="http://www.joedolson.com/contact.php">please contact me</a>!','my-calendar');
		echo "</p>";
		echo "</div>";
		}
	}

// First some quick cleaning up 
$edit = $create = $save = $delete = false;

$action = !empty($_POST['event_action']) ? $_POST['event_action'] : '';
$event_id = !empty($_POST['event_id']) ? $_POST['event_id'] : '';

if ( isset( $_GET['mode'] ) ) {
	$action = $_GET['mode'];
	if ( $action == 'edit' || $action == 'copy' ) {
		$event_id = (int) $_GET['event_id'];
	}
}

// Check whether My Calendar is up to date and installed.
check_my_calendar();

if ( !empty($_POST['mass_edit']) && isset($_POST['mass_delete']) ) {
	$nonce=$_REQUEST['_wpnonce'];
    if (! wp_verify_nonce($nonce,'my-calendar-nonce') ) die("Security check failed");
	$events = $_POST['mass_edit'];
	$sql = 'DELETE FROM ' . my_calendar_table() . ' WHERE event_id IN (';	
	$i=0;
	$deleted = array();
	foreach ($events as $value) {
		$value = (int) $value;
		$ea = "SELECT event_author FROM " . my_calendar_table() . " WHERE event_id = $value";
		$result = $mcdb->get_results( $ea, ARRAY_A );
		$total = count($events);	
		if ( mc_can_edit_event( $result[0]['event_author'] ) ) {
			$delete_occurrences = "DELETE FROM ".my_calendar_event_table()." WHERE occur_event_id = $value";
			$delete = $mcdb->query($delete_occurrences);
			$sql .= mysql_real_escape_string($value).',';
			$deleted[] = $value;
			$i++;
		}
	}
	$sql = substr( $sql, 0, -1 );
	$sql .= ')';
	$result = $mcdb->query($sql);
	if ( $result !== 0 && $result !== false ) {
		mc_delete_cache();
		// argument: array of event IDs
		do_action( 'mc_mass_delete_events', $deleted );		
		$message = "<div class='updated'><p>".sprintf(__('%1$d events deleted successfully out of %2$d selected','my-calendar'), $i, $total )."</p></div>";
	} else {
		$message = "<div class='error'><p><strong>".__('Error','my-calendar').":</strong>".__('Your events have not been deleted. Please investigate.','my-calendar')."</p></div>";
	}
	echo $message;
}

if ( !empty($_POST['mass_edit']) && isset($_POST['mass_approve']) ) {
	$nonce=$_REQUEST['_wpnonce'];
    if (! wp_verify_nonce($nonce,'my-calendar-nonce') ) die("Security check failed");
	$events = $_POST['mass_edit'];
	$sql = 'UPDATE ' . my_calendar_table() . ' SET event_approved = 1 WHERE event_id IN (';	
	$i=0;
	$approved = array();
	foreach ($events as $value) {
		$value = (int) $value;
		$total = count($events);	
		if ( current_user_can('mc_approve_events') ) {
			$sql .= mysql_real_escape_string($value).',';
			$approved[] = $value;
			$i++;
		}
	}
	$sql = substr( $sql, 0, -1 );
	$sql .= ')';
	$result = $mcdb->query($sql);
	if ( $result !== 0 && $result !== false ) {
		mc_delete_cache();
		// argument: array of event IDs
		do_action( 'mc_mass_approve_events', $approved );		
		$message = "<div class='updated'><p>".sprintf(__('%1$d events approved successfully out of %2$d selected','my-calendar'), $i, $total )."</p></div>";
	} else {
		$message = "<div class='error'><p><strong>".__('Error','my-calendar').":</strong>".__('Your events have not been approved. Please investigate.','my-calendar')."</p></div>";
	}
	echo $message;
}

if ( isset( $_GET['mode'] ) && $_GET['mode'] == 'delete' ) { 
	    $sql = "SELECT event_title, event_author FROM " . my_calendar_table() . " WHERE event_id=" . (int) $_GET['event_id'];
	   $result = $mcdb->get_results( $sql, ARRAY_A );
	if ( mc_can_edit_event( $result[0]['event_author'] ) ) {
		if ( isset( $_GET['date'] ) ) {
			$event_instance = (int) $_GET['date'];
			$sql = "SELECT occur_begin FROM " . my_calendar_event_table() . " WHERE occur_id=" . $event_instance;
			$inst = $mcdb->get_var( $sql );
			$instance_date = ' ('.date('Y-m-d',strtotime($inst) ).')';
		} else {
			$instance_date = '';
		}
	?>
		<div class="error">
		<form action="<?php echo admin_url('admin.php?page=my-calendar'); ?>" method="post">
		<p><strong><?php _e('Delete Event','my-calendar'); ?>:</strong> <?php _e('Are you sure you want to delete this event?','my-calendar'); ?>		
		<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" />		
		<input type="hidden" value="delete" name="event_action" />
		<?php if ( !empty( $_GET['date'] ) ) { ?> 
			<input type="hidden" name="event_instance" value="<?php echo (int) $_GET['date']; ?>" />
		<?php } ?>
		<input type="hidden" value="<?php echo (int) $_GET['event_id']; ?>" name="event_id" />
		<input type="submit" name="submit" class="button-secondary delete" value="<?php _e('Delete','my-calendar'); echo " &quot;".stripslashes( $result[0]['event_title'] )."&quot;$instance_date"; ?>" />
		</form></p>
		</div>
<?php } else { ?>
		<div class="error">
		<p><strong><?php _e( 'You do not have permission to delete that event.','my-calendar' ); ?></strong></p>
		</div>
<?php }
}

// Approve and show an Event ...originally by Roland
if ( isset( $_GET['mode'] ) && $_GET['mode'] == 'approve' ) {
	if ( current_user_can( 'mc_approve_events' ) ) {
	    $sql = "UPDATE " . my_calendar_table() . " SET event_approved = 1 WHERE event_id=" . (int) $_GET['event_id'];
		$result = $mcdb->get_results( $sql, ARRAY_A );
		mc_delete_cache();
	} else {
	?>
		<div class="error">
		<p><strong><?php _e('You do not have permission to approve that event.','my-calendar'); ?></strong></p>
		</div>
	<?php
	}
}

// Reject and hide an Event ...by Roland
if ( isset( $_GET['mode'] ) && $_GET['mode'] == 'reject' ) {
	if ( current_user_can( 'mc_approve_events' ) ) {
	    $sql = "UPDATE " . my_calendar_table() . " SET event_approved = 2 WHERE event_id=" . (int) $_GET['event_id'];
		$result = $mcdb->get_results( $sql, ARRAY_A );
		mc_delete_cache();
	} else {
	?>
		<div class="error">
		<p><strong><?php _e('You do not have permission to reject that event.','my-calendar'); ?></strong></p>
		</div>
	<?php
	}
}

if ( isset( $_POST['event_action'] ) ) {
	$nonce=$_REQUEST['_wpnonce'];
    if (! wp_verify_nonce($nonce,'my-calendar-nonce') ) die("Security check failed");
	$proceed = false;
	global $mc_output;
	$count = 0;
	if ( isset($_POST['event_begin']) && is_array( $_POST['event_begin'] ) ) {
		$count = count($_POST['event_begin']);
	} else {
		$response = my_calendar_save($action,$mc_output,(int) $_POST['event_id']);
		echo $response;
	}
	for ($i=0;$i<$count;$i++) {
	$mc_output = mc_check_data($action,$_POST, $i);
		if ($action == 'add' || $action == 'copy' ) {
			$response = my_calendar_save($action,$mc_output);
		} else {
			$response = my_calendar_save($action,$mc_output,(int) $_POST['event_id']);		
		}
		echo $response;
	}
}

?>

<div class="wrap jd-my-calendar">

<?php my_calendar_check_db();?>
<?php 
if ( get_site_option('mc_multisite') == 2 ) { 
	if ( get_option('mc_current_table') == 0 ) {
		$message = __('Currently editing your local calendar','my-calendar');
	} else {
		$message = __('Currently editing your central calendar','my-calendar');
	}
	echo "<div class='message updated'><p>$message</p></div>";
} ?>
	<?php
	if ( $action == 'edit' || ($action == 'edit' && $error_with_saving == 1) ) {
		?>
<div id="icon-edit" class="icon32"></div>		
		<h2><?php _e('Edit Event','my-calendar'); ?></h2>
		<?php
		if ( empty($event_id) ) {
			echo "<div class=\"error\"><p>".__("You must provide an event id in order to edit it",'my-calendar')."</p></div>";
		} else {
			jd_events_edit_form('edit', $event_id);
		}		
	} else if ( $action == 'copy' || ($action == 'copy' && $error_with_saving == 1)) { ?>
<div id="icon-edit" class="icon32"></div>	
		<h2><?php _e('Copy Event','my-calendar'); ?></h2>
		<?php
		if ( empty($event_id) ) {
			echo "<div class=\"error\"><p>".__("You must provide an event id in order to edit it",'my-calendar')."</p></div>";
		} else {
			jd_events_edit_form('copy', $event_id);
		}
	} else {
	?>	
<div id="icon-edit" class="icon32"></div>	
		<h2><?php _e('Add Event','my-calendar'); ?></h2>
		<?php jd_events_edit_form(); ?>
	<?php } ?>
		<?php jd_show_support_box(); ?>
		<?php jd_events_display_list(); ?>
</div>
		<?php
} 

function my_calendar_save( $action,$output,$event_id=false ) {

global $wpdb,$event_author;
	$mcdb = $wpdb;
	$proceed = $output[0];
	$message = '';
	$formats = array( 
					'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',
					'%d','%d','%d','%d','%d','%d','%d','%d','%d','%d','%d','%d','%d','%d','%d',
					'%f','%f'
					);	
	if ( ( $action == 'add' || $action == 'copy' ) && $proceed == true ) {
		$add = $output[2]; // add format here
		$result = $mcdb->insert( 
				my_calendar_table(), 
				$add, 
				$formats 
				);
		$new_event = $mcdb->insert_id;
		// create all occurrences of event
		$occurrences = mc_increment_event( $new_event );			
		if ( !$result ) {
			$message = "<div class='error notice'><p><strong>". __('Error','my-calendar') .":</strong> ". __('I\'m sorry! I couldn\'t add that event to the database.','my-calendar') . "</p></div>";	      
		} else {
			// do an action using the $action and processed event data
			do_action( 'mc_save_event', $action, $add );				
			// Call mail function
			if ( get_option('mc_event_mail') == 'true' ) {				
				$event = mc_get_event( $mcdb->insert_id ); // insert_id is last occurrence inserted in the db
				my_calendar_send_email( $event );
			}
			if ( $add['event_approved'] == 0 ) {
				$message = "<div class='updated notice'><p>".__('Event saved. An administrator will review and approve your event.','my-calendar')."</p></div>";
			} else {
				$message = "<div class='updated notice'><p>". __('Event added. It will now show in your calendar.','my-calendar') . "</p></div>";
			}
			mc_delete_cache();
		}
	}
	if ( $action == 'edit' && $proceed == true ) {
	$url = ( get_option('mc_uri') != '' )?' '.sprintf(__('View <a href="%s">your calendar</a>.','my-calendar'),get_option('mc_uri') ):'';	
		$event_author = (int) ($_POST['event_author']);
		if ( mc_can_edit_event( $event_author ) ) {	
			$update = $output[2];
			$date_changed = ( 
					$update['event_begin'] != $_POST['prev_event_begin'] || 
					$update['event_time'] != $_POST['prev_event_time'] || 
					$update['event_end'] != $_POST['prev_event_end'] || 
					( $update['event_endtime'] != $_POST['prev_event_endtime'] && ( $_POST['prev_event_endtime'] != '' && $update['event_endtime']!= '00:00:00' ) ) )
					?true:false;
			if ( isset($_POST['event_instance']) ) {
				$is_changed = mc_compare( $update, $event_id );// compares the information sent to the information saved for a given event.
				$event_instance = (int) $_POST['event_instance'];				
				if ( $is_changed ) {
				// if anything changed, create new event record, match group id, update instance to reflect new event connection -- but same group id.
				// if group ID == 0, need to add group ID to both records.
				if ( $update['event_group_id'] == 0 ) { $update['event_group_id'] = $event_id; mc_update_data( $event_id, 'event_group_id', $event_id ); }
					$result = $mcdb->insert( 
							my_calendar_table(), 
							$update, 
							$formats
							);
					$new_event = $mcdb->insert_id; // need to get this variable into URL for form submit
					$result = mc_update_instance( $event_instance, $new_event );
					mc_delete_cache();
				} else {
					if ( $update['event_begin'][0] == $_POST['prev_event_begin'] && $update['event_end'][0] == $_POST['prev_event_end'] ) {
					// There were no changes at all.
						$message = "<div class='updated notice'><p>".__('Nothing was changed in that update.','my-calendar')."$url</p></div>";					
					} else {
						$result = mc_update_instance( $event_instance, $event_id, $update );		
						// Only dates were changed
						$message = "<div class='updated notice'><p>".__('Date/time information for this event has been updated.','my-calendar')."$url</p></div>";					
						mc_delete_cache();
					}
				}
			} else {
				//$mcdb->show_errors();
				$result = $mcdb->update( 
						my_calendar_table(),
						$update,
						array( 'event_id'=>$event_id ),
						$formats,
						'%d' );
				$recur_changed = ( $update['event_repeats'] != $_POST['prev_event_repeats'] || $update['event_recur'] != $_POST['prev_event_recur'] )?true:false;
				if ( $date_changed || $recur_changed ) {
					$delete_instances = mc_delete_instances( $event_id );
					$edit_instances = mc_increment_event( $event_id );	
					mc_delete_cache();
				}
			}
			//$mcdb->print_error();
				if ( $result === false ) {
					$message = "<div class='error'><p><strong>".__('Error','my-calendar').":</strong>".__('Your event was not updated.','my-calendar')."$url</p></div>";
				} else if ( $result === 0 ) {
					$message = "<div class='updated'><p>".__('Nothing was changed in that update.','my-calendar')."$url</p></div>";
				} else {
					// do an action using the $action and processed event data
					do_action( 'mc_save_event', $action, $update );	
					$message = "<div class='updated'><p>".__('Event updated successfully','my-calendar').".$url</p></div>";
					mc_delete_cache();
				}
		} else {
			$message = "<div class='error'><p><strong>".__('You do not have sufficient permissions to edit that event.','my-calendar')."</strong></p></div>";
		}			
	}

	if ( $action == 'delete' ) {
// Deal with deleting an event from the database
		if ( empty($event_id) )	{
			$message = "<div class='error'><p><strong>".__('Error','my-calendar').":</strong>".__("You can't delete an event if you haven't submitted an event id",'my-calendar')."</p></div>";
		} else {
			if ( empty( $_POST['event_instance'] ) ) {
				$sql = "DELETE FROM " . my_calendar_table() . " WHERE event_id='" . (int) $event_id . "'";
				$delete_occurrences = "DELETE FROM ".my_calendar_event_table()." WHERE occur_event_id = ".(int) $event_id;
				$delete = $mcdb->query($delete_occurrences);			
				$mcdb->query($sql);
				$sql = "SELECT event_id FROM " . my_calendar_table() . " WHERE event_id='" . (int) $event_id . "'";
				$result = $mcdb->get_results($sql);
			} else {
				$delete = "DELETE FROM " . my_calendar_event_table(). " WHERE occur_id = ".(int) $_POST['event_instance'];
				$result = $mcdb->get_results($delete);
			}
			if ( empty($result) || empty($result[0]->event_id) ) {
				mc_delete_cache();
				// do an action using the event_id
				do_action( 'mc_delete_event', $event_id );				
				return "<div class='updated'><p>".__('Event deleted successfully','my-calendar')."</p></div>";
			} else {
				$message = "<div class='error'><p><strong>".__('Error','my-calendar').":</strong>".__('Despite issuing a request to delete, the event still remains in the database. Please investigate.','my-calendar')."</p></div>";
			}	
		}
	}
	$message = $message ."\n". $output[3];
	return $message;
}

function jd_acquire_form_data($event_id=false) {
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
		// Recover users entries if there was an error
		if (!empty($users_entries)) {
		    $data = $users_entries;
		}
	} else {
	  // Deal with possibility that form was submitted but not saved due to error - recover user's entries here
	  $data = $users_entries;
	}
	return $data;

}

// The event edit form for the manage events admin page
function jd_events_edit_form($mode='add', $event_id=false) {
	global $wpdb,$users_entries,$output;
	$mcdb = $wpdb;
	if ($event_id != false) {
		$data = jd_acquire_form_data($event_id);
	} else {
		$data = $users_entries;
	}
	if ( is_object($data) && $data->event_approved != 1 && $mode == 'edit' ) {
		$message = __('This event must be approved in order for it to appear on the calendar.','my-calendar');
	} else {
		$message = "";
	}
	echo ($message != '')?"<div class='error'><p>$message</p></div>":'';
	?>
	<?php my_calendar_print_form_fields($data,$mode,$event_id); ?>

<?php
}

function mc_get_instance_data( $instance_id ) {
	global $wpdb;
	$mcdb = $wpdb; 
	$result = $wpdb->get_row("SELECT * FROM ".my_calendar_event_table()." WHERE occur_id = $instance_id");
	return $result;
}

function my_calendar_print_form_fields( $data,$mode,$event_id ) {
	global $wpdb, $user_ID;
	$mcdb = $wpdb;
	$mc_input_administrator = (get_option('mc_input_options_administrators')=='true' && current_user_can('manage_options'))?true:false;
	$mc_input = get_option('mc_input_options');
	
	$instance = ( isset($_GET['date'] ) )?(int) $_GET['date']:false;
	if ( $instance ) { $ins = mc_get_instance_data( $instance ); $event_id = $ins->occur_event_id; $data = mc_get_event_core( $event_id );}
	?>

<div class="postbox-container" style="width: 70%">
<div class="metabox-holder">

<?php if ( $mode == 'add' || $mode == 'copy' ) { $edit_args = ''; } else {
	$edit_args = "&amp;mode=$mode&amp;event_id=$event_id";
	if ( $instance ) { $edit_args .= "&amp;date=$instance"; }
} 
?>
<form id="my-calendar" method="post" action="<?php echo admin_url('admin.php?page=my-calendar').$edit_args; ?>">

<div>
<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" />
<input type="hidden" name="event_group_id" value="<?php if ( !empty( $data->event_group_id ) && $mode != 'copy' ) { echo $data->event_group_id; } else { echo mc_group_id(); } ?>" />
<input type="hidden" name="event_action" value="<?php echo $mode; ?>" />
<input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
<?php if ( $mode != 'edit' ) { ?>
<input type="hidden" name="event_author" value="<?php echo $user_ID; ?>" />
<?php } else { ?>
<input type="hidden" name="event_author" value="<?php echo $data->event_author; ?>" />
<?php } ?>
<input type="hidden" name="event_nonce_name" value="<?php echo wp_create_nonce('event_nonce'); ?>" />
</div>

<div class="ui-sortable meta-box-sortables">
<div class="postbox">	
	<h3><?php _e('Add/Edit Event','my-calendar'); ?> <small>(<a href="#mc-manage"><?php _e('Edit events','my-calendar'); ?>)</a></small></h3>
	<div class="inside">
			<p>
                <input type="submit" name="save" class="button-primary" value="<?php _e('Save Event','my-calendar'); ?>" />
			</p>	
<?php
	if ( !empty( $_GET['date'] ) && $data->event_recur != 'S' ) {
		$event = mc_get_event( $instance );
		$date = date_i18n( get_option('mc_date_format'),strtotime( $event->occur_begin ) );
		$message = __("You are editing the <strong>$date</strong> instance of this event. Other instances of this event will not be changed.",'my-calendar');
		echo "<div><input type='hidden' name='event_instance' value='$instance' /></div>";
		echo "<div class='message updated'><p>$message</p></div>";
	} else if ( isset( $_GET['date'] ) && empty( $_GET['date'] ) ) {
		echo "<div class='message updated'><p>".__('There was an error acquiring information about this event instance. The ID for this event instance was not provided. <strong>You are editing this entire recurrence set.</strong>','my-calendar')."</p></div>";
	}
?>
        <fieldset>
		<legend><?php _e('Enter your Event Information','my-calendar'); ?></legend>
		<p>
		<label for="event_title"><?php _e('Event Title','my-calendar'); ?> <span><?php _e('(required)','my-calendar'); ?></span></label><br /><input type="text" id="event_title" name="event_title" class="input" size="60" value="<?php if ( !empty($data) ) echo stripslashes(esc_attr($data->event_title)); ?>" />
<?php if ( $mode == 'edit' ) { ?>
	<?php if ( get_option( 'mc_event_approve' ) == 'true' ) { ?>
		<?php if ( current_user_can( 'mc_approve_events' ) ) { // (Added by Roland P. ?>
				<input type="checkbox" value="1" id="event_approved" name="event_approved"<?php if ( !empty($data) && $data->event_approved == '1' ) { echo " checked=\"checked\""; } else if ( !empty($data) && $data->event_approved == '0' ) { echo ""; } else if ( get_option( 'mc_event_approve' ) == 'true' ) { echo "checked=\"checked\""; } ?> /> <label for="event_approved"><?php _e('Publish','my-calendar'); ?><?php if ($data->event_approved != 1) { ?> <small>[<?php _e('You must approve this event to promote it to the calendar.','my-calendar'); ?>]</small> <?php } ?></label>
		<?php } else { // case: editing, approval enabled, user cannot approve ?>
				<input type="hidden" value="0" name="event_approved" /><?php _e('An administrator must approve your new event.','my-calendar'); ?>
		<?php } ?> 
	<?php } else { // Case: editing, approval system is disabled - auto approve ?>	
				<input type="hidden" value="1" name="event_approved" />
	<?php } ?>
<?php } else { // case: adding new event (if use can, then 1, else 0) ?>
<?php if ( current_user_can( 'mc_approve_events' ) ) { $dvalue = 1; } else { $dvalue = 0; } ?>
			<input type="hidden" value="<?php echo $dvalue; ?>" name="event_approved" />
<?php } ?>
		</p>
		<?php if (  is_object($data) && $data->event_flagged == 1 ) { ?>
		<div class="error">
		<p>
		<input type="checkbox" value="0" id="event_flagged" name="event_flagged"<?php if ( !empty($data) && $data->event_flagged == '0' ) { echo " checked=\"checked\""; } else if ( !empty($data) && $data->event_flagged == '1' ) { echo ""; } ?> /> <label for="event_flagged"><?php _e('This event is not spam','my-calendar'); ?></label>
		</p>
		</div>
		<?php } ?>
		<?php if ($mc_input['event_desc'] == 'on' || $mc_input_administrator ) { ?>
		<div class="event_description">
		<?php if ( !empty($data) ) { $description = $data->event_desc; } else { $description = ''; } ?>
		<label for="content"><?php _e('Event Description (<abbr title="hypertext markup language">HTML</abbr> allowed)','my-calendar'); ?></label><br />
		<?php 
		if ( $mc_input['event_use_editor'] == 'on' ) {  
			if ( version_compare( get_bloginfo( 'version' ) , '3.3' , '>=' ) ) {
				wp_editor( stripslashes($description), 'content', array( 'textarea_rows'=>10 ) ); 
			} else { 
				the_editor( stripslashes($description) ); 
			} 
		} else {
			?><textarea id="content" name="content" class="event_desc" rows="5" cols="80"><?php echo stripslashes(esc_attr($description)); ?></textarea>
			<?php if ( $mc_input['event_use_editor'] == 'on' ) { ?></div><?php } 
		} ?>
		</div>
		<?php } ?>
		<?php
		// If the editor is enabled, shouldn't display the image uploader. 
		// It restricts use of the image uploader to a single image and forces it to be in 
		// the event image field, rather than the event description.
		if ( !isset($mc_input['event_image']) ) { $mc_input['event_image'] = 'off'; }	
		if ( ( $mc_input['event_image'] == 'on' ) || $mc_input_administrator ) { ?>
		<p>
		<?php if ( !empty($data->event_image) ) { ?>
		<div class="event_image"><?php _e("This event's image:",'my-calendar'); ?><br /><img src="<?php if ( !empty($data) ) echo esc_attr($data->event_image); ?>" alt="" /></div>
		<?php } ?>
		<label for="event_image"><?php _e("Add an image:",'my-calendar'); ?></label> <input type="text" name="event_image" id="event_image" size="60" value="<?php if ( !empty($data) ) echo esc_attr($data->event_image); ?>" /> 
			<?php if ( $mc_input['event_use_editor'] == 'on' ) { ?>
				<?php echo " "; _e('(URL to Event image)','my-calendar'); ?>
			<?php } else { ?>
		<input id="upload_image_button" type="button" class="button" value="<?php _e('Upload Image','my-calendar'); ?>" /><br /><?php _e('Include your image URL or upload an image.','my-calendar'); ?>
			<?php } ?>
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
		<label for="event_short"><?php _e('Event Short Description (<abbr title="hypertext markup language">HTML</abbr> allowed)','my-calendar'); ?></label><br /><textarea id="event_short" name="event_short" class="input" rows="2" cols="80"><?php if ( !empty($data) ) echo stripslashes(esc_attr($data->event_short)); ?></textarea>
		</p>
		<?php } ?>
	<p>
	<label for="event_host"><?php _e('Event Host','my-calendar'); ?></label>
	<select id="event_host" name="event_host">
		<?php 
			 // Grab all the categories and list them
			$users = my_calendar_getUsers();				 
			foreach($users as $u) {
			 echo '<option value="'.$u->ID.'"';
					if (  is_object($data) && $data->event_host == $u->ID ) {
					 echo ' selected="selected"';
					} else if(  is_object($u) && $u->ID == $user_ID && empty($data->event_host) ) {
				    echo ' selected="selected"';
					}
				echo '>'.$u->display_name."</option>\n";
			}
		?>
	</select>
	</p>			
		<?php if ($mc_input['event_category'] == 'on' || $mc_input_administrator ) { ?>
        <p>
		<label for="event_category"><?php _e('Event Category','my-calendar'); ?></label>
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
			<label for="event_link"><?php _e('Event Link (Optional)','my-calendar'); ?></label> <input type="text" id="event_link" name="event_link" class="input" size="40" value="<?php if ( !empty($data) ) { echo esc_url($data->event_link); } ?>" /> <input type="checkbox" value="1" id="event_link_expires" name="event_link_expires"<?php if ( !empty($data) && $data->event_link_expires == '1' ) { echo " checked=\"checked\""; } else if ( !empty($data) && $data->event_link_expires == '0' ) { echo ""; } else if ( get_option( 'mc_event_link_expires' ) == 'true' ) { echo " checked=\"checked\""; } ?> /> <label for="event_link_expires"><?php _e('This link will expire when the event passes.','my-calendar'); ?></label>
			</p>
			<?php } ?>
			</fieldset>
</div>
</div>
</div>

<div class="ui-sortable meta-box-sortables">
<div class="postbox">
<h3><?php _e('Event Date and Time','my-calendar'); ?></h3>
<div class="inside">
			<?php if ( is_object($data) ) { // information for rewriting recurring data ?>
			<input type="hidden" name="prev_event_begin" value="<?php echo $data->event_begin; ?>" />
			<input type="hidden" name="prev_event_time" value="<?php echo $data->event_time; ?>" />
			<input type="hidden" name="prev_event_end" value="<?php echo $data->event_end; ?>" />
			<input type="hidden" name="prev_event_endtime" value="<?php echo $data->event_endtime; ?>" />			
			<?php } ?>
			<fieldset><legend><?php _e('Event Date and Time','my-calendar'); ?></legend>
			<div id="event_schedule">
			<div id="event1" class="clonedInput">
			<?php
			if ( !empty($data) ) {
				$event_begin = esc_attr($data->event_begin); 
				$event_end = esc_attr($data->event_end);
				if ( !empty($_GET['date'] ) ) {
					$event = mc_get_event( $instance ); 				
					$event_begin = date( 'Y-m-d', strtotime( $event->occur_begin ) );
					$event_end = date( 'Y-m-d', strtotime( $event->occur_end ) );
				} 
			} else { 
				$event_begin = date("Y-m-d");
				$event_end = '';
			}
			?>
			<p>
			<label for="event_begin" id="eblabel"><?php _e('Start Date (YYYY-MM-DD)','my-calendar'); ?> <span><?php _e('(required)','my-calendar'); ?></span></label> <input type="text" id="event_begin" name="event_begin[]" class="event_begin calendar_input" size="11" value="<?php echo $event_begin; ?>" /> <label for="event_time"><?php _e('Time (hh:mm am/pm)','my-calendar'); ?></label> <input type="text" id="event_time" name="event_time[]" class="input" size="10"	value="<?php 
					$offset = (60*60*get_option('gmt_offset')); // need this for below
					if ( !empty($data) ) {
						echo ($data->event_time == "00:00:00" && $data->event_endtime == "00:00:00")?'':date("h:i a",strtotime($data->event_time));
					} else {
						echo date_i18n("h:i a",current_time('timestamp') );
					}?>" /> <input type="checkbox" value="1" id="event_allday" name="event_allday"<?php if ( !empty($data) && ( $data->event_time == '00:00:00' && $data->event_endtime == '00:00:00' ) ) { echo " checked=\"checked\""; } ?> /> <label for="event_allday"><?php _e('All day event','my-calendar'); ?></label>
			</p>
			<p>
			<label for="event_end" id="eelabel"><?php _e('End Date (YYYY-MM-DD)','my-calendar'); ?></label> <input type="text" name="event_end[]" id="event_end" class="event_end calendar_input" size="11" value="<?php echo $event_end; ?>" /> <label for="event_endtime"><?php _e('End Time (hh:mm am/pm)','my-calendar'); ?></label> <input type="text" id="event_endtime" name="event_endtime[]" class="input" size="10" value="<?php
					if ( !empty($data) ) {
						echo ($data->event_endtime == "00:00:00" && $data->event_time == "00:00:00")?'':date("h:i a",strtotime($data->event_endtime));
					} else {
						echo date("h:i a",strtotime( "+1 hour" )+$offset );
					}?>" /> <input type="checkbox" value="1" id="event_hide_end" name="event_hide_end"<?php if ( !empty($data) && $data->event_hide_end == '1' ) { echo " checked=\"checked\""; } ?> /> <label for="event_hide_end"><?php _e('Hide end time','my-calendar'); ?></label>

			</p>
			</div>
			<?php if ( $mode != 'edit' ) { ?>			
			<p>
			<input type="checkbox" value="1" id="event_span" name="event_span"<?php if ( !empty($data) && $data->event_span == '1' ) { echo " checked=\"checked\""; } else if ( !empty($data) && $data->event_span == '0' ) { echo ""; } else if ( get_option( 'mc_event_span' ) == 'true' ) { echo " checked=\"checked\""; } ?> /> <label for="event_span"><?php _e('This is a multi-day event.','my-calendar'); ?></label>
			</p>
			<p class="note"><em><?php _e('Enter the beginning and ending dates/times for each occurrence of the event.','my-calendar'); ?> <?php _e('If this is a multi-day event, it creates a single event with multiple dates/times; otherwise it creates separate events for each occurrence.','my-calendar'); ?></em></p>
			<div>
				<input type="button" id="add_field" value="<?php _e('Add another occurrence','my-calendar'); ?>" class="button" />
				<input type="button" id="del_field" value="<?php _e('Remove last occurrence','my-calendar'); ?>" class="button" />
			</div>
			<?php } else { ?>
			<?php if ( $data->event_recur != 'S' ) { ?>
			<h4><?php _e('Dates for this event:','my-calendar'); ?></h4>
				<?php _e('Editing a single date of an event changes only that event. Editing the root event changes all events in the series.','my-calendar'); ?>			
				<ul class="columns">
					<?php if ( isset($_GET['date']) ) { $date = (int) $_GET['date']; } else { $date = false; } ?>
					<?php echo mc_instance_list( $data->event_id, $date ); ?>
				</ul>
			<?php } ?>
			<?php if ( $data->event_group_id != 0 ) { ?>
				<?php
					$edit_group_url = admin_url('admin.php?page=my-calendar-groups&mode=edit&event_id='.$data->event_id.'&group_id='.$data->event_group_id);
				?>			
				<h4><?php _e('Related Events:','my-calendar'); ?> (<a href='<?php echo $edit_group_url; ?>'><?php _e('Edit group','my-calendar'); ?></a>)</h4>
					<ul class="columns">
						<?php mc_related_events( $data->event_group_id ); ?>
					</ul>			
				<?php } ?>
			<?php } ?>
			<p>
			<?php _e('Current time difference from GMT is ','my-calendar'); echo get_option('gmt_offset'); _e(' hour(s)', 'my-calendar'); ?>
			</p>
			</div>
			</fieldset>
</div>
</div>
</div>
			<?php if ( ( $mc_input['event_recurs'] == 'on' || $mc_input_administrator ) && empty( $_GET['date'] ) ) { ?>
<div class="ui-sortable meta-box-sortables">		
<div class="postbox">
<h3><?php _e('Recurring Events','my-calendar'); ?></h3>
<div class="inside">
			<?php if ( is_object($data) ) { // information for rewriting recurring data ?>
			<input type="hidden" name="prev_event_repeats" value="<?php echo $data->event_repeats; ?>" />
			<input type="hidden" name="prev_event_recur" value="<?php echo $data->event_recur; ?>" />
			<?php } ?>
			<fieldset>
			<legend><?php _e('Recurring Events','my-calendar'); ?></legend> 
			<?php if (  is_object($data) && $data->event_repeats != NULL ) { $repeats = $data->event_repeats; } else { $repeats = 0; } ?>
			<p>
			<label for="event_repeats"><?php _e('Repeats for','my-calendar'); ?></label> <input type="text" name="event_repeats" id="event_repeats" class="input" size="1" value="<?php echo $repeats; ?>" /> 
			<label for="event_recur"><?php _e('Units','my-calendar'); ?></label> <select name="event_recur" class="input" id="event_recur">
				<?php 
				$event_recur = ( is_object($data) )?$data->event_recur:''; 
				echo mc_recur_options($event_recur);
				?>
			</select><br />
					<?php _e('Your entry is the number of events after the first occurrence of the event: a recurrence of <em>2</em> means the event will happen three times.','my-calendar'); ?>
			</p>
			</fieldset>	
</div>
</div>	
</div>			
			<?php } else { ?>
			<div>
			<?php if ( is_object($data) ) { // information for rewriting recurring data ?>
			<input type="hidden" name="prev_event_repeats" value="<?php echo $data->event_repeats; ?>" />
			<input type="hidden" name="prev_event_recur" value="<?php echo $data->event_recur; ?>" />
			<?php } ?>			
			<input type="hidden" name="event_repeats" value="0" />
			<input type="hidden" name="event_recur" value="S" />
			</div>
		
			<?php } ?>

			<?php if ($mc_input['event_open'] == 'on' || $mc_input_administrator ) { ?>	
<div class="ui-sortable meta-box-sortables">
<div class="postbox">
<h3><?php _e('Event Registration Settings','my-calendar'); ?></h3>
<div class="inside">
			<fieldset>
			<legend><?php _e('Event Registration Status','my-calendar'); ?></legend>
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
				<?php foreach ( $locations as $location ) {
					echo "<option value=\"".$location->location_id."\">".stripslashes($location->location_label)."</option>";
				} ?>
			</select>
			</p>
				<?php } else { ?>
				<input type="hidden" name="location_preset" value="none" />
				<p><a href="<?php echo admin_url('admin.php?page=my-calendar-locations'); ?>"><?php _e('Add recurring locations for later use.','my-calendar'); ?></a></p>
				<?php } ?>
			<?php } else { ?>
				<input type="hidden" name="location_preset" value="none" />			
			<?php } ?>
			<?php if ($mc_input['event_location'] == 'on' || $mc_input_administrator ) { ?>			
			<p>
			<?php _e('All location fields are optional: <em>insufficient information may result in an inaccurate map</em>.','my-calendar'); ?>
			</p>
			<?php if ( current_user_can( 'mc_edit_locations' ) ) { ?><p><input type="checkbox" value="on" name="mc_copy_location" id="mc_copy_location" /> <label for="mc_copy_location"><?php _e('Copy this location into the locations table','my-calendar'); ?></label></p><?php } ?>			
			<p>
			<label for="event_label"><?php _e('Name of Location (e.g. <em>Joe\'s Bar and Grill</em>)','my-calendar'); ?></label> 
			<?php if ( mc_controlled_field( 'label' ) ) {
				if ( !empty( $data ) ) $cur_label = ( stripslashes( $data->event_label ) );			
				echo mc_location_controller( 'label', $cur_label );
			} else { ?>
			<input type="text" id="event_label" name="event_label" class="input" size="40" value="<?php if ( !empty($data) ) esc_attr_e(stripslashes($data->event_label)); ?>" />
			<?php } ?>
			</p>
			<p>
			<label for="event_street"><?php _e('Street Address','my-calendar'); ?></label> <input type="text" id="event_street" name="event_street" class="input" size="40" value="<?php if ( !empty($data) ) esc_attr_e(stripslashes($data->event_street)); ?>" />
			</p>
			<p>
			<label for="event_street2"><?php _e('Street Address (2)','my-calendar'); ?></label> <input type="text" id="event_street2" name="event_street2" class="input" size="40" value="<?php if ( !empty($data) ) esc_attr_e(stripslashes($data->event_street2)); ?>" />
			</p>
			<p>
			<label for="event_phone"><?php _e('Phone','my-calendar'); ?></label> <input type="text" id="event_phone" name="event_phone" class="input" size="32" value="<?php if ( !empty($data) ) esc_attr_e(stripslashes($data->event_phone)); ?>" />
			</p>			
			<p>
			<label for="event_city"><?php _e('City','my-calendar'); ?></label> 
			<?php if ( mc_controlled_field( 'city' ) ) {
				if ( !empty( $data ) ) $cur_label = ( stripslashes( $data->event_city ) );			
				echo mc_location_controller( 'city', $cur_label );
			} else { ?>
			<input type="text" id="event_city" name="event_city" class="input" size="40" value="<?php if ( !empty($data) ) esc_attr_e(stripslashes($data->event_city)); ?>" /> 
			<?php } ?>
			<label for="event_state"><?php _e('State/Province','my-calendar'); ?></label> 
			<?php if ( mc_controlled_field( 'state' ) ) {
				if ( !empty( $data ) ) $cur_label = ( stripslashes( $data->event_state ) );			
				echo mc_location_controller( 'state', $cur_label );
			} else { ?>
			<input type="text" id="event_state" name="event_state" class="input" size="10" value="<?php if ( !empty($data) ) esc_attr_e(stripslashes($data->event_state)); ?>" /> 
			<?php } ?>
			</p>
			<p>
			<label for="event_postcode"><?php _e('Postal Code','my-calendar'); ?></label> 
			<?php if ( mc_controlled_field( 'postcode' ) ) {
			if ( !empty( $data ) ) $cur_label = ( stripslashes( $data->event_postcode ) );			
				echo mc_location_controller( 'postcode', $cur_label );
			} else { ?>
			<input type="text" id="event_postcode" name="event_postcode" class="input" size="10" value="<?php if ( !empty($data) ) esc_attr_e(stripslashes($data->event_postcode)); ?>" />
			<?php } ?>
			<label for="event_region"><?php _e('Region','my-calendar'); ?></label> 
			<?php if ( mc_controlled_field( 'region' ) ) {			
			if ( !empty( $data ) ) $cur_label = ( stripslashes( $data->event_region ) );			
				echo mc_location_controller( 'region', $cur_label );
			} else { ?>
			<input type="text" id="event_region" name="event_region" class="input" size="40" value="<?php if ( !empty( $data ) ) esc_attr_e(stripslashes($data->event_region)); ?>" />
			<?php } ?>
			</p>
			<p>		
			<label for="event_country"><?php _e('Country','my-calendar'); ?></label> 
			<?php if ( mc_controlled_field( 'country' ) ) {			
			if ( !empty( $data ) ) $cur_label = ( stripslashes( $data->event_country ) );			
				echo mc_location_controller( 'country', $cur_label );
			} else { ?>
			<input type="text" id="event_country" name="event_country" class="input" size="10" value="<?php if ( !empty($data) ) esc_attr_e(stripslashes($data->event_country)); ?>" />
			<?php } ?>
			</p>
			<p>
			<label for="event_zoom"><?php _e('Initial Zoom','my-calendar'); ?></label> 
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
			<label for="event_url"><?php _e('Location URL','my-calendar'); ?></label> <input type="text" id="event_url" name="event_url" class="input" size="40" value="<?php if ( !empty( $data ) ) esc_attr_e(stripslashes($data->event_url)); ?>" />
			</p>			
			<fieldset>
			<legend><?php _e('GPS Coordinates (optional)','my-calendar'); ?></legend>
			<p>
			<small><?php _e('If you supply GPS coordinates for your location, they will be used in place of any other address information to provide your map link.','my-calendar'); ?></small>
			</p>
			<p>
			<label for="event_latitude"><?php _e('Latitude','my-calendar'); ?></label> <input type="text" id="event_latitude" name="event_latitude" class="input" size="10" value="<?php if ( !empty( $data ) ) esc_attr_e(stripslashes($data->event_latitude)); ?>" /> <label for="event_longitude"><?php _e('Longitude','my-calendar'); ?></label> <input type="text" id="event_longitude" name="event_longitude" class="input" size="10" value="<?php if ( !empty( $data ) ) esc_attr_e(stripslashes($data->event_longitude)); ?>" />
			</p>			
			</fieldset>	
			<?php } ?>
			<?php if ( ( $mc_input['event_location'] == 'on' || $mc_input['event_location_dropdown'] == 'on' ) || $mc_input_administrator ) { ?>
			</fieldset>
		</div>
		</div>
	</div>
			<?php } ?>
			<?php if ( !isset($mc_input['event_specials']) || $mc_input['event_specials'] == 'on' || $mc_input_administrator ) { ?>			
<div class="ui-sortable meta-box-sortables">
	<div class="postbox">
	<h3><?php _e('Special scheduling options','my-calendar'); ?></h3>
		<div class="inside">		
			<fieldset>
			<legend><?php _e('Special Options','my-calendar'); ?></legend>
			<p>
			<label for="event_holiday"><?php _e('Cancel this event if it occurs on a date with an event in the Holidays category','my-calendar'); ?></label> <input type="checkbox" value="true" id="event_holiday" name="event_holiday"<?php if ( !empty($data) && $data->event_holiday == '1' ) { echo " checked=\"checked\""; } else if ( !empty($data) && $data->event_holiday == '0' ) { echo ""; } else if ( get_option( 'mc_skip_holidays' ) == 'true' ) { echo " checked=\"checked\""; } ?> />
			</p>
			<p>
			<label for="event_fifth_week"><?php _e('If this event recurs, and falls on the 5th week of the month in a month with only four weeks, move it back one week.','my-calendar'); ?></label> <input type="checkbox" value="true" id="event_fifth_week" name="event_fifth_week"<?php if ( !empty($data) && $data->event_fifth_week == '1' ) { echo " checked=\"checked\""; } else if ( !empty($data) && $data->event_fifth_week == '0' ) { echo ""; } else if ( get_option( 'mc_no_fifth_week' ) == 'true' ) { echo " checked=\"checked\""; } ?> />
			</p>
			</fieldset>
		</div>
	</div>
</div>
			<?php } else { ?>
				<div>
				<input type="hidden" name="event_holiday" value="true"<?php if ( get_option( 'mc_skip_holidays' ) == 'true' ) { echo " checked=\"checked\""; } ?> />
				<input type="hidden" name="event_fifth_week" value="true"<?php if ( get_option( 'mc_no_fifth_week' ) == 'true' ) { echo " checked=\"checked\""; } ?>/>
				</div>
			<?php } ?>
		<p>
			<input type="submit" name="save" class="button-secondary" value="<?php _e('Save Event','my-calendar'); ?>" />
		</p>
</form>	
</div>
</div>

<?php }

// Used on the manage events admin page to display a list of events
function jd_events_display_list( $type='normal' ) {
	global $wpdb;
	$mcdb = $wpdb;
	if ( current_user_can('mc_approve_events') || current_user_can('mc_manage_events') ) {
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
		    case 1:$sortbyvalue = 'event_ID';break;
			case 2:$sortbyvalue = 'event_title';break;
			case 3:$sortbyvalue = 'event_desc';break;
			case 4:$sortbyvalue = 'event_begin';break;
			case 5:$sortbyvalue = 'event_author';break;
			case 6:$sortbyvalue = 'event_category';break;
			case 7:$sortbyvalue = 'event_label';break;
			default:$sortbyvalue = 'event_begin';
		}
	}
	$sortbydirection = ($sortdir == 'default')?'DESC':$sortdir;
	$sorting = ($sortbydirection == 'DESC')?"&amp;order=ASC":'';
	
	$status = ( isset($_GET['limit']) )?$_GET['limit']:'all';
	$restrict = ( isset( $_GET['restrict'] ) )?$_GET['restrict']:'all';
	switch ($status) {
		case 'all':$limit = '';break;
		case 'reserved':$limit = 'WHERE event_approved = 0';break;
		case 'published':$limit = 'WHERE event_approved = 1';break;
		default:$limit = '';
	}
	switch ( $restrict ) {
		case 'all':$filter='';break;
		case 'where':$filter =( isset( $_GET['filter'] ) )?$_GET['filter']:''; $restrict = "event_label"; break;
		case 'author':$filter =( isset( $_GET['filter'] ) )?(int) $_GET['filter']:''; $restrict = "event_author"; break;
		case 'category':$filter =( isset( $_GET['filter'] ) )?(int) $_GET['filter']:''; $restrict = "event_category"; break;
		default:$filter='';
	}
	$filter = esc_sql(urldecode($filter));
	if ( $restrict == "event_label" ) { $filter = "'$filter'"; }	
	if ( $limit == '' && $filter != '' ) {
		$limit = "WHERE $restrict = $filter";
	} else if ( $limit != '' && $filter != '' ) {
		$limit .= "AND WHERE $restrict = $filter";
	}
	if ( $filter == '' ) { $filtered = ""; } else { $filtered = "<a href='".admin_url('admin.php?page=my-calendar')."'>".__('Clear filters','my-calendar')."</a>"; }
	$current = empty($_GET['paged']) ? 1 : intval($_GET['paged']);
	$items_per_page = ( get_option('mc_num_per_page') == '' )?50:get_option('mc_num_per_page');	
	$events = $mcdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM " . my_calendar_table() . " $limit ORDER BY $sortbyvalue $sortbydirection LIMIT ".(($current-1)*$items_per_page).", ".$items_per_page );
	$found_rows = $wpdb->get_col("SELECT FOUND_ROWS();");
	$items = $found_rows[0];
	?>
	<h2 class='mc-clear' id='mc-manage'><?php _e('Manage Events','my-calendar'); ?></h2>
		<?php if ( get_option('mc_event_approve') == 'true' ) { ?>
		<ul class="links">
		<li><a <?php echo ( isset($_GET['limit']) && $_GET['limit']=='published' )?' class="active-link"':''; ?> href="<?php echo admin_url('admin.php?page=my-calendar&amp;limit=published#my-calendar-admin-table'); ?>"><?php _e('Published','my-calendar'); ?></a></li>
		<li><a <?php echo ( isset($_GET['limit']) && $_GET['limit']=='reserved')?' class="active-link"':''; ?>  href="<?php echo admin_url('admin.php?page=my-calendar&amp;limit=reserved#my-calendar-admin-table'); ?>"><?php _e('Reserved','my-calendar'); ?></a></li> 
		<li><a <?php echo ( isset($_GET['limit']) && $_GET['limit']=='all' || !isset($_GET['limit']))?' class="active-link"':''; ?>  href="<?php echo admin_url('admin.php?page=my-calendar&amp;limit=all#my-calendar-admin-table'); ?>"><?php _e('All','my-calendar'); ?></a></li>
		</ul>
		<?php } ?>
		<?php echo $filtered; ?>
	<?php
	if ( !empty($events) ) {
		?>
		<form action="<?php echo admin_url('admin.php?page=my-calendar'); ?>" method="post">
		<div>
		<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" />
		</div>
		<?php
		$num_pages = ceil($items / $items_per_page);
		if ( $num_pages > 1 ) {
			$page_links = paginate_links( array(
				'base' => add_query_arg( 'paged', '%#%' ),
				'format' => '',
				'prev_text' => __('&laquo; Previous Page','my-calendar'),
				'next_text' => __('Next Page &raquo;','my-calendar'),
				'total' => $num_pages,
				'current' => $current
			));
			echo "<div class='tablenav'>";
			echo "<div class='tablenav-pages'>";
			echo $page_links; 
			echo "</div>";
			echo "</div>";
		}
		?>
<table class="widefat page fixed" id="my-calendar-admin-table">
	<thead>
	<tr>
		<th class="manage-column" scope="col"><a href="<?php echo admin_url("admin.php?page=my-calendar&amp;sort=1$sorting"); ?>"><?php _e('ID','my-calendar') ?></a></th>
		<th class="manage-column" scope="col"><a href="<?php echo admin_url("admin.php?page=my-calendar&amp;sort=2$sorting"); ?>"><?php _e('Title','my-calendar') ?></a></th>
		<th class="manage-column" scope="col"><a href="<?php echo admin_url("admin.php?page=my-calendar&amp;sort=7$sorting"); ?>"><?php _e('Where','my-calendar') ?></a></th>
		<th class="manage-column" scope="col"><a href="<?php echo admin_url("admin.php?page=my-calendar&amp;sort=3$sorting"); ?>"><?php _e('Description','my-calendar') ?></a></th>
		<th class="manage-column" scope="col"><a href="<?php echo admin_url("admin.php?page=my-calendar&amp;sort=4$sorting"); ?>"><?php _e('Starts','my-calendar') ?></a></th>
		<th class="manage-column" scope="col"><?php _e('Recurs','my-calendar') ?></th>
		<th class="manage-column" scope="col"><a href="<?php echo admin_url("admin.php?page=my-calendar&amp;sort=5$sorting"); ?>"><?php _e('Author','my-calendar') ?></a></th>
		<th class="manage-column" scope="col"><a href="<?php echo admin_url("admin.php?page=my-calendar&amp;sort=6$sorting"); ?>"><?php _e('Category','my-calendar') ?></a></th>
		<th class="manage-column" scope="col"><?php _e('Edit / Delete','my-calendar') ?></th>
	</tr>
	</thead>
		<?php
		$class = '';
		$sql = "SELECT * FROM " . my_calendar_categories_table() ;
        $categories = $mcdb->get_results($sql);

		foreach ( array_keys($events) as $key ) {
			$event =& $events[$key];
			$class = ($class == 'alternate') ? '' : 'alternate';
			$spam = ($event->event_flagged == 1) ? ' spam' : '';
			$pending = ($event->event_approved == 0) ? ' pending' : '';
			
			$spam_label = ($event->event_flagged == 1) ? '<strong>Possible spam:</strong> ' : '';
			$author = ( $event->event_author != 0 )?get_userdata($event->event_author):'Public Submitter';
			$title = ($event->event_link != '')?"<a href='".esc_attr($event->event_link)."'>$event->event_title</a>":$event->event_title;
			?>
			<tr class="<?php echo $class; echo $spam; echo $pending; ?>">
				<th scope="row"><input type="checkbox" value="<?php echo $event->event_id; ?>" name="mass_edit[]" id="mc<?php echo $event->event_id; ?>" <?php echo ($event->event_flagged == 1)?' checked="checked"':''; ?> /> <label for="mc<?php echo $event->event_id; ?>"><?php echo $event->event_id; ?></label></th>
				<td><?php echo $spam_label; echo stripslashes($title); ?></td>
				<td><a href='<?php $elabel = urlencode($event->event_label); echo admin_url("admin.php?page=my-calendar&amp;filter=$elabel&amp;restrict=where"); ?>' title="<?php _e('Filter by location','my-calendar'); ?>"><?php echo stripslashes($event->event_label); ?></a></td>
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
					$eternity = _mc_increment_values( $event->event_recur );
					if ($event->event_recur == 'S') { _e('N/A','my-calendar'); }
					else if ( $event->event_repeats > 0 ) { printf( __('%d Times','my-calendar'),$event->event_repeats); }	
					else if ( $eternity ) { printf( __('%d Times','my-calendar'),$eternity ); }	
				?>				
				</td>
				<td><a href="<?php $auth = (is_object($author))?$author->ID:0; echo admin_url("admin.php?page=my-calendar&amp;filter=$auth&amp;restrict=author"); ?>" title="<?php _e('Filter by author','my-calendar'); ?>"><?php echo ( is_object($author)?$author->display_name:$author ); ?></a></td>
                                <?php
								$this_category = $event->event_category;
								foreach ($categories as $key=>$value) {
									if ($value->category_id == $this_category) {
										$this_cat = $categories[$key];
									} 
								}
                                ?>
				<td><div class="category-color" style="background-color:<?php echo (strpos($this_cat->category_color,'#') !== 0)?'#':''; echo $this_cat->category_color;?>;"> </div> <a href='<?php echo admin_url("admin.php?page=my-calendar&amp;filter=$event->event_category&amp;restrict=category"); ?>' title="<?php _e('Filter by category','my-calendar'); ?>"><?php echo stripslashes($this_cat->category_name); ?></a></td>
				<?php unset($this_cat); ?>
				<td>
				<a href="<?php echo admin_url("admin.php?page=my-calendar&amp;mode=copy&amp;event_id=$event->event_id"); ?>" class='copy'><?php _e('Copy','my-calendar'); ?></a> &middot; 
				<?php if ( mc_can_edit_event( $event->event_author ) ) { ?>
				<a href="<?php echo admin_url("admin.php?page=my-calendar&amp;mode=edit&amp;event_id=$event->event_id"); ?>" class='edit'><?php _e('Edit','my-calendar'); ?></a> <?php if ( mc_event_is_grouped( $event->event_group_id ) ) { ?>
				&middot; <a href="<?php echo admin_url("admin.php?page=my-calendar-groups&amp;mode=edit&amp;event_id=$event->event_id&amp;group_id=$event->event_group_id"); ?>" class='edit group'><?php _e('Edit Group','my-calendar'); ?></a>
				<?php } ?>
				&middot; <a href="<?php echo admin_url("admin.php?page=my-calendar&amp;mode=delete&amp;event_id=$event->event_id"); ?>" class="delete"><?php _e('Delete','my-calendar'); ?></a>
				<?php } else { _e("Not editable.",'my-calendar'); } ?>
				<?php if ( get_option( 'mc_event_approve' ) == 'true' ) { ?>
				 &middot; 
						<?php if ( current_user_can( 'mc_approve_events' ) ) { // Added by Roland P.?>
							<?php	// by Roland 
							if ( $event->event_approved == '1' )  { ?>
								<a href="<?php echo admin_url("admin.php?page=my-calendar&amp;mode=reject&amp;event_id=$event->event_id"); ?>" class='reject'><?php _e('Reject','my-calendar'); ?></a>
							<?php } else { 	?>
								<a href="<?php echo admin_url("admin.php?page=my-calendar&amp;mode=approve&amp;event_id=$event->event_id"); ?>" class='publish'><?php _e('Approve','my-calendar'); ?></a>		
							<?php } ?>
						<?php } else { ?>
							<?php	// by Roland 
							if ( $event->event_approved == '1' )  { ?>
								<?php _e('Approved','my-calendar'); ?>
							<?php } else if ($event->event_approved == '2' ) { 	?>
								<?php _e('Rejected','my-calendar'); ?>							
							<?php } else { ?>
								<?php _e('Awaiting Approval','my-calendar'); ?>		
							<?php } ?>
						<?php } ?>	
				<?php } ?>					
				</td>	
			</tr>
<?php
		}
?>
		</table>
		<p>
		<input type="submit" class="button-secondary delete" name="mass_delete" value="<?php _e('Delete checked events','my-calendar'); ?>" />
		<?php if ( current_user_can('mc_approve_events') ) { ?>
		<input type="submit" class="button-secondary mc-approve" name="mass_approve" value="<?php _e('Approve checked events','my-calendar'); ?>" />
		<?php } ?>
		</p>
		</form>
<?php
/* LATER
		if ( get_option('mc_admin_calendar') == 'on' ) {
			echo do_shortcode("[my_calendar]");
		}
*/
	} else {
?>
		<p><?php _e("There are no events in the database!",'my-calendar') ?></p>
<?php	
	}
	}
}

function mc_check_data($action,$post, $i) {
	global $wpdb, $current_user, $users_entries;
	$mcdb = $wpdb;
	$start_date_ok = 0;
	$end_date_ok = 0;
	$time_ok = 0;
	$endtime_ok = 0;
	$url_ok = 0;
	$title_ok = 0;
	$recurring_ok = 0;
	$proceed = true;
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
		$recur = !empty($post['event_recur']) ? trim($post['event_recur']) : '';
		// if this is an all weekdays event, and it's been scheduled to start on a weekend, the math gets nasty. 
		// ...AND there's no reason to allow it, since weekday events will NEVER happen on the weekend.
			$begin = trim($post['event_begin'][$i]);
			$end = trim($post['event_end'][$i]);
		if ( $recur == 'E' && ( date( 'w', strtotime( $begin ) ) == 0 || date( 'w', strtotime( $begin ) ) == 6 ) ) {
			if ( date( 'w', strtotime( $begin ) ) == 0 ) {
				$newbegin = my_calendar_add_date( $begin, 1 );
				if ( !empty( $post['event_end'][$i] ) ) {
					$newend = my_calendar_add_date( $end, 1 );
				} else {
					$newend = $newbegin;
				}
			} else if ( date( 'w', strtotime( $begin ) ) == 6 ) {
				$newbegin = my_calendar_add_date( $begin, 2 );
				if ( !empty( $post['event_end'][$i] ) ) {
					$newend = my_calendar_add_date( $end, 2 );
				} else {
					$newend = $newbegin;
				}		
			}
			$begin = $newbegin;
			$end = $newend;
		} else {
			$begin = !empty($post['event_begin'][$i]) ? trim($post['event_begin'][$i]) : '';
			$end = !empty($post['event_end'][$i]) ? trim($post['event_end'][$i]) : $begin;
		}
		$begin = date( 'Y-m-d',strtotime($begin) );// regardless of entry format, convert.
		$end = date( 'Y-m-d',strtotime($end) );// regardless of entry format, convert.
		$time = !empty($post['event_time'][$i]) ? trim($post['event_time'][$i]) : '';
		$endtime = !empty($post['event_endtime'][$i]) ? trim($post['event_endtime'][$i]) : date('H:i:s',strtotime( $time . ' +1 hour' ) );
			$endtime = ( $time == '' || $time == '00:00:00' )?'00:00:00':$endtime; // set at midnight if all day.
			if ( isset($post['event_allday']) ) { $time = $endtime = '00:00:00'; }
		$repeats = ( !empty($post['event_repeats']) || trim($post['event_repeats'])=='' ) ? trim($post['event_repeats']) : 0;
		$host = !empty($post['event_host']) ? $post['event_host'] : $current_user->ID;	
		$category = !empty($post['event_category']) ? $post['event_category'] : '';
		$linky = !empty($post['event_link']) ? trim($post['event_link']) : '';
		$expires = !empty($post['event_link_expires']) ? $post['event_link_expires'] : '0';
		$approved = !empty($post['event_approved']) ? $post['event_approved'] : '0';
		$location_preset = !empty($post['location_preset']) ? $post['location_preset'] : '';
		$event_author = !empty($post['event_author']) ? $post['event_author'] : $current_user->ID;
		$event_open = (isset($post['event_open']) && $post['event_open']!==0) ? $post['event_open'] : '2';
		$event_group = !empty($post['event_group']) ? 1 : 0;
		$event_flagged = ( !isset($post['event_flagged']) || $post['event_flagged']===0 )?0:1;
		$event_image = ( isset($post['event_image']) )?esc_url_raw( $post['event_image'] ):'';
		$event_fifth_week = !empty($post['event_fifth_week']) ? 1 : 0;
		$event_holiday = !empty($post['event_holiday']) ? 1 : 0;
		// get group id: if multiple events submitted, auto group OR if event being submitted is already part of a group; otherwise zero.
			$group_id_submitted = (int) $post['event_group_id'];
		$event_group_id = ( ( is_array($post['event_begin']) && count($post['event_begin'])>1 ) || mc_event_is_grouped( $group_id_submitted) )?$group_id_submitted:0;
		$event_span = (!empty($post['event_span']) && $event_group_id != 0 ) ? 1 : 0;
		$event_hide_end	= (!empty($post['event_hide_end']) ) ? 1 : 0;
		$event_hide_end = ( $time == '' || $time == '00:00:00' )?1:$event_hide_end; // hide end time automatically on all day events
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
				$event_phone = !empty($post['event_phone'])? $post['event_phone'] : '';
				if ( isset($post['mc_copy_location']) && $post['mc_copy_location'] == 'on' ) { 
					$add_loc = array(
					'location_label'=>$event_label,
					'location_street'=>$event_street,
					'location_street2'=>$event_street2,
					'location_city'=>$event_city,
					'location_state'=>$event_state,
					'location_postcode'=>$event_postcode,
					'location_region'=>$event_region,
					'location_country'=>$event_country,
					'location_url'=>$event_url,
					'location_longitude'=>$event_longitude,
					'location_latitude'=>$event_latitude,
					'location_zoom'=>$event_zoom,
					'location_phone'=>$event_phone
					);
					$loc_formats = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%d', '%s' );		
					$results = $mcdb->insert( my_calendar_locations_table(), $add_loc, $loc_formats );			
				}
			}
		// Perform some validation on the submitted dates - this checks for valid years and months
			// We know we have a valid year and month and valid integers for days so now we do a final check on the date
			$begin_split = explode('-',$begin);
			$begin_y = $begin_split[0]; 
			$begin_m = $begin_split[1];
			$begin_d = $begin_split[2];
			$end_split = explode('-',$end);
			$end_y = $end_split[0];
			$end_m = $end_split[1];
			$end_d = $end_split[2];
			if (checkdate($begin_m,$begin_d,$begin_y) && checkdate($end_m,$end_d,$end_y)) {
			// Ok, now we know we have valid dates, we want to make sure that they are either equal or that the end date is later than the start date
				if (strtotime($end) >= strtotime($begin)) {
				$start_date_ok = 1;
				$end_date_ok = 1;
				} else {
					$errors .= "<div class='error'><p><strong>".__('Error','my-calendar').":</strong> ".__('Your event end date must be either after or the same as your event begin date','my-calendar')."</p></div>";
				}
			} else {
					$errors .= "<div class='error'><p><strong>".__('Error','my-calendar').":</strong> ".__('Your date formatting is correct but one or more of your dates is invalid. Check for number of days in month and leap year related errors.','my-calendar')."</p></div>";
			}

			// We check for a valid time, or an empty one
			$time = ($time == '')?'00:00:00':date( 'H:i:00',strtotime($time) );
			$time_format_one = '/^([0-1][0-9]):([0-5][0-9]):([0-5][0-9])$/';
			$time_format_two = '/^([2][0-3]):([0-5][0-9]):([0-5][0-9])$/';
			if (preg_match($time_format_one,$time) || preg_match($time_format_two,$time) || $time == '') {
				$time_ok = 1;
			} else {
				$errors .= "<div class='error'><p><strong>".__('Error','my-calendar').":</strong> ".__('The time field must either be blank or be entered in the format hh:mm am/pm','my-calendar')."</p></div>";
			}
			// We check for a valid end time, or an empty one
			$endtime = ($endtime == '')?'00:00:00':date( 'H:i:00',strtotime($endtime) );
			if (preg_match($time_format_one,$endtime) || preg_match($time_format_two,$endtime) || $endtime == '') {
				$endtime_ok = 1;
			} else {
				$errors .= "<div class='error'><p><strong>".__('Error','my-calendar').":</strong> ".__('The end time field must either be blank or be entered in the format hh:mm am/pm','my-calendar')."</p></div>";
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
		// We run some checks on recurrance                                                                        
		if (( $repeats == 0 && $recur == 'S' ) || (($repeats >= 0) && ($recur == 'W' || $recur == 'B' || $recur == 'M' || $recur == 'U' || $recur == 'Y' || $recur == 'D' || $recur == 'E' ))) {
			$recurring_ok = 1;
		} else {
			$errors .= "<div class='error'><p><strong>".__('Error','my-calendar').":</strong> ".__('The repetition value must be 0 unless a type of recurrence is selected.','my-calendar')."</p></div>";
		}
		if ( function_exists('mcs_submissions') && isset($post['mcs_check_conflicts']) ) {
			$conflicts = mcs_check_conflicts( $begin, $time, $end, $endtime, $event_label );
			if ( $conflicts ) { 
				$errors .= "<div class='error'><p><strong>".__('Error','my-calendar').":</strong> ".__('That event conflicts with a previously scheduled event.','my-calendar')."</p></div>";
				$proceed = false; 
			}
		}	
		if ($start_date_ok == 1 && $end_date_ok == 1 && $time_ok == 1 && $endtime_ok == 1 && $url_ok == 1 && $title_ok == 1 && $recurring_ok == 1 && $proceed != false) {
			$proceed = true;
			$submit = array(
			// strings
				'event_begin'=>$begin, 
				'event_end'=>$end, 
				'event_title'=>$title, 
				'event_desc'=>$desc, 			
				'event_short'=>$short,
				'event_time'=>$time,
				'event_endtime'=>$endtime, 				
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
				'event_recur'=>$recur, 
				'event_image'=>$event_image,
				'event_phone'=>$event_phone,
			// integers
				'event_repeats'=>$repeats, 
				'event_author'=>$event_author,
				'event_category'=>$category, 		
				'event_link_expires'=>$expires, 				
				'event_zoom'=>$event_zoom,
				'event_open'=>$event_open,
				'event_group'=>$event_group,
				'event_approved'=>$approved,
				'event_host'=>$host,
				'event_flagged'=> mc_akismet( $linky, $desc ),
				'event_fifth_week'=>$event_fifth_week,
				'event_holiday'=>$event_holiday,
				'event_group_id'=>$event_group_id,
				'event_span'=>$event_span,
				'event_hide_end'=>$event_hide_end,
			// floats
				'event_longitude'=>$event_longitude,
				'event_latitude'=>$event_latitude			
				);
		} else {
			// The form is going to be rejected due to field validation issues, so we preserve the users entries here
			$users_entries->event_title = $title;
			$users_entries->event_desc = $desc;
			$users_entries->event_begin = $begin;
			$users_entries->event_end = $end;
			$users_entries->event_time = $time;
			$users_entries->event_endtime = $endtime;
			$users_entries->event_recur = $recur;
			$users_entries->event_repeats = $repeats;
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
			$users_entries->event_author = $event_author;
			$users_entries->event_open = $event_open;
			$users_entries->event_short = $short;
			$users_entries->event_group = $event_group;
			$users_entries->event_approved = $approved;
			$users_entries->event_image = $event_image;
			$users_entries->event_fifth_week = $event_fifth_week;
			$users_entries->event_holiday = $event_holiday;
			$users_entries->event_flagged = 0;
			$users_entries->event_group_id = $event_group_id;
			$users_entries->event_span = $event_span;
			$users_entries->event_hide_end = $event_hide_end;
			$proceed = false;
		}
	$data = array($proceed, $users_entries, $submit,$errors);

	return $data;
}

function mcs_check_conflicts( $begin, $time, $end, $endtime, $event_label ) {
	global $wpdb;
	$select_location = ( $event_label != '' )?"event_label = '$event_label' AND":'';
	$event_query = "SELECT occur_id 
					FROM " . MY_CALENDAR_EVENTS_TABLE . "
					JOIN " . MY_CALENDAR_TABLE ."
					ON (event_id=occur_event_id) 
					WHERE $select_location
					( occur_begin BETWEEN '$begin $time' AND '$end $endtime' OR occur_end BETWEEN '$begin $time' AND '$end $endtime' )";
	$results = $wpdb->get_results($event_query);
	if ( !empty($results) ) {
		return $results; 
	} else {
		return false;
	}
}

/* Event editing utilities */

function mc_compare( $update, $id ) {
	$event = mc_get_event_core( $id );
	$update_string = $event_string = '';
	//$comparison_test = array();
	foreach ( $update as $k=>$v ) {
		// event_recur and event_repeats always get set to single and 0; event_begin and event_end need to be checked elsewhere.
		if ( $k != 'event_recur' && $k != 'event_repeats' && $k != 'event_begin' && $k != 'event_end' ) {
			$update_string .= trim($v);
			$event_string .= trim($event->$k);
			$v2 = $event->$k;
			//$comparison_test[$k] = "$v2, $v";
		}
	}
	//echo "<pre>";print_r($comparison_test);echo "</pre>";
	$update_hash = md5($update_string);
	$event_hash = md5($event_string);
	if ( $update_hash == $event_hash ) {
		return false;
	} else {
		return true;
	}
}
// args: instance ID, event ID, array containing updated dates.
function mc_update_instance( $event_instance, $event_id, $update=array() ) {
global $wpdb;
$mcdb = $wpdb;
if ( !empty($update) ) {
	$formats = array( '%d','%s','%s','%d' );
	$begin = ( !empty($update) )?$update['event_begin'].' '.$update['event_time']:$event->occur_begin;
	$end = ( !empty($update) )?$update['event_end'].' '.$update['event_endtime']:$event->occur_end;
	$data = array( 'occur_event_id'=>$event_id, 'occur_begin'=>$begin,'occur_end'=>$end,'occur_group_id'=>$update['event_group_id'] );
} else {
	$formats = array( '%d','%d' );
	$group_id = mc_get_data( 'event_group_id', $event_id );
	$data = array( 'occur_event_id'=>$event_id,'occur_group_id'=>$group_id );
}
	$result = $mcdb->update( 
		my_calendar_event_table(),
		$data,
		array( 'occur_id'=>$event_instance ),
		$formats,
		'%d' );
	return $result;
}
// arbitrary field update to event table
function mc_update_data( $event_id, $field, $value, $format='%d' ) {
	global $wpdb;
	$data = array( $field=>$value );
	$formats = ( $format );
	$result = $wpdb->update(
		my_calendar_table(),
		$data,
		array( 'event_id'=>$event_id ),
		$formats,
		'%d' );
	return $result;
}
/* returns next available group ID */
function mc_group_id() {
	global $wpdb;
	$mcdb = $wpdb;
	$query = "SELECT MAX(event_id) FROM ".my_calendar_table();
	$result = $mcdb->get_var($query);
	$next = $result+1;
	return $next;
}

function mc_instance_list( $id, $occur=false, $template='<h3>{title}</h3>{description}', $list='<li>{date}, {time}</li>', $before="<ul>", $after="</ul>" ) {
	global $wpdb;
	$id = (int) $id;
	$output = '';
	$sql = "SELECT * FROM ".my_calendar_event_table()." WHERE occur_event_id=$id";
	$results = $wpdb->get_results($sql);
	if ( is_array($results) && is_admin() ) {
		foreach ( $results as $result ) {
			if ( $result->occur_id == $occur ) { 
				$current = "<em>".__('Editing: ','my-calendar')."</em>";  $end = '';
			} else { 
				$current = "<a href='".admin_url('admin.php?page=my-calendar')."&amp;mode=edit&amp;event_id=$id&amp;date=$result->occur_id'>"; $end = "</a>";
			}
			$begin = date( get_option('mc_date_format'),strtotime($result->occur_begin) ) . ' ' . date( get_option('mc_time_format'),strtotime($result->occur_begin) );
			$output.= "<li>$current$begin$end</li>";
		}
	} else {
		$details = '';
		foreach ( $results as $result ) {
			$event_id = $result->occur_id;
			$event = mc_get_event( $event_id );
			$array = event_as_array($event);
			if ( $details == '' ) {
				$details = ( $template != '' )?jd_draw_template( $array, $template ):' ';
			}
			$item = ( $list != '' )?jd_draw_template( $array, $list ):'';
			$output.= $item;
		}
		$output = $details . $before . $output . $after;
	}
	return $output;
}

function mc_get_data( $field, $id ) {
	global $wpdb;
	$mcdb = $wpdb;
    if ( get_option( 'mc_remote' ) == 'true' && function_exists('mc_remote_db') ) { $mcdb = mc_remote_db(); }
	$sql = "SELECT $field FROM ".my_calendar_table()." WHERE event_id=$id";
	$result = $mcdb->get_var($sql);
	return $result;
}

function mc_related_events( $id ) {
	global $wpdb;
	$id = (int) $id;
	if ( $id === 0 ) { echo "<li>".__('No related events','my-calendar')."</li>"; return; }
	$output = '';
	$sql = "SELECT * FROM ".my_calendar_event_table()." WHERE occur_group_id=$id";
	$results = $wpdb->get_results($sql);
	if ( is_array($results) && !empty($results) ) {
		foreach ( $results as $result ) {
			$event = $result->occur_event_id;
			$title = mc_get_data('event_title',$result->occur_event_id );
			$current = "<a href='".admin_url('admin.php?page=my-calendar')."&amp;mode=edit&amp;event_id=$event'>"; $end = "</a>";
			$begin = date( get_option('mc_date_format'),strtotime($result->occur_begin) ) . ' ' . date( get_option('mc_time_format'),strtotime($result->occur_begin) );
			$output.= "<li>$title; $current$begin$end</li>";
		}
	} else {
		$output = "<li>".__('No related events','my-calendar')."</li>";
	}
	echo $output;
}


function mc_event_is_grouped( $group_id ) {
	global $wpdb;
	$mcdb = $wpdb;
	if ( $group_id == 0 ) { return false; } else {
	$query = "SELECT count( event_group_id ) FROM ".my_calendar_table()." WHERE event_group_id = $group_id";
	$value = $mcdb->get_var($query);
		if ( $value > 1 ) {
			return true;
		} else {
			return false;
		}
	}
}