<?php
/**
 * Shortcode Class
 *
 * @package WooCartCounter
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shortcode handler class
 */
class Woo_Cart_Counter_Shortcode {

	/**
	 * Render the shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function render( $atts ) {
		// Parse attributes
		$atts = shortcode_atts(
			array(
				'icon'             => 'cart',
				'icon_url'         => '',
				'show_count'       => 'true',
				'show_total'       => 'false',
				'hide_empty'       => 'false',
				'custom_class'     => '',
				'link_to_cart'     => 'true',
				'display_style'    => 'icon_count',
				'count_position'   => 'top_right',
				'text_before'      => '',
				'text_after'       => '',
				'currency_symbol'  => 'true',
			),
			$atts,
			'woo_cart_counter'
		);

		// Convert string booleans to actual booleans
		$show_count      = filter_var( $atts['show_count'], FILTER_VALIDATE_BOOLEAN );
		$show_total      = filter_var( $atts['show_total'], FILTER_VALIDATE_BOOLEAN );
		$hide_empty      = filter_var( $atts['hide_empty'], FILTER_VALIDATE_BOOLEAN );
		$link_to_cart    = filter_var( $atts['link_to_cart'], FILTER_VALIDATE_BOOLEAN );
		$currency_symbol = filter_var( $atts['currency_symbol'], FILTER_VALIDATE_BOOLEAN );

		// Get cart data
		$cart_count = Woo_Cart_Counter::get_instance()->get_cart_count();
		$cart_total = Woo_Cart_Counter::get_instance()->get_cart_total();
		$cart_url   = Woo_Cart_Counter::get_instance()->get_cart_url();

		// Hide if empty and hide_empty is true
		if ( $hide_empty && 0 === $cart_count ) {
			return '';
		}

		// Build classes
		$classes = array(
			'woo-cart-counter',
			'woo-cart-counter-style-' . esc_attr( $atts['display_style'] ),
			'woo-cart-counter-position-' . esc_attr( $atts['count_position'] ),
			'woo-cart-counter-icon-' . esc_attr( $atts['icon'] ),
		);

		if ( ! empty( $atts['custom_class'] ) ) {
			$classes[] = esc_attr( $atts['custom_class'] );
		}

		// Add additional classes from settings
		$additional_classes = get_option( 'woo_cart_counter_additional_classes', '' );
		if ( ! empty( $additional_classes ) ) {
			$extra_classes = explode( ' ', $additional_classes );
			foreach ( $extra_classes as $class ) {
				if ( ! empty( $class ) ) {
					$classes[] = esc_attr( $class );
				}
			}
		}

		if ( 0 === $cart_count ) {
			$classes[] = 'woo-cart-counter-empty';
		}

		// Start building output
		ob_start();
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php if ( $link_to_cart ) : ?>
				<a href="<?php echo esc_url( $cart_url ); ?>" class="woo-cart-counter-link" aria-label="<?php esc_attr_e( 'View cart', 'woo-cart-counter' ); ?>">
			<?php endif; ?>

				<div class="woo-cart-counter-inner">
					<?php if ( ! empty( $atts['text_before'] ) ) : ?>
						<span class="woo-cart-counter-text-before"><?php echo esc_html( $atts['text_before'] ); ?></span>
					<?php endif; ?>

					<?php if ( 'text_only' !== $atts['display_style'] ) : ?>
						<span class="woo-cart-counter-icon-wrapper">
							<?php echo $this->get_icon_html_safe( $atts['icon'], $atts['icon_url'] ); ?>
							<?php if ( $show_count && 'inline' !== $atts['count_position'] && $cart_count > 0 ) : ?>
								<span class="woo-cart-counter-count" data-count="<?php echo esc_attr( $cart_count ); ?>"><?php echo esc_html( $cart_count ); ?></span>
							<?php endif; ?>
						</span>
					<?php endif; ?>

					<?php if ( $show_count && ( 'inline' === $atts['count_position'] || 'text_only' === $atts['display_style'] ) ) : ?>
						<span class="woo-cart-counter-count-inline">
							<span class="woo-cart-counter-count" data-count="<?php echo esc_attr( $cart_count ); ?>"><?php echo esc_html( $cart_count ); ?></span>
							<span class="woo-cart-counter-label"><?php echo esc_html( _n( 'item', 'items', $cart_count, 'woo-cart-counter' ) ); ?></span>
						</span>
					<?php endif; ?>

					<?php if ( $show_total ) : ?>
						<span class="woo-cart-counter-total-wrapper">
							<?php if ( ! $currency_symbol ) : ?>
								<span class="woo-cart-counter-total"><?php echo wp_kses_post( wp_strip_all_tags( $cart_total ) ); ?></span>
							<?php else : ?>
								<span class="woo-cart-counter-total"><?php echo wp_kses_post( $cart_total ); ?></span>
							<?php endif; ?>
						</span>
					<?php endif; ?>

					<?php if ( ! empty( $atts['text_after'] ) ) : ?>
						<span class="woo-cart-counter-text-after"><?php echo esc_html( $atts['text_after'] ); ?></span>
					<?php endif; ?>
				</div>

			<?php if ( $link_to_cart ) : ?>
				</a>
			<?php endif; ?>
		</div>
		<?php

		$output = ob_get_clean();

		// Allow filtering of final output
		return apply_filters( 'woo_cart_counter_shortcode_output', $output, $atts );
	}

	/**
	 * Get icon HTML with proper escaping
	 *
	 * @param string $icon Icon type.
	 * @param string $icon_url Custom icon URL.
	 * @return string
	 */
	private function get_icon_html_safe( $icon, $icon_url = '' ) {
		$html = $this->get_icon_html( $icon, $icon_url );
		
		// Allow SVG tags and attributes
		$allowed_svg = array(
			'svg' => array(
				'class' => true,
				'xmlns' => true,
				'viewBox' => true,
				'fill' => true,
				'stroke' => true,
				'stroke-width' => true,
				'stroke-linecap' => true,
				'stroke-linejoin' => true,
			),
			'circle' => array(
				'cx' => true,
				'cy' => true,
				'r' => true,
			),
			'path' => array(
				'd' => true,
			),
			'line' => array(
				'x1' => true,
				'y1' => true,
				'x2' => true,
				'y2' => true,
			),
			'img' => array(
				'src' => true,
				'alt' => true,
				'class' => true,
			),
		);
		
		return wp_kses( $html, $allowed_svg );
	}

	/**
	 * Get icon HTML
	 *
	 * @param string $icon Icon type.
	 * @param string $icon_url Custom icon URL.
	 * @return string
	 */
	private function get_icon_html( $icon, $icon_url = '' ) {
		if ( 'custom' === $icon && ! empty( $icon_url ) ) {
			return '<img src="' . esc_url( $icon_url ) . '" alt="' . esc_attr__( 'Cart', 'woo-cart-counter' ) . '" class="woo-cart-counter-icon woo-cart-counter-icon-custom">';
		}

		// SVG icons
		$svg_icons = array(
			'cart'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="woo-cart-counter-icon woo-cart-counter-icon-cart"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>',
			'basket' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="woo-cart-counter-icon woo-cart-counter-icon-basket"><path d="M5.52 7h13"></path><path d="M9 11v6"></path><path d="M12 11v6"></path><path d="M15 11v6"></path><path d="M8 7V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v3"></path><path d="M5.25 7.01l.66 8.6A2 2 0 0 0 7.9 17.6h8.2a2 2 0 0 0 2-1.99l.66-8.6"></path></svg>',
			'bag'    => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="woo-cart-counter-icon woo-cart-counter-icon-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>',
		);

		// Allow filtering of SVG icons
		$svg_icons = apply_filters( 'woo_cart_counter_svg_icons', $svg_icons );

		return isset( $svg_icons[ $icon ] ) ? $svg_icons[ $icon ] : $svg_icons['cart'];
	}
}