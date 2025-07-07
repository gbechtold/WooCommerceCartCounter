<?php
/**
 * AJAX Handler Class
 *
 * @package WooCartCounter
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * AJAX functionality handler
 */
class Woo_Cart_Counter_Ajax {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize AJAX hooks
	 */
	private function init_hooks() {
		// Cart update actions
		add_action( 'wp_ajax_woo_cart_counter_update', array( $this, 'handle_cart_update' ) );
		add_action( 'wp_ajax_nopriv_woo_cart_counter_update', array( $this, 'handle_cart_update' ) );

		// WooCommerce AJAX events
		add_action( 'woocommerce_add_to_cart', array( $this, 'trigger_cart_update' ) );
		add_action( 'woocommerce_cart_item_removed', array( $this, 'trigger_cart_update' ) );
		add_action( 'woocommerce_cart_item_restored', array( $this, 'trigger_cart_update' ) );
		add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'trigger_cart_update' ) );
	}

	/**
	 * Handle cart update AJAX request
	 */
	public function handle_cart_update() {
		// Verify nonce
		if ( ! check_ajax_referer( 'woo-cart-counter-nonce', 'nonce', false ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Security check failed', 'woo-cart-counter' ),
				)
			);
		}

		// Get cart data
		$cart_data = $this->get_cart_data();

		// Send response
		wp_send_json_success( $cart_data );
	}

	/**
	 * Get cart data
	 *
	 * @return array
	 */
	private function get_cart_data() {
		// Ensure WooCommerce cart is loaded
		if ( ! WC()->cart ) {
			return array(
				'count'       => 0,
				'total'       => wc_price( 0 ),
				'total_raw'   => 0,
				'items'       => array(),
				'currency'    => get_woocommerce_currency_symbol(),
				'cart_url'    => wc_get_cart_url(),
				'is_empty'    => true,
			);
		}

		// Get cart items
		$items = array();
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$product = $cart_item['data'];
			if ( $product ) {
				$items[] = array(
					'id'       => $cart_item['product_id'],
					'name'     => $product->get_name(),
					'quantity' => $cart_item['quantity'],
					'price'    => wc_price( $product->get_price() ),
				);
			}
		}

		// Prepare cart data
		$cart_count = WC()->cart->get_cart_contents_count();
		$cart_total = WC()->cart->get_cart_total();
		$cart_total_raw = WC()->cart->get_cart_contents_total();

		return apply_filters(
			'woo_cart_counter_ajax_cart_data',
			array(
				'count'       => $cart_count,
				'total'       => $cart_total,
				'total_raw'   => $cart_total_raw,
				'items'       => $items,
				'currency'    => get_woocommerce_currency_symbol(),
				'cart_url'    => wc_get_cart_url(),
				'is_empty'    => 0 === $cart_count,
				'fragments'   => $this->get_cart_fragments(),
			)
		);
	}

	/**
	 * Get cart fragments for updating
	 *
	 * @return array
	 */
	private function get_cart_fragments() {
		$fragments = array();

		// Count fragment
		$fragments['.woo-cart-counter-count'] = '<span class="woo-cart-counter-count" data-count="' . esc_attr( WC()->cart->get_cart_contents_count() ) . '">' . esc_html( WC()->cart->get_cart_contents_count() ) . '</span>';

		// Total fragment
		$fragments['.woo-cart-counter-total'] = '<span class="woo-cart-counter-total">' . wp_kses_post( WC()->cart->get_cart_total() ) . '</span>';

		// Allow custom fragments
		return apply_filters( 'woo_cart_counter_ajax_fragments', $fragments );
	}

	/**
	 * Trigger cart update via AJAX
	 */
	public function trigger_cart_update() {
		// This will be handled by JavaScript
		// The JavaScript will listen for WooCommerce events and update accordingly
	}

	/**
	 * Add custom cart data to WooCommerce fragments
	 *
	 * @param array $fragments Cart fragments.
	 * @return array
	 */
	public function add_custom_fragments( $fragments ) {
		$fragments = array_merge( $fragments, $this->get_cart_fragments() );
		return $fragments;
	}
}

// Initialize AJAX handler
new Woo_Cart_Counter_Ajax();