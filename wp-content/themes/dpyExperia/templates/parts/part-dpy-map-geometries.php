<?php
header('Content-Type: application/json');
// Our include
define('WP_USE_THEMES', false);
require_once('../../../../../wp-load.php');
 
// Our variables
$postid = $_GET['postid'];
$posttype = $_GET['posttype'];

$args = array( 'p' => $postid, 'post_type' => $posttype);
query_posts($args);

if (have_posts()) :
	the_post();
	$custom_fields = get_post_custom();

	if($posttype === "route"){
		$mapjson = $custom_fields['mapjson'][0];
		echo $mapjson;
	}else if($posttype == "destination"){		
		$latitude = $custom_fields['latitude'][0];
		$longitude = $custom_fields['longitude'][0];
		$zoom = $custom_fields['zoom'][0];
		$icon_size = $custom_fields['icon_size'][0];
		$icon = $custom_fields['icon'][0];
?>

{
"latitude" : <?php echo $latitude?> ,
"longitude" : <?php echo $longitude?> ,
"zoom" : <?php echo $zoom?> ,
"icon_size" : <?php echo $icon_size?> ,
"icon" : "<?php echo $icon?>"	
}

<?php
	}
endif;
wp_reset_query();
?>
 
