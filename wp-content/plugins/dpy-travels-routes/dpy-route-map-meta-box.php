<?php 
	$custom_fields = get_post_custom();
	//var_dump($custom_fields);
?>

<table>
	<tr>
		<th><?php _e('Test field', 'dpyTravelRoutes');?></th>
		<td><input type="text" name="test" value="<?php echo $custom_fields['test'][0];?>" /></td>
	</tr>
	<tr>
		<th><?php _e('Draw route on google maps:', 'dpyTravelRoutes');?></th>
		<td>
			<script>	
			var markers = [];
			function addMarkersOnClick(map, createAndReturnMarkerOnPositionFunction) {
			
				google.maps.event.addListener(map, 'click', function(event) {
					var position = event.latLng;
					map.panTo(position);
					var marker = createAndReturnMarkerOnPositionFunction(position);
					
					google.maps.event.addListener(marker, 'rightclick', function() {
						var result = prompt("Change marker title:", marker.getTitle());
						if(result != null) {
							marker.setTitle(result);
						}
						else {
							var index = markers.indexOf(marker);
							if (index > -1) {
								markers.splice(index, 1);
							}
							marker.setMap(null);
						}
					});
					
					markers.push(marker);
				});
			};
			
			function getMarkerInfos() {
				var infos = [];
				
				for(var i=0; i<markers.length; i+=1){
					infos.push({
						position: markers[i].getPosition(),
						title: markers[i].getTitle()
					});
				}
				
				return JSON.stringify(infos);
			};
			</script>  
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
				
				var infoWindow = new google.maps.InfoWindow({maxWidth: 200});
				var showInfo = function(position, content) {
					map.panTo(position);
					infoWindow.setContent(content);
					infoWindow.setPosition(position);
					infoWindow.open(map)
				};		
		
				var showNewInfo = function(position, content) {
					var infoNewWindow = new google.maps.InfoWindow({maxWidth: 200});
					var wrapper = document.createElement('div');
					wrapper.innerHTML = content;
					infoNewWindow.setContent(wrapper);
					infoNewWindow.setPosition(position);
					infoNewWindow.open(map)
				};			
				
				var addMarker = function(position, title, showInfoWindow) {
					var marker = new google.maps.Marker({
						position: position,
						icon: {
							path: google.maps.SymbolPath.CIRCLE,
							scale: 6.18,
							fillColor: '#FF0000',
							fillOpacity: 0.35,
							strokeColor: '#FF7777',
						},
						map: map,
						title: title
					});
					
					if(showInfoWindow){
						showNewInfo(position, title);
					}
					
					google.maps.event.addListener(marker, 'click', function() {
						showInfo(marker.getPosition(), marker.getTitle());
					});
					
					return marker;
				};
				
				var meetingMarkers = [{"position":{"d":42.671848159549086,"e":23.350630402565002},"title":"Среща на метростанция \"Жулио Кюри\" в 15:40"},{"position":{"d":42.67575474319189,"e":23.346880674362183},"title":"Зала \"София\" Бадминтон"}];
				for(var i=0; i<meetingMarkers.length;i+=1){
					var marker = meetingMarkers[i];
					addMarker(new google.maps.LatLng(marker.position.d, marker.position.e), marker.title, true);
				}
			  
				// TODO: Delete this row when uploaded in production!
				addMarkersOnClick(map, function(position) { return addMarker(position, "Marker on:" + position.lat() + "; " + position.lng()); });
			};
		
			google.maps.event.addDomListener(window, 'load', initializeMap);
		    </script>
		    <div id="map-canvas" style="width:500px; height:500px; margin:10px auto;"></div>
		</td>
	</tr>
</table>