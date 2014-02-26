<?php
// Display the admin configuration page
function edit_mc_templates() {
	global $wpdb;
	$mcdb = $wpdb;
	// We can't use this page unless My Calendar is installed/upgraded
	check_my_calendar();
	$templates = get_option( 'mc_templates' );

	if ( isset($_POST['mc_grid_template'] ) ) {
		$nonce=$_REQUEST['_wpnonce'];
		if ( !wp_verify_nonce($nonce,'my-calendar-nonce') ) die("Security check failed");

		$mc_grid_template = $_POST['mc_grid_template'];
		$templates['grid'] = $mc_grid_template;
		update_option( 'mc_templates', $templates );
		update_option( 'mc_use_grid_template',( empty($_POST['mc_use_grid_template'])?0:1 ) );

		echo "<div class=\"updated\"><p><strong>".__('Grid Output Template saved','my-calendar').".</strong></p></div>";
	}
	
	if ( isset($_POST['mc_rss_template'] ) ) {
		$nonce=$_REQUEST['_wpnonce'];
		if ( !wp_verify_nonce($nonce,'my-calendar-nonce') ) die("Security check failed");

		$mc_rss_template = $_POST['mc_rss_template'];
		$templates['rss'] = $mc_rss_template;
		update_option( 'mc_templates', $templates );
		update_option( 'mc_use_rss_template',( empty($_POST['mc_use_rss_template'])?0:1 ) );

		echo "<div class=\"updated\"><p><strong>".__('RSS Feed Output Template saved','my-calendar').".</strong></p></div>";
	}	
	
	if ( isset($_POST['mc_list_template'] ) ) {
		$nonce=$_REQUEST['_wpnonce'];
		if ( !wp_verify_nonce($nonce,'my-calendar-nonce') ) die("Security check failed");

		$mc_list_template = $_POST['mc_list_template'];
		$templates['list'] = $mc_list_template;
		update_option( 'mc_templates', $templates );
		update_option( 'mc_use_list_template',( empty($_POST['mc_use_list_template'])?0:1 ) );

		echo "<div class=\"updated\"><p><strong>".__('List Output Template saved','my-calendar').".</strong></p></div>";
	}

	if ( isset($_POST['mc_mini_template'] ) ) {
		$nonce=$_REQUEST['_wpnonce'];
		if ( !wp_verify_nonce($nonce,'my-calendar-nonce') ) die("Security check failed");

		$mc_mini_template = $_POST['mc_mini_template'];
		$templates['mini'] = $mc_mini_template;
		update_option( 'mc_templates', $templates );
		update_option( 'mc_use_mini_template',( empty($_POST['mc_use_mini_template'])?0:1 ) );
		echo "<div class=\"updated\"><p><strong>".__('Mini Output Template saved','my-calendar').".</strong></p></div>";
	}

	if ( isset($_POST['mc_details_template'] ) ) {
		$nonce=$_REQUEST['_wpnonce'];
		if ( !wp_verify_nonce($nonce,'my-calendar-nonce') ) die("Security check failed");

		$mc_details_template = $_POST['mc_details_template'];
		$templates['details'] = $mc_details_template;
		update_option( 'mc_templates', $templates );
		update_option( 'mc_use_details_template',( empty($_POST['mc_use_details_template'])?0:1 ) );
		echo "<div class=\"updated\"><p><strong>".__('Event Details Template saved','my-calendar').".</strong></p></div>";
	}	
	global $grid_template, $list_template, $mini_template, $single_template, $rss_template;
	$mc_grid_template = stripslashes( ($templates['grid']!='')?$templates['grid']:$grid_template );
	$mc_use_grid_template = get_option('mc_use_grid_template');
	$mc_rss_template = stripslashes( ($templates['rss']!='')?$templates['rss']:$rss_template );
	$mc_use_rss_template = get_option('mc_use_rss_template');	
	$mc_list_template = stripslashes( ($templates['list']!='')?$templates['list']:$list_template );
	$mc_use_list_template = get_option('mc_use_list_template');
	$mc_mini_template = stripslashes( ($templates['mini']!='')?$templates['mini']:$mini_template );
	$mc_use_mini_template = get_option('mc_use_mini_template');
	$mc_details_template = stripslashes( ($templates['details']!='')?$templates['details']:$single_template );
	$mc_use_details_template = get_option('mc_use_details_template');	
?>
    <div class="wrap jd-my-calendar">
	
	
<?php my_calendar_check_db(); ?>
    <h2><?php _e('My Calendar Information Templates','my-calendar'); ?></h2>
	
<div class="postbox-container" style="width: 70%">
<div class="metabox-holder">
	
	<p><?php _e('Advanced users may customize the HTML template for each event. This page lets you create a customized view of your events in each context. All available template tags are documented on the Help page. These default templates are based on the default views with all output enabled. <strong>Custom templates will override any other output rules in your settings.</strong>','my-calendar'); ?> <a href="<?php echo admin_url("admin.php?page=my-calendar-help#templates"); ?>"><?php _e("Templates Help",'my-calendar'); ?></a> &raquo;</p>


	<div class="ui-sortable meta-box-sortables">   
	<div class="postbox">
		<h3><?php _e('My Calendar: Grid Event Template','my-calendar'); ?></h3>
		<div class="inside">	
		<form method="post" action="<?php echo admin_url("admin.php?page=my-calendar-templates"); ?>">
		<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" /></div>
		<p>
		<input type="checkbox" id="mc_use_grid_template" name="mc_use_grid_template" value="1"  <?php mc_is_checked('mc_use_grid_template',1); ?>/> <label for="mc_use_grid_template"><?php _e('Use this grid event template','my-calendar'); ?></label>
		</p>
		<p>
		<label for="mc_grid_template"><?php _e('Your custom template for events in the calendar grid output.','my-calendar'); ?></label><br /><textarea id="mc_grid_template" name="mc_grid_template" class="template-editor" rows="12" cols="76"><?php echo $mc_grid_template; ?></textarea>
		</p>
		<p>
			<input type="submit" name="save" class="button-primary" value="<?php _e('Save Grid Template','my-calendar'); ?>" />
		</p>
		</form>
		</div>
	</div>
	</div>

	<div class="ui-sortable meta-box-sortables">   
	<div class="postbox">
		<h3><?php _e('My Calendar: List Event Template','my-calendar'); ?></h3>
		<div class="inside">	
		<form method="post" action="<?php echo admin_url("admin.php?page=my-calendar-templates"); ?>">
		<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" /></div>
		<p>
		<input type="checkbox" id="mc_use_list_template" name="mc_use_list_template" value="1"  <?php mc_is_checked('mc_use_list_template',1); ?>/> <label for="mc_use_list_template"><?php _e('Use this list event template','my-calendar'); ?></label>
		</p>
		<p>
		<label for="mc_list_template"><?php _e('Your custom template for events in calendar list output.','my-calendar'); ?></label><br /><textarea id="mc_list_template" name="mc_list_template" class="template-editor" rows="12" cols="76"><?php echo $mc_list_template; ?></textarea>
		</p>
		<p>
			<input type="submit" name="save" class="button-primary" value="<?php _e('Save List Template','my-calendar'); ?>" />
		</p>
		</form>
		</div>
	</div>
	</div>

	<div class="ui-sortable meta-box-sortables">   
	<div class="postbox">
		<h3><?php _e('My Calendar: Mini Calendar Template','my-calendar'); ?></h3>
		<div class="inside">	
		<form method="post" action="<?php echo admin_url("admin.php?page=my-calendar-templates"); ?>">
		<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" /></div>
		<p>
		<input type="checkbox" id="mc_use_mini_template" name="mc_use_mini_template" value="1"  <?php mc_is_checked('mc_use_mini_template',1); ?>/> <label for="mc_use_mini_template"><?php _e('Use this mini event template','my-calendar'); ?></label>
		</p>
		<p>
		<label for="mc_mini_template"><?php _e('Your custom template for events in sidebar/mini calendar output.','my-calendar'); ?></label><br /><textarea id="mc_mini_template" name="mc_mini_template" rows="12" cols="76" class="template-editor"><?php echo $mc_mini_template; ?></textarea>
		</p>
		<p>
			<input type="submit" name="save" class="button-primary" value="<?php _e('Save Mini Template','my-calendar'); ?>" />
		</p>
		</form>
		</div>
	</div>
	</div>

	<div class="ui-sortable meta-box-sortables">   
	<div class="postbox">
		<h3><?php _e('My Calendar: Event Details Page Template','my-calendar'); ?></h3>
		<div class="inside">	
		<form method="post" action="<?php echo admin_url("admin.php?page=my-calendar-templates"); ?>">
		<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" /></div>
		<p>
		<input type="checkbox" id="mc_use_details_template" name="mc_use_details_template" value="1"  <?php mc_is_checked('mc_use_details_template',1); ?>/> <label for="mc_use_details_template"><?php _e('Use this details template','my-calendar'); ?></label>
		</p>
		<p>
		<label for="mc_details_template"><?php _e('Your custom template for events on the event details page.','my-calendar'); ?></label><br /><textarea id="mc_details_template" name="mc_details_template" rows="12" cols="76" class="template-editor"><?php echo $mc_details_template; ?></textarea>
		</p>
		<p>
			<input type="submit" name="save" class="button-primary" value="<?php _e('Save Details Template','my-calendar'); ?>" />
		</p>
		</form>
		</div>
	</div>
	</div>
	
	<div class="ui-sortable meta-box-sortables">   
	<div class="postbox">
		<h3><?php _e('My Calendar: RSS Event Template','my-calendar'); ?></h3>
		<div class="inside">
		<p><?php _e('Notice: HTML templates are very forgiving of errors. RSS templates are not. Be sure to test your changes.','my-calendar'); ?></p>
		<form method="post" action="<?php echo admin_url("admin.php?page=my-calendar-templates"); ?>">
		<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" /></div>
		<p>
		<input type="checkbox" id="mc_use_rss_template" name="mc_use_rss_template" value="1"  <?php mc_is_checked('mc_use_rss_template',1); ?>/> <label for="mc_use_grid_template"><?php _e('Use this custom RSS event template','my-calendar'); ?></label>
		</p>
		<p>
		<label for="mc_rss_template"><?php _e('Your custom template for events in the RSS feed.','my-calendar'); ?></label><br /><textarea id="mc_rss_template" name="mc_rss_template" class="template-editor" rows="12" cols="76"><?php echo $mc_rss_template; ?></textarea>
		</p>
		<p>
			<input type="submit" name="save" class="button-primary" value="<?php _e('Save RSS Template','my-calendar'); ?>" />
		</p>
		</form>
		</div>
	</div>
	</div>	
	
</div>
</div>

	<?php jd_show_support_box('templates'); ?>

<?php
}
?>