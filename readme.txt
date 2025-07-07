=== WooCommerce Cart Counter ===
Contributors: starsmedia
Donate link: https://starsmedia.com/donate
Tags: woocommerce, cart, counter, shopping cart, cart icon
Requires at least: 5.8
Tested up to: 6.8
Stable tag: 1.1.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Lightweight, customizable cart counter for WooCommerce with shortcode, widget, and block support. Display real-time cart count with AJAX updates.

== Description ==

WooCommerce Cart Counter is a lightweight, performance-optimized plugin that adds a customizable cart counter to your WooCommerce store. Display the cart item count anywhere on your site using shortcodes, widgets, or Gutenberg blocks.

= Key Features =

* **Multiple Display Methods**: Shortcode, Widget, and Gutenberg Block
* **Real-time Updates**: AJAX-powered cart count updates without page reload
* **Highly Customizable**: Choose from different icons, positions, and styles
* **Performance Optimized**: Minimal impact on page load with lazy loading
* **Accessibility Ready**: WCAG 2.1 AA compliant with ARIA labels
* **Mobile Responsive**: Touch-friendly design that works on all devices
* **Translation Ready**: Full internationalization support
* **Developer Friendly**: Extensive hooks and filters for customization

= Display Options =

* **Icons**: Shopping cart, basket, bag, or custom icon
* **Styles**: Icon with count, icon with text, or text only
* **Positions**: Top right, top left, bottom right, bottom left, or inline
* **Visibility**: Option to hide when cart is empty
* **Extras**: Show cart total, add custom text before/after

= Usage =

**Shortcode Example:**
`[woo_cart_counter icon="cart" show_count="true" show_total="false" link_to_cart="true"]`

**PHP Function:**
`<?php echo do_shortcode('[woo_cart_counter]'); ?>`

= Shortcode Parameters =

* `icon` - Icon type: cart, basket, bag, or custom (default: cart)
* `icon_url` - URL for custom icon
* `show_count` - Show item count: true/false (default: true)
* `show_total` - Show cart total: true/false (default: false)
* `hide_empty` - Hide when empty: true/false (default: false)
* `custom_class` - Additional CSS classes
* `link_to_cart` - Link to cart page: true/false (default: true)
* `display_style` - Display style: icon_count, icon_text, text_only
* `count_position` - Count position: top_right, top_left, bottom_right, bottom_left, inline
* `text_before` - Text to display before counter
* `text_after` - Text to display after counter
* `currency_symbol` - Show currency symbol: true/false (default: true)

= Developer Features =

* Singleton pattern for optimal performance
* WordPress coding standards compliant
* Extensive action and filter hooks
* REST API support
* Compatible with page builders
* Custom events for JavaScript integration

= Performance =

* Conditional asset loading
* Minified CSS and JavaScript
* LocalStorage caching
* Debounced AJAX requests
* SVG icons (no icon fonts)

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/woo-cart-counter`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the shortcode `[woo_cart_counter]` in your content
4. Or add the widget through Appearance > Widgets
5. Or use the Gutenberg block in the block editor

= Minimum Requirements =

* WordPress 5.8 or greater
* PHP version 7.4 or greater
* WooCommerce 5.0 or greater

= Automatic Installation =

1. Log in to your WordPress dashboard
2. Navigate to Plugins > Add New
3. Search for "WooCommerce Cart Counter"
4. Click "Install Now" and then "Activate"

== Frequently Asked Questions ==

= Does this plugin work with my theme? =

Yes! The plugin is designed to work with any properly coded WordPress theme. It uses standard WooCommerce hooks and follows WordPress coding standards.

= Can I customize the appearance? =

Absolutely! You can customize colors, sizes, and styles using:
- CSS variables for easy theming
- Custom CSS in the plugin settings
- Your theme's custom CSS
- Filter hooks for advanced customization

= Will it slow down my site? =

No. The plugin is optimized for performance with:
- Conditional loading (only loads assets where needed)
- Minified CSS and JavaScript files
- Efficient AJAX updates
- LocalStorage caching

= Is it compatible with caching plugins? =

Yes, the plugin works well with caching plugins. The AJAX functionality ensures the cart count is always up-to-date regardless of page caching.

= Can I use multiple counters on the same page? =

Yes, you can use multiple instances of the shortcode, widget, or block on the same page with different settings.

= How do I hide the counter on specific pages? =

You can use CSS to hide the counter on specific pages, or use conditional logic in your theme. The plugin also provides filters for developers to control visibility programmatically.

= Does it support multi-currency plugins? =

Yes, the plugin respects WooCommerce currency settings and is compatible with popular multi-currency plugins.

= Is it GDPR compliant? =

Yes, the plugin does not collect or store any personal data. It only uses LocalStorage for performance optimization, which stores cart count data locally in the user's browser.

== Screenshots ==

1. Cart counter with default cart icon and top-right position
2. Different icon styles - cart, basket, and bag
3. Widget configuration in WordPress admin
4. Shortcode examples with various parameters
5. Gutenberg block in the editor
6. Mobile responsive design
7. Real-time AJAX updates in action
8. Settings page in WooCommerce admin

== Changelog ==

= 1.1.0 =
* Added theme compatibility mode for better positioning control
* New backend options for fine-tuning counter position (offset top/right)
* Added cart container margin top setting
* Z-index control for better layering
* Force colors option to override theme styles
* Advanced CSS selectors for targeting specific elements
* Additional CSS classes option
* Hide counter badge when cart has 0 items
* Improved shortcode configurator with live preview
* Better support for themes with conflicting styles

= 1.0.0 =
* Initial release
* Shortcode support with full parameter set
* WordPress widget
* Gutenberg block support
* AJAX real-time updates
* Multiple icon choices
* Customizable positions and styles
* Mobile responsive design
* Accessibility features
* Translation ready

== Upgrade Notice ==

= 1.1.0 =
New theme compatibility features and positioning controls. Update recommended if you experience display issues with your theme.

= 1.0.0 =
Initial release of WooCommerce Cart Counter.

== Additional Information ==

= Contributing =

Want to contribute? The plugin source code is available on [GitHub](https://github.com/starsmedia/woo-cart-counter).

= Support =

For support, please use the [WordPress.org support forum](https://wordpress.org/support/plugin/woo-cart-counter/).

= Credits =

* Developed by StarsMedia.com
* Icons by Feather Icons
* Inspired by the WooCommerce community

== Privacy Policy ==

This plugin does not collect, store, or transmit any personal data. It uses browser LocalStorage for performance optimization, storing only cart count information locally on the user's device.