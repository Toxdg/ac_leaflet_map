;(function($){
    $(document).ready(function() {
	console.log('Map start'); 
        var map;
        var $latFld_set = $('#latFld').val();
        var $lngFld_set = $('#lngFld').val();

        if( $latFld_set != '' && $lngFld_set != ''){
            console.log('latlng not empty');
        }else{
            $latFld_set = '52.173931692568';
            $lngFld_set = '18.8525390625';
        }
        map = L.map("map_canvas").setView([$latFld_set, $lngFld_set], 5);
        L.esri.basemapLayer("Topographic").addTo(map);

        var geocodeService = L.esri.Geocoding.geocodeService();
        
        var greenIcon = L.icon({
            iconUrl: 'https://leafletjs.com/examples/custom-icons/leaf-green.png',
            shadowUrl: 'https://leafletjs.com/examples/custom-icons/leaf-shadow.png',

            iconSize:     [38, 95], // size of the icon
            shadowSize:   [50, 64], // size of the shadow
            iconAnchor:   [22, 94], // point of the icon which will correspond to marker's location
            shadowAnchor: [4, 62],  // the same for the shadow
            popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
        });
        var marker = L.marker([$latFld_set, $lngFld_set]).addTo(map);
        
        
        map.on('click', function(e) {
            geocodeService.reverse().latlng(e.latlng).run(function(error, result) {
                console.log(result);
                $('#latFld').val(result.latlng.lat);
                $('#lngFld').val(result.latlng.lng);
                //L.marker(result.latlng).addTo(map).bindPopup(result.address.Match_addr).openPopup();
                map.removeLayer(marker);
                //marker = L.marker(result.latlng, {icon: greenIcon}).addTo(map);
                marker = L.marker(result.latlng).addTo(map);
            });
        });
        // sprawdzanie pozycji
        $( ".set_position" ).click(function() {
            var address = $('#adres').val();
            L.esri.Geocoding.geocode().address(address).run(function(err, results, response){
                console.log(results.results[0]);
                map.removeLayer(marker);
                $('#latFld').val(results.results[0].latlng.lat);
                $('#lngFld').val(results.results[0].latlng.lng);
                marker = L.marker(results.results[0].latlng).addTo(map);
            });
        });
        //add marker
        if ( $( ".marker_input" ).length ) {
            $('#set-ico-button').click(function() {
                formfield = $('#set-ico-button').attr('name');
                tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
                return false;
            });

            window.send_to_editor = function(html) {
             //alert(html);
             imgurl = $(html).attr('src');
             //alert(imgurl);
             $('#marker').val(imgurl);
             tb_remove();

            }

            $('#reset_ico').click(function() {
                    $('#marker').val('');
                    //$('#submit').click();
            });
        }
    });
})(jQuery);