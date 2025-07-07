/**
 * WooCommerce Cart Counter Frontend JavaScript
 *
 * @package WooCartCounter
 * @version 1.0.0
 */

(function() {
    'use strict';

    // Main cart counter object
    var WooCartCounter = {
        // Configuration
        config: {
            ajaxUrl: '',
            nonce: '',
            enableAjax: true,
            updateDelay: 1000,
            selectors: {
                counter: '.woo-cart-counter',
                count: '.woo-cart-counter-count',
                total: '.woo-cart-counter-total',
                widget: '.woo-cart-counter-widget-content'
            }
        },

        // State
        state: {
            isUpdating: false,
            updateTimeout: null,
            lastCount: null
        },

        /**
         * Initialize cart counter
         */
        init: function() {
            // Merge config with localized data
            if (typeof window.wooCartCounter !== 'undefined') {
                this.config = Object.assign(this.config, window.wooCartCounter);
            }

            // Bind events
            this.bindEvents();

            // Initialize counter state
            this.initializeState();

            // Set up mutation observer for dynamic content
            this.setupMutationObserver();
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            var self = this;

            // Listen for WooCommerce events
            if (window.jQuery) {
                jQuery(document.body).on('added_to_cart removed_from_cart updated_cart_totals', function() {
                    self.scheduleUpdate();
                });

                // Listen for cart updates
                jQuery(document).on('cart_page_refreshed cart_totals_refreshed', function() {
                    self.scheduleUpdate();
                });

                // Listen for mini cart updates
                jQuery(document.body).on('wc_fragments_refreshed', function() {
                    self.updateFromFragments();
                });
            }

            // Listen for custom events
            document.addEventListener('woo_cart_counter_update', function() {
                self.scheduleUpdate();
            });

            // Storage event for cross-tab updates
            window.addEventListener('storage', function(e) {
                if (e.key === 'woo_cart_counter_data') {
                    self.updateFromStorage(e.newValue);
                }
            });

            // Page visibility change
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    self.scheduleUpdate();
                }
            });
        },

        /**
         * Initialize counter state
         */
        initializeState: function() {
            var counters = document.querySelectorAll(this.config.selectors.count);
            if (counters.length > 0) {
                this.state.lastCount = parseInt(counters[0].getAttribute('data-count') || counters[0].textContent, 10);
            }

            // Load from storage
            var storedData = this.getStoredData();
            if (storedData) {
                this.updateCounters(storedData);
            }
        },

        /**
         * Set up mutation observer
         */
        setupMutationObserver: function() {
            var self = this;
            
            if (!window.MutationObserver) {
                return;
            }

            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length) {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === 1 && node.matches && node.matches(self.config.selectors.counter)) {
                                self.updateSingleCounter(node);
                            }
                        });
                    }
                });
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        },

        /**
         * Schedule update
         */
        scheduleUpdate: function() {
            var self = this;

            if (!this.config.enableAjax) {
                return;
            }

            // Clear existing timeout
            if (this.state.updateTimeout) {
                clearTimeout(this.state.updateTimeout);
            }

            // Schedule new update
            this.state.updateTimeout = setTimeout(function() {
                self.updateCart();
            }, this.config.updateDelay);
        },

        /**
         * Update cart via AJAX
         */
        updateCart: function() {
            var self = this;

            if (this.state.isUpdating) {
                return;
            }

            this.state.isUpdating = true;

            // Create request
            var xhr = new XMLHttpRequest();
            xhr.open('POST', this.config.ajaxUrl || this.config.ajax_url, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                self.state.isUpdating = false;

                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success && response.data) {
                            self.handleUpdateResponse(response.data);
                        }
                    } catch (e) {
                        console.error('WooCartCounter: Failed to parse response', e);
                    }
                }
            };

            xhr.onerror = function() {
                self.state.isUpdating = false;
                console.error('WooCartCounter: AJAX request failed');
            };

            // Send request
            var params = 'action=woo_cart_counter_update&nonce=' + encodeURIComponent(this.config.nonce);
            xhr.send(params);
        },

        /**
         * Handle update response
         */
        handleUpdateResponse: function(data) {
            // Update counters
            this.updateCounters(data);

            // Store data
            this.storeData(data);

            // Trigger custom event
            this.triggerEvent('updated', data);

            // Update fragments if provided
            if (data.fragments) {
                this.updateFragments(data.fragments);
            }
        },

        /**
         * Update counters
         */
        updateCounters: function(data) {
            var self = this;

            // Update count
            if (typeof data.count !== 'undefined') {
                var countElements = document.querySelectorAll(this.config.selectors.count);
                countElements.forEach(function(element) {
                    element.textContent = data.count;
                    element.setAttribute('data-count', data.count);
                });

                // Update empty class
                var counters = document.querySelectorAll(this.config.selectors.counter);
                counters.forEach(function(counter) {
                    if (data.count === 0) {
                        counter.classList.add('woo-cart-counter-empty');
                    } else {
                        counter.classList.remove('woo-cart-counter-empty');
                    }
                });

                // Animate if count changed
                if (this.state.lastCount !== null && this.state.lastCount !== data.count) {
                    this.animateCountChange(data.count > this.state.lastCount);
                }

                this.state.lastCount = data.count;
            }

            // Update total
            if (data.total) {
                var totalElements = document.querySelectorAll(this.config.selectors.total);
                totalElements.forEach(function(element) {
                    element.innerHTML = data.total;
                });
            }
        },

        /**
         * Update single counter
         */
        updateSingleCounter: function(counter) {
            var storedData = this.getStoredData();
            if (!storedData) {
                return;
            }

            var countElement = counter.querySelector(this.config.selectors.count);
            if (countElement) {
                countElement.textContent = storedData.count;
                countElement.setAttribute('data-count', storedData.count);
            }

            var totalElement = counter.querySelector(this.config.selectors.total);
            if (totalElement && storedData.total) {
                totalElement.innerHTML = storedData.total;
            }

            if (storedData.count === 0) {
                counter.classList.add('woo-cart-counter-empty');
            } else {
                counter.classList.remove('woo-cart-counter-empty');
            }
        },

        /**
         * Update fragments
         */
        updateFragments: function(fragments) {
            Object.keys(fragments).forEach(function(selector) {
                var elements = document.querySelectorAll(selector);
                elements.forEach(function(element) {
                    var temp = document.createElement('div');
                    temp.innerHTML = fragments[selector];
                    var newElement = temp.firstChild;
                    if (newElement) {
                        element.parentNode.replaceChild(newElement, element);
                    }
                });
            });
        },

        /**
         * Update from WooCommerce fragments
         */
        updateFromFragments: function() {
            // This is called when WooCommerce updates fragments
            // We don't need to do anything as WooCommerce handles the update
        },

        /**
         * Animate count change
         */
        animateCountChange: function(isIncrease) {
            var counters = document.querySelectorAll(this.config.selectors.counter);
            var animationClass = isIncrease ? 'woo-cart-counter-increase' : 'woo-cart-counter-decrease';

            counters.forEach(function(counter) {
                counter.classList.add(animationClass);
                setTimeout(function() {
                    counter.classList.remove(animationClass);
                }, 600);
            });
        },

        /**
         * Store data in localStorage
         */
        storeData: function(data) {
            if (!window.localStorage) {
                return;
            }

            try {
                var storageData = {
                    count: data.count,
                    total: data.total,
                    timestamp: Date.now()
                };
                localStorage.setItem('woo_cart_counter_data', JSON.stringify(storageData));
            } catch (e) {
                // Fail silently
            }
        },

        /**
         * Get stored data
         */
        getStoredData: function() {
            if (!window.localStorage) {
                return null;
            }

            try {
                var data = localStorage.getItem('woo_cart_counter_data');
                if (data) {
                    var parsed = JSON.parse(data);
                    // Check if data is not too old (5 minutes)
                    if (parsed.timestamp && (Date.now() - parsed.timestamp) < 300000) {
                        return parsed;
                    }
                }
            } catch (e) {
                // Fail silently
            }

            return null;
        },

        /**
         * Update from storage
         */
        updateFromStorage: function(value) {
            if (!value) {
                return;
            }

            try {
                var data = JSON.parse(value);
                this.updateCounters(data);
            } catch (e) {
                // Fail silently
            }
        },

        /**
         * Trigger custom event
         */
        triggerEvent: function(eventName, data) {
            var event;
            
            if (typeof CustomEvent === 'function') {
                event = new CustomEvent('woo_cart_counter_' + eventName, {
                    detail: data,
                    bubbles: true,
                    cancelable: true
                });
            } else {
                // IE fallback
                event = document.createEvent('CustomEvent');
                event.initCustomEvent('woo_cart_counter_' + eventName, true, true, data);
            }

            document.dispatchEvent(event);
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            WooCartCounter.init();
        });
    } else {
        WooCartCounter.init();
    }

    // Export for external use
    window.WooCartCounterInstance = WooCartCounter;

})();