<?php
// create custom plugin settings menu
//
function ac_media_poi_js() {
    $plugins_url = plugins_url();
    wp_enqueue_media();
    wp_register_script('ac_media_poi', $plugins_url.'/ac_poi_maps/js/media.js',  array('jquery', 'media-editor'));
    wp_enqueue_script('ac_media_poi');
}
add_action('admin_enqueue_scripts', 'ac_media_poi_js');


add_action('admin_menu', 'ac_poimaps_leaflet_create_menu');

function ac_poimaps_leaflet_create_menu() {
	//create new top-level menu
	//add_menu_page('File settings Settings', 'Settings', 'administrator', __FILE__, 'file_settings_page',plugins_url('/img/icon.png', __FILE__));
	add_submenu_page( 'edit.php?post_type='.poimaps_leaflet_cfg()->post_name, 'Settings', __('Settings'), 'manage_options', __FILE__, 'ac_leaflet_settings_page' ); 	
}

add_action( 'admin_init', 'ac_leaflet_settings' );

function ac_leaflet_settings() {
    //register our settings
    register_setting( 'poimaps-settings-group', 'ac_leaflet_category' );
    register_setting( 'poimaps-settings-group', 'ac_leaflet_wojewodztwa' );
    register_setting( 'poimaps-settings-group', 'ac_leaflet_poi_list' );
    register_setting( 'poimaps-settings-group', 'ac_leaflet_filtr' );
    register_setting( 'poimaps-settings-group', 'ac_leaflet_latFld' );
    register_setting( 'poimaps-settings-group', 'ac_leaflet_lngFld' );	
    register_setting( 'poimaps-settings-group', 'ac_leaflet_ico' );
    register_setting( 'poimaps-settings-group', 'ac_leaflet_ico_id' );
    register_setting( 'poimaps-settings-group', 'ac_leaflet_style' );
    register_setting( 'poimaps-settings-group', 'ac_leaflet_mcluster' );
}

function ac_leaflet_settings_page() {
?>
<div class="wrap">
<h2><?php echo __('Settings');?> - AC leaflet Maps </h2>

<form method="post" action="options.php">
    <?php settings_fields( 'poimaps-settings-group' ); ?>
    <?php do_settings_sections( 'poimaps-settings-group' ); ?>
    <table class="form-table poi-settings">
    	<tr valign="top">
            <th scope="row"></th>
            <td><h2><?php echo __('Settings', poimaps_leaflet_cfg()->textdomain); ?></h2></td>
        </tr>
         <tr valign="top">
            <th scope="row"><?php echo __('Category', poimaps_leaflet_cfg()->textdomain);?></th>
            <td><input type="checkbox" name="ac_leaflet_category" value="1"<?php if(get_option('ac_leaflet_category') == 1){ echo 'checked'; } ?>><?php if(get_option('ac_leaflet_category') == 1){ echo 'on'; }else{ echo 'off';} ?></td>
        </tr> 
  
       <tr valign="top">
            <th scope="row"><?php echo __('Province', poimaps_leaflet_cfg()->textdomain);?></th>
            <td><input type="checkbox" name="ac_leaflet_wojewodztwa" value="1"<?php if(get_option('ac_leaflet_wojewodztwa') == 1){ echo 'checked'; } ?>><?php if(get_option('ac_leaflet_wojewodztwa') == 1){ echo 'on'; }else{ echo 'off';} ?></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php echo __('MarkerCluster', poimaps_leaflet_cfg()->textdomain);?></th>
            <td><input type="checkbox" name="ac_leaflet_mcluster" value="1"<?php if(get_option('ac_leaflet_mcluster') == 1){ echo 'checked'; } ?>><?php if(get_option('ac_leaflet_mcluster') == 1){ echo 'on'; }else{ echo 'off';} ?></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php echo __('List POI', poimaps_leaflet_cfg()->textdomain);?></th>
            <td><input type="checkbox" name="ac_leaflet_poi_list" value="1"<?php if(get_option('ac_leaflet_poi_list') == 1){ echo 'checked'; } ?>><?php if(get_option('ac_leaflet_poi_list') == 1){ echo 'on'; }else{ echo 'off';} ?></td>
        </tr> 
        <tr valign="top">
            <th scope="row"><?php echo __('Filter', poimaps_leaflet_cfg()->textdomain);?></th>
            <td><input type="checkbox" name="ac_leaflet_filtr" value="1"<?php if(get_option('ac_leaflet_filtr') == 1){ echo 'checked'; } ?>><?php if(get_option('ac_leaflet_filtr') == 1){ echo 'on'; }else{ echo 'off';} ?></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php echo __('Style', poimaps_leaflet_cfg()->textdomain);?></th>
            <td>
                <?php
                $def_styles = array(
                    'Topographic',
                    'Streets',
                    'NationalGeographic',
                    'Oceans',
                    'Gray',
                    'DarkGray',
                    'Imagery',
                    'ImageryClarity',
                    'ImageryFirefly',
                    'ShadedRelief',
                    'Terrain',
                    'USATopo',
                    'Physical'
                );
                $output = '<select name="ac_leaflet_style">';
                for( $i=0; $i<count($def_styles); $i++ ) {
                    $output .= '<option '
                        . ( get_option('ac_leaflet_style') == $def_styles[$i] ? 'selected="selected"' : '' ) . '>'
                        . $def_styles[$i]
                        . '</option>';
                }
                $output .= '</select>';
                echo $output;
                ?>
            </td>
        </tr>
        <!--        <tr valign="top">
                    <th scope="row"><?php echo __('Map Icon', poimaps_leaflet_cfg()->textdomain);?></th>
                    <td><a href="#" id="set-ico-button" class="button upload_image_button"><?php echo __('Set icon', poimaps_leaflet_cfg()->textdomain);?></a><a href="#" id="reset_ico" class="button"><?php echo __('Remove icon', poimaps_leaflet_cfg()->textdomain);?></a><br>
                        <input type="text" name="ac_leaflet_ico" id="icon_input" value="<?php echo get_option('ac_leaflet_ico');?>">
                        <input type="text" name="ac_leaflet_ico_id" id="ico_id" value="<?php echo get_option('ac_leaflet_ico_id');?>" style="opacity:0; display: none;">
                    </td>
                </tr> -->
        <tr valign="top">
            <th scope="row"></th>
            <td><h2><?php echo __('Map Center', 'ac_poi_maps'); ?></h2> <a href="http://web4you.com.pl/11.html" target="_blank"><?php echo __('check', poimaps_leaflet_cfg()->textdomain); ?></a></td>
        </tr> 
        <tr valign="top">
            <th scope="row">latFld</th>
            <td><input type="text" name="ac_leaflet_latFld" value="<?php if(get_option('ac_leaflet_latFld') == ''){ echo '52.173931692568'; }else{ echo get_option('ac_leaflet_latFld');} ?>"></td>
        </tr>
        <tr valign="top">
            <th scope="row">lngFld</th>
            <td><input type="text" name="ac_leaflet_lngFld" value="<?php if(get_option('ac_leaflet_lngFld') == ''){ echo '18.8525390625'; }else{ echo get_option('ac_leaflet_lngFld');} ?>"></td>
        </tr>  
        <tr valign="top">
            <th scope="row"></th>
            <td><h2><?php echo __('Shortcodes', poimaps_leaflet_cfg()->textdomain); ?></h2></td>
        </tr>  
        <tr valign="top">
            <th scope="row">Shortcode</th>
            <td>
                <table>
                    <tr><td><b><?php echo __('Full view', poimaps_leaflet_cfg()->textdomain);?>:</b> </td><td>[map_full title="off" zoom="5" popup="off" poi="off" style="Topographic"]</td></tr>
                    <tr><td><b><?php echo __('Single Point', poimaps_leaflet_cfg()->textdomain);?>:</b> </td><td>[ac_map_single id="post_id" title="off" zoom="5" style="Topographic" mcluster="0"]</td></tr>
                    <tr><td><b><?php echo __('Single category POI', poimaps_leaflet_cfg()->textdomain);?>:</b> </td><td>[ac_map_category id='ID' title='off' zoom='5' popup="off" poi="off" style="Topographic" mcluster="0"]</td></tr>
                    <tr><td>*title</td><td>on / off</td></tr>
                    <tr><td>*Marker Cluster(mcluster)</td><td>0 = off / 1 = on</td></tr>
                    <tr><td>*Style</td><td>
                            Streets<br>
                            Topographic<br>
                            NationalGeographic<br>
                            Oceans<br>
                            Gray<br>
                            DarkGray<br>
                            Imagery<br>
                            ImageryClarity<br>
                            ImageryFirefly<br>
                            ShadedRelief<br>
                            Terrain<br>
                            USATopo<br>
                            Physical<br>
                        </td></tr>



                </table>
            </td>
        </tr>  
          
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php }

function ac_leaflet_get_style(){
    return get_option('ac_leaflet_style');
}

function ac_leaflet_get_style_label(){
    $def_styles = array(
        'Topographic' => '',
        'Streets' => '',
        'NationalGeographic' => '',
        'Oceans' => 'OceansLabels',
        'Gray' => 'GrayLabels',
        'DarkGray' => 'DarkGrayLabels',
        'Imagery' => 'ImageryLabels',
        'ImageryClarity' => 'ImageryTransportation',
        'ImageryFirefly' => 'ImageryTransportation',
        'ShadedRelief' => 'ShadedReliefLabels',
        'Terrain' => 'TerrainLabels',
        'USATopo' => 'TerrainLabels',
        'Physical' => 'TerrainLabels'
    );
    return $def_styles[get_option('ac_leaflet_style')];
}

function ac_leaflet_get_mcluster(){
    return get_option('ac_leaflet_mcluster');
}
?>