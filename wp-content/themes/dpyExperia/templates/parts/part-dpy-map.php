<?php
$dpy_map_height = 800;
$dpy_map_height_gmap = sprintf('position:relative; height:%spx;', $dpy_map_height );
?>

<div class="gmap" style="<?php echo $dpy_map_height_gmap;?>">

	<div class="javo_somw_panel" id="map-panel">

		<form onsubmit="return false">

			<div class="newrow">

				<h4 class="title"><?php _e('Theme', 'dpyTravelRoutes');?></h4>		
				
				<button value="All" class="dpy_theme_btn active"><?php _e('All', 'dpyTravelRoutes');?></button>	
				<?php 
					$themes = get_terms(array('route_theme'));					
					wp_reset_query();					
					foreach($themes as $item){					
						printf('<button value="%s" class="%s">%s</button>', $item->term_id, "dpy_theme_btn", $item->name);					
					};
				?>	

			</div>

			<div class="newrow">

				<h4 class="title"><?php _e('Type', 'dpyTravelRoutes');?></h4>				
				<button value="All" class="dpy_type_btn active"><?php _e('All', 'dpyTravelRoutes');?></button>	
				<button value="POIs" class="dpy_type_btn"><?php _e('POIs', 'dpyTravelRoutes');?></button>	
				<?php 
					$types = get_terms(array('route_type'));					
					wp_reset_query();					
					foreach($types as $item){					
						printf('<button value="%s" class="%s">%s</button>', $item->term_id, "dpy_type_btn", $item->name);					
					};
				?>
			</div>			

			<div class="newrow">

				<h4 class="title"><?php _e('Keyword', 'dpyTravelRoutes');?></h4>

				<input id="dpy_keyword" type="text" class="fullcolumn">

			</div>

		</form>

		<section class="newrow">

			<h4 class="javo_somw_list_title"><?php _e('List', 'dpyTravelRoutes');?></h4>

			<article class="output"></article>

		</section>

	</div> <!-- javo_somw_panel -->

	<span class="javo_somw_opener_type1 active"><?php _e('Hide', 'dpyTravelRoutes');?></span>

	<div class="map_area" id="dpy-map-canvas" style="height:100%; width:100%"></div> <!-- map_area : it shows map part -->

</div><!-- Gmap -->

<script type="text/javascript">
// Show/Hide side panel
(function($){

	var _panel = $(".javo_somw_panel");

	$("body").on("click", ".javo_somw_opener_type1", function(){

		if( $(this).hasClass("active") ){

			$(this).animate({marginLeft:-(parseInt(_panel.outerWidth())) + "px" }, 500);

			_panel.animate({marginLeft:-(parseInt(_panel.outerWidth())) + "px"}, 500);

			$(".map_area").animate({marginLeft:0}, 500, function(){

				$(".map_area").gmap3({ trigger:"resize" });

			});

			$(this).text("Show").removeClass('active');

		}else{

			$(this).animate({marginLeft:0}, 500);

			_panel.animate({marginLeft:0}, 500);

			$(".map_area").animate({marginLeft:parseInt(_panel.outerWidth()) + "px"}, 500, function(){

				$(".map_area").gmap3({ trigger:"resize" });

			});

			$(this).text("Hide").addClass('active');

		};

	});

	$(".map_area").css({marginLeft:parseInt(_panel.outerWidth()) + "px"});

	$(".javo_somw_opener_type1").css({

		"top": "50%"

		, "left": parseInt(_panel.outerWidth()) - 2 + "px"

	});

})(jQuery);

</script>