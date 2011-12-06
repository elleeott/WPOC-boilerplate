<link rel="stylesheet" type="text/css" href="<?php echo $this->dir ?>/css/acf.css" />
<script type="text/javascript" src="<?php echo $this->dir ?>/js/acf.js" ></script>
<?php
/*--------------------------------------------------------------------------------------
*
*	Screen Meta Content
*
*-------------------------------------------------------------------------------------*/
?>
<div id="screen-meta-activate-acf-wrap" class="screen-meta-wrap hidden acf">
	<div class="screen-meta-content">
		
		<h5><?php _e("Unlock Special Fields.",'acf'); ?></h5>
		<p><?php _e("Special Fields can be unlocked by purchasing a license key. Each key can be used on multiple sites.",'acf'); ?> <a href="http://plugins.elliotcondon.com/shop/"><?php _e("Visit the Plugin Store",'acf'); ?></a></p>
		<table class="acf_activate widefat">
			<thead>
				<tr>
					<th><?php _e("Field Type",'acf'); ?></th>
					<th><?php _e("Status",'acf'); ?></th>
					<th><?php _e("Activation Code",'acf'); ?></th>
				</tr>
			</thead>
			<tbody>
				<!-- Repeater Field -->
				<tr>
					<td><?php _e("Repeater Field",'acf'); ?></td>
					<td><?php echo $this->is_field_unlocked('repeater') ? __("Active",'acf') : __("Inactive",'acf'); ?></td>
					<td>
						<form action="" method="post">
							<?php if($this->is_field_unlocked('repeater')){
								echo '<span class="activation_code">XXXX-XXXX-XXXX-'.substr($this->get_license_key('repeater'),-4) .'</span>';
								echo '<input type="hidden" name="acf_field_deactivate" value="repeater" />';
								echo '<input type="submit" class="button" value="Deactivate" />';
							}
							else
							{
								echo '<input type="text" name="key" value="" />';
								echo '<input type="hidden" name="acf_field_activate" value="repeater" />';
								echo '<input type="submit" class="button" value="Activate" />';
							} ?>
						</form>
					</td>
				</tr>
				<!-- Options Page -->
				<tr>
					<td><?php _e("Options Page",'acf'); ?></td>
					<td><?php echo $this->is_field_unlocked('options_page') ? __("Active",'acf') : __("Inactive",'acf'); ?></td>
					<td>
						<form action="" method="post">
							<?php if($this->is_field_unlocked('options_page')){
								echo '<span class="activation_code">XXXX-XXXX-XXXX-'.substr($this->get_license_key('options_page'),-4) .'</span>';
								echo '<input type="hidden" name="acf_field_deactivate" value="options_page" />';
								echo '<input type="submit" class="button" value="Deactivate" />';
							}
							else
							{
								echo '<input type="text" name="key" value="" />';
								echo '<input type="hidden" name="acf_field_activate" value="options_page" />';
								echo '<input type="submit" class="button" value="Activate" />';
							} ?>
						</form>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div id="screen-meta-export-acf-wrap" class="screen-meta-wrap hidden acf">
	<div class="screen-meta-content">
		
		<form id="acf-screen-meta-form-1" method="post" action="<?php echo $this->dir; ?>/core/actions/export.php">
		
			<h5><?php _e("Export",'acf'); ?></h5>
			<p><?php _e("ACF will create a .xml export file which is compatible with the native WP import plugin.",'acf'); ?></p>
			
			<table class="acf_activate widefat">
			<thead>
				<tr>
					<th><?php _e("Select which ACF groups to export",'acf'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
					<?php
			
					$acfs = get_pages(array(
						'numberposts' 	=> 	-1,
						'post_type'		=>	'acf',
						'sort_column' => 'menu_order',
						'order' => 'ASC',
					));
		
					// blank array to hold acfs
					$acf_posts = array();
					
					if($acfs)
					{
						foreach($acfs as $acf)
						{
							$acf_posts[$acf->ID] = $acf->post_title;
						}
					}
					
					$this->create_field(array(
						'type'	=>	'select',
						'name'	=>	'acf_posts',
						'value'	=>	'',
						'choices'	=>	$acf_posts,
						'multiple'	=>	'1',
					));
					
					?>
						<input type="submit" class="button" name="acf_export" value="<?php _e("Export",'acf'); ?>" />
					</td>
				</tr>
			</tbody>
			</table>
		</form>
		
		<form id="acf-screen-meta-form-2">
			<h5><?php _e("Import",'acf'); ?></h5>
			<p><?php _e("Have an ACF export file? Import it here. Please note that v2 and v3 .xml files are not compatible.",'acf'); ?></p>
			
			<table class="acf_activate widefat">
			<thead>
				<tr>
					<th><?php _e("Import your .xml file",'acf'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<ol>
							<li>Navigate to the <a href="<?php echo admin_url(); ?>import.php">Import Tool</a> and select WordPress</li>
							<li>Install WP import plugin if prompted</li>
							<li>Upload and import your exported .xml file</li>
							<li>Select your user and ignore Import Attachments</li>
							<li>That's it! Happy WordPressing</li>
						</ol>
					</td>
				</tr>
			</tbody>
			</table>
		</form>
		
		<div class="clear"></div>
	</div>
</div>
<?php
/*--------------------------------------------------------------------------------------
*
*	Screen Meta Toggle Tabs
*
*-------------------------------------------------------------------------------------*/
?>
<div id="screen-meta-activate-acf-link-wrap" class="hide-if-no-js screen-meta-toggle acf">
	<a href="#screen-meta-activate-acf" id="screen-meta-activate-acf-link" class="show-settings"><?php _e("Unlock Fields",'acf'); ?></a>
</div>
<div id="screen-meta-export-acf-link-wrap" class="hide-if-no-js screen-meta-toggle acf">
	<a href="#screen-meta-export-acf" id="screen-meta-export-acf-link" class="show-settings"><?php _e("Import / Export",'acf'); ?></a>
</div>
<?php
/*--------------------------------------------------------------------------------------
*
*	Layout
*
*-------------------------------------------------------------------------------------*/
?>
<div class="acf_col_right hidden metabox-holder" id="poststuff" >

	<div class="postbox">
		<div class="handlediv"><br></div>
		<h3 class="hndle"><span><?php _e("Advanced Custom Fields v",'acf'); ?><?php echo $this->version; ?></span></h3>
		<div class="inside">
			<div class="field">
				<h4><?php _e("Changelog",'acf'); ?></h4>
				<p><?php _e("See what's new in",'acf'); ?> <a class="thickbox" href="<?php bloginfo('url'); ?>/wp-admin/plugin-install.php?tab=plugin-information&plugin=advanced-custom-fields&section=changelog&TB_iframe=true&width=640&height=559">v<?php echo $this->version; ?></a>
			</div>
			<div class="field">
				<h4><?php _e("Resources",'acf'); ?></h4>
				<p><?php _e("Watch tutorials, read documentation, learn the API code and find some tips &amp; tricks for your next web project.",'acf'); ?><br />
				<a href="http://plugins.elliotcondon.com/advanced-custom-fields/"><?php _e("View the plugins website",'acf'); ?></a></p>
			</div>
			<!-- <div class="field">
				<h4><?php _e("Support",'acf'); ?></h4>
				<p><?php _e("Join the growing community over at the support forum to share ideas, report bugs and keep up to date with ACF",'acf'); ?><br />
				<a href="http://support.plugins.elliotcondon.com/categories/advanced-custom-fields/"><?php _e("View the Support Forum",'acf'); ?></a></p>
			</div> -->
			<div class="field">
				<h4><?php _e("Developed by",'acf'); ?> Elliot Condon</h4>
				<p><a href="http://wordpress.org/extend/plugins/advanced-custom-fields/"><?php _e("Vote for ACF",'acf'); ?></a> | <a href="http://twitter.com/elliotcondon"><?php _e("Twitter",'acf'); ?></a> | <a href="http://blog.elliotcondon.com"><?php _e("Blog",'acf'); ?></a></p>
			</div>
			
		
		</div>
	</div>
</div>