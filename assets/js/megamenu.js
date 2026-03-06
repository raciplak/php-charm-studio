/*global $ */
$(document).ready(function () {

    "use strict";

    $('.menu > ul > li:has( > ul)').addClass('menu-dropdown-icon');
    $('.menu > ul > li > ul:not(:has(ul))').addClass('normal-sub');

    // Mark all nested items that have sub-menus
    $('.menu ul li:has( > ul)').addClass('has-sub');

    // Desktop: hover dropdowns (all levels)
    $(".menu ul li").hover(
        function (e) {
            if ($(window).width() > 959) {
                $(this).children("ul").stop(true, true).fadeIn(150);
            }
        }, function (e) {
            if ($(window).width() > 959) {
                $(this).children("ul").stop(true, true).fadeOut(150);
            }
        }
    );

    // Mobile: click to toggle sub-menus (ALL levels, not just first)
    $(".menu").on("click", "li", function(e) {
        if ($(window).width() <= 959) {
            var $sub = $(this).children("ul");
            if ($sub.length) {
                e.preventDefault();
                e.stopPropagation();
                // Close siblings at the same level
                $(this).siblings().removeClass('open').children("ul").slideUp(200);
                // Toggle current
                $(this).toggleClass('open');
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
