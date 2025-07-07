# Cart Counter for WooCommerce

[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/woo-cart-counter)](https://wordpress.org/plugins/woo-cart-counter/)
[![WordPress Plugin Rating](https://img.shields.io/wordpress/plugin/stars/woo-cart-counter)](https://wordpress.org/plugins/woo-cart-counter/)
[![WordPress Plugin Downloads](https://img.shields.io/wordpress/plugin/dt/woo-cart-counter)](https://wordpress.org/plugins/woo-cart-counter/)
[![WordPress Tested](https://img.shields.io/wordpress/plugin/tested/woo-cart-counter)](https://wordpress.org/plugins/woo-cart-counter/)
[![License](https://img.shields.io/badge/license-GPL--2.0%2B-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

A lightweight, customizable cart counter for WooCommerce with shortcode, widget, and block support. Display real-time cart count with AJAX updates.

## Description

Cart Counter for WooCommerce is a performance-optimized plugin that adds a customizable cart counter to your WooCommerce store. Display the cart item count anywhere on your site using shortcodes, widgets, or Gutenberg blocks.

### Key Features

* **Multiple Display Methods**: Shortcode, Widget, and Gutenberg Block
* **Real-time Updates**: AJAX-powered cart count updates without page reload
* **Highly Customizable**: Choose from different icons, positions, and styles
* **Performance Optimized**: Minimal impact on page load with lazy loading
* **Accessibility Ready**: WCAG 2.1 AA compliant with ARIA labels
* **Mobile Responsive**: Touch-friendly design that works on all devices
* **Translation Ready**: Full internationalization support
* **Developer Friendly**: Extensive hooks and filters for customization
* **Theme Compatibility Mode**: Enhanced compatibility with various themes

### Display Options

* **Icons**: Shopping cart, basket, bag, or custom icon
* **Styles**: Icon with count, icon with text, or text only
* **Positions**: Top right, top left, bottom right, bottom left, or inline
* **Visibility**: Option to hide when cart is empty
* **Extras**: Show cart total, add custom text before/after

## Installation

### From WordPress Admin

1. Navigate to Plugins > Add New
2. Search for "Cart Counter for WooCommerce"
3. Click "Install Now" and then "Activate"

### Manual Installation

1. Download the plugin zip file
2. Extract to `/wp-content/plugins/woo-cart-counter`
3. Activate through the 'Plugins' menu in WordPress

### Minimum Requirements

* WordPress 5.8 or greater
* PHP version 7.4 or greater
* WooCommerce 5.0 or greater

## Usage

### Shortcode

Basic usage:
```
[woo_cart_counter]
```

With parameters:
```
[woo_cart_counter icon="basket" show_total="true" count_position="top_left"]
```

### PHP Template

```php
<?php echo do_shortcode('[woo_cart_counter]'); ?>
```

### Shortcode Parameters

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

## Configuration

### Basic Settings

Navigate to **WooCommerce → Settings → Cart Counter** to configure:

* Default icon style
* Counter colors (background, text)
* Icon and badge sizes
* AJAX update settings
* Custom CSS

### Position & Compatibility

New in version 1.1.0:

* **Compatibility Mode**: Force absolute positioning for better theme compatibility
* **Counter Offset**: Fine-tune vertical and horizontal position
* **Container Margin**: Add space above the cart icon
* **Z-Index Control**: Manage layering with other elements
* **Force Colors**: Override theme color conflicts

### Advanced CSS Selectors

* Target specific containers with custom CSS selectors
* Add additional CSS classes to the counter element
* Full control over styling with custom CSS

## Shortcode Generator

Use the built-in shortcode generator at **WooCommerce → Cart Counter** to:

* Configure all options visually
* See live preview of your settings
* Copy generated shortcode with one click
* Get usage examples

## Developer Features

### Hooks and Filters

#### Filters

* `woo_cart_counter_cart_count` - Modify the cart count
* `woo_cart_counter_cart_total` - Modify the cart total
* `woo_cart_counter_cart_url` - Modify the cart URL
* `woo_cart_counter_available_icons` - Add custom icon options
* `woo_cart_counter_settings` - Modify plugin settings
* `woo_cart_counter_load_assets` - Control asset loading
* `woo_cart_counter_shortcode_output` - Filter shortcode output
* `woo_cart_counter_svg_icons` - Add custom SVG icons

#### Actions

* `woo_cart_counter_before_render` - Before counter renders
* `woo_cart_counter_after_render` - After counter renders
* `woo_cart_counter_upgrade` - During plugin upgrade

### JavaScript Events

```javascript
// Listen for cart updates
document.addEventListener('wooCartCounterUpdated', function(e) {
    console.log('New cart count:', e.detail.count);
    console.log('New cart total:', e.detail.total);
});
```

### CSS Variables

```css
:root {
    --wcc-primary-color: #333333;
    --wcc-counter-bg: #ff0000;
    --wcc-counter-color: #ffffff;
    --wcc-icon-size: 24px;
    --wcc-counter-size: 18px;
    --wcc-counter-font-size: 12px;
    --wcc-border-radius: 50%;
    --wcc-animation-duration: 0.3s;
    --wcc-hover-scale: 1.05;
}
```

## Frequently Asked Questions

### Does this plugin work with my theme?

Yes! The plugin is designed to work with any properly coded WordPress theme. Version 1.1.0 includes enhanced theme compatibility mode for better support.

### Can I customize the appearance?

Absolutely! You can customize colors, sizes, and styles using:
- Backend settings for easy configuration
- CSS variables for consistent theming
- Custom CSS for advanced styling
- Filter hooks for programmatic customization

### Will it slow down my site?

No. The plugin is optimized for performance with:
- Conditional loading (only loads assets where needed)
- Minified CSS and JavaScript files
- Efficient AJAX updates
- LocalStorage caching

### Is it compatible with caching plugins?

Yes, the AJAX functionality ensures the cart count is always up-to-date regardless of page caching.

## Changelog

### 1.1.0
* Added theme compatibility mode for better positioning control
* New backend options for fine-tuning counter position
* Added cart container margin top setting
* Z-index control for better layering
* Force colors option to override theme styles
* Advanced CSS selectors for targeting specific elements
* Hide counter badge when cart has 0 items
* Improved shortcode configurator with live preview

### 1.0.0
* Initial release

## Support

For support, please use the [WordPress.org support forum](https://wordpress.org/support/plugin/woo-cart-counter/).

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## Credits

* Developed by [StarsMedia.com](https://starsmedia.com)
* Icons by Feather Icons
* Inspired by the WooCommerce community

## License

This plugin is licensed under the GPL v2 or later.

> This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

> This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

> You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.