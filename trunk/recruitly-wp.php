<?php
/*
Plugin Name: Recruitly Wordpress Plugin
Plugin URI: https://recruitly.io
Description: Recruitly job board integration.
Version: 1.0.33
Author: Recruitly
Author URI: https://recruitly.io
License: GNU GENERAL PUBLIC LICENSE
*/
defined( 'RECRUITLY_POST_TYPE' ) or define( 'RECRUITLY_POST_TYPE', 'recruitlyjobs' );

register_activation_hook(__FILE__, 'activate_recruitly_wordpress_plugin');
register_deactivation_hook(__FILE__, 'deactivate_recruitly_wordpress_plugin');
register_uninstall_hook(__FILE__, 'uninstall_recruitly_wordpress_plugin');

define( 'RECRUITLY_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'RECRUITLY_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
define( 'RECRUITLY_CRON_ACTION', 'recruitly_cron' );

/**
 * Include dependencies
 */
include( plugin_dir_path( __FILE__ ) . 'recruitly-wp-templates.php');
include( plugin_dir_path( __FILE__ ) . 'admin/includes/commons.php');
include( plugin_dir_path( __FILE__ ) . 'admin/includes/menus.php');
include( plugin_dir_path( __FILE__ ) . 'admin/includes/customposttypes.php');
include( plugin_dir_path( __FILE__ ) . 'admin/includes/filters.php');
include( plugin_dir_path( __FILE__ ) . 'admin/includes/shortcodes.php');
include( plugin_dir_path( __FILE__ ) . 'admin/includes/taxonomies.php');
include( plugin_dir_path( __FILE__ ) . 'admin/settings.php');
include( plugin_dir_path( __FILE__ ) . 'admin/dataloader.php');

function activate_recruitly_wordpress_plugin()
{
	recruitly_wordpress_truncate_post_type();
}

function deactivate_recruitly_wordpress_plugin()
{
	recruitly_wordpress_truncate_post_type();
	if ( isset( $wp_post_types[ RECRUITLY_POST_TYPE ] ) ) {
		unset( $wp_post_types[ RECRUITLY_POST_TYPE  ] );
	}
	wp_clear_scheduled_hook( RECRUITLY_CRON_ACTION );
}

function uninstall_recruitly_wordpress_plugin()
{
	wp_clear_scheduled_hook( RECRUITLY_CRON_ACTION );
	delete_option('recruitly_apiserver');
	delete_option('recruitly_apikey');

	recruitly_wordpress_truncate_post_type();

	recruitly_wordpress_delete_taxonomies();

	if ( isset( $wp_post_types[ RECRUITLY_POST_TYPE ] ) ) {
		unset( $wp_post_types[ RECRUITLY_POST_TYPE  ] );
	}
}

add_action( RECRUITLY_CRON_ACTION, 'recruitly_wordpress_insert_post_type' );

add_filter( 'cron_schedules', function( $schedules ) {
	if( empty( $schedules[ 'recruitly' ] ) ) {
		$time = get_option( 'recruitly_refresh' );
		$time = !$time ? 60 : $time * 60;

		$schedules[ 'recruitly' ] = array(
			'interval' => $time,
			'display' => __( 'Recruitly time' )
        );
	}
	return $schedules;
});

add_action( 'wp', function() {
	$interval = get_option( 'recruitly_refresh' );
	$last_checked = get_option( 'recruitly_last_refreshed' );

	if( !$last_checked ) {
		recruitly_wordpress_insert_post_type();
		update_option( 'recruitly_last_refreshed', time() );
	}
	else {
		$interval = $interval ? $interval * 60 : 86400;
		if( ( time() - $last_checked ) >= $interval ) {
			recruitly_wordpress_insert_post_type();
			update_option( 'recruitly_last_refreshed', time() );
		}
	}
});

function recruitly_scripts_to_header() {
    wp_enqueue_script('jquery');
    wp_register_script( 'featherlight-js', 'https://cdnjs.cloudflare.com/ajax/libs/featherlight/1.7.13/featherlight.min.js', array('jquery'),'',true  );
    wp_register_style( 'featherlight-css','https://cdnjs.cloudflare.com/ajax/libs/featherlight/1.7.13/featherlight.min.css','','', 'screen' );
    wp_enqueue_script( 'featherlight-js' );
    wp_enqueue_style( 'featherlight-css' );
}

//Hooks our custom function into wp_enqueue_scripts function
add_action( 'wp_enqueue_scripts', 'recruitly_scripts_to_header' );