<?php
/**
 * Plugin Name: DPY Travel Routes
 * Description: Plugin allowing to manage travel roots with google maps integration. Each route can have several destinations and author.
 * Author: Deyan Yosifov
 * Author URI: http://deyan-yosifov.com
 * Version: 1.0
 * License: GPLv2
 *
 */

/**
 * Copyright (C) 2014 Deyan Yosifov

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * */

/**
 * The main class for the travel routes plugin initializations.
 *
 * @author deyan-yosifov
 *
 */
class DPY {
	const ROUTE_POST_NAME = "route";
	const DESTINATION_POST_NAME = "destination";
	const THUMBNAIL_COLUMN_NAME = "featured";

	static function init(){
		// TODO: Define static variables here!
	}
} 
DPY::init();

class DPY_Travel_Routes_Plugin_Initializator {	
	
	public function __construct() {
		// create custom posts
		add_action( 'init', array( $this, 'register_dpy_routes_post' ) );	
		add_action( 'init', array( $this, 'register_dpy_destination_post' ) );			
		add_action( 'init', array($this, 'register_dpy_taxonomies'));
		
		// manage custom posts columns
		add_filter( 'manage_taxonomies_for_'.DPY::ROUTE_POST_NAME.'_columns', Array($this, "route_taxonomy_columns_initialize"));
		add_filter('manage_edit-'.DPY::ROUTE_POST_NAME.'_columns', Array($this, "route_columns_initialize"));
		add_action('manage_'.DPY::ROUTE_POST_NAME.'_posts_custom_column', Array($this, "route_columns_content"), 10, 2);
		add_filter( 'manage_taxonomies_for_'.DPY::DESTINATION_POST_NAME.'_columns', Array($this, "destination_taxonomy_columns_initialize"));
		add_filter('manage_edit-'.DPY::DESTINATION_POST_NAME.'_columns', Array($this, "destination_columns_initialize"));
		add_action('manage_'.DPY::DESTINATION_POST_NAME.'_posts_custom_column', Array($this, "destination_columns_content"), 10, 2);
		
		// manage custom posts custom meta boxes
		add_action('admin_enqueue_scripts', Array($this, 'dpy_admin_post_meta_enqueue'));
		add_action('add_meta_boxes', Array($this, 'dpy_post_meta_box_init'));
		add_action('save_post', Array($this, 'dpy_post_meta_box_save'));
	}
	
	/**
	 * Adds taxonomies columns in routes listing.
	 */
	public function route_taxonomy_columns_initialize( $taxonomies ) {
		$taxonomies[] = 'route_theme';
		$taxonomies[] = 'route_type';
		return $taxonomies;
	}
	
	/**
	 * Adds taxonomies columns in destinations listing.
	 */
	public function destination_taxonomy_columns_initialize( $taxonomies ) {
		$taxonomies[] = 'route_theme';
		return $taxonomies;
	}
	
	/**
	 * Setup DPY custom columns in routes listing.
	 */
	public function route_columns_initialize($columns) {
		$insertion_array = array(DPY::THUMBNAIL_COLUMN_NAME => "Thumbnail");		
		return $this->insert_array_at($columns, $insertion_array, 1);
	}
	
	/**
	 * Setup DPY routes columns content definition.
	 */
	public function route_columns_content($columns_name, $post_id) {
		switch($columns_name){
			case DPY::THUMBNAIL_COLUMN_NAME:
				$src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id));
				printf("<img src='%s' width='80' height='80'>", $src[0]);
			break;	
		};
	}
	
	/**
	 * Setup DPY custom columns in destinations listing.
	 */
	public function destination_columns_initialize($columns) {
		return $this->route_columns_initialize($columns);
	}
	
	/**
	 * Setup DPY destination columns content definition.
	 */
	public function destination_columns_content($columns_name, $post_id) {
		$this->route_columns_content($columns_name, $post_id);
	}
	
	private function insert_array_at($old_array, $insertion_array, $position){
		$result_array = array_slice($old_array, 0, $position, true) +
		$insertion_array +
		array_slice($old_array, $position, count($old_array)-$position, true);
		
		return $result_array;
	}
	
	/**
	 * Setup the DPY routes post type.
	 */
	public function register_dpy_routes_post() {
		register_post_type( DPY::ROUTE_POST_NAME, array(
			'labels' => array(
				'name' => __( 'Routes', 'dpyTravelRoutes' ),
				'singular_name' => __( 'Route', 'dpyTravelRoutes' ),
				'add_new' => _x( 'Add New', 'pluginbase', 'dpyTravelRoutes' ),
				'add_new_item' => __( 'Add New Route', 'dpyTravelRoutes' ),
				'edit_item' => __( 'Edit Route', 'dpyTravelRoutes' ),
				'new_item' => __( 'New Route', 'dpyTravelRoutes' ),
				'view_item' => __( 'View Route', 'dpyTravelRoutes' ),
				'search_items' => __( 'Search Route', 'dpyTravelRoutes' ),
				'not_found' =>  __( 'No routes found', 'dpyTravelRoutes' ),
				'not_found_in_trash' => __( 'No routes found in Trash', 'dpyTravelRoutes' ),
				),
			'description' => __( 'Routes on google maps.', 'dpyTravelRoutes' ),
			'public' => true,
			'publicly_queryable' => true,
			'query_var' => true,
			'rewrite' => true,
			'exclude_from_search' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_position' => 40, // probably have to change, many plugins use this
			'supports' => array(
				'title',
				'thumbnail',
				'author',
				'editor',
				'page-attributes',
				//'custom-fields',
				),
		));		
	}
		
	/**
	 * Setup the DPY destination post type.
	 */
	public function register_dpy_destination_post() {
		register_post_type( DPY::DESTINATION_POST_NAME, array(
			'labels' => array(
				'name' => __( 'Destinations', 'dpyTravelRoutes' ),
				'singular_name' => __( 'Destination', 'dpyTravelRoutes' ),
				'add_new' => _x( 'Add New', 'pluginbase', 'dpyTravelRoutes' ),
				'add_new_item' => __( 'Add New Destination', 'dpyTravelRoutes' ),
				'edit_item' => __( 'Edit Destination', 'dpyTravelRoutes' ),
				'new_item' => __( 'New Destination', 'dpyTravelRoutes' ),
				'view_item' => __( 'View Destination', 'dpyTravelRoutes' ),
				'search_items' => __( 'Search Destination', 'dpyTravelRoutes' ),
				'not_found' =>  __( 'No destinations found', 'dpyTravelRoutes' ),
				'not_found_in_trash' => __( 'No destinations found in Trash', 'dpyTravelRoutes' ),
				),
			'description' => __( 'Travel destination on google maps.', 'dpyTravelRoutes' ),
			'public' => true,
			'publicly_queryable' => true,
			'query_var' => true,
			'rewrite' => true,
			'exclude_from_search' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_position' => 40, // probably have to change, many plugins use this
			'supports' => array(
				'title',
				'thumbnail',
				'author',
				'editor',
				'page-attributes',
				//'custom-fields',
				),
		));
	}

	/**
	 * Registers routes and destinations taxonomies.
	 */
	public function register_dpy_taxonomies(){
		// Route > Theme
		register_taxonomy('route_theme', Array(DPY::ROUTE_POST_NAME, DPY::DESTINATION_POST_NAME), Array(
		'label' => __( 'Theme', "dpyTravelRoutes" ),
		'rewrite' => array( 'slug' => 'route_theme' ),
		'hierarchical' => true,
		));
	
		// Route > Type
		register_taxonomy('route_type', DPY::ROUTE_POST_NAME, Array(
		'label' => __( 'Type', "dpyTravelRoutes" ),
		'rewrite' => array( 'slug' => 'route_type' ),
		'hierarchical' => true,
		));
	}

	/**
	 * Enqueues needed scripts for the DPY admin pages.
	 */
	public  function dpy_admin_post_meta_enqueue(){
		// TODO: add needed scripts for the admin page here!
	}
	
	/**
	 * Defines additional field for each specific DPY post type.
	 */
	public  function dpy_post_meta_box_init(){
		add_meta_box( "dpy_route_map", "Route on google maps", Array($this, "dpy_route_map_meta_box_include"), DPY::ROUTE_POST_NAME);
		add_meta_box( "dpy_destination_map", "Destination on google maps", Array($this, "dpy_destination_map_meta_box_include"), DPY::DESTINATION_POST_NAME);
		
	}
	
	public function dpy_route_map_meta_box_include($post){
		include_once 'dpy-route-map-meta-box.php';
	}
	
	public function dpy_destination_map_meta_box_include($post){
		include_once 'dpy-destination-map-meta-box.php';
	}
	
	/**
	 * Saves the data from custom fields for each specific DPY post type.
	 */
	public function dpy_post_meta_box_save($post_id){
		if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE){
			return $post_id;
		};
		
		switch( get_post_type($post_id) ){
			case DPY::ROUTE_POST_NAME:
				$defaultJson = "{"
						. "'latitude':42.673885293117664,"
						. "'longitude':23.348543643951416,"
						. "'zoom':16,"
						. "'destinationIDs':[],"
						. "'shapes':[],"
						. "}";
				$this->dpy_update_post_meta($post_id, "mapjson", $defaultJson);
				break;
			case DPY::DESTINATION_POST_NAME:
				$this->dpy_update_post_meta($post_id, "latitude", "42.673885293117664");
				$this->dpy_update_post_meta($post_id, "longitude", "23.348543643951416");
				$this->dpy_update_post_meta($post_id, "zoom", "16");
				$this->dpy_update_post_meta($post_id, "icon_size", 15);
				$this->dpy_update_post_meta($post_id, "icon", "");
				break;
		}
	}
	
	private function dpy_update_post_meta($post_id, $meta_key, $default = ""){
		update_post_meta($post_id, $meta_key, !empty($_POST[$meta_key]) ? $_POST[$meta_key] : $default);
	}
		
}

//init
new DPY_Travel_Routes_Plugin_Initializator();