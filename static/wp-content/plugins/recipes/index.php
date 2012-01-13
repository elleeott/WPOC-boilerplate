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

//template tags - functions for use in themes
include('recipe-ratings.php');


