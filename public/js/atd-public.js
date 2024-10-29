(function ($) {
    'use strict';
    $(document).ready(function () {

        $(".single_variation_wrap").on("show_variation", function (event, variation) {
            // Fired when the user selects all the required dropdowns / attributes
            // and a final variation is selected / shown
            var variation_id = $("input[name='variation_id']").val();
            if (variation_id)
            {
                $(".atd-buttons-wrap-variation").hide();
                $(".atd-buttons-wrap-variation[data-id='" + variation_id + "']").show();

                if (typeof hide_cart_button !== 'undefined') {
                    if ($(".atd-buttons-wrap-variation[data-id='" + variation_id + "']").length > 0 && hide_cart_button === 1) {
                        $(".atd-buttons-wrap-variation").parent().find('.add_to_cart_button').hide();
                        $(".atd-buttons-wrap-variation").parent().find('.single_add_to_cart_button').hide();
                    } else {
                        $(".atd-buttons-wrap-variation").parent().find('.add_to_cart_button').show();
                        $(".atd-buttons-wrap-variation").parent().find('.single_add_to_cart_button').show();
                    }
                }

            }
        });

        $(".single_variation_wrap").on("hide_variation", function (event, variation) {
            $(".atd-buttons-wrap-variation").hide();
        });


        $(".atd-customize-product, .atd-buttons-wrap-variation .btn-choose").on("click", function (event) {
            // Fired when the user selects all the required dropdowns / attributes
            event.preventDefault();
            var link = $(this).attr("href");
            var quantity = $(".input-text").val();

            if ($('.variations_form').length > 0) {
                var attributes = atd_retrieve_selected_attributes();
                var variation_id = $("input[name='variation_id']").val();
                attributes.product_id = variation_id;
                $.post(
                        ajax_object.ajax_url,
                        {
                            action: "atd_store_variation_attributes",
                            data: attributes
                    }, function ( result ) {
                            if (typeof 'undefined' !== result && '' !== result) {
                                var request_data = JSON.parse(result);
                                link = request_data['url'];
                                if ('undefined' !== typeof quantity) {
                                    if (quantity.length > 0) {
                                        if (link.indexOf('?') > -1) {
                                            link += "&custom_qty=" + quantity;
                                        } else {
                                            link += "?custom_qty=" + quantity;
                                        }
                                    }
                                }
                                window.location.href = link;
                            }
                        }
                );
            } else {
                window.location.href = link;
            }
        });


        /**
         * Get chosen attributes from form.
         * @return array
         */
        function atd_retrieve_selected_attributes() {
            var data = {};
            var count = 0;
            var chosen = 0;

            $('.variations_form').find('.variations select').each(function () {
                var attribute_name = $(this).data('attribute_name') || $(this).attr('name');
                var value = $(this).val() || '';

                if (value.length > 0) {
                    chosen++;
                }

                count++;
                data[ attribute_name ] = value;
            });

            return {
                'data': data
            };
        }

        /** Begin Preview Cart Design Script */

   $(".atd-prev-cart-des").click(function() {

    var targetPartName = $(this).attr("data-part-name");
    var targetVariationId = $(this).attr("data-variation-id");

    var $targetTabContent = $(
      ".atd-preview-box-prev-cart-des[data-part-name='" + targetPartName + "'][data-variation-id='" + targetVariationId + "']"
    );

    var $targetShadow = $(
    ".atd-shadow-prev-cart-des[data-part-name='" + targetPartName + "'][data-variation-id='" + targetVariationId + "']"
    );

    $targetShadow.addClass("atd-show");

    $targetTabContent.addClass("atd-show");

    $("body").css("overflow", "hidden");

      // $(".atd-shadow-prev-cart-des, .atd-preview-box-prev-cart-des").addClass("atd-show");

  });

  $(".atd-shadow-prev-cart-des").click(function () {
    $(this).removeClass("atd-show");

    $(".atd-preview-box-prev-cart-des").removeClass("atd-show");

    $("body").css("overflow", "scroll");
  });

  $(".atd-icon-prev-cart-cross").click(function (e) {
    $(".atd-shadow-prev-cart-des").removeClass("atd-show");

    $(this).closest(".atd-preview-box-prev-cart-des").removeClass("atd-show");

    $("body").css("overflow", "scroll");
  });

  /** End Preview Cart Design Script */
   
    });

})(jQuery);
