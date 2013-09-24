<?php
/**
 * Add custome post type 'baby_chart'
 *
 */


function abwbg_register_baby_chart() {

  global $baby_measures_scripts;

  $labels = array(
    'name'                => __('Baby charts', 'baby_chart'),
    'singular_name'       => __('Baby chart', 'baby_chart'),
    'add_new'             => __('Add New', 'baby_chart'),
    'add_new_item'        => __('Add New Baby chart', 'baby_chart'),
    'edit_item'           => __('Edit Baby chart', 'baby_chart'),
    'new_item'            => __('New Baby chart', 'baby_chart'),
    'all_items'           => __('Baby charts', 'baby_chart'),
    'view_item'           => __('View Baby chart', 'baby_chart'),
    'search_items'        => __('Search Baby charts', 'baby_chart'),
    'not_found'           => __('No Baby charts found', 'baby_chart'),
    'not_found_in_trash'  => __('No Baby charts found in Trash', 'baby_chart'),
    'menu_name'           => __('Baby charts', 'baby_chart'),
  );

  $args = array(
    'label'                => 'Baby charts',
    'labels'               => $labels,
    'description'          => __('Add new baby measures associate to a date', 'baby_chart'),
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
    'register_meta_box_cb' => 'abwbg_add_baby_chart_metabox',
  );

  register_post_type( 'baby_chart', $args );
}
add_action( 'init', 'abwbg_register_baby_chart' );


/**
 * Add meta box to baby_chart UI
 *
 */
function abwbg_add_baby_chart_metabox(){
  add_meta_box( 'abwbg_baby_chart_metabox', __( 'Baby chart info', 'baby_chart' ), 'abwbg_baby_chart_metabox_content', 'baby_chart' );
}


/**
 * baby_stage metabox content
 *
 * @param  object $post The WordPress post Object
 *
 */
function abwbg_baby_chart_metabox_content( $post ){

  // Add script
  wp_enqueue_style('wp-color-picker');
  wp_enqueue_script('abwbg_admin_scripts');

  // Use nonce for verification
  wp_nonce_field( plugin_basename( __FILE__ ), 'abwbg_baby_chart_metabox' );

  // The actual fields for data entry
  $baby_chart = get_post_meta( $post->ID, 'abwbg_baby_chart', true );

  ?>
  <table class="form-table">
  	<tr valign="top">
  		<th>
        <?php _e('Chart', 'baby_chart' ); ?>
      </th>
      <td>
        <fieldset>
          <p>
    				<label for="abwbg_baby_chart_value"><?php _e('Value', 'baby_chart' ); ?></label>
      			<input type="text" id="abwbg_baby_chart_value" name="abwbg_baby_chart[value]" value="<?php if( isset($baby_chart['value']) ) esc_attr_e($baby_chart['value']) ?>" />
          </p>
          <p>
            <label for="abwbg_baby_chart_unit"><?php _e('Unit', 'baby_chart' )?></label>
            <input type="text" id="abwbg_baby_chart_unit" name="abwbg_baby_chart[unit]" value="<?php if( isset($baby_chart['unit']) ) esc_attr_e($baby_chart['unit']) ?>" />
          </p>
          <p>
            <label for="abwbg_baby_chart_nb_lines"><?php _e('Number of lines', 'baby_chart' )?></label>
            <input type="number" id="abwbg_baby_chart_nb_lines" name="abwbg_baby_chart[nb_lines]" value="<?php if( isset($baby_chart['nb_lines']) ) esc_attr_e($baby_chart['nb_lines']) ?>" />
          </p>
        </fieldset>
      </td>
    </tr>
    <?php
    // Minimun one line
    $nb_line = isset($baby_chart['nb_lines']) ? $baby_chart['nb_lines'] : 1;
    for ( $id = 1; $id <= $nb_line; $id++ ) {
      ?>
      <tr valign="top">
        <th>
          <?php printf( __('Lines %s', 'baby_chart' ), $id ); ?>
        </th>
        <td class="line_fieldset">
          <fieldset>
            <p>
      				<label>
                <?php _e('name', 'baby_chart' )?>
          			<input type="text"
                  name="abwbg_baby_chart[line][<?php echo $id; ?>][name]"
                  value="<?php if( isset($baby_chart['line'][$id]['name']) )
                    esc_attr_e($baby_chart['line'][$id]['name']) ?>"
                />
              </label>
            </p>
            <p>
              <label>
                <?php _e('color', 'baby_chart' )?>
                <input type="text" id="abwbg_baby_chart_line_<?php echo $id; ?>_color" class="line_color" name="abwbg_baby_chart[line][<?php echo $id; ?>][color]"
                value="<?php if( isset($baby_chart['line'][$id]['color']) )
                  esc_attr_e($baby_chart['line'][$id]['color']) ?>"
                />
              </label>
            </p>
          </fieldset>
        </td>
      </tr>
      <?php
    }
    ?>
  </table>
  <?php
}


/**
 * Save baby_chart metabox param
 *
 * @param  int $post_id id of the current baby_chart
 *
 */
function abwbg_save_baby_chart_meta( $post_id ){

  if ( ! current_user_can( 'edit_post', $post_id ) )
    return;


  // Secondly we need to check if the user intended to change this value.
  if ( ! isset( $_POST['abwbg_baby_chart_metabox'] ) || ! wp_verify_nonce( $_POST['abwbg_baby_chart_metabox'], plugin_basename( __FILE__ ) ) )
      return;

  // Sanitize user input
  // foreach ($_POST['abwbg_baby_chart'] as $key => $meta) {
  //   if( is_array($meta) ){
  // 	 $baby_chart[$key] = $meta;
  //   } else {
  //    $baby_chart[$key] = sanitize_text_field( $meta );
  //   }
  // }

  update_post_meta($post_id, 'abwbg_baby_chart', $_POST['abwbg_baby_chart']  );

}
add_action( 'save_post', 'abwbg_save_baby_chart_meta' );

// Custom column
function abwbg_add_baby_chart_columns($columns){
  return array_merge($columns, array('shortcode' => __('Shortcode') ) );
}
add_filter('manage_baby_chart_posts_columns' , 'abwbg_add_baby_chart_columns');


function abwbg_baby_chart_custom_columns( $column, $post_id ) {
  switch ( $column ) {
  case 'shortcode' :
    echo "[baby_chart id='$post_id']";
    break;
  }
}
add_action( 'manage_baby_chart_posts_custom_column' , 'abwbg_baby_chart_custom_columns', 10, 2 );