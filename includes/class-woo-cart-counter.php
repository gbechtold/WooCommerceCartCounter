<?php
/**
 * Main WooCommerce Cart Counter Class
 *
 * @package WooCartCounter
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class
 */
class Woo_Cart_Counter {

	/**
	 * Single instance of the class
	 *
	 * @var Woo_Cart_Counter
	 */
	protected static $instance = null;

	/**
	 * Plugin settings
	 *
	 * @var array
	 */
	private $settings = array();

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->load_settings();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Get single instance of the class
	 *
	 * @return Woo_Cart_Counter
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Load plugin settings
	 */
	private function load_settings() {
		$this->settings = get_option( 'woo_cart_counter_settings', array() );
		$this->settings = wp_parse_args(
			$this->settings,
			array(
				'default_icon'     => 'cart',
				'default_position' => 'top_right',
				'enable_ajax'      => true,
				'cache_enabled'    => true,
				'custom_css'       => '',
			)
		);
	}

	/**
	 * Include required files
	 */
	private function includes() {
		// Core classes
		require_once WOO_CART_COUNTER_PLUGIN_DIR . 'includes/class-shortcode.php';
		require_once WOO_CART_COUNTER_PLUGIN_DIR . 'includes/class-widget.php';
		require_once WOO_CART_COUNTER_PLUGIN_DIR . 'includes/class-ajax.php';

		// Admin classes
		if ( is_admin() ) {
			require_once WOO_CART_COUNTER_PLUGIN_DIR . 'includes/class-admin.php';
		}

		// Block support
		if ( function_exists( 'register_block_type' ) ) {
			require_once WOO_CART_COUNTER_PLUGIN_DIR . 'includes/class-block.php';
		}
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		// Frontend hooks
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		add_action( 'wp_head', array( $this, 'add_inline_styles' ) );

		// Widget registration
		add_action( 'widgets_init', array( $this, 'register_widget' ) );

		// AJAX hooks
		add_action( 'wp_ajax_woo_cart_counter_get_count', array( $this, 'ajax_get_cart_count' ) );
		add_action( 'wp_ajax_nopriv_woo_cart_counter_get_count', array( $this, 'ajax_get_cart_count' ) );

		// WooCommerce hooks
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'add_to_cart_fragments' ) );

		// Shortcode
		add_shortcode( 'woo_cart_counter', array( $this, 'render_shortcode' ) );

		// Block registration
		add_action( 'init', array( $this, 'register_block' ) );

		// Plugin upgrade
		add_action( 'init', array( $this, 'check_version' ) );
	}

	/**
	 * Enqueue frontend assets
	 */
	public function enqueue_frontend_assets() {
		// Check if we should load assets
		if ( ! $this->should_load_assets() ) {
			return;
		}

		// CSS
		wp_enqueue_style(
			'woo-cart-counter-frontend',
			WOO_CART_COUNTER_PLUGIN_URL . 'assets/css/frontend.min.css',
			array(),
			WOO_CART_COUNTER_VERSION
		);

		// JavaScript
		wp_enqueue_script(
			'woo-cart-counter-frontend',
			WOO_CART_COUNTER_PLUGIN_URL . 'assets/js/frontend.min.js',
			array(),
			WOO_CART_COUNTER_VERSION,
			true
		);

		// Localize script
		wp_localize_script(
			'woo-cart-counter-frontend',
			'wooCartCounter',
			array(
				'ajax_url'     => admin_url( 'admin-ajax.php' ),
				'nonce'        => wp_create_nonce( 'woo-cart-counter-nonce' ),
				'enable_ajax'  => $this->settings['enable_ajax'],
				'update_delay' => apply_filters( 'woo_cart_counter_update_delay', 1000 ),
				'i18n'         => array(
					'items' => __( 'items', 'woo-cart-counter' ),
					'item'  => __( 'item', 'woo-cart-counter' ),
				),
			)
		);
	}

	/**
	 * Add inline styles
	 */
	public function add_inline_styles() {
		if ( ! $this->should_load_assets() ) {
			return;
		}

		// Get all settings with defaults
		$compatibility_mode = get_option( 'woo_cart_counter_compatibility_mode', 'no' );
		$force_colors = get_option( 'woo_cart_counter_force_colors', 'no' );
		$offset_top = get_option( 'woo_cart_counter_offset_top', '-8' );
		$offset_right = get_option( 'woo_cart_counter_offset_right', '-8' );
		$margin_top = get_option( 'woo_cart_counter_margin_top', '0' );
		$z_index = get_option( 'woo_cart_counter_z_index', '999' );
		
		// Color settings
		$primary_color = get_option( 'woo_cart_counter_primary_color', '#333333' );
		$counter_bg = get_option( 'woo_cart_counter_bg_color', '#ff0000' );
		$counter_color = get_option( 'woo_cart_counter_text_color', '#ffffff' );
		$icon_size = get_option( 'woo_cart_counter_icon_size', '24' );
		$badge_size = get_option( 'woo_cart_counter_badge_size', '18' );

		// Build dynamic CSS
		$dynamic_css = ':root {
			--wcc-primary-color: ' . esc_attr( $primary_color ) . ';
			--wcc-counter-bg: ' . esc_attr( $counter_bg ) . ';
			--wcc-counter-color: ' . esc_attr( $counter_color ) . ';
			--wcc-icon-size: ' . esc_attr( $icon_size ) . 'px;
			--wcc-counter-size: ' . esc_attr( $badge_size ) . 'px;
			--wcc-counter-font-size: ' . ( intval( $badge_size ) - 6 ) . 'px;
			--wcc-counter-offset-top: ' . esc_attr( $offset_top ) . 'px;
			--wcc-counter-offset-right: ' . esc_attr( $offset_right ) . 'px;
			--wcc-z-index: ' . esc_attr( $z_index ) . ';
		}';

		// Add margin top if set
		if ( $margin_top > 0 ) {
			$dynamic_css .= '
			.woo-cart-counter {
				margin-top: ' . esc_attr( $margin_top ) . 'px !important;
			}';
		}

		// Compatibility mode styles
		if ( 'yes' === $compatibility_mode ) {
			$important = 'yes' === $force_colors ? ' !important' : '';
			
			$dynamic_css .= '
			.woo-cart-counter {
				position: relative !important;
			}
			.woo-cart-counter-icon-wrapper {
				position: relative !important;
				display: inline-block !important;
			}
			.woo-cart-counter-count {
				position: absolute !important;
				background-color: var(--wcc-counter-bg)' . $important . ';
				color: var(--wcc-counter-color)' . $important . ';
				top: var(--wcc-counter-offset-top) !important;
				right: var(--wcc-counter-offset-right) !important;
				border-radius: 50% !important;
				min-width: var(--wcc-counter-size) !important;
				height: var(--wcc-counter-size) !important;
				display: flex !important;
				align-items: center !important;
				justify-content: center !important;
				font-size: var(--wcc-counter-font-size) !important;
				font-weight: 700 !important;
				line-height: 1 !important;
				z-index: var(--wcc-z-index) !important;
			}
			.woo-cart-counter-link {
				display: inline-flex !important;
				align-items: center !important;
				position: relative !important;
			}';
		}

		// Custom CSS
		$custom_css = $this->settings['custom_css'];
		if ( ! empty( $custom_css ) ) {
			$dynamic_css .= "\n" . wp_strip_all_tags( $custom_css );
		}

		?>
		<style type="text/css" id="woo-cart-counter-custom-css">
			<?php echo wp_strip_all_tags( $dynamic_css ); ?>
		</style>
		<?php
	}

	/**
	 * Check if assets should be loaded
	 *
	 * @return bool
	 */
	private function should_load_assets() {
		// Always load on cart and checkout pages
		if ( is_cart() || is_checkout() ) {
			return true;
		}

		// Check if shortcode is present
		global $post;
		if ( $post && has_shortcode( $post->post_content, 'woo_cart_counter' ) ) {
			return true;
		}

		// Check if widget is active
		if ( is_active_widget( false, false, 'woo_cart_counter_widget', true ) ) {
			return true;
		}

		// Allow filtering
		return apply_filters( 'woo_cart_counter_load_assets', false );
	}

	/**
	 * Register widget
	 */
	public function register_widget() {
		register_widget( 'Woo_Cart_Counter_Widget' );
	}

	/**
	 * Render shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function render_shortcode( $atts ) {
		$shortcode = new Woo_Cart_Counter_Shortcode();
		return $shortcode->render( $atts );
	}

	/**
	 * Register Gutenberg block
	 */
	public function register_block() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		$block = new Woo_Cart_Counter_Block();
		$block->register();
	}

	/**
	 * AJAX handler to get cart count
	 */
	public function ajax_get_cart_count() {
		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'woo-cart-counter-nonce' ) ) {
			wp_die( -1 );
		}

		$response = array(
			'count' => $this->get_cart_count(),
			'total' => $this->get_cart_total(),
		);

		wp_send_json_success( $response );
	}

	/**
	 * Add cart fragments for AJAX updates
	 *
	 * @param array $fragments Cart fragments.
	 * @return array
	 */
	public function add_to_cart_fragments( $fragments ) {
		$fragments['.woo-cart-counter-count'] = '<span class="woo-cart-counter-count">' . esc_html( $this->get_cart_count() ) . '</span>';
		$fragments['.woo-cart-counter-total'] = '<span class="woo-cart-counter-total">' . wp_kses_post( $this->get_cart_total() ) . '</span>';
		
		return $fragments;
	}

	/**
	 * Get cart item count
	 *
	 * @return int
	 */
	public function get_cart_count() {
		if ( ! WC()->cart ) {
			return 0;
		}

		return apply_filters( 'woo_cart_counter_cart_count', WC()->cart->get_cart_contents_count() );
	}

	/**
	 * Get cart total
	 *
	 * @return string
	 */
	public function get_cart_total() {
		if ( ! WC()->cart ) {
			return wc_price( 0 );
		}

		return apply_filters( 'woo_cart_counter_cart_total', WC()->cart->get_cart_total() );
	}

	/**
	 * Get cart URL
	 *
	 * @return string
	 */
	public function get_cart_url() {
		return apply_filters( 'woo_cart_counter_cart_url', wc_get_cart_url() );
	}

	/**
	 * Check plugin version for upgrades
	 */
	public function check_version() {
		$current_version = get_option( 'woo_cart_counter_version' );
		
		if ( $current_version !== WOO_CART_COUNTER_VERSION ) {
			// Run upgrade routines if needed
			$this->upgrade( $current_version );
			
			// Update version
			update_option( 'woo_cart_counter_version', WOO_CART_COUNTER_VERSION );
		}
	}

	/**
	 * Run upgrade routines
	 *
	 * @param string $from_version Previous version.
	 */
	private function upgrade( $from_version ) {
		// Future upgrade routines can be added here
		do_action( 'woo_cart_counter_upgrade', $from_version, WOO_CART_COUNTER_VERSION );
	}

	/**
	 * Get plugin settings
	 *
	 * @param string $key Setting key.
	 * @param mixed  $default Default value.
	 * @return mixed
	 */
	public function get_setting( $key, $default = null ) {
		return isset( $this->settings[ $key ] ) ? $this->settings[ $key ] : $default;
	}

	/**
	 * Get available icons
	 *
	 * @return array
	 */
	public function get_available_icons() {
		return apply_filters(
			'woo_cart_counter_available_icons',
			array(
				'cart'   => __( 'Shopping Cart', 'woo-cart-counter' ),
				'basket' => __( 'Shopping Basket', 'woo-cart-counter' ),
				'bag'    => __( 'Shopping Bag', 'woo-cart-counter' ),
				'custom' => __( 'Custom Icon', 'woo-cart-counter' ),
			)
		);
	}
}