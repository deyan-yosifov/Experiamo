<?php
// Our include
define('WP_USE_THEMES', false);
require_once('../../../../../wp-load.php');
 
// Our variables
$theme = (isset($_GET['theme'])) ? $_GET['theme'] : "All";
$type = (isset($_GET['type'])) ? $_GET['type'] : "All";
$keyword = (isset($_GET['keyword'])) ? $_GET['keyword'] : "";
 ?>
 
 <?php 
class ItemGenerator{
	public static function showCurrentItem(){

	$imageSize = array(50,50);
?>
<div class="row javo_somw_list_inner">
	<div class="col-md-3 thumb-wrap">
		<?php if(has_post_thumbnail()) the_post_thumbnail(/*$imageSize*/);?>
	</div><!-- col-md-3 thumb-wrap -->

	<div class="cols-md-9 meta-wrap">
		<div class="javo_somw_list">
			<a href="#"><?php the_title();?></a>
		</div>
		<div class="javo_somw_list"><?php the_content("[more...], true")?></div>
	</div><!-- col-md-9 meta-wrap -->
</div>

<?php 		
	}
	
	public static function showQueryItems($queryArray){
		query_posts($queryArray);
		if (have_posts()) :
			while (have_posts()) :
				the_post();
				ItemGenerator::showCurrentItem();
			endwhile;
		endif;
		wp_reset_query();
	}
}
?>
 
<?php 
if($type!=="POIs") :
	$routeQuery = array(
	'post_type' => DPY::ROUTE_POST_NAME,
	's' => $keyword
	);

	if($type !== "All"){
		$typeName = get_term($type, "route_type")->name;
		$routeQuery["route_type"] = $typeName;
	}
	if($theme !== "All"){
		$themeName = get_term($theme, "route_theme")->name;
		$routeQuery["route_theme"] = $themeName;
	}
	
	ItemGenerator::showQueryItems($routeQuery);
endif;
?>

<?php 
if($type==="All" || $type==="POIs") :
	$destinationQuery = array(
	'post_type' => DPY::DESTINATION_POST_NAME,
	's' => $keyword
	);

	if($theme !== "All"){
		$themeName = get_term($theme, "route_theme")->name;
		$destinationQuery["route_theme"] = $themeName;
	}
	
	ItemGenerator::showQueryItems($destinationQuery);
endif;
?>