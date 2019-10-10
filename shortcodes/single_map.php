<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// single map
function ac_leaflet_single_map_function($atts){
    $plugins_url = plugins_url();    
    ac_leaflet_style_map();
    $id = $atts['id'].rand(10, 99);
    $post = get_post($atts['id']);
    $custom = get_post_custom($atts['id']);	
    $zoom = $atts['zoom'];
    $title = $post->post_title;
    $desc = $post->post_content;
    $desc = apply_filters('the_content', $desc);
    if($zoom == ''){$zoom = 13;}
	$popup = $atts['popup'];
    if($popup == 'on'){$popup = 1;}else{$popup = 0;}
    if($atts['style'] !== NULL) {
        $style = $atts['style'];
    }else{
        $style = ac_leaflet_get_style();
    }
    $style_label = ac_leaflet_get_style_label($style);
    
    $map = "<div id='".$id."' "
            . "data-id='single_".$id."'"
            . "data-popup='".$popup."'"
            . "data-style='".$style."'"
            . "data-style_l='".$style_label."'"
            . "data-content='".$desc."' "
            . "data-title='".$title."' "
            //. "data-icon='".$custom['marker'][0]."' "
            . "data-icon='".ac_ac_leaflet_ikona_size($custom['marker'][0])."' "

            . "data-zoom='".$zoom."' "
            . "data-map='single'"
            . "data-url='".ac_leaflet_ikona()."' "
            . "class='single_map ac_leaflet_map' "
            . "data-lat='".$custom['latFld'][0]."' "
            . "data-lng='".$custom['lngFld'][0]."' "
            . "data-url='".plugin_dir_url( __FILE__ )."images/'>";
    if($atts['title'] == 'on'){
            $map .= "<h2>".__('Map', poimaps_leaflet_cfg()->textdomain)."</h2>";	
    }
    $map .="<div id='single_".$id."' class='ac_poi_map'></div>";
    $map .="</div>";
    return $map;
}
add_shortcode('ac_map_single', 'ac_leaflet_single_map_function');