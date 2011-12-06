<?php
/*
Plugin Name: javascript
Description: enqueue all js
*/


// enque required scripts per the following method to output correctly in OC:
// http://scribu.net/wordpress/optimal-script-loading.html

add_action('init', 'reg_scripts'); 
add_action('wp_footer', 'print_scripts'); 


if ( !is_admin() ) {
	function reg_scripts() {
        $protocol='http:';
        if(!empty($_SERVER['HTTPS'])) {
            $protocol='https:';
        }
		
		//remove l10n js
		wp_deregister_script( 'l10n' );	
			
		//reqister protocol relative google cdn jquery
	    wp_deregister_script( 'jquery' );
		wp_register_script('jquery', $protocol.'//ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js', false, NULL, true);
		
		// register fancybox
		wp_register_script('fancybox', $protocol.'//static.'.str_replace('www.','',$_SERVER['SERVER_NAME']).'/js/fancybox/jquery.fancybox-1.3.4.pack.js', array('jquery'), NULL, true);
		
		// register theme script
		wp_register_script('site-script', $protocol.'//static.'.str_replace('www.','',$_SERVER['SERVER_NAME']).'/js/index.js', array('jquery'), NULL, true);
	}
	function print_scripts() {
		wp_print_scripts('jquery');	
		wp_print_scripts('fancybox');	
		wp_print_scripts('site-script');	
	}
}


?>