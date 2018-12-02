<?php

function create_ac_leaflet_taxonomies() {
    register_taxonomy(
        'ac_leaflet_category',
        poimaps_leaflet_cfg()->post_name,
        array(
            'labels' => array(
                'name' => __('Category POI', poimaps_leaflet_cfg()->textdomain),
                'add_new_item' => __('Add POI Category', poimaps_leaflet_cfg()->textdomain),
                'new_item_name' => __('New POI category', poimaps_leaflet_cfg()->textdomain)
                
            ),
            'show_ui' => true,
            'show_tagcloud' => false,
            //'rewrite' => array( 'slug' => 'ac_poi_maps' ), // rewrite url 
            'hierarchical' => true
        )
    );
}

if(get_option('ac_leaflet_category') == 1){
    add_action( 'init', 'create_ac_leaflet_taxonomies' );
}

function create_ac_leaflet_taxonomies_region() {
    register_taxonomy(
        'ac_leaflet_category_region',
        poimaps_leaflet_cfg()->post_name,
        array(
            'labels' => array(
                'name' => __('Region POI', poimaps_leaflet_cfg()->textdomain),
                'add_new_item' => __('Add POI Region', poimaps_leaflet_cfg()->textdomain),
                'new_item_name' => __('New POI Region', poimaps_leaflet_cfg()->textdomain)
                
            ),
            'show_ui' => true,
            'show_tagcloud' => false,
            //'rewrite' => array( 'slug' => 'ac_poi_maps_region' ), // rewrite url 
            'hierarchical' => true
        )
    );
}

if(get_option('ac_leaflet_wojewodztwa') == 1){
    add_action( 'init', 'create_ac_leaflet_taxonomies_region' );
}