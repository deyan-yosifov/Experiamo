<?php 
	$custom_fields = get_post_custom();
	$latitude = $custom_fields['latitude'][0];
	$longitude = $custom_fields['longitude'][0];
	$zoom = $custom_fields['zoom'][0];
	$icon_size = $custom_fields['icon_size'][0];
	$icon = $custom_fields['icon'][0];
?>

<table>
	<tr>
		<th><?php _e('Latitude', 'dpyTravelRoutes');?></th>
		<td><input type="text" name="latitude" value="<?php echo $latitude;?>" readonly="readonly"/></td>
	</tr>
	<tr>
		<th><?php _e('Longitude', 'dpyTravelRoutes');?></th>
		<td><input type="text" name="longitude" value="<?php echo $longitude;?>" readonly="readonly"/></td>
	</tr>
	<tr>
		<th><?php _e('Zoom', 'dpyTravelRoutes');?></th>
		<td><input type="text" name="zoom" value="<?php echo $zoom;?>" readonly="readonly"/></td>
	</tr>
	<tr>
		<th><?php _e('Icon size', 'dpyTravelRoutes');?></th>
		<td>
			<input type="number" name="icon_size" value="<?php echo $icon_size;?>" />
			<a href="javascript:" id="dpy_update_icon_size">Update</a>
		</td>
	</tr>
	<tr>
		<th><a href="javascript:" id="dpy_icon_add" class="button button-primary"><?php _e('Choose marker icon', 'dpyTravelRoutes');?></a></th>
		<td>
			<input type="hidden" name="icon" value="<?php echo $icon;?>" readonly="readonly"/>
			<span id="dpy_icon_wrapper">
				<img src="<?php echo $icon;?>" alt="icon image" width="<?php echo $icon_size?>" height="<?php echo $icon_size?>" id="icon_img" />
				<a href="javascript:" id="dpy_icon_del">Remove icon</a>
			</span>			
			<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
		    <script>	
		    var map, marker;	  
		    var iconURL = "<?php echo $icon;?>";
			var iconSize = parseInt("<?php echo $icon_size;?>");
			  
			function initializeMap() {	
				//var map;	
				var mapOptions = {
					zoom: <?php echo $zoom;?>,
					center: new google.maps.LatLng(<?php echo $latitude;?>, <?php echo $longitude;?>),
					mapTypeId: google.maps.MapTypeId.HYBRID
				};
				map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
								
				marker = new google.maps.Marker({
				    position: map.getCenter(),
				    map: map,
				  });
				
				if(iconURL && iconURL != ""){					
					marker.setIcon({
					     url: iconURL, // url
					     size: new google.maps.Size(iconSize, iconSize), // size
					     origin: new google.maps.Point(0,0), // origin
					     anchor: new google.maps.Point(iconSize/2.0,iconSize/2.0) // anchor 
					 });
				}

				var input_latitude = document.getElementsByName("latitude")[0];
				var input_longitude = document.getElementsByName("longitude")[0];
				var input_zoom = document.getElementsByName("zoom")[0];
				
				google.maps.event.addListener(map, 'click', function(event) {
					var position = event.latLng;
					//map.panTo(position);
					marker.setPosition(position);
					input_latitude.value = position.lat();
					input_longitude.value = position.lng();
				});

				google.maps.event.addListener(map, 'zoom_changed', function(event) {
					input_zoom.value = map.getZoom();
				});
			};
		
			google.maps.event.addDomListener(window, 'load', initializeMap);
					
			var icon_wrapper = document.getElementById("dpy_icon_wrapper");
			var icon_input = document.getElementsByName("icon")[0];
			var icon_img = document.getElementById("icon_img");
			var icon_del = document.getElementById("dpy_icon_del");
			var icon_add = document.getElementById("dpy_icon_add");
			var icon_update_size= document.getElementById("dpy_update_icon_size");
			var icon_size_input = document.getElementsByName("icon_size")[0];

			if(!iconURL || iconURL==""){
				icon_wrapper.style.display="none";
			}
			else {
				icon_wrapper.style.display="inline";
			}

			icon_update_size.addEventListener("click", function(){
				var iconURL = icon_img.src;
				var iconSize = parseInt(icon_size_input.value);
				if(iconURL != "" && icon_wrapper.style.display != "none"){					
					marker.setIcon({
					     url: iconURL, // url
					     size: new google.maps.Size(iconSize, iconSize), // size
					     origin: new google.maps.Point(0,0), // origin
					     anchor: new google.maps.Point(iconSize/2.0,iconSize/2.0) // anchor 
					 });

					icon_img.width = iconSize;
					icon_img.height = iconSize;
				}
			});
			
			icon_del.addEventListener("click", function(){
				icon_input.value="";
				icon_img.src="";
				icon_wrapper.style.display="none";	
				marker.setIcon(null);
			});

			icon_add.addEventListener("click", function(){
				var file_frame;
				if(file_frame){ file_frame.open(); return; }
				file_frame = wp.media.frames.file_frame = wp.media({
					title: "Choose destination icon!",
					button: {
						text: "Select",
					},
					multiple: false
				});
				file_frame.on( 'select', function(){
					attachment = file_frame.state().get('selection').first().toJSON();

					icon_input.value=attachment.url;
					icon_img.src=attachment.url;
					icon_wrapper.style.display="inline";

					var iconURL = attachment.url;
					var iconSize = parseInt(icon_size_input.value);
					if(iconURL != ""){					
						marker.setIcon({
						     url: iconURL, // url
						     size: new google.maps.Size(iconSize, iconSize), // size
						     origin: new google.maps.Point(0,0), // origin
						     anchor: new google.maps.Point(iconSize/2.0,iconSize/2.0) // anchor 
						 });
					}
				});
				file_frame.open();
			});	
			</script>
		</td>
	</tr>
	<tr>
		<th><?php _e('Select position on google maps:', 'dpyTravelRoutes');?></th>
		<td>			 		    
		    <div id="map-canvas" style="width:800px; height:500px; margin:10px auto;"></div>
		</td>
	</tr>
</table>