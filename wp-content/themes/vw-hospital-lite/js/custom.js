jQuery(function($){
 	"use strict";
   	jQuery('.main-menu > ul').superfish({
		delay:       500,
		animation:   {opacity:'show',height:'show'},  
		speed:       'fast'
   });
});

function vw_hospital_lite_menu_open_nav() {
	window.vw_hospital_lite_responsiveMenu=true;
	jQuery(".sidenav").addClass('show');
}
function vw_hospital_lite_menu_close_nav() {
	window.vw_hospital_lite_responsiveMenu=false;
 	jQuery(".sidenav").removeClass('show');
}

jQuery(document).ready(function () {
	window.vw_hospital_lite_currentfocus=null;
  	vw_hospital_lite_checkfocusdElement();
	var vw_hospital_lite_body = document.querySelector('body');
	vw_hospital_lite_body.addEventListener('keyup', vw_hospital_lite_check_tab_press);
	var vw_hospital_lite_gotoHome = false;
	var vw_hospital_lite_gotoClose = false;
	window.vw_hospital_lite_responsiveMenu=false;
 	function vw_hospital_lite_checkfocusdElement(){
	 	if(window.vw_hospital_lite_currentfocus=document.activeElement.className){
		 	window.vw_hospital_lite_currentfocus=document.activeElement.className;
	 	}
 	}
 	function vw_hospital_lite_check_tab_press(e) {
		"use strict";
		// pick passed event or global event object if passed one is empty
		e = e || event;
		var activeElement;

		if(window.innerWidth < 999){
		if (e.keyCode == 9) {
			if(window.vw_hospital_lite_responsiveMenu){
			if (!e.shiftKey) {
				if(vw_hospital_lite_gotoHome) {
					jQuery( ".main-menu ul:first li:first a:first-child" ).focus();
				}
			}
			if (jQuery("a.closebtn.mobile-menu").is(":focus")) {
				vw_hospital_lite_gotoHome = true;
			} else {
				vw_hospital_lite_gotoHome = false;
			}

		}else{

			if(window.vw_hospital_lite_currentfocus=="responsivetoggle"){
				jQuery( "" ).focus();
			}
			}
		}
		}
		if (e.shiftKey && e.keyCode == 9) {
		if(window.innerWidth < 999){
			if(window.vw_hospital_lite_currentfocus=="header-search"){
				jQuery(".responsivetoggle").focus();
			}else{
				if(window.vw_hospital_lite_responsiveMenu){
				if(vw_hospital_lite_gotoClose){
					jQuery("a.closebtn.mobile-menu").focus();
				}
				if (jQuery( ".main-menu ul:first li:first a:first-child" ).is(":focus")) {
					vw_hospital_lite_gotoClose = true;
				} else {
					vw_hospital_lite_gotoClose = false;
				}
			
			}else{

			if(window.vw_hospital_lite_responsiveMenu){
			}
			}

			}
		}
		}
	 	vw_hospital_lite_checkfocusdElement();
	}
});

/*sticky sidebar*/
window.addEventListener('scroll', function() {
  var sticky = document.querySelector('.sidebar-sticky');
  if (!sticky) return;

  var scrollTop = window.scrollY || document.documentElement.scrollTop;
  var windowHeight = window.innerHeight;
  var documentHeight = document.documentElement.scrollHeight;

  var isBottom = scrollTop + windowHeight >= documentHeight-100;

  if (scrollTop >= 100 && !isBottom) {
    sticky.classList.add('sidebar-fixed');
  } else {
    sticky.classList.remove('sidebar-fixed');
  }
});

(function( $ ) {

	jQuery('document').ready(function($){
	    setTimeout(function () {
    		jQuery("#preloader").fadeOut("slow");
	    },1000);
	});

	$(window).scroll(function(){
	  	var sticky = $('.header-sticky'),
      		scroll = $(window).scrollTop();
	  	if (scroll >= 100) sticky.addClass('header-fixed');
	  	else sticky.removeClass('header-fixed');
	});

	$(document).ready(function () {
		$(window).scroll(function () {
		    if ($(this).scrollTop() > 100) {
		        $('.scrollup i').fadeIn();
		    } else {
		        $('.scrollup i').fadeOut();
		    }
		});
		$('.scrollup i').click(function () {
		    $("html, body").animate({
		        scrollTop: 0
		    }, 600);
		    return false;
		});
	});
	
})( jQuery );

/*sticky copyright*/
window.addEventListener('scroll', function() {
  var sticky = document.querySelector('.copyright-sticky');
  if (!sticky) return;

  var scrollTop = window.scrollY || document.documentElement.scrollTop;
  var windowHeight = window.innerHeight;
  var documentHeight = document.documentElement.scrollHeight;

  var isBottom = scrollTop + windowHeight >= documentHeight-100;

  if (scrollTop >= 100 && !isBottom) {
    sticky.classList.add('copyright-fixed');
  } else {
    sticky.classList.remove('copyright-fixed');
  }
});
