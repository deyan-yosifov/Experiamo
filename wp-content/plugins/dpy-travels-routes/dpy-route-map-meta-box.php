<?php 
	$custom_fields = get_post_custom();
?>

<table>
	<tr>
		<th><?php _e('Draw route on google maps:', 'dpyTravelRoutes');?></th>
		<td> 
		    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
		    <script>
			function initializeMap() {	
				var map;	
				var defaultZoom = 16;
				var mapCenterCoordinates = new google.maps.LatLng(42.673885293117664, 23.348543643951416);
				var mapOptions = {
					zoom: defaultZoom,
					center: mapCenterCoordinates,
					mapTypeId: google.maps.MapTypeId.HYBRID
				};
				map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);	
					
// 					google.maps.event.addListener(marker, 'click', function() {
// 						showInfo(marker.getPosition(), marker.getTitle());
// 					});
					
			};
		
			google.maps.event.addDomListener(window, 'load', initializeMap);
		    </script>
		    <div id="map-canvas" style="width:800px; height:500px; margin:10px auto;"></div>
		</td>
	</tr>
</table>