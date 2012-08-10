<?php

/*
  Plugin Name: Pressbox
  Plugin URI: http://lesharris.com/pressbox
  Description: Bring the power of Dropbox to your blog.
  Version: 1.0
  Author: Les Harris
  Author URI: http://lesharris.com
  License: GPLv2
 */

/* Copyright 2011 Les Harris (email : les@lesharris.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

/**
 * Display file from Dropbox
 * 
 * This needs to be run super early since we need
 * to output a header.
 * 
 * FIXME: This is utterly terrible. Anyway to use get_option()
 * without including wp-load like this?
 */
if (isset($_GET['display'])) {
  require_once( "../../../wp-load.php" );
  require_once( plugin_dir_path(__FILE__) . "/includes/pressbox-oauth.php");
  require_once( plugin_dir_path(__FILE__) . "/includes/pressbox-api.php");

  if (!wp_verify_nonce($_GET['_wpnonce'])) {
    exit;
  }

  $connection = new PressboxOauth(
                  get_option('lh_pressbox_consumer_key'),
                  get_option('lh_pressbox_consumer_secret'),
                  get_option('lh_pressbox_access_token'),
                  get_option('lh_pressbox_access_token_secret'));

  $metadata = lh_pressbox_get_metadata_for($connection, $_GET['display']);

  header("Content-Type: " . $metadata->{ 'mime_type' });
  echo lh_pressbox_get_file($connection, $_GET['display'], null);

  exit;
}

if (isset($_GET['display_thumb'])) {
  require_once( "../../../wp-load.php" );
  require_once( plugin_dir_path(__FILE__) . "/includes/pressbox-oauth.php");
  require_once( plugin_dir_path(__FILE__) . "/includes/pressbox-api.php");
    
  if (!wp_verify_nonce($_GET['_wpnonce'])) {
    exit;
  }

  $connection = new PressboxOauth(
                  get_option('lh_pressbox_consumer_key'),
                  get_option('lh_pressbox_consumer_secret'),
                  get_option('lh_pressbox_access_token'),
                  get_option('lh_pressbox_access_token_secret'));
  
  header("Content-Type: image/jpeg");
  echo lh_pressbox_get_thumbnail($connection, $_GET['display_thumb'], $_GET['size']);

  exit;
}

// Include some common functionality
require_once( plugin_dir_path(__FILE__) . "/includes/pressbox-oauth.php");
require_once( plugin_dir_path(__FILE__) . "/includes/pressbox-api.php");

/**
 * Activation Code
 *
 * Checks for plugin compatibility and installs settings on
 * activation.
 *
 */
add_action( 'admin_init', 'lh_pressbox_activate' );

function lh_pressbox_activate() {
  // We require the JSON support in PHP 5.2+, disable if
  // we don't find it.
  if (!function_exists("json_decode")) {
    add_action("admin_notices", "lh_activation_php_error");
  }

  // We require at least Wordpress 3.0
  if (version_compare(get_bloginfo('version'), '3.0', '<')) {
    add_action("admin_notices", "lh_activation_wpversion_error");
  }
}

function lh_activation_php_error() {
  echo "<div class='error'><p>Pressbox requires PHP 5.2+ to function.</p></div>";
  deactivate_plugins(basename(__FILE__));
}

function lh_activation_wpversion_error() {
  echo "<div class='error'><p>Pressbox requires Wordpress 3.0+ to function.</p></div>";
  deactivate_plugins(basename(__FILE__));
}

/**
 * Insert Settings Page
 *
 * Adds Pressbox menu into Settings menu.
 *
 */
add_action('admin_menu', 'lh_pressbox_create_menu');

function lh_pressbox_create_menu() {
  $settings_page = add_options_page('Pressbox Settings', 'Pressbox', 'manage_options', __FILE__, 'lh_pressbox_settings_page');
  add_action('admin_head-' . $settings_page, 'lh_pressbox_get_styles');
}

function lh_pressbox_get_styles() {
  if ($_GET['page'] == "pressbox/pressbox.php") {
    echo '<link type="text/css" rel="stylesheet" href="' . plugin_dir_url(__FILE__) . '/css/pressbox.css" />';
  }
}

/**
 * Pressbox Settings Page
 *
 * Displays Pressbox Settings
 *
 */
function lh_pressbox_settings_page() {
  if (file_exists(dirname(__FILE__) . '/includes/pressbox-settings.php')) {
    include( dirname(__FILE__) . '/includes/pressbox-settings.php' );
  } else {
    echo "<div class=\"error\">Could not locate the settings page. Please reinstall Pressbox.</div>";
  }
}

/**
 * Add Pressbox Media Upload Tab
 */
add_filter('media_upload_tabs', 'lh_pressbox_media_menu');
add_action('media_upload_pressbox', 'lh_pressbox_media_menu_handle');

function lh_pressbox_media_menu($tabs) {

  if (get_option('lh_pressbox_access_token')) { // Are we connected to Dropbox?
    $newtab = array('pressbox' => __('From Dropbox', 'pressbox'));
    return array_merge($tabs, $newtab);
  } else { // No, not connected.
    return $tabs;
  }
}

function lh_pressbox_media_menu_handle() {
  return wp_iframe('media_lh_pressbox_process');
}

function media_lh_pressbox_process() {
  if (file_exists(dirname(__FILE__) . '/includes/pressbox-media.php')) {
    include( dirname(__FILE__) . '/includes/pressbox-media.php' );
  } else {
    echo "<div class=\"error\">Could not locate the media page. Please reinstall Pressbox.</div>";
  }
}

/**
 * Shortcodes
 */
add_shortcode("pressbox", "lh_pressbox_shortcode");

function lh_pressbox_shortcode($attr) {
  if ($attr['path']) {
    $code = wp_create_nonce();

    if (isset($attr['type']) && $attr['type'] == "link") {
      if (isset($attr['name'])) {
        $name = $attr['name'];
      } else {
        $name = $attr['path'];
      }
      return "<a href=\"" . plugin_dir_url(__FILE__) . "pressbox.php?display=" . $attr['path'] . "&_wpnonce=" . $code . "\" />" . $name . "</a>";
    } else {
      return "<img src=\"" . plugin_dir_url(__FILE__) . "pressbox.php?display=" . $attr['path'] . "&_wpnonce=" . $code . "\" />";
    }
  } else {
    return "Pressbox Error: No path passed to shortcode.";
  }
}