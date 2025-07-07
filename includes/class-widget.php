<?php
/**
 * Widget Class
 *
 * @package WooCartCounter
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Cart Counter Widget
 */
class Woo_Cart_Counter_Widget extends WP_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'woo_cart_counter_widget',
			'description'                 => __( 'Display WooCommerce cart counter', 'woo-cart-counter' ),
			'customize_selective_refresh' => true,
		);

		parent::__construct(
			'woo_cart_counter_widget',
			__( 'WooCommerce Cart Counter', 'woo-cart-counter' ),
			$widget_ops
		);
	}

	/**
	 * Output the widget content
	 *
	 * @param array $args Display arguments.
	 * @param array $instance Widget settings.
	 */
	public function widget( $args, $instance ) {
		// Get widget settings
		$title           = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$icon            = ! empty( $instance['icon'] ) ? $instance['icon'] : 'cart';
		$icon_url        = ! empty( $instance['icon_url'] ) ? $instance['icon_url'] : '';
		$show_count      = isset( $instance['show_count'] ) ? $instance['show_count'] : true;
		$show_total      = isset( $instance['show_total'] ) ? $instance['show_total'] : false;
		$hide_empty      = isset( $instance['hide_empty'] ) ? $instance['hide_empty'] : false;
		$link_to_cart    = isset( $instance['link_to_cart'] ) ? $instance['link_to_cart'] : true;
		$display_style   = ! empty( $instance['display_style'] ) ? $instance['display_style'] : 'icon_count';
		$count_position  = ! empty( $instance['count_position'] ) ? $instance['count_position'] : 'top_right';
		$text_before     = ! empty( $instance['text_before'] ) ? $instance['text_before'] : '';
		$text_after      = ! empty( $instance['text_after'] ) ? $instance['text_after'] : '';
		$currency_symbol = isset( $instance['currency_symbol'] ) ? $instance['currency_symbol'] : true;
		$custom_class    = ! empty( $instance['custom_class'] ) ? $instance['custom_class'] : '';

		// Get cart count
		$cart_count = Woo_Cart_Counter::get_instance()->get_cart_count();

		// Hide if empty and hide_empty is true
		if ( $hide_empty && 0 === $cart_count ) {
			return;
		}

		// Output widget wrapper
		echo wp_kses_post( $args['before_widget'] );

		// Output title if set
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( apply_filters( 'widget_title', $title, $instance, $this->id_base ) ) . wp_kses_post( $args['after_title'] );
		}

		// Build shortcode attributes
		$shortcode_atts = array(
			'icon'            => $icon,
			'icon_url'        => $icon_url,
			'show_count'      => $show_count ? 'true' : 'false',
			'show_total'      => $show_total ? 'true' : 'false',
			'hide_empty'      => $hide_empty ? 'true' : 'false',
			'link_to_cart'    => $link_to_cart ? 'true' : 'false',
			'display_style'   => $display_style,
			'count_position'  => $count_position,
			'text_before'     => $text_before,
			'text_after'      => $text_after,
			'currency_symbol' => $currency_symbol ? 'true' : 'false',
			'custom_class'    => $custom_class . ' woo-cart-counter-widget-content',
		);

		// Convert attributes to string
		$atts_string = '';
		foreach ( $shortcode_atts as $key => $value ) {
			if ( ! empty( $value ) ) {
				$atts_string .= ' ' . $key . '="' . esc_attr( $value ) . '"';
			}
		}

		// Output shortcode
		echo do_shortcode( '[woo_cart_counter' . $atts_string . ']' );

		// Close widget wrapper
		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Output the widget settings form
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		// Get settings
		$title           = isset( $instance['title'] ) ? $instance['title'] : '';
		$icon            = isset( $instance['icon'] ) ? $instance['icon'] : 'cart';
		$icon_url        = isset( $instance['icon_url'] ) ? $instance['icon_url'] : '';
		$show_count      = isset( $instance['show_count'] ) ? (bool) $instance['show_count'] : true;
		$show_total      = isset( $instance['show_total'] ) ? (bool) $instance['show_total'] : false;
		$hide_empty      = isset( $instance['hide_empty'] ) ? (bool) $instance['hide_empty'] : false;
		$link_to_cart    = isset( $instance['link_to_cart'] ) ? (bool) $instance['link_to_cart'] : true;
		$display_style   = isset( $instance['display_style'] ) ? $instance['display_style'] : 'icon_count';
		$count_position  = isset( $instance['count_position'] ) ? $instance['count_position'] : 'top_right';
		$text_before     = isset( $instance['text_before'] ) ? $instance['text_before'] : '';
		$text_after      = isset( $instance['text_after'] ) ? $instance['text_after'] : '';
		$currency_symbol = isset( $instance['currency_symbol'] ) ? (bool) $instance['currency_symbol'] : true;
		$custom_class    = isset( $instance['custom_class'] ) ? $instance['custom_class'] : '';

		// Get available icons
		$available_icons = Woo_Cart_Counter::get_instance()->get_available_icons();
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'woo-cart-counter' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'icon' ) ); ?>"><?php esc_html_e( 'Icon:', 'woo-cart-counter' ); ?></label>
			<select class="widefat woo-cart-counter-icon-select" id="<?php echo esc_attr( $this->get_field_id( 'icon' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'icon' ) ); ?>">
				<?php foreach ( $available_icons as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $icon, $key ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p class="woo-cart-counter-custom-icon-field" <?php echo ( 'custom' !== $icon ) ? 'style="display:none;"' : ''; ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'icon_url' ) ); ?>"><?php esc_html_e( 'Custom Icon URL:', 'woo-cart-counter' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'icon_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'icon_url' ) ); ?>" type="url" value="<?php echo esc_url( $icon_url ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'display_style' ) ); ?>"><?php esc_html_e( 'Display Style:', 'woo-cart-counter' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'display_style' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display_style' ) ); ?>">
				<option value="icon_count" <?php selected( $display_style, 'icon_count' ); ?>><?php esc_html_e( 'Icon with Count', 'woo-cart-counter' ); ?></option>
				<option value="icon_text" <?php selected( $display_style, 'icon_text' ); ?>><?php esc_html_e( 'Icon with Text', 'woo-cart-counter' ); ?></option>
				<option value="text_only" <?php selected( $display_style, 'text_only' ); ?>><?php esc_html_e( 'Text Only', 'woo-cart-counter' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'count_position' ) ); ?>"><?php esc_html_e( 'Count Position:', 'woo-cart-counter' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'count_position' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count_position' ) ); ?>">
				<option value="top_right" <?php selected( $count_position, 'top_right' ); ?>><?php esc_html_e( 'Top Right', 'woo-cart-counter' ); ?></option>
				<option value="top_left" <?php selected( $count_position, 'top_left' ); ?>><?php esc_html_e( 'Top Left', 'woo-cart-counter' ); ?></option>
				<option value="bottom_right" <?php selected( $count_position, 'bottom_right' ); ?>><?php esc_html_e( 'Bottom Right', 'woo-cart-counter' ); ?></option>
				<option value="bottom_left" <?php selected( $count_position, 'bottom_left' ); ?>><?php esc_html_e( 'Bottom Left', 'woo-cart-counter' ); ?></option>
				<option value="inline" <?php selected( $count_position, 'inline' ); ?>><?php esc_html_e( 'Inline', 'woo-cart-counter' ); ?></option>
			</select>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_count ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_count' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_count' ) ); ?>"><?php esc_html_e( 'Show item count', 'woo-cart-counter' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_total ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_total' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_total' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_total' ) ); ?>"><?php esc_html_e( 'Show cart total', 'woo-cart-counter' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $hide_empty ); ?> id="<?php echo esc_attr( $this->get_field_id( 'hide_empty' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hide_empty' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'hide_empty' ) ); ?>"><?php esc_html_e( 'Hide when cart is empty', 'woo-cart-counter' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $link_to_cart ); ?> id="<?php echo esc_attr( $this->get_field_id( 'link_to_cart' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link_to_cart' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'link_to_cart' ) ); ?>"><?php esc_html_e( 'Link to cart page', 'woo-cart-counter' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $currency_symbol ); ?> id="<?php echo esc_attr( $this->get_field_id( 'currency_symbol' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'currency_symbol' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'currency_symbol' ) ); ?>"><?php esc_html_e( 'Show currency symbol', 'woo-cart-counter' ); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'text_before' ) ); ?>"><?php esc_html_e( 'Text Before:', 'woo-cart-counter' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text_before' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text_before' ) ); ?>" type="text" value="<?php echo esc_attr( $text_before ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'text_after' ) ); ?>"><?php esc_html_e( 'Text After:', 'woo-cart-counter' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text_after' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text_after' ) ); ?>" type="text" value="<?php echo esc_attr( $text_after ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'custom_class' ) ); ?>"><?php esc_html_e( 'Custom CSS Class:', 'woo-cart-counter' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'custom_class' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'custom_class' ) ); ?>" type="text" value="<?php echo esc_attr( $custom_class ); ?>">
		</p>

		<script type="text/javascript">
			jQuery(function($) {
				$('#<?php echo esc_js( $this->get_field_id( 'icon' ) ); ?>').on('change', function() {
					var $customField = $(this).closest('.widget-content').find('.woo-cart-counter-custom-icon-field');
					if ($(this).val() === 'custom') {
						$customField.show();
					} else {
						$customField.hide();
					}
				});
			});
		</script>
		<?php
	}

	/**
	 * Update widget settings
	 *
	 * @param array $new_instance New settings.
	 * @param array $old_instance Old settings.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title']           = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['icon']            = ! empty( $new_instance['icon'] ) ? sanitize_text_field( $new_instance['icon'] ) : 'cart';
		$instance['icon_url']        = ! empty( $new_instance['icon_url'] ) ? esc_url_raw( $new_instance['icon_url'] ) : '';
		$instance['show_count']      = ! empty( $new_instance['show_count'] );
		$instance['show_total']      = ! empty( $new_instance['show_total'] );
		$instance['hide_empty']      = ! empty( $new_instance['hide_empty'] );
		$instance['link_to_cart']    = ! empty( $new_instance['link_to_cart'] );
		$instance['display_style']   = ! empty( $new_instance['display_style'] ) ? sanitize_text_field( $new_instance['display_style'] ) : 'icon_count';
		$instance['count_position']  = ! empty( $new_instance['count_position'] ) ? sanitize_text_field( $new_instance['count_position'] ) : 'top_right';
		$instance['text_before']     = ! empty( $new_instance['text_before'] ) ? sanitize_text_field( $new_instance['text_before'] ) : '';
		$instance['text_after']      = ! empty( $new_instance['text_after'] ) ? sanitize_text_field( $new_instance['text_after'] ) : '';
		$instance['currency_symbol'] = ! empty( $new_instance['currency_symbol'] );
		$instance['custom_class']    = ! empty( $new_instance['custom_class'] ) ? sanitize_html_class( $new_instance['custom_class'] ) : '';

		return $instance;
	}
}