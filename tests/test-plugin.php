<?php
/**
 * PHPUnit Tests for WooCommerce Cart Counter
 *
 * @package WooCartCounter
 * @since 1.0.0
 */

/**
 * Test case for the main plugin functionality
 */
class WooCartCounterTest extends WP_UnitTestCase {

	/**
	 * Plugin instance
	 *
	 * @var Woo_Cart_Counter
	 */
	private $plugin;

	/**
	 * Set up test environment
	 */
	public function setUp(): void {
		parent::setUp();
		
		// Ensure WooCommerce is loaded
		if ( ! class_exists( 'WooCommerce' ) ) {
			$this->markTestSkipped( 'WooCommerce is not installed.' );
		}

		// Initialize plugin
		$this->plugin = Woo_Cart_Counter::get_instance();
	}

	/**
	 * Test plugin initialization
	 */
	public function test_plugin_initialization() {
		$this->assertInstanceOf( 'Woo_Cart_Counter', $this->plugin );
		$this->assertTrue( class_exists( 'Woo_Cart_Counter' ) );
	}

	/**
	 * Test shortcode registration
	 */
	public function test_shortcode_registration() {
		$this->assertTrue( shortcode_exists( 'woo_cart_counter' ) );
	}

	/**
	 * Test shortcode output
	 */
	public function test_shortcode_output() {
		$output = do_shortcode( '[woo_cart_counter]' );
		$this->assertStringContainsString( 'woo-cart-counter', $output );
		$this->assertStringContainsString( 'woo-cart-counter-count', $output );
	}

	/**
	 * Test shortcode attributes
	 */
	public function test_shortcode_attributes() {
		// Test custom icon
		$output = do_shortcode( '[woo_cart_counter icon="basket"]' );
		$this->assertStringContainsString( 'woo-cart-counter-icon-basket', $output );

		// Test hide when empty
		$output = do_shortcode( '[woo_cart_counter hide_empty="true"]' );
		// Should be empty when cart is empty
		$this->assertEmpty( trim( $output ) );

		// Test custom class
		$output = do_shortcode( '[woo_cart_counter custom_class="my-custom-class"]' );
		$this->assertStringContainsString( 'my-custom-class', $output );
	}

	/**
	 * Test cart count retrieval
	 */
	public function test_cart_count() {
		// Test with empty cart
		$count = $this->plugin->get_cart_count();
		$this->assertEquals( 0, $count );

		// Test with items in cart (requires WooCommerce session)
		if ( function_exists( 'WC' ) && WC()->cart ) {
			// Clear cart first
			WC()->cart->empty_cart();
			$this->assertEquals( 0, $this->plugin->get_cart_count() );
		}
	}

	/**
	 * Test available icons
	 */
	public function test_available_icons() {
		$icons = $this->plugin->get_available_icons();
		$this->assertIsArray( $icons );
		$this->assertArrayHasKey( 'cart', $icons );
		$this->assertArrayHasKey( 'basket', $icons );
		$this->assertArrayHasKey( 'bag', $icons );
		$this->assertArrayHasKey( 'custom', $icons );
	}

	/**
	 * Test plugin settings
	 */
	public function test_plugin_settings() {
		$setting = $this->plugin->get_setting( 'default_icon', 'cart' );
		$this->assertIsString( $setting );

		$non_existent = $this->plugin->get_setting( 'non_existent_setting', 'default' );
		$this->assertEquals( 'default', $non_existent );
	}

	/**
	 * Test widget registration
	 */
	public function test_widget_registration() {
		global $wp_widget_factory;
		$this->assertArrayHasKey( 'Woo_Cart_Counter_Widget', $wp_widget_factory->widgets );
	}

	/**
	 * Test AJAX security
	 */
	public function test_ajax_security() {
		// Test without nonce
		$_POST['action'] = 'woo_cart_counter_update';
		
		try {
			$this->_handleAjax( 'woo_cart_counter_update' );
		} catch ( WPAjaxDieContinueException $e ) {
			$this->assertEquals( '-1', $e->getMessage() );
		}
	}

	/**
	 * Test CSS/JS asset loading conditions
	 */
	public function test_asset_loading_conditions() {
		// Test that assets are not loaded by default
		$should_load = apply_filters( 'woo_cart_counter_load_assets', false );
		$this->assertFalse( $should_load );
	}

	/**
	 * Test cart URL retrieval
	 */
	public function test_cart_url() {
		$cart_url = $this->plugin->get_cart_url();
		$this->assertIsString( $cart_url );
		$this->assertStringContainsString( 'cart', $cart_url );
	}

	/**
	 * Test plugin constants
	 */
	public function test_plugin_constants() {
		$this->assertTrue( defined( 'WOO_CART_COUNTER_VERSION' ) );
		$this->assertTrue( defined( 'WOO_CART_COUNTER_PLUGIN_DIR' ) );
		$this->assertTrue( defined( 'WOO_CART_COUNTER_PLUGIN_URL' ) );
		$this->assertTrue( defined( 'WOO_CART_COUNTER_PLUGIN_BASENAME' ) );
	}

	/**
	 * Test shortcode class instantiation
	 */
	public function test_shortcode_class() {
		$shortcode = new Woo_Cart_Counter_Shortcode();
		$this->assertInstanceOf( 'Woo_Cart_Counter_Shortcode', $shortcode );
	}

	/**
	 * Test widget class instantiation
	 */
	public function test_widget_class() {
		$widget = new Woo_Cart_Counter_Widget();
		$this->assertInstanceOf( 'Woo_Cart_Counter_Widget', $widget );
		$this->assertInstanceOf( 'WP_Widget', $widget );
	}

	/**
	 * Test AJAX class instantiation
	 */
	public function test_ajax_class() {
		$ajax = new Woo_Cart_Counter_Ajax();
		$this->assertInstanceOf( 'Woo_Cart_Counter_Ajax', $ajax );
	}

	/**
	 * Test block class instantiation (if Gutenberg is available)
	 */
	public function test_block_class() {
		if ( function_exists( 'register_block_type' ) ) {
			$block = new Woo_Cart_Counter_Block();
			$this->assertInstanceOf( 'Woo_Cart_Counter_Block', $block );
		} else {
			$this->markTestSkipped( 'Gutenberg blocks not available.' );
		}
	}

	/**
	 * Test admin class instantiation (in admin context)
	 */
	public function test_admin_class() {
		if ( is_admin() ) {
			$admin = new Woo_Cart_Counter_Admin();
			$this->assertInstanceOf( 'Woo_Cart_Counter_Admin', $admin );
		} else {
			$this->markTestSkipped( 'Not in admin context.' );
		}
	}

	/**
	 * Test plugin singleton pattern
	 */
	public function test_singleton_pattern() {
		$instance1 = Woo_Cart_Counter::get_instance();
		$instance2 = Woo_Cart_Counter::get_instance();
		$this->assertSame( $instance1, $instance2 );
	}

	/**
	 * Test filter hooks
	 */
	public function test_filter_hooks() {
		// Test cart count filter
		add_filter( 'woo_cart_counter_cart_count', function( $count ) {
			return $count + 5;
		});

		$filtered_count = apply_filters( 'woo_cart_counter_cart_count', 0 );
		$this->assertEquals( 5, $filtered_count );

		// Test available icons filter
		add_filter( 'woo_cart_counter_available_icons', function( $icons ) {
			$icons['test'] = 'Test Icon';
			return $icons;
		});

		$icons = $this->plugin->get_available_icons();
		$this->assertArrayHasKey( 'test', $icons );
	}

	/**
	 * Test cart total formatting
	 */
	public function test_cart_total() {
		$total = $this->plugin->get_cart_total();
		$this->assertIsString( $total );
		// Should contain currency formatting
		$this->assertStringContainsString( get_woocommerce_currency_symbol(), $total );
	}

	/**
	 * Clean up after tests
	 */
	public function tearDown(): void {
		// Clear any cart contents
		if ( function_exists( 'WC' ) && WC()->cart ) {
			WC()->cart->empty_cart();
		}

		parent::tearDown();
	}
}

/**
 * Test case for shortcode functionality
 */
class WooCartCounterShortcodeTest extends WP_UnitTestCase {

	/**
	 * Test shortcode render method
	 */
	public function test_shortcode_render() {
		$shortcode = new Woo_Cart_Counter_Shortcode();
		$output = $shortcode->render( array() );
		
		$this->assertIsString( $output );
		$this->assertStringContainsString( 'woo-cart-counter', $output );
	}

	/**
	 * Test shortcode with different display styles
	 */
	public function test_display_styles() {
		$shortcode = new Woo_Cart_Counter_Shortcode();

		// Test icon_count style
		$output = $shortcode->render( array( 'display_style' => 'icon_count' ) );
		$this->assertStringContainsString( 'woo-cart-counter-style-icon_count', $output );

		// Test text_only style
		$output = $shortcode->render( array( 'display_style' => 'text_only' ) );
		$this->assertStringContainsString( 'woo-cart-counter-style-text_only', $output );
	}

	/**
	 * Test shortcode with different positions
	 */
	public function test_count_positions() {
		$shortcode = new Woo_Cart_Counter_Shortcode();

		$positions = array( 'top_right', 'top_left', 'bottom_right', 'bottom_left', 'inline' );

		foreach ( $positions as $position ) {
			$output = $shortcode->render( array( 'count_position' => $position ) );
			$this->assertStringContainsString( 'woo-cart-counter-position-' . $position, $output );
		}
	}
}

/**
 * Test case for widget functionality
 */
class WooCartCounterWidgetTest extends WP_UnitTestCase {

	/**
	 * Test widget form method
	 */
	public function test_widget_form() {
		$widget = new Woo_Cart_Counter_Widget();
		
		ob_start();
		$widget->form( array() );
		$form_output = ob_get_clean();
		
		$this->assertStringContainsString( 'woo_cart_counter_widget', $form_output );
		$this->assertStringContainsString( 'Title:', $form_output );
	}

	/**
	 * Test widget update method
	 */
	public function test_widget_update() {
		$widget = new Woo_Cart_Counter_Widget();
		
		$new_instance = array(
			'title' => 'My Cart',
			'icon' => 'basket',
			'show_count' => '1',
		);
		
		$updated = $widget->update( $new_instance, array() );
		
		$this->assertEquals( 'My Cart', $updated['title'] );
		$this->assertEquals( 'basket', $updated['icon'] );
		$this->assertTrue( $updated['show_count'] );
	}
}