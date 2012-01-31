<?php
/*
Plugin Name: Recipes
Description: Custom Recipes
*/

//register post type and taxonomies
include('register-scheme.php');

//admin interface for recipes
include('recipe-admin.php');

//template tags - functions for use in themes
include('template-functions.php');

//ratings functionality and widget
include('recipe-ratings.php');

//ingredients widget
include('recipe-widgets.php');

