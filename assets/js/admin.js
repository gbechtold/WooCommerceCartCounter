/**
 * WooCommerce Cart Counter Admin JavaScript
 *
 * @package WooCartCounter
 * @version 1.0.0
 */

(function($) {
    'use strict';

    var WooCartCounterAdmin = {
        init: function() {
            this.bindEvents();
            this.initPreview();
            this.addHelpContent();
            this.initShortcodeGenerator();
        },

        bindEvents: function() {
            // Color picker changes
            $(document).on('change', '[id*="woo_cart_counter"][id*="color"]', this.updatePreview);
            
            // Size changes
            $(document).on('input change', '[id*="woo_cart_counter_icon_size"], [id*="woo_cart_counter_badge_size"]', this.updatePreview);
            
            // Icon and position changes
            $(document).on('change', '[id*="woo_cart_counter_default_icon"], [id*="woo_cart_counter_default_position"]', this.updatePreview);
            
            // Performance indicator updates
            $(document).on('change', '[id="woo_cart_counter_update_delay"]', this.updatePerformanceIndicator);
            
            // Custom CSS changes
            $(document).on('input', '[id="woo_cart_counter_custom_css"]', this.debounce(this.updatePreview, 500));
        },

        initPreview: function() {
            if ($('.woo-cart-counter-preview').length === 0) {
                this.addPreview();
            }
            this.updatePreview();
        },

        addPreview: function() {
            var previewHTML = '<div class="woo-cart-counter-preview">' +
                '<h4>Preview</h4>' +
                '<div id="woo-cart-counter-live-preview">' +
                    '<div class="woo-cart-counter woo-cart-counter-style-icon_count woo-cart-counter-position-top_right">' +
                        '<div class="woo-cart-counter-inner">' +
                            '<span class="woo-cart-counter-icon-wrapper">' +
                                '<svg class="woo-cart-counter-icon woo-cart-counter-icon-cart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' +
                                    '<circle cx="9" cy="21" r="1"></circle>' +
                                    '<circle cx="20" cy="21" r="1"></circle>' +
                                    '<path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>' +
                                '</svg>' +
                                '<span class="woo-cart-counter-count">3</span>' +
                            '</span>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';

            $('#woo_cart_counter_settings').closest('table').after(previewHTML);
        },

        updatePreview: function() {
            var preview = $('#woo-cart-counter-live-preview .woo-cart-counter');
            if (preview.length === 0) return;

            // Get current settings
            var settings = WooCartCounterAdmin.getSettings();
            
            // Update CSS variables
            var style = '<style id="woo-cart-counter-preview-style">' +
                ':root {' +
                    '--wcc-primary-color: ' + settings.primaryColor + ';' +
                    '--wcc-counter-bg: ' + settings.counterBg + ';' +
                    '--wcc-counter-color: ' + settings.counterColor + ';' +
                    '--wcc-icon-size: ' + settings.iconSize + 'px;' +
                    '--wcc-counter-size: ' + settings.counterSize + 'px;' +
                '}' +
                settings.customCSS +
            '</style>';

            // Remove old style and add new
            $('#woo-cart-counter-preview-style').remove();
            $('head').append(style);

            // Update icon
            WooCartCounterAdmin.updatePreviewIcon(settings.icon);
            
            // Update position class
            preview.removeClass(function(index, className) {
                return (className.match(/(^|\s)woo-cart-counter-position-\S+/g) || []).join(' ');
            }).addClass('woo-cart-counter-position-' + settings.position);
        },

        updatePreviewIcon: function(iconType) {
            var iconWrapper = $('#woo-cart-counter-live-preview .woo-cart-counter-icon-wrapper');
            var icons = {
                cart: '<svg class="woo-cart-counter-icon woo-cart-counter-icon-cart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>',
                basket: '<svg class="woo-cart-counter-icon woo-cart-counter-icon-basket" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5.52 7h13"></path><path d="M9 11v6"></path><path d="M12 11v6"></path><path d="M15 11v6"></path><path d="M8 7V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v3"></path><path d="M5.25 7.01l.66 8.6A2 2 0 0 0 7.9 17.6h8.2a2 2 0 0 0 2-1.99l.66-8.6"></path></svg>',
                bag: '<svg class="woo-cart-counter-icon woo-cart-counter-icon-bag" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>'
            };

            if (icons[iconType]) {
                iconWrapper.html(icons[iconType] + '<span class="woo-cart-counter-count">3</span>');
            }
        },

        getSettings: function() {
            return {
                primaryColor: $('#woo_cart_counter_primary_color').val() || '#333333',
                counterBg: $('#woo_cart_counter_bg_color').val() || '#ff0000',
                counterColor: $('#woo_cart_counter_text_color').val() || '#ffffff',
                iconSize: $('#woo_cart_counter_icon_size').val() || '24',
                counterSize: $('#woo_cart_counter_badge_size').val() || '18',
                icon: $('#woo_cart_counter_default_icon').val() || 'cart',
                position: $('#woo_cart_counter_default_position').val() || 'top_right',
                customCSS: $('#woo_cart_counter_custom_css').val() || ''
            };
        },

        updatePerformanceIndicator: function() {
            var delay = parseInt($('#woo_cart_counter_update_delay').val(), 10);
            var indicator = $('.woo-cart-counter-performance-indicator');
            
            // Remove existing classes
            indicator.removeClass('good warning poor');
            
            // Add appropriate class based on delay
            if (delay >= 1000) {
                indicator.addClass('good');
            } else if (delay >= 500) {
                indicator.addClass('warning');
            } else {
                indicator.addClass('poor');
            }
        },

        addHelpContent: function() {
            // Add CSS variables help
            if ($('#woo_cart_counter_custom_css').length > 0 && $('.woo-cart-counter-css-variables').length === 0) {
                var cssHelp = '<div class="woo-cart-counter-css-variables">' +
                    '<h5>Available CSS Variables:</h5>' +
                    '<code>--wcc-primary-color: Icon and text color</code>' +
                    '<code>--wcc-counter-bg: Counter background color</code>' +
                    '<code>--wcc-counter-color: Counter text color</code>' +
                    '<code>--wcc-icon-size: Icon size</code>' +
                    '<code>--wcc-counter-size: Counter badge size</code>' +
                    '<code>--wcc-counter-font-size: Counter font size</code>' +
                    '<code>--wcc-border-radius: Counter border radius</code>' +
                    '<code>--wcc-animation-duration: Animation duration</code>' +
                '</div>';

                $('#woo_cart_counter_custom_css').before(cssHelp);
            }

            // Add shortcode examples
            if ($('.woo-cart-counter-shortcode-examples').length === 0) {
                var shortcodeExamples = '<div class="woo-cart-counter-shortcode-examples">' +
                    '<h4>Shortcode Examples:</h4>' +
                    '<pre>[woo_cart_counter]</pre>' +
                    '<pre>[woo_cart_counter icon="basket" show_total="true"]</pre>' +
                    '<pre>[woo_cart_counter display_style="text_only" text_before="Cart: "]</pre>' +
                    '<pre>[woo_cart_counter icon="custom" icon_url="/path/to/icon.png"]</pre>' +
                '</div>';

                $('.woo-cart-counter-preview').after(shortcodeExamples);
            }

            // Add performance indicator
            if ($('#woo_cart_counter_update_delay').length > 0 && $('.woo-cart-counter-performance-indicator').length === 0) {
                $('#woo_cart_counter_update_delay').after('<span class="woo-cart-counter-performance-indicator"></span>');
                this.updatePerformanceIndicator();
            }
        },

        debounce: function(func, wait) {
            var timeout;
            return function executedFunction() {
                var context = this;
                var args = arguments;
                var later = function() {
                    timeout = null;
                    func.apply(context, args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        // Shortcode Generator Methods
        initShortcodeGenerator: function() {
            if ($('#wcc-shortcode-form').length === 0) return;

            var self = this;
            
            // Bind events for shortcode generator
            $('.wcc-option').on('change input', function() {
                self.updateShortcode();
                self.updateShortcodePreview();
            });

            // Handle icon type change
            $('#wcc-icon').on('change', function() {
                if ($(this).val() === 'custom') {
                    $('.wcc-custom-icon-row').show();
                } else {
                    $('.wcc-custom-icon-row').hide();
                    $('#wcc-icon-url').val('');
                }
            });

            // Handle display style change
            $('#wcc-display-style').on('change', function() {
                var style = $(this).val();
                if (style === 'text_only') {
                    $('#wcc-count-position').closest('tr').hide();
                } else {
                    $('#wcc-count-position').closest('tr').show();
                }
            });

            // Copy shortcode functionality
            $('.wcc-copy-shortcode').on('click', function() {
                var shortcode = $('#wcc-generated-shortcode').val();
                var button = $(this);
                
                // Create temporary input element
                var $temp = $('<input>');
                $('body').append($temp);
                $temp.val(shortcode).select();
                
                // Copy to clipboard
                document.execCommand('copy');
                $temp.remove();
                
                // Update button text
                var originalText = button.text();
                button.text('Copied!').addClass('copied');
                
                setTimeout(function() {
                    button.text(originalText).removeClass('copied');
                }, 2000);
            });

            // Initial updates
            this.updateShortcode();
            this.updateShortcodePreview();
        },

        updateShortcode: function() {
            var shortcode = '[woo_cart_counter';
            var defaults = {
                icon: 'cart',
                show_count: 'true',
                show_total: 'false',
                hide_empty: 'false',
                link_to_cart: 'true',
                display_style: 'icon_count',
                count_position: 'top_right',
                currency_symbol: 'true'
            };

            // Collect all options
            $('.wcc-option').each(function() {
                var $input = $(this);
                var name = $input.attr('name');
                var value = '';
                
                if ($input.is(':checkbox')) {
                    value = $input.is(':checked') ? 'true' : 'false';
                } else {
                    value = $input.val();
                }
                
                // Only add non-default values
                if (value && value !== defaults[name] && value !== '') {
                    shortcode += ' ' + name + '="' + value + '"';
                }
            });

            shortcode += ']';
            
            $('#wcc-generated-shortcode').val(shortcode);
        },

        updateShortcodePreview: function() {
            var self = this;
            var previewContainer = $('#wcc-preview-container');
            
            // Get current settings
            var settings = {
                icon: $('#wcc-icon').val(),
                iconUrl: $('#wcc-icon-url').val(),
                showCount: $('#wcc-show-count').is(':checked'),
                showTotal: $('#wcc-show-total').is(':checked'),
                hideEmpty: $('#wcc-hide-empty').is(':checked'),
                linkToCart: $('#wcc-link-to-cart').is(':checked'),
                displayStyle: $('#wcc-display-style').val(),
                countPosition: $('#wcc-count-position').val(),
                textBefore: $('#wcc-text-before').val(),
                textAfter: $('#wcc-text-after').val(),
                customClass: $('#wcc-custom-class').val()
            };

            // Build classes
            var classes = [
                'woo-cart-counter',
                'woo-cart-counter-style-' + settings.displayStyle,
                'woo-cart-counter-position-' + settings.countPosition,
                'woo-cart-counter-icon-' + settings.icon
            ];

            if (settings.customClass) {
                classes.push(settings.customClass);
            }

            // Build preview HTML
            var html = '<div class="' + classes.join(' ') + '">';
            
            if (settings.linkToCart) {
                html += '<a href="#" class="woo-cart-counter-link" onclick="return false;">';
            }

            html += '<div class="woo-cart-counter-inner">';

            // Text before
            if (settings.textBefore) {
                html += '<span class="woo-cart-counter-text-before">' + settings.textBefore + '</span>';
            }

            // Icon
            if (settings.displayStyle !== 'text_only') {
                html += '<span class="woo-cart-counter-icon-wrapper">';
                html += self.getIconHTML(settings.icon, settings.iconUrl);
                
                if (settings.showCount && settings.countPosition !== 'inline') {
                    html += '<span class="woo-cart-counter-count" data-count="3">3</span>';
                }
                
                html += '</span>';
            }

            // Inline count
            if (settings.showCount && (settings.countPosition === 'inline' || settings.displayStyle === 'text_only')) {
                html += '<span class="woo-cart-counter-count-inline">';
                html += '<span class="woo-cart-counter-count" data-count="3">3</span>';
                html += '<span class="woo-cart-counter-label">items</span>';
                html += '</span>';
            }

            // Total
            if (settings.showTotal) {
                html += '<span class="woo-cart-counter-total-wrapper">';
                html += '<span class="woo-cart-counter-total">$29.99</span>';
                html += '</span>';
            }

            // Text after
            if (settings.textAfter) {
                html += '<span class="woo-cart-counter-text-after">' + settings.textAfter + '</span>';
            }

            html += '</div>';

            if (settings.linkToCart) {
                html += '</a>';
            }

            html += '</div>';

            previewContainer.html(html);
        },

        getIconHTML: function(iconType, iconUrl) {
            if (iconType === 'custom' && iconUrl) {
                return '<img src="' + iconUrl + '" alt="Cart" class="woo-cart-counter-icon woo-cart-counter-icon-custom">';
            }

            var icons = {
                cart: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="woo-cart-counter-icon woo-cart-counter-icon-cart"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>',
                basket: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="woo-cart-counter-icon woo-cart-counter-icon-basket"><path d="M5.52 7h13"></path><path d="M9 11v6"></path><path d="M12 11v6"></path><path d="M15 11v6"></path><path d="M8 7V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v3"></path><path d="M5.25 7.01l.66 8.6A2 2 0 0 0 7.9 17.6h8.2a2 2 0 0 0 2-1.99l.66-8.6"></path></svg>',
                bag: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="woo-cart-counter-icon woo-cart-counter-icon-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>'
            };

            return icons[iconType] || icons.cart;
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        WooCartCounterAdmin.init();
    });

    // Export for global access
    window.WooCartCounterAdmin = WooCartCounterAdmin;

})(jQuery);