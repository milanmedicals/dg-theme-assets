// enhancements.js - Optimized JavaScript for Pharmacy Theme
(function($) {
    'use strict';
    
    // Debounce function to limit resize events and prevent forced reflows
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Update sidebar visibility without causing layout thrashing
    function updateSidebarVisibility() {
        // Use requestAnimationFrame to batch style changes
        requestAnimationFrame(function() {
            if (window.matchMedia("(max-width: 991px)").matches) {
                $(".sidebar-categories").css('display', 'none');
                if (!$(".hamburger-menu").is(":visible")) {
                    $(".sidebar-categories")
                        .removeClass("active")
                        .css('display', 'block');
                }
            } else {
                $(".sidebar-categories")
                    .removeClass("active")
                    .css('display', 'block');
            }
        });
    }

    // Initialize when DOM is ready
    $(document).ready(function() {
        // Mobile menu toggle with optimized event handling
        if ($(".hamburger-menu").length) {
            $(".hamburger-menu").on("click", function () {
                requestAnimationFrame(function() {
                    $(".sidebar-categories")
                        .slideToggle(300)
                        .toggleClass("active");
                    
                    // Optimized class toggling
                    var $icon = $(".hamburger-menu i");
                    if ($icon.hasClass("fa-bars")) {
                        $icon.removeClass("fa-bars").addClass("fa-times");
                    } else {
                        $icon.removeClass("fa-times").addClass("fa-bars");
                    }
                });
            });
        }

        // Handle resize event with debounce to prevent excessive reflows
        const debouncedResize = debounce(function() {
            updateSidebarVisibility();
        }, 250);
        
        $(window).on("resize", debouncedResize);

        // Initial call to set correct state
        updateSidebarVisibility();
        
        // Optimized AJAX search functionality
        var searchTimeout;
        $('#search-input').on('input', function() {
            clearTimeout(searchTimeout);
            var searchTerm = $(this).val().trim();
            
            if (searchTerm.length > 2) {
                searchTimeout = setTimeout(function() {
                    performSearch(searchTerm);
                }, 300);
            }
        });
        
        // Cart fragment update optimization
        $(document.body).on('added_to_cart', function() {
            // Use requestAnimationFrame to update cart count
            requestAnimationFrame(function() {
                if (typeof WC !== 'undefined' && WC.cart_fragments) {
                    $.post(WC.cart_fragments.apply_filters.url, {
                        _wpnonce: WC.cart_fragments.apply_filters.nonce
                    }, function(data) {
                        if (data && data.fragments) {
                            $.each(data.fragments, function(key, value) {
                                $(key).replaceWith(value);
                            });
                        }
                    });
                }
            });
        });
    });
    
    // Perform search with optimized AJAX call
    function performSearch(term) {
        if (term.length < 3) return;
        
        $.ajax({
            url: pharma_ajax.ajax_url,
            type: 'GET',
            data: {
                action: 'pharmacy_search',
                term: term,
                nonce: pharma_ajax.nonce
            },
            beforeSend: function() {
                // Show loading indicator if needed
            },
            success: function(response) {
                if (response && response.length) {
                    updateSearchResults(response);
                }
            },
            error: function() {
                // Handle error quietly
            }
        });
    }
    
    // Update search results efficiently
    function updateSearchResults(results) {
        // This would be implemented based on your search UI needs
        // Optimized to minimize DOM manipulation
    }

})(jQuery);

// Load non-critical resources after page load
window.addEventListener('load', function() {
    // Load Font Awesome asynchronously
    var fa = document.createElement('link');
    fa.rel = 'stylesheet';
    fa.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';
    fa.crossOrigin = 'anonymous';
    fa.referrerPolicy = 'no-referrer';
    document.head.appendChild(fa);
    
    // Preconnect to important domains for performance
    var preconnectDomains = [
        'https://cdnjs.cloudflare.com',
        'https://fonts.googleapis.com',
        'https://fonts.gstatic.com'
    ];
    
    preconnectDomains.forEach(function(domain) {
        var link = document.createElement('link');
        link.rel = 'preconnect';
        link.href = domain;
        document.head.appendChild(link);
    });
});

// Optimize Web Vitals - Core Web Vitals improvements
(function() {
    // Track largest contentful paint
    let lcpValue = 0;
    const lcpObserver = new PerformanceObserver(function(entryList) {
        const entries = entryList.getEntries();
        const lastEntry = entries[entries.length - 1];
        lcpValue = lastEntry.renderTime || lastEntry.loadTime;
    });
    
    lcpObserver.observe({type: 'largest-contentful-paint', buffered: true});
    
    // Track cumulative layout shift
    let clsValue = 0;
    const clsObserver = new PerformanceObserver(function(entryList) {
        const entries = entryList.getEntries();
        entries.forEach(function(entry) {
            if (!entry.hadRecentInput) {
                clsValue += entry.value;
            }
        });
    });
    
    clsObserver.observe({type: 'layout-shift', buffered: true});
    
    // Report Web Vitals if needed (for monitoring)
    function reportWebVitals() {
        // Implementation for analytics would go here
    }
})();
