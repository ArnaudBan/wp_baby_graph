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

  // Need a graph id
  if( isset( $attr['id'] ) && ! empty( $attr['id'] ) && is_numeric( $attr['id'] ) ){
    return abwbg_get_graphe( $attr['id'] );
  }
}
add_shortcode('baby_graph', 'abwbg_display_baby_weight_graph');

function abwbg_get_graphe( $id ){

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

  $baby_graph = get_post( $id );
  $baby_graph_meta = get_post_meta( $id, 'abwbg_baby_graph', true );

  if( $all_baby_measures->have_posts() ){

    $baby_measures_scripts[$id]['title'] = $baby_graph->post_title;
    $baby_measures_scripts[$id]['value'] = $baby_graph_meta['value'];
    $baby_measures_scripts[$id]['unit'] = $baby_graph_meta['unit'];

    while ($all_baby_measures->have_posts() ) {
      $all_baby_measures->the_post();
      $baby_measures = get_post_meta( get_the_ID(), 'abwbg_baby_measures', true );

      if( isset($baby_measures[$id]) )
        $baby_measures_scripts[$id]['data'][] = array(
          get_the_date('Y,m,d'),
          $baby_measures[$id],
          get_the_date() . ' : ' . $baby_measures[$id]. ' '. $baby_graph_meta['unit']
        );
    }
    wp_reset_postdata();

    wp_enqueue_script( 'abwbg_scripts' );
    return '<div class="abwbg-google-graph" data-measures="'. $id .'"></div>';
  }
}


function abwbg_send_data_to_script(){
  global $baby_measures_scripts;

  $baby_measures_scripts['date'] = __('date', 'baby_graph');
  if( is_array( $baby_measures_scripts ) )
    wp_localize_script( 'abwbg_scripts', "baby_measures_data",  $baby_measures_scripts);
}
add_action('wp_footer', 'abwbg_send_data_to_script');
