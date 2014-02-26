<?php
/*
Plugin Name: My Calendar
Plugin URI: http://www.joedolson.com/articles/my-calendar/
Description: Accessible WordPress event calendar plugin. Show events from multiple calendars on pages, in posts, or in widgets.
Author: Joseph C Dolson
Author URI: http://www.joedolson.com
Version: 2.1.0
*/
/*  Copyright 2009-2012  Joe Dolson (email : joe@joedolson.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
global $mc_version, $wpdb;
$mc_version = '2.1.0';

// Define the tables used in My Calendar
if ( function_exists('is_multisite') && is_multisite() && get_site_option('mc_multisite_show') == 1 ) {
	define('MY_CALENDAR_TABLE', $wpdb->base_prefix . 'my_calendar');
	define('MY_CALENDAR_EVENTS_TABLE', $wpdb->base_prefix . 'my_calendar_events');	
	define('MY_CALENDAR_CATEGORIES_TABLE', $wpdb->base_prefix . 'my_calendar_categories');
	define('MY_CALENDAR_LOCATIONS_TABLE', $wpdb->base_prefix . 'my_calendar_locations');
} else {
	define('MY_CALENDAR_TABLE', $wpdb->prefix . 'my_calendar');
	define('MY_CALENDAR_EVENTS_TABLE', $wpdb->prefix . 'my_calendar_events');	
	define('MY_CALENDAR_CATEGORIES_TABLE', $wpdb->prefix . 'my_calendar_categories');
	define('MY_CALENDAR_LOCATIONS_TABLE', $wpdb->prefix . 'my_calendar_locations');
}

if ( function_exists('is_multisite') && is_multisite() ) {
	// Define the tables used in My Calendar
	define('MY_CALENDAR_GLOBAL_TABLE', $wpdb->base_prefix . 'my_calendar');
	define('MY_CALENDAR_GLOBAL_EVENT_TABLE', $wpdb->base_prefix . 'my_calendar_events');
	define('MY_CALENDAR_GLOBAL_CATEGORIES_TABLE', $wpdb->base_prefix . 'my_calendar_categories');
	define('MY_CALENDAR_GLOBAL_LOCATIONS_TABLE', $wpdb->base_prefix . 'my_calendar_locations');
}

include(dirname(__FILE__).'/my-calendar-core.php' );
include(dirname(__FILE__).'/my-calendar-install.php' );
include(dirname(__FILE__).'/my-calendar-settings.php' );
include(dirname(__FILE__).'/my-calendar-categories.php' );
include(dirname(__FILE__).'/my-calendar-locations.php' );
include(dirname(__FILE__).'/my-calendar-help.php' );
include(dirname(__FILE__).'/my-calendar-event-manager.php' );
include(dirname(__FILE__).'/my-calendar-styles.php' );
include(dirname(__FILE__).'/my-calendar-behaviors.php' );
include(dirname(__FILE__).'/my-calendar-widgets.php' );
include(dirname(__FILE__).'/date-utilities.php' );
include(dirname(__FILE__).'/my-calendar-upgrade-db.php' );
include(dirname(__FILE__).'/my-calendar-user.php' );
include(dirname(__FILE__).'/my-calendar-output.php' );
include(dirname(__FILE__).'/my-calendar-templates.php' );
include(dirname(__FILE__).'/my-calendar-rss.php' );
include(dirname(__FILE__).'/my-calendar-ical.php' );
include(dirname(__FILE__).'/my-calendar-events.php' );
include(dirname(__FILE__).'/my-calendar-limits.php' );
include(dirname(__FILE__).'/my-calendar-shortcodes.php' );
include(dirname(__FILE__).'/my-calendar-detect-mobile.php' );
include(dirname(__FILE__).'/my-calendar-templating.php' );
include(dirname(__FILE__).'/my-calendar-group-manager.php' );
include(dirname(__FILE__).'/my-calendar-export.php' );
 
// Install on activation
register_activation_hook( __FILE__, 'check_my_calendar' );

// Enable internationalisation
load_plugin_textdomain( 'my-calendar',false, dirname( plugin_basename( __FILE__ ) ) . '/lang' ); 

if ( version_compare( get_bloginfo( 'version' ) , '3.0' , '<' ) && is_ssl() ) {
	$wp_content_url = str_replace( 'http://' , 'https://' , get_option( 'siteurl' ) );
} else {
	$wp_content_url = get_option( 'siteurl' );
}

$wp_plugin_url = plugin_dir_url( __FILE__ );
$wp_plugin_dir = plugin_dir_path( __FILE__ );

// Add actions
add_action( 'admin_menu', 'my_calendar_menu' );
add_action( 'wp_head', 'my_calendar_wp_head' );
add_action( 'delete_user', 'mc_deal_with_deleted_user' );
add_action( 'widgets_init', create_function('', 'return register_widget("my_calendar_today_widget");') );
add_action( 'widgets_init', create_function('', 'return register_widget("my_calendar_upcoming_widget");') );
add_action( 'widgets_init', create_function('', 'return register_widget("my_calendar_mini_widget");') );
add_action( 'show_user_profile', 'mc_user_profile' );
add_action( 'edit_user_profile', 'mc_user_profile' );
add_action( 'profile_update', 'mc_user_save_profile');
add_action( 'init', 'my_calendar_add_feed' );
add_action( 'admin_menu', 'my_calendar_add_javascript' );
add_action( 'wp_footer','mc_footer_js' );
add_action( 'wp_head','my_calendar_fouc' );
add_action( 'wp_enqueue_scripts','mc_enqueue' );
add_action( 'init', 'my_calendar_export_vcal', 200 );
// Add filters 
add_filter( 'widget_text', 'do_shortcode', 9 );
add_filter('plugin_action_links', 'jd_calendar_plugin_action', -10, 2);

// produce admin support box
function jd_show_support_box( $show='', $add=false, $remove=false ) {

if ( current_user_can('mc_view_help') ) {
?>
	<div class="postbox-container" style="width:20%">
	<div class="metabox-holder">
		<?php if ( !$remove ) { ?>
		<?php if ( !function_exists('mcs_submit_exists') ) { ?>
		<div class="ui-sortable meta-box-sortables">
			<div class="postbox support">
			<h3><strong><?php _e('My Calendar: Submissions','my-calendar'); ?></strong></h3>
			<div class="inside resources">
				<p class="mcsbuy"><img src="<?php echo plugins_url('my-calendar/images/submissions.png'); ?>" alt="My Calendar: Submissions" class="alignleft" /><?php _e("Buy the <a href='http://www.joedolson.com/articles/my-calendar/submissions/' rel='external'>My Calendar: Submissions add-on</a> &mdash; let your site's visitors help build your calendar.",'my-calendar'); ?></p>
				<p class="mc-button"><a href="http://www.joedolson.com/articles/my-calendar/submissions/" rel="external"><?php _e('Learn more!','my-calendar'); ?></a></p>
			</div>
			</div>
		</div>	
		<?php } ?>
		<div class="ui-sortable meta-box-sortables">
			<div class="postbox support">
			<h3><strong><?php _e('Support This Plug-in','my-calendar'); ?></strong></h3>
			<div class="inside resources">
				<p class="mcbuy"><img src="<?php echo plugins_url('my-calendar/images/guide.png'); ?>" alt="My Calendar User's Guide" class="alignleft" /><?php _e('Help me help you:','my-calendar'); ?> <a href="http://www.joedolson.com/articles/my-calendar/users-guide/" rel="external"><?php _e("Buy the My Calendar User's Guide",'my-calendar'); ?></a></p>
				<p><?php _e('<strong>Or make a donation today!</strong> Every donation counts - donate $2, $10, or $100 and help me keep this plug-in running!','my-calendar'); ?></p>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<p class="mcd">
					<input type="hidden" name="cmd" value="_s-xclick" />
					<input type="hidden" name="hosted_button_id" value="UZBQUG2LKKMRW" />
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" name="submit" alt="<?php _e('Make a Donation','my-calendar'); ?>" />
					<img alt="" src="https://www.paypalobjects.com/WEBSCR-640-20110429-1/en_US/i/scr/pixel.gif" width="1" height="1" />
				</p>
				<p class="mcd"><strong><a href="http://www.joedolson.com/donate.php" rel="external"><?php _e("Make a Donation",'my-calendar'); ?></a></strong></p>								
				</form>
			</div>
			</div>
		</div>
		<?php } ?>
		<div class="ui-sortable meta-box-sortables">
			<div class="postbox">
			<h3><?php _e('Get Help','my-calendar'); ?></h3>
			<div class="inside">
				<ul>
					<li><strong><a href="<?php echo admin_url("admin.php?page=my-calendar-help"); ?>#get-started"><?php _e("Getting Started",'my-calendar'); ?></strong></a></li>								
					<li><a href="<?php echo admin_url("admin.php?page=my-calendar-help"); ?>#get-support"><?php _e("Get Support",'my-calendar'); ?></a></li>
					<li><a href="<?php echo admin_url("admin.php?page=my-calendar-help"); ?>"><?php _e("My Calendar Help",'my-calendar'); ?></a></li>				
					<li><a href="http://profiles.wordpress.org/users/joedolson/"><?php _e('Check out my other plug-ins','my-calendar'); ?></a></li>
					<li><a href="http://wordpress.org/extend/plugins/my-calendar/"><?php _e('Rate this plug-in 5 stars!','my-calendar'); ?></a></li>
					<li><a href="http://translate.joedolson.com/projects/my-calendar"><?php _e('Help translate this plug-in!','my-calendar'); ?></a></li>					</ul>
			</div>
			</div>
		</div>
		<?php if ( is_array( $add ) ) {
			foreach ( $add as $key=>$value ) {
				?>
		<div class="ui-sortable meta-box-sortables">
			<div class="postbox">
			<h3><?php echo $key; ?></h3>	
				<div class='<?php echo sanitize_title($key); ?> inside'>
				<?php echo $value; ?>
				</div>
			</div>
		</div>
				<?php
			}
		} ?>
		<?php if ( $show == 'templates' ) { ?>
		<div class="ui-sortable meta-box-sortables">
			<div class="postbox">
			<h3><?php _e('Event Template Tags','my-calendar'); ?></h3>	
				<div class='mc_template_tags inside'>
		<dl>
		<dt><code>{title}</code></dt>
		<dd><?php _e('Title of the event.','my-calendar'); ?></dd>

		<dt><code>{link_title}</code></dt>
		<dd><?php _e('Title of the event as a link if a URL is present, or the title alone if not.','my-calendar'); ?></dd>

		<dt><code>{time}</code></dt>
		<dd><?php _e('Start time for the event.','my-calendar'); ?></dd>

		<dt><code>{usertime}</code>/<code>{endusertime}</code></dt>
		<dd><?php _e('Event times adjusted to the current user\'s time zone if set.','my-calendar'); ?></dd>

		<dt><code>{date}</code></dt>
		<dd><?php _e('Date on which the event begins.','my-calendar'); ?></dd>

		<dt><code>{enddate}</code></dt>
		<dd><?php _e('Date on which the event ends.','my-calendar'); ?></dd>

		<dt><code>{endtime}</code></dt>
		<dd><?php _e('Time at which the event ends.','my-calendar'); ?></dd>

		<dt><code>{daterange}</code></dt>
		<dd><?php _e('Beginning date to end date; excludes end date if same as beginning.','my-calendar'); ?></dd>

		<dt><code>{multidate}</code></dt>
		<dd><?php _e('Multi-day events: an unordered list of dates/times. Otherwise, beginning date/time.','my-calendar'); ?></dd>

		<dt><code>{author}</code></dt>
		<dd><?php _e('Author who posted the event.','my-calendar'); ?></dd>

		<dt><code>{host}</code></dt>
		<dd><?php _e('Name of the assigned host for the event.','my-calendar'); ?></dd>

		<dt><code>{host_email}</code></dt>
		<dd><?php _e('Email for the person assigned as host.','my-calendar'); ?></dd>

		<dt><code>{shortdesc}</code></dt>
		<dd><?php _e('Short event description.','my-calendar'); ?></dd>

		<dt><code>{description}</code></dt>
		<dd><?php _e('Description of the event.','my-calendar'); ?></dd>

		<dt><code>{image}</code></dt>
		<dd><?php _e('Image associated with the event.','my-calendar'); ?></dd>

		<dt><code>{link}</code></dt>
		<dd><?php _e('URL provided for the event.','my-calendar'); ?></dd>

		<dt><code>{details}</code></dt>
		<dd><?php _e('Link to an auto-generated page containing information about the event.','my-calendar'); ?>

		<dt><code>{event_open}</code></dt>
		<dd><?php _e('Whether event is currently open for registration.','my-calendar'); ?></dd>

		<dt><code>{event_status}</code></dt>
		<dd><?php _e('Current status of event: either "Published" or "Reserved."','my-calendar'); ?></dd>
		</dl>
		<h4><?php _e('Location Template Tags','my-calendar'); ?></h4>

		<dl>
		<dt><code>{location}</code></dt>
		<dd><?php _e('Name of the location of the event.','my-calendar'); ?></dd>

		<dt><code>{street}</code></dt>
		<dd><?php _e('First line of the site address.','my-calendar'); ?></dd>

		<dt><code>{street2}</code></dt>
		<dd><?php _e('Second line of the site address.','my-calendar'); ?></dd>

		<dt><code>{city}</code></dt>
		<dd><?php _e('City.','my-calendar'); ?></dd>

		<dt><code>{state}</code></dt>
		<dd><?php _e('State.','my-calendar'); ?></dd>

		<dt><code>{postcode}</code></dt>
		<dd><?php _e('Postal code/zip code.','my-calendar'); ?></dd>

		<dt><code>{region}</code></dt>
		<dd><?php _e('Custom region.','my-calendar'); ?></dd>

		<dt><code>{country}</code></dt>
		<dd><?php _e('Country for the event location.','my-calendar'); ?></dd>

		<dt><code>{sitelink}</code></dt>
		<dd><?php _e('Output the URL for the location.','my-calendar'); ?></dd>

		<dt><code>{hcard}</code></dt>
		<dd><?php _e('Event address in <a href="http://microformats.org/wiki/hcard">hcard</a> format.','my-calendar'); ?></dd>

		<dt><code>{link_map}</code></dt>
		<dd><?php _e('Link to Google Map to the event, if address information is available.','my-calendar'); ?></dd>
		</dl>
		<h4><?php _e('Category Template Tags','my-calendar'); ?></h4>

		<dl>
		<dt><code>{category}</code></dt>
		<dd><?php _e('Name of the category of the event.','my-calendar'); ?></dd>

		<dt><code>{icon}</code></dt>
		<dd><?php _e('URL for the event\'s category icon.','my-calendar'); ?></dd>

		<dt><code>{color}</code></dt>
		<dd><?php _e('Hex code for the event\'s category color.','my-calendar'); ?></dd>

		<dt><code>{category_id}</code></dt>
		<dd><?php _e('ID of the category of the event.','my-calendar'); ?></dd>
		</dl>
		</div>
		</div>
		</div>
		<?php } ?>
	</div>
	</div>	
<?php
	}
}

// Function to deal with adding the calendar menus
function my_calendar_menu() {
  global $wpdb;
	$mcdb = $wpdb;  
  check_my_calendar();
  $icon_path = plugins_url('/my-calendar/images');
	if ( function_exists('add_object_page') ) {
		if ( get_option( 'mc_remote' ) != 'true' ) {
			add_object_page(__('My Calendar','my-calendar'), __('My Calendar','my-calendar'), 'mc_add_events', 'my-calendar', 'edit_my_calendar',$icon_path.'/icon.png' );
		} else {
			add_object_page(__('My Calendar','my-calendar'), __('My Calendar','my-calendar'), 'mc_edit_settings', 'my-calendar', 'edit_my_calendar_config',$icon_path.'/icon.png' );		
		}
	} else {  
		if ( function_exists('add_menu_page') ) {
			if ( get_option( 'mc_remote' ) != 'true' ) {
				add_menu_page(__('My Calendar','my-calendar'), __('My Calendar','my-calendar'), 'mc_add_events', 'my-calendar', 'edit_my_calendar',$icon_path.'/icon.png' );
			} else {
				add_menu_page(__('My Calendar','my-calendar'), __('My Calendar','my-calendar'), 'mc_edit_settings', 'my-calendar', 'edit_my_calendar_config',$icon_path.'/icon.png' );		
			}			
		}
	}
	if ( function_exists('add_submenu_page') ) {
		add_action( "admin_head", 'my_calendar_write_js' );		
		add_action( "admin_head", 'my_calendar_add_styles' );
		if ( get_option( 'mc_remote' ) == 'true' && function_exists('mc_remote_db') ) {
		} else { // if we're accessing a remote page, remove these pages.
			add_submenu_page('my-calendar', __('Add/Edit Events','my-calendar'), __('Add/Edit Events','my-calendar'), 'mc_add_events', 'my-calendar', 'edit_my_calendar');
			add_submenu_page('my-calendar', __('Manage Categories','my-calendar'), __('Manage Categories','my-calendar'), 'mc_edit_cats', 'my-calendar-categories', 'my_calendar_manage_categories');
			add_submenu_page('my-calendar', __('Manage Event Groups','my-calendar'), __('Manage Event Groups','my-calendar'), 'mc_manage_events', 'my-calendar-groups', 'edit_my_calendar_groups');		
			add_submenu_page('my-calendar', __('Manage Locations','my-calendar'), __('Manage Locations','my-calendar'), 'mc_edit_locations', 'my-calendar-locations', 'my_calendar_manage_locations');		
		}
		add_submenu_page('my-calendar', __('Settings','my-calendar'), __('Settings','my-calendar'), 'mc_edit_settings', 'my-calendar-config', 'edit_my_calendar_config');
		add_submenu_page('my-calendar', __('Style Editor','my-calendar'), __('Style Editor','my-calendar'), 'mc_edit_styles', 'my-calendar-styles', 'edit_my_calendar_styles');
		add_submenu_page('my-calendar', __('Behavior Editor','my-calendar'), __('Behavior Editor','my-calendar'), 'mc_edit_behaviors', 'my-calendar-behaviors', 'edit_my_calendar_behaviors');	
		add_submenu_page('my-calendar', __('Template Editor','my-calendar'), __('Template Editor','my-calendar'), 'mc_edit_templates', 'my-calendar-templates', 'edit_mc_templates');
		add_submenu_page('my-calendar', __('My Calendar Help','my-calendar'), __('Help','my-calendar'), 'mc_view_help', 'my-calendar-help', 'my_calendar_help');		
	}
	if ( function_exists( 'mcs_submissions' ) ) {
		add_action( "admin_head", 'my_calendar_sub_js' );		
		add_action( "admin_head", 'my_calendar_sub_styles' );	
		add_submenu_page('my-calendar', __('Event Submissions','my-calendar'), __('Event Submissions','my-calendar'), 'manage_options', 'my-calendar-submissions', 'mcs_settings');
		add_submenu_page('my-calendar', __('Payments','my-calendar'), __('Payments','my-calendar'), 'manage_options', 'my-calendar-payments', 'mcs_sales_page');
	}
}

// return a result for admin_url in 2.9.2
if ( !function_exists( 'admin_url' ) ) {
	function admin_url() {
		return get_bloginfo('wpurl').'/wp-admin/';
	}
}
if ( !function_exists( 'home_url' ) ) {
	function home_url() {
		return get_option('home');
	}
}

// add shortcode interpreters
add_shortcode('my_calendar','my_calendar_insert');
add_shortcode('my_calendar_upcoming','my_calendar_insert_upcoming');
add_shortcode('my_calendar_today','my_calendar_insert_today');
add_shortcode('my_calendar_locations','my_calendar_locations');
add_shortcode('my_calendar_categories','my_calendar_categories');
add_shortcode('my_calendar_show_locations','my_calendar_show_locations_list');
add_shortcode('my_calendar_event','my_calendar_show_event');