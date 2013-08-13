<?php
/**
 * Add shortcode to display baby_stage data
 */

function abwbg_display_baby_stage( $attr ){

  $measures_table = '';

  // Need a baby stage id
  if( isset( $attr['id'] ) && ! empty( $attr['id'] ) && is_numeric( $attr['id'] ) ){

    $baby_measures = get_post_meta( $attr['id'], 'abwbg_baby_measures', true );

    if( $baby_measures && is_array( $baby_measures ) ){
      $measures_table = '<table>';
      foreach ($baby_measures as $key => $num) {
        $measures_table .= '<tr>';
        $measures_table .= '<th>';
        $measures_table .= abwbg_get_label_baby_measures( $key );
        $measures_table .= '</th>';
        $measures_table .= '<td>';
        $measures_table .= $num .' '. abwbg_get_unit_baby_measures( $key );
        $measures_table .= '</td>';
        $measures_table .= '</tr>';
      }
      $measures_table .= '</table>';
    }
  }


  return $measures_table;
}
add_shortcode('baby_stage', 'abwbg_display_baby_stage');


function abwbg_display_baby_weight_graph( $attr ){

  return abwbg_get_graphe( 'weight' );
}
add_shortcode('baby_weight_graph', 'abwbg_display_baby_weight_graph');

function abwbg_display_baby_size_graph( $attr ){

  return abwbg_get_graphe( 'size' );
}
add_shortcode('baby_size_graph', 'abwbg_display_baby_size_graph');

function abwbg_display_baby_head_graph( $attr ){

  return abwbg_get_graphe( 'head_circumference' );
}
add_shortcode('baby_head_circumference_graph', 'abwbg_display_baby_head_graph');

function abwbg_get_graphe( $slug ){

  global $baby_measures_scripts;

  $all_baby_measures = new WP_Query( array(
    'post_type'     => 'baby_stage',
    'post_per_page' => -1,
    'order'         => 'ASC',
    'meta_query'    => array(
                        'meta_value' => 'abwbg_baby_measures',
                        'compare'    => 'EXISTS',
                      )
  ));

  if( $all_baby_measures->have_posts() ){

    $baby_measures_scripts[$slug]['title'] = __('Weight Graph', 'baby_graph');
    $baby_measures_scripts[$slug]['data'] = array(
        array( __('Date', 'baby_graph'), abwbg_get_label_baby_measures ( $slug ) ),
      );

    while ($all_baby_measures->have_posts() ) {
      $all_baby_measures->the_post();
      $baby_measures = get_post_meta( get_the_ID(), 'abwbg_baby_measures', true );

      if( isset($baby_measures[$slug]) )
        $baby_measures_scripts[$slug]['data'][] = array( get_the_date(), $baby_measures[$slug] );
    }
    wp_reset_postdata();

    wp_enqueue_script( 'abwbg_scripts' );
    return '<div class="google-graph" data-measures="'. $slug .'"></div>';
  }
}
function abwbg_send_data_to_script(){
  global $baby_measures_scripts;
  wp_localize_script( 'abwbg_scripts', "baby_measures_data",  $baby_measures_scripts);
}
add_action('wp_footer', 'abwbg_send_data_to_script');

function abwbg_get_label_baby_measures ( $slug ){

  $convert_array = array(
      'weight' => __('Weight', 'baby_graph'),
      'size' => __('Size', 'baby_graph'),
      'head_circumference' => __('Head Circumference', 'baby_graph'),
    );

  return isset( $convert_array[$slug] ) ? $convert_array[$slug] : false;
}

function abwbg_get_unit_baby_measures ( $slug ){

  $convert_array = array(
      'weight' => 'Kg',
      'size' => 'cm',
      'head_circumference' => 'cm',
    );

  return isset( $convert_array[$slug] ) ? $convert_array[$slug] : false;
}
