<?php
/*
Plugin Name: Wp Baby Graph
Plugin URI: http://arnaudban.me
Description: Show the evolution of your baby
Version: 0.1
Author: ArnaudBan
Author URI: http://arnaudban.me
License: GPL2
*/


// Add the shortcodes
require_once( 'includes/wbg_shortcode.php' );

// Add CPT
require_once( 'includes/wbg_cpt_baby_graph.php' );
require_once( 'includes/wbg_cpt_baby_stage.php' );


// Add js
function abwbg_add_scripts(){

  wp_register_script( 'google_sharts', 'https://www.google.com/jsapi', array(), '', true );
  wp_register_script( 'abwbg_scripts', plugins_url( '/js/scripts.js', __FILE__), array('google_sharts'), '20130813', true );
}
add_action( 'wp_enqueue_scripts', 'abwbg_add_scripts');

