!function(e){e(document).ready(function(){console.log("Map start");var l,o=e("#latFld").val(),a=e("#lngFld").val();""!=o&&""!=a?console.log("latlng not empty"):(o="52.173931692568",a="18.8525390625"),l=L.map("map_canvas").setView([o,a],5),L.esri.basemapLayer("Topographic").addTo(l);var n=L.esri.Geocoding.geocodeService(),t=(L.icon({iconUrl:"https://leafletjs.com/examples/custom-icons/leaf-green.png",shadowUrl:"https://leafletjs.com/examples/custom-icons/leaf-shadow.png",iconSize:[38,95],shadowSize:[50,64],iconAnchor:[22,94],shadowAnchor:[4,62],popupAnchor:[-3,-76]}),L.marker([o,a]).addTo(l));l.on("click",function(o){n.reverse().latlng(o.latlng).run(function(o,a){console.log(a),e("#latFld").val(a.latlng.lat),e("#lngFld").val(a.latlng.lng),l.removeLayer(t),t=L.marker(a.latlng).addTo(l)})}),e(".set_position").click(function(){var o=e("#adres").val();L.esri.Geocoding.geocode().address(o).run(function(o,a,n){console.log(a.results[0]),l.removeLayer(t),e("#latFld").val(a.results[0].latlng.lat),e("#lngFld").val(a.results[0].latlng.lng),t=L.marker(a.results[0].latlng).addTo(l)})}),e(".marker_input").length&&(e("#set-ico-button").click(function(){return formfield=e("#set-ico-button").attr("name"),tb_show("","media-upload.php?type=image&amp;TB_iframe=true"),!1}),window.send_to_editor=function(l){imgurl=e(l).attr("src"),e("#marker").val(imgurl),tb_remove()},e("#reset_ico").click(function(){e("#marker").val("")}))})}(jQuery);