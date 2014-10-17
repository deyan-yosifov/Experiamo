<?php
$queryArray = array('post_type' => array(DPY::ROUTE_POST_NAME, DPY::DESTINATION_POST_NAME));
query_posts($queryArray);
	if (have_posts()) :
		while (have_posts()) :
			the_post();
?>
	<div style="margin:20px auto; padding:20px; width:80%; border: 2px solid #BBBBBB;">
		<h2 style="text-align: center"><?php the_title()?></h2>
		<article style="text-align: center"><?php the_content()?></article>
		<p style="text-align: right">Written by <?php the_author_meta('first_name');?> <?php the_author_meta('last_name');?></p>
	</div>
<?php 
		endwhile;
	endif;
wp_reset_query();
?>