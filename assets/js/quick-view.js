/**
 * Quick View - Product Image Zoom Modal
 */
(function($) {
    "use strict";

    var currentIndex = 0;
    var photos = [];
    var canClose = false;
    var $overlay, $modal;

    function initModal() {
        if ($("#qvOverlay").length) return;

        var html = '<div class="qv-overlay" id="qvOverlay">' +
            '<div class="qv-modal" id="qvModal">' +
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
        $modal = $("#qvModal");

        // Close button
        $("#qvClose").on("click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log("[QV] Close button clicked");
            closeModal();
        });

        // Navigation
        $("#qvPrev").on("click", function(e) { e.stopPropagation(); navigate(-1); });
        $("#qvNext").on("click", function(e) { e.stopPropagation(); navigate(1); });

        // Prevent ALL events inside modal from reaching overlay
        $modal.on("click mousedown mouseup touchstart touchend", function(e) {
            e.stopPropagation();
        });

        // Overlay background click to close - only if canClose is true
        $overlay.on("click", function(e) {
            console.log("[QV] Overlay click, target:", e.target.className, "canClose:", canClose);
            if (!canClose) return;
            if (e.target === $overlay[0]) {
                console.log("[QV] Closing from overlay click");
                closeModal();
            }
        });

        // Keyboard
        $(document).on("keydown", function(e) {
            if (!$overlay || !$overlay.hasClass("active")) return;
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
        console.log("[QV] openModal called, productId:", productId);
        initModal();
        currentIndex = 0;
        photos = [];
        canClose = false;

        $("#qvInfo").html('<div class="qv-loading"><i class="fa fa-spinner"></i></div>');
        $("#qvMainImg").attr("src", "");
        $("#qvThumbs").empty();
        $("#qvCounter").text("");

        // Show modal
        $overlay.addClass("active");
        $("body").css("overflow", "hidden");
        console.log("[QV] Modal opened, overlay active:", $overlay.hasClass("active"));

        // Only allow closing after 1 second
        setTimeout(function() {
            canClose = true;
            console.log("[QV] canClose set to true");
        }, 1000);

        // Fetch product data
        $.ajax({
            url: "quick-view-ajax.php",
            data: { id: productId },
            dataType: "json",
            success: function(data) {
                console.log("[QV] AJAX success:", data);
                if (data.error) {
                    console.log("[QV] Data has error, closing");
                    closeModal();
                    return;
                }

                photos = data.photos || [];

                if (photos.length > 0) {
                    $("#qvMainImg").attr("src", photos[0]).attr("alt", data.name);
                }

                var thumbsHtml = "";
                for (var i = 0; i < photos.length; i++) {
                    thumbsHtml += '<div class="qv-thumb' + (i === 0 ? ' active' : '') + '" data-index="' + i + '">' +
                        '<img src="' + photos[i] + '" alt="Foto ' + (i + 1) + '">' +
                    '</div>';
                }
                $("#qvThumbs").html(thumbsHtml);

                if (photos.length <= 1) {
                    $("#qvThumbs").hide();
                } else {
                    $("#qvThumbs").show();
                }

                updateNavigation();

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
            },
            error: function(xhr, status, err) {
                console.log("[QV] AJAX FAILED:", status, err, xhr.status, xhr.responseText && xhr.responseText.substring(0, 200));
                // Do NOT close modal on AJAX fail - show error message instead
                $("#qvInfo").html('<div style="padding:30px;text-align:center;color:#999;"><i class="fa fa-exclamation-circle" style="font-size:40px;"></i><p style="margin-top:15px;">Ürün bilgisi yüklenemedi.</p></div>');
            }
        });
    }

    function closeModal() {
        console.log("[QV] closeModal called, trace:", new Error().stack);
        if ($overlay) {
            $overlay.removeClass("active");
            $("body").css("overflow", "");
        }
        canClose = false;
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

        $(".qv-thumb").removeClass("active");
        $(".qv-thumb[data-index='" + currentIndex + "']").addClass("active");

        var $activeThumb = $(".qv-thumb.active");
        if ($activeThumb.length) {
            var $container = $("#qvThumbs");
            var scrollLeft = $activeThumb[0].offsetLeft - $container.width() / 2 + $activeThumb.width() / 2;
            $container.animate({ scrollLeft: scrollLeft }, 200);
        }

        updateNavigation();
    }

    function updateNavigation() {
        $("#qvPrev").toggleClass("hidden", currentIndex === 0);
        $("#qvNext").toggleClass("hidden", currentIndex >= photos.length - 1);

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

    // Quick view button handler
    $(document).on("click", ".quick-view-btn", function(e) {
        console.log("[QV] Button clicked");
        e.preventDefault();
        e.stopPropagation();
        var pid = $(this).data("product-id");
        if (pid) openModal(pid);
        return false;
    });

})(jQuery);
