<style type="text/css">
.container {
	line-height:25px;
	background:#dee8e8;
	margin:10px 0 10px 0;
	padding: 4px 10px 10px 10px;
	-moz-border-radius:6px;
}

.container strong {
	clear: both;
	display:inline-block;
	width:130px;
	float: left;
}

.container h3 {
	color:gray;
	font-size:16px;
	padding:0 0 0 0;
	margin:0 0 6px 0;
	font-weight:bold;
	border-bottom:1px solid #B1B1B1;
}

.container  input {
	width: 450px;
	background-color: #fff;
	border: 1px solid #afbcc2;
	padding: 3px;
}
.container textarea {
	width: 450px;
	background-color: #fff;
	border: 1px solid #afbcc2;
	padding: 3px;
}

.container select {
	display:inline-block;
	width: 452px;
	padding: 1px;
}

.ui-autocomplete {
	width: 445px;
	background-color: #fff;
	border: 1px solid #afbcc2;
	max-height: 200px;
	overflow-y: auto;
	overflow-x: hidden;
	font-size: 12px;
	padding: 5px;
}

* html .ui-autocomplete {
	height: 200px;
}

.ui-autocomplete li {
	padding: 3px;
}

.ui-autocomplete li:hover {
	cursor: pointer;
	color:red !important;
}

#map_canvas {
	width: 455px;
	height: 300px;
	margin: 5px 0 5px 130px;
	border: 1px solid #afbcc2;
}

.clboth {  
	display:block;
	clear:both;
	line-height:0;
	height:1px;
	font-size:0;
	visibility:hidden;
}
</style>

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&amp;region=DE"></script>

<script type="text/javascript">
//Useful links:
// https://developers.google.com/maps/documentation/javascript/
// http://code.google.com/apis/maps/documentation/javascript/reference.html#Marker
// http://code.google.com/apis/maps/documentation/javascript/services.html#Geocoding
// http://jqueryui.com/demos/autocomplete/#remote-with-cache
// http://stackoverflow.com/questions/8248077/google-maps-v3-standard-icon-shadow-names-equiv-of-g-default-icon-in-v2
      
var geocoder;
var map;
var marker;
var startlat = 'REX_VALUE[2]';
var startlng = 'REX_VALUE[3]';
var startzoom = 'REX_VALUE[8]';
if (startzoom == '') startzoom = 10;
startzoom = parseInt(startzoom);
if (startlat == '') startlat = '50.1109221'; 
if (startlng == '') startlng = '8.682126700000026';
    
function initialize(){
  //MAP
  var latlng = new google.maps.LatLng(startlat, startlng);

  var options = {
    scrollwheel: false,
    zoom: startzoom,
    center: latlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };

  map = new google.maps.Map(document.getElementById("map_canvas"), options);

  //GEOCODER
  geocoder = new google.maps.Geocoder();

  marker = new google.maps.Marker({
    map: map,
    draggable: true
  });

  // Marker
  var location = new google.maps.LatLng(startlat, startlng);
  marker.setPosition(location);
  marker.setIcon('https://maps.gstatic.com/mapfiles/ms2/micons/' + jQuery('#marker').val() + '-dot.png');
  
  // Set Maptype
  map.setMapTypeId(jQuery('#maptype').val());
  
  // Set Zoom
  zoomer = jQuery('#zoom').val();
  zoomer = parseInt(zoomer);
  if (isNaN(zoomer))
    zoomer = 10;
  map.setZoom(zoomer);
  
}

jQuery(document).ready(function(){ 

  initialize();

  jQuery(function() {
    jQuery("#address").autocomplete({
      //This bit uses the geocoder to fetch address values
      source: function(request, response) {
        geocoder.geocode( {'address': request.term }, function(results, status) {
          response(jQuery.map(results, function(item) {
            return {
              label:  item.formatted_address,
              value: item.formatted_address,
              latitude: item.geometry.location.lat(),
              longitude: item.geometry.location.lng()
            }
          }));
        })
      },
      //This bit is executed upon selection of an address
      select: function(event, ui) {
        jQuery("#latitude").val(ui.item.latitude);
        jQuery("#longitude").val(ui.item.longitude);
        var location = new google.maps.LatLng(ui.item.latitude, ui.item.longitude);
        marker.setPosition(location);
        map.setCenter(location);
      }
    });
  });

  //Add listener to marker for reverse geocoding
  google.maps.event.addListener(marker, 'drag', function() {
    geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        if (results[0]) {
          jQuery('#address').val(results[0].formatted_address);
          jQuery('#latitude').val(marker.getPosition().lat());
          jQuery('#longitude').val(marker.getPosition().lng());
        }
      }
    });
  });
  
  // Map-Darstellung geändert - Selectbox ändern
  google.maps.event.addListener(map, 'maptypeid_changed', function(){
    jQuery('#maptype').val(map.getMapTypeId());
	 map.setCenter(marker.getPosition());
  });
  
  // Map-Zoom geändert - Input ändern
  google.maps.event.addListener(map, 'zoom_changed', function(){
    jQuery('#zoom').val(map.getZoom());
	 map.setCenter(marker.getPosition());
  });
  
  // Map-Darstellung über Selectbox ändern
  jQuery('#maptype').bind('change keypress', function(){
    map.setMapTypeId(jQuery('#maptype').val());
    map.setCenter(marker.getPosition());
  });

  // Map-Zoom über Eingabefeld ändern
  jQuery('#zoom').change(function() {
    zoomer = jQuery('#zoom').val();
    zoomer = parseInt(zoomer);
    if (isNaN(zoomer))
      zoomer = 10;
    map.setZoom(zoomer);
	 map.setCenter(marker.getPosition());
  });

  // Marker über Selectbox ändern
  jQuery('#marker').bind('change keypress', function(){
    marker.setIcon('https://maps.gstatic.com/mapfiles/ms2/micons/' + jQuery('#marker').val() + '-dot.png');
  });
  
});
</script>

<div class="container">
<h3>Routenplaner</h3>
	<strong>Zieladresse</strong>
			<input type="text" id="address" name="VALUE[1]" value="REX_VALUE[1]" />	
			
	<strong>Latitude</strong>
		<input type="text" id="latitude" name="VALUE[2]" value="REX_VALUE[2]" />

	<strong>Longitude</strong>
		<input type="text" id="longitude" name="VALUE[3]" value="REX_VALUE[3]" />

	<strong>Infotext</strong>
		<textarea cols="50" rows="3" name="VALUE[11]">REX_VALUE[11]</textarea><br />
		
	<div id="map_canvas"></div>		

	<strong>Darstellung</strong>
		<select id="maptype" name="VALUE[9]">
			<?php
			foreach (array('roadmap', 'satellite', 'hybrid', 'terrain') as $value) {
				echo '<option value="'.$value.'" ';
	
				if ( "REX_VALUE[9]"=="$value" ) {
					echo 'selected="selected" ';
				}
				echo '>'.$value.'</option>';
			}
			?>
		</select>
	 
	<strong>Region (default = de)</strong>
		<input type="text" id="region" name="VALUE[6]" value="REX_VALUE[6]" />

	<strong>Zoom (default = 10)</strong>
			<input type="text" id="zoom" name="VALUE[8]" value="REX_VALUE[8]" />

	<strong>Markerfarbe</strong>
		<span class="right">
			<select id="marker" name="VALUE[7]" >
				<option value='red' <?php if ("REX_VALUE[7]" == 'red') echo 'selected'; ?>>rot</option>
				<option value='green' <?php if ("REX_VALUE[7]" == 'green') echo 'selected'; ?>>gr&uuml;n</option>
				<option value='blue' <?php if ("REX_VALUE[7]" == 'blue') echo 'selected'; ?>>blau</option>
				<option value='ltblue' <?php if ("REX_VALUE[7]" == 'ltblue') echo 'selected'; ?>>t&uuml;rkis</option>
				<option value='yellow' <?php if ("REX_VALUE[7]" == 'yellow') echo 'selected'; ?>>gelb</option>
				<option value='purple' <?php if ("REX_VALUE[7]" == 'purple') echo 'selected'; ?>>lila</option>
				<option value='pink' <?php if ("REX_VALUE[7]" == 'pink') echo 'selected'; ?>>pink</option>
			</select>
		</span>

	<strong>Map-Style</strong>
		<input type="text" id="longitude" name="VALUE[10]" value="REX_VALUE[10]" />

	<strong>Externe Karte</strong>
		<input type="text" id="extmap" name="VALUE[12]" value="REX_VALUE[12]" />
		
	<div class="clboth"></div>
	
</div>

<div class="container">
<h3>Formular f&uuml;r Routenplanung</h3>
	<strong>Legende</strong>
			<input type="text" name="VALUE[5]" value="REX_VALUE[5]" />	

	<strong>Submit-Button</strong>
			<input type="text" name="VALUE[4]" value="REX_VALUE[4]" />	

	<div class="clboth"></div>
</div>

<div class="clboth"></div>
