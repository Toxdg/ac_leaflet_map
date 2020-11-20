<?php
function ac_leaflet_ikona(){
    $plugins_url = plugins_url();
    if(get_option('ac_leaflet_ico') == ''){
        $ikona = plugin_dir_url( __FILE__ ).'images/marker-icon.png';
    }else{
        $ikona = get_option('ac_leaflet_ico');
    }

    return ac_ac_leaflet_ikona_size($ikona);
    
}
function ac_ac_leaflet_ikona_size($ikona){
    if(is_null($ikona) || $ikona == ''){
        return;
    }
    list($width, $height, $type, $attr) = getimagesize($ikona);
    return $ikona.','.$width.','.$height;
}
//odpalenie styli i skryptów
function ac_leaflet_style_map(){
    
    //my scripts
    $ext = '.js';
    $ext_css = '.css';
    if(poimaps_leaflet_cfg()->debug === true){
        $ext = '-debug.js?'.time();
        $ext_css = '.css?'.time();
    } 

    wp_register_style('ac_poimaps_leaflet_core', plugin_dir_url( __FILE__ ).'css/core'.$ext_css); 
    wp_register_style('ac_poimaps_leaflet_main', plugin_dir_url( __FILE__ ).'css/style'.$ext_css); 
    wp_enqueue_style('ac_poimaps_leaflet_core');
    wp_enqueue_style('ac_poimaps_leaflet_main');
    

    wp_register_script('Leaflet_lib', plugin_dir_url( __FILE__ ).'js/leaflet'.$ext);
    wp_register_script('Leaflet_lib_esri', plugin_dir_url( __FILE__ ).'js/esri-leaflet'.$ext);
    wp_register_script('Leaflet_lib_geocoder', plugin_dir_url( __FILE__ ).'js/esri-leaflet-geocoder'.$ext);
    wp_register_script('Leaflet_lib_markerCluster', plugin_dir_url( __FILE__ ).'js/leaflet.markercluster-src'.$ext);

    wp_register_script('ac_poimaps_leaflet_maps_admin', plugin_dir_url( __FILE__ ).'js/map'.$ext, array(
        'jquery',
        'Leaflet_lib',
        'Leaflet_lib_esri',
        'Leaflet_lib_geocoder',
        'Leaflet_lib_markerCluster'
    ));
    wp_enqueue_script('ac_poimaps_leaflet_maps_admin');	
}

function ac_category_list_term($list){
    $ile = @count($list);
    $html = '';
    $i = 1;
    foreach ($list as $value) {
        $html .= $value;
        if($i < $ile){
            $html .= ',';
        }
        $i++;
    }
    return $html;
}

function ac_location__list($list){
    $ile = @count($list);
    $html = '';
    $i = 1;
    foreach ($list as $value) {
        $html .= $value->slug;
        if($i < $ile){
            $html .= ',';
        }
        $i++;
    }
    return $html;
}

include 'shortcodes/single_map.php';
include 'shortcodes/category_map.php';
include 'shortcodes/full_map.php';
