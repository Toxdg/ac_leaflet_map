<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//category map
function ac_leaflet_category_function($atts){
    $popup = $atts['popup'];
    ac_leaflet_style_map();
    if($popup == 'on'){$popup = 1;}else{$popup = 0;}
    $plugins_url = plugins_url();
    $id = $atts['id'];
    $rand = rand(10, 99);
    $zoom = $atts['zoom'];
    if($zoom == ''){$zoom = 13;}
    if($atts['style'] !== NULL) {
        $style = $atts['style'];
    }else{
        $style = ac_leaflet_get_style();
    }
    if($atts['mcluster'] !== NULL) {
        $mcluster = $atts['mcluster'];
    }else{
        $mcluster = ac_leaflet_get_mcluster();
    }
    $style_label = ac_leaflet_get_style_label($style);
    $cat_latFld = get_option('latFld');
    $cat_lngFld = get_option('lngFld');
    $term = get_term($id);
    $term_location = get_term_meta($id);
    $term_location = $term_location['term_location'];
    if(!is_null($term_location[0])){
        $tab_center = unserialize($term_location[0]);
        $cat_latFld = $tab_center['latFld'];
        $cat_lngFld = $tab_center['lngFld'];
    }
    //var_dump(unserialize($term_location));
    $map = "<div id='map-category_".$id."' class='ac_leaflet_map_category ac_leaflet_map'
    data-zoom='".$zoom."'
    data-map='category'
    data-style_l='".$style_label."'
    data-mcluster='".$mcluster."'
    data-style='".$style."'
    data-id='category_".$id.$rand."'
    data-popup='".$popup."'
    data-url='".ac_leaflet_ikona()."'
    data-latFld='".$cat_latFld."'
    data-lngFld='".$cat_lngFld."'>";

    if($atts['title'] == 'on'){
        $map .= "<h2>".__('Map', poimaps_leaflet_cfg()->textdomain).": ".$term->name."</h2>";
    }
    $map .="<div id='category_".$id.$rand."' class='ac_poi_map_category_map ac_poi_map'></div>";
    $lista_show = $atts['poi'];
    if(get_option('ac_leaflet_poi_list') != 0 || $lista_show == 'on'){
        $hide_list = 'ac_show';
    }elseif($lista_show == 'off'){
        $hide_list = 'ac_hide';
    }
    $args = array(
        'posts_per_page'   => -1,
        'orderby'          => 'title',
        'order'            => 'asc',
        'post_type'        => poimaps_leaflet_cfg()->post_name,
        'tax_query' => array(
            array(
                'taxonomy' => $term->taxonomy,
                'terms'	   => $id
            )
        ),
        'post_status'      => 'publish'
    );
    $all_points = get_posts( $args );

    $map .="<ul id='list_post_".$id."' class='ac_poimaps_list_post_cat ".$hide_list."'>";
    foreach ( $all_points as $post_poi ) {
        $post = get_post($post_poi->ID);
        $custom = get_post_custom($post_poi->ID);
        $content_post = get_post($post_poi->ID);
        $decription_marker = $post->post_content;
        $decription_marker = apply_filters('the_content', $decription_marker);
        
        $term_list = wp_get_post_terms($post_poi->ID, 'ac_leaflet_category', array("fields" => "ids"));
        $term_list = ac_category_list_term($term_list);

        $lacation_list = wp_get_post_terms($post_poi->ID, 'ac_leaflet_category_region');
        $lacation_list = ac_location__list($lacation_list);
        if($custom['marker'][0] == ''){ $marker_ico = ''; }else{ $marker_ico = $custom['marker'][0]; }
        $map .="<li
        data-title='".$content_post->post_name."'
        data-icon='".ac_ac_leaflet_ikona_size($marker_ico)."'
        data-url='".ac_leaflet_ikona()."'
        data-lat='".$custom['latFld'][0]."'
        data-content='".$decription_marker."'
        data-lng='".$custom['lngFld'][0]."'
        data-country='".$lacation_list."'
        data-catid='".$term_list."'>";
        $map .= "<h3>".$content_post->post_name."</h3><div>";
        $content = $content_post->post_content;
        $content = apply_filters('the_content', $content);
        $map .= $content."</div></li>";
    }
    $map .="</ul>";

    $map .= "</div>";
    return $map;
}
add_shortcode('ac_map_category', 'ac_leaflet_category_function');
