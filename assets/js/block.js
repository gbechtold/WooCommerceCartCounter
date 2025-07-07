/**
 * Gutenberg Block for WooCommerce Cart Counter
 *
 * @package WooCartCounter
 * @version 1.0.0
 */

(function() {
    'use strict';

    var __ = wp.i18n.__;
    var createElement = wp.element.createElement;
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var BlockControls = wp.blockEditor.BlockControls;
    var AlignmentToolbar = wp.blockEditor.AlignmentToolbar;
    var PanelBody = wp.components.PanelBody;
    var SelectControl = wp.components.SelectControl;
    var ToggleControl = wp.components.ToggleControl;
    var TextControl = wp.components.TextControl;
    var RangeControl = wp.components.RangeControl;
    var ServerSideRender = wp.serverSideRender;

    // Icon SVG
    var blockIcon = createElement('svg', {
        width: 24,
        height: 24,
        viewBox: '0 0 24 24',
        fill: 'none',
        stroke: 'currentColor',
        strokeWidth: 2,
        strokeLinecap: 'round',
        strokeLinejoin: 'round'
    }, [
        createElement('circle', { key: 'circle1', cx: 9, cy: 21, r: 1 }),
        createElement('circle', { key: 'circle2', cx: 20, cy: 21, r: 1 }),
        createElement('path', { key: 'path', d: 'M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6' })
    ]);

    // Register block
    registerBlockType('woo-cart-counter/cart-counter', {
        title: __('Cart Counter', 'woo-cart-counter'),
        description: __('Display WooCommerce cart counter with customizable options.', 'woo-cart-counter'),
        icon: blockIcon,
        category: 'woocommerce',
        keywords: [
            __('cart', 'woo-cart-counter'),
            __('counter', 'woo-cart-counter'),
            __('shopping', 'woo-cart-counter'),
            __('woocommerce', 'woo-cart-counter')
        ],
        supports: {
            align: ['left', 'center', 'right'],
            html: false
        },
        attributes: {
            icon: {
                type: 'string',
                default: 'cart'
            },
            iconUrl: {
                type: 'string',
                default: ''
            },
            showCount: {
                type: 'boolean',
                default: true
            },
            showTotal: {
                type: 'boolean',
                default: false
            },
            hideEmpty: {
                type: 'boolean',
                default: false
            },
            customClass: {
                type: 'string',
                default: ''
            },
            linkToCart: {
                type: 'boolean',
                default: true
            },
            displayStyle: {
                type: 'string',
                default: 'icon_count'
            },
            countPosition: {
                type: 'string',
                default: 'top_right'
            },
            textBefore: {
                type: 'string',
                default: ''
            },
            textAfter: {
                type: 'string',
                default: ''
            },
            currencySymbol: {
                type: 'boolean',
                default: true
            },
            align: {
                type: 'string'
            }
        },

        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            // Available options
            var iconOptions = [
                { label: __('Shopping Cart', 'woo-cart-counter'), value: 'cart' },
                { label: __('Shopping Basket', 'woo-cart-counter'), value: 'basket' },
                { label: __('Shopping Bag', 'woo-cart-counter'), value: 'bag' },
                { label: __('Custom Icon', 'woo-cart-counter'), value: 'custom' }
            ];

            var displayStyleOptions = [
                { label: __('Icon with Count', 'woo-cart-counter'), value: 'icon_count' },
                { label: __('Icon with Text', 'woo-cart-counter'), value: 'icon_text' },
                { label: __('Text Only', 'woo-cart-counter'), value: 'text_only' }
            ];

            var countPositionOptions = [
                { label: __('Top Right', 'woo-cart-counter'), value: 'top_right' },
                { label: __('Top Left', 'woo-cart-counter'), value: 'top_left' },
                { label: __('Bottom Right', 'woo-cart-counter'), value: 'bottom_right' },
                { label: __('Bottom Left', 'woo-cart-counter'), value: 'bottom_left' },
                { label: __('Inline', 'woo-cart-counter'), value: 'inline' }
            ];

            // Inspector controls
            var inspectorControls = createElement(InspectorControls, {},
                // Icon Settings
                createElement(PanelBody, {
                    title: __('Icon Settings', 'woo-cart-counter'),
                    initialOpen: true
                }, [
                    createElement(SelectControl, {
                        key: 'icon-select',
                        label: __('Icon Type', 'woo-cart-counter'),
                        value: attributes.icon,
                        options: iconOptions,
                        onChange: function(value) {
                            setAttributes({ icon: value });
                        }
                    }),
                    attributes.icon === 'custom' && createElement(TextControl, {
                        key: 'icon-url',
                        label: __('Custom Icon URL', 'woo-cart-counter'),
                        value: attributes.iconUrl,
                        onChange: function(value) {
                            setAttributes({ iconUrl: value });
                        },
                        help: __('Enter the URL of your custom icon image.', 'woo-cart-counter')
                    })
                ]),

                // Display Settings
                createElement(PanelBody, {
                    title: __('Display Settings', 'woo-cart-counter'),
                    initialOpen: false
                }, [
                    createElement(SelectControl, {
                        key: 'display-style',
                        label: __('Display Style', 'woo-cart-counter'),
                        value: attributes.displayStyle,
                        options: displayStyleOptions,
                        onChange: function(value) {
                            setAttributes({ displayStyle: value });
                        }
                    }),
                    createElement(SelectControl, {
                        key: 'count-position',
                        label: __('Count Position', 'woo-cart-counter'),
                        value: attributes.countPosition,
                        options: countPositionOptions,
                        onChange: function(value) {
                            setAttributes({ countPosition: value });
                        }
                    }),
                    createElement(ToggleControl, {
                        key: 'show-count',
                        label: __('Show Item Count', 'woo-cart-counter'),
                        checked: attributes.showCount,
                        onChange: function(value) {
                            setAttributes({ showCount: value });
                        }
                    }),
                    createElement(ToggleControl, {
                        key: 'show-total',
                        label: __('Show Cart Total', 'woo-cart-counter'),
                        checked: attributes.showTotal,
                        onChange: function(value) {
                            setAttributes({ showTotal: value });
                        }
                    }),
                    createElement(ToggleControl, {
                        key: 'currency-symbol',
                        label: __('Show Currency Symbol', 'woo-cart-counter'),
                        checked: attributes.currencySymbol,
                        onChange: function(value) {
                            setAttributes({ currencySymbol: value });
                        }
                    })
                ]),

                // Behavior Settings
                createElement(PanelBody, {
                    title: __('Behavior Settings', 'woo-cart-counter'),
                    initialOpen: false
                }, [
                    createElement(ToggleControl, {
                        key: 'link-to-cart',
                        label: __('Link to Cart Page', 'woo-cart-counter'),
                        checked: attributes.linkToCart,
                        onChange: function(value) {
                            setAttributes({ linkToCart: value });
                        }
                    }),
                    createElement(ToggleControl, {
                        key: 'hide-empty',
                        label: __('Hide When Cart is Empty', 'woo-cart-counter'),
                        checked: attributes.hideEmpty,
                        onChange: function(value) {
                            setAttributes({ hideEmpty: value });
                        }
                    })
                ]),

                // Text Settings
                createElement(PanelBody, {
                    title: __('Text Settings', 'woo-cart-counter'),
                    initialOpen: false
                }, [
                    createElement(TextControl, {
                        key: 'text-before',
                        label: __('Text Before Counter', 'woo-cart-counter'),
                        value: attributes.textBefore,
                        onChange: function(value) {
                            setAttributes({ textBefore: value });
                        }
                    }),
                    createElement(TextControl, {
                        key: 'text-after',
                        label: __('Text After Counter', 'woo-cart-counter'),
                        value: attributes.textAfter,
                        onChange: function(value) {
                            setAttributes({ textAfter: value });
                        }
                    })
                ]),

                // Advanced Settings
                createElement(PanelBody, {
                    title: __('Advanced Settings', 'woo-cart-counter'),
                    initialOpen: false
                }, [
                    createElement(TextControl, {
                        key: 'custom-class',
                        label: __('Custom CSS Class', 'woo-cart-counter'),
                        value: attributes.customClass,
                        onChange: function(value) {
                            setAttributes({ customClass: value });
                        },
                        help: __('Add custom CSS classes for styling.', 'woo-cart-counter')
                    })
                ])
            );

            // Server-side render
            var serverSideRender = createElement(ServerSideRender, {
                block: 'woo-cart-counter/cart-counter',
                attributes: attributes
            });

            return [
                inspectorControls,
                createElement('div', {
                    key: 'woo-cart-counter-editor',
                    className: 'woo-cart-counter-editor-wrapper'
                }, serverSideRender)
            ];
        },

        save: function() {
            // Server-side rendering, so return null
            return null;
        }
    });

})();