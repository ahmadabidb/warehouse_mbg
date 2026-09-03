(function($) {
  "use strict"; // Start of use strict

  // Toggle the side navigation
  $("#sidebarToggle, #sidebarToggleTop").on('click', function(e) {
    $("body").toggleClass("sidebar-toggled");
    $(".sidebar").toggleClass("toggled");
    if ($(".sidebar").hasClass("toggled")) {
      $('.sidebar .collapse').collapse('hide');
    };
  });

  // Remember the last known window width so we only react to real width
  // changes that cross a breakpoint. On mobile, hiding/showing the browser's
  // URL bar while scrolling fires a `resize` event with the SAME width; without
  // this guard that would reset the sidebar's open state and make it appear to
  // "auto-close" on scroll.
  var lastWindowWidth = $(window).width();

  // Collapse open menu accordions / sync the sidebar on genuine responsive
  // breakpoint changes (resize window, rotate device) — WITHOUT forcing the
  // sidebar closed while the user simply scrolls on a mobile device.
  $(window).resize(function() {
    var currentWidth = $(window).width();
    if (Math.abs(currentWidth - lastWindowWidth) < 1) {
      return; // no real width change (e.g. mobile URL bar show/hide) -> do nothing
    }
    lastWindowWidth = currentWidth;

    // Close any open menu accordions when window is resized below 768px
    if (currentWidth < 768) {
      $('.sidebar .collapse').collapse('hide');
    };

    // Toggle the side navigation when window is resized below 480px
    if (currentWidth < 480 && !$(".sidebar").hasClass("toggled")) {
      $("body").addClass("sidebar-toggled");
      $(".sidebar").addClass("toggled");
      $('.sidebar .collapse').collapse('hide');
    };
  });

  // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
  $('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function(e) {
    if ($(window).width() > 768) {
      var e0 = e.originalEvent,
        delta = e0.wheelDelta || -e0.detail;
      this.scrollTop += (delta < 0 ? 1 : -1) * 30;
      e.preventDefault();
    }
  });

  // Scroll to top button appear
  $(document).on('scroll', function() {
    var scrollDistance = $(this).scrollTop();
    if (scrollDistance > 100) {
      $('.scroll-to-top').fadeIn();
    } else {
      $('.scroll-to-top').fadeOut();
    }
  });

  // Smooth scrolling using jQuery easing
  $(document).on('click', 'a.scroll-to-top', function(e) {
    var $anchor = $(this);
    $('html, body').stop().animate({
      scrollTop: ($($anchor.attr('href')).offset().top)
    }, 1000, 'easeInOutExpo');
    e.preventDefault();
  });

})(jQuery); // End of use strict
