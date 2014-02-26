<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'my-calendar-locations.php' == basename($_SERVER['SCRIPT_FILENAME'])) {
	die ('Please do not load this page directly. Thanks!');
}
// Function to handle the management of locations

function my_calendar_manage_locations() {
  global $wpdb;
	$mcdb = $wpdb;
  // My Calendar must be installed and upgraded before this will work
  check_my_calendar();
	$formats = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%d', '%s' )
?>
<div class="wrap jd-my-calendar">
<?php 
my_calendar_check_db();
?>
<?php
  // We do some checking to see what we're doing
	if ( !empty($_POST) ) {
		$nonce=$_REQUEST['_wpnonce'];
		if (! wp_verify_nonce($nonce,'my-calendar-nonce') ) die("Security check failed");
	}
	if (isset($_POST['mode']) && $_POST['mode'] == 'add') {
		$add = array(
		'location_label'=>$_POST['location_label'],
		'location_street'=>$_POST['location_street'],
		'location_street2'=>$_POST['location_street2'],
		'location_city'=>$_POST['location_city'],
		'location_state'=>$_POST['location_state'],
		'location_postcode'=>$_POST['location_postcode'],
		'location_region'=>$_POST['location_region'],
		'location_country'=>$_POST['location_country'],
		'location_url'=>$_POST['location_url'],
		'location_longitude'=>$_POST['location_longitude'],
		'location_latitude'=>$_POST['location_latitude'],
		'location_zoom'=>$_POST['location_zoom'],
		'location_phone'=>$_POST['location_phone']
		);
		$results = $mcdb->insert( my_calendar_locations_table(), $add, $formats );
	  
		if ($results) {
			echo "<div class=\"updated\"><p><strong>".__('Location added successfully','my-calendar')."</strong></p></div>";
		} else {
			echo "<div class=\"error\"><p><strong>".__('Location could not be added to database','my-calendar')."</strong></p></div>";
		}
    } else if ( isset($_GET['location_id']) && $_GET['mode'] == 'delete') {
		$sql = "DELETE FROM " . my_calendar_locations_table() . " WHERE location_id=".(int)($_GET['location_id']);
		$results = $mcdb->query($sql);
		if ($results) {
			echo "<div class=\"updated\"><p><strong>".__('Location deleted successfully','my-calendar')."</strong></p></div>";
		} else {
			echo "<div class=\"error\"><p><strong>".__('Location could not be deleted','my-calendar')."</strong></p></div>";	  
		}
    } else if (isset($_GET['mode']) && isset($_GET['location_id']) && $_GET['mode'] == 'edit' && !isset($_POST['mode'])) {
	  $cur_loc = (int) $_GET['location_id'];
      mc_show_location_form('edit', $cur_loc);
    } else if ( isset($_POST['location_id']) && isset($_POST['location_label']) && $_POST['mode'] == 'edit' ) {
		$update = array(
		'location_label'=>$_POST['location_label'],
		'location_street'=>$_POST['location_street'],
		'location_street2'=>$_POST['location_street2'],
		'location_city'=>$_POST['location_city'],
		'location_state'=>$_POST['location_state'],
		'location_postcode'=>$_POST['location_postcode'],
		'location_region'=>$_POST['location_region'],
		'location_country'=>$_POST['location_country'],
		'location_url'=>$_POST['location_url'],
		'location_longitude'=>$_POST['location_longitude'],
		'location_latitude'=>$_POST['location_latitude'],
		'location_zoom'=>$_POST['location_zoom'],
		'location_phone'=>$_POST['location_phone']
		);
		$where = array(
		'location_id'=>(int) $_POST['location_id']
		);
		$results = $mcdb->update( my_calendar_locations_table(), $update, $where, $formats, '%d' );
		if ( $results === false ) {
			echo "<div class=\"error\"><p><strong>".__('Location could not be edited.','my-calendar')."</strong></p></div>";
		} else if ( $results == 0 ) {
			echo "<div class=\"updated error\"><p><strong>".__('Location was not changed.','my-calendar')."</strong></p></div>";  
		} else {
			echo "<div class=\"updated\"><p><strong>".__('Location edited successfully','my-calendar')."</strong></p></div>";
		}
		$cur_loc = (int) $_POST['location_id'];		
		mc_show_location_form('edit', $cur_loc);
		
	}

	if ( isset( $_GET['mode']) && $_GET['mode'] != 'edit' || isset($_POST['mode']) && $_POST['mode'] != 'edit' || !isset($_GET['mode']) && !isset($_POST['mode']) ) {
		mc_show_location_form('add');
	} 
}

function mc_show_location_form( $view='add',$curID='' ) {
global $wpdb;
	$mcdb = $wpdb;
	if ($curID != '') {
		$sql = "SELECT * FROM " . my_calendar_locations_table() . " WHERE location_id=$curID";
		$cur_loc = $mcdb->get_row($sql);
	}
?>
<?php if ($view == 'add') { ?>
<h2><?php _e('Add New Location','my-calendar'); ?></h2>
<?php } else { ?>
<h2><?php _e('Edit Location','my-calendar'); ?></h2>
<?php } ?>
<div class="postbox-container" style="width: 70%">
<div class="metabox-holder">

<div class="ui-sortable meta-box-sortables">   
<div class="postbox">
<h3><?php _e('Location Editor','my-calendar'); ?></h3>
	<div class="inside">	   
    <form id="my-calendar" method="post" action="<?php echo admin_url("admin.php?page=my-calendar-locations"); ?>">
	<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" /></div>	
		<?php if ( $view == 'add' ) { ?>
			<div>
			<input type="hidden" name="mode" value="add" />
            <input type="hidden" name="location_id" value="" />
			</div>
		<?php } else { ?>
		<div>
			<input type="hidden" name="mode" value="edit" />
            <input type="hidden" name="location_id" value="<?php echo $cur_loc->location_id ?>" />
		</div>
		<?php } ?>
			<fieldset>
			<legend><?php _e('Event Location','my-calendar'); ?></legend>
			<p>
			<?php _e('All location fields are optional: <em>insufficient information may result in an inaccurate map</em>.','my-calendar'); ?>
			</p>
			<p>
			<label for="location_label"><?php _e('Name of Location (e.g. <em>Joe\'s Bar and Grill</em>)','my-calendar'); ?></label> 
			<?php if ( mc_controlled_field( 'label' ) ) {
				if ( !empty( $cur_loc ) ) $cur_label = (stripslashes($cur_loc->location_label));			
				echo mc_location_controller( 'label', $cur_label );
			} else { ?>
			<input type="text" id="location_label" name="location_label" class="input" size="40" value="<?php if ( !empty( $cur_loc ) ) esc_attr_e(stripslashes($cur_loc->location_label)); ?>" />
			<?php } ?>
			</p>
			<p>
			<label for="location_street"><?php _e('Street Address','my-calendar'); ?></label> <input type="text" id="location_street" name="location_street" class="input" size="40" value="<?php if ( !empty( $cur_loc ) ) esc_attr_e(stripslashes($cur_loc->location_street)); ?>" />
			</p>			
			<p>
			<label for="location_street2"><?php _e('Street Address (2)','my-calendar'); ?></label> <input type="text" id="location_street2" name="location_street2" class="input" size="40" value="<?php if ( !empty( $cur_loc ) ) esc_attr_e(stripslashes($cur_loc->location_street2)); ?>" />
			</p>
			<p>
			<label for="location_phone"><?php _e('Phone','my-calendar'); ?></label> <input type="text" id="location_phone" name="location_phone" class="input" size="32" value="<?php if ( !empty( $cur_loc ) ) esc_attr_e(stripslashes($cur_loc->location_phone)); ?>" />
			</p>			
			<p>
			<label for="location_city"><?php _e('City','my-calendar'); ?></label> 
			<?php if ( mc_controlled_field( 'city' ) ) {
				if ( !empty( $cur_loc ) ) $cur_label = ( stripslashes( $cur_loc->location_city ) );			
				echo mc_location_controller( 'city', $cur_label );
			} else { ?>
			<input type="text" id="location_city" name="location_city" class="input" size="40" value="<?php if ( !empty( $cur_loc ) ) esc_attr_e(stripslashes($cur_loc->location_city)); ?>" />
			<?php } ?>
			<label for="location_state"><?php _e('State/Province','my-calendar'); ?></label> 
			<?php if ( mc_controlled_field( 'state' ) ) {
				if ( !empty( $cur_loc ) ) $cur_label = (stripslashes($cur_loc->location_state));			
				echo mc_location_controller( 'state', $cur_label );
			} else { ?>
			<input type="text" id="location_state" name="location_state" class="input" size="10" value="<?php if ( !empty( $cur_loc ) ) echo stripslashes(esc_attr($cur_loc->location_state)); ?>" />	
			<?php } ?>
			</p>
			<p>			
			<label for="location_postcode"><?php _e('Postal Code','my-calendar'); ?></label> 
			<?php if ( mc_controlled_field( 'postcode' ) ) {
				if ( !empty( $cur_loc ) ) $cur_label = (stripslashes($cur_loc->location_postcode));			
				echo mc_location_controller( 'postcode', $cur_label );
			} else { ?>
			<input type="text" id="location_postcode" name="location_postcode" class="input" size="10" value="<?php if ( !empty( $cur_loc ) ) echo stripslashes(esc_attr($cur_loc->location_postcode)); ?>" />
			<?php } ?>
			<label for="location_region"><?php _e('Region','my-calendar'); ?></label> 
			<?php if ( mc_controlled_field( 'region' ) ) {
				if ( !empty( $cur_loc ) ) $cur_label = (stripslashes($cur_loc->location_region));			
				echo mc_location_controller( 'region', $cur_label );
			} else { ?>
			<input type="text" id="location_region" name="location_region" class="input" size="40" value="<?php if ( !empty( $cur_loc ) ) esc_attr_e(stripslashes($cur_loc->location_region)); ?>" />
			<?php } ?>
			</p>
			<p>
			<label for="location_country"><?php _e('Country','my-calendar'); ?></label> 
			<?php if ( mc_controlled_field( 'country' ) ) {
				if ( !empty( $cur_loc ) ) $cur_label = (stripslashes($cur_loc->location_country));			
				echo mc_location_controller( 'country', $cur_label );
			} else { ?>
			<input type="text" id="location_country" name="location_country" class="input" size="10" value="<?php if ( !empty( $cur_loc ) ) esc_attr_e(stripslashes($cur_loc->location_country)); ?>" />
			<?php } ?>
			<label for="location_zoom"><?php _e('Initial Zoom','my-calendar'); ?></label> 
				<select name="location_zoom" id="location_zoom">
				<option value="16"<?php if ( !empty( $cur_loc ) && ( $cur_loc->location_zoom == 16 ) ) { echo " selected=\"selected\""; } ?>><?php _e('Neighborhood','my-calendar'); ?></option>
				<option value="14"<?php if ( !empty( $cur_loc ) && ( $cur_loc->location_zoom == 14 ) ) { echo " selected=\"selected\""; } ?>><?php _e('Small City','my-calendar'); ?></option>
				<option value="12"<?php if ( !empty( $cur_loc ) && ( $cur_loc->location_zoom == 12 ) ) { echo " selected=\"selected\""; } ?>><?php _e('Large City','my-calendar'); ?></option>
				<option value="10"<?php if ( !empty( $cur_loc ) && ( $cur_loc->location_zoom == 10 ) ) { echo " selected=\"selected\""; } ?>><?php _e('Greater Metro Area','my-calendar'); ?></option>
				<option value="8"<?php if ( !empty( $cur_loc ) && ( $cur_loc->location_zoom == 8 ) ) { echo " selected=\"selected\""; } ?>><?php _e('State','my-calendar'); ?></option>
				<option value="6"<?php if ( !empty( $cur_loc ) && ( $cur_loc->location_zoom == 6 ) ) { echo " selected=\"selected\""; } ?>><?php _e('Region','my-calendar'); ?></option>
				</select>
			</p>
			<p>
			<label for="location_url"><?php _e('Website','my-calendar'); ?></label> <input type="text" id="location_url" name="location_url" class="input" size="40" value="<?php if ( !empty( $cur_loc ) ) esc_attr_e(stripslashes($cur_loc->location_url)); ?>" />
			</p>			
			<fieldset>
			<legend><?php _e('GPS Coordinates (optional)','my-calendar'); ?></legend>
			<p>
			<small><?php _e('If you supply GPS coordinates for your location, they will be used in place of any other address information to pinpoint your location.','my-calendar'); ?></small>
			</p>
			<p>
			 <label for="location_latitude"><?php _e('Latitude','my-calendar'); ?></label> <input type="text" id="location_latitude" name="location_latitude" class="input" size="10" value="<?php if ( !empty( $cur_loc ) ) { esc_attr_e(stripslashes($cur_loc->location_latitude)); } else { echo '0.000000'; } ?>" />
			 <label for="location_longitude"><?php _e('Longitude','my-calendar'); ?></label> <input type="text" id="location_longitude" name="location_longitude" class="input" size="10" value="<?php if ( !empty( $cur_loc ) ) { esc_attr_e(stripslashes($cur_loc->location_longitude)); } else { echo '0.000000'; } ?>" />
			</p>			
			</fieldset>
			<p>
                <input type="submit" name="save" class="button-primary" value="<?php if ($view == 'edit') { _e('Save Changes','my-calendar'); } else { _e('Add Location','my-calendar'); } ?> &raquo;" />
			</p>
			</fieldset>
		</form>
	</div>
</div>
</div>
<?php if ($view == 'edit') { ?>
<p><a href="<?php echo admin_url("admin.php?page=my-calendar-locations"); ?>"><?php _e('Add a New Location','my-calendar'); ?> &raquo;</a></p>
<?php } ?>

	<div class="ui-sortable meta-box-sortables">
	<div class="postbox">
	<h3><?php _e('Manage Locations','my-calendar'); ?></h3>
		<div class="inside">	
	<?php mc_manage_locations(); ?>
		</div>
	</div>
	</div>
</div>
	<?php jd_show_support_box(); ?>

</div>

<?php
}

function mc_controlled_field( $this_field ) {
	$this_field = trim($this_field);
	$controlled = get_option( 'mc_location_control' );
	if ( $controlled == 'on' ) {
		$controlled_field = trim(str_replace( 'event_','',get_option('mc_location_type') ));
		return ( $controlled_field==$this_field )?true:false;
	} else {
		return false;
	}
}

function mc_location_controller( $fieldname, $selected ) {
		if ( isset($_GET['page']) && $_GET['page'] == 'my-calendar-locations' ) {
			$fieldname = 'location_'.$fieldname;
		} else {
			$fieldname = 'event_'.$fieldname;
		}
		$selected = trim($selected);
		$options = get_option('mc_user_settings');
		$regions = $options['my_calendar_location_default']['values'];
		$form .= "<select name='$fieldname' id='$fieldname'>";
		$form .= "<option value='none'>No preference</option>\n";				
		foreach ($regions as $key=>$value) {
			$key = trim($key);
			$aselected = ($selected==$key)?" selected='selected'":'';
			$form .= "<option value='$key'$aselected>$value</option>\n";
		}
		$form .= "</select>";		
	return $form;
}

function mc_manage_locations() {
global $wpdb;
$mcdb = $wpdb;
?>
<?php
    
    // We pull the locations from the database	
    $locations = $mcdb->get_results("SELECT * FROM " . my_calendar_locations_table() . " ORDER BY location_label ASC");

 if ( !empty($locations) )
   {
     ?>
     <table class="widefat page fixed" id="my-calendar-admin-table">
       <thead> 
       <tr>
         <th class="manage-column" scope="col"><?php _e('ID','my-calendar') ?></th>
	 <th class="manage-column" scope="col"><?php _e('Location','my-calendar') ?></th>
	 <th class="manage-column" scope="col"><?php _e('Edit','my-calendar') ?></th>
	 <th class="manage-column" scope="col"><?php _e('Delete','my-calendar') ?></th>
       </tr>
       </thead>
       <?php
       $class = '';
       foreach ( $locations as $location ) {
	   $class = ($class == 'alternate') ? '' : 'alternate';
           ?>
         <tr class="<?php echo $class; ?>">
	     <th scope="row"><?php echo $location->location_id; ?></th>
	     <td><?php echo stripslashes($location->location_label) . "<br />" . stripslashes($location->location_street) . "<br />" . stripslashes($location->location_street2) . "<br />" . stripslashes($location->location_city) . ", " . stripslashes($location->location_state) . " " . stripslashes($location->location_postcode); ?></td>
	     <td><a href="<?php echo admin_url("admin.php?page=my-calendar-locations&amp;mode=edit&amp;location_id=$location->location_id"); ?>" class='edit'><?php _e('Edit','my-calendar'); ?></a></td>
         <td><a href="<?php echo admin_url("admin.php?page=my-calendar-locations&amp;mode=delete&amp;location_id=$location->location_id"); ?>" class="delete" onclick="return confirm('<?php _e('Are you sure you want to delete this category?','my-calendar'); ?>')"><?php _e('Delete','my-calendar'); ?></a></td>
         </tr>
                <?php
          }
      ?>
      </table>
      <?php
   } else {
     echo '<p>'.__('There are no locations in the database yet!','my-calendar').'</p>';
   }
?>
<p><em>
<?php _e('Please note: editing or deleting locations stored for re-use will have no effect on any event previously scheduled at that location. The location database exists purely as a shorthand method to enter frequently used locations into event records.','my-calendar'); ?>
</em></p>

  </div>
<?php
}