<?php
/**
 * Uninstall Script
 *
 * @package WooCartCounter
 * @since 1.0.0
 */

// If uninstall not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Security check
if ( ! current_user_can( 'activate_plugins' ) ) {
	exit;
}

// Check if it was intended as an array access
if ( __FILE__ !== WP_UNINSTALL_PLUGIN ) {
	exit;
}

/**
 * Clean up plugin data
 */
function woo_cart_counter_uninstall_cleanup() {
	global $wpdb;

	// Remove plugin options
	$options_to_delete = array(
		'woo_cart_counter_settings',
		'woo_cart_counter_version',
		'woo_cart_counter_default_icon',
		'woo_cart_counter_default_position',
		'woo_cart_counter_enable_ajax',
		'woo_cart_counter_cache_enabled',
		'woo_cart_counter_primary_color',
		'woo_cart_counter_bg_color',
		'woo_cart_counter_text_color',
		'woo_cart_counter_icon_size',
		'woo_cart_counter_badge_size',
		'woo_cart_counter_custom_css',
		'woo_cart_counter_update_delay',
		'woo_cart_counter_load_everywhere',
		'woo_cart_counter_debug_mode',
		'woo_cart_counter_compatibility_mode',
		'woo_cart_counter_offset_top',
		'woo_cart_counter_offset_right',
		'woo_cart_counter_margin_top',
		'woo_cart_counter_z_index',
		'woo_cart_counter_force_colors',
		'woo_cart_counter_container_selector',
		'woo_cart_counter_additional_classes',
	);

	foreach ( $options_to_delete as $option ) {
		delete_option( $option );
		
		// For multisite, also delete from each site
		if ( is_multisite() ) {
			$sites = get_sites( array( 'number' => false ) );
			foreach ( $sites as $site ) {
				switch_to_blog( $site->blog_id );
				delete_option( $option );
				restore_current_blog();
			}
		}
	}

	// Remove any transients
	$transients_to_delete = array(
		'woo_cart_counter_cache',
		'woo_cart_counter_data',
	);

	foreach ( $transients_to_delete as $transient ) {
		delete_transient( $transient );
		
		// For multisite
		if ( is_multisite() ) {
			$sites = get_sites( array( 'number' => false ) );
			foreach ( $sites as $site ) {
				switch_to_blog( $site->blog_id );
				delete_transient( $transient );
				restore_current_blog();
			}
		}
	}

	// Clear any scheduled events
	wp_clear_scheduled_hook( 'woo_cart_counter_cleanup' );
	wp_clear_scheduled_hook( 'woo_cart_counter_cache_cleanup' );

	// Remove custom database tables if any were created (none in this plugin)
	// This is left as a placeholder for future versions

	// Clean up user meta if any (none in this plugin)
	// This is left as a placeholder for future versions

	// Clean up post meta if any (none in this plugin)
	// This is left as a placeholder for future versions

	// Remove widget instances
	$widget_instances = get_option( 'widget_woo_cart_counter_widget' );
	if ( $widget_instances ) {
		delete_option( 'widget_woo_cart_counter_widget' );
	}

	// For multisite, clean up widget instances on all sites
	if ( is_multisite() ) {
		$sites = get_sites( array( 'number' => false ) );
		foreach ( $sites as $site ) {
			switch_to_blog( $site->blog_id );
			delete_option( 'widget_woo_cart_counter_widget' );
			restore_current_blog();
		}
	}

	// Clear object cache
	wp_cache_flush();
}

// Only run cleanup if this is a real uninstall
if ( defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	woo_cart_counter_uninstall_cleanup();
}