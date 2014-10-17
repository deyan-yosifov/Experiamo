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

	if($posttype === "route"){
?>
{"postid" : <?php echo $postid?> }
<?php
	}else if($posttype == "destination"){
?>
{"postid" : <?php echo $postid?> }
<?php
	}
endif;
wp_reset_query();
?>
 
