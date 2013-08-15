<?php
/**
 * Add custome post type 'baby_graph'
 *
 */


function abwbg_register_baby_graph() {

  global $baby_measures_scripts;

  $labels = array(
    'name'                => __('Baby graphs', 'baby_graph'),
    'singular_name'       => __('Baby graph', 'baby_graph'),
    'add_new'             => __('Add New', 'baby_graph'),
    'add_new_item'        => __('Add New Baby graph', 'baby_graph'),
    'edit_item'           => __('Edit Baby graph', 'baby_graph'),
    'new_item'            => __('New Baby graph', 'baby_graph'),
    'all_items'           => __('Baby graphs', 'baby_graph'),
    'view_item'           => __('View Baby graph', 'baby_graph'),
    'search_items'        => __('Search Baby graphs', 'baby_graph'),
    'not_found'           => __('No Baby graphs found', 'baby_graph'),
    'not_found_in_trash'  => __('No Baby graphs found in Trash', 'baby_graph'),
    'menu_name'           => __('Baby graphs', 'baby_graph'),
  );

  $args = array(
    'label'                => 'Baby graphs',
    'labels'               => $labels,
    'description'          => __('Add new baby measures associate to a date', 'baby_graph'),
    'exclude_from_search'  => true,
    'publicly_queryable'   => false,
    'show_ui'              => true,
    'show_in_menu'				 => 'edit.php?post_type=baby_stage',
    'show_in_nav_menus'    => false,
    'query_var'            => 'false',
    'capability_type'      => 'post',
    'has_archive'          => false,
    'hierarchical'         => false,
    'supports'             => array( 'title' ),
    'register_meta_box_cb' => 'abwbg_add_baby_graph_metabox',
  );

  register_post_type( 'baby_graph', $args );
}
add_action( 'init', 'abwbg_register_baby_graph' );


/**
 * Add meta box to baby_graph UI
 *
 */
function abwbg_add_baby_graph_metabox(){
  add_meta_box( 'abwbg_baby_graph_metabox', __( 'Baby graph info', 'baby_graph' ), 'abwbg_baby_graph_metabox_content', 'baby_graph' );
}


/**
 * baby_stage metabox content
 *
 * @param  object $post The WordPress post Object
 *
 */
function abwbg_baby_graph_metabox_content( $post ){

  // Use nonce for verification
  wp_nonce_field( plugin_basename( __FILE__ ), 'abwbg_baby_graph_metabox' );

  // The actual fields for data entry
  $baby_graph = get_post_meta( $post->ID, 'abwbg_baby_graph', true );

  ?>
  <table class="form-table">
  	<tr>
  		<th>
				<label for="abwbg_baby_graph_value"><?php _e('Value', 'baby_graph' )?></label>
  		</th>
  		<td>
  			<input type="text" id="abwbg_baby_graph_value" name="abwbg_baby_graph[value]" value="<?php if( isset($baby_graph['value']) ) esc_attr_e($baby_graph['value']) ?>" />
  		</td>
  	</tr>
  	<tr>
  		<th>
				<label for="abwbg_baby_graph_unit"><?php _e('Unit', 'baby_graph' )?></label>
  		</th>
  		<td>
  			<input type="text" id="abwbg_baby_graph_unit" name="abwbg_baby_graph[unit]" value="<?php if( isset($baby_graph['unit']) ) esc_attr_e($baby_graph['unit']) ?>" />
  		</td>
  	</tr>
  </table>
  <?php
}


/**
 * Save baby_graph metabox param
 *
 * @param  int $post_id id of the current baby_graph
 *
 */
function abwbg_save_baby_graph_meta( $post_id ){

  if ( ! current_user_can( 'edit_post', $post_id ) )
    return;


  // Secondly we need to check if the user intended to change this value.
  if ( ! isset( $_POST['abwbg_baby_graph_metabox'] ) || ! wp_verify_nonce( $_POST['abwbg_baby_graph_metabox'], plugin_basename( __FILE__ ) ) )
      return;

  // Sanitize user input
  foreach ($_POST['abwbg_baby_graph'] as $key => $meta) {
  	$baby_graph[$key] = sanitize_text_field( $meta );
  }

  update_post_meta($post_id, 'abwbg_baby_graph', $baby_graph );

}
add_action( 'save_post', 'abwbg_save_baby_graph_meta' );

// Custom column
function abwbg_add_baby_graph_columns($columns){
  return array_merge($columns, array('shortcode' => __('Shortcode') ) );
}
add_filter('manage_baby_graph_posts_columns' , 'abwbg_add_baby_graph_columns');


function abwbg_baby_graph_custom_columns( $column, $post_id ) {
  switch ( $column ) {
  case 'shortcode' :
    echo "[baby_graph id='$post_id']";
    break;
  }
}
add_action( 'manage_baby_graph_posts_custom_column' , 'abwbg_baby_graph_custom_columns', 10, 2 );