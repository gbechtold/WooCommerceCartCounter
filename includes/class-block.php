<?php
/**
 * Gutenberg Block Class
 *
 * @package WooCartCounter
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gutenberg block handler
 */
class Woo_Cart_Counter_Block {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'register' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
	}

	/**
	 * Register the block
	 */
	public function register() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		register_block_type(
			'woo-cart-counter/cart-counter',
			array(
				'attributes'      => $this->get_block_attributes(),
				'render_callback' => array( $this, 'render_block' ),
				'editor_script'   => 'woo-cart-counter-block-editor',
				'style'           => 'woo-cart-counter-frontend',
			)
		);
	}

	/**
	 * Get block attributes
	 *
	 * @return array
	 */
	private function get_block_attributes() {
		return array(
			'icon'             => array(
				'type'    => 'string',
				'default' => 'cart',
			),
			'iconUrl'          => array(
				'type'    => 'string',
				'default' => '',
			),
			'showCount'        => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'showTotal'        => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'hideEmpty'        => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'customClass'      => array(
				'type'    => 'string',
				'default' => '',
			),
			'linkToCart'       => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'displayStyle'     => array(
				'type'    => 'string',
				'default' => 'icon_count',
			),
			'countPosition'    => array(
				'type'    => 'string',
				'default' => 'top_right',
			),
			'textBefore'       => array(
				'type'    => 'string',
				'default' => '',
			),
			'textAfter'        => array(
				'type'    => 'string',
				'default' => '',
			),
			'currencySymbol'   => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'align'            => array(
				'type' => 'string',
			),
			'className'        => array(
				'type' => 'string',
			),
		);
	}

	/**
	 * Render the block
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public function render_block( $attributes ) {
		// Convert camelCase to snake_case for shortcode
		$shortcode_atts = array(
			'icon'            => $attributes['icon'],
			'icon_url'        => $attributes['iconUrl'],
			'show_count'      => $attributes['showCount'] ? 'true' : 'false',
			'show_total'      => $attributes['showTotal'] ? 'true' : 'false',
			'hide_empty'      => $attributes['hideEmpty'] ? 'true' : 'false',
			'link_to_cart'    => $attributes['linkToCart'] ? 'true' : 'false',
			'display_style'   => $attributes['displayStyle'],
			'count_position'  => $attributes['countPosition'],
			'text_before'     => $attributes['textBefore'],
			'text_after'      => $attributes['textAfter'],
			'currency_symbol' => $attributes['currencySymbol'] ? 'true' : 'false',
		);

		// Add custom class
		$classes = array( 'wp-block-woo-cart-counter-cart-counter' );
		
		if ( ! empty( $attributes['customClass'] ) ) {
			$classes[] = $attributes['customClass'];
		}
		
		if ( ! empty( $attributes['className'] ) ) {
			$classes[] = $attributes['className'];
		}
		
		if ( ! empty( $attributes['align'] ) ) {
			$classes[] = 'align' . $attributes['align'];
		}

		$shortcode_atts['custom_class'] = implode( ' ', $classes );

		// Build shortcode string
		$shortcode = '[woo_cart_counter';
		foreach ( $shortcode_atts as $key => $value ) {
			if ( ! empty( $value ) ) {
				$shortcode .= ' ' . $key . '="' . esc_attr( $value ) . '"';
			}
		}
		$shortcode .= ']';

		return do_shortcode( $shortcode );
	}

	/**
	 * Enqueue block editor assets
	 */
	public function enqueue_block_editor_assets() {
		// Register block editor script
		wp_enqueue_script(
			'woo-cart-counter-block-editor',
			WOO_CART_COUNTER_PLUGIN_URL . 'assets/js/block.js',
			array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-i18n' ),
			WOO_CART_COUNTER_VERSION,
			true
		);

		// Localize script
		wp_localize_script(
			'woo-cart-counter-block-editor',
			'wooCartCounterBlock',
			array(
				'availableIcons' => Woo_Cart_Counter::get_instance()->get_available_icons(),
				'cartCount'      => Woo_Cart_Counter::get_instance()->get_cart_count(),
				'cartTotal'      => Woo_Cart_Counter::get_instance()->get_cart_total(),
			)
		);

		// Set translations
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'woo-cart-counter-block-editor', 'woo-cart-counter' );
		}
	}
}