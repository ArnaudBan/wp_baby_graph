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
        $baby_graph_meta = get_post_meta( $key, 'abwbg_baby_graph', true );

        $measures_table .= '<tr>';
        $measures_table .= '<th>';
        $measures_table .= $baby_graph_meta['value'];
        $measures_table .= '</th>';
        $measures_table .= '<td>';
        $measures_table .= $num .' '. $baby_graph_meta['unit'];
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
  $baby_graph_meta = get_post_meta( $id, 'abwbg_baby_chart', true );

  if( $all_baby_measures->have_posts() ){

    $baby_measures_scripts[$id]['title'] = $baby_graph->post_title;
    $baby_measures_scripts[$id]['value'] = $baby_graph_meta['value'];
    $baby_measures_scripts[$id]['unit'] = $baby_graph_meta['unit'];

    foreach ($baby_graph_meta['line'] as $line_id => $value) {
        $baby_measures_scripts[$id]['lines_info'][$line_id]['name'] = $value['name'];
        $baby_measures_scripts[$id]['lines_info'][$line_id]['color'] = $value['color'];
    }

    $i = 0;
    while ($all_baby_measures->have_posts() ) {
      $all_baby_measures->the_post();
      $baby_measures = get_post_meta( get_the_ID(), 'abwbg_baby_measures', true );

      $tooltip = '<div style="padding:1px 5px;"><h2 style="font-size:1em;font-weight:bold;">' .get_the_title() .'</h2>';

      if( get_the_excerpt() != '')
        $tooltip .= get_the_excerpt() .'</br>';

      $tooltip .=  get_the_date().'</br>';
      $tooltip .= '<ul>';
      foreach ($baby_graph_meta['line'] as $line_id => $value) {
        $tooltip .= '<li><strong>'. $value['name'] .'</strong> : '. $baby_measures[$id][$line_id] . ' ' . $baby_graph_meta['unit'] .'</li>';
      }
      $tooltip .= '</ul>';
      if( isset($baby_measures[$id]) ){

        $baby_measures_scripts[$id]['data'][$i] = array( get_the_date('Y,m,d') );
        array_push($baby_measures_scripts[$id]['data'][$i], $tooltip)  ;
        foreach ($baby_measures[$id] as $value) {
          array_push($baby_measures_scripts[$id]['data'][$i],(int) $value);
        }
      }
      $i++;
    }
    wp_reset_postdata();

    wp_enqueue_script( 'abwbg_scripts' );
    return '<div class="abwbg-google-graph" data-measures="'. $id .'"></div>';
  }
}


function abwbg_send_data_to_script(){
  global $baby_measures_scripts;

  if( is_array( $baby_measures_scripts ) ){
    $baby_measures_scripts['date'] = __('date', 'baby_graph');
    wp_localize_script( 'abwbg_scripts', "baby_measures_data",  $baby_measures_scripts);
  }
}
add_action('wp_footer', 'abwbg_send_data_to_script');
