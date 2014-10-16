<?php 
	$custom_fields = get_post_custom();
	$mapjson = $custom_fields['mapjson'][0];
	
?>
		    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=drawing"></script>
<!-- 		    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script> -->
<!-- 		    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&libraries=drawing"></script> -->
<table>
	<tr>
		<th><?php _e('MapJSON', 'dpyTravelRoutes');?></th>
		<td><textarea name="mapjson" readonly="readonly"><?php echo $mapjson;?></textarea></td>
	</tr>
	<tr>
		<th><?php _e('Select stroke color', 'dpyTravelRoutes');?></th>
		<td><input type="color" id="strokeColor" onchange="getPolyOptions();" /></td>
	</tr>
	<tr>
		<th><?php _e('Select fill color', 'dpyTravelRoutes');?></th>
		<td><input type="color" id="fillColor" onchange="getPolyOptions();" /></td>
	</tr>
	<tr>
		<th><?php _e('Select stroke thickness', 'dpyTravelRoutes');?></th>
		<td><input type="number" value="2" onchange="getPolyOptions();" id="strokeThickness" /></td>
	</tr>
	<tr>
		<th><?php _e('Draw route on google maps:', 'dpyTravelRoutes');?></th>
		<td> 
		    <div id="map-canvas" style="width:800px; height:500px; margin:10px auto;"></div>
		</td>
	</tr>
</table>
    <script>
		var unselectedMarkerIcon = {
			path: google.maps.SymbolPath.CIRCLE,
			scale: 6.18,
			fillColor: '#FF0000',
			fillOpacity: 0.35,
			strokeColor: '#FF7777',
		};
    
		var routeInfo = <?php echo $mapjson;?>;
		var mapInfoUI = document.getElementsByName("mapjson")[0];	
		var infoWindow = new google.maps.InfoWindow({});
		var markers = [];
		var currentMarker = null;
		var drawingManager = null;
	    var selectedShape = null;

	    var inputStroke = document.getElementById("strokeColor");
	    var inputFill = document.getElementById("fillColor");
	    var inputThickness = document.getElementById("strokeThickness");

	    function getPolyOptions(){
			var strokeColor = inputStroke.value;
			var fillColor = inputFill.value;
			var strokeThickness = inputThickness.value;
		    
	    	var polylineOptions = drawingManager.get('polylineOptions');
            polylineOptions.strokeColor = strokeColor;
            polylineOptions.strokeWeight = strokeThickness;
            polylineOptions.editable = true;
            drawingManager.set('polylineOptions', polylineOptions);

            var polygonOptions = drawingManager.get('polygonOptions');
            polygonOptions.fillColor = fillColor;
            polygonOptions.strokeColor = strokeColor;
            polygonOptions.strokeWeight = strokeThickness;
            polygonOptions.editable = true;
            polygonOptions.fillOpacity = 0.45;

            drawingManager.set('rectangleOptions', polygonOptions);	
            drawingManager.set('circleOptions', polygonOptions);	
            drawingManager.set('polygonOptions', polygonOptions);

            return polygonOptions;
		};

		function setPolyOptions(){
			
		};
		
		function addRemoveMarkerToRoute(){
			if(currentMarker.dpy.isIncluded){
				var index = routeInfo["destinationIDs"].indexOf(currentMarker.dpy.destinationId);
				if(index > -1){
					routeInfo["destinationIDs"].splice(index, 1);
				}
				currentMarker.setIcon(unselectedMarkerIcon);
			}else {
				routeInfo["destinationIDs"].push(currentMarker.dpy.destinationId);
				currentMarker.setIcon(currentMarker.dpy.icon);					
			}

			updateMapInfoUI();
			currentMarker.dpy.isIncluded = !currentMarker.dpy.isIncluded;	
			infoWindow.close();
		};
		function getAddRemoveButtonText(){
			return (currentMarker.dpy.isIncluded ? 'Remove from route' : 'Add to route');
		};
		
		function updateMapInfoUI(){
			mapInfoUI.value = JSON.stringify(routeInfo);
		};
	    
		function initializeMap() {	
			var map;	
			var defaultZoom = routeInfo["zoom"];
			var mapCenterCoordinates = new google.maps.LatLng(routeInfo["latitude"], routeInfo["longitude"]);
			var mapOptions = {
				zoom: defaultZoom,
				center: mapCenterCoordinates,
				mapTypeId: google.maps.MapTypeId.HYBRID
			};
			map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);	

			google.maps.event.addListener(map, 'zoom_changed', function(event) {
				routeInfo["zoom"] = map.getZoom();
				updateMapInfoUI();
			});

			google.maps.event.addListener(map, 'center_changed', function(event) {
				var center = map.getCenter();
				routeInfo["latitude"] = center.lat();
				routeInfo["longitude"] = center.lng();
				updateMapInfoUI();
			});

			var showInfo = function(position, content) {
				map.panTo(position);
				infoWindow.setContent(content);
				infoWindow.setPosition(position);
				infoWindow.open(map)
			};	
			
			// DEFINE DESTINATION MARKERS HERE!
			(function(){					
<?php 							
			$destinationPosts = new WP_Query('post_type='.DPY::DESTINATION_POST_NAME);
			if($destinationPosts->have_posts()) :
				while ($destinationPosts->have_posts()) : $destinationPosts->the_post();
				$destinationPostID = get_the_ID();
				$post_custom_fields = get_post_custom($destinationPostID);
				$latitude = $post_custom_fields['latitude'][0];
				$longitude = $post_custom_fields['longitude'][0];
				$zoom = $post_custom_fields['zoom'][0];
				$icon_size = $post_custom_fields['icon_size'][0];
				$icon = $post_custom_fields['icon'][0];
?>
				(function(){
					var iconURL = "<?php echo $icon ?>";
					var iconSize = parseInt("<?php echo $icon_size ?>");
					var destination = <?php echo $destinationPostID?>;
					var index = routeInfo["destinationIDs"].indexOf(destination);
					var included = index > -1;
		
					var destinationIcon = ((iconURL && iconURL != "") ? {
					     url: iconURL, // url
					     size: new google.maps.Size(iconSize, iconSize), // size
					     origin: new google.maps.Point(0,0), // origin
					     anchor: new google.maps.Point(iconSize/2.0,iconSize/2.0) // anchor 
					 } : null);
					
					var marker = new google.maps.Marker({
						dpy: {
							title:"<?php the_title()?>",
							destinationId: destination,
							isIncluded: included,
							icon : destinationIcon
						},
					    position: new google.maps.LatLng(<?php echo $latitude;?>, <?php echo $longitude;?>),
					    map: map,
					    icon: (included ? destinationIcon : unselectedMarkerIcon),
					  });
		
					markers.push(marker);
				
					google.maps.event.addListener(marker, 'click', function() {
						currentMarker = marker;
						var content = '<div style="width: 200px; height: auto; word-wrap:break-word; overflow:auto;">';
						content += '<div>';
						content += marker.dpy.title;
						content += '</div><div>';
						content += '<input type="button" value="'+getAddRemoveButtonText()+'" onclick="addRemoveMarkerToRoute();"/>';
						content += '</div></div>';
						showInfo(marker.getPosition(), content);
					});
				})();
		
		<?php 
				endwhile;
			endif;
		?>
		})();		
		
		// DEFINE DRAWING MANAGE HERE
		(function(){
								
			var polyOptions = {
	          strokeWeight: 0,
	          fillOpacity: 0.45,
	          editable: true
	        };
	        
	        // Creates a drawing manager attached to the map that allows the user to draw
	        // markers, lines, and shapes.
	        drawingManager = new google.maps.drawing.DrawingManager({
	          drawingMode: google.maps.drawing.OverlayType.POLYGON,
	          markerOptions: { draggable: true },
	          polylineOptions: { editable: true },
	          rectangleOptions: polyOptions,
	          circleOptions: polyOptions,
	          polygonOptions: polyOptions,
	          map: map
	        });

	        getPolyOptions();

	        google.maps.event.addListener(drawingManager, 'overlaycomplete', function(e) {
	            if (e.type != google.maps.drawing.OverlayType.MARKER) {
	            // Switch back to non-drawing mode after drawing a shape.
	            drawingManager.setDrawingMode(null);

	            // Add an event listener that selects the newly-drawn shape when the user
	            // mouses down on it.
	            var newShape = e.overlay;
	            newShape.type = e.type;
	            google.maps.event.addListener(newShape, 'click', function() {
	              setSelection(newShape);
	              console.log("new shape click!");
	            });
	            setSelection(newShape);
	            console.log("overlay complete!");
	          }
	        });

		  function clearSelection() {
            if (selectedShape) {
              selectedShape.setEditable(false);
              selectedShape = null;
            }
          }

          function setSelection(shape) {
            clearSelection();
            selectedShape = shape;
            shape.setEditable(true);
            setPolyOptions();
          }

          function deleteSelectedShape() {
            if (selectedShape) {
              selectedShape.setMap(null);
            }
          }
			        
		})();
	};

	google.maps.event.addDomListener(window, 'load', initializeMap);
    </script>
