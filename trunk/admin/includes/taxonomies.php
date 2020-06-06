<?php

/**
 * Register taxonomy
 * This taxonomy holds list of all job sectors
 */
function recruitly_wordpress_setup_taxonomies() {

	try {

        $customPostType = get_option( 'recruitly_customposttype','recruitlyjobs' );

		$labels = array(
			'name'          => 'Job Sectors',
			'singular_name' => 'Job Sector',
			'search_items'  => 'Search Job Sectors',
			'all_items'     => 'All Job Sectors',
			'edit_item'     => 'Edit Job Sector',
			'update_item'   => 'Update Job Sector',
			'add_new_item'  => 'Add New Job Sector',
			'new_item_name' => 'New Job Sector',
			'menu_name'     => 'Job Sectors'
		);
		register_taxonomy( 'jobsector', $customPostType, array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'query_var'         => true,
			'show_admin_column' => false
		) );

		$labels = array(
			'name'          => 'Countys',
			'singular_name' => 'County ',
			'search_items'  => 'Search Countys ',
			'all_items'     => 'All Countys',
			'edit_items'    => 'Edit County',
			'update_item'   => 'Update County',
			'add_new_item'  => 'Add New County',
			'new_item_name' => 'New County',
			'menu name'     => 'Countys'
		);
		register_taxonomy( 'jobcounty', $customPostType, array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'query_var'         => true,
			'show_admin_column' => false
		) );

		$labels = array(
			'name'          => 'Cities',
			'singular_name' => 'City ',
			'search_items'  => 'Search Cities ',
			'all_items'     => 'All Cities',
			'edit_items'    => 'Edit City',
			'update_item'   => 'Update City',
			'add_new_item'  => 'Add New City',
			'new_item_name' => 'New City',
			'menu name'     => 'Cities'
		);
		register_taxonomy( 'jobcity', $customPostType, array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'query_var'         => true,
			'show_admin_column' => false
		) );

		$labels = array(
			'name'          => 'Job Types',
			'singular_name' => 'Job Type ',
			'search_items'  => 'Search Types ',
			'all_items'     => 'All Job Types',
			'edit_items'    => 'Edit Job Type',
			'update_item'   => 'Update Job Type',
			'add_new_item'  => 'Add New Job Type',
			'new_item_name' => 'New Job Type',
			'menu name'     => 'Job Types'
		);
		register_taxonomy( 'jobtype', $customPostType, array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'query_var'         => true,
			'show_admin_column' => false
		) );

        recruitly_admin_notice("Taxonomies for post type $customPostType registered successfully",'success');

	} catch ( Exception $ex ) {
		recruitly_admin_notice( $ex->getMessage(), 'error' );
	}
}

/**
 * De-register taxonomies.
 */
function recruitly_wordpress_delete_taxonomies() {

	try {

        $customPostType = get_option( 'recruitly_customposttype','recruitlyjobs' );

		unregister_taxonomy_for_object_type( 'jobcounty', $customPostType );
		unregister_taxonomy_for_object_type( 'jobsector', $customPostType );
		unregister_taxonomy_for_object_type( 'jobtype', $customPostType );
		unregister_taxonomy_for_object_type( 'jobcity', $customPostType );

	} catch ( Exception $ex ) {
		recruitly_admin_notice( $ex->getMessage(), 'error' );
	}

}