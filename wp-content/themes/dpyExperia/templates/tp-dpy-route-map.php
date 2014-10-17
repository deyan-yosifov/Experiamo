<?php
/*
 * Template Name: DPY-Route-Map
*/
?>

<?php get_header();?>

<?php get_template_part('templates/parts/part-dpy', 'map');?>

<?php get_footer();?>


<div class="mobile-map">
	<a class="go-under-map"><?php _e('Go Under the Map', 'dpyTravelRoutes');?></a>
</div> <!-- mobile-map-->

<script type="text/javascript">
var map;
var markers=[];
var shapes=[];

jQuery(document).ready(function($){

	jQuery('body').on('click', '.go-under-map', function(){
		$('body, html')
			.animate({ scrollTop: $('.javo_somw_panel').position().top - ($('#stick-nav').offset().top + $('#stick-nav').height()) }, 500);
	
	}).on("click change", ".dpy_theme_btn", function(){
		$(".dpy_theme_btn")
		.removeClass('active');
		$(this).addClass('active');
		var buttonValue = $(this).val();
		console.log("button: "+buttonValue);
		updateQuery();
		
	}).on("click change", ".dpy_type_btn", function(){
		$(".dpy_type_btn")
		.removeClass('active');
		$(this).addClass('active');
		var buttonValue = $(this).val();
		console.log("button: "+buttonValue);
		updateQuery();
		
	}).on("keypress", "#dpy_keyword", function(e){
		if(e.keyCode == 13){
			var keyword = $(this).val();
			console.log("keyword: "+keyword);
			updateQuery();
			return false;
		}
	}).on("change", "#dpy_keyword", function(){		
		var keyword = $(this).val();
		console.log("keyword: "+keyword);
		updateQuery();
		
	}).on("click", ".dpy_list_item > a", function(e){
		console.log("change selected item");
		var postId = $(this).data('postid');
		var postType = $(this).data('posttype');
		changeMapGraphics(postId, postType, true);
	});

	function changeMapGraphics(postId, postType, shouldDeleteExistings){
		console.log(postId + " - " + postType);
		$.ajax({
            type       : "GET",
            data       : {
			                postid : postId,
			                posttype : postType
			                },
            dataType   : "json",
            url        : "<?php echo get_template_directory_uri()?>"+"/templates/parts/part-dpy-map-geometries.php",
            beforeSend : function(){	                
           		// TODO: show some loading gif.
            },
            success    : function(data){   
                if(shouldDeleteExistings){             
            		clearOldGeometries();
                }
                
				if(postType === "destination"){
					addDestination(data);
					zoomAndPosition(data);
				}else if(postType === "route"){
					for(var i = 0; i < data.destinationIDs.length; i+=1){
						changeMapGraphics(data.destinationIDs[i], "destination", false);
					}

					for(var j = 0; j < data.shapes.length; j++){
						addShape(data.shapes[i]);
					}

					zoomAndPosition(data);
				}
            	
            },
            error     : function(jqXHR, textStatus, errorThrown) {
                alert(jqXHR + " :: " + textStatus + " :: " + errorThrown);
            }
	    });
	};

	function addDestination(jsonData){
		var markerOptions = {
				position: new google.maps.LatLng(jsonData.latitude, jsonData.longitude),
				map: map
			};

		if(jsonData.icon && jsonData.icon != ""){
			markerOptions.icon = {
				     url: jsonData.icon, // url
				     size: new google.maps.Size(jsonData.icon_size, jsonData.icon_size), // size
				     origin: new google.maps.Point(0,0), // origin
				     anchor: new google.maps.Point(jsonData.icon_size/2.0,jsonData.icon_size/2.0) // anchor 
				 };
		}
		
		var marker = new google.maps.Marker(markerOptions);
		markers.push(marker);
	};

	// types

    var RECTANGLE = google.maps.drawing.OverlayType.RECTANGLE;
    var CIRCLE = google.maps.drawing.OverlayType.CIRCLE;
    var POLYGON = google.maps.drawing.OverlayType.POLYGON;
    var POLYLINE = google.maps.drawing.OverlayType.POLYLINE;
    var MARKER = google.maps.drawing.OverlayType.MARKER; 

	function addShape(jsonData){
		var shapeType = jsonData.type;
        
        switch (shapeType) {
        case RECTANGLE:
            var rectangle = jsonReadRectangle(jsonData);
            shapes.push(rectangle);
            break;

        case CIRCLE:
            var circle = jsonReadCircle(jsonData);
            shapes.push(circle);
            break;

        case POLYLINE:
            var polyline = jsonReadPolyline(jsonData);
            shapes.push(polyline);
            break;

        case POLYGON:
            var polygon = jsonReadPolygon(jsonData);
            shapes.push(polygon);
            break;
        }
	};

	function jsonReadPolygon(polygon){
  	  var buf = {};
  	  copyProperties(polygon, buf, ["type", "strokeWeight", "fillOpacity", "fillColor", "strokeColor"]);
  	  buf.paths = [];
  	  for(var i = 0; i < polygon.paths.length; i+=1){
			var path = polygon.paths[i];
			buf.paths[i] = jsonReadPath(path);
  	  }
        buf.map = map;
        
        var newShape = new google.maps.Polygon(buf);	        

        return newShape;
    };

    function jsonReadPolyline(polyline){
  	  var buf = {};
  	  copyProperties(polyline, buf, ["type", "strokeWeight", "strokeColor"]);
  	  buf.path = jsonReadPath(polyline.pathArray);  
        buf.map = map;
        
        var newShape = new google.maps.Polyline(buf);
	   
        return newShape;
    };

    function jsonReadPath(pathArray){
    	var path = [];
		for(var i = 0; i < pathArray.length; i+=1){
			path.push(jsonReadPoint(pathArray[i]));
		}

		return path;
  	};

  	function jsonReadCircle(circle){
        var buf = jsonMakeCircle(circle);
        buf.center = jsonReadPoint(circle.center);              
        buf.map = map;
        
        var newShape = new google.maps.Circle(buf);
	       
        return newShape;
    };

    function jsonMakeCircle(circle){
        var buf = {};
        copyProperties(circle, buf, ["type", "strokeWeight", "fillOpacity", "fillColor", "strokeColor", "radius", "center"]);
        buf.editable = false;

        return buf;
    };

    function jsonReadPoint(point){
  	  return new google.maps.LatLng(point.k, point.B);
    }; 

    function jsonReadRectangle(rectangle){
  	  var buf = jsonMakeRectangle(rectangle);
        buf.bounds = new google.maps.LatLngBounds(
                new google.maps.LatLng(rectangle.bounds.Ea.j, rectangle.bounds.va.j),
                new google.maps.LatLng(rectangle.bounds.Ea.k, rectangle.bounds.va.k)
                 );              
        buf.map = map;
        
        var newShape = new google.maps.Rectangle(buf);
	   
        return newShape;
    }

    function jsonMakeRectangle(rectangle){
  	  var buf = {};
        copyProperties(rectangle, buf, ["type", "strokeWeight", "fillOpacity", "fillColor", "strokeColor", "bounds"]);
        buf.editable = false;

        return buf;
    }

    function copyProperties(from, to, props){
		for(var i = 0; i < props.length; i+=1){
			to[props[i]] = from[props[i]];
		}
  	};

	function zoomAndPosition(jsonData){
		map.panTo(new google.maps.LatLng(jsonData.latitude, jsonData.longitude));
		map.setZoom(jsonData.zoom);
	};
	
	function clearOldGeometries(){
		while(markers.length > 0){
			markers.pop().setMap(null);
		}
		while(shapes.length > 0){
			shapes.pop().setMap(null);
		}
	};

	function updateQuery(){
		var queryContent = $("article.output");
		var selectedTheme = $(".dpy_theme_btn.active").first().val();
		var selectedType = $(".dpy_type_btn.active").first().val();
		var selectedKeyword = $("#dpy_keyword").val();
		$.ajax({
	            type       : "GET",
	            data       : {theme : selectedTheme, type: selectedType, keyword: selectedKeyword},
	            dataType   : "html",
	            url        : "<?php echo get_template_directory_uri()?>"+"/templates/parts/part-dpy-map-query-items.php",
	            beforeSend : function(){	                
               		// TODO: show some loading gif.
	            },
	            success    : function(data){                
	            	queryContent.html(data);
	            },
	            error     : function(jqXHR, textStatus, errorThrown) {
	                alert(jqXHR + " :: " + textStatus + " :: " + errorThrown);
	            }
	    });
	};

	updateQuery();	
});

function initializeMap() {	
	//var map;	
	var defaultZoom = 16;
	var mapCenterCoordinates = new google.maps.LatLng(42.673885293117664, 23.348543643951416);
	var mapOptions = {
		zoom: defaultZoom,
		center: mapCenterCoordinates,
		mapTypeId: google.maps.MapTypeId.HYBRID
	};
	map = new google.maps.Map(document.getElementById("dpy-map-canvas"), mapOptions);
					
};

google.maps.event.addDomListener(window, 'load', initializeMap);
</script>