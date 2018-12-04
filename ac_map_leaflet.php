<?php

/**
 * Plugin Name: AC map - leaflet
 * Plugin URI: http://tox.ovh
 * Version: 0.9
 * Author: Tomasz Gołkowski
 * Author URI: http://tox.ovh
 * Description: leaflet map plugin
 * License: MIT
 */
function poimaps_leaflet_cfg()
{
    $domain = $_SERVER['SERVER_NAME'];
    $debug_domain = array('localhost', 'dev.tox.ovh');
    $debug = false;
    if (in_array($domain, $debug_domain)) {
        $debug = true;
    }
    $settings = array(
        'debug' => $debug,
        'textdomain' => 'ac_poi_leaflet_maps',
        'post_name' => 'ac_poimaps_leaflet',
    );
    $settings = (object)$settings;
    return $settings;
}

include 'option.php';
include 'shortcodes.php';
include 'src/functions/tax.php';
include 'src/functions/tax_fields.php';
include_once 'loader.php';
//load_modules('src/functions/');


add_action('init', 'poimaps_leaflet_register');
function poimaps_leaflet_register()
{
    $plugins_url = plugins_url();
    $labels = array();
    $args = array(
        'name' => __('Points on map', poimaps_leaflet_cfg()->textdomain),
        'label' => __('Points on map', poimaps_leaflet_cfg()->textdomain),
        'singular_label' => __('Point', poimaps_leaflet_cfg()->textdomain),
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => array('slug' => 'mapa'), // rewrite url 
        'query_var' => 'punkt',
    	//'rewrite' => true,
    	//'supports' => array('title','thumbnail','editor', 'tags'),
        'supports' => array('title', 'editor'),
    	//'taxonomies' => array('post_tag'),
    );
    register_post_type('ac_poimaps_leaflet', $args);
}

//admin js
function ac_poimaps_leaflet_maps()
{
    $plugins_url = plugins_url();
    $screen = get_current_screen();
    
    //my scripts
    $ext = '.js';
    $ext_css = '.css';
    if (poimaps_leaflet_cfg()->debug === true) {
        $ext = '-debug.js?' . time();
        $ext_css = '.css?' . time();
    }

    $screen = $screen->post_type;
    if ($screen == "ac_poimaps_leaflet") {
        wp_register_script('ac_poimaps_leaflet_media', plugin_dir_url(__FILE__) . 'js/media' . $ext, array(
            'jquery',
        ));
        
        wp_enqueue_script('ac_poimaps_leaflet_media');

        wp_register_style('ac_poimaps_leaflet_core', plugin_dir_url(__FILE__) . 'css/core' . $ext_css);
        wp_register_style('ac_poimaps_leaflet_main', plugin_dir_url(__FILE__) . 'css/style' . $ext_css);

        wp_register_script('Leaflet_lib', plugin_dir_url(__FILE__) . 'js/leaflet' . $ext);
        wp_register_script('Leaflet_lib_esri', plugin_dir_url(__FILE__) . 'js/esri-leaflet' . $ext);
        wp_register_script('Leaflet_lib_geocoder', plugin_dir_url(__FILE__) . 'js/esri-leaflet-geocoder' . $ext);
        wp_register_script('Leaflet_lib_markerCluster', plugin_dir_url(__FILE__) . 'js/leaflet.markercluster-src' . $ext);

        wp_register_script('ac_poimaps_leaflet_maps_admin', plugin_dir_url(__FILE__) . 'js/admin' . $ext, array(
            'jquery',
            'Leaflet_lib',
            'Leaflet_lib_esri',
            'Leaflet_lib_geocoder',
            'Leaflet_lib_markerCluster'
        ));
        wp_enqueue_script('ac_poimaps_leaflet_maps_admin');

        wp_enqueue_style('ac_poimaps_leaflet_core');
        wp_enqueue_style('ac_poimaps_leaflet_main');
    }
}
add_action('admin_enqueue_scripts', 'ac_poimaps_leaflet_maps');

//dodatkowe pola
add_action("admin_init", "ac_poimaps_leaflet_info");
function ac_poimaps_leaflet_info()
{
    add_meta_box("metabox" . rand(100, 999), __('Place:', poimaps_leaflet_cfg()->textdomain), "ac_poimaps_leaflet_meta_options", poimaps_leaflet_cfg()->post_name, "normal", "low"); // tu mozna ustawic pozycje blokowpost_name
    add_meta_box("metabox" . rand(100, 999), __('Shortcode:', poimaps_leaflet_cfg()->textdomain), "ac_poimaps_leaflet_meta_options_1", poimaps_leaflet_cfg()->post_name, "side", "low"); // tu mozna ustawic pozycje blokow
    add_meta_box("metabox".rand(100, 999),  __( 'Marker:', poimaps_leaflet_cfg()->textdomain) , "ac_poimaps_leaflet_meta_options_2", poimaps_leaflet_cfg()->post_name, "side", "low"); // tu mozna ustawic pozycje blokow      
}
function ac_poimaps_leaflet_meta_options_1()
{
    global $post; ?>
	<label><?php __('Point Shortcode', 'ac_poi_maps'); ?></label><input type="text" value="[ac_map_single id='<?php echo $post->ID ?>' title='off' zoom='5' popup='off']" size="80" style="width:99%" />
	<?php	
}

function ac_poimaps_leaflet_meta_options_2()
{
    global $post;
    $custom = get_post_custom($post->ID); ?>
	<?php $marker = $custom["marker"][0]; ?>
	<label><?php _e('Marcer icon(url):', poimaps_leaflet_cfg()->textdomain) ?></label><input type="text" name="marker"  id="marker" class="marker_input" value="<?php echo esc_attr($marker); ?>" size="80" style="width:99%" placeholder="<?php _e('marker url(.png)', poimaps_leaflet_cfg()->textdomain); ?>" />
        <a href="#" id="set-ico-button" class="button upload_image_button"><?php echo __('Set icon', poimaps_leaflet_cfg()->textdomain); ?></a><a href="#" id="reset_ico" class="button"><?php echo __('Remove icon', poimaps_leaflet_cfg()->textdomain); ?></a>
<?php 
}

function ac_poimaps_leaflet_meta_options()
{
    global $post;

    $custom = get_post_custom($post->ID); ?>
	<?php $adres = $custom["adres"][0]; ?>
	<label><?php _e('Address:', poimaps_leaflet_cfg()->textdomain) ?></label><input type="text" name="adres"  id="adres" value="<?php echo esc_attr($adres); ?>" size="80" style="width:99%" placeholder="<?php _e('Address: street number, city', poimaps_leaflet_cfg()->textdomain); ?>" />
	<input type="button" name="check_adress" value="<?php _e('Check the address', poimaps_leaflet_cfg()->textdomain); ?>" class="button-secondary set_position">
	<br>	
	<br>
	<?php $latFld = $custom["latFld"][0]; ?>
	<label><?php _e('Latitude', poimaps_leaflet_cfg()->textdomain) ?>:</label><input type="text" name="latFld"  id="latFld" value="<?php echo esc_attr($latFld); ?>" size="80" style="width:99%" />

	<?php $lngFld = $custom["lngFld"][0]; ?>
	<label><?php _e('Longitude', poimaps_leaflet_cfg()->textdomain) ?>:</label><input type="text" name="lngFld" id="lngFld"  value="<?php echo esc_attr($lngFld); ?>" size="80" style="width:99%" />
	<div style="width: 100%; height: 30px;"></div>
	<p class="description">
        <?php _e('Latitude and longitude should be given in decimal form.', poimaps_leaflet_cfg()->textdomain); ?>
    </p> 
	<div id="map_canvas" style="width:99%; height:250px; background:#E3E3E3" data-latFld="<?php echo esc_attr($latFld); ?>" data-lngFld="<?php echo esc_attr($lngFld); ?>"></div>
	
<?php 
}

//
add_action('save_post', 'save_acpoimaps_data');
function save_acpoimaps_data()
{
    global $post;
    update_post_meta($post->ID, "marker", $_POST["marker"]);

    if ($_POST["latFld"] != '' && $_POST["lngFld"] != '') {
        update_post_meta($post->ID, "latFld", $_POST["latFld"]);
        update_post_meta($post->ID, "lngFld", $_POST["lngFld"]);
    } else {
        update_post_meta($post->ID, "latFld", '52.173931692568');
        update_post_meta($post->ID, "lngFld", '18.8525390625');
    }
}
// style i tłumaczenia
function ac_poimaps_leaflet_style()
{
    wp_enqueue_style('style', plugin_dir_url(__FILE__) . 'css/style.css');
}
add_action('wp_head', 'ac_poimaps_leaflet_style');

function ac_poimaps_leaflet_text_domain()
{
    load_plugin_textdomain(poimaps_leaflet_cfg()->textdomain, false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'ac_poimaps_leaflet_text_domain');


