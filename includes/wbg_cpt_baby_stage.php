<?php

/**
 * Add custom post type 'baby_stage'
 *
 */
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


/**
 * Add meta box to baby_stage UI
 *
 */
function abwbg_add_baby_stage_metabox(){
  add_meta_box( 'abwbg_baby_stage_metabox', 'Baby Measures', 'abwbg_baby_stage_metabox_content', 'baby_stage' );
}


/**
 * baby_stage metabox content
 *
 * @param  object $post The WordPress post Object
 *
 */
function abwbg_baby_stage_metabox_content( $post ){

  // Use nonce for verification
  wp_nonce_field( plugin_basename( __FILE__ ), 'abwbg_baby_stage_metabox' );

  // The actual fields for data entry
  $baby_measures = get_post_meta( $post->ID, 'abwbg_baby_measures', true );

  $all_baby_graph_args = array(
      'post_type'       => 'baby_chart',
      'posts_per_page'  => -1
    );

  $all_baby_graph = new WP_Query( $all_baby_graph_args );

  if( $all_baby_graph->have_posts() ){
    ?>
    <table class="form-table">
      <?php
      while ( $all_baby_graph->have_posts() ) {
        $all_baby_graph->the_post();

        $baby_chart_meta = get_post_meta( get_the_ID(), 'abwbg_baby_chart', true );
        $the_id = get_the_ID();
        ?>
        <tr>
          <th>
              <?php echo ( $baby_chart_meta && isset($baby_chart_meta['value']) ) ? $baby_chart_meta['value'] : get_the_title(); ?>
          </th>
          <td>
            <?php
            foreach ($baby_chart_meta['line'] as $line_id => $line) {
              ?>
              <p>
                <label><?php echo $baby_chart_meta['line'][$line_id]['name'] ?></label>
                <input type="number" step="0.01" id="abwbg_baby_<?php echo $the_id.'_'.$line_id ?>" name="abwbg_baby_measures[<?php echo $the_id; ?>][<?php echo $line_id; ?>]" value="<?php if( isset($baby_measures[$the_id][$line_id]) ) esc_attr_e($baby_measures[$the_id][$line_id]) ?>" />
                <span><?php if( $baby_chart_meta && isset($baby_chart_meta['unit']) ) echo $baby_chart_meta['unit']; ?></span>
              </p>
              <?php
            }
            ?>
          </td>
        </tr>
        <?php
      }
      ?>
    </table>
    <?php

    wp_reset_postdata();
  } else {
    _e('Please add chart', 'baby_stage');
  }
}


/**
 * Save baby_stage metabox param
 *
 * @param  int $post_id id of the current baby_stage
 *
 */
function abwbg_save_baby_stage_meta( $post_id ){

  if ( ! current_user_can( 'edit_post', $post_id ) )
    return;


  // Secondly we need to check if the user intended to change this value.
  if ( ! isset( $_POST['abwbg_baby_stage_metabox'] ) || ! wp_verify_nonce( $_POST['abwbg_baby_stage_metabox'], plugin_basename( __FILE__ ) ) )
      return;

  // Sanitize user input
  // foreach ( $_POST['abwbg_baby_measures'] as $key => $num) {
  //   if( is_numeric($num) )
  //     $baby_measures[$key] = (float) $num;
  // }
  $baby_measures = $_POST['abwbg_baby_measures'];
  update_post_meta($post_id, 'abwbg_baby_measures', $baby_measures );

}
add_action( 'save_post', 'abwbg_save_baby_stage_meta' );

// Custom column
function abwbg_add_baby_stage_columns($columns){
  return array_merge($columns, array('shortcode' => __('Shortcode') ) );
}
add_filter('manage_baby_stage_posts_columns' , 'abwbg_add_baby_stage_columns');


function abwbg_baby_stage_custom_columns( $column, $post_id ) {
  switch ( $column ) {
  case 'shortcode' :
    echo "[baby_stage id='$post_id']";
    break;
  }
}
add_action( 'manage_baby_stage_posts_custom_column' , 'abwbg_baby_stage_custom_columns', 10, 2 );