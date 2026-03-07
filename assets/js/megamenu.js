/*global $ */
$(document).ready(function () {

    "use strict";

    $('.menu > ul > li:has( > ul)').addClass('menu-dropdown-icon');
    $('.menu > ul > li > ul:not(:has(ul))').addClass('normal-sub');

    // Mark ALL items that have sub-menus (all levels)
    $('.menu ul li:has( > ul)').addClass('has-sub');

    // Desktop: hover dropdowns (all levels)
    $(".menu ul li").hover(
        function () {
            if ($(window).width() > 959) {
                $(this).children("ul").stop(true, true).fadeIn(150);
            }
        }, function () {
            if ($(window).width() > 959) {
                $(this).children("ul").stop(true, true).fadeOut(150);
            }
        }
    );

    // Store original parent for menu so we can move it back
    var $menu = $(".menu");
    var $overlay = $(".mobile-menu-overlay");
    var $menuOriginalParent = $menu.parent();
    var $overlayOriginalParent = $overlay.parent();
    var movedToBody = false;

    function moveMenuToBody() {
        if (!movedToBody && $(window).width() <= 959) {
            $menu.appendTo("body");
            $overlay.appendTo("body");
            movedToBody = true;
        }
    }

    function moveMenuBack() {
        if (movedToBody) {
            $menuOriginalParent.append($menu);
            $overlayOriginalParent.append($overlay);
            movedToBody = false;
        }
    }

    // Mobile: click to toggle sub-menus (ALL levels)
    $("body").on("click", ".menu li.has-sub", function(e) {
        if ($(window).width() <= 959) {
            e.preventDefault();
            e.stopPropagation();

            var $sub = $(this).children("ul");

            // Close siblings at the same level
            $(this).siblings(".has-sub").removeClass('open').children("ul").slideUp(200);
            $(this).siblings(".has-sub").find('.has-sub').removeClass('open').children("ul").slideUp(200);

            // Toggle current
            if ($(this).hasClass('open')) {
                $(this).removeClass('open');
                $(this).find('.has-sub').removeClass('open').children("ul").slideUp(200);
                $sub.slideUp(250);
            } else {
                $(this).addClass('open');
                $sub.slideDown(250);
            }
        }
    });

    // Mobile: clicking a LEAF item (no sub-menu) — navigate normally
    $("body").on("click", ".menu li:not(.has-sub)", function(e) {
        if ($(window).width() <= 959) {
            e.stopPropagation();
        }
    });

    // Hamburger button toggle (both desktop nav button and mobile header bar button)
    $("#hamburgerBtn, #mobileHamburgerTrigger").click(function (e) {
        e.preventDefault();
        moveMenuToBody();
        // Sync both buttons
        $("#hamburgerBtn").toggleClass('active');
        $menu.toggleClass('mobile-open');
        $overlay.toggleClass('active');
        $("body").toggleClass('mobile-menu-body-lock');
    });

    // Close menu on overlay click
    $("body").on("click", ".mobile-menu-overlay.active", function () {
        $("#hamburgerBtn").removeClass('active');
        $menu.removeClass('mobile-open');
        $(this).removeClass('active');
        $("body").removeClass('mobile-menu-body-lock');
    });

    // Close menu on window resize to desktop
    $(window).resize(function () {
        if ($(window).width() > 959) {
            $("#hamburgerBtn").removeClass('active');
            $menu.removeClass('mobile-open');
            $overlay.removeClass('active');
            $("body").removeClass('mobile-menu-body-lock');
            moveMenuBack();
        }
    });

});
