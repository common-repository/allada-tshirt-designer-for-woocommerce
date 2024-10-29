/* global atd, Storage, to_load_on_add_product, ajax_object, to_load, accounting, atd_ninja_form_validation, wc_cart_fragments_params, _ */
var atd_EDITOR = (function ($, atd_editor) {
  "use strict";
  var atd_editor = {};
  atd_editor.canvas = [];
  atd_editor.serialized_parts = {};
  atd_editor.indexed_serialized_parts = {};
  atd_editor.final_canvas_parts = {};
  atd_editor.selected_part = 0;
  atd_editor.canvasManipulationsPosition = [];
  atd_editor.box_center_x = false;
  atd_editor.box_center_y = false;
  atd_editor.scale_factor = false;
  atd_editor.arr_filters = [
    "grayscale",
    "invert",
    "remove-white",
    "sepia",
    "sepia2",
    "brightness",
    "noise",
    "gradient-transparency",
    "pixelate",
    "blur",
    "convolute",
  ];
  $(document).ready(function () {
    var resizeId;
    //var tools_accordion = new Spry.Widget.Accordion("atd-tools-box-container", {useFixedPanelHeights: false, defaultPanel: -1});
    //new Spry.Widget.Accordion("my-designs-accordion", {useFixedPanelHeights: false, defaultPanel: -1});
    //$("[data-tooltip-title]").otooltip();
    init_canvas();
    init_empty_canvas_data_array();

    atd_editor.isCanvasBlank = function () {
      var isCanvasBlank = 0;
      $.each(atd_editor.canvas, function (key, canvas) {
        if (
          !canvas
            .getContext("2d")
            .getImageData(0, 0, canvas.width, canvas.height)
            .data.some((channel) => channel !== 0)
        ) {
          isCanvasBlank++;
        }
      });
      if (isCanvasBlank === atd_editor.canvas.length) {
        return true;
      } else {
        return false;
      }
    };

    window.addEventListener("beforeunload", function (e) {
      if (
        typeof atd.is_beforeunload !== "undefined" &&
        !atd.is_beforeunload &&
        !atd_editor.isCanvasBlank()
      ) {
        atd_editor.save_canvas();
        //console.log(atd_editor.isCanvasBlank());
        if (typeof Storage !== "undefined") {
          var atd_save_canvas_status = {};
          if (
            !atd_editor.isEmpty(localStorage.getItem("atd_save_canvas_status"))
          ) {
            atd_save_canvas_status = JSON.parse(
              localStorage.getItem("atd_save_canvas_status")
            );
          }
          atd_save_canvas_status[atd.product_id] = JSON.stringify(
            atd_editor.serialized_parts
          );
          localStorage.setItem(
            "atd_save_canvas_status",
            JSON.stringify(atd_save_canvas_status)
          );
        }
        var confirmationMessage = "Changes you have made may not be saved.";
        (e || window.event).returnValue = confirmationMessage;
        return confirmationMessage;
      }
    });

    function save_canvas_status(data) {
      if (typeof data === "object") {
        var part_index = 0;
        $.each(data, function (index, value) {
          $.each(value, function (index1, value1) {
            atd_editor.serialized_parts[index] = [];
            atd_editor.canvasManipulationsPosition[index] = 0;
            var json_value = value1;
            atd_editor.serialized_parts[index].push(json_value);
            atd_editor.selected_part = 0;
            atd_editor.canvas[part_index].loadFromJSON(json_value, function () {
              if (typeof atd_editor.canvas[part_index] !== "undefined")
                atd_editor.canvas[part_index].renderAll.bind(
                  atd_editor.canvas[part_index]
                );
              rescale_canvas_if_needed();
            });
            atd_editor.canvas[part_index].calcOffset();
            load_first_part_img();

            if (json_value.indexOf('{"type":"text"') > -1) {
              fabric_text_to_itext();
            }
            load_background_overlay_if_needed(part_index);
          });
          part_index++;
        });
      }
      setTimeout(function () {
        atd_editor.canvas[atd_editor.selected_part].renderAll();
        $("#atd-parts-bar > li").first().click();
      }, 500);
    }

    $(".atd-responsive-mode .atd-editor-menu-right").toggle(
      function () {
        $(".atd-responsive-mode .atd-editor-col.right").css(
          "display",
          "inline-block"
        );
      },
      function () {
        $(".atd-responsive-mode .atd-editor-col.right").css("display", "none");
      }
    );

    function preload_canvas_on_change_product(data) {
      if (typeof data === "object") {
        $.each(data, function (index, value) {
          $.each(value, function (index1, value1) {
            if (index1 === "json") {
              atd_editor.serialized_parts[index] = [];
              atd_editor.canvasManipulationsPosition[index] = 0;
              var json_value = value1;
              atd_editor.serialized_parts[index].push(json_value);
              atd_editor.selected_part = index;
              atd_editor.canvas[index].loadFromJSON(json_value, function () {
                atd_editor.canvas[index].renderAll.bind(
                  atd_editor.canvas[index]
                );
                rescale_canvas_if_needed();
              });
              atd_editor.canvas[index].calcOffset();
              load_background_overlay_if_needed(index);
              if (json_value.indexOf('{"type":"text"') > -1) {
                fabric_text_to_itext();
              }
            }
          });
        });
        $("#atd-parts-bar > li").first().click();
        var frm_data = new FormData();
        frm_data.append("action", "atd_close_cookies");
        $.ajax({
          type: "POST",
          url: ajax_object.ajax_url,
          data: frm_data,
          processData: false,
          contentType: false,
        }).done(function () {});
      }
      setTimeout(function () {
        $.each(atd_editor.canvas, function (key, canvas) {
          canvas.renderAll();
        });
      }, 500);
    }

    $("#add-products").click(function (e) {
      e.preventDefault();
      atd.is_beforeunload = true;
      var answer = confirm(
        "You are about to be redirected to the store to add a product. The current state of your design will be saved.\n Are you sure you want to add some products to this design?"
      );
      if (answer) {
        var link = $(this).attr("data-href");
        var customization_link = $(location).attr("href");
        atd_editor.save_canvas();
        $("#atd-parts-bar > li").each(function () {
          var index = $(this).index();
          var json = JSON.stringify(
            atd_editor.canvas[index].toJSON([
              "lockMovementX",
              "lockMovementY",
              "lockRotation",
              "lockScalingX",
              "lockScalingY",
              "price",
              "lockDeletion",
              "originalText",
              "radius",
              "spacing",
            ])
          );
          if (typeof atd_editor.indexed_serialized_parts[index] === "undefined")
            atd_editor.indexed_serialized_parts[index] = {};
          atd_editor.indexed_serialized_parts[index]["json"] = json;
        });
        var frm_data = new FormData();
        frm_data.append("action", "add_product");
        frm_data.append("customization_page_link", customization_link);
        frm_data.append("current_product_id", atd.product_id);
        frm_data.append(
          "indexed_serialized_parts",
          JSON.stringify(atd_editor.indexed_serialized_parts)
        );
        $.ajax({
          type: "POST",
          url: ajax_object.ajax_url,
          data: frm_data,
          processData: false,
          contentType: false,
        }).done(function () {
          window.location.href = link;
        });
      }
    });

    $("#change-product").click(function (e) {
      e.preventDefault();
      atd.is_beforeunload = true;
      var answer = confirm(
        "You are about to be redirected to the store to change a product. The current state of your design will be saved.\n Are you sure you want to change this product?"
      );
      if (answer) {
        var link = $(this).attr("data-href");
        atd_editor.save_canvas();
        $("#atd-parts-bar > li").each(function () {
          var index = $(this).index();
          var json = JSON.stringify(
            atd_editor.canvas[index].toJSON([
              "lockMovementX",
              "lockMovementY",
              "lockRotation",
              "lockScalingX",
              "lockScalingY",
              "price",
              "lockDeletion",
              "originalText",
              "radius",
              "spacing",
            ])
          );
          if (typeof atd_editor.indexed_serialized_parts[index] === "undefined")
            atd_editor.indexed_serialized_parts[index] = {};
          atd_editor.indexed_serialized_parts[index]["json"] = json;
        });
        var frm_data = new FormData();
        frm_data.append("action", "change_product");
        frm_data.append("current_product_id", atd.product_id);
        frm_data.append(
          "indexed_serialized_parts",
          JSON.stringify(atd_editor.indexed_serialized_parts)
        );
        $.ajax({
          type: "POST",
          url: ajax_object.ajax_url,
          data: frm_data,
          processData: false,
          contentType: false,
        }).done(function () {
          window.location.href = link;
        });
      }
    });

    atd_editor.isEmpty = function (value) {
      return (
        (typeof value == "string" && !value.trim()) ||
        typeof value == "undefined" ||
        value === null
      );
    };

    /*atd_editor.rgb2hex = function(rgb) {
            //rgb = rgb.match(/^rgb((d+),s*(d+),s*(d+))$/);
            rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
            var hex;
            if (!atd_editor.isEmpty(rgb)) {
                hex = "#" + atd_editor.hex(rgb[1]) + atd_editor.hex(rgb[2]) + atd_editor.hex(rgb[3]);
            } else {
                hex = "#4f71b9";
            }
            return hex;
        };
        
        atd_editor.hex = function(x) {
            var hexDigits = new Array
        ("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f"); 
            return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
        };*/

    function get_optimal_canvas_dimensions() {
      var porto_header_height = $("#header-wrap").outerHeight();
      var header_height = $("header").outerHeight();
      var wpadminbar = $("#wpadminbar").outerHeight();
      if (wpadminbar !== null && wpadminbar !== "undefined") {
        wpadminbar = wpadminbar + 21;
      } else {
        wpadminbar = 0;
      }
      if (porto_header_height === null && porto_header_height === "undefined") {
        porto_header_height = header_height;
      }
      var recup_h =
        $(window).outerHeight() - porto_header_height - wpadminbar - 74;
      var recup_w = $(".atd-container").outerWidth();
      var data_id = $(
        "#atd-parts-bar > li:eq(" + atd_editor.selected_part + ")"
      ).attr("data-id");
      return atd_editor.get_dimensions_with_space_available(
        recup_h,
        recup_w,
        data_id,
        1
      );
    }

    if (typeof to_load !== "undefined") {
      setTimeout(function () {
        preload_canvas(to_load);
      }, 500);
    } else if (typeof to_load_on_add_product !== "undefined") {
      setTimeout(function () {
        preload_canvas_on_change_product(to_load_on_add_product);
      }, 500);
    } else if (
      typeof Storage !== "undefined" &&
      localStorage.getItem("atd_save_canvas_status") !== "" &&
      typeof to_load_on_add_product === "undefined"
    ) {
      var atd_save_canvas_status = JSON.parse(
        localStorage.getItem("atd_save_canvas_status")
      );
      if (!atd_editor.isEmpty(atd_save_canvas_status)) {
        if (
          atd_save_canvas_status[atd.product_id] !== "" &&
          typeof atd_save_canvas_status[atd.product_id] !== "undefined"
        ) {
          var confirmation = confirm(
            "Do you want to reload your previous design?"
          );
          if (confirmation) {
            setTimeout(function () {
              save_canvas_status(
                JSON.parse(atd_save_canvas_status[atd.product_id])
              );
            }, 500);
          } else {
            $("#atd-parts-bar > li").each(function (key) {
              var data_id = $(this).attr("data-id");
              atd_editor.serialized_parts[data_id] = [];
              atd_editor.canvasManipulationsPosition[data_id] = -1;
              var nb_parts = $("#atd-parts-bar > li").length;
              if (key === nb_parts - 1) {
                loop_through_parts(
                  atd.output_loop_delay,
                  click_on_part,
                  function () {
                    $("#atd-parts-bar > li").first().click();
                    atd_editor.canvas[atd_editor.selected_part].renderAll();
                    rescale_canvas_if_needed();
                    $.unblockUI();
                  }
                );
              }
            });
          }
        }
      }
    }

    function preload_canvas(data) {
      if (typeof data === "object") {
        var part_index = 0;
        $.each(data, function (index, value) {
          $.each(value, function (index1, value1) {
            if (index1 === "json") {
              atd_editor.serialized_parts[index] = [];
              atd_editor.canvasManipulationsPosition[index] = 0;
              var json_value = value1;
              atd_editor.serialized_parts[index].push(json_value);
              atd_editor.selected_part = 0;
              atd_editor.canvas[part_index].loadFromJSON(
                json_value,
                function () {
                  if (typeof atd_editor.canvas[part_index] !== "undefined")
                    atd_editor.canvas[part_index].renderAll.bind(
                      atd_editor.canvas[part_index]
                    );
                  rescale_canvas_if_needed();
                }
              );
              atd_editor.canvas[part_index].calcOffset();
              load_first_part_img();

              if (json_value.indexOf('{"type":"text"') > -1) {
                fabric_text_to_itext();
              }
              load_background_overlay_if_needed(part_index);
            }
          });
          part_index++;
        });
      }
      setTimeout(function () {
        atd_editor.canvas[atd_editor.selected_part].renderAll();
      }, 500);
    }

    function update_price() {
      var nb_parts = $("#atd-parts-bar > li").length;
      var variations = {};
      var tpl = atd.query_vars["tpl"];
      if (typeof tpl === "undefined") tpl = "";

      $.each($(".atd-qty-container"), function (key, curr_object) {
        var qty = $(this).find(".atd-qty").val();
        variations[$(this).data("id")] = qty;
      });

      $(".atd-preview-box-quantity-inner").each(function () {
        var vars = {};
        $(this).find(".atd-input-number").each(function(){
          var qty = $(this).val();
          var variation_id = $(this).attr("name");
          variation_id = variation_id.split("variation_qty_");
          variation_id = parseInt(variation_id[1]);
          if(!isNaN(qty)) vars[variation_id] = qty;
        })
        variations = vars
      });

      var parts_json = {};
      $.each($("#atd-parts-bar > li"), function (key, curr_object) {
        var data_id = $(this).attr("data-id");
        if (atd_editor.serialized_parts[data_id]) {
          var x = {};
          x["json"] =
            atd_editor.serialized_parts[data_id][
              atd_editor.canvasManipulationsPosition[data_id]
            ];

          parts_json[data_id] = x;
        }

        var variation_id = atd.global_variation_id;
        var tthat = $("form.formbuilt");
        if (tthat.length !== 0) {
          var form_fields = tthat.serializeJSON();
          delete form_fields.id_ofb;
          parts_json["form_fields"] = form_fields;
        }
        parts_json["variation_id"] = variation_id;

        if ($(".atd-checkbox-add-name").is(":checked"))
          parts_json["atd_team_add_name"] = "yes";
        else parts_json["atd_team_add_name"] = "no";

        if ($(".atd-checkbox-add-number").is(":checked"))
          parts_json["atd_team_add_number"] = "yes";
        else parts_json["atd_team_add_number"] = "no";

        if ($(this).index() === nb_parts - 1) {
          $.post(
            ajax_object.ajax_url,
            {
              action: "get_design_price",
              variations: variations,
              serialized_parts: JSON.stringify(parts_json),
              tpl: tpl,
            },
            function (data) {
              if (atd_editor.is_json(data)) {
                var response = JSON.parse(data);
                var tmp_price = 0;
                $.each($(".atd-qty-container"), function (key, curr_object) {
                  var variation_id = $(this).data("id");
                  var price = response.prices[variation_id];
                  var qty = $(this).find(".atd-qty");
                  $(this)
                    .find(".total_order")
                    .html(accounting.formatMoney(price));
                  qty.attr("uprice", price);
                  qty.trigger("change");
                });

                $(".atd-preview-box-quantity-inner").each(function () {
                  $(this).find(".atd-input-number").each(function(){
                    var qty = $(this).val();
                    var variation_id = $(this).attr("name");
                    variation_id = variation_id.split("variation_qty_");
                    variation_id = parseInt(variation_id[1]);
                    var price = response.prices[variation_id];

                    if(!isNaN(qty)){
                      var tPrice = price * qty;
                      tmp_price += tPrice;
                    }
                  })
                });
                $(".atd-ui-price").html(accounting.formatMoney(tmp_price));
              } else $("#debug").html(data);
            }
          );
        }
      });
    }

    function load_first_part_img() {
      var bg_included = $("#atd-parts-bar > li").first().attr("data-url");
      var bg_code = "url('" + bg_included + "') no-repeat center center";
      $("#atd-editor-container .canvas-container").css("background", bg_code);
    }

    $(document).on("click", "#atd-parts-bar > li", function (e) {
      var part_index = $(this).index();
      var data_id = $(this).attr("data-id");
      $("#atd-parts-bar > li").each(function (key) {
        if (key !== part_index) {
          $("#atd-editor-container .canvas-container:eq(" + key + ")").css(
            "display",
            "none"
          );
        } else {
          $("#atd-editor-container .canvas-container:eq(" + key + ")").css(
            "display",
            "block"
          );
        }
      });
      load_background_overlay_if_needed(part_index);
      if (atd_editor.selected_part === $(this).index()) {
        return;
      } else {
        $("#atd-parts-bar > li").removeClass("active");
        $(this).addClass("active");
        if (atd_editor.selected_part >= 0) {
          atd_editor.save_canvas();
        }
        atd_editor.selected_part = $(this).index();
      }
      if (typeof atd_editor.serialized_parts[data_id] === "undefined") {
        atd_editor.serialized_parts[data_id] = [];
        atd_editor.canvasManipulationsPosition[data_id] = -1;
      }
      atd_editor.refresh_undo_redo_status();
    });

    function applyImageFilters() {
      atd_editor.canvas[atd_editor.selected_part].forEachObject(function (obj) {
        if (obj.type === "image" && obj.filters.length) {
          obj.applyFilters(function () {
            obj.canvas.renderAll();
          });
        }
      });
    }

    atd_editor.get_dimensions_with_space_available = function (
      recup_h,
      recup_w,
      data_id,
      screen = 1
    ) {
      var canvas_d = atd.canvas[data_id];
      var t_shirt_h = recup_h;
      var t_shirt_w = recup_w;
      if (screen) {
      }
      // var t_shirt_w = recup_w - 620;

      if (t_shirt_h > t_shirt_w) var ratio = t_shirt_w / t_shirt_h;
      else var ratio = t_shirt_h / t_shirt_w;

      t_shirt_h = atd.canvas[data_id].t_shirt_height * ratio;
      t_shirt_w = atd.canvas[data_id].t_shirt_width * ratio;
      var canvas_top = atd.canvas[data_id].canvas_top * ratio;
      var canvas_left = atd.canvas[data_id].canvas_left * ratio;
      var canvas_height = atd.canvas[data_id].canvas_height * ratio;
      var canvas_width = atd.canvas[data_id].canvas_width * ratio;

      return [
        t_shirt_h,
        t_shirt_w,
        canvas_top,
        canvas_left,
        canvas_height,
        canvas_width,
      ];
    };

    function init_canvas() {
      //We determine the best dimensions to use
      $("#atd-parts-bar > li").each(function (key) {
        var data_id = $(this).attr("data-id");
        var canvas_d = atd.canvas[data_id];
        atd_editor.canvas.push(
          new fabric.Canvas("atd-editor-" + data_id, {
            width: canvas_d.canvas_width,
            height: canvas_d.canvas_height,
          })
        );
        load_canvas_listeners(key);
      });
      atd_editor.canvas[
        atd_editor.selected_part
      ].backgroundImageStretch = false;
      atd_editor.canvas[atd_editor.selected_part].renderAll();
      if (typeof atd !== "undefined") {
        accounting.settings = {
          currency: {
            symbol: atd.currency, // default currency symbol is '$'
            format: atd.price_format, // controls output: %s = symbol, %v = value/number (can be object: see below)
            decimal: atd.decimal_sep, // decimal point separator
            thousand: atd.thousand_sep, // thousands separator
            precision: atd.nb_decimals, // decimal places
          },
          number: {
            precision: atd.nb_decimals, // default precision on numbers is 0
            thousand: atd.thousand_sep,
            decimal: atd.decimal_sep,
          },
        };
      }
    }

    function load_canvas_listeners(selected_part) {
      /*atd_editor.canvas[selected_part].on('object:removed', function(options) {
                console.log("toto");
            });*/
      atd_editor.canvas[selected_part].on(
        "before:selection:cleared",
        function (options) {
          var clearedObject;
          if (
            typeof atd_editor.canvas[selected_part].getActiveObject() !==
            "undefined"
          ) {
            clearedObject = atd_editor.canvas[selected_part].getActiveObject();
            if ("i-text" === clearedObject.type) {
              if ("undefined" !== typeof clearedObject.clipTeam) {
                $(
                  "#atd-icon-tooltip-alignment, #new-text, #atd-add-text"
                ).removeClass("atd-disable-btn");
                $("#copy_paste_btn")
                  .parents(".atd-icon-tooltip")
                  .removeClass("atd-disable-btn");
              }
              $(".atd-editing-text").removeClass("atd-active");
              $("#new-text").val("");
              $(".atd-font-container .atd-font-label").text(
                $(".atd-font-drop-down-item:first .atd-font-name").text()
              );
              $(".atd-text-style-option")
                .find(".atd-text-style-option-item")
                .removeClass("atd-active");
              $(".atd-text-decoration-option")
                .find(".atd-text-decoration-option-item")
                .removeClass("atd-active");
              $(".atd-text-alignement-option")
                .find(".atd-text-alignement-option-item")
                .removeClass("atd-active");
              $(
                "#txt-color-selector, #txt-outline-color-selector, #txt-bg-color-selector"
              ).val("#4f71b9");
              $("#atd-outline-size").val("0");
              $(".add-row-text-outline .atd-range-text").text("0");
              $("#font-size-selector").val("15");
              $(".atd-range-container-text-size .atd-range-text").text("15");
              $("#cb-curved").prop("checked", false);
              $("#opacity-slider").val("1");
              $(".atd-range-container-text-opacity .atd-range-text").text("1");
              $("#curved-txt-radius-slider").val("150");
              $(".atd-range-container-text-radius .atd-range-text").text("150");
              $("#curved-txt-spacing-slider").val("9");
              $(".atd-range-container-text-spacing .atd-range-text").text("9");
            } else if (
              clearedObject.type === "group" &&
              clearedObject.get("originalText")
            ) {
              $(".atd-editing-text").removeClass("atd-active");
              $("#new-text").val("");
              $(".atd-font-container .atd-font-label").text(
                $(".atd-font-drop-down-item:first .atd-font-name").text()
              );
              $(".atd-text-style-option")
                .find(".atd-text-style-option-item")
                .removeClass("atd-active");
              $(".atd-text-decoration-option")
                .find(".atd-text-decoration-option-item")
                .removeClass("atd-active");
              $(".atd-text-alignement-option")
                .find(".atd-text-alignement-option-item")
                .removeClass("atd-active");
              $(
                "#txt-color-selector, #txt-outline-color-selector, #txt-bg-color-selector"
              ).val("#4f71b9");
              //$("#atd-outline-size").val("0");
              //$(".add-row-text-outline .atd-range-text").text("0");
            } else if ("image" === clearedObject.type) {
              var img_src = clearedObject.src;
              var cliparts_elements = $(
                ".atd-preview-clippart-group .atd-preview-clippart"
              ).filter(function () {
                return (
                  $(this).css("background-image") === 'url("' + img_src + '")'
                );
              });

              var uploads_elements = $(
                ".atd-upload-inner .atd-preview-upolad"
              ).filter(function () {
                return (
                  $(this).css("background-image") === 'url("' + img_src + '")'
                );
              });

              if (0 < cliparts_elements.length) {
                $(".atd-tab-item").removeClass("active");
                $(".atd-tab-item[data-title='clippart']").addClass("active");
                $(".atd-tab-item[data-title='clippart']").trigger("click");
                $(".atd-tab-tools-content").css("display", "none");
                $(".atd-tab-tools-content[data-title='clippart']").css(
                  "display",
                  "block"
                );
                cliparts_elements
                  .closest(".atd-tab-tools-content")
                  .find(".atd-clippart-edit-inner")
                  .removeClass("atd-active");
                $(".atd-clippart-container").removeClass("atd-active");
                $(".atd-clippart-inner").addClass("atd-active");
                $(".atd-clippart-group-container").addClass("atd-active");
                return "cliparts-panel";
              } else if (0 < uploads_elements.length) {
                $(".atd-tab-item").removeClass("active");
                $(".atd-tab-item[data-title='upload']").addClass("active");
                $(".atd-tab-item[data-title='upload']").trigger("click");
                $(".atd-tab-tools-content").css("display", "none");
                $(".atd-tab-tools-content[data-title='upload']").css(
                  "display",
                  "block"
                );
                uploads_elements
                  .closest(".atd-tab-tools-content")
                  .find(".atd-upload-edit-inner")
                  .removeClass("atd-active");
                $(".atd-upload-container").removeClass("atd-active");
                $(".atd-upload-inner").addClass("atd-active");
                return "uploads-panel";
              }
            }
          } else {
            clearedObject = atd_editor.canvas[selected_part].getActiveGroup();
          }
        }
      );
      atd_editor.canvas[selected_part].on(
        "object:selected",
        function (options) {
          wp.hooks.doAction("atd_EDITOR.object_selected", options);
          if (options.target) {
            var objectType = options.target.type;
            var arr_shapes = ["rect", "circle", "triangle", "polygon", "path"];
            $(
              "#atd-icon-tooltip-alignment, #new-text, #atd-add-text"
            ).removeClass("atd-disable-btn");
            $("#copy_paste_btn")
              .parents(".atd-icon-tooltip")
              .removeClass("atd-disable-btn");
            if (objectType === "i-text") {
              if ("undefined" !== typeof options.target.clipTeam) {
                $(".atd-tab-item").removeClass("active");
                $(".atd-tab-item[data-title='team']").addClass("active");
                $(".atd-tab-item[data-title='team']").trigger("click");
                $(".atd-tab-tools-content").css("display", "none");
                $(".atd-tab-tools-content[data-title='team']").css(
                  "display",
                  "block"
                );
                $(
                  "#atd-icon-tooltip-alignment, #new-text, #atd-add-text"
                ).addClass("atd-disable-btn");
                $("#copy_paste_btn")
                  .parents(".atd-icon-tooltip")
                  .addClass("atd-disable-btn");
              } else {
                $(".atd-tab-item").removeClass("active");
                $(".atd-tab-item[data-title='text']").addClass("active");
                $(".atd-tab-item[data-title='text']").trigger("click");
                $(".atd-tab-tools-content").css("display", "none");
                $(".atd-tab-tools-content[data-title='text']").css(
                  "display",
                  "block"
                );
                $(".atd-add-text").removeClass("atd-active");
                $(".atd-editing-text").addClass("atd-active");
              }
              wp.hooks.applyFilters("atd.add_text_selected", options);
              $(".atd-font-container .atd-font-label").text(
                options.target.get("fontFamily")
              );
              $("#font-size-selector").val(options.target.get("fontSize"));
              $("#txt-color-selector").show();
              $(".atd-range-container-text-size .atd-range-text").text(
                options.target.get("fontSize")
              );
              $("#txt-color-selector").spectrum(
                "set",
                options.target.get("fill")
              );
              $("#txt-bg-color-selector").show();
              $("#txt-bg-color-selector").spectrum(
                "set",
                options.target.get("backgroundColor")
              );
              $("#new-text").val(options.target.get("text"));

              var fontWeight = options.target.get("fontWeight");
              if (fontWeight === "bold") {
                $("#bold-cb").addClass("atd-active");
              } else {
                $("#bold-cb").removeClass("atd-active");
              }

              var fontStyle = options.target.get("fontStyle");
              if (fontStyle === "italic") {
                $("#italic-cb").addClass("atd-active");
              } else {
                $("#italic-cb").removeClass("atd-active");
              }

              if (
                options.target.get("stroke") !== false &&
                options.target.getStroke() !== null
              ) {
                $("#txt-outline-color-selector").show();
                $("#txt-outline-color-selector").spectrum(
                  "set",
                  options.target.get("stroke")
                );
                $("#atd-outline-size").val(options.target.get("strokeWidth"));
                $(".add-row-text-outline .atd-range-text").text(
                  options.target.get("strokeWidth")
                );
              } else {
                $("#atd-outline-size").val(0);
                $("#txt-outline-color-selector").spectrum("set", "#4f71b9");
                $(".add-row-text-outline .atd-range-text").text("0");
              }

              var textDecoration = options.target.get("textDecoration");

              if (textDecoration === "underline") {
                $("#underline-cb")
                  .closest(".atd-text-decoration-option-item")
                  .siblings(".atd-active")
                  .removeClass("atd-active");
                $("#underline-cb")
                  .closest(".atd-text-decoration-option-item")
                  .addClass("atd-active");
              } else if (textDecoration === "line-through") {
                $("#strikethrough-cb")
                  .closest(".atd-text-decoration-option-item")
                  .siblings(".atd-active")
                  .removeClass("atd-active");
                $("#strikethrough-cb")
                  .closest(".atd-text-decoration-option-item")
                  .addClass("atd-active");
              } else if (textDecoration === "overline") {
                $("#overline-cb")
                  .closest(".atd-text-decoration-option-item")
                  .siblings(".atd-active")
                  .removeClass("atd-active");
                $("#overline-cb")
                  .closest(".atd-text-decoration-option-item")
                  .addClass("atd-active");
              } else if (textDecoration === "none") {
                $("#txt-none-cb")
                  .closest(".atd-text-decoration-option-item")
                  .siblings(".atd-active")
                  .removeClass("atd-active");
                $("#txt-none-cb")
                  .closest(".atd-text-decoration-option-item")
                  .addClass("atd-active");
              }

              var textAlign = options.target.get("textAlign");

              if (textAlign === "left") {
                $("#txt-align-left")
                  .closest(".atd-text-alignement-option-item")
                  .siblings(".atd-active")
                  .removeClass("atd-active");
                $("#txt-align-left")
                  .closest(".atd-text-alignement-option-item")
                  .addClass("atd-active");
              } else if (textAlign === "center") {
                $("#txt-align-center")
                  .closest(".atd-text-alignement-option-item")
                  .siblings(".atd-active")
                  .removeClass("atd-active");
                $("#txt-align-center")
                  .closest(".atd-text-alignement-option-item")
                  .addClass("atd-active");
              } else if (textAlign === "right") {
                $("#txt-align-right")
                  .closest(".atd-text-alignement-option-item")
                  .siblings(".atd-active")
                  .removeClass("atd-active");
                $("#txt-align-right")
                  .closest(".atd-text-alignement-option-item")
                  .addClass("atd-active");
              } else if (textAlign === "justify") {
                $("#txt-align-justify")
                  .closest(".atd-text-alignement-option-item")
                  .siblings(".atd-active")
                  .removeClass("atd-active");
                $("#txt-align-justify")
                  .closest(".atd-text-alignement-option-item")
                  .addClass("atd-active");
              }

              var txt_opacity = options.target.opacity;
              $("#opacity-slider").val(txt_opacity);
              $(".atd-range-container-text-opacity .atd-range-text").text(
                txt_opacity
              );
            } else if (
              objectType === "group" &&
              options.target.get("originalText")
            ) {
              //If it's a curved text, we load the first item properties (which should be the same than all other items
              //if (options.target.get("originalText")) {
              $(".atd-editing-text").addClass("atd-active");
              $(".atd-tab-item").removeClass("active");
              $(".atd-tab-item[data-title='text']").addClass("active");
              $(".atd-tab-item[data-title='text']").trigger("click");
              $(".atd-tab-tools-content").css("display", "none");
              $(".atd-tab-tools-content[data-title='text']").css(
                "display",
                "block"
              );
              $(".atd-add-text").removeClass("atd-active");
              $(".atd-editing-text").addClass("atd-active");

              //                            $("#cb-curved").attr('checked', 'checked');
              //tools_accordion.openPanel("text-panel");
              $(".atd-font-view").html(options.target.get("originalText"));
              $(".atd-font-container .atd-font-label")
                .text(options.target.item(0).get("fontFamily"))
                .trigger("click");
              $("#font-size-selector").val(
                options.target.item(0).get("fontSize")
              );
              $(".atd-range-container-text-size .atd-range-text").text(
                options.target.get("fontSize")
              );
              $("#txt-color-selector").show();
              $("#txt-color-selector").spectrum(
                "set",
                options.target.get("fill")
              );
              $("#txt-bg-color-selector").show();
              $("#txt-bg-color-selector").spectrum(
                "set",
                options.target.get("backgroundColor")
              );
              $("#new-text").val(options.target.get("originalText"));
              $("#curved-txt-radius-slider").val(options.target.get("radius"));
              $("#curved-txt-spacing-slider").val(
                options.target.get("spacing")
              );
              $(
                ".txt-align[value='" +
                  options.target.item(0).get("textAlign") +
                  "']"
              ).attr("checked", "checked");
              $(
                ".txt-decoration[value='" +
                  options.target.item(0).get("textDecoration") +
                  "']"
              ).attr("checked", "checked");
              var fontWeight = options.target.item(0).get("fontWeight");
              if (fontWeight === "bold")
                $("#bold-cb").attr("checked", "checked");
              else $("#bold-cb").removeAttr("checked");
              var fontStyle = options.target.item(0).get("fontStyle");
              if (fontStyle === "italic")
                $("#italic-cb").attr("checked", "checked");
              else $("#italic-cb").removeAttr("checked");

              if (
                options.target.item(0).get("stroke") !== false &&
                options.target.item(0).getStroke() !== null
              ) {
                $("#txt-outline-color-selector").show();
                $("#txt-outline-color-selector").spectrum(
                  "set",
                  options.target.item(0).get("stroke")
                );
                $("#atd-outline-size").val(
                  options.target.item(0).get("strokeWidth")
                );
                $(".add-row-text-outline .atd-range-text").text(
                  options.target.item(0).get("strokeWidth")
                );
              } else {
                $("#atd-outline-size").val(0);
                $("#txt-outline-color-selector").spectrum("set", "#4f71b9");
                $(".add-row-text-outline .atd-range-text").text("0");
              }

              var txt_opacity = options.target.item(0).opacity;
              $("#opacity-slider").val(txt_opacity);
              $(".atd-range-container-text-opacity .atd-range-text").text(
                txt_opacity
              );
              //}
            } else if (jQuery.inArray(objectType, arr_shapes) >= 0) {
              var shape_opacity = options.target.opacity;
              $("#shape-opacity-slider").val(shape_opacity);
              $("#shape-color-selector").css(
                "background-color",
                options.target.get("fill")
              );
              $("#shape-outline-color-selector").css(
                "background-color",
                options.target.get("stroke")
              );
              $("#shape-thickness-slider").val(
                options.target.get("strokeWidth")
              );
              wp.hooks.applyFilters("atd.add_shape_selected");
              //tools_accordion.openPanel("shapes-panel");
            } else if (objectType === "image") {
              var img_src = options.target.getSrc();
              //open_src_panel(options);
              var filters = options.target.filters;
              $("#img-effects input:checkbox").removeAttr("checked");
              $.each(filters, function (index, value) {
                if (value) {
                  var filter = value.type;
                  var matrix = value.matrix;
                  var blur_matrix = [
                    1 / 9,
                    1 / 9,
                    1 / 9,
                    1 / 9,
                    1 / 9,
                    1 / 9,
                    1 / 9,
                    1 / 9,
                    1 / 9,
                  ];
                  var sharpen_maxtrix = [0, -1, 0, -1, 5, -1, 0, -1, 0];
                  var emboss_matrix = [1, 1, 1, 1, 0.7, -1, -1, -1, -1];
                  if (filter === "Grayscale")
                    $(".acd-grayscale").attr("checked", "checked");
                  else if (filter === "Invert")
                    $(".acd-invert").attr("checked", "checked");
                  else if (filter === "Sepia")
                    $(".acd-sepia").attr("checked", "checked");
                  else if (filter === "Sepia2")
                    $(".acd-sepia2").attr("checked", "checked");
                  else if (filter === "Convolute") {
                    if (
                      $(matrix).not(blur_matrix).length === 0 &&
                      $(blur_matrix).not(matrix).length === 0
                    )
                      $(".acd-blur").attr("checked", "checked");
                    else if (
                      $(matrix).not(sharpen_maxtrix).length === 0 &&
                      $(sharpen_maxtrix).not(matrix).length === 0
                    )
                      $(".acd-sharpen").attr("checked", "checked");
                    else if (
                      $(matrix).not(emboss_matrix).length === 0 &&
                      $(emboss_matrix).not(matrix).length === 0
                    )
                      $(".acd-emboss").attr("checked", "checked");
                  }
                }
              });
              var target = options.target;
              var image_type = open_src_panel(options);
              wp.hooks.doAction(
                "atd_EDITOR.image_selected",
                target,
                image_type
              );
            } else if (
              objectType === "path" ||
              objectType === "path-group" ||
              (objectType === "group" && atd.svg_colorization !== "none")
            ) {
              var target = options.target;
              if (objectType === "group") {
                wp.hooks.applyFilters("atd.add_text_selected");
              }
              //var active_tab = open_src_panel(options, "cliparts-panel");
              var active_tab = "atd-uploads-filters-wrap";
              var target = options.target;
              var image_type = open_src_panel(options);
              wp.hooks.doAction(
                "atd_EDITOR.image_selected",
                target,
                image_type
              );
              $("#" + active_tab + " .clipart-bg-color-container").html("");
              if (
                (options.target.isSameColor && options.target.isSameColor()) ||
                !options.target.paths
              ) {
                var color_picker_id = "clipart-bg-" + 1 + "-color-selector";
                var colorpicker_tpl =
                  '<span id="' +
                  color_picker_id +
                  '" class="svg-color-selector" data-placement="top" data-tooltip-title="' +
                  atd.translated_strings.svg_background_tooltip +
                  '" style="background-color:' +
                  options.target.get("fill") +
                  '"></span>';
                $("#" + active_tab + " .clipart-bg-color-container").append(
                  colorpicker_tpl
                );
                //$("[data-tooltip-title]").otooltip();
                load_svg_color_picker(color_picker_id);
              } else if (options.target.paths) {
                var used_colors = [];
                var picker_index = 0;
                for (var i = 0; i < options.target.paths.length; i++) {
                  var color_picker_id =
                    "clipart-bg-" + picker_index + "-color-selector";
                  var current_color = options.target.paths[i].fill;
                  var colorpicker_tpl =
                    '<span id="' +
                    color_picker_id +
                    '" class="svg-color-selector" data-placement="top" data-tooltip-title="' +
                    atd.translated_strings.svg_background_tooltip +
                    '" style="background-color:' +
                    current_color +
                    '" data-index="' +
                    i +
                    '"></span>';
                  if (atd.svg_colorization === "by-colors") {
                    var color_pos = jQuery.inArray(current_color, used_colors);
                    if (color_pos === -1) {
                      $(
                        "#" + active_tab + " .clipart-bg-color-container"
                      ).append(colorpicker_tpl);
                      //$("[data-tooltip-title]").otooltip();
                      load_svg_color_picker(color_picker_id);
                      used_colors.push(current_color);
                      picker_index++;
                    } else {
                      var original_picker_id =
                        "#clipart-bg-" + color_pos + "-color-selector";
                      var old_indexes =
                        $(original_picker_id).attr("data-index");
                      $(original_picker_id).attr(
                        "data-index",
                        old_indexes + "," + i
                      );
                    }
                  } else if (atd.svg_colorization === "by-path") {
                    $("#" + active_tab + " .clipart-bg-color-container").append(
                      colorpicker_tpl
                    );
                    //$("[data-tooltip-title]").otooltip();
                    load_svg_color_picker(color_picker_id);
                    picker_index++;
                  }
                }
              }
            }

            if (options.target.get("lockMovementX"))
              $("#lock-mvt-x").attr("checked", "checked");
            else $("#lock-mvt-x").removeAttr("checked");
            if (options.target.get("lockMovementY"))
              $("#lock-mvt-y").attr("checked", "checked");
            else $("#lock-mvt-y").removeAttr("checked");
            if (options.target.get("lockScalingX"))
              $("#lock-scl-x").attr("checked", "checked");
            else $("#lock-scl-x").removeAttr("checked");
            if (options.target.get("lockScalingY"))
              $("#lock-scl-y").attr("checked", "checked");
            else $("#lock-scl-y").removeAttr("checked");
            if (options.target.get("lockDeletion"))
              $("#lock-Deletion").attr("checked", "checked");
            else $("#lock-Deletion").removeAttr("checked");
          }
        }
      );
      atd_editor.canvas[selected_part].on("object:added", function (options) {
        if (options.target) {
          atd_editor.canvas[selected_part].calcOffset();
          atd_editor.canvas[selected_part].renderAll();
          options.target.setCoords();
          var objectType = options.target.type;
          if (objectType === "i-text") {
            reset_text_palette();
          } else if (
            objectType === "group" &&
            options.target.get("originalText")
          ) {
            $(".atd-editing-text").addClass("atd-active");
          }
          atd_editor.canvas[selected_part].calcOffset();
        }
      });
      atd_editor.canvas[selected_part].on(
        "object:modified",
        function (options) {
          atd_editor.canvas[selected_part].calcOffset();
          atd_editor.canvas[selected_part].renderAll();
          options.target.setCoords();
          atd_editor.save_canvas();
        }
      );
      atd_editor.canvas[selected_part].on("object:moving", function (options) {
        var obj = options.target;
        // if object is too big ignore
        if (
          obj.currentHeight > obj.canvas.height ||
          obj.currentWidth > obj.canvas.width
        ) {
          return;
        }
        obj.setCoords();
        // top-left  corner
        if (obj.getBoundingRect().top < 0 || obj.getBoundingRect().left < 0) {
          obj.top = Math.max(obj.top, obj.top - obj.getBoundingRect().top);
          obj.left = Math.max(obj.left, obj.left - obj.getBoundingRect().left);
        }
        // bot-right corner
        if (
          obj.getBoundingRect().top + obj.getBoundingRect().height >
            obj.canvas.height ||
          obj.getBoundingRect().left + obj.getBoundingRect().width >
            obj.canvas.width
        ) {
          obj.top = Math.min(
            obj.top,
            obj.canvas.height -
              obj.getBoundingRect().height +
              obj.top -
              obj.getBoundingRect().top
          );
          obj.left = Math.min(
            obj.left,
            obj.canvas.width -
              obj.getBoundingRect().width +
              obj.left -
              obj.getBoundingRect().left
          );
        }
      });
    }

    function _setCustomProperties(object) {
      object.toObject = (function (toObject) {
        return function () {
          return fabric.util.object.extend(toObject.call(this), {
            lockMovementX: this.lockMovementX,
            lockMovementY: this.lockMovementY,
            lockScalingX: this.lockScalingX,
            lockScalingY: this.lockScalingY,
            lockDeletion: this.lockDeletion,
            price: this.price,
            originalText: this.originalText,
            boundingBox: this.boundingBox,
            radius: this.radius,
            spacing: this.spacing,
          });
        };
      })(object.toObject);
    }

    atd_editor.setCustomProperties = function (object) {
      _setCustomProperties(object);
    };

    atd_editor.is_json = function (data) {
      if (
        /^[\],:{}\s]*$/.test(
          data
            .replace(/\\["\\\/bfnrtu]/g, "@")
            .replace(
              /"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,
              "]"
            )
            .replace(/(?:^|:|,)(?:\s*\[)+/g, "")
        )
      )
        return true;
      else return false;
    };

    atd_editor.save_canvas = function () {
      var data_id = $(
        "#atd-parts-bar > li:eq(" + atd_editor.selected_part + ")"
      ).attr("data-id");
      if (typeof atd_editor.serialized_parts[data_id] === "undefined")
        atd_editor.serialized_parts[data_id] = ["{}"];
      var i;
      for (
        i = atd_editor.canvasManipulationsPosition[data_id];
        i <= atd_editor.serialized_parts[data_id].length - 2;
        i++
      ) {
        atd_editor.serialized_parts[data_id].pop();
      }

      atd_editor.canvasManipulationsPosition[data_id]++;
      var json = JSON.stringify(
        atd_editor.canvas[atd_editor.selected_part].toJSON([
          "lockMovementX",
          "lockMovementY",
          "lockRotation",
          "lockScalingX",
          "lockScalingY",
          "price",
          "lockDeletion",
          "originalText",
          "radius",
          "spacing",
        ])
      );
      atd_editor.serialized_parts[data_id].push(json);
      update_price();
      atd_editor.refresh_undo_redo_status();
    };

    atd_editor.reload_background_overlay_if_needed = function (
      index,
      callback
    ) {
      load_background_overlay_if_needed(index, callback);
    };

    atd_editor.centerObjectH = function (object) {
      var data_id = $(
        "#atd-parts-bar > li:eq(" + atd_editor.selected_part + ")"
      ).attr("data-id");
      if (atd_editor.box_center_x) {
        if (atd_editor.scale_factor && atd.clip_x) {
          object.set("left", atd_editor.box_center_x / atd_editor.scale_factor);
        } else object.set("left", atd_editor.box_center_x);
      } else {
        var realWidth = object.getWidth();
        //We make sure we're making our calculations based on the scaled width
        if (atd_editor.scale_factor)
          realWidth = realWidth * atd_editor.scale_factor;
        var left = parseFloat(atd.canvas[data_id].canvas_width) / 2;
        if (atd_editor.box_center_x)
          left = atd_editor.box_center_x - realWidth / 2;
        if (object.originX === "left") left = left - realWidth / 2;
        object.set("left", left);
      }
    };

    atd_editor.centerObjectV = function (object) {
      var data_id = $(
        "#atd-parts-bar > li:eq(" + atd_editor.selected_part + ")"
      ).attr("data-id");
      if (atd_editor.box_center_y) {
        if (atd_editor.scale_factor && atd.clip_y)
          object.set("top", atd_editor.box_center_y / atd_editor.scale_factor);
        else object.set("top", atd_editor.box_center_y);
      } else {
        var realHeight = object.getHeight();
        //We make sure we're making our calculations based on the scaled height
        if (atd_editor.scale_factor)
          realHeight = realHeight * atd_editor.scale_factor;
        var top = parseFloat(atd.canvas[data_id].canvas_height) / 2;
        if (atd_editor.box_center_y)
          top = parseFloat(atd_editor.box_center_y) - realHeight / 2;
        object.set("top", top);
      }
    };

    atd_editor.centerObject = function (object) {
      atd_editor.centerObjectV(object);
      atd_editor.centerObjectH(object);
    };

    atd_editor.change_item_color = function (id, hex) {
      var selected_object =
        atd_editor.canvas[atd_editor.selected_part].getActiveObject();
      if (!atd_editor.isEmpty(selected_object)) {
        if (selected_object !== null && selected_object.type !== "group") {
          atd_set_color(id, selected_object, hex);
        } else if (
          selected_object !== null &&
          selected_object.type === "group"
        ) {
          selected_object.forEachObject(function (a) {
            atd_set_color(id, a, hex);
          });
        }
      }
    };

    function open_src_panel(options, default_panel) {
      var obj = options.target.toObject();
      var img_src = obj.src;
      //var in_cliparts = $(".atd-preview-clippart-group img[src='" + img_src + "']").length;
      //var in_cliparts = $(".atd-preview-clippart-group img[src='" + img_src + "']").length;
      //var in_uploads = $(".atd-upload-inner .atd-preview-upolad[style='background-image:url('" + img_src + "')']").length;

      var cliparts_elements = $(
        ".atd-preview-clippart-group .atd-preview-clippart"
      ).filter(function () {
        return $(this).css("background-image") === 'url("' + img_src + '")';
      });

      var uploads_elements = $(".atd-upload-inner .atd-preview-upolad").filter(
        function () {
          return $(this).css("background-image") === 'url("' + img_src + '")';
        }
      );

      if (0 < cliparts_elements.length) {
        $(".atd-tab-item").removeClass("active");
        $(".atd-tab-item[data-title='clippart']").addClass("active");
        $(".atd-tab-item[data-title='clippart']").trigger("click");
        $(".atd-tab-tools-content").css("display", "none");
        $(".atd-tab-tools-content[data-title='clippart']").css(
          "display",
          "block"
        );
        $(".atd-clippart-inner").removeClass("atd-active");
        $(".atd-clippart-container").removeClass("atd-active");
        cliparts_elements
          .closest(".atd-tab-tools-content")
          .find(".atd-clippart-edit-inner")
          .addClass("atd-active");
        return "cliparts-panel";
      } else if (0 < uploads_elements.length) {
        $(".atd-tab-item").removeClass("active");
        $(".atd-tab-item[data-title='upload']").addClass("active");
        $(".atd-tab-item[data-title='upload']").trigger("click");
        $(".atd-tab-tools-content").css("display", "none");
        $(".atd-tab-tools-content[data-title='upload']").css(
          "display",
          "block"
        );
        $(".atd-upload-inner").removeClass("atd-active");
        $(".atd-upload-container").removeClass("atd-active");
        uploads_elements
          .closest(".atd-tab-tools-content")
          .find(".atd-upload-edit-inner")
          .addClass("atd-active");
        return "uploads-panel";
      }
    }

    function atd_set_color(id, selected_object, hex) {
      if (
        id === "txt-color-selector" ||
        id === "shape-color-selector" ||
        id === "clipart-bg-color-selector" ||
        id === "team-name-color-selector" ||
        id === "team-number-color-selector"
      ) {
        selected_object.set("fill", hex);
      } else if (id === "txt-bg-color-selector")
        selected_object.set("backgroundColor", hex);
      else if (
        id === "txt-outline-color-selector" ||
        id === "shape-outline-color-selector"
      )
        selected_object.set("stroke", hex);
      wp.hooks.doAction(
        "atd_EDITOR.element_color_changed",
        id,
        selected_object,
        hex
      );
      atd_editor.canvas[atd_editor.selected_part].renderAll();
    }

    atd_editor.refresh_undo_redo_status = function () {
      var data_id = $(
        "#atd-parts-bar > li:eq(" + atd_editor.selected_part + ")"
      ).attr("data-id");
      if (
        atd_editor.serialized_parts[data_id].length === 1 ||
        atd_editor.canvasManipulationsPosition[data_id] === 0
      )
        $("#undo-btn").addClass("disabled");
      else $("#undo-btn").removeClass("disabled");
      if (
        atd_editor.serialized_parts[data_id].length > 0 &&
        atd_editor.canvasManipulationsPosition[data_id] <
          atd_editor.serialized_parts[data_id].length - 1
      )
        $("#redo-btn").removeClass("disabled");
      else $("#redo-btn").addClass("disabled");
    };

    function init_empty_canvas_data_array() {
      if (
        typeof to_load ===
        "undefined" /*&& (typeof localStorage.getItem("atd_save_canvas_status") === 'undefined' || localStorage.getItem("atd_save_canvas_status") === "")*/
      ) {
        $("#atd-parts-bar > li").each(function (key) {
          var data_id = $(this).attr("data-id");
          atd_editor.serialized_parts[data_id] = [];
          atd_editor.canvasManipulationsPosition[data_id] = -1;
          var nb_parts = $("#atd-parts-bar > li").length;
          if (key === nb_parts - 1) {
            loop_through_parts(
              atd.output_loop_delay,
              click_on_part,
              function () {
                $("#atd-parts-bar > li").first().click();
                atd_editor.canvas[atd_editor.selected_part].renderAll();
                rescale_canvas_if_needed();
                $.unblockUI();
              }
            );
          }
        });
      }
    }

    function click_on_part(part_index) {
      $("#atd-parts-bar > li:eq(" + part_index + ")").click();
    }

    function loop_through_parts(delay, loop_callback, end_callback) {
      $.blockUI({ message: atd.translated_strings.loading_msg });
      var nb_parts = $("#atd-parts-bar > li").length;
      var current_part = 0;
      var loopKey = setInterval(function () {
        if ($.isFunction(loop_callback)) loop_callback(current_part);
        if (current_part === nb_parts - 1) {
          window.clearInterval(loopKey);
          if ($.isFunction(end_callback)) {
            setTimeout(function () {
              end_callback();
            }, delay);
          } else $.unblockUI();
        } else current_part++;
      }, delay);
    }

    function load_background_overlay_if_needed(
      index,
      callback,
      generating_output
    ) {
      var selector = $("#atd-parts-bar > li:eq(" + index + ")");
      var overlay_not_included = selector.attr("data-ovni");
      if (typeof generating_output === "undefined") generating_output = false;
      var bg_img = new Image();
      //Both background and overlay images consider the scale when being defined so we don't need to resize them
      bg_img.onload = function () {
        var dimensions = atd_editor.get_img_best_fit_dimensions(
          bg_img,
          atd.canvas_w,
          atd.canvas_h
        );
        atd_editor.canvas[atd_editor.selected_part].setBackgroundImage(
          bg_img.src,
          atd_editor.canvas[atd_editor.selected_part].renderAll.bind(
            atd_editor.canvas[atd_editor.selected_part]
          ),
          {
            left: atd.canvas_w / 2,
            top: atd.canvas_h / 2,
            originX: "center",
            originY: "center",
            width: dimensions[0],
            height: dimensions[1],
          }
        );
      };
      if (overlay_not_included === "-1" && generating_output) {
        //White bg if CMYK mode
        if (atd.output_format === "jpg") {
          atd_editor.canvas[atd_editor.selected_part].setBackgroundColor(
            "rgba(255, 255, 255, 1)",
            atd_editor.canvas[atd_editor.selected_part].renderAll.bind(
              atd_editor.canvas[atd_editor.selected_part]
            )
          );
        }
        atd_editor.canvas[atd_editor.selected_part].overlayImage = null;
        atd_editor.canvas[atd_editor.selected_part].renderAll.bind(
          atd_editor.canvas[atd_editor.selected_part]
        );
      } else {
        //White bg if CMYK mode
        if (atd.output_format === "jpg") {
          atd_editor.canvas[atd_editor.selected_part].setBackgroundColor(
            "",
            atd_editor.canvas[atd_editor.selected_part].renderAll.bind(
              atd_editor.canvas[atd_editor.selected_part]
            )
          );
        }
        var ov_img = new Image();
        ov_img.onload = function () {
          var dimensions = atd_editor.get_img_best_fit_dimensions(
            ov_img,
            atd.canvas_w,
            atd.canvas_h
          );
          atd_editor.canvas[atd_editor.selected_part].setOverlayImage(
            ov_img.src,
            atd_editor.canvas[atd_editor.selected_part].renderAll.bind(
              atd_editor.canvas[atd_editor.selected_part]
            ),
            {
              left: atd.canvas_w / 2,
              top: atd.canvas_h / 2,
              originX: "center",
              originY: "center",
              width: dimensions[0],
              height: dimensions[1],
            }
          );
        };
      }

      //Background not included
      var bg_not_included_url = selector.attr("data-url");
      if (bg_not_included_url) {
        var bg_code =
          "url('" + bg_not_included_url + "') no-repeat center center";
        $("#atd-editor-container .canvas-container:eq(" + index + ")").css(
          "background",
          bg_code
        );
        var porto_header_height = $("#header-wrap").outerHeight();
        var header_height = $("header").outerHeight();
        var wpadminbar = $("#wpadminbar").outerHeight();
        if (wpadminbar !== null && wpadminbar !== "undefined") {
          wpadminbar = wpadminbar + 21;
        } else {
          wpadminbar = 0;
        }
        if (
          porto_header_height === null &&
          porto_header_height === "undefined"
        ) {
          porto_header_height = header_height;
        }

        var recup_h = $(".atd-column-center").outerHeight();
        var recup_w = $(".atd-column-center").outerWidth();
        var data_id = selector.attr("data-id");
        var canvas_d = atd.canvas[data_id];
        var dimensions_with_space_available =
          atd_editor.get_dimensions_with_space_available(
            recup_h,
            recup_w,
            data_id,
            1
          );
        
        var verif_recup_h;
        var verif_recup_w;
        var verif_canvas_top;
        var verif_canvas_left;
        var verif_canvas_w;
        var verif_canvas_h;
        var t_shirt_ratio = atd.canvas[data_id].t_shirt_width / atd.canvas[data_id].t_shirt_height;
  
        if(recup_h >= dimensions_with_space_available[1]){
            var verif_recup_h = dimensions_with_space_available[1];
            var verif_canvas_top = dimensions_with_space_available[2];
            var verif_canvas_h = dimensions_with_space_available[4];
        }else{
            //var verif_recup_h = recup_h * t_shirt_ratio;
            var verif_recup_h = recup_h;
            var verif_canvas_top = (verif_recup_h * atd.canvas[data_id].canvas_top) / atd.canvas[data_id].t_shirt_height;
            var verif_canvas_h = (verif_recup_h * atd.canvas[data_id].canvas_height) / atd.canvas[data_id].t_shirt_height;
        }
        
        if(recup_w >= dimensions_with_space_available[0]){
            var verif_recup_w = dimensions_with_space_available[0];
            var verif_canvas_left = dimensions_with_space_available[3];
            var verif_canvas_w = dimensions_with_space_available[5];
        }else{
            var verif_recup_w = recup_w * t_shirt_ratio;
            if(verif_recup_w > verif_recup_h){
                verif_recup_w = (atd.canvas[data_id].canvas_width * verif_recup_h) / atd.canvas[data_id].canvas_height;
            }
            var verif_canvas_left = (verif_recup_w * atd.canvas[data_id].canvas_left) / atd.canvas[data_id].t_shirt_width;
            var verif_canvas_w = (verif_recup_w * atd.canvas[data_id].canvas_width) / atd.canvas[data_id].t_shirt_width;
        }        
        
        $("#atd-editor-container .canvas-container").css({
          width: verif_recup_w,
          height: verif_recup_h
        });      
        
        var canvasBorderColor =
          canvas_d.border_color === "" ? "lightgrey" : canvas_d.border_color;
        $("#atd-editor-container .canvas-container canvas").css({
            border: "1px dashed " + canvasBorderColor,
            top: verif_canvas_top + "px",
            left: verif_canvas_left + "px",
            height: verif_canvas_h + "px",
            width: verif_canvas_w + "px"
        });
      } else $("#atd-editor-container .canvas-container").css("background", "none");
      if ($.isFunction(callback))
        setTimeout(function () {
          callback(index);
        }, 200);
    }

    atd_editor.get_img_best_fit_dimensions = function (
      img,
      max_width,
      max_height
    ) {
      var w = img.width;
      var h = img.height;
      if (w < max_width && h < max_height) return [w, h];
      var ratio = w / h;
      w = max_width;
      h = max_width / ratio;
      if (h > max_height) {
        h = max_height;
        w = max_height * ratio;
      }
      return wp.hooks.applyFilters(
        "atd_EDITOR.atd_img_dimensions",
        [w, h],
        img
      );
    };

    function load_svg_color_picker(id) {
      //var id = $(this).attr("id");
      if ($("#" + id).hasClass("atd-team")) {
        atd.palette_type = atd.atd_team_name_colors_palette_type;
        atd.palette_tpl = atd.atd_team_name_palette_tpl;
      }
      if (atd.palette_type === "unlimited") {
        $("#" + id).spectrum({
          color: "#4f71b9",
          showInput: true,
          showAlpha: true,
          showPalette: false,
          showButtons: false,
          preferredFormat: "hex",
          move: function (color) {
            $("#" + id).val(color);
            atd_editor.change_item_color(id, $("#" + id).val());
          },
          change: function (color) {
            $("#" + id).val(color);
            atd_editor.change_item_color(id, $("#" + id).val());
          },
        });
      } else {
        $("#" + id).spectrum({
          color: "#4f71b9",
          showInput: true,
          showAlpha: true,
          showPaletteOnly: true,
          showPalette: true,
          showButtons: false,
          preferredFormat: "hex",
          palette: atd.palette_tpl,
          move: function (color) {
            $("#" + id).val(color);
            if ("atd-team-name-color-selector" === id) {
              atd_EDITOR.click_part("name");
              var selected_object =
                atd_EDITOR.findByCliperName("#atd-team-name");
              atd_EDITOR.canvas[atd_EDITOR.selected_part].setActiveObject(
                selected_object
              );
              atd_EDITOR.canvas[atd_EDITOR.selected_part].renderAll();
              atd_EDITOR.save_canvas();
            } else if ("atd-team-number-color-selector" === id) {
              atd_EDITOR.click_part("number");
              var selected_object =
                atd_EDITOR.findByCliperName("#atd-team-number");
              atd_EDITOR.canvas[atd_EDITOR.selected_part].setActiveObject(
                selected_object
              );
              atd_EDITOR.canvas[atd_EDITOR.selected_part].renderAll();
              atd_EDITOR.save_canvas();
            }
            atd_editor.change_item_color(id, $("#" + id).val());
          },
          change: function (color) {
            $("#" + id).val(color);
            atd_editor.change_item_color(id, $("#" + id).val());
          },
        });
      }
    }

    function change_svg_color(id, hex, index) {
      $("#" + id).css("background-color", "#" + hex);
      var selected_object =
        atd_editor.canvas[atd_editor.selected_part].getActiveObject();
      if (!atd_editor.isEmpty(selected_object)) {
        if (
          selected_object !== null &&
          (selected_object.type === "path" ||
            selected_object.type === "path-group" ||
            selected_object.type === "group")
        ) {
          {
            if (
              (selected_object.isSameColor && selected_object.isSameColor()) ||
              !selected_object.paths
            ) {
              selected_object.set("fill", "#" + hex);
            } else if (selected_object.paths) {
              if (atd.svg_colorization === "by-colors") {
                index = $("#" + id).attr("data-index");
                var indexes = index.split(",");
                $.each(indexes, function (key, value) {
                  selected_object.paths[value].setFill("#" + hex);
                });
              } else selected_object.paths[index].setFill("#" + hex);
            }
          }
          atd_editor.canvas[atd_editor.selected_part].renderAll();
        }
      }
    }

    $(document).on("click", ".atd-custom-colors-container span", function (e) {
      var id = $(this).parent().data("id");
      var hex = $(this).data("color");
      atd_editor.change_item_color(id, hex);
    });

    $(document).on(
      "click",
      ".atd-custom-svg-colors-container span",
      function (e) {
        var id = $(this).parent().data("id");
        var index = $(this).parent().data("index");
        var hex = $(this).data("color");
        change_svg_color(id, hex, index);
      }
    );

    function fabric_text_to_itext() {
      //Array of property which will be used to create the i-text object
      var text_prop_array = [
        "active",
        "angle",
        "backgroundColor",
        "clipTo",
        "currentHeight",
        "currentWidth",
        "fill",
        "currentWidth",
        "flipX",
        "flipY",
        "fontFamily",
        "fontSize",
        "fontStyle",
        "fontWeight",
        "height",
        "left",
        "lineHeight",
        "originX",
        "originY",
        "scaleX",
        "scaleY",
        "shadow",
        "text",
        "textAlign",
        "textBackgroundColor",
        "textDecoration",
        "top",
        "width",
        "lockMovementX",
        "lockMovementY",
        "lockRotation",
        "lockScalingX",
        "lockScalingY",
        "lockUniScaling",
      ];
      setTimeout(function () {
        var canvas_objs = atd_editor.canvas[atd_editor.selected_part]
          .getObjects()
          .map(function (o) {
            return o;
          });
        $.each(canvas_objs, function (obj_index, obj_value) {
          if (obj_value.type === "text") {
            var itext = new fabric.IText("");
            $.each(text_prop_array, function (prop_index, prop_name) {
              itext.set(prop_name, obj_value.get(prop_name));
            });
            atd_editor.canvas[atd_editor.selected_part].remove(obj_value);
            atd_editor.canvas[atd_editor.selected_part].add(itext);
          }
        });
        atd_editor.canvas[atd_editor.selected_part].renderAll.bind(
          atd_editor.canvas[atd_editor.selected_part]
        );
      }, 3600);
    }

    function reset_text_palette() {
      $("#new-text").val("");
      $(".atd-text-alignement-option").removeClass(".atd-active");
      $("#underline-cb")
        .closest(".atd-text-decoration-option-item")
        .removeClass(".atd-active");
      $("#strikethrough-cb")
        .closest(".atd-text-decoration-option-item")
        .removeClass(".atd-active");
      $("#overline-cb")
        .closest(".atd-text-decoration-option-item")
        .removeClass(".atd-active");
      $("#txt-none-cb")
        .closest(".atd-text-decoration-option-item")
        .removeClass(".atd-active");
      $("#bold-cb")
        .closest(".atd-text-style-option-item")
        .removeClass(".atd-active");
      $("#italic-cb")
        .closest(".atd-text-style-option-item")
        .removeClass(".atd-active");
      $(".atd-font-container .atd-font-label").text(
        $(".atd-font-drop-down-item:first .atd-font-name").text()
      ) /*.trigger('click')*/;
      $("#atd-outline-size").val($("#atd-outline-size option:first").val());
      $("#opacity-slider").val(1);
    }

    function not_yet_implemented(e) {
      e.preventDefault();
      alert("Not yet implemented");
      return;
    }

    //Preview
    $(document).on("touchstart click", "#preview-btn", function (e) {
      // $("#atd-modal .omodal-body").html("");
      var itemCarouselLength = $('.atd-preview-prev-des-item').length;
      
      if(itemCarouselLength !== 0) {
        
        $(".atd-preview-prev-des-item").each(function (i, val) {
          $(".atd-preview-prev-des-inner")
            .trigger("remove.owl.carousel", [i])
            .trigger("refresh.owl.carousel");
        });

      }
      
      // $(".atd-preview-prev-des-inner").html("");
      //Make sure the last modification is handled
      atd_editor.save_canvas();
      if (atd.clip_include_in_output === "no")
        atd_editor.canvas[atd_editor.selected_part].clipTo = null;
      loop_through_parts(
        atd.output_loop_delay,
        generate_canvas_part,
        function () {
          // $("#atd-parts-bar > li").first().click();
          // $("#atd-parts-bar > li").each(function() {
          //     var data_id = $(this).attr("data-id");
          //     setTimeout(function() {
          //         var get_dimension_w = $('#atd-modal .omodal-body > div#preview-' + data_id).width();
          //         var get_dimension_h = $('#atd-modal .omodal-body > div#preview-' + data_id).height();
          //         var dimensions_with_space_available = atd_editor.get_dimensions_with_space_available(get_dimension_h, get_dimension_w, data_id, 0);
          //         $('#atd-modal .omodal-body > div#preview-' + data_id).css({ position: 'relative', margin: "0 auto", height: dimensions_with_space_available[0] + "px", width: dimensions_with_space_available[1] + "px" });
          //         $('#atd-modal .omodal-body > div#preview-' + data_id + '> img').css({ border: "1px solid " + atd.canvas[data_id].border_color, position: 'absolute', top: dimensions_with_space_available[2] + "px", left: dimensions_with_space_available[3] + "px", height: dimensions_with_space_available[4] + "px", width: dimensions_with_space_available[5] + "px" });
          //     }, 1000);
          //     $('#atd-modal').omodal("show");
          //     $.unblockUI();
          // });
          $(".atd-shadow-prev-des, .atd-preview-box-prev-des").addClass(
            "atd-show"
          );

          $("body").css("overflow", "hidden");

          $.unblockUI();
        }
      );
    });

    //Download design
    $(document).on("click", "#download-btn", function (e) {
      $("#debug").html("");
      if (atd.clip_include_in_output === "no")
        atd_editor.canvas[atd_editor.selected_part].clipTo = null;
      loop_through_parts(
        atd.output_loop_delay,
        generate_final_canvas_part,
        function () {
          if (jQuery.isEmptyObject(atd_editor.final_canvas_parts)) {
            $("#debug").html(
              "<div class='atd-failure'>" +
                atd.translated_strings.empty_object_msg +
                "</div>"
            );
            $.unblockUI();
          } else {
            var variation_id = atd.global_variation_id;
            var frm_data = new FormData();
            frm_data.append("action", "generate_downloadable_file");
            frm_data.append("variation_id", variation_id);
            frm_data.append("format", atd.output_format);
            frm_data = convert_final_canvas_parts_to_blob(frm_data);

            $.ajax({
              type: "POST",
              url: ajax_object.ajax_url,
              data: frm_data,
              processData: false,
              contentType: false,
            }).done(function (data) {
              $.unblockUI();
              if (atd_editor.is_json(data)) {
                var response = JSON.parse(data);
                if ($("#atd-parts-bar > li").length > 1) {
                  $("#atd-parts-bar > li").first().click();
                } else reload_first_part_data();

                $("#debug").html(response.message);
                $.ajax($fragment_refresh);
                $(".atd-debug-wrap").addClass("atd-active");
              } else $("#debug").html(data);
            });
          }
        }
      );
    });

    function reload_first_part_data() {
      var data_id = $("#atd-parts-bar > li:eq(0)").attr("data-id");
      atd_editor.canvas[atd_editor.selected_part].clear();
      atd_editor.canvas[atd_editor.selected_part].loadFromJSON(
        atd_editor.serialized_parts[data_id][
          atd_editor.canvasManipulationsPosition[data_id]
        ],
        function () {
          atd_editor.canvas[atd_editor.selected_part].renderAll.bind(
            atd_editor.canvas[atd_editor.selected_part]
          );
          rescale_canvas_if_needed();
        }
      );
    }

    //Delete design
    $(document).on("touchstart click", ".atd-delete-design", function (e) {
      e.preventDefault();
      atd.is_beforeunload = true;
      var index = $(this).data("index");
      var frm_data = new FormData();
      frm_data.append("action", "delete_saved_design");
      frm_data.append("design_index", index);

      $.ajax({
        type: "POST",
        url: ajax_object.ajax_url,
        data: frm_data,
        processData: false,
        contentType: false,
      }).done(function (data) {
        var response = JSON.parse(data);
        if (response.success) {
          alert(response.success_message);
          location.reload();
        } else {
          alert(response.failure_message);
        }
      });
    });

    //Save design for later
    $(document).on("touchstart click", "#save-btn", function (e) {
      atd.is_beforeunload = true;
      var index = $(this).data("index");
      var data_action = $(this).attr("data-action");
      if (typeof data_action === "undefined") data_action = "new-save";

      var design_name = $("#design-name").val();
      if ("undefined" !== design_name && "" !== design_name) {
        // $(this).parent().parent().find(".close.ti-close").click();
        $(this)
          .closest(".atd-preview-box-save")
          .find(".atd-icon-save-cross")
          .click();
        loop_through_parts(
          atd.output_loop_delay,
          generate_final_canvas_part,
          function () {
            if (jQuery.isEmptyObject(atd_editor.final_canvas_parts)) {
              $("#debug").html(
                "<div class='atd-failure'>" +
                  atd.translated_strings.empty_object_msg +
                  "</div>"
              );
              $.unblockUI();
            } else {
              var variation_id = atd.global_variation_id;
              var frm_data = new FormData();
              frm_data.append("action", "save_design_for_later");
              frm_data.append("design_index", index);
              frm_data.append("design_name", design_name);
              frm_data.append("variation_id", variation_id);
              frm_data.append("format", atd.output_format);
              frm_data.append("data_action", data_action);
              frm_data = convert_final_canvas_parts_to_blob(frm_data);
              $.ajax({
                type: "POST",
                url: ajax_object.ajax_url,
                data: frm_data,
                processData: false,
                contentType: false,
              }).done(function (data) {
                $.unblockUI();
                if (atd_editor.is_json(data)) {
                  var response = JSON.parse(data);
                  $("#atd-parts-bar > li").first().click();
                  if (!data.is_logged) $(location).attr("href", response.url);
                  else {
                    if (data.success) $(location).attr("href", response.url);
                  }
                } else $("#debug").html(data);
              });
            }
          }
        );
      }
    });

    //Quantity setter
    $(document).on(
      "click",
      ".atd-qty-container .plus, .atd-qty-container .minus",
      function () {
        // Get values
        var $qty = $(this).siblings(".atd-qty"); //$("#atd-qty"),
        var currentVal = parseFloat($qty.val());
        var max = parseFloat($qty.attr("max"));
        var min = parseFloat($qty.attr("min"));
        var step = $qty.attr("step");
        // Format values
        if (!currentVal || currentVal === "" || currentVal === "NaN")
          currentVal = 0;
        if (max === "" || max === "NaN") max = "";
        if (min === "" || min === "NaN") min = 0;
        if (
          step === "any" ||
          step === "" ||
          step === undefined ||
          parseFloat(step) === "NaN"
        )
          step = 1;
        // Change the value
        if ($(this).is(".plus")) {
          if (max && (max === currentVal || currentVal > max)) {
            $qty.val(max);
          } else {
            $qty.val(currentVal + parseFloat(step));
          }
        } else {
          if (min && (min === currentVal || currentVal < min)) {
            $qty.val(min);
          } else if (currentVal > 0) {
            $qty.val(currentVal - parseFloat(step));
          }
        }
        // Trigger change event
        $qty.trigger("change");
        //For WAD (bulk discounts)
        update_price();
      }
    );

    $(".atd-rp-attribute.cart-item-edit").click(function () {
      return confirm(atd.translated_strings.cart_item_edition_switch);
    });

    $(document).on("change", ".atd-qty", function () {
      var qty = $(this).val();
      var unit_price = $(this).attr("uprice");
      var opt_price = $(this).attr("opt_price");
      var total_field = $(this).siblings(".total-price").find(".total_order");
      if (!$.isNumeric(qty)) {
        $(this).val(1);
        total_field.html(accounting.formatMoney(unit_price));
        return;
      }
      if ($.isNumeric(opt_price)) {
        unit_price = parseFloat(unit_price) + parseFloat(opt_price);
        unit_price = wp.hooks.applyFilters(
          "atd.unit_price",
          unit_price,
          $(this)
        );
      }
      var total = unit_price * qty;
      total_field.html(accounting.formatMoney(total));
    });

    //Add to cart
    $(document).on("touchstart click", "#add-to-cart-btn", function (e) {
      $("#debug").html("");
      var variations = {};

      $.each($(".atd-qty-container"), function (key, curr_object) {
        var qty = $(this).find(".atd-qty").val();
        var variation_name = $(this).find(".atd-qty").attr("variation_name");
        variations[variation_name] = {};
        variations[variation_name]["qty"] = qty;
        variations[variation_name]["id"] = $(this).data("id");
      });
      var form_builder_is_validate = false;
      if ($("form.formbuilt").length === 0) {
        form_builder_is_validate = true;
      } else {
        form_builder_is_validate = $("form.formbuilt").validationEngine(
          "validate",
          { showArrow: false }
        );
      }
      //Make sure the last modification is handled
      atd_editor.save_canvas();

      atd_ninja_form_validation().then(function function_name(form_is_valid) {
        if (!form_is_valid || !form_builder_is_validate) {
          $.unblockUI();
        } else {
          if (atd.clip_include_in_output === "no")
            atd_editor.canvas.clipTo = null;
          loop_through_parts(
            atd.output_loop_delay,
            generate_final_canvas_part,
            function () {
              if (jQuery.isEmptyObject(atd_editor.final_canvas_parts)) {
                $("#debug").html(
                  "<div class='atd-failure'>" +
                    atd.translated_strings.empty_object_msg +
                    "</div>"
                );
                $.unblockUI();
              } else {
                var quantity = $(".atd-qty").val();
                var variation_id = atd.global_variation_id;
                var cart_item_key = atd.query_vars["edit"];
                if (typeof cart_item_key === "undefined") cart_item_key = "";
                var tpl = atd.query_vars["tpl"];
                if (typeof tpl === "undefined") tpl = "";
                var frm_data = new FormData();
                frm_data.append("variation_id", variation_id);
                frm_data.append("variations", JSON.stringify(variations));
                frm_data.append("format", atd.output_format);
                frm_data.append("action", "add_custom_design_to_cart");
                frm_data.append("cart_item_key", cart_item_key);
                frm_data.append("tpl", tpl);
                frm_data.append(
                  "final_canvas_parts",
                  atd_editor.final_canvas_parts
                );
                frm_data.append("quantity", quantity);
                if (typeof window.atd_key !== "undefined") {
                  frm_data.append("atd_key", window.atd_key);
                }
                var atd_design_options = JSON.stringify(get_design_options());
                frm_data.append("atd-design-opt", atd_design_options);
                frm_data = convert_final_canvas_parts_to_blob(frm_data);
                var tthat = $("form.formbuilt");
                if (tthat.length !== 0) {
                  tthat.validationEngine("validate", { showArrow: false });
                  var form_fields = tthat.serializeJSON();
                  delete form_fields.id_ofb;
                  var jsonform_fields = JSON.stringify(form_fields);
                  console.log(jsonform_fields);
                  frm_data.append("form_fields", jsonform_fields);
                }

                if ($(".atd-checkbox-add-name").is(":checked"))
                  frm_data.append("atd_team_add_name", "yes");
                else frm_data.append("atd_team_add_name", "no");

                if ($(".atd-checkbox-add-number").is(":checked"))
                  frm_data.append("atd_team_add_number", "yes");
                else frm_data.append("atd_team_add_number", "no");

                if (
                  $(".atd-checkbox-add-number").is(":checked") ||
                  $(".atd-checkbox-add-number").is(":checked")
                )
                  frm_data.append(
                    "atd_team_data_recap",
                    JSON.stringify(atd_editor.get_team_data_recap())
                  );

                $.ajax({
                  type: "POST",
                  url: ajax_object.ajax_url,
                  data: frm_data,
                  processData: false,
                  contentType: false,
                }).done(function (data) {
                  if (atd_editor.is_json(data)) {
                    var response = JSON.parse(data);
                    if ($("#atd-parts-bar > li").length > 1)
                      $("#atd-parts-bar > li").first().click();
                    else reload_first_part_data();
                    if (atd.redirect_after === 1 && response.success) {
                      $(location).attr("href", response.url);
                    } else {
                      $("#debug").html(response.message);
                      $.ajax($fragment_refresh);
                      $.unblockUI();
                    }
                  } else {
                    $("#debug").html(data);
                    $.ajax($fragment_refresh);
                    $.unblockUI();
                  }
                });
              }
            }
          );
        }
      });
    });

    //ninja form validation
    var atd_ninja_form_validation = function () {
      var deferred = new $.Deferred();
      if ($(".atd-container .ninja-forms-form").length > 0) {
        $(".atd-container form.ninja-forms-form").each(function () {
          var form = $(this);
          if (form.find('input[type="submit"]').length < 1) {
            form.append(
              '<input type="submit" class="ninja-forms-field" style="display:none;"/>'
            );
          }
          var form_id = form.attr("id");
          $("#" + form_id).submit();
          $(document).on("submitResponse.example", function (e, response) {
            form.show();
            if (response.success) {
              deferred.resolve(true);
            } else {
              if ($("#atd-parts-bar > li").length > 1) {
                $("#atd-parts-bar > li").first().click();
              } else reload_first_part_data();
              deferred.resolve(false);
            }
          });
        });
      } else {
        deferred.resolve(true);
      }
      return deferred.promise();
    };

    //Serialize ninja form
    function get_design_options() {
      var atd_desing_opt = {};
      var result = {};
      jQuery(".atd-container .ninja-forms-form-wrap")
        .find(
          "input[type=text], input[type=checkbox], input[type=radio], input[type=number], select, textarea, input[name=_form_id]"
        )
        .each(function (index) {
          var name = jQuery(this).attr("name");
          if (name !== "" && typeof name !== "undefined") {
            var type = jQuery(this).attr("type");
            var val = "";
            if (type === "radio") {
              if (
                jQuery(".ninja-forms-form-wrap [name=" + name + "]:checked")
                  .length
              )
                val = jQuery(
                  ".ninja-forms-form-wrap [name=" + name + "]:checked"
                ).val();
              else val = "";
            } else if (type === "checkbox") {
              if (jQuery(this).parents(".list-checkbox-wrap").length > 0) {
                val = result.hasOwnProperty(name) ? result[name] : "";
                if (jQuery(this).is(":checked")) {
                  val += jQuery(this).val() + ": checked; ";
                } else {
                  val += jQuery(this).val() + ": unchecked; ";
                }
              } else {
                if (jQuery(this).is(":checked")) {
                  val = " checked";
                } else {
                  val = " unchecked";
                }
              }
            } else if (jQuery.isArray(jQuery(this).val())) {
              var tpm_val = jQuery(this).val();
              jQuery.each(tpm_val, function (index, single_val) {
                if (index < tpm_val.length - 1) {
                  val += single_val + " | ";
                } else {
                  val += single_val;
                }
              });
            } else {
              val = jQuery(this).val();
            }

            result[name] = val;
          }
        });
      var output = {};
      if (jQuery(".atd-qty").length > 0) {
        var opt_price = jQuery(".atd-qty").first().attr("opt_price");
        if (opt_price !== "undefined") {
          output["opt_price"] = opt_price;
        }
        output["atd_design_opt_list"] = result;
        return output;
      } else {
        output["atd_design_opt_list"] = result;
        return output;
      }
    }

    if (typeof wc_cart_fragments_params !== "undefined") {
      var $fragment_refresh = {
        url: wc_cart_fragments_params.wc_ajax_url
          .toString()
          .replace("%%endpoint%%", "get_refreshed_fragments"),
        type: "POST",
        success: function (data) {
          if (data && data.fragments) {
            $.each(data.fragments, function (key, value) {
              $(key).replaceWith(value);
            });
            var test = JSON.stringify(data.fragments);
            $(document.body).trigger("wc_fragments_refreshed");
            $(".wc-mini-cart").html(test);
          }
        },
      };
    }

    $(
      "#lock-mvt-x, #lock-mvt-y, #lock-scl-x, #lock-scl-y, #lock-Deletion"
    ).change(function (e) {
      var property = $(this).data("property");
      var selected_object =
        atd_editor.canvas[atd_editor.selected_part].getActiveObject();
      var selected_group =
        atd_editor.canvas[atd_editor.selected_part].getActiveGroup();
      if (selected_object !== null) {
        if ($(this).is(":checked")) selected_object[property] = true;
        else selected_object[property] = false;
        atd_editor.save_canvas();
      } else if (selected_group !== null) {
        if ($(this).is(":checked")) selected_group[property] = true;
        else selected_group[property] = false;
        atd_editor.save_canvas();
      }
    });

    $(".post-type-atd-template #publish").click(function (e) {
      e.preventDefault();
      loop_through_parts(
        atd.output_loop_delay,
        generate_final_canvas_part,
        function () {
          if (jQuery.isEmptyObject(atd_editor.final_canvas_parts)) {
            alert(atd.translated_strings.empty_object_msg);
            $.unblockUI();
          } else {
            var frm_data = new FormData();
            frm_data.append("action", "save_canvas_to_session");
            frm_data = convert_final_canvas_parts_to_blob(frm_data);
            $.ajax({
              type: "POST",
              url: ajax_object.ajax_url,
              data: frm_data,
              processData: false,
              contentType: false,
            }).done(function (data) {
              $("#post").submit();
            });
          }
        }
      );
    });
    //Generate design for output
    function generate_final_canvas_part(part_index) {
      generate_canvas_part(part_index, false);
    }

    function generate_canvas_part(part_index, preview) {
      atd_editor.selected_part = part_index;
      preview = typeof preview !== "undefined" ? preview : true;
      var data_id = $("#atd-parts-bar > li:eq(" + part_index + ")").attr(
        "data-id"
      );
      var data_part_img = $("#atd-parts-bar > li:eq(" + part_index + ")").attr(
        "data-url"
      );
      if (typeof atd_editor.serialized_parts[data_id] === "undefined") {
        atd_editor.serialized_parts[data_id] = ["{}"];
      }
      atd_editor.canvas[atd_editor.selected_part].loadFromJSON(
        atd_editor.serialized_parts[data_id][
          atd_editor.canvasManipulationsPosition[data_id]
        ],
        function () {
          applyImageFilters();
          load_background_overlay_if_needed(
            atd_editor.selected_part,
            function () {
              var multiplier =
                atd.canvas[data_id].output_w /
                atd_editor.canvas[atd_editor.selected_part].getWidth();
              if (preview) multiplier = 1;
              //We split the multiplier per 2 if we're in retina mode
              if (
                window.devicePixelRatio !== 1 &&
                atd.responsive !== 1 &&
                atd.enable_retina === "yes"
              ) {
                multiplier = multiplier / 2;
              }
              var image = atd_editor.canvas[atd_editor.selected_part].toDataURL(
                {
                  format: atd.output_format,
                  multiplier: multiplier,
                  quality: 1,
                }
              );
              var svg = "";
              if (atd.generate_svg)
                svg = atd_editor.canvas[atd_editor.selected_part].toSVG();
              var blob_image = dataURItoBlob(image);
              if (preview) {
                var modal_content = "";
                if (atd.watermark) {
                  var frm_data = new FormData();
                  frm_data.append("action", "get_watermarked_preview");
                  frm_data.append("watermark", atd.watermark);
                  frm_data.append("format", atd.output_format);
                  frm_data.append("product-id", atd.global_variation_id);
                  frm_data.append("image", blob_image);
                  //frm_data = convert_final_canvas_parts_to_blob(frm_data);

                  $.ajax({
                    type: "POST",
                    url: ajax_object.ajax_url,
                    data: frm_data,
                    processData: false,
                    contentType: false,
                  }).done(function (data) {
                    if (atd_editor.is_json(data)) {
                      var response = JSON.parse(data);
                      // if (data_part_img)
                      //     modal_content = "<div data-name='" + data_id + "' id='preview-" + data_id + "' style='background-image:url(" + data_part_img + ");'><img src='" + response.url + "'></div>";
                      // else
                      //     modal_content = "<div data-name='" + data_id + "' id='preview-" + data_id + "'><img src='" + response.url + "'></div>";
                      // $("#atd-modal .omodal-body").append(modal_content);
                      if (data_part_img) {
                        modal_content =
                          '<div class="atd-preview-prev-des-item"><header>' +
                          data_id +
                          '</header><div class="atd-preview-prev-des-img-container"><div class="atd-preview-prev-des-img" style="background-image:url(' +
                          data_part_img +
                          ')"><img src="' +
                          response.url +
                          '"></div></div></div>';
                      } else {
                        modal_content =
                          '<div class="atd-preview-prev-des-item"><header>' +
                          data_id +
                          '</header><div class="atd-preview-prev-des-img-container"><div class="atd-preview-prev-des-img"><img src="' +
                          response.url +
                          '"></div></div></div>';
                      }
                      $(".atd-preview-prev-des-inner")
                      .trigger("add.owl.carousel", modal_content)
                      .trigger("refresh.owl.carousel");
                      $(".atd-preview-prev-des-inner").trigger("to.owl.carousel", [0, 500]);
                      // $(".atd-preview-prev-des-inner").append(modal_content);
                    } else {
                      $("#debug").html(data);
                    }
                  });
                } else {
                  // if (data_part_img)
                  //     modal_content = "<div data-name='" + data_id + "' id='preview-" + data_id + "' style='background-image:url(" + data_part_img + ");'><img src='" + image + "'></div>";
                  // else
                  //     modal_content = "<div data-name='" + data_id + "' id='preview-" + data_id + "'><img src='" + image + "'></div>";
                  // $("#atd-modal .omodal-body").append(modal_content);
                  
                  if (data_part_img) {
                    modal_content =
                      '<div class="atd-preview-prev-des-item"><header>' +
                      data_id +
                      '</header><div class="atd-preview-prev-des-img-container"><div class="atd-preview-prev-des-img" style="background-image:url(' +
                      data_part_img +
                      ')"><img src="' +
                      image +
                      '"></div></div></div>';
                  } else {
                    modal_content =
                      '<div class="atd-preview-prev-des-item"><header>' +
                      data_id +
                      '</header><div class="atd-preview-prev-des-img-container"><div class="atd-preview-prev-des-img"><img src="' +
                      response.url +
                      '"></div></div></div>';
                  }
                  
                  $(".atd-preview-prev-des-inner")
                      .trigger("add.owl.carousel", [modal_content])
                      .trigger("refresh.owl.carousel");
                      
                  $(".atd-preview-prev-des-inner").trigger("to.owl.carousel", [0, 500]);
                  // $(".atd-preview-prev-des-inner").append(modal_content);
                }
              } else {
                var canvas_obj = $.parseJSON(
                  atd_editor.serialized_parts[data_id][
                    atd_editor.canvasManipulationsPosition[data_id]
                  ]
                );
                var layers = [];
                if (atd.print_layers) {
                  var objects = canvas_obj.objects;
                  $.each(objects, function (key, curr_object) {
                    var tmp_canvas_obj = canvas_obj;
                    tmp_canvas_obj.objects = [curr_object];
                    var tmp_canvas_json = JSON.stringify(tmp_canvas_obj);
                    atd_editor.canvas[atd_editor.selected_part].loadFromJSON(
                      tmp_canvas_json,
                      function () {
                        applyImageFilters();
                        atd_editor.canvas[
                          atd_editor.selected_part
                        ].renderAll.bind(
                          atd_editor.canvas[atd_editor.selected_part]
                        );
                        //Removes overlay not included from layers
                        load_background_overlay_if_needed(
                          atd_editor.selected_part,
                          "",
                          true
                        );
                        var multiplier =
                          atd.canvas[data_id].output_w /
                          atd_editor.canvas[
                            atd_editor.selected_part
                          ].getWidth();
                        var layer = atd_editor.canvas[
                          atd_editor.selected_part
                        ].toDataURL({
                          format: atd.output_format,
                          multiplier: multiplier,
                          quality: 1,
                        });
                        var blob_layer = dataURItoBlob(layer);
                        layers.push(blob_layer);
                        //Loads the complete canvas before the save later otherwise, we end up with the last layer loaded as part data
                        if (key === objects.length - 1) {
                          atd_editor.canvas[
                            atd_editor.selected_part
                          ].loadFromJSON(
                            atd_editor.serialized_parts[data_id][
                              atd_editor.canvasManipulationsPosition[data_id]
                            ]
                          );
                          applyImageFilters();
                        }
                      }
                    );
                  });
                }
                atd_editor.final_canvas_parts[data_id] = {
                  json: atd_editor.serialized_parts[data_id][
                    atd_editor.canvasManipulationsPosition[data_id]
                  ],
                  image: blob_image,
                  original_part_img: data_part_img,
                  layers: layers,
                  svg: svg,
                };
              }
              load_background_overlay_if_needed(atd_editor.selected_part);
            },
            true
          );
        }
      );
    }

    function dataURItoBlob(dataURI) {
      // convert base64/URLEncoded data component to raw binary data held in a string
      var byteString;
      if (dataURI.split(",")[0].indexOf("base64") >= 0)
        byteString = atob(dataURI.split(",")[1]);
      else byteString = unescape(dataURI.split(",")[1]);
      // separate out the mime component
      var mimeString = dataURI.split(",")[0].split(":")[1].split(";")[0];
      // write the bytes of the string to a typed array
      var ia = new Uint8Array(byteString.length);
      for (var i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
      }

      var blob = new Blob([ia], { type: mimeString });
      return blob;
    }

    function convert_final_canvas_parts_to_blob(frm_data) {
      $.each(atd_editor.final_canvas_parts, function (part_key, part_data) {
        $.each(part_data, function (data_key, data_value) {
          if (data_key === "image")
            frm_data.append(part_key + "[" + data_key + "]", data_value);
          else if (data_key === "layers") {
            $.each(data_value, function (layer_index, layer_data) {
              frm_data.append("layers[" + part_key + "][]", layer_data);
            });
          } else
            frm_data.append(
              "final_canvas_parts[" + part_key + "][" + data_key + "]",
              data_value
            );
        });
      });
      return frm_data;
    }

    function rescale_canvas_if_needed() {
      if (atd.responsive !== 1) return false;
      var optimal_dimensions = get_optimal_canvas_dimensions();
      var scaleFactor = optimal_dimensions[0] / atd.canvas_w;
      if (scaleFactor !== 1) {
        atd_editor.scale_factor = scaleFactor;
        atd_editor.canvas[atd_editor.selected_part].setWidth(
          optimal_dimensions[0]
        );
        atd_editor.canvas[atd_editor.selected_part].setHeight(
          optimal_dimensions[1]
        );
        atd_editor.canvas[atd_editor.selected_part].setZoom(scaleFactor);
        atd_editor.canvas[atd_editor.selected_part].calcOffset();
        atd_editor.canvas[atd_editor.selected_part].renderAll();
      }

      applyImageFilters();
    }

    $(window).resize(function () {
      clearTimeout(resizeId);
      resizeId = setTimeout(handle_resize, 500);
    });

    function handle_resize() {
      $(".canvas-container").hide();
      rescale_canvas_if_needed();
      $(".canvas-container").show();
      $("#atd-parts-bar > li:eq(" + atd_editor.selected_part + ")").click();
    }

    //Shortcuts
    if (parseInt(atd.disable_shortcuts) !== 1) {
      $(document).keydown(function (e) {
        var selected_object =
          atd_editor.canvas[atd_editor.selected_part].getActiveObject();
        var selected_group =
          atd_editor.canvas[atd_editor.selected_part].getActiveGroup();
  
        if(atd_editor.isEmpty(selected_object)) {
            selected_object = null;
        }
        if(atd_editor.isEmpty(selected_group)) {
            selected_group = null;
        }
  
        if (e.which === 46)
          //Delete button
          $("#delete_btn").click();
        else if (e.which === 37) {
          //Left button
          if (selected_group !== null && !selected_group.get("lockMovementX")) {
            selected_group.set("left", selected_group.left - 1);
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            atd_editor.save_canvas();
          } else if (
            selected_object !== null &&
            !selected_object.get("lockMovementX")
          ) {
            selected_object.set("left", selected_object.left - 1);
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            atd_editor.save_canvas();
          }
        } else if (e.which === 39) {
          //Right button
          if (selected_group !== null && !selected_group.get("lockMovementX")) {
            selected_group.set("left", selected_group.left + 1);
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            atd_editor.save_canvas();
          } else if (
            selected_object !== null &&
            !selected_object.get("lockMovementX")
          ) {
            selected_object.set("left", selected_object.left + 1);
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            atd_editor.save_canvas();
          }
        } else if (e.which === 38) {
          //Top button
          if (selected_group !== null && !selected_group.get("lockMovementY")) {
            e.preventDefault();
            selected_group.set("top", selected_group.top - 1);
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            atd_editor.save_canvas();
          } else if (
            selected_object !== null &&
            !selected_object.get("lockMovementY")
          ) {
            e.preventDefault();
            selected_object.set("top", selected_object.top - 1);
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            atd_editor.save_canvas();
          }
        } else if (e.which === 40) {
          //Bottom button
          if (selected_group !== null && !selected_group.get("lockMovementY")) {
            e.preventDefault();
            selected_group.set("top", selected_group.top + 1);
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            atd_editor.save_canvas();
          } else if (
            selected_object !== null &&
            !selected_object.get("lockMovementY")
          ) {
            e.preventDefault();
            selected_object.set("top", selected_object.top + 1);
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            atd_editor.save_canvas();
          }
        } else if (e.keyCode === 67 && e.ctrlKey) {
          //ctrl+c
          $("#copy_paste_btn").click();
        } else if (e.keyCode === 90 && e.ctrlKey) {
          //ctrl+z
          $("#undo-btn").click();
        } else if (e.keyCode === 89 && e.ctrlKey) {
          //ctrl+y
          $("#redo-btn").click();
        }
      });
    }

    $(".atd-rp-attribute").mouseenter(function () {
      $("#atd-rp-desc").html($(this).data("desc"));
    });

    $(".atd-rp-attribute").mouseout(function () {
      var default_desc = $(".atd-rp-attribute.selected").data("desc");
      $("#atd-rp-desc").html(default_desc);
    });

    $("canvas").bind("contextmenu", function (e) {
      return false;
    });

    $(document).on(
      "click",
      '[id$="color-selector"] .o-colorpicker_clear',
      function (event) {
        var parent_id = $(this).parents(".atd-colorpicker").attr("id");
        var selected_object =
          atd_editor.canvas[atd_editor.selected_part].getActiveObject();
        if (
          parent_id.indexOf("bg-color-selector") >= 0 &&
          selected_object !== null
        ) {
          selected_object.set("backgroundColor", "transparent");
          atd_editor.canvas[atd_editor.selected_part].renderAll();
          atd_editor.save_canvas();
        } else if (
          parent_id.indexOf("outline-color-selector") >= 0 &&
          selected_object !== null
        ) {
          selected_object.set("stroke", "transparent");
          atd_editor.canvas[atd_editor.selected_part].renderAll();
          atd_editor.save_canvas();
        } else if (
          parent_id.indexOf("color-selector") >= 0 &&
          selected_object !== null
        ) {
          selected_object.set("fill", "transparent");
          atd_editor.canvas[atd_editor.selected_part].renderAll();
          atd_editor.save_canvas();
        }
      }
    );

    atd_editor.findByCliperName = function (name) {
      return _(atd_editor.canvas[atd_editor.selected_part].getObjects())
        .filter({
          clipTeam: name,
        })
        .first();
    };

    atd_editor.remove_team_name_number = function (text) {
      var selected_object = atd_editor.findByCliperName(text);
      atd_editor.canvas[atd_editor.selected_part].remove(selected_object);
      atd_editor.canvas[atd_editor.selected_part].calcOffset();
      atd_editor.canvas[atd_editor.selected_part].renderAll();
      atd_editor.save_canvas();
    };

    atd_editor.click_part = function (team_name_number) {
      var side = $(
        "#atd-team-" +
          team_name_number +
          "-tool .atd-team-" +
          team_name_number +
          "-side"
      ).val();
      atd_editor.click_part_by_name(side);
    };

    atd_editor.click_part_by_name = function (side) {
      side = side
        .split(/\s+/)
        .map((s) => s.charAt(0).toUpperCase() + s.substring(1).toLowerCase())
        .join(" ");
      $("#atd-parts-bar li[data-tooltip-title='" + side + "']").click();
    };

    atd_editor.team_name_number_height = function (team_name_number) {
      return $(
        "#atd-team-" +
          team_name_number +
          "-tool .atd-team-" +
          team_name_number +
          "-height"
      ).val();
    };

    atd_editor.convert_to_px = function (dimensions, unit) {
      var dimensions = dimensions;
      var unit = unit;
      if (typeof unit != "undefined" && typeof dimensions != "undefined") {
        switch (unit) {
          case "pt":
            dimensions = dimensions * 1.333;
            break;
          case "mm":
            dimensions = dimensions * 118;
            break;
          case "px":
            dimensions = dimensions;
            break;
          case "inch":
            dimensions = dimensions * 300;
            break;
          case "feet":
            dimensions = dimensions * 3600;
            break;
          case "cm":
            dimensions = dimensions * 120;
            break;
          default:
            break;
        }
        return parseFloat(dimensions).toFixed(2);
      }
    };

    atd_editor.get_team_data_recap = function () {
      var atd_team_data_recap = [];
      var i = 0;
      $(".atd-team-row").each(function (e) {
        var name = $(this).find(".atd-team-names-list").val();
        var number = $(this).find(".atd-team-numbers-list").val();
        var size = $(this).find(".atd-team-sizes-list").val();
        if ("" !== name && "" !== number) {
          atd_team_data_recap.push({ name: name, number: number, size: size });
        }
        i++;
      });
      return atd_team_data_recap;
    };

    $(document).on("click", "#txt-bg-color-selector-none", function (e) {
      if (
        null !== atd_editor.canvas[atd_editor.selected_part].getActiveObject()
      ) {
        atd_editor.canvas[atd_editor.selected_part]
          .getActiveObject()
          .set("backgroundColor", "transparent");
        atd_editor.canvas[atd_editor.selected_part].renderAll();
        $("#txt-bg-color-selector span.atd-color-view").css(
          "background-color",
          "transparent"
        );
      }
    });

    $(document).on("change", "#atd-checkbox-none-bg", function (e) {
      if ($(this).is(":checked")) {
        if (
          null !== atd_editor.canvas[atd_editor.selected_part].getActiveObject()
        ) {
          atd_editor.canvas[atd_editor.selected_part]
            .getActiveObject()
            .set("backgroundColor", "transparent");
          atd_editor.canvas[atd_editor.selected_part].renderAll();
        }
      } else {
        if (
          null !== atd_editor.canvas[atd_editor.selected_part].getActiveObject()
        ) {
          atd_editor.canvas[atd_editor.selected_part]
            .getActiveObject()
            .set("backgroundColor", $("#txt-bg-color-selector").val());
          atd_editor.canvas[atd_editor.selected_part].renderAll();
          atd_editor.change_item_color(
            "txt-bg-color-selector",
            $("#txt-bg-color-selector").val()
          );

          $("#txt-bg-color-selector").show();
          $("#txt-bg-color-selector").spectrum(
            "set",
            $("#txt-bg-color-selector").val()
          );
        }
      }
    });

    // Add to cart for related product
   
    $(document).on("click", ".atd-btn-cart-go-add-to-cart", function (e) {
        var get_data = $(".atd-preview-box-quantity input").serializeArray();

      $("#debug").html("");

      $(".atd-preview-box-quantity")
      .find(".atd-icon-quantity-cross")
      .click();
      if (atd.clip_include_in_output === "no")
        atd_editor.canvas[atd_editor.selected_part].clipTo = null;
      loop_through_parts(
        atd.output_loop_delay,
        generate_final_canvas_part,
        function () {
          if (jQuery.isEmptyObject(atd_editor.final_canvas_parts)) {
            $("#debug").html(
              "<div class='atd-failure'>" +
                atd.translated_strings.empty_object_msg +
                "</div>"
            );
          } else {
            var cart_item_key = atd.query_vars["edit"];
            if (typeof cart_item_key === "undefined") cart_item_key = "";
            var tpl = atd.query_vars["tpl"];
            if (typeof tpl === "undefined") tpl = "";

            if (typeof window.atd_key !== "undefined") {
              var atd_key = window.atd_key;
            }
            var frm = new FormData();
            frm.append("action", "add_related_custom_products_to_carts");
            frm.append("final_canvas_parts", atd_editor.final_canvas_parts);
            frm.append("variations_data", JSON.stringify(get_data));
            frm.append("tpl", tpl);
            frm.append("atd_key", atd_key);
            frm.append("cart_item_key", cart_item_key);
            frm.append("format", atd["output_format"]);
            frm = convert_final_canvas_parts_to_blob(frm);

            if ($(".atd-checkbox-add-name").is(":checked"))
              frm.append("atd_team_add_name", "yes");
            else frm.append("atd_team_add_name", "no");

            if ($(".atd-checkbox-add-number").is(":checked"))
              frm.append("atd_team_add_number", "yes");
            else frm.append("atd_team_add_number", "no");

            if (
              $(".atd-checkbox-add-number").is(":checked") ||
              $(".atd-checkbox-add-number").is(":checked")
            )
              frm.append(
                "atd_team_data_recap",
                JSON.stringify(atd_editor.get_team_data_recap())
              );

            $.ajax({
              type: "POST",
              url: ajax_object.ajax_url,
              data: frm,
              processData: false,
              contentType: false
            }).success(function (data) {
              var msg = JSON.parse(data);
              $("#debug").html(msg.message);
              $.ajax($fragment_refresh);
              $.unblockUI();
              $(".atd-debug-wrap").addClass("atd-active");
              $("#atd-parts-bar").children().first().click();
              //$(".atd-preview-box-quantity").removeClass("atd-show");
              
              //$(".atd-shadow-quantity").removeClass("atd-show");
            });
          }
        }
      );
    });

    $(document).on("change keyup keydown", ".atd-input-number", function(){
      update_price();
    })

  });
  return atd_editor;
})(jQuery, atd_EDITOR);
