<?php
// Display the admin configuration page
function edit_my_calendar_behaviors() {
  global $wpdb, $initial_listjs, $initial_caljs, $initial_minijs, $initial_ajaxjs;
	$mcdb = $wpdb;
  
  // We can't use this page unless My Calendar is installed/upgraded
  check_my_calendar();

  if ( isset($_POST['mc_caljs'] ) ) {
    $nonce=$_REQUEST['_wpnonce'];
    if (! wp_verify_nonce($nonce,'my-calendar-nonce') ) die("Security check failed");
	$mc_caljs = $_POST['mc_caljs'];
	$mc_listjs = $_POST['mc_listjs'];
	$mc_minijs = $_POST['mc_minijs'];
	$mc_ajaxjs = $_POST['mc_ajaxjs'];
	
	  update_option('mc_calendar_javascript', ( empty($_POST['calendar_javascript']) )?0:1);
	  update_option('mc_list_javascript', ( empty($_POST['list_javascript']) )?0:1 );
	  update_option('mc_mini_javascript', ( empty($_POST['mini_javascript']) )?0:1 );
	  update_option('mc_ajax_javascript', ( empty($_POST['ajax_javascript']) )?0:1 );
	  update_option('mc_draggable', ( empty($_POST['mc_draggable']) )?0:1 );
	  // set js
	  update_option('mc_listjs',$mc_listjs);
	  update_option('mc_minijs',$mc_minijs);
	  update_option('mc_caljs',$mc_caljs);
	  update_option('mc_ajaxjs',$mc_ajaxjs);
	  
	$mc_show_js = ($_POST['mc_show_js']=='')?'':$_POST['mc_show_js'];
	update_option('mc_show_js',$mc_show_js);   
	
		if ( !empty($_POST['reset_caljs']) ) {
			update_option('mc_caljs',$initial_caljs);
		}
		if ( !empty($_POST['reset_listjs']) ) {
			update_option('mc_listjs',$initial_listjs);
		}
		if ( !empty($_POST['reset_minijs']) ) {
			update_option('mc_minijs',$initial_minijs);
		}	
		if ( !empty($_POST['reset_ajaxjs']) ) {
			update_option('mc_ajaxjs',$initial_ajaxjs);
		}
		echo "<div class=\"updated\"><p><strong>".__('Behavior Settings saved','my-calendar').".</strong></p></div>";
    }

	$mc_listjs = stripcslashes(get_option('mc_listjs'));
	$list_javascript = get_option('mc_list_javascript');

	$mc_caljs = stripcslashes(get_option('mc_caljs'));
	$calendar_javascript = get_option('mc_calendar_javascript');

	$mc_minijs = stripcslashes(get_option('mc_minijs'));
	$mini_javascript = get_option('mc_mini_javascript'); 

	$mc_ajaxjs = stripcslashes(get_option('mc_ajaxjs'));
	$ajax_javascript = get_option('mc_ajax_javascript'); 
	
	$mc_show_js = stripcslashes(get_option('mc_show_js'));
	$mc_draggable = get_option('mc_draggable');

  // Now we render the form
  ?>
    <div class="wrap jd-my-calendar">
<?php 
my_calendar_check_db();
?>
    <h2><?php _e('My Calendar Behaviors','my-calendar'); ?></h2>
<div class="postbox-container" style="width: 70%">
<div class="metabox-holder">

<div class="ui-sortable meta-box-sortables">
<div class="postbox" id="cdiff">
	
	<h3><?php _e('Calendar Behavior Settings','my-calendar'); ?></h3>
	<div class="inside">	
    <form id="my-calendar" method="post" action="<?php echo admin_url('admin.php?page=my-calendar-behaviors'); ?>">
	<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" /></div>
	<p>
	<label for="mc_show_js"><?php _e('Insert scripts on these pages (comma separated post IDs)','my-calendar'); ?></label> <input type="text" id="mc_show_js" name="mc_show_js" value="<?php echo $mc_show_js; ?>" />
	</p>  
	<p>
	<input type="checkbox" id="mc_draggable" name="mc_draggable" value="1" <?php mc_is_checked('mc_draggable',1); ?> /> <label for="mc_draggable"><?php _e('Details boxes are draggable','my-calendar'); ?></label>
	</p>
	<fieldset>
	<legend><?php _e('Calendar Behaviors: Calendar View','my-calendar'); ?></legend>
	<p>
	<input type="checkbox" id="reset_caljs" name="reset_caljs" /> <label for="reset_caljs"><?php _e('Update/Reset the My Calendar Calendar Javascript','my-calendar'); ?></label> <input type="checkbox" id="calendar_javascript" name="calendar_javascript" value="1"  <?php mc_is_checked('mc_calendar_javascript',1); ?>/> <label for="calendar_javascript"><?php _e('Disable Calendar Javascript Effects','my-calendar'); ?></label>
	</p>
	<p>
	<label for="calendar-javascript"><?php _e('Edit the jQuery scripts for My Calendar in Calendar format','my-calendar'); ?></label><br /><textarea id="calendar-javascript" name="mc_caljs" rows="8" cols="80"><?php echo $mc_caljs; ?></textarea>
	</p>
	<?php
	$left_string  = normalize_whitespace($mc_caljs);
	$right_string = normalize_whitespace($initial_caljs);
	if ( isset( $_GET['cdiff'] ) ) {
		echo '<div class="wrap jd-my-calendar" id="diff">';
		echo wp_text_diff( $left_string,$right_string, array( 'title' => __('Comparing scripts with latest installed version of My Calendar','my-calendar'), 'title_right' => __('Latest (from plugin)','my-calendar'), 'title_left' => __('Current (in use)','my-calendar') ) );
		echo '</div>';
	} else if ( trim($left_string)!=trim($right_string) ) {
		echo '<div class="wrap jd-my-calendar">';
		echo '<div class="updated"><p>'.__('There have been updates to the calendar view scripts.','my-calendar').' <a href="'.admin_url('admin.php?page=my-calendar-behaviors&amp;cdiff#cdiff').'">'.__('Compare your scripts with latest installed version of My Calendar.','my-calendar').'</a></p></div>';
		echo '</div>';
	} else {
		echo '<div class="wrap jd-my-calendar"><em>';
			_e('Your script matches that included with My Calendar.','my-calendar');
		echo '</em></div>';
	}
	?>	
	<p>
		<input type="submit" name="save" class="button-secondary" value="<?php _e('Save','my-calendar'); ?>" />
	</p>	
	</fieldset>
	

	
    <fieldset id="ldiff">
	<legend><?php _e('Calendar Behaviors: List View','my-calendar'); ?></legend>
	<p>
	<input type="checkbox" id="reset_listjs" name="reset_listjs" /> <label for="reset_listjs"><?php _e('Update/Reset the My Calendar List Javascript','my-calendar'); ?></label> <input type="checkbox" id="list_javascript" name="list_javascript" value="1" <?php mc_is_checked('mc_list_javascript',1); ?> /> <label for="list_javascript"><?php _e('Disable List Javascript Effects','my-calendar'); ?></label> 
	</p>
	<p>
	<label for="list-javascript"><?php _e('Edit the jQuery scripts for My Calendar in List format','my-calendar'); ?></label><br /><textarea id="list-javascript" name="mc_listjs" rows="8" cols="80"><?php echo $mc_listjs; ?></textarea>
	</p>
	<?php
	$left_string  = normalize_whitespace($mc_listjs);
	$right_string = normalize_whitespace($initial_listjs);
	if ( isset( $_GET['ldiff'] ) ) {
		echo '<div class="wrap jd-my-calendar" id="diff">';
		echo wp_text_diff( $left_string,$right_string, array( 'title' => __('Comparing scripts with latest installed version of My Calendar','my-calendar'), 'title_right' => __('Latest (from plugin)','my-calendar'), 'title_left' => __('Current (in use)','my-calendar') ) );
		echo '</div>';
	} else if ( trim($left_string)!=trim($right_string) ) {
		echo '<div class="wrap jd-my-calendar">';
		echo '<div class="updated"><p>'.__('There have been updates to the list view scripts.','my-calendar').' <a href="'.admin_url('admin.php?page=my-calendar-behaviors&amp;ldiff#ldiff').'">'.__('Compare your scripts with latest installed version of My Calendar.','my-calendar').'</a></p></div>';
		echo '</div>';
	} else {
		echo '<div class="wrap jd-my-calendar"><em>';
			_e('Your script matches that included with My Calendar.','my-calendar');
		echo '</em></div>';
	}
	?>	
	<p>
		<input type="submit" name="save" class="button-secondary" value="<?php _e('Save','my-calendar'); ?>" />
	</p>	
	</fieldset>

	
   <fieldset id="mdiff">
	<legend><?php _e('Calendar Behaviors: Mini Calendar View','my-calendar'); ?></legend>
	<p>
	<input type="checkbox" id="reset_minijs" name="reset_minijs" /> <label for="reset_minijs"><?php _e('Update/Reset the My Calendar Mini Format Javascript','my-calendar'); ?></label> <input type="checkbox" id="mini_javascript" name="mini_javascript" value="1" <?php mc_is_checked('mc_mini_javascript',1); ?> /> <label for="mini_javascript"><?php _e('Disable Mini Javascript Effects','my-calendar'); ?></label> 
	</p>
	<p>
	<label for="mini-javascript"><?php _e('Edit the jQuery scripts for My Calendar in Mini Calendar format','my-calendar'); ?></label><br /><textarea id="mini-javascript" name="mc_minijs" rows="8" cols="80"><?php echo $mc_minijs; ?></textarea>
	</p>
	<?php
	$left_string  = normalize_whitespace($mc_minijs);
	$right_string = normalize_whitespace($initial_minijs);
	if ( isset( $_GET['mdiff'] ) ) {
		echo '<div class="wrap jd-my-calendar" id="diff">';
		echo wp_text_diff( $left_string,$right_string, array( 'title' => __('Comparing scripts with latest installed version of My Calendar','my-calendar'), 'title_right' => __('Latest (from plugin)','my-calendar'), 'title_left' => __('Current (in use)','my-calendar') ) );
		echo '</div>';
	} else if ( trim($left_string)!=trim($right_string) ) {
		echo '<div class="wrap jd-my-calendar">';
		echo '<div class="updated"><p>'.__('There have been updates to the mini view scripts.','my-calendar').' <a href="'.admin_url('admin.php?page=my-calendar-behaviors&amp;mdiff#mdiff').'">'.__('Compare your scripts with latest installed version of My Calendar.','my-calendar').'</a></p></div>';
		echo '</div>';
	} else {
		echo '<div class="wrap jd-my-calendar"><em>';
			_e('Your script matches that included with My Calendar.','my-calendar');
		echo '</em></div>';
	}
	?>
	<p>
		<input type="submit" name="save" class="button-secondary" value="<?php _e('Save','my-calendar'); ?>" />
	</p>	
	</fieldset>
	

	
    <fieldset id="adiff">
	<legend><?php _e('Calendar Behaviors: AJAX Navigation','my-calendar'); ?></legend>
	<p>
	<input type="checkbox" id="reset_ajaxjs" name="reset_ajaxjs" /> <label for="reset_ajaxjs"><?php _e('Update/Reset the My Calendar AJAX Javascript','my-calendar'); ?></label> <input type="checkbox" id="ajax_javascript" name="ajax_javascript" value="1" <?php mc_is_checked('mc_ajax_javascript',1); ?> /> <label for="ajax_javascript"><?php _e('Disable AJAX Effects','my-calendar'); ?></label> 
	</p>
	<p>
	<label for="ajax-javascript"><?php _e('Edit the jQuery scripts for My Calendar AJAX navigation','my-calendar'); ?></label><br /><textarea id="ajax-javascript" name="mc_ajaxjs" rows="8" cols="80"><?php echo $mc_ajaxjs; ?></textarea>
	</p>
	<?php
	$left_string  = normalize_whitespace($mc_ajaxjs);
	$right_string = normalize_whitespace($initial_ajaxjs);
	if ( isset( $_GET['adiff'] ) ) {
		echo '<div class="wrap jd-my-calendar" id="diff">';
		echo wp_text_diff( $left_string,$right_string, array( 'title' => __('Comparing scripts with latest installed version of My Calendar','my-calendar'), 'title_right' => __('Latest (from plugin)','my-calendar'), 'title_left' => __('Current (in use)','my-calendar') ) );
		echo '</div>';
	} else if ( trim($left_string)!=trim($right_string) ) {
		echo '<div class="wrap jd-my-calendar">';
		echo '<div class="updated"><p>'.__('There have been updates to the AJAX scripts.','my-calendar').' <a href="'.admin_url('admin.php?page=my-calendar-behaviors&amp;adiff#adiff').'">'.__('Compare your scripts with latest installed version of My Calendar.','my-calendar').'</a></p></div>';
		echo '</div>';
	} else {
		echo '<div class="wrap jd-my-calendar"><em>';
			_e('Your script matches that included with My Calendar.','my-calendar');
		echo '</em></div>';
	}
	?>		
	<p>
		<input type="submit" name="save" class="button-secondary" value="<?php _e('Save','my-calendar'); ?>" />
	</p>	
	</fieldset>	

	<p>
		<input type="submit" name="save" class="button-primary" value="<?php _e('Save','my-calendar'); ?>" />
	</p>		
  </form>
  </div>
 </div>
</div>
<p><?php _e('Resetting JavaScript will set that script to the version currently distributed with the plug-in.','my-calendar'); ?></p>
 </div>
 </div>
 <?php jd_show_support_box(); ?>

 </div>
<?php }