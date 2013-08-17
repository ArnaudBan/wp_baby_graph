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
  wp_register_script( 'abwbg_scripts', plugins_url( '/js/scripts.js', __FILE__), array('google_sharts', 'jquery'), '20130813', true );
}
add_action( 'wp_enqueue_scripts', 'abwbg_add_scripts');

function abwbg_add_admin_scripts(){
  wp_register_script( 'abwbg_admin_scripts', plugins_url( '/js/admin.js', __FILE__), array('jquery', 'wp-color-picker'), '20130817', true );
}
add_action( 'admin_enqueue_scripts', 'abwbg_add_admin_scripts');


// Add content to the "right now" dashboard widget
add_action('right_now_content_table_end', 'abwbg_add_baby_graph_cpt_to_right_now_widget');

function abwbg_add_baby_graph_cpt_to_right_now_widget() {

  $post_type_to_add = array( 'baby_stage', 'baby_graph' );

  foreach ($post_type_to_add as $post_type) {

    if ( post_type_exists( $post_type ) ) {

      $num_posts = wp_count_posts( $post_type );
      $num = number_format_i18n( $num_posts->publish );
      $labels = get_post_type_object( $post_type );
      $text = _n( $labels->labels->singular_name, $labels->labels->name, intval($num_posts->publish) );
      if ( current_user_can( 'edit_posts' ) ) {
          $num = "<a href='edit.php?post_type=$post_type'>$num</a>";
          $text = "<a href='edit.php?post_type=$post_type'>$text</a>";
      }
      echo '<tr>';
      echo "<td class='first b b-$post_type'>$num</td>";
      echo "<td class='t $post_type'>$text</td>";
      echo '</tr>';
    }
  }

}
?>