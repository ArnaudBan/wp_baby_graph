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
require_once( 'wbg_shortcode.php' );


// Add js
function abwbg_add_scripts(){

  wp_register_script( 'google_sharts', 'https://www.google.com/jsapi', array(), '', true );
  wp_register_script( 'abwbg_scripts', plugins_url( '/js/scripts.js', __FILE__), array('google_sharts'), '20130813', true );
}
add_action( 'wp_enqueue_scripts', 'abwbg_add_scripts');

function abwbg_register_baby_stage() {

  global $baby_measures_scripts;

  $labels = array(
    'name'                => __('Baby stages', 'baby_graph'),
    'singular_name'       => __('Baby stage', 'baby_graph'),
    'add_new'             => __('Add New', 'baby_graph'),
    'add_new_item'        => __('Add New Baby stage', 'baby_graph'),
    'edit_item'           => __('Edit Baby stage', 'baby_graph'),
    'new_item'            => __('New Baby stage', 'baby_graph'),
    'all_items'           => __('All Baby stages', 'baby_graph'),
    'view_item'           => __('View Baby stage', 'baby_graph'),
    'search_items'        => __('Search Baby stages', 'baby_graph'),
    'not_found'           => __('No Baby stages found', 'baby_graph'),
    'not_found_in_trash'  => __('No Baby stages found in Trash', 'baby_graph'),
    'parent_item_colon'   => __('', 'baby_graph'),
    'menu_name'           => __('Baby stages', 'baby_graph'),
  );

  $args = array(
    'label'                => 'Baby stages',
    'labels'               => $labels,
    'description'          => __('Add new baby measures associate to a date', 'baby_graph'),
    'exclude_from_search'  => true,
    'publicly_queryable'   => false,
    'show_ui'              => true,
    'show_in_nav_menus'    => false,
    'query_var'            => 'false',
    'capability_type'      => 'post',
    'has_archive'          => false,
    'hierarchical'         => false,
    'supports'             => array( 'title', 'excerpt' ),
    'register_meta_box_cb' => 'abwbg_add_baby_stage_metabox',
  );

  register_post_type( 'baby_stage', $args );
}
add_action( 'init', 'abwbg_register_baby_stage' );

function abwbg_add_baby_stage_metabox(){
  add_meta_box( 'abwbg_baby_stage_metabox', 'Baby Measures', 'abwbg_baby_stage_metabox_content', 'baby_stage' );
}


function abwbg_baby_stage_metabox_content( $post ){

  // Use nonce for verification
  wp_nonce_field( plugin_basename( __FILE__ ), 'abwbg_baby_stage_metabox' );

  // The actual fields for data entry
  $baby_measures = get_post_meta( $post->ID, 'abwbg_baby_measures', true );

  ?>
  <table class="form-table">
    <tr>
      <td><label for="abwbg_baby_weight"><?php _e('Weight', 'baby_stage' )?></label></td>
      <td><input type="number" step="0.001" id="abwbg_baby_weight" name="abwbg_baby_measures[weight]" value="<?php if( isset($baby_measures['weight']) ) esc_attr_e($baby_measures['weight']) ?>" /></td>
    </tr>
    <tr>
      <td><label for="abwbg_baby_size"><?php _e('Size', 'baby_stage' )?></label></td>
      <td><input type="number" step="0.05" id="abwbg_baby_size" name="abwbg_baby_measures[size]" value="<?php if( isset($baby_measures['size']) ) esc_attr_e($baby_measures['size']) ?>" /></td>
    </tr>
    <tr>
      <td><label for="abwbg_baby_head_circumference"><?php _e('Head Circumference', 'baby_stage' )?></label></td>
      <td><input type="number" step="0.05" id="abwbg_baby_head_circumference" name="abwbg_baby_measures[head_circumference]" value="<?php if( isset($baby_measures['head_circumference']) ) esc_attr_e($baby_measures['head_circumference']) ?>" /></td>
    </tr>
  </table>
  <?php
}

function abwbg_save_baby_stage_meta( $post_id ){

  if ( ! current_user_can( 'edit_post', $post_id ) )
    return;


  // Secondly we need to check if the user intended to change this value.
  if ( ! isset( $_POST['abwbg_baby_stage_metabox'] ) || ! wp_verify_nonce( $_POST['abwbg_baby_stage_metabox'], plugin_basename( __FILE__ ) ) )
      return;

  // Sanitize user input
  foreach ( $_POST['abwbg_baby_measures'] as $key => $num) {
    if( is_numeric($num) )
      $baby_measures[$key] = (float) $num;
  }

  update_post_meta($post_id, 'abwbg_baby_measures', $baby_measures );

}
add_action( 'save_post', 'abwbg_save_baby_stage_meta' );