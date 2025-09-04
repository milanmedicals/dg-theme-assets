// pharma.js - optimized Astra child theme scripts
jQuery(function ($) {
  const $sidebar = $(".sidebar-categories");
  const $hamburger = $(".hamburger-menu");

  function updateSidebar() {
    const isMobile = window.matchMedia("(max-width: 991px)").matches;
    if (isMobile) {
      $sidebar.hide().removeClass("active");
    } else {
      $sidebar.show().removeClass("active");
      $hamburger.find("i").removeClass("fa-times").addClass("fa-bars");
    }
  }

  $hamburger.on("click", function () {
    $sidebar.stop(true, true).slideToggle(250).toggleClass("active");
    $(this).find("i").toggleClass("fa-bars fa-times");
  });

  // Debounce resize to avoid forced reflows
  let resizeTimer;
  $(window).on("resize", function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(updateSidebar, 200);
  });

  updateSidebar();
});
