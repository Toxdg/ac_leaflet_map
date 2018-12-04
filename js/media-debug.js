jQuery(function($) {
  if ($(".poi-settings").length) {
    //alert();
    var frame;
    var id; // tablica
    var attachment;
    $("#set-ico-button").click(function() {
      id = $("#ico_id").val();
      if (!frame) {
        frame = wp.media({
          frame: "select",
          title: "fotka",
          multiple: false
        });
        frame.on("select", onImageSelect);
        frame.on("open", onImageChecked);
      }
      frame.open();
      return false;
    });

    function onImageChecked() {
      var selection = frame.state().get("selection");
      var library = frame.state().get("library");
      var attachment = wp.media.attachment(id);

      attachment.fetch({
        success: function() {
          library.add(attachment);
          selection.reset([attachment]);
        }
      });
    }
    function onImageSelect() {
      var selection = frame.state().get("selection");

      if (selection.length) {
        selection.each(function(item) {});
        //console.log(selection.first().get('id'));
        //console.log(selection.first().get('sizes').full.url);
        $("#icon_input").val(selection.first().get("sizes").full.url);
        $("#ico_id").val(selection.first().get("id"));
      }
    }

    $("#reset_ico").click(function() {
      $("#icon_input").val("");
      //$('#submit').click();
    });
    //geolocation
    $("#set-geo-button").click(function() {
      getLocation();
    });

    function getLocation() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
      } else {
        alert("Geolocation is not supported by this browser.");
      }
    }

    function showPosition(position) {
      $("#latFld").val(position.coords.latitude);
      $("#lngFld").val(position.coords.longitude);
    }
  }
});
