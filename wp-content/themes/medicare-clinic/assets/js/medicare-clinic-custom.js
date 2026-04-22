jQuery(function($) {
    "use strict";

    // Scroll to top functionality
    $(window).on('scroll', function() {
        if ($(this).scrollTop() >= 50) {
            $('#return-to-top').fadeIn(200);
        } else {
            $('#return-to-top').fadeOut(200);
        }
    });

    $('#return-to-top').on('click', function() {
        $('body,html').animate({ scrollTop: 0 }, 500);
    });

    // Side navigation toggle
    $('.gb_toggle').on('click', function() {
        medicare_clinic_Keyboard_loop($('.side_gb_nav'));
    });

    // Preloader fade out
    setTimeout(function() {
        $(".loader").fadeOut("slow");
    }, 1000);

});

// Mobile responsive menu
function medicare_clinic_menu_open_nav() {
    jQuery(".sidenav").addClass('open');
}

function medicare_clinic_menu_close_nav() {
    jQuery(".sidenav").removeClass('open');
}

// slider
jQuery(document).ready(function($) {
   // Slider
  $(document).ready(function() {
    $('#slider .owl-carousel').owlCarousel({
      loop: true,
      margin: 0,
      nav: true,
      dots: false,
      rtl: false,
      items: 1,
      autoplay: false,
      autoplayTimeout: 3000,
      autoplayHoverPause: true,
    });
  });

  $('#our-services .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        nav: false,
        dots: false,
        autoplay: false,
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 2
            },
            1000: {
                items: 4
            }
        }
    });
  
});