// enhancements.js - Optimized for performance
(function($) {
    'use strict';
    
    // Debounce function to limit resize events
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

    function updateSidebarVisibility() {
        if (window.matchMedia("(max-width: 991px)").matches) {
            $(".sidebar-categories").hide();
            if (!$(".hamburger-menu").is(":visible")) {
                $(".sidebar-categories").removeClass("active").show();
            }
        } else {
            $(".sidebar-categories").removeClass("active").show();
        }
    }

    // Initialize when DOM is ready
    $(document).ready(function() {
        // Mobile menu toggle
        if ($(".hamburger-menu").length) {
            $(".hamburger-menu").on("click", function () {
                $(".sidebar-categories").slideToggle(300).toggleClass("active");
                $(this).find("i").toggleClass("fa-bars fa-times");
            });
        }

        // Handle resize event with debounce
        const debouncedResize = debounce(function() {
            updateSidebarVisibility();
        }, 250);
        
        $(window).on("resize", debouncedResize);

        // Initial call to set correct state
        updateSidebarVisibility();
    });

})(jQuery);
