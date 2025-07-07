<?php
/**
 * Admin Settings Class
 *
 * @package WooCartCounter
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Admin settings handler
 */
class Woo_Cart_Counter_Admin {

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
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 50 );
		add_action( 'woocommerce_settings_tabs_woo_cart_counter', array( $this, 'settings_tab' ) );
		add_action( 'woocommerce_update_options_woo_cart_counter', array( $this, 'update_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		
		// Add admin menu for shortcode configurator
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		
		// Add settings link to plugin page
		add_filter( 'plugin_action_links_' . WOO_CART_COUNTER_PLUGIN_BASENAME, array( $this, 'add_plugin_links' ) );
	}

	/**
	 * Add settings tab to WooCommerce
	 *
	 * @param array $settings_tabs Array of WooCommerce setting tabs.
	 * @return array
	 */
	public function add_settings_tab( $settings_tabs ) {
		$settings_tabs['woo_cart_counter'] = __( 'Cart Counter', 'woo-cart-counter' );
		return $settings_tabs;
	}

	/**
	 * Settings tab content
	 */
	public function settings_tab() {
		woocommerce_admin_fields( $this->get_settings() );
	}

	/**
	 * Update settings
	 */
	public function update_settings() {
		woocommerce_update_options( $this->get_settings() );
	}

	/**
	 * Get plugin settings
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = array(
			array(
				'name' => __( 'Cart Counter Settings', 'woo-cart-counter' ),
				'type' => 'title',
				'desc' => __( 'Configure default settings for the cart counter display.', 'woo-cart-counter' ),
				'id'   => 'woo_cart_counter_settings',
			),

			array(
				'name'     => __( 'Default Icon', 'woo-cart-counter' ),
				'type'     => 'select',
				'desc'     => __( 'Choose the default icon to display.', 'woo-cart-counter' ),
				'id'       => 'woo_cart_counter_default_icon',
				'default'  => 'cart',
				'options'  => array(
					'cart'   => __( 'Shopping Cart', 'woo-cart-counter' ),
					'basket' => __( 'Shopping Basket', 'woo-cart-counter' ),
					'bag'    => __( 'Shopping Bag', 'woo-cart-counter' ),
				),
				'desc_tip' => true,
			),

			array(
				'name'     => __( 'Default Position', 'woo-cart-counter' ),
				'type'     => 'select',
				'desc'     => __( 'Choose the default position for the count badge.', 'woo-cart-counter' ),
				'id'       => 'woo_cart_counter_default_position',
				'default'  => 'top_right',
				'options'  => array(
					'top_right'    => __( 'Top Right', 'woo-cart-counter' ),
					'top_left'     => __( 'Top Left', 'woo-cart-counter' ),
					'bottom_right' => __( 'Bottom Right', 'woo-cart-counter' ),
					'bottom_left'  => __( 'Bottom Left', 'woo-cart-counter' ),
					'inline'       => __( 'Inline', 'woo-cart-counter' ),
				),
				'desc_tip' => true,
			),

			array(
				'name'     => __( 'Enable AJAX Updates', 'woo-cart-counter' ),
				'type'     => 'checkbox',
				'desc'     => __( 'Enable real-time cart count updates via AJAX.', 'woo-cart-counter' ),
				'id'       => 'woo_cart_counter_enable_ajax',
				'default'  => 'yes',
				'desc_tip' => __( 'When enabled, the cart counter will update automatically when items are added or removed.', 'woo-cart-counter' ),
			),

			array(
				'name'     => __( 'Enable Caching', 'woo-cart-counter' ),
				'type'     => 'checkbox',
				'desc'     => __( 'Enable browser caching for better performance.', 'woo-cart-counter' ),
				'id'       => 'woo_cart_counter_cache_enabled',
				'default'  => 'yes',
				'desc_tip' => __( 'Uses browser localStorage to cache cart data for improved performance.', 'woo-cart-counter' ),
			),

			array(
				'name'        => __( 'Primary Color', 'woo-cart-counter' ),
				'type'        => 'color',
				'desc'        => __( 'Choose the primary color for the cart counter.', 'woo-cart-counter' ),
				'id'          => 'woo_cart_counter_primary_color',
				'default'     => '#333333',
				'css'         => 'width:6em;',
				'desc_tip'    => true,
			),

			array(
				'name'        => __( 'Counter Background Color', 'woo-cart-counter' ),
				'type'        => 'color',
				'desc'        => __( 'Choose the background color for the count badge.', 'woo-cart-counter' ),
				'id'          => 'woo_cart_counter_bg_color',
				'default'     => '#ff0000',
				'css'         => 'width:6em;',
				'desc_tip'    => true,
			),

			array(
				'name'        => __( 'Counter Text Color', 'woo-cart-counter' ),
				'type'        => 'color',
				'desc'        => __( 'Choose the text color for the count badge.', 'woo-cart-counter' ),
				'id'          => 'woo_cart_counter_text_color',
				'default'     => '#ffffff',
				'css'         => 'width:6em;',
				'desc_tip'    => true,
			),

			array(
				'name'        => __( 'Icon Size (px)', 'woo-cart-counter' ),
				'type'        => 'number',
				'desc'        => __( 'Set the icon size in pixels.', 'woo-cart-counter' ),
				'id'          => 'woo_cart_counter_icon_size',
				'default'     => '24',
				'custom_attributes' => array(
					'min'  => '16',
					'max'  => '64',
					'step' => '1',
				),
				'desc_tip'    => true,
			),

			array(
				'name'        => __( 'Counter Size (px)', 'woo-cart-counter' ),
				'type'        => 'number',
				'desc'        => __( 'Set the counter badge size in pixels.', 'woo-cart-counter' ),
				'id'          => 'woo_cart_counter_badge_size',
				'default'     => '18',
				'custom_attributes' => array(
					'min'  => '12',
					'max'  => '32',
					'step' => '1',
				),
				'desc_tip'    => true,
			),

			array(
				'type' => 'sectionend',
				'id'   => 'woo_cart_counter_settings',
			),

			// Position & Compatibility Section
			array(
				'name' => __( 'Position & Compatibility', 'woo-cart-counter' ),
				'type' => 'title',
				'desc' => __( 'Fine-tune positioning and theme compatibility.', 'woo-cart-counter' ),
				'id'   => 'woo_cart_counter_position_settings',
			),

			array(
				'name'     => __( 'Enable Compatibility Mode', 'woo-cart-counter' ),
				'type'     => 'checkbox',
				'desc'     => __( 'Force absolute positioning and override theme styles.', 'woo-cart-counter' ),
				'id'       => 'woo_cart_counter_compatibility_mode',
				'default'  => 'no',
				'desc_tip' => __( 'Enable this if the counter position is not displaying correctly with your theme.', 'woo-cart-counter' ),
			),

			array(
				'name'        => __( 'Counter Offset Top (px)', 'woo-cart-counter' ),
				'type'        => 'number',
				'desc'        => __( 'Adjust vertical position of the counter badge.', 'woo-cart-counter' ),
				'id'          => 'woo_cart_counter_offset_top',
				'default'     => '-8',
				'custom_attributes' => array(
					'min'  => '-20',
					'max'  => '20',
					'step' => '1',
				),
				'desc_tip'    => __( 'Negative values move the counter up, positive values move it down.', 'woo-cart-counter' ),
			),

			array(
				'name'        => __( 'Counter Offset Right (px)', 'woo-cart-counter' ),
				'type'        => 'number',
				'desc'        => __( 'Adjust horizontal position of the counter badge.', 'woo-cart-counter' ),
				'id'          => 'woo_cart_counter_offset_right',
				'default'     => '-8',
				'custom_attributes' => array(
					'min'  => '-20',
					'max'  => '20',
					'step' => '1',
				),
				'desc_tip'    => __( 'Negative values move the counter left, positive values move it right.', 'woo-cart-counter' ),
			),

			array(
				'name'        => __( 'Cart Container Margin Top (px)', 'woo-cart-counter' ),
				'type'        => 'number',
				'desc'        => __( 'Add space above the entire cart icon.', 'woo-cart-counter' ),
				'id'          => 'woo_cart_counter_margin_top',
				'default'     => '0',
				'custom_attributes' => array(
					'min'  => '0',
					'max'  => '50',
					'step' => '1',
				),
				'desc_tip'    => __( 'Pushes the entire cart element down by specified pixels.', 'woo-cart-counter' ),
			),

			array(
				'name'        => __( 'Z-Index', 'woo-cart-counter' ),
				'type'        => 'number',
				'desc'        => __( 'Control stacking order of the counter.', 'woo-cart-counter' ),
				'id'          => 'woo_cart_counter_z_index',
				'default'     => '999',
				'custom_attributes' => array(
					'min'  => '1',
					'max'  => '9999',
					'step' => '1',
				),
				'desc_tip'    => __( 'Higher values ensure the counter appears above other elements.', 'woo-cart-counter' ),
			),

			array(
				'name'     => __( 'Force Colors', 'woo-cart-counter' ),
				'type'     => 'checkbox',
				'desc'     => __( 'Override theme colors with !important.', 'woo-cart-counter' ),
				'id'       => 'woo_cart_counter_force_colors',
				'default'  => 'no',
				'desc_tip' => __( 'Enable if theme colors are overriding your chosen colors.', 'woo-cart-counter' ),
			),

			array(
				'type' => 'sectionend',
				'id'   => 'woo_cart_counter_position_settings',
			),

			// CSS Selectors Section
			array(
				'name' => __( 'Advanced CSS Selectors', 'woo-cart-counter' ),
				'type' => 'title',
				'desc' => __( 'Target specific elements with custom CSS selectors (for developers).', 'woo-cart-counter' ),
				'id'   => 'woo_cart_counter_css_selectors',
			),

			array(
				'name'        => __( 'Container Selector', 'woo-cart-counter' ),
				'type'        => 'text',
				'desc'        => __( 'CSS selector for the cart container wrapper.', 'woo-cart-counter' ),
				'id'          => 'woo_cart_counter_container_selector',
				'default'     => '',
				'placeholder' => '.site-header .cart-widget',
				'css'         => 'width: 100%;',
				'desc_tip'    => __( 'Leave empty to use default. Example: .header-cart, #site-navigation .cart', 'woo-cart-counter' ),
			),

			array(
				'name'        => __( 'Additional Target Classes', 'woo-cart-counter' ),
				'type'        => 'text',
				'desc'        => __( 'Add extra CSS classes to the cart counter.', 'woo-cart-counter' ),
				'id'          => 'woo_cart_counter_additional_classes',
				'default'     => '',
				'placeholder' => 'my-custom-class theme-specific-class',
				'css'         => 'width: 100%;',
				'desc_tip'    => __( 'Space-separated list of CSS classes to add to the counter element.', 'woo-cart-counter' ),
			),

			array(
				'name'        => __( 'Custom CSS', 'woo-cart-counter' ),
				'type'        => 'textarea',
				'desc'        => __( 'Add custom CSS to style the cart counter.', 'woo-cart-counter' ),
				'id'          => 'woo_cart_counter_custom_css',
				'css'         => 'width:100%; height: 150px;',
				'desc_tip'    => __( 'Use CSS variables like --wcc-primary-color, --wcc-counter-bg, etc. for consistent theming.', 'woo-cart-counter' ),
			),

			array(
				'type' => 'sectionend',
				'id'   => 'woo_cart_counter_css_selectors',
			),

			// Performance Section
			array(
				'name' => __( 'Performance Settings', 'woo-cart-counter' ),
				'type' => 'title',
				'desc' => __( 'Configure performance and optimization settings.', 'woo-cart-counter' ),
				'id'   => 'woo_cart_counter_performance',
			),

			array(
				'name'        => __( 'Update Delay (ms)', 'woo-cart-counter' ),
				'type'        => 'number',
				'desc'        => __( 'Delay in milliseconds before updating cart count via AJAX.', 'woo-cart-counter' ),
				'id'          => 'woo_cart_counter_update_delay',
				'default'     => '1000',
				'custom_attributes' => array(
					'min'  => '100',
					'max'  => '5000',
					'step' => '100',
				),
				'desc_tip'    => __( 'Higher values reduce server load but may delay updates. Lower values provide faster updates but increase server requests.', 'woo-cart-counter' ),
			),

			array(
				'name'     => __( 'Load Assets Everywhere', 'woo-cart-counter' ),
				'type'     => 'checkbox',
				'desc'     => __( 'Load cart counter assets on all pages (not recommended).', 'woo-cart-counter' ),
				'id'       => 'woo_cart_counter_load_everywhere',
				'default'  => 'no',
				'desc_tip' => __( 'By default, assets are only loaded on pages that use the cart counter. Enable this only if you have issues with dynamic content.', 'woo-cart-counter' ),
			),

			array(
				'name'     => __( 'Debug Mode', 'woo-cart-counter' ),
				'type'     => 'checkbox',
				'desc'     => __( 'Enable debug mode for troubleshooting.', 'woo-cart-counter' ),
				'id'       => 'woo_cart_counter_debug_mode',
				'default'  => 'no',
				'desc_tip' => __( 'Enables console logging and loads unminified assets. Disable on production sites.', 'woo-cart-counter' ),
			),

			array(
				'type' => 'sectionend',
				'id'   => 'woo_cart_counter_performance',
			),
		);

		return apply_filters( 'woo_cart_counter_settings', $settings );
	}

	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_submenu_page(
			'woocommerce',
			__( 'Cart Counter Shortcode', 'woo-cart-counter' ),
			__( 'Cart Counter', 'woo-cart-counter' ),
			'manage_woocommerce',
			'woo-cart-counter-shortcode',
			array( $this, 'render_shortcode_page' )
		);
	}

	/**
	 * Add plugin action links
	 *
	 * @param array $links Existing links.
	 * @return array
	 */
	public function add_plugin_links( $links ) {
		$plugin_links = array(
			'<a href="' . admin_url( 'admin.php?page=woo-cart-counter-shortcode' ) . '">' . __( 'Shortcode Generator', 'woo-cart-counter' ) . '</a>',
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=woo_cart_counter' ) . '">' . __( 'Settings', 'woo-cart-counter' ) . '</a>',
		);
		
		return array_merge( $plugin_links, $links );
	}

	/**
	 * Render shortcode configurator page
	 */
	public function render_shortcode_page() {
		?>
		<div class="wrap woo-cart-counter-admin">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			
			<div class="wcc-shortcode-generator">
				<div class="wcc-generator-container">
					<div class="wcc-options-panel">
						<h2><?php esc_html_e( 'Shortcode Configuration', 'woo-cart-counter' ); ?></h2>
						
						<form id="wcc-shortcode-form">
							<table class="form-table">
								<tr>
									<th scope="row">
										<label for="wcc-icon"><?php esc_html_e( 'Icon Style', 'woo-cart-counter' ); ?></label>
									</th>
									<td>
										<select id="wcc-icon" name="icon" class="wcc-option">
											<option value="cart"><?php esc_html_e( 'Shopping Cart', 'woo-cart-counter' ); ?></option>
											<option value="basket"><?php esc_html_e( 'Shopping Basket', 'woo-cart-counter' ); ?></option>
											<option value="bag"><?php esc_html_e( 'Shopping Bag', 'woo-cart-counter' ); ?></option>
											<option value="custom"><?php esc_html_e( 'Custom Icon', 'woo-cart-counter' ); ?></option>
										</select>
									</td>
								</tr>
								
								<tr class="wcc-custom-icon-row" style="display: none;">
									<th scope="row">
										<label for="wcc-icon-url"><?php esc_html_e( 'Custom Icon URL', 'woo-cart-counter' ); ?></label>
									</th>
									<td>
										<input type="url" id="wcc-icon-url" name="icon_url" class="wcc-option regular-text" placeholder="https://example.com/icon.png">
									</td>
								</tr>
								
								<tr>
									<th scope="row">
										<label for="wcc-show-count"><?php esc_html_e( 'Show Count', 'woo-cart-counter' ); ?></label>
									</th>
									<td>
										<input type="checkbox" id="wcc-show-count" name="show_count" class="wcc-option" value="true" checked>
										<label for="wcc-show-count"><?php esc_html_e( 'Display item count', 'woo-cart-counter' ); ?></label>
									</td>
								</tr>
								
								<tr>
									<th scope="row">
										<label for="wcc-show-total"><?php esc_html_e( 'Show Total', 'woo-cart-counter' ); ?></label>
									</th>
									<td>
										<input type="checkbox" id="wcc-show-total" name="show_total" class="wcc-option" value="true">
										<label for="wcc-show-total"><?php esc_html_e( 'Display cart total amount', 'woo-cart-counter' ); ?></label>
									</td>
								</tr>
								
								<tr>
									<th scope="row">
										<label for="wcc-hide-empty"><?php esc_html_e( 'Hide When Empty', 'woo-cart-counter' ); ?></label>
									</th>
									<td>
										<input type="checkbox" id="wcc-hide-empty" name="hide_empty" class="wcc-option" value="true">
										<label for="wcc-hide-empty"><?php esc_html_e( 'Hide counter when cart is empty', 'woo-cart-counter' ); ?></label>
									</td>
								</tr>
								
								<tr>
									<th scope="row">
										<label for="wcc-link-to-cart"><?php esc_html_e( 'Link to Cart', 'woo-cart-counter' ); ?></label>
									</th>
									<td>
										<input type="checkbox" id="wcc-link-to-cart" name="link_to_cart" class="wcc-option" value="true" checked>
										<label for="wcc-link-to-cart"><?php esc_html_e( 'Make counter clickable to cart page', 'woo-cart-counter' ); ?></label>
									</td>
								</tr>
								
								<tr>
									<th scope="row">
										<label for="wcc-display-style"><?php esc_html_e( 'Display Style', 'woo-cart-counter' ); ?></label>
									</th>
									<td>
										<select id="wcc-display-style" name="display_style" class="wcc-option">
											<option value="icon_count"><?php esc_html_e( 'Icon with Count Badge', 'woo-cart-counter' ); ?></option>
											<option value="icon_text"><?php esc_html_e( 'Icon with Text', 'woo-cart-counter' ); ?></option>
											<option value="text_only"><?php esc_html_e( 'Text Only', 'woo-cart-counter' ); ?></option>
										</select>
									</td>
								</tr>
								
								<tr>
									<th scope="row">
										<label for="wcc-count-position"><?php esc_html_e( 'Count Position', 'woo-cart-counter' ); ?></label>
									</th>
									<td>
										<select id="wcc-count-position" name="count_position" class="wcc-option">
											<option value="top_right"><?php esc_html_e( 'Top Right', 'woo-cart-counter' ); ?></option>
											<option value="top_left"><?php esc_html_e( 'Top Left', 'woo-cart-counter' ); ?></option>
											<option value="bottom_right"><?php esc_html_e( 'Bottom Right', 'woo-cart-counter' ); ?></option>
											<option value="bottom_left"><?php esc_html_e( 'Bottom Left', 'woo-cart-counter' ); ?></option>
											<option value="inline"><?php esc_html_e( 'Inline', 'woo-cart-counter' ); ?></option>
										</select>
									</td>
								</tr>
								
								<tr>
									<th scope="row">
										<label for="wcc-text-before"><?php esc_html_e( 'Text Before', 'woo-cart-counter' ); ?></label>
									</th>
									<td>
										<input type="text" id="wcc-text-before" name="text_before" class="wcc-option regular-text" placeholder="<?php esc_attr_e( 'Cart:', 'woo-cart-counter' ); ?>">
									</td>
								</tr>
								
								<tr>
									<th scope="row">
										<label for="wcc-text-after"><?php esc_html_e( 'Text After', 'woo-cart-counter' ); ?></label>
									</th>
									<td>
										<input type="text" id="wcc-text-after" name="text_after" class="wcc-option regular-text" placeholder="<?php esc_attr_e( 'items', 'woo-cart-counter' ); ?>">
									</td>
								</tr>
								
								<tr>
									<th scope="row">
										<label for="wcc-custom-class"><?php esc_html_e( 'Custom CSS Class', 'woo-cart-counter' ); ?></label>
									</th>
									<td>
										<input type="text" id="wcc-custom-class" name="custom_class" class="wcc-option regular-text" placeholder="my-custom-class">
									</td>
								</tr>
							</table>
						</form>
					</div>
					
					<div class="wcc-preview-panel">
						<h2><?php esc_html_e( 'Preview', 'woo-cart-counter' ); ?></h2>
						<div class="wcc-preview-area">
							<div id="wcc-preview-container">
								<!-- Preview will be rendered here via JavaScript -->
							</div>
						</div>
						
						<h3><?php esc_html_e( 'Generated Shortcode', 'woo-cart-counter' ); ?></h3>
						<div class="wcc-shortcode-output">
							<input type="text" id="wcc-generated-shortcode" readonly value="[woo_cart_counter]" class="widefat">
							<button type="button" class="button button-secondary wcc-copy-shortcode">
								<?php esc_html_e( 'Copy Shortcode', 'woo-cart-counter' ); ?>
							</button>
						</div>
						
						<div class="wcc-usage-info">
							<h4><?php esc_html_e( 'Usage', 'woo-cart-counter' ); ?></h4>
							<p><?php esc_html_e( 'Copy the shortcode above and paste it into any post, page, or widget area.', 'woo-cart-counter' ); ?></p>
							<p><?php esc_html_e( 'You can also use it in your theme files:', 'woo-cart-counter' ); ?></p>
							<code>&lt;?php echo do_shortcode('[woo_cart_counter]'); ?&gt;</code>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Enqueue admin assets
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin_assets( $hook ) {
		// Load on WooCommerce settings pages or shortcode generator page
		$is_settings_page = ( 'woocommerce_page_wc-settings' === $hook && isset( $_GET['tab'] ) && 'woo_cart_counter' === sanitize_text_field( wp_unslash( $_GET['tab'] ) ) );
		$is_shortcode_page = ( 'woocommerce_page_woo-cart-counter-shortcode' === $hook );
		
		if ( ! $is_settings_page && ! $is_shortcode_page ) {
			return;
		}

		// Enqueue admin CSS
		wp_enqueue_style(
			'woo-cart-counter-admin',
			WOO_CART_COUNTER_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			WOO_CART_COUNTER_VERSION
		);
		
		// Also enqueue frontend CSS for preview on shortcode page
		if ( $is_shortcode_page ) {
			wp_enqueue_style(
				'woo-cart-counter-frontend',
				WOO_CART_COUNTER_PLUGIN_URL . 'assets/css/frontend.css',
				array(),
				WOO_CART_COUNTER_VERSION
			);
		}

		// Enqueue admin JS
		wp_enqueue_script(
			'woo-cart-counter-admin',
			WOO_CART_COUNTER_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			WOO_CART_COUNTER_VERSION,
			true
		);

		// Localize script
		wp_localize_script(
			'woo-cart-counter-admin',
			'wooCartCounterAdmin',
			array(
				'nonce'      => wp_create_nonce( 'woo-cart-counter-admin-nonce' ),
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'cart_count' => Woo_Cart_Counter::get_instance()->get_cart_count(),
				'cart_total' => Woo_Cart_Counter::get_instance()->get_cart_total(),
			)
		);
	}
}

// Initialize admin class
new Woo_Cart_Counter_Admin();