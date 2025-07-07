<?php
/**
 * Plugin Name: WooCommerce Cart Counter
 * Plugin URI: https://wordpress.org/plugins/woo-cart-counter/
 * Description: Lightweight, customizable cart counter for WooCommerce with shortcode, widget, and block support.
 * Version: 1.1.2
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: StarsMedia.com
 * Author URI: https://starsmedia.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: woo-cart-counter
 * Domain Path: /languages
 * WC requires at least: 5.0
 * WC tested up to: 9.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WOO_CART_COUNTER_VERSION' ) ) {
	define( 'WOO_CART_COUNTER_VERSION', '1.1.2' );
}

if ( ! defined( 'WOO_CART_COUNTER_PLUGIN_DIR' ) ) {
	define( 'WOO_CART_COUNTER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'WOO_CART_COUNTER_PLUGIN_URL' ) ) {
	define( 'WOO_CART_COUNTER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'WOO_CART_COUNTER_PLUGIN_BASENAME' ) ) {
	define( 'WOO_CART_COUNTER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}

/**
 * Check if WooCommerce is active
 */
function woo_cart_counter_check_woocommerce() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woo_cart_counter_woocommerce_missing_notice' );
		return false;
	}
	return true;
}

/**
 * Display admin notice if WooCommerce is not active
 */
function woo_cart_counter_woocommerce_missing_notice() {
	?>
	<div class="notice notice-error is-dismissible">
		<p><?php esc_html_e( 'WooCommerce Cart Counter requires WooCommerce to be installed and active.', 'woo-cart-counter' ); ?></p>
	</div>
	<?php
}

/**
 * Main plugin initialization
 */
function woo_cart_counter_init() {
	if ( ! woo_cart_counter_check_woocommerce() ) {
		return;
	}

	// Load text domain
	load_plugin_textdomain( 'woo-cart-counter', false, dirname( WOO_CART_COUNTER_PLUGIN_BASENAME ) . '/languages' );

	// Include required files
	require_once WOO_CART_COUNTER_PLUGIN_DIR . 'includes/class-woo-cart-counter.php';

	// Initialize the plugin
	Woo_Cart_Counter::get_instance();
}
add_action( 'plugins_loaded', 'woo_cart_counter_init', 15 );

/**
 * Activation hook
 */
function woo_cart_counter_activate() {
	// Set default options if not exists
	if ( ! get_option( 'woo_cart_counter_settings' ) ) {
		$default_settings = array(
			'default_icon'     => 'cart',
			'default_position' => 'top_right',
			'enable_ajax'      => true,
			'cache_enabled'    => true,
			'custom_css'       => '',
		);
		add_option( 'woo_cart_counter_settings', $default_settings );
	}

	// Clear any transients
	delete_transient( 'woo_cart_counter_cache' );

	// Flush rewrite rules
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'woo_cart_counter_activate' );

/**
 * Deactivation hook
 */
function woo_cart_counter_deactivate() {
	// Clear transients
	delete_transient( 'woo_cart_counter_cache' );

	// Clear scheduled events if any
	wp_clear_scheduled_hook( 'woo_cart_counter_cleanup' );
}
register_deactivation_hook( __FILE__, 'woo_cart_counter_deactivate' );


/**
 * Declare compatibility with WooCommerce features
 */
function woo_cart_counter_declare_compatibility() {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
	}
}
add_action( 'before_woocommerce_init', 'woo_cart_counter_declare_compatibility' );