<?php

$zieladresse = "REX_VALUE[1]";
$lat = "REX_VALUE[2]";
$lng = "REX_VALUE[3]";

$textSendForm = "REX_VALUE[4]";
$textLegend = "REX_VALUE[5]"; 

$sprache = "REX_VALUE[6]";
$markerfarbe = "REX_VALUE[7]";

$zoomlevel = "REX_VALUE[8]";
$maptype = "REX_VALUE[9]";
$mapstyle = "REX_VALUE[10]";
$infopopup = <<<EOT
REX_VALUE[11]
EOT;
$repl = array("\r\n" => "", "\r" => "", "\n" => "", '"' => "'");
$infopopup = trim(strtr($infopopup, $repl));
$extmap = "REX_VALUE[12]";

if ($sprache == '') { $sprache = 'de'; }
if ($zoomlevel == '') { $zoomlevel = '10'; }
if ($lat == '') { $lat = '50.1109221'; }
if ($lng == '') { $lng = '8.682126700000026'; }
if ($mapstyle == '') { $mapstyle = 'width:100%;height:300px;'; }

$doroute = false;
if ($textLegend <> '' and $textSendForm <> '') { $doroute = true; }


if ($REX['REDAXO'])
{
	echo "Zieladresse: <strong>$zieladresse</strong><br />";
	echo "Latitude: <strong>$lat</strong><br />";
	echo "Longitude: <strong>$lng</strong><br />";
}
else
{
	if (!isset($REX['GoogleMap']['MapCounter'])) $REX['GoogleMap']['MapCounter'] = 0;
	$REX['GoogleMap']['MapCounter']++;
	$mapidx = $REX['GoogleMap']['MapCounter'];
	if ($mapidx==1) $mapidx = '';
?>

<?php if ($mapidx==''){	?>
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;language=<?php echo $sprache; ?>&amp;region=<?php echo strtoupper($sprache) ?>"></script>
<?php } ?>

	<script type="text/javascript">
<?php if ($doroute){	?>
		var directionDisplay<?php echo $mapidx; ?>;
		var directionsService<?php echo $mapidx; ?> = new google.maps.DirectionsService();
		function calcRoute<?php echo $mapidx; ?>(mapidx, zieladresse, lat, lng) {
			var mylatlng = new google.maps.LatLng(lat,lng);
			var start = document.getElementById('start'+mapidx).value;
			var end = mylatlng;
			var request = {
				origin: start,
				destination: end,
				travelMode: google.maps.DirectionsTravelMode.DRIVING
			};
			directionsService<?php echo $mapidx; ?>.route(request, function(response, status){
				if (status == google.maps.DirectionsStatus.OK) {
					$('#directions-panel<?php echo $mapidx; ?>').html('');
					directionsDisplay<?php echo $mapidx; ?>.setDirections(response);
				} else if (status == google.maps.DirectionsStatus.NOT_FOUND) {
					$('#directions-panel<?php echo $mapidx; ?>').html('<p class="warning">Adresse nicht gefunden!</p>');
				} 
			});
		}	
<?php } ?>
		function initializemap<?php echo $mapidx; ?>(){
	
			var myLatlng = new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lng; ?>);

			var myOptions = {
				zoom: <?php echo $zoomlevel; ?>,
				mapTypeId: '<?php echo $maptype; ?>',
				scrollwheel: false,
				center: myLatlng
			};
			var map<?php echo $mapidx; ?> = new google.maps.Map(document.getElementById('map_canvas<?php echo $mapidx; ?>'), myOptions);

			var marker = new google.maps.Marker({
				position: myLatlng, 
				title: '<?php echo $zieladresse ?>',
				icon: 'https://maps.gstatic.com/mapfiles/ms2/micons/<?php echo $markerfarbe; ?>-dot.png',	
				map: map<?php echo $mapidx."\n"; ?>
			});
			
<?php if ($doroute){	?>
			directionsDisplay<?php echo $mapidx; ?> = new google.maps.DirectionsRenderer();
			directionsDisplay<?php echo $mapidx; ?>.setMap(map<?php echo $mapidx; ?>);
			directionsDisplay<?php echo $mapidx; ?>.setPanel(document.getElementById('directions-panel<?php echo $mapidx; ?>'));
<?php } ?>
			
<?php if ($infopopup<>''){	?>
			var contentString = '<?php echo $infopopup; ?>';
			var infowindow = new google.maps.InfoWindow({
				content: contentString
			});
			infowindow.open(map<?php echo $mapidx; ?>, marker);
			google.maps.event.addListener(marker, 'click', function() {
				infowindow.open(map<?php echo $mapidx; ?>, marker);
			});
<?php } ?>
		}

		google.maps.event.addDomListener(window, 'load', initializemap<?php echo $mapidx; ?>);
	</script>

<!-- Google Maps Routenplaner -->	
<div id="googelmapsroutenplaner<?php echo $mapidx; ?>" class="googelmapsroutenplaner">

	<!-- Ausgabe der Karte -->
	<div id="map_canvas<?php echo $mapidx; ?>" class="map_canvas" style="<?php echo trim($mapstyle); ?>"></div>

<?php if ($extmap<>''){	?>
	<a class="externerkartenlink" href="http://maps.google.de/maps?q=<?php echo urlencode($zieladresse); ?>"><?php echo $extmap; ?></a>
<?php } ?>

<?php if ($doroute){	?>
	<!-- Formular -->
	<form id="routenplaner<?php echo $mapidx; ?>" class="routenplaner" action="javascript:void(0);" onsubmit="return calcRoute<?php echo $mapidx; ?>('<?php echo $mapidx; ?>', '<?php echo $zieladresse ?>', '<?php echo $lat ?>', '<?php echo $lng ?>')">
		<fieldset>
		<?php if ($textLegend != '') {echo '<legend>'.$textLegend.'</legend>';} ?>
			<input type="text" id="start<?php echo $mapidx; ?>" value="" />
			<input name="submit" type="submit" class="submit" value="<?php echo $textSendForm ?>" />
		</fieldset>
	</form>

	<!-- Ausgabe der Wegbeschreibung -->
	<div id="directions-panel<?php echo $mapidx; ?>" class="directions-panel"></div>
<?php } ?>

</div>
<!-- /Google Maps Routenplaner -->

<?php
}
?>