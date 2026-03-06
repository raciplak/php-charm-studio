/**
 * Quick View - Product Image Zoom Modal
 */
(function($) {
    "use strict";

    var currentIndex = 0;
    var photos = [];
    var isOpening = false;
    var $overlay, $modal;

    // Build modal HTML once
    function initModal() {
        if ($("#qvOverlay").length) return;

        var html = '<div class="qv-overlay" id="qvOverlay">' +
            '<div class="qv-modal">' +
                '<button class="qv-close" id="qvClose">&times;</button>' +
                '<div class="qv-image-section">' +
                    '<div class="qv-main-image-wrapper">' +
                        '<button class="qv-nav-arrow qv-nav-prev" id="qvPrev"><i class="fa fa-chevron-left"></i></button>' +
                        '<img class="qv-main-image" id="qvMainImg" src="" alt="">' +
                        '<button class="qv-nav-arrow qv-nav-next" id="qvNext"><i class="fa fa-chevron-right"></i></button>' +
                        '<span class="qv-photo-counter" id="qvCounter"></span>' +
                    '</div>' +
                    '<div class="qv-thumbnails" id="qvThumbs"></div>' +
                '</div>' +
                '<div class="qv-info-section" id="qvInfo">' +
                    '<div class="qv-loading"><i class="fa fa-spinner"></i></div>' +
                '</div>' +
            '</div>' +
        '</div>';

        $("body").append(html);
        $overlay = $("#qvOverlay");
        $modal = $overlay.find(".qv-modal");

        // Events
        $("#qvClose").on("click", closeModal);
        $overlay.on("click", function(e) {
            if (isOpening) return;
            if ($(e.target).is($overlay)) closeModal();
        });
        $("#qvPrev").on("click", function() { navigate(-1); });
        $("#qvNext").on("click", function() { navigate(1); });

        // Keyboard
        $(document).on("keydown", function(e) {
            if (!$overlay.hasClass("active")) return;
            if (e.keyCode === 27) closeModal();
            if (e.keyCode === 37) navigate(-1);
            if (e.keyCode === 39) navigate(1);
        });

        // Thumbnail clicks
        $("#qvThumbs").on("click", ".qv-thumb", function() {
            goToSlide($(this).data("index"));
        });
    }

    function openModal(productId) {
        initModal();
        currentIndex = 0;
        photos = [];
        isOpening = true;

        // Show loading
        $("#qvInfo").html('<div class="qv-loading"><i class="fa fa-spinner"></i></div>');
        $("#qvMainImg").attr("src", "");
        $("#qvThumbs").empty();
        $("#qvCounter").text("");

        $overlay.addClass("active");
        $("body").css("overflow", "hidden");

        // Prevent immediate close from event bubbling
        setTimeout(function() { isOpening = false; }, 300);

        // Fetch product data
        $.getJSON("quick-view-ajax.php", { id: productId }, function(data) {
            if (data.error) {
                closeModal();
                return;
            }

            photos = data.photos || [];

            // Set main image
            if (photos.length > 0) {
                $("#qvMainImg").attr("src", photos[0]).attr("alt", data.name);
            }

            // Build thumbnails
            var thumbsHtml = "";
            for (var i = 0; i < photos.length; i++) {
                thumbsHtml += '<div class="qv-thumb' + (i === 0 ? ' active' : '') + '" data-index="' + i + '">' +
                    '<img src="' + photos[i] + '" alt="Foto ' + (i + 1) + '">' +
                '</div>';
            }
            $("#qvThumbs").html(thumbsHtml);

            // Hide thumbnails if only 1 photo
            if (photos.length <= 1) {
                $("#qvThumbs").hide();
            } else {
                $("#qvThumbs").show();
            }

            // Update arrows & counter
            updateNavigation();

            // Build info section
            var infoHtml = '<h3 class="qv-product-name">' + escapeHtml(data.name) + '</h3>' +
                '<div class="qv-price">' + data.currency + data.current_price + '</div>';

            if (data.old_price && data.old_price !== '' && parseFloat(data.old_price) > 0) {
                infoHtml += '<div class="qv-old-price">' + data.currency + data.old_price + '</div>';
            }

            if (data.short_desc) {
                infoHtml += '<p class="qv-description">' + data.short_desc + '</p>';
            }

            infoHtml += '<a href="' + data.url + '" class="qv-detail-link"><i class="fa fa-eye"></i> Ürün Detayı</a>';

            $("#qvInfo").html(infoHtml);

        }).fail(function() {
            closeModal();
        });
    }

    function closeModal() {
        $overlay.removeClass("active");
        $("body").css("overflow", "");
    }

    function navigate(dir) {
        var newIndex = currentIndex + dir;
        if (newIndex < 0 || newIndex >= photos.length) return;
        goToSlide(newIndex);
    }

    function goToSlide(index) {
        currentIndex = index;
        $("#qvMainImg").css("opacity", 0.3);
        setTimeout(function() {
            $("#qvMainImg").attr("src", photos[currentIndex]).css("opacity", 1);
        }, 150);

        // Update active thumb
        $(".qv-thumb").removeClass("active");
        $(".qv-thumb[data-index='" + currentIndex + "']").addClass("active");

        // Scroll thumbnail into view
        var $activeThumb = $(".qv-thumb.active");
        if ($activeThumb.length) {
            var $container = $("#qvThumbs");
            var scrollLeft = $activeThumb[0].offsetLeft - $container.width() / 2 + $activeThumb.width() / 2;
            $container.animate({ scrollLeft: scrollLeft }, 200);
        }

        updateNavigation();
    }

    function updateNavigation() {
        // Arrows
        $("#qvPrev").toggleClass("hidden", currentIndex === 0);
        $("#qvNext").toggleClass("hidden", currentIndex >= photos.length - 1);

        // Counter
        if (photos.length > 1) {
            $("#qvCounter").text((currentIndex + 1) + " / " + photos.length);
        } else {
            $("#qvCounter").text("");
        }
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    // Delegate click on quick-view buttons
    $(document).on("click", ".quick-view-btn", function(e) {
        e.preventDefault();
        e.stopPropagation();
        var pid = $(this).data("product-id");
        if (pid) openModal(pid);
    });

})(jQuery);
