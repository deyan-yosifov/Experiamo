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
		
	}).on("click", ".dpy_list_item", function(e){
		console.log("change selected item");
	});

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