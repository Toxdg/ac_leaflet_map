<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//full map
function map_full( $atts ){
    $popup = $atts['popup'];
    if($popup == 'on'){$popup = 1;}else{$popup = 0;}
    $plugins_url = plugins_url();
    $label_cat = __('Category', poimaps_leaflet_cfg()->textdomain);
    $label_woj = __('Province', poimaps_leaflet_cfg()->textdomain);
    $zoom = $atts['zoom'];
    $id_full = "full_".$id.rand(1000,9999);
    if($atts['mcluster'] !== NULL) {
        $mcluster = $atts['mcluster'];
    }else{
        $mcluster = ac_leaflet_get_mcluster();
    }
    if($zoom == ''){$zoom = 6;}
    if($atts['style'] !== NULL) {
        $style = $atts['style'];
    }else{
        $style = ac_leaflet_get_style();
    }
    $style_label = ac_leaflet_get_style_label();
    ac_leaflet_style_map();
    if(get_option('ac_leaflet_filtr') == 1){
        $hide_select = 'ac_show';
    }else{
        $hide_select = 'ac_hide';
    }
    $map = "<div id='ac_leaflet_map_full_".rand(1000,9999)."'  class='ac_leaflet_map_full ac_leaflet_map'
    data-map='full'
    data-id='".$id_full."'
    data-url='".ac_leaflet_ikona()."'
    data-popup='".$popup."'
    data-mcluster='".$mcluster."'
    data-style_l='".$style_label."'
    data-style='".$style."'
    data-zoom='".$zoom."'
    data-latFld='".get_option('ac_leaflet_latFld')."'
    data-lngFld='".get_option('ac_leaflet_lngFld')."'>";
    if($atts['title'] == 'on'){
        $map .= "<h2>".__('Map', poimaps_leaflet_cfg()->textdomain)."</h2>";
    }
    $map .="<div id='' class='poi-select-box ".$hide_select." '>";
    $map .="<div>";
    if(get_option('ac_leaflet_wojewodztwa') == 1){
        $map .="<label>".$label_cat.":</label><br>";
    }
    $map .="<select class='select_box cat_select' name='cat' id='cat'>";
    $map .="<option value='0' class=''>".__('All', 'ac_poi_maps')."</option>";
    $args = array(
        'orderby' => 'name',
        'parent' => 0,
        'hide_empty' => 1,
        'taxonomy' => 'ac_leaflet_category'
    );
    $categories = get_categories( $args );

    $lista_main = array();
    foreach ( $categories as $category ) {
        //var_dump($category);
        if($_POST["cat"] == $category->term_id){
            $selected="selected='selected' ";
        }else{
            $selected = '';
        }
        $map .= "<option ".$selected." value=".$category->term_id." class='".$category->slug."'>".$category->name."</option>";
        $lista_main[] = $category->term_id;
    }
    $map .="</select>";
    $map .="</div><div>";
    if(get_option('ac_leaflet_wojewodztwa') == 1){
        $args = array(
            'orderby' => 'name',
            'parent' => 0,
            'hide_empty' => 1,
            'taxonomy' => 'ac_leaflet_category_region'
        );

        $all_points_position = get_categories( $args );
        $map .="<label>".$label_woj.":</label><br>";
        $map .="<select class='select_box woj_select'>";
        $map .="<option value='0' class='' data-lnt='".get_option('latFld')."' data-lng='".get_option('lngFld')."'>".__('All', 'ac_poi_maps')."</option>";
        foreach ( $all_points_position as $post_poi ) {
            $term_meta = get_term_meta($post_poi->term_id, 'term_location', true);
            $lat = $term_meta["latFld"];
            $lng = $term_meta["lngFld"];
            $map .="<option value='".$post_poi->category_nicename."' data-lnt='".$lat."' data-lng='".$lng."'>".$post_poi->name."</option>";
        }
        $map .="</select>";
    }
    $map .="</div>";
    $map .="</div>";
    //mapa wlasciwa
    $map .="<div id='".$id_full."' class='ac_poi_map_full_map ac_poi_map'></div>";
    $lista_show = $atts['poi'];
    if(get_option('ac_leaflet_poi_list') != 0 || $lista_show == 'on'){
        $hide_list = 'ac_show';
    }elseif($lista_show == 'off'){
        $hide_list = 'ac_hide';
    }
    $args2 = array(
        'posts_per_page'   => -1,
        'orderby'          => 'title',
        'order'            => 'asc',
        'post_type'        => poimaps_leaflet_cfg()->post_name,
        'post_status'      => 'publish'
    );
    $all_points = get_posts( $args2 );
    //var_dump($all_points);
    $map .="<ul id='' class='ac_poimaps_full_list_post ".$hide_list."'>";
    foreach ( $all_points as $post_poi ) {
        $custom = get_post_custom($post_poi->ID);
        $content_post = get_post($post_poi->ID);
        $decription_marker = $content_post->post_content;
        $term_list = wp_get_post_terms($post_poi->ID, 'ac_leaflet_category', array("fields" => "ids"));
        $term_list = ac_category_list($term_list);
        $lacation_list = wp_get_post_terms($post_poi->ID, 'ac_leaflet_category_region');
        $lacation_list = ac_location_list($lacation_list);
        if($custom['marker'][0] == ''){ $marker_ico = 'null'; }else{ $marker_ico = $custom['marker'][0]; }
        $map .="<li
        data-title='".$content_post->post_name."'
        data-icon='".$marker_ico."'
        data-url='".ac_leaflet_ikona()."'
        data-lat='".$custom['latFld'][0]."'
        data-content='".$decription_marker."'
        data-lng='".$custom['lngFld'][0]."'
        data-country='0,".$lacation_list."'
        data-catid='0,".$term_list."'>";
        $map .= "<h3>".$content_post->post_name."</h3><div>";
        $content = $content_post->post_content;
        $content = apply_filters('the_content', $content);
        $map .= $content."</div></li>";
    }
    $map .="</ul>";
    $map .= "</div>";
    return $map;
}
add_shortcode( 'map_full', 'map_full' );