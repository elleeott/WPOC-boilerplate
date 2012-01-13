<?php
/*
Plugin Name: opencart integratation
Description: This makes opencart session data available to wordpress.
*/

//init stuff - register post type and taxonomies, get env. variables from OC
include ('oc-init.php');

//create list of opencart products and categories, add them to primary nav menu
//include ('oc-menu-items.php');

//create sidebar widget for oc products
include ('oc-widget.php');

//create wp-admin setting screen to sync db tables
include ('oc-admin.php');

//get additional data from OC tables for use in template
include ('oc-template-options.php');

//create wp-admin setting screen to sync db tables
// REQUIRES RECIPES PLUGIN
include ('oc-related-recipes.php');