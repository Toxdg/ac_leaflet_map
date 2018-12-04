;(function($){
    $(document).ready(function() {
	console.log('Map start');
        var mapWarapper = $('.ac_leaflet_map');
        if(!mapWarapper.length){
            console.log('Map not found');
            return;
        }
        
        mapWarapper.each(function( index ) {
            var mapType = $(this).data('map');
            if(mapType == 'single'){
                single_map_init($(this));
            }else if(mapType == 'category'){
                category_map_init($(this));
            }else{
                full_map_init($(this));
            }
        });
        
    });
    function make_icon(ikona, url_data){
        if(ikona == ''){
            ikona = url_data;
        }
        var ikonaArray = ikona.split(',');
        console.log(ikonaArray);
        var mapIcon = L.icon({
            iconUrl: ikonaArray[0],
            iconSize:     [ikonaArray[1], ikonaArray[2]], // size of the icon
            iconAnchor: [ikonaArray[1]/2, ikonaArray[2]],
            popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
        });
        return mapIcon;
    }
    /*
     *
     *
     * single map
     *
     *
     *
     * */
    function single_map_init($element){
        var mapWrapper = $element.data('id');
        
        var id = $element.attr('id');
        var lat = $element.data('lat');
        var lng = $element.data('lng');
        var popup = $element.data('popup');
        var content = $element.data('content');
        var url_data = $element.data( 'url');
        var ikona = $element.data( 'icon');
        var zoom = $element.data( 'zoom');
        var style = $element.data( 'style');
        var style_l = $element.data( 'style_l');
        // wlasciwa mapa
        var map = L.map(mapWrapper).setView([lat, lng], zoom);

        L.esri.basemapLayer(style).addTo(map);
        if(style_l != ''){
            L.esri.basemapLayer(style_l).addTo(map);
        }

        // przełaczanie
        //map.dragging.disable();
        //map.dragging.enable();
        map.scrollWheelZoom.disable();
        map.on('click', function() {
            if (map.scrollWheelZoom.enabled()) {
                map.scrollWheelZoom.disable();
            }else{
                map.scrollWheelZoom.enable();
            }
        });

        //jesli ikona jest ustawiona
        var mapIcon = make_icon(ikona, url_data);


        if(content != '' && popup == 1){
            L.marker([lat, lng], {icon: mapIcon}).bindPopup(content).addTo(map);
            //L.marker([lat, lng]).bindPopup(content).addTo(map);
        }else{
            L.marker([lat, lng], {icon: mapIcon}).addTo(map);
            //L.marker([lat, lng]).addTo(map);
        }
        
    }
    /*
     *
     *
     * category map
     *
     *
     *
     * */
    function category_map_init($element){
        var mapWrapper = $element.data('id');
        var $liczba_cat = 0;
        var zoom = $element.data( 'zoom');
        var markers = [];
        var marker;
        var mcluster = $element.data( 'mcluster');
        var url_data = $element.data( 'url');
        var $latFld_set = $element.data( 'latfld');
        var $lngFld_set = $element.data( 'lngfld');
        var popup = $element.data( 'popup');
        var style = $element.data( 'style');
        var style_l = $element.data( 'style_l');
        var thisPopup;
        // tu mod
        var szer_cat = 0;
        var dlug_cat = 0;
        var ile_cat = 0;
        var list_wrapper = $element.find('.ac_poimaps_list_post_cat > li');
        list_wrapper.each(function(){
            szer_cat = szer_cat + $(this).data( 'lat');
            dlug_cat = dlug_cat + $(this).data( 'lng');
            ile_cat = ile_cat + 1;
        });
        if(ile_cat > 1) {
            $latFld_set = szer_cat / ile_cat;
            $lngFld_set = dlug_cat / ile_cat;
        }
        // tu koniec

        // wlasciwa mapa
        
        var map = L.map(mapWrapper).setView([$latFld_set, $lngFld_set], zoom);
        L.esri.basemapLayer(style).addTo(map);
        if(style_l != ''){
            L.esri.basemapLayer(style_l).addTo(map);
        }
        
        map.scrollWheelZoom.disable();
        map.on('click', function() {
            if (map.scrollWheelZoom.enabled()) {
                map.scrollWheelZoom.disable();
            }else{
                map.scrollWheelZoom.enable();
            }
        });


        var markerCluster = L.markerClusterGroup();
        //generowanie markerow
        
        list_wrapper.each(function(index){
            //console.log('index cat: '+index);
            var szer = $(this).data( 'lat');
            var dlug = $(this).data( 'lng');
            var content = $(this).data('content');
            var ico_data = $(this).data( 'icon');
            
            //jesli ikona jest ustawiona
            var mapIcon = make_icon(ico_data, url_data);

            // jesli sa wspolrzedne geograficzne
            if (szer != '' && dlug !=''){
                if(content != '' && popup == 1){
                    marker = L.marker([szer, dlug], {wi_index: index, icon: mapIcon, alt: {index: index, opis: content}}).bindPopup(content);
                    //marker = L.marker([szer, dlug], {wi_index: index, alt: {index: index, opis: content}}).bindPopup(content);
                }else{
                    marker = L.marker([szer, dlug], {wi_index: index, alt: {index: index, opis: content}, icon: mapIcon});
                    //marker = L.marker([szer, dlug], {wi_index: index, alt: {index: index, opis: content}});
                }
                markers.push(marker);
                markerCluster.addLayer(marker);
            }else{
                //cos poszlo nie tak
                console.log('error point');
            }
        });
        //dodanie markerów do mapy
        if(mcluster == 1){
            map.addLayer(markerCluster);
        }else{
            L.featureGroup(markers).addTo(map);
        }

    }
    /*
     *
     *
     * full map
     *
     *
     *
     * */
    function full_map_init($element){
        var mapWrapper = $element.data('id');
        var zoom = $element.data( 'zoom');
        var url_data = $element.data( 'url');
        var $latFld_set = $element.data( 'latfld');
        var $lngFld_set = $element.data( 'lngfld');
        var popup = $element.data( 'popup');
        var style = $element.data( 'style');
        var style_l = $element.data( 'style_l');
        var list_wrapper = $element.find('.ac_poimaps_full_list_post > li');

        var mcluster = $element.data( 'mcluster');
        var markers = [];
        var markers_all = [];
        var marker;
        
        // wlasciwa mapa
        var map = L.map(mapWrapper).setView([$latFld_set, $lngFld_set], zoom);
        L.esri.basemapLayer(style).addTo(map);
        if(style_l != ''){
            L.esri.basemapLayer(style_l).addTo(map);
        }
        
        map.scrollWheelZoom.disable();
        map.on('click', function() {
            if (map.scrollWheelZoom.enabled()) {
                map.scrollWheelZoom.disable();
            }else{
                map.scrollWheelZoom.enable();
            }
        });

        var markerCluster = L.markerClusterGroup();

        // czyszczenie mapy
        function remove_markers(){
            $.each( markers, function( key, value ) {
              map.removeLayer(value);
            });
            markers = [];
            //map.removeLayer(marker2);
            markerCluster.clearLayers();
          }
        // generowanie listy markerow
        list_wrapper.each(function(index){
            //console.log('index cat: '+index);
            var szer = $(this).data( 'lat');
            var dlug = $(this).data( 'lng');
            var content = $(this).data('content');
            var ico_data = $(this).data( 'icon');
            //jesli ikona jest ustawiona
            var mapIcon = make_icon(ico_data, url_data);
            // jesli sa wspolrzedne geograficzne
            if (szer != '' && dlug !=''){
                if(content != '' && popup == 1){
                    marker = L.marker([szer, dlug], {wi_index: index, icon: mapIcon, alt: {index: index, opis: content}}).bindPopup(content);
                    //marker = L.marker([szer, dlug], {wi_index: index, alt: {index: index, opis: content}}).bindPopup(content);
                }else{
                    marker = L.marker([szer, dlug], {wi_index: index, icon: mapIcon, alt: {index: index, opis: content}});
                    //marker = L.marker([szer, dlug], {wi_index: index, alt: {index: index, opis: content}});
                }
                markers.push(marker);
                markerCluster.addLayer(marker);
            }else{
                //cos poszlo nie tak
                console.log('error point');
            }

        });
        //dodanie markerów do mapy
        //L.featureGroup(markers).addTo(map);
        if(mcluster == 1){
            map.addLayer(markerCluster);
        }else{
            L.featureGroup(markers).addTo(map);
        }

        //
        //filtr
        //
        var $category = $element.find('.cat_select');
        var $region = $element.find('.woj_select');
        reset_filter();
        function reset_filter(){
            $category.prop('selectedIndex',0);
            $region.prop('selectedIndex',0);
        }
        $region.change(function() {
            var var_id = $category.find("option:selected").val();
            var var_woj = $region.find(" > option:selected").val();
            wybrane(var_id, var_woj);
        });
        $category.change(function() {
            var var_id = $category.find("option:selected").val();
            var var_woj = $region.find(" > option:selected").val();
            wybrane(var_id, var_woj);
        });
        function wybrane(id, woj){
            console.log('wybrane kryteria: id '+id+' woj '+woj);
            remove_markers();
            markers = []; // resetuje zawartosc markers
            // sprawdzanie współrzednych woj           
            if (typeof woj === "undefined") {
                $latFld_set = $element.data( 'latfld');
                $lngFld_set = $element.data( 'lngfld');
                woj = '0';
            }else{
                $latFld_set = $region.find(" > option:selected").data('lnt');
                $lngFld_set = $region.find(" > option:selected").data('lng');
            }
            /*
             * petla
             * */
            var $ile_pkt = 0;
            var $sum_szer = 0;
            var $sum_dl = 0;
            var $lista_pkt = [];
            list_wrapper.each(function(index){
                var $this = $(this);
                var szer = $(this).data( 'lat');
                var dlug = $(this).data( 'lng');
                var ico_data = $(this).data( 'icon');
                var content = $(this).data('content');
                //jesli ikona jest ustawiona
                var mapIcon = make_icon(ico_data, url_data);
                // jesli sa wspolrzedne geograficzne
                var cat_id = $( this ).data( 'catid');
                var region_name = $( this ).data( 'country');
                var array_region = region_name.split(',');
                var array_category = cat_id.split(',');

                if($.inArray(woj, array_region) != -1 && $.inArray(id, array_category) != -1){
                    $lista_pkt.push($this);
                    $sum_szer = $sum_szer + szer;
                    $sum_dl = $sum_dl + dlug;
                    $ile_pkt++;
                    
                    if (szer != '' && dlug !=''){
                        if(content != '' && popup == 1){
                            
                            marker = L.marker([szer, dlug], {wi_index: index, icon: mapIcon, alt: {index: index, opis: content}}).bindPopup(content);
                            //marker = L.marker([szer, dlug], {wi_index: index, alt: {index: index, opis: content}}).bindPopup(content);
                        }else{
                            marker = L.marker([szer, dlug], {wi_index: index, icon: mapIcon, alt: {index: index, opis: content}});
                            //marker = L.marker([szer, dlug], {wi_index: index, alt: {index: index, opis: content}});
                        }
                        // dodaje marker do tablicy markerow
                        markers.push(marker);
                        markerCluster.addLayer(marker);
                    }
                }else{
                    //cos poszlo nie tak
                    console.log('punk nie spełnia warunków');
                }
            });   
            //dodanie markerów do mapy
            //L.featureGroup(markers).addTo(map);
            if(mcluster == 1){
                map.addLayer(markerCluster);
            }else{
                L.featureGroup(markers).addTo(map);
            }
        }  
    }
    
    
    
    function generate_marker($icon_url){

        var img = new Image();
        var img_h = 0;
        var img_w = 0;
        var icon;
        img.src = $icon_url;
        img.onload = function() {
            img_h = this.height;
            img_w = this.width;
            icon = L.icon({
                iconUrl: $icon_url,
                iconSize: [img_w, img_h], // size of the icon
            });
            console.log(icon);
            return icon;
        }
        
    }
})(jQuery);