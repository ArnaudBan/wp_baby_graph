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
