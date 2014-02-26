<?php
function my_calendar_check_db() {
if ( get_option( 'mc_remote' ) == 'true' && function_exists('mc_remote_db') ) { return; }
	global $wpdb;
	$mcdb = $wpdb;
$row = $mcdb->get_row( 'SELECT * FROM '.my_calendar_table() );
	if ( isset( $_POST['upgrade'] ) && $_POST['upgrade'] == 'true' ) {
		$upgraded = my_calendar_mc_upgrade_db();
		?>
			<div class='upgrade-db updated'>
			<p>
			<?php _e('My Calendar Database is updated.','my-calendar'); ?>
			</p>
			</div>
			<?php
	} else if ( !isset( $row->event_hide_end ) && isset( $row->event_id ) ) {
		if ( $_GET['page'] == 'my-calendar-config' ) { ?>
	<div class='upgrade-db error'>
		<form method="post" action="<?php echo admin_url("admin.php?page=my-calendar-config"); ?>">
		<div>
		<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" />
		<input type="hidden" name="upgrade" value="true" />
		</div>
		<p>
		<?php _e('The My Calendar database needs to be updated.','my-calendar'); ?>
		<input type="submit" value="<?php _e('Update now','my-calendar'); ?>" name="update-calendar" class="button-primary" />
		</p>
		</form>
	</div>
	<?php } else { ?>
	<div class='upgrade-db error'>
		<p>
		<?php _e('The My Calendar database needs to be updated.','my-calendar'); ?> <a href="<?php echo admin_url("admin.php?page=my-calendar-config"); ?>"><?php _e('Update now.','my-calendar'); ?></a>
		</p>
	</div><?php 
		}
	} elseif ( !isset ( $row->event_id ) ) { ?>
	<div class='upgrade-db error'>
		<form method="post" action="<?php echo admin_url("admin.php?page=my-calendar-config"); ?>">
		<div>
		<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" />
		<input type="hidden" name="upgrade" value="true" />
		</div>
		<p>
		<?php _e('You haven\'t entered any events, so My Calendar can\'t tell whether your database is up to date. If you can\'t add events, upgrade your database!','my-calendar'); ?>
		<input type="submit" value="<?php _e('Update now','my-calendar'); ?>" name="update-calendar" class="button-primary" />
		</p>
		</form>
	</div><?php
	}
}

function my_calendar_mc_upgrade_db() {
global $mc_version,$initial_db,$initial_cat_db, $initial_loc_db, $initial_occur_db;
 	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$q_db = dbDelta($initial_db);
	$cat_db = dbDelta($initial_cat_db);
	$loc_db = dbDelta($initial_loc_db);	
	$occ_db = dbDelta($initial_occur_db);
	mc_migrate_db();
	update_option('mc_db_version',$mc_version);
	return true;
} ?>