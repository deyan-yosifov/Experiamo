<?php
$blogusers = get_users(array());
// Array of WP_User objects.
foreach ( $blogusers as $user ) {
?>
	<div style="margin:20px auto; padding:20px; width:80%; border: 2px solid #BBBBBB;">
		<p><?php get_avatar($user->ID, 120)?></p>
		<h2 style="text-align: center"><?php get_user_meta($user->ID, 'first_name', true)?> <?php get_user_meta($user->ID, 'last_name', true)?></h2>
		<p style="text-align: right"><?php get_user_meta($user->ID, 'description', true)?></p>
	</div>
<?php 
}
?>
