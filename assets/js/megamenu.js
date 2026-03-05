/*global $ */
$(document).ready(function () {

    "use strict";

    $('.menu > ul > li:has( > ul)').addClass('menu-dropdown-icon');
    $('.menu > ul > li > ul:not(:has(ul))').addClass('normal-sub');

    // Desktop: hover dropdowns
    $(".menu > ul > li").hover(
        function (e) {
            if ($(window).width() > 959) {
                $(this).children("ul").fadeIn(150);
                e.preventDefault();
            }
        }, function (e) {
            if ($(window).width() > 959) {
                $(this).children("ul").fadeOut(150);
                e.preventDefault();
            }
        }
    );

    // Mobile: click to toggle sub-menus
    $(".menu > ul > li").click(function(e) {
        if ($(window).width() <= 959) {
            var $sub = $(this).children("ul");
            if ($sub.length) {
                e.preventDefault();
                e.stopPropagation();
                $(this).siblings().children("ul").slideUp(200);
                $sub.slideToggle(250);
            }
        }
    });

    // Hamburger button toggle
    $("#hamburgerBtn").click(function (e) {
        e.preventDefault();
        $(this).toggleClass('active');
        $(".menu").toggleClass('mobile-open');
        $("#mobileMenuOverlay").toggleClass('active');
        $("body").toggleClass('mobile-menu-body-lock');
    });

    // Close menu on overlay click
    $("#mobileMenuOverlay").click(function () {
        $("#hamburgerBtn").removeClass('active');
        $(".menu").removeClass('mobile-open');
        $(this).removeClass('active');
        $("body").removeClass('mobile-menu-body-lock');
    });

    // Close menu on window resize to desktop
    $(window).resize(function () {
        if ($(window).width() > 959) {
            $("#hamburgerBtn").removeClass('active');
            $(".menu").removeClass('mobile-open');
            $("#mobileMenuOverlay").removeClass('active');
            $("body").removeClass('mobile-menu-body-lock');
        }
    });

});
