var atd_EDITOR = (function ($, atd_editor) {
  "use strict";

  /**
   * All of the code for your public-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */

  /**
   * 
   *  Desktop JS Begin
   * 
   */

  $(document).ready(function () {
    /* Begin Perfect Scrollbar Script */

    $(
      ".atd-textarea-field textarea, .atd-editing-text, .atd-filter-container, .atd-clippart-group-container, .atd-preview-clippart-group, .atd-preview-upolad-container, .atd-team-tools-wrap, .atd-my-design-container, .atd-font-drop-down, .atd-team-list-section, .atd-totals-team-variations, .atd-preview-box-add-cart-inner, .atd-add-cart-content-details, .atd-preview-box-quantity-inner, .atd-ui-qty-details:last-child"
    ).perfectScrollbar({
      suppressScrollX: true,
    });

    // $(".atd-preview-prev-des-inner").perfectScrollbar();

    /* End Perfect Scrollbar Script */

    /* Begin Tabs Script */

    var afficheTabs = function ($li, duration) {
      if (duration === undefined) {
        duration = 500;
      }

      if ($li.hasClass("active")) {
        return false;
      }

      var targetLi = $li.attr("data-title");

      var $targetTabContent = $(
        ".atd-tab-tools-container .atd-tab-tools-content[data-title='" +
          targetLi +
          "']"
      );

      if (
        $(".atd-team-container .atd-checkbox-input").not(":checked").length < 2
      ) {
        $(".atd-team-inner").removeClass("atd-active");
        $(".atd-team-container").addClass("atd-active");
      }
      if (
        $(".atd-team-container .atd-checkbox-input").not(":checked").length ===
        2
      ) {
        $(".atd-team-inner").addClass("atd-active");
        $(".atd-team-container").removeClass("atd-active");
      }

      // var index = $li.index();

      $li.siblings(".active").removeClass("active");

      $li.addClass("active");

      // $targetTabContent.siblings(':visible').fadeOut(duration, function() {

      //     $targetTabContent.fadeIn(duration);

      // });

      // $targetTabContent.siblings('.active').removeClass('active');

      // $targetTabContent.addClass('active');

      $targetTabContent.siblings(":visible").slideUp("fast", function () {
        $targetTabContent.show("slow");
      });
    };

    $(".atd-menu .atd-tab-item").click(function (e) {
      // var is_team_bar = $(this).attr("data-title") == "team";
      // var color_is_used =
      //   $(".atd-container-add-cart-item .owl-item").length > 1;

      // if (is_team_bar && color_is_used) return;
      if (
        $(this).attr("data-title") == "team" &&
        $(".atd-container-add-cart-item .owl-item").length > 1
      ) {
        $(
          ".atd-preview-box-add-team-information, .atd-shadow-add-team-information"
        ).addClass("atd-show");
        $("body").css("overflow", "hidden");
        return;
      }

      var $li = afficheTabs($(this));
    });

    /** Begin Curved Checked Script */

    $(".atd-checkbox-curved").on("input", function (e) {
      $(".atd-curved-content").toggleClass("atd-show");
    });

    $(".atd-checkbox-none-bg").on("input", function () {
      if ($(this).is(":checked")) {
        $(".atd-bg").css({
          pointerEvents: "none",
          opacity: "0.5",
        });
      } else {
        $(".atd-bg").css({
          pointerEvents: "auto",
          opacity: "1",
        });
      }
    });

    if ($(".atd-checkbox-none-bg").is(":checked")) {
      $(".atd-bg").css({
        pointerEvents: "none",
        opacity: "0.5",
      });
    }

    /** End Curved Checked Script */

    /** Begin Switch Between Back and Front Script  */

    $(".atd-navigation-canvas .atd-navigation-canvas-item").click(function (e) {
      $(this).siblings(".active").removeClass("active");

      // $(this).closest('.atd-navigation-canvas').find(".atd-navigation-canvas-item").removeClass('active');

      $(this).addClass("active");

      var targetLi = $(this).attr("data-title");

      var $targetTabContent = $(
        ".atd-canvas-item.atd-column-center .atd-canvas-inner[data-title='" +
          targetLi +
          "']"
      );

      switch (targetLi) {
        case "Front":
          $(
            ".atd-canvas-item.atd-column-center .atd-canvas-inner[data-title='Back']"
          ).css("left", "150%");
  
          $(
            ".atd-canvas-item.atd-column-center .atd-canvas-inner[data-title='Left']"
          ).css("left", "150%");
  
          $(
            ".atd-canvas-item.atd-column-center .atd-canvas-inner[data-title='Right']"
          ).css("left", "150%");
  
          $(
            ".atd-canvas-item.atd-column-center .atd-canvas-inner[data-title='Chest']"
          ).css("left", "150%");
  
          $targetTabContent.css("left", "50%");
          break;
        case "Back":
          $(
            ".atd-canvas-item.atd-column-center .atd-canvas-inner[data-title='Front']"
          ).css("left", "-150%");
  
          $(
            ".atd-canvas-item.atd-column-center .atd-canvas-inner[data-title='Left']"
          ).css("left", "150%");
  
          $(
            ".atd-canvas-item.atd-column-center .atd-canvas-inner[data-title='Right']"
          ).css("left", "150%");
  
          $(
            ".atd-canvas-item.atd-column-center .atd-canvas-inner[data-title='Chest']"
          ).css("left", "150%");
  
          $targetTabContent.css("left", "50%");
          break;
        case "Left":
          $(
            ".atd-canvas-item.atd-column-center .atd-canvas-inner[data-title='Front']"
          ).css("left", "-150%");
  
          $(
            ".atd-canvas-item.atd-column-center .atd-canvas-inner[data-title='Back']"
          ).css("left", "-150%");
  
          $(
            ".atd-canvas-item.atd-column-center .atd-canvas-inner[data-title='Right']"
          ).css("left", "150%");
  
          $(
            ".atd-canvas-item.atd-column-center .atd-canvas-inner[data-title='Chest']"
          ).css("left", "150%");
  
          $targetTabContent.css("left", "50%");
          break;
        case "Right":
          $(
            ".atd-canvas-item.atd-column-center .atd-canvas-inner[data-title='Front']"
          ).css("left", "-150%");
  
          $(
            ".atd-canvas-item.atd-column-center .atd-canvas-inner[data-title='Back']"
          ).css("left", "-150%");
  
          $(
            ".atd-canvas-item.atd-column-center .atd-canvas-inner[data-title='Left']"
          ).css("left", "-150%");
  
          $(
            ".atd-canvas-item.atd-column-center .atd-canvas-inner[data-title='Chest']"
          ).css("left", "150%");
  
          $targetTabContent.css("left", "50%");
          break;
        case "Chest":
          $(
            ".atd-canvas-item.atd-column-center .atd-canvas-inner[data-title='Front']"
          ).css("left", "-150%");
  
          $(
            ".atd-canvas-item.atd-column-center .atd-canvas-inner[data-title='Back']"
          ).css("left", "-150%");
  
          $(
            ".atd-canvas-item.atd-column-center .atd-canvas-inner[data-title='Left']"
          ).css("left", "-150%");
  
          $(
            ".atd-canvas-item.atd-column-center .atd-canvas-inner[data-title='Right']"
          ).css("left", "-150%");
  
          $targetTabContent.css("left", "50%");
          break;
      }
    });

    $(".atd-text-style-option-item").click(function (e) {
      //$(this).siblings('.atd-active').removeClass('atd-active');

      //$(this).addClass('atd-active');

      $(this).toggleClass("atd-active");

      // $(this).closest(".atd-text-style-option").addClass("atd-active");
    });

    $(".atd-text-decoration-option-item").click(function (e) {
      $(this).siblings(".atd-active").removeClass("atd-active");

      $(this).addClass("atd-active");

      //$(this).toggleClass('atd-active');

      // $(this).closest(".atd-text-style-option").addClass("atd-active");
    });

    $(".atd-text-alignement-option-item").click(function (e) {
      $(this).siblings(".atd-active").removeClass("atd-active");

      $(this).addClass("atd-active");

      //$(this).toggleClass('atd-active');

      // $(this).closest(".atd-text-style-option").addClass("atd-active");
    });

    /** End Switch Between Back and Front Script  */

    /* End Tabs Script */

    /**
     * Begin Contextmenu Script
     *
     * The context menu or right click has been disabled on this section.
     *
     * Show or hide context menu.
     */

    // $('#atd-canvas-box').contextmenu(function(e) {

    //     e.preventDefault();

    // });

    /*$(".atd-btn").click(function() {

            if ($("#new-text").val().length > 0) {

                $(".atd-editing-text").addClass("atd-active");

                $(".atd-add-text").removeClass("atd-active");

            }

        });*/

    /* Begin Clippart Script */

    $(".atd-clippart-group-item").click(function () {
      $(this)
        .closest(".atd-clippart-group-container")
        .removeClass("atd-active");

      var targetLi = $(this).attr("data-title");

      var $targetTabContent = $(
        ".atd-clippart-container .atd-preview-clippart-group[data-title='" +
          targetLi +
          "']"
      );

      $(".atd-clippart-inner").removeClass("atd-active");

      $(".atd-clippart-container").addClass("atd-active");

      $(".atd-clipart-head .atd-title").text(targetLi);

      $(".atd-preview-clippart-group")
        .siblings(":visible")
        .removeClass("atd-active");

      $targetTabContent.addClass("atd-active");
    });

    /*$(".atd-preview-clippart-item").click(function() {

            $(".atd-clippart-inner").removeClass("atd-active");

            $(".atd-clippart-container").removeClass("atd-active");

            $(this).closest(".atd-tab-tools-content").find(".atd-clippart-edit-inner").addClass("atd-active");

        });*/

    $(".atd-icon-cli-cross").click(function () {
      $(".atd-clippart-group-container").addClass("atd-active");

      $(".atd-clippart-inner").addClass("atd-active");

      $(".atd-clippart-container").removeClass("atd-active");
    });

    $(".atd-icon-edit-cli-cross").click(function () {
      $(".atd-clippart-inner").addClass("atd-active");

      $(".atd-clippart-inner")
        .find(".atd-clippart-group-container")
        .addClass("atd-active");

      $(".atd-clippart-container").removeClass("atd-active");

      $(".atd-tab-tools-content .atd-clippart-edit-inner").removeClass(
        "atd-active"
      );
    });

    /* End Clippart Script */

    /* Begin Team Script */

    $(".atd-btn-team.atd-step-1").click(function () {
      $(".atd-team-inner").removeClass("atd-active");

      $(".atd-team-container").addClass("atd-active");
    });

    $(".atd-team-container .atd-icon-team-cross").click(function () {
      $(".atd-team-inner").addClass("atd-active");

      $(".atd-team-container").removeClass("atd-active");
    });

    function atd_get_team_name_tools(team_name_tools) {
      if ($(".atd-checkbox-add-name").is(":checked")) {
        var team_name_default_height =
          atd_EDITOR.team_name_number_height("name");
        var team_name_default_unit = $(
          "#atd-team-name-tool .atd-team-name-height option[value='" +
            team_name_default_height +
            "']"
        ).attr("data-unit");
        var team_name_default_height = atd_EDITOR.convert_to_px(
          team_name_default_height,
          team_name_default_unit
        );
        atd_EDITOR.click_part("name");
        atd_EDITOR.add_text(
          "EXAMPLE",
          false,
          false,
          team_name_default_height,
          atd.canvas[
            $("#atd-parts-bar > li:eq(" + atd_EDITOR.selected_part + ")").attr(
              "data-id"
            )
          ].canvas_width * 0.2,
          false
        );
      } else {
        atd_EDITOR.click_part("name");
        atd_EDITOR.remove_team_name_number("#atd-team-name");
        $(".atd-select.atd-team-name-side option:first-child").prop(
          "selected",
          true
        );
        $(
          ".atd-select.atd-team-name-height option[data-default-value='yes']"
        ).prop("selected", true);
        $("#team-name-color-selector").spectrum("set", "#4f71b9");
      }
    }

    $(".atd-checkbox-add-name").on("input", function (e) {
      $(".atd-team-name-content").toggleClass("atd-show");

      atd_get_team_name_tools();
    });

    $(
      ".atd-team-name-side, .atd-team-name-height, #team-name-color-selector"
    ).on("focus", function (e) {
      atd_EDITOR.click_part("name");
      var selected_object = atd_EDITOR.findByCliperName("#atd-team-name");
      atd_EDITOR.canvas[atd_EDITOR.selected_part].setActiveObject(
        selected_object
      );
    });

    $(".atd-team-name-side").on("change", function (e) {
      var selected_object = atd_EDITOR.findByCliperName("#atd-team-name");
      var selected_part = atd_EDITOR.selected_part;
      atd_EDITOR.click_part("name");
      atd_EDITOR.canvas[selected_part].remove(selected_object);
      atd_EDITOR.canvas[selected_part].calcOffset();
      atd_EDITOR.canvas[selected_part].renderAll();
      atd_EDITOR.canvas[atd_EDITOR.selected_part].add(selected_object);
      atd_EDITOR.canvas[atd_EDITOR.selected_part].calcOffset();
      atd_EDITOR.canvas[atd_EDITOR.selected_part].renderAll();
      atd_EDITOR.canvas[atd_EDITOR.selected_part].setActiveObject(
        selected_object
      );
      atd_EDITOR.save_canvas();
    });

    $(".atd-team-name-height").on("change", function (e) {
      var selected_object = atd_EDITOR.findByCliperName("#atd-team-name");
      atd_EDITOR.canvas[atd_EDITOR.selected_part].setActiveObject(
        selected_object
      );
      var height = atd_EDITOR.team_name_number_height("name");
      atd_EDITOR.canvas[atd_EDITOR.selected_part]
        .getActiveObject()
        .setFontSize(height);
      atd_EDITOR.canvas[atd_EDITOR.selected_part].renderAll();
      atd_EDITOR.save_canvas();
    });

    function atd_get_team_number_tools() {
      if ($(".atd-checkbox-add-number").is(":checked")) {
        var team_number_default_height =
          atd_EDITOR.team_name_number_height("number");
        var team_number_default_unit = $(
          "#atd-team-number-tool .atd-team-number-height option[value='" +
            team_number_default_height +
            "']"
        ).attr("data-unit");
        var team_number_default_height = atd_EDITOR.convert_to_px(
          team_number_default_height,
          team_number_default_unit
        );
        atd_EDITOR.click_part("number");
        atd_EDITOR.add_text(
          "00",
          false,
          false,
          team_number_default_height,
          false,
          atd.canvas[
            $("#atd-parts-bar > li:eq(" + atd_EDITOR.selected_part + ")").attr(
              "data-id"
            )
          ].canvas_width * 0.4
        );
      } else {
        atd_EDITOR.click_part("number");
        atd_EDITOR.remove_team_name_number("#atd-team-number");
        $(".atd-select.atd-team-number-side option:first-child").prop(
          "selected",
          true
        );
        $(
          ".atd-select.atd-team-number-height option[data-default-value='yes']"
        ).prop("selected", true);
        $("#team-number-color-selector").spectrum("set", "#4f71b9");
      }
    }

    $(".atd-checkbox-add-number").on("input", function (e) {
      $(".atd-team-number-content").toggleClass("atd-show");

      atd_get_team_number_tools();
    });

    $(
      ".atd-team-number-side, .atd-team-number-height, #team-number-color-selector"
    ).on("focus", function (e) {
      atd_EDITOR.click_part("number");
      var selected_object = atd_EDITOR.findByCliperName("#atd-team-number");
      atd_EDITOR.canvas[atd_EDITOR.selected_part].setActiveObject(
        selected_object
      );
    });

    $(".atd-team-number-side").on("change", function (e) {
      var selected_object = atd_EDITOR.findByCliperName("#atd-team-number");
      var selected_part = atd_EDITOR.selected_part;
      atd_EDITOR.click_part("number");
      atd_EDITOR.canvas[selected_part].remove(selected_object);
      atd_EDITOR.canvas[selected_part].calcOffset();
      atd_EDITOR.canvas[selected_part].renderAll();
      atd_EDITOR.canvas[atd_EDITOR.selected_part].add(selected_object);
      atd_EDITOR.canvas[atd_EDITOR.selected_part].calcOffset();
      atd_EDITOR.canvas[atd_EDITOR.selected_part].renderAll();
      atd_EDITOR.canvas[atd_EDITOR.selected_part].setActiveObject(
        selected_object
      );
      atd_EDITOR.save_canvas();
    });

    $(".atd-team-number-height").on("change", function (e) {
      var selected_object = atd_EDITOR.findByCliperName("#atd-team-number");
      atd_EDITOR.canvas[atd_EDITOR.selected_part].setActiveObject(
        selected_object
      );
      var height = atd_EDITOR.team_name_number_height("number");
      atd_EDITOR.canvas[atd_EDITOR.selected_part]
        .getActiveObject()
        .setFontSize(height);
      atd_EDITOR.canvas[atd_EDITOR.selected_part].renderAll();
      atd_EDITOR.save_canvas();
    });

    $(".atd-btn-team.atd-step-2").click(function () {
      // if (
      //   !$(".atd-checkbox-add-name").is(":checked") &&
      //   !$(".atd-checkbox-add-number").is(":checked")
      // ) {
      //   $(".atd-checkbox-add-name").click();
      //   $(".atd-checkbox-add-number").click();
      // }
      if (
        $(".atd-team-container .atd-checkbox-input").not(":checked").length ===
        2
      ) {
        $(".atd-checkbox-add-name, .atd-checkbox-add-number").click();
      }

      if (!$(".atd-checkbox-add-name").is(":checked")) {
        $(".atd-team-names-list").prop("disabled", true);
        $(".atd-team-names-list").addClass("isAtdDisabled");
      } else {
        $(".atd-team-names-list").prop("disabled", false);
        $(".atd-team-names-list").removeClass("isAtdDisabled");
      }
      if (!$(".atd-checkbox-add-number").is(":checked")) {
        $(".atd-team-numbers-list").prop("disabled", true);
        $(".atd-team-numbers-list").addClass("isAtdDisabled");
      } else {
        $(".atd-team-numbers-list").prop("disabled", false);
        $(".atd-team-numbers-list").removeClass("isAtdDisabled");
      }
      atd_EDITOR.canvas[atd_EDITOR.selected_part].deactivateAll().renderAll();
      $(".atd-shadow-team").addClass("atd-show");
      $(".atd-preview-box-team").addClass("atd-show");
      $("body").css("overflow", "hidden");
    });

    function count_names() {
      var count = 0;
      $(".atd-team-names-list").each(function () {
        if ("" !== $(this).val()) {
          count++;
        }
      });
      return count;
    }

    function count_numbers() {
      var count = 0;
      $(".atd-team-numbers-list").each(function () {
        if ("" !== $(this).val()) {
          count++;
        }
      });
      return count;
    }

    function count_items() {
      var count = 0;
      $(".atd-team-row").each(function () {
        var name = $(this).find(".atd-team-names-list").val();
        var number = $(this).find(".atd-team-numbers-list").val();
        var size = $(this).find(".atd-team-sizes-list").val();
        if ("" !== name && "" !== number && "" !== size) {
          count++;
        }
      });
      return count;
    }

    function get_team_totals_summary() {
      var countnames = count_names();
      var countnumbers = count_numbers();
      var countitems = count_items();

      $(".atd-totals-team-text .atd-total-name.atd-badge").text(countnames);
      $(".atd-totals-team-text .atd-total-number.atd-badge").text(countnumbers);
      $(".atd-totals-team-text .atd-total-item.atd-badge").text(countitems);

      if (1 === countnames)
        $(".atd-totals-team-text .atd-total-name-label").text("name");
      else $(".atd-totals-team-text .atd-total-name-label").text("names");

      if (1 === countnumbers)
        $(".atd-totals-team-text .atd-total-number-label").text("numbers");
      else $(".atd-totals-team-text .atd-total-number-label").text("numbers");

      if (1 === countitems)
        $(".atd-totals-team-text .atd-total-item-label").text("item");
      else $(".atd-totals-team-text .atd-total-item-label").text("items");

      var team_sizes_summary = get_team_sizes_summary();
      var team_sizes_summary_text = "";
      $.each(team_sizes_summary, function (key, value) {
        if (0 < value["totals"]) {
          team_sizes_summary_text +=
            "(" +
            value["items"] +
            "/" +
            value["totals"] +
            ") " +
            $(
              "select[name='atd-team-sizes-list[]']:eq(0) option[value='" +
                key +
                "']"
            ).text();
        }
      });
      $(".wb-item-variation-text").text(team_sizes_summary_text);

      var missingNames = missing_names();
      var missingNumbers = missing_numbers();

      $(".wb-team-help-duplicate-list").text(function () {
        return "";
      });

      $(".atd-team-row").each(function () {
        if (
          ($(this).find(".atd-team-names-list").val() !== "" &&
            $(this).find(".atd-team-numbers-list").val() !== "") ||
          ($(this).find(".atd-team-names-list").val() === "" &&
            $(this).find(".atd-team-numbers-list").val() === "")
        ) {
          $(this)
            .find(".atd-team-names-list, .atd-team-numbers-list")
            .removeClass("wb-isNan");
        }
      });

      $(".wb-team-help-duplicates")[
        isAddOrRemoveClass(missingNames, missingNumbers)
      ]("isHidden");

      if (isArrayNotEmpty(missingNames)) {
        $(".wb-team-help-missing-name")[
          isAddOrRemoveClass(missingNames, missingNumbers)
        ]("isHidden");

        $(missingNames).each(function (i, value) {
          showMissing(
            value.number,
            value.message,
            "wb-team-help-missing-name",
            "wb-missing-numb"
          );

          $(
            ".atd-team-row:eq(" +
              value.row +
              ") td:nth-child(1) .atd-team-names-list"
          ).addClass("wb-isNan");
        });
      } else {
        $(".wb-team-help-missing-name")[
          isAddOrRemoveClass(missingNames, missingNumbers, "name")
        ]("isHidden");
      }

      if (isArrayNotEmpty(missingNumbers)) {
        $(".wb-team-help-missing-numb")[
          isAddOrRemoveClass(missingNames, missingNumbers)
        ]("isHidden");

        $(missingNumbers).each(function (i, value) {
          showMissing(
            value.name,
            value.message,
            "wb-team-help-missing-numb",
            "wb-missing-name"
          );

          $(
            ".atd-team-row:eq(" +
              value.row +
              ") td:nth-child(2) .atd-team-numbers-list"
          ).addClass("wb-isNan");
        });
      } else {
        $(".wb-team-help-missing-numb")[
          isAddOrRemoveClass(missingNames, missingNumbers, "numb")
        ]("isHidden");
      }
    }

    function isArrayNotEmpty(arr) {
      return arr.length > 0;
    }

    function showMissing(targetText, message, selectorParent, classTarget) {
      $(
        "<li><span class=" +
          classTarget +
          ">#" +
          targetText +
          "</span>&nbsp;" +
          message +
          "</li>"
      ).appendTo("." + selectorParent + " .wb-team-help-duplicate-list");
    }

    function isAddOrRemoveClass(
      missingNames = false,
      missingNumbers = false,
      actionName = ""
    ) {
      return (isArrayNotEmpty(missingNames) && actionName === "name") ||
        (isArrayNotEmpty(missingNumbers) && actionName === "numb") ||
        actionName === ""
        ? "removeClass"
        : "addClass";
    }

    function missing_names() {
      var missing_names = [];

      $(".atd-team-row").each(function (e) {
        var name = $(this).find(".atd-team-names-list").val();
        var number = $(this).find(".atd-team-numbers-list").val();
        if ("" === name && "" !== number) {
          var missing_name = {
            row: e,
            number: number,
            message: " doesn’t have a name",
          };
          missing_names.push(missing_name);
        }
      });
      return missing_names;
    }

    function missing_numbers() {
      var missing_numbers = [];

      $(".atd-team-row").each(function (e) {
        var name = $(this).find(".atd-team-names-list").val();
        var number = $(this).find(".atd-team-numbers-list").val();
        if ("" !== name && "" === number) {
          var missing_number = {
            row: e,
            name: name,
            message: " doesn’t have a number",
          };
          missing_numbers.push(missing_number);
        }
      });
      return missing_numbers;
    }

    $(document).on(
      "change",
      ".atd-team-names-list, .atd-team-numbers-list, .atd-team-sizes-list",
      function () {
        get_team_totals_summary();
      }
    );

    $(document).on("click", ".atd-icon-team.fas.fa-trash", function () {
      get_team_totals_summary();
    });

    get_team_totals_summary();

    $(".atd-shadow-team").click(function () {
      $(this).removeClass("atd-show");

      $(".atd-preview-box-team").removeClass("atd-show");

      $("body").css("overflow", "scroll");
    });

    function get_team_sizes_summary() {
      var team_sizes_summary = {};
      $("select[name='atd-team-sizes-list[]']:eq(0) option").each(function (e) {
        team_sizes_summary[$(this).val()] = { items: 0, totals: 0 };
      });

      $(".atd-team-row").each(function () {
        var name = $(this).find(".atd-team-names-list").val();
        var number = $(this).find(".atd-team-numbers-list").val();
        var size = $(this).find(".atd-team-sizes-list").val();
        if ("" !== size) {
          if ("" !== name || "" !== number) {
            team_sizes_summary[size]["items"] += 1;
          }
          team_sizes_summary[size]["totals"] += 1;
        }
      });
      return team_sizes_summary;
    }

    $(".atd-preview-box-team .atd-icon-team-cross").click(function (e) {
      $(".atd-shadow-team").removeClass("atd-show");

      $(this).closest(".atd-preview-box-team").removeClass("atd-show");

      $("body").css("overflow", "scroll");
    });

    $(".atd-team-btn-done").click(function () {
      $(".atd-preview-box-team .atd-icon-team-cross").click();
    });

    $(document).on(
      "click",
      ".atd-team-btn-more .atd-btn-team-action",

      function () {
        var $targetLine = $(".atd-team-row");

        var $lastRepeatingGroup = $targetLine.last();

        setTimeout(function () {
          var $cloneLastTarget = $lastRepeatingGroup.clone(true);

          $cloneLastTarget.find(".atd-input-text, .atd-input-number").val("");

          $cloneLastTarget.insertAfter($lastRepeatingGroup);
          get_team_totals_summary();
        }, 200);
      }
    );

    $(".atd-icon-team").click(function () {
      if ($(".atd-team-row").length < 2) {
        return;
      }

      $(this).closest(".atd-team-row").remove();
    });

    $(".wb-missing-link").click(function (e) {
      e.preventDefault();

      $(this)
        .closest(".wb-team-help-missing-none")
        .find(".wb-team-help-duplicate-list")
        .toggleClass("isHidden");

      $(this)
        .find(".wb-missing-action")
        .text(function () {
          return $(this)
            .closest(".wb-team-help-missing-none")
            .find(".wb-team-help-duplicate-list")
            .hasClass("isHidden")
            ? "Show"
            : "Hide";
        });
    });

    /* End Team Script */

    $("#atd-icon-tooltip-alignment").click(function () {
      $(".atd-context-menu-alignment").toggleClass("is-active");
    });

    $(document).on("click", function (e) {
      var $this = $(e.target);

      if ($this.closest("#atd-icon-tooltip-alignment").length === 0) {
        $(".atd-context-menu-alignment").removeClass("is-active");
      }

      if ($this.closest(".atd-context-menu-alignment").length === 1) {
        $(".atd-context-menu-alignment").addClass("is-active");
      }

      if ($this.closest(".atd-font-container").length === 0) {
        $(".atd-font-container").removeClass("atd-active");

        $(this).find(".atd-font-field span").removeClass("atd-rotate");
      }

      /*if ($this.closest(".atd-tab-tools-content[data-title='clippart']").length === 0) {

                $(".atd-clippart-inner").addClass("atd-active");

                $(".atd-clippart-inner").find(".atd-clippart-group-container").addClass("atd-active");

                $(".atd-clippart-container").removeClass("atd-active");

                $(".atd-tab-tools-content .atd-clippart-edit-inner").removeClass("atd-active");

            }

            if ($this.closest(".atd-tab-tools-content[data-title='upload']").length === 0) {

                $(".atd-upload-inner").addClass("atd-active");

                $(".atd-tab-tools-content .atd-upload-edit-inner").removeClass("atd-active");

            }*/
    });

    /** End Contextmenu Script */

    /** Begin range Script */

    $(".atd-range-slider input").on("input", function (e) {
      var value = $(this).val();

      // var valueWithPercent = value + '%';

      // $(this).closest(".atd-range-slider").find(".atd-thumb").css("left", valueWithPercent);

      // $(this).closest(".atd-range-slider").find(".atd-progress-bar").css("width", valueWithPercent);

      // $(this).closest(".atd-range-slider-inner").find(".atd-slider-value span").text(value);

      // $(this).closest(".atd-range-slider-inner").find(".atd-slider-value span").css("left", valueWithPercent);

      // $(this).closest(".atd-range-slider-inner").find(".atd-slider-value span").addClass("atd-show");

      $(this)
        .closest(".atd-range-container")
        .find(".atd-range-text")
        .text(value);
    });

    // $(".atd-range-slider input").on('blur', function(e) {

    //     $(this).closest(".atd-range-slider-inner").find(".atd-slider-value span").removeClass("atd-show");

    // });

    /** End range Script */

    /** Begin isActive Font Script */

    $(".atd-font-container").click(function () {
      $(this).toggleClass("atd-active");

      $(this).find(".atd-font-field span").toggleClass("atd-rotate");
    });

    /*$(".atd-font-drop-down-item").click(function() {

            $(this).closest(".atd-font-container").find(".atd-font-label").text($(this).find(".atd-font-name").text());

        });
        
        $('#txt-color-selector, #atd-color-picker-outline').spectrum({

            color: "#4f71b9",

            showInput: true,

            showAlpha: true,

            //showPalette: false,

            showButtons: false,

            preferredFormat: "hex3",

            change: function (color) {
                console.log(color.toHexString());
            }

        });*/

    /** End isActive Font Script */

    /** Begin Remove Upload Image Script */

    // $(".atd-icon-cross").click(function(e) {

    //     $(this).closest(".atd-preview-upolad-item").remove();

    // });

    $(".atd-icon-edit-uplo-cross").click(function () {
      $(".atd-upload-inner").addClass("atd-active");

      $(".atd-tab-tools-content .atd-upload-edit-inner").removeClass(
        "atd-active"
      );
    });

    /*$(".atd-preview-upolad-item").click(function() {

            $(".atd-upload-inner").removeClass("atd-active");

            $(this).closest(".atd-tab-tools-content").find(".atd-upload-edit-inner").addClass("atd-active");

        });*/

    /** End Remove Upload Image Script */

    /** Begin My Design Script */

    $(".atd-my-design-tab-item").click(function () {
      $(this).siblings(".atd-active").removeClass("atd-active");

      $(this).addClass("atd-active");

      var targetLi = $(this).attr("data-title");

      if (targetLi == "past-order") {
        $(".atd-underline-design").css("left", "50%");

        $(".atd-my-design-past").css("left", "0");

        $(".atd-my-design-saved").css("left", "-150%");
      } else {
        $(".atd-underline-design").css("left", "0");

        $(".atd-my-design-past").css("left", "-150%");

        $(".atd-my-design-saved").css("left", "0");
      }
    });

    $(".atd-my-design-saved-item").click(function () {
      var targetLi = $(this).attr("data-name");

      var $targetTabContent = $(
        ".atd-preview-box-saved[data-name='" + targetLi + "']"
      );

      $targetTabContent.find(".atd-preview-title").text($(this).text());

      $(".atd-shadow").addClass("atd-show");

      $targetTabContent.addClass("atd-show");

      $("body").css("overflow", "hidden");

      // $(".atd-preview-box-saved .atd-preview-title").text($(this).text());

      // $(".atd-shadow, .atd-preview-box-saved").addClass("atd-show");
    });

    $(".atd-shadow").click(function () {
      $(this).removeClass("atd-show");

      $(".atd-preview-box-saved").removeClass("atd-show");

      $("body").css("overflow", "scroll");
    });

    $(".atd-icon-des-cross").click(function (e) {
      $(".atd-shadow").removeClass("atd-show");

      $(this).closest(".atd-preview-box-saved").removeClass("atd-show");

      $("body").css("overflow", "scroll");
    });

    $(".atd-preview-saved-inner").owlCarousel({
      margin: 20,

      responsive: {
        0: {
          items: 2,
        },

        // 600: {
        //     items: 2,
        //     nav: false
        // },

        // 1000: {
        //     items: 3,
        //     nav: false
        // }
      },
    });

    /** End My Design Script */

    /** Begin Preview Cart Design Script */

   $(".atd-prev-cart-des").click(function() {

      var targetLi = $(this).attr("data-part-name");

      var $targetTabContent = $(
        ".atd-preview-box-prev-cart-des[data-part-name='" + targetLi + "']"
      );

      $targetTabContent.find(".atd-preview-title").text($(this).text());

      $(".atd-shadow-prev-cart-des").addClass("atd-show");

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

    /** Begin Preview Design Script */

    // $(".atd-prev-des").click(function() {

    //     $(".atd-shadow-prev-des, .atd-preview-box-prev-des").addClass("atd-show");

    // });

    $(".atd-shadow-prev-des").click(function () {
      $(this).removeClass("atd-show");

      $(".atd-preview-box-prev-des").removeClass("atd-show");

      $("body").css("overflow", "scroll");
    });

    $(".atd-icon-prev-cross").click(function (e) {
      $(".atd-shadow-prev-des").removeClass("atd-show");

      $(this).closest(".atd-preview-box-prev-des").removeClass("atd-show");

      $("body").css("overflow", "scroll");
    });

    $(".atd-preview-prev-des-inner").owlCarousel({
      margin: 20,

      responsive: {
        0: {
          items: 1,
        },

        600: {
            items: 2,
            nav: false
        },

        // 1000: {
        //     items: 3,
        //     nav: false
        // }
      },
    });

    /** End Preview Design Script */

    /** Begin Save Design Script */

    $(".atd-save-des").click(function () {
      $(".atd-shadow-save, .atd-preview-box-save").addClass("atd-show");

      $("body").css("overflow", "hidden");
    });

    $(".atd-shadow-save").click(function () {
      $(this).removeClass("atd-show");

      $(".atd-preview-box-save").removeClass("atd-show");

      $("body").css("overflow", "scroll");
    });

    $(".atd-icon-save-cross").click(function (e) {
      $(".atd-shadow-save").removeClass("atd-show");

      $(this).closest(".atd-preview-box-save").removeClass("atd-show");

      $("body").css("overflow", "scroll");
    });

    /** End Save Design Script */

    /** Begin Add Product || Team Information Design Script */

    $(".atd-shadow-add-product-information, .atd-shadow-add-team-information").click(function () {
      $(this).removeClass("atd-show");

      $(".atd-preview-box-add-product-information, .atd-preview-box-add-team-information").removeClass("atd-show");
      $("body").css("overflow", "scroll");
    });

    $(".atd-icon-add-product-information-cross, .atd-icon-add-team-information-cross").click(function () {
      $(".atd-shadow-add-product-information, .atd-shadow-add-team-information").removeClass("atd-show");

      $(this)
        .closest(".atd-preview-box-add-product-information, .atd-preview-box-add-team-information")
        .removeClass("atd-show");
      $("body").css("overflow", "scroll");
    });

    /** End Add Product || Team Information Design Script */

    /** Begin Cart(configurator) Script */

    $(".atd-debug-icon").click(function () {
      $(".atd-debug-wrap").removeClass("atd-active");
    });

    // $(document).on("keyup", ".atd-input-text.atd-team-names-list", function () {
    //   $(this).val($(this).val().toUpperCase());
    // });

    //        var atdIncOrDec = function($btn, action) { // Increment or decrement depending on user action on plus or minus buttons
    //
    //            var newQty = 0;
    //
    //            var $targetQty = $btn.closest(".atd-cart-item").find(".atd-cart-qty");
    //
    //            var oldQty = $targetQty.data("qty");
    //
    //            if (action === "increase") {
    //
    //                $btn.closest(".atd-icon-btn").find(".atd-icon-btn-item").removeClass("atd-disable");
    //
    //                newQty = parseInt($targetQty.text()) + oldQty;
    //
    //            } else {
    //
    //                newQty = parseInt($targetQty.text()) - oldQty;
    //
    //                if (oldQty >= newQty) {
    //
    //                    $btn.addClass("atd-disable");
    //
    //                    newQty = oldQty;
    //
    //                }
    //
    //            }
    //
    //            $targetQty.text(newQty);
    //
    //        }
    //
    //
    //        $(".atd-icon-plus").click(function() {
    //
    //            var $btn = atdIncOrDec($(this), "increase");
    //
    //        });
    //
    //        $(".atd-icon-minus").click(function() {
    //
    //            var $btn = atdIncOrDec($(this));
    //
    //        });

    /** End Cart(configurator) Script */

    /** Begin Cart(configurator) Bottom Script */

    $(".atd-add-cart-item-card").click(function () {
      $(".atd-add-cart-item-card").removeClass("wb-isActive");

      $(this).addClass("wb-isActive");
    });

    $(".atd-container-add-cart-item-carousel").owlCarousel({
      margin: 9,

      responsive: {
        0: {
          items: 4,
        },

        // 600: {
        //     items: 2,
        //     nav: false
        // },

        // 1000: {
        //     items: 3,
        //     nav: false
        // }
      },
    });

    $(".atd-icon-cart-cross").click(function () {
      if (
        $(this)
          .closest(".owl-item")
          .find(".atd-add-cart-item-card")
          .hasClass("wb-isActive")
      ) {
        var active_variation = $(this).closest(".owl-item");
        var prev_active_variation_id = active_variation
          .prev()
          .find(".atd-add-cart-item-card")
          .attr("data-id");
        var next_active_variation_id = active_variation
          .next()
          .find(".atd-add-cart-item-card")
          .attr("data-id");

        if (!isNaN(parseInt(prev_active_variation_id))) {
          add_selected_custom_colors_on_designers(prev_active_variation_id);
          active_variation
            .prev()
            .find(".atd-add-cart-item-card")
            .addClass("wb-isActive");
          var active_title=active_variation
          .prev()
          .find(".atd-add-cart-item-card")
          .attr("data-name");
          var active_color=active_variation
          .prev()
          .find(".atd-add-cart-item-card")
          .attr("data-color");
        } else if (!isNaN(parseInt(next_active_variation_id))) {
          add_selected_custom_colors_on_designers(next_active_variation_id);
          active_variation
            .next()
            .find(".atd-add-cart-item-card")
            .addClass("wb-isActive");
          var active_title=active_variation
          .next()
          .find(".atd-add-cart-item-card")
          .attr("data-name");
          var active_color=active_variation
          .next()
          .find(".atd-add-cart-item-card")
          .attr("data-color");
        } else return;
      }
      var $userSelected = $(this)
        .closest(".atd-add-cart-item-card")
        .find(".atd-add-cart-item-img-self");
      var variationImgTargetReplace = $userSelected.attr("src");

      $(".atd-color-choice-item").each(function () {
        if (
          variationImgTargetReplace ===
          $(this).find(".atd-preview-box-add-cart-img").attr("src")
        ) {
          $(this).removeClass("isOrder");
        }
      });

      $(".atd-container-add-cart-item-carousel")
        .trigger(
          "remove.owl.carousel",
          $(".atd-add-cart-item-card").index(
            $(this).closest(".atd-add-cart-item-card")
          )
        )
        .trigger("refresh.owl.carousel");
      $(".atd-cart-product-color").text(active_color);
      $(".atd-cart-product-name").text(active_title);
    });

    $(".atd-shadow-add-cart-first").click(function () {
      $(this).removeClass("atd-show");

      $(".atd-preview-box-add-cart-first").removeClass("atd-show");

      $("body").css("overflow", "scroll");

      setTimeout(function () {
        $(".atd-products-choices").css("marginLeft", "0");
        $(".atd-icon-add-cart-back").addClass("isHidden");
      }, 500);
    });

    $(".atd-preview-box-add-cart-first .atd-icon-add-cart-first-cross").click(
      function (e) {
        $(".atd-shadow-add-cart-first").removeClass("atd-show");

        $(this)
          .closest(".atd-preview-box-add-cart-first")
          .removeClass("atd-show");

        $("body").css("overflow", "scroll");

        setTimeout(function () {
          $(".atd-products-choices").css("marginLeft", "0");
          $(".atd-icon-add-cart-back").addClass("isHidden");
        }, 500);
      }
    );

    $(document).on("mouseenter", ".atd-color-choice-item", function () {
      var $userSelected = $(this).find(".atd-preview-box-add-cart-img");
      var $target = $(".atd-img-swatch .atd-preview-box-add-cart-img");
      var variationImgTarget = $userSelected.attr("src");
      var color = $(this).attr("data-title");
      if(color=="") 
        color=$(".atd-cart-product-color").text();
      $(".atd-product-variation-text").text(color);
      $target.attr("src", variationImgTarget);
    });
    $(document).on("mouseleave", ".atd-color-choice-item", function () {
      var $target = $(".atd-img-swatch .atd-preview-box-add-cart-img");
      var oldVariationImg = $target.attr("data-src");
      var old_color = $(".atd-product-variation-text").attr("old-color");
      if (old_color != "") {
        $(".atd-product-variation-text").text(old_color);
      } else {
        old_color = $(".atd-cart-product-color").text();
        $(".atd-product-variation-text").text(old_color);
      }

      $target.attr("src", oldVariationImg);
    });

    $(document).on("click", ".atd-color-choice-item", function () {
      var color = $(this).attr("data-title");
      var own_id = $(this).attr("atd-variation-id");
      var $userSelected = $(this).find(".atd-preview-box-add-cart-img");
      var $userSelectedId = $(this).find(".atd-preview-box-add-cart-img").attr("data-own-id");
      var $target = $(".atd-img-swatch .atd-preview-box-add-cart-img");
      var $targetDetailsImg = $(
        ".atd-modal-product-details .atd-preview-box-add-cart-img-inner .atd-preview-box-add-cart-img"
      );
      var $btnProductAdd = $(".atd-btn-product-add");
      var cancelBtnDisableAttr = $btnProductAdd.attr("data-own-id");
      var variationImgTargetReplace = $userSelected.attr("src");

      $(".atd-product-variation-text").text(color);
      $(".atd-product-variation-text").removeAttr("old-color");
      $(".atd-product-variation-text").attr("old-color", color);

      if (
        $(this).hasClass("isOrder") &&
        $(".atd-btn-product-add").attr("ata-own-id") !==
          $(this).find(".atd-preview-box-add-cart-img").attr("src")
      ) {
        return;
      }

      $(this).siblings(".isActive").removeClass("isActive");
      $(this).addClass("isActive");
      $target.attr("data-id", own_id);
      $target.attr("src", variationImgTargetReplace);
      $target.attr("data-src", variationImgTargetReplace);
      $targetDetailsImg.attr("src", variationImgTargetReplace);
      $btnProductAdd.attr("data-id", $userSelectedId)

      $(".atd-color-choice-item").each(function () {
        if (
          cancelBtnDisableAttr ===
          $(this).find(".atd-preview-box-add-cart-img").attr("src")
        ) {
          $(this).addClass("isOrder");
        }
      });

      $btnProductAdd.prop("disabled", function () {
        return variationImgTargetReplace === cancelBtnDisableAttr
          ? true
          : false;
      });

      if (
        $(this).hasClass("isOrder") &&
        cancelBtnDisableAttr ===
          $(this).find(".atd-preview-box-add-cart-img").attr("src")
      ) {
        $(this).removeClass("isOrder");
      }

      // showImageInModalSelected($(this));
    });

    $(".atd-icon-add-cart-back").click(function () {
      $(this).addClass("isHidden");
      $(".atd-products-choices").css("marginLeft", "0");
    });

    $(document).on("click", ".atd-btn-product-add", function () {
      var $userSelectedId = $(
        ".atd-img-swatch .atd-preview-box-add-cart-img"
      ).attr("data-id");
      var $userSelectedSize=$(
        ".atd-img-swatch .atd-preview-box-add-cart-img"
      ).attr("data-size");

      var $userSelectedOwnId=$(
        ".atd-btn-product-add"
      ).attr("data-id");

      var $targetDetailsImg = $(
        ".atd-modal-product-details .atd-preview-box-add-cart-img-inner .atd-preview-box-add-cart-img"
      );

      var variationImgTarget = $targetDetailsImg.attr("src");

      var $targetCardParent = $(".atd-container-add-cart-item-carousel");

      var $targetCard = $targetCardParent.find(
        ".atd-add-cart-item-card.wb-isActive"
      );

      var $cloneTargetCard = $targetCard.clone(true);

      if (
        $('.atd-tab-tools .atd-tab-item[data-title="team"]').hasClass("active")
      ) {
        $(".atd-tab-tools .atd-tab-item").first().click();
      }

      var old_color = $(".atd-product-variation-text").attr("old-color");
      var old_name = $(".atd-product-variation-text").attr("data-name");

      $cloneTargetCard
        .find(".atd-add-cart-item-img .atd-add-cart-item-img-self")
        .attr("src", variationImgTarget);
      $cloneTargetCard.attr("data-name", old_name);
      $cloneTargetCard.attr("data-color", old_color);
      $cloneTargetCard.attr("data-id", $userSelectedId);
      $cloneTargetCard.attr("data-size",$userSelectedSize);
      $cloneTargetCard.attr("data-own-id",$userSelectedOwnId);
      $cloneTargetCard.attr("data-id-color", old_color+$userSelectedId);
      $(".atd-cart-product-color").text(old_color);
      $(".atd-cart-product-name").text(old_name);

      $cloneTargetCard.click();
      $(".atd-color-choice-item.isActive").each(function () {
        $(this).addClass("isOrder");
      });

      $targetCardParent
        .trigger("add.owl.carousel", $cloneTargetCard)
        .trigger("refresh.owl.carousel");

      $targetCardParent.trigger("to.owl.carousel", [
        $(".owl-item").length,
        500,
      ]);

      if (save_selected_variation_part_image($userSelectedId)) {
        add_selected_custom_colors_on_designers($userSelectedId);
      }
      $(".atd-icon-add-cart-first-cross").click();
    });

    /**
     * save selected variation part image
     * @param {*variation id} variation_id
     */

    function save_selected_variation_part_image(variation_id) {
      var part_name = ["Front", "Back", "Left", "Right", "Chest"];
      $("<div data-id='" + variation_id + "'></div>").appendTo(
        ".atd-selected-part-image-section"
      );
      $.each(part_name, function (index, param) {
        var image_link = $(
          "span[data-title='" + param + "_" + variation_id + "'] img"
        ).attr("src");
        var icon_link = $(
          "span[data-title='icon_" + param + "_" + variation_id + "'] img"
        ).attr("src");
        if (image_link != "" && typeof image_link !== "undefined") {
          $(
            ".atd-selected-part-image-section [data-id='" +
              variation_id +
              "'] [data-title='" +
              param +
              "']"
          ).remove();
          $("#atd-parts-bar [data-title='" + param + "']").attr(
            "data-url",
            image_link
          );
          $(
            "#atd-editor-container [data-title='" +
              param +
              "'] .canvas-container"
          ).css("background", "url(" + image_link + ")");
          $("<img src='" + image_link + "'>")
            .appendTo(
              ".atd-selected-part-image-section [data-id='" +
                variation_id +
                "']"
            )
            .attr("data-title", param);
        }

        if (icon_link != "") {
          $("#atd-parts-bar [data-title='" + param + "']")
            .find(".atd-part-img img")
            .attr("src", icon_link);
          $("<img src='" + icon_link + "'>")
            .appendTo(
              ".atd-selected-part-image-section [data-id='" +
                variation_id +
                "']"
            )
            .attr("data-title", "icon_" + param);
        }

        if (icon_link != "" && typeof icon_link !== "undefined") {
          $("#atd-parts-bar [data-title='" + param + "']")
            .find(".atd-part-img img")
            .attr("src", icon_link);
          $("<img src='" + icon_link + "'>")
            .appendTo(
              ".atd-selected-part-image-section [data-id='" +
                variation_id +
                "']"
            )
            .attr("data-title", "icon_" + param);
        }
      });

      return true;
    }

    // function showImageInModalSelected($this) {
    //   var $userSelected = $this.find(".atd-preview-box-add-cart-img");
    //   var $userSelectedId=$userSelected.attr("data-id");
    //   var $target = $(".atd-img-swatch .atd-preview-box-add-cart-img");
    //   var $targetDetailsImg = $(
    //     ".atd-modal-product-details .atd-preview-box-add-cart-img-inner .atd-preview-box-add-cart-img"
    //   );
    //   var $btnProductAdd = $(".atd-btn-product-add");
    //   var cancelBtnDisableAttr = $btnProductAdd.attr("ata-own-id");
    //   var variationImgTargetReplace = $userSelected.attr("src");

    //   $target.attr("data-id", $userSelectedId);
    //   $target.attr("src", variationImgTargetReplace);
    //   $target.attr("data-src", variationImgTargetReplace);
    //   $targetDetailsImg.attr("src", variationImgTargetReplace);
    //   if ($this.find(".atd-btn-cart-modal").hasClass("atd-btn-cart-modal")) {
    //     $(".atd-color-choice-item").each(function () {
    //       if (
    //         variationImgTargetReplace ===
    //         $(this).find(".atd-preview-box-add-cart-img").attr("src")
    //       ) {
    //         $(this).siblings(".isActive").removeClass("isActive");
    //         $(this).addClass("isActive");
    //         $btnProductAdd.attr("ata-own-id", variationImgTargetReplace);
    //         $btnProductAdd.prop("disabled", true);
    //         return false;
    //       }
    //     });
    //   }
    //   if ($this.hasClass("atd-color-choice-item")) {
    //     $btnProductAdd.prop("disabled", function () {
    //       return variationImgTargetReplace === cancelBtnDisableAttr
    //         ? true
    //         : false;
    //     });
    //   }
    // }

    $(".atd-btn-cart-continue").click(function () {
      $(".atd-preview-box-quantity").addClass("atd-show");

      $(".atd-shadow-quantity").addClass("atd-show");

      $("body").css("overflow", "hidden");
    });

    $(".atd-shadow-quantity").click(function () {
      $(this).removeClass("atd-show");

      $(".atd-preview-box-quantity").removeClass("atd-show");

      $("body").css("overflow", "scroll");
    });

    $(".atd-icon-quantity-cross").click(function (e) {
      $(".atd-shadow-quantity").removeClass("atd-show");

      $(this).closest(".atd-preview-box-quantity").removeClass("atd-show");

      $("body").css("overflow", "scroll");
    });

    $(".atd-ui-qty-size-item").click(function () {
      $(this).siblings(".isChecked").removeClass("isChecked");

      if ($(this).hasClass("isChecked")) {
        $(this).removeClass("isChecked");
        $(".atd-ui-qty-variation-size-text").text("");
      } else {
        $(this).addClass("isChecked");
        $(this)
          .closest(".atd-ui-qty-details")
          .find(".atd-ui-qty-variation-size-text")
          .text($(this).find("span").text());
      }
    });

    $(document).on("click", ".atd-icon-qty-cross", function () {
      var variation_id = $(this)
        .closest(".atd-ui-qty-item")
        .attr("atd-variation-id");
      $(this).closest(".atd-ui-qty-item").remove();
      $(".atd-add-cart-item-card[data-id='" + variation_id + "']")
        .find(".atd-icon-cart-cross")
        .click();
    });

    /** End Cart(configurator) Bottom Script */

    var atd_variation_data = [];
    /**
     * Check if team is actif
     * @returns bool
     */
    function check_if_team_is_actif() {
      if (
        $("#atd-checkbox-add-name").is(":checked") ||
        $("#atd-checkbox-add-number").is(":checked")
      )
        return true;
      else return false;
    }

    /**
     * Display modal
     */
    function display_atd_related_custom_product_modal() {
      $(".atd-shadow-add-cart-first").addClass("atd-show");
      $(".atd-preview-box-add-cart-first").addClass("atd-show");

      $("body").css("overflow", "hidden");
    }

    var part_name = ["Front", "Back", "Left", "Right", "Chest"],
      default_product_id = $("[atd-related-product-id]").attr(
        "atd-related-product-id"
      ),
      active_on_designers = [];

    active_on_designers.push(default_product_id);
    active_on_designers.push(get_default_variation_id());

    $.each(part_name, function (index, value) {
      var img_target = $(
        "#atd-parts-bar .atd-navigation-canvas-item[data-title='" + value + "']"
      )
        .find(".atd-part-img img")
        .attr("src");
      $(
        ".atd-add-cart-item-card[data-id='" + get_default_variation_id() + "']"
      ).attr("data-img-" + value, img_target);
    });

    /**
     * Get default variation id
     * @returns default variation id
     */
    function get_default_variation_id() {
      var variation_id=$(".atd-container-add-cart-wrap")
      .first()
      .attr("data-id");
      return parseInt(variation_id);
    }

    /**
     * Get product id with variation id
     * @param {*variation id} $variation_id
     * @returns product id
     */
    function get_product_id_by_variation_id($variation_id) {
      var result = [];
      $.each(atd_variation_data, function (product_id, variation_data) {
        if ($.isArray(variation_data)) {
          $.each(variation_data, function (index, variation) {
            if (
              $.inArray(parseInt($variation_id), variation["variation_id"]) !=
              -1
            ) {
              result = [];
              result.push(variation["product_id"]);
            }
          });
        }
      });
      return result[0];
    }

    /**
     * add variation part image on designer
     * @param {*variation id} variation_id
     */

    function add_selected_custom_colors_on_designers(variation_id) {
      active_on_designers = [];
      if (variation_id == get_default_variation_id()) {
        active_on_designers.push(default_product_id);
      } else {
        var product_id = get_product_id_by_variation_id(variation_id);

        if (!isNaN(product_id)) active_on_designers.push(product_id);
      }
      active_on_designers.push(variation_id);
      var part_section = $(
        ".atd-selected-part-image-section [data-id='" + variation_id + "']"
      );
      $.each(part_name, function (index, param) {
        var image_link = part_section
          .find("img[data-title='" + param + "']")
          .attr("src");
        var icon_link = part_section
          .find("img[data-title='icon_" + param + "']")
          .attr("src");
        if (typeof image_link !== undefined) {
          $("#atd-parts-bar [data-title='" + param + "']").attr(
            "data-url",
            image_link
          );
          $(
            "#atd-editor-container [data-title='" +
              param +
              "'] .canvas-container"
          ).css("background", "url(" + image_link + ")");
        } else {
          $("#atd-parts-bar [data-title='" + param + "']").attr("data-url", "");
          $(
            "#atd-editor-container [data-title='" +
              param +
              "'] .canvas-container"
          ).css("background", "url('')");
        }

        if (
          variation_id == get_default_variation_id() &&
          $(
            ".atd-add-cart-item-card[data-id='" +
              get_default_variation_id() +
              "']"
          ).attr("data-img-" + param) != ""
        ) {
          icon_link = $(
            ".atd-add-cart-item-card[data-id='" +
              get_default_variation_id() +
              "']"
          ).attr("data-img-" + param);
        }

        if (typeof icon_link !== "undefined") {
          $("#atd-canvas-box").addClass("atd-with-img");
          $("#atd-parts-bar").addClass("atd-with-img-part");
          // $(
          //   "#atd-parts-bar .atd-navigation-canvas-item[data-title='" +
          //     param +
          //     "']"
          // )
          //   .find(".atd-part-img img")
          //   .attr("src", "");
          $(
            "#atd-parts-bar .atd-navigation-canvas-item[data-title='" +
              param +
              "']"
          )
            .find(".atd-part-img")
            .html("<img src='" + icon_link + "'>");
        } else {
          $(
            "#atd-parts-bar .atd-navigation-canvas-item[data-title='" +
              param +
              "']"
          )
            .find(".atd-part-img img")
            .remove();
        }
      });

      $("#atd-parts-bar").children().first().click();
    }

    /**
     * Au click sur le bouton add product
     */

    $(".atd-btn-product").click(function () {
      if (
        $(".atd-checkbox-add-name").is(":checked") ||
        $(".atd-checkbox-add-number").is(":checked")
      ) {
        $(
          ".atd-preview-box-add-product-information, .atd-shadow-add-product-information"
        ).addClass("atd-show");
        $("body").css("overflow", "hidden");
        return;
      }

      if (active_on_designers.length > 0 && !isNaN(active_on_designers[0]))
        var product_id = active_on_designers[0];
      else var product_id = default_product_id;
      $.ajax({
        url: ajax_object.ajax_url,
        type: "POST",
        data: {
          action: "display_related_custom_products",
          product_id: product_id,
          variation_id: active_on_designers[1],
        },
      }).success(function (response) {
        $(".atd-preview-box-add-cart-wrap").html(response);
        display_atd_related_custom_product_modal();
      });
    });

    $(".atd-add-product-information-link").click(function (e) {
      e.preventDefault();

      $(".atd-team-container .atd-checkbox-input").prop("checked", false);
      $(".atd-team-name-content, .atd-team-number-content").removeClass(
        "atd-show"
      );
      $(".atd-icon-add-product-information-cross").click();
      $(".atd-btn-product").click();
    });

    $(".atd-add-team-information-link").click(function (e) {
      e.preventDefault();

      $(".atd-add-cart-item-card").each(function (i, val) {
        if (!$(this).hasClass("wb-isActive")) {
          $(".atd-container-add-cart-item-carousel")
            .trigger("remove.owl.carousel", [i])
            .trigger("refresh.owl.carousel");
        }
      });

      $(".atd-color-choice-item").each(function () {
        $(this).removeClass("isActive");
        $(this).removeClass("isOrder");
      });
      $(".atd-icon-add-team-information-cross").click();
      $('.atd-tab-tools .atd-tab-item[data-title="team"]').click();
    });

    /**
     * Get variation data by ajax
     */
    $(document).on("click", ".atd-btn-cart-modal", function () {
      var product_id = parseInt(
        $(this).closest(".atd-preview-box-add-cart-card").attr("data-id")
      );
      if (!isNaN(product_id)) {
        $.ajax({
          url: ajax_object.ajax_url,
          type: "POST",
          data: {
            action: "display_related_custom_product_details",
            product_id: product_id,
            dflt_variation_id: active_on_designers[1],
            dflt_product_id: active_on_designers[0],
            active_color:$(".atd-cart-product-color").text()
          },
        }).success(function (response) {
          var $result = JSON.parse(response);
          if (
            $(".atd-add-cart-content-details").html($result["content"]) &&
            $result["success"]
          ) {
            atd_variation_data[$result["data"]["product_id"]] = [];
            atd_variation_data[$result["data"]["product_id"]].push(
              $result["data"]
            );
            team_size_details[$result["data"]["product_id"]] = $result["data"]["variation_sizes"]
            // showImageInModalSelected(
            //   $(this).closest(".atd-preview-box-add-cart-card")
            // );

            $(".atd-icon-add-cart-back").removeClass("isHidden");
            $(".atd-products-choices").css("marginLeft", "-50%");

            $(".atd-add-cart-item-card").each(function (index, value) {
              var $this_id = $(value).attr("data-id-color");

              $(".atd-color-choices")
                .find('div[atd-id-color="' + $this_id + '"]')
                .addClass("isOrder");
            });

            if($(".atd-product-variation-text").text()=="")
            {
              var this_color=$(".atd-cart-product-color").text();
              $(".atd-product-variation-text").text(this_color);
            }
          } else {
            $(".atd-add-cart-content-details").html($result["content"]);
          }
        });
      }
    });

    $(document).on(
      "click mouseover mousedown keyup keydown ready",
      ".atd-add-cart-content-details",
      function () {
        var isActive = [];

        $(".atd-color-choices .atd-color-choice-item").each(function () {
          if ($(this).hasClass("isActive") && !$(this).hasClass("isOrder")) {
            isActive = [];
            isActive.push(1);
          }
        });

        if (isActive.length > 0)
          $(".atd-btn-product-add").removeAttr("disabled");
        else $(".atd-btn-product-add").attr("disabled", "true");
      }
    );

    $(document).on("click", ".atd-add-cart-item-img", function () {
      var $variation_id = $(this)
        .closest(".atd-add-cart-item-card")
        .attr("data-id");
      var color = $(this).closest(".atd-add-cart-item-card").attr("data-color");
      var old_name = $(this)
        .closest(".atd-add-cart-item-card")
        .attr("data-name");
      $(".atd-cart-product-color").text(color);
      $(".atd-cart-product-name").text(old_name);
      add_selected_custom_colors_on_designers($variation_id);
   
    });

    $(document).on("click", ".atd-btn-team.atd-step-2", function(){
      var this_product_id = $(".atd-add-cart-item-card.wb-isActive")
      .attr("data-own-id");
      var actif = $(".atd-preview-box-team").attr("data-id");
      if(team_size_details.length > 0 && (typeof actif!==undefined && actif !==this_product_id))
      {
        $(".atd-team-sizes-list").each(function()
        {
          $(this).find("option").remove();
          for(var $i=0; $i < team_size_details[this_product_id].length; $i++)
          {
            $(this).prepend("<option value='"+
            team_size_details[this_product_id][$i]
            +"'>"+
            team_size_details[this_product_id][$i]+
            "</option>");
          }
          $(this).prepend("<option value class='atd-option' selected></option>");
          $(".atd-preview-box-team").attr("data-id", this_product_id);
        })
      }
    })

    function remove_attr_space(param) {
      var split_param = param.split(" ");
      if (split_param[0] != "") return split_param[0];
      else return split_param[1];
    }

    /**
     * Add to cart
     */
     $(document).on("click", ".atd-btn-cart-continue", function () {
        atd_EDITOR.canvas[atd_EDITOR.selected_part].deactivateAll().renderAll();
      $(".atd-preview-box-quantity-wrap").html("");
      $(".atd-ui-qty-totals-numb").text("0");
      $(".atd-preview-box-quantity-inner")
      .find(".atd-loader")
      .addClass("atd-show");
       $(".atd-container-add-cart-item .atd-add-cart-item-card")
       .each(function(){
          var cart_title=$(this).attr("data-name"),
              var_size=$(this).attr("data-size"),
              var_color=$(this).attr("data-color"),
              var_id=$(this).attr("data-id"),// Variation id
              var_own_id=$(this).attr("data-own-id"),// Product id
              var_img=$(this).find("img").attr("src"),
              clone_cart_item = $(".atd-add-to-cart-origin-elm .atd-ui-qty-item"
              ).clone(), temp=[];
              clone_cart_item.attr("atd-variation-id", var_id);
              clone_cart_item.attr("atd-product-id", var_own_id);
              clone_cart_item
              .find(".atd-ui-qty-img")
              .html(
                "<img src='" + var_img + "' class='atd-ui-qty-img-self'>"
              );
              clone_cart_item
              .find(".atd-ui-qty-product-name")
              .text(cart_title);
                
              if (
                typeof atd_variation_data[var_own_id] !== "undefined"
              ) {
                  var clone_cart_item=set_clone_data(atd_variation_data[var_own_id][0]); 
                  clone_cart_item.find(".atd-ui-qty-variation-size-text").text(var_size);
                  clone_cart_item.clone().prependTo(".atd-preview-box-quantity-wrap");
                  $(".atd-preview-box-quantity-inner")
                  .find(".atd-loader")
                  .removeClass("atd-show");
                  $(".atd-input-number").trigger("change");
              }
              else{
                $.ajax({
                  url: ajax_object.ajax_url,
                  type: "POST",
                  data: {
                    action: "get_default_product_variation_details",
                    product_id: default_product_id,
                  },
                })
                .success(function (response) {
                  var default_variation_details = JSON.parse(response),
                  clone_cart_item=set_clone_data(default_variation_details); 
                  clone_cart_item.find(".atd-ui-qty-variation-size-text").text(var_size);
                  clone_cart_item.clone().prependTo(".atd-preview-box-quantity-wrap");
                  $(".atd-preview-box-quantity-inner")
                  .find(".atd-loader")
                  .removeClass("atd-show");
                  $(".atd-input-number").trigger("change");
                })
              } 

              function set_clone_data(own_data)
              {
                var var_all_size=own_data["variation_sizes"],
                var_all_id=own_data["variation_id"],
                var_all_color=own_data["variation_color"],
                clone=[];
                $.each(var_all_color, function(index, color){
                  var clone_id=var_all_id[index],
                  clone_size=var_all_size[index];
                  if(var_color==color && var_all_size[index]!="" && clone_id!="" &&
                  $.inArray(
                    var_own_id+
                    clone_size+
                    var_color, temp)==-1)
                  {
                    temp.push(
                      var_own_id+
                      clone_size+
                      var_color)
                    clone_cart_item = set_cart_item_content(
                        clone_cart_item,
                        clone_id,
                        var_own_id,
                        clone_size,
                        var_color
                    );
                      if ( check_if_team_is_actif() &&
                        set_team_cart_qty(clone_cart_item, clone_size)
                      )
                      clone_cart_item = set_team_cart_qty(
                        clone_cart_item,
                        clone_size
                        );
                      clone.push(clone_cart_item);
                  }
                })
                if(typeof clone[0]==="undefined")
                  clone[0]=clone_cart_item;
                return clone[0];
              }     
            
       })
     })

    /**
     * Adds the amount of team defined by the user in the cart
     * @param {*} clone_cart_item the shopping cart section of the clone variation
     * @param {*} self_size the value of the size attribute of the variation
     * @returns {*} clone_cart_item html content with team quantity
     */

    function set_team_cart_qty(clone_cart_item, self_size) {
      var compt = 0,
        get_team_size = [];
      $(".atd-styled-table .atd-team-row").each(function (index, value) {
        var team_name = $(this).find("input[type='text']");
        var team_number = $(this).find("input[type='number']");
        var team_size = $(this).find("select");

        if (
          ($(team_name).val() != "" || $(team_number).val() != "") &&
          $(team_size).val() != ""
        ) {
          if (
            $("#atd-checkbox-add-name").is(":checked") &&
            $("#atd-checkbox-add-number").is(":checked") &&
            ($(team_name).val() == "" || $(team_number).val() == "")
          ) {
            return;
          }

          compt += 1;
          get_team_size[$(team_size).val().toLowerCase()] = [];
          get_team_size[$(team_size).val().toLowerCase()].push(compt);
        }
      });

      if (self_size != "undefined") {
        if (typeof get_team_size[self_size] !== "undefined") {
          clone_cart_item
            .find(
              ".atd-ui-qty-size-container div[data-title='" +
                self_size +
                "'] input"
            )
            .val(get_team_size[self_size]);
          $(".atd-ui-qty-totals-numb").text(get_team_size[self_size]);
          return clone_cart_item;
        } else return false;
      } else return false;
    }

    /**
     * Définit les valeurs de la variations sur l'éléments cloner
     * @param {*} $clone
     * @param {*} $variation_id
     * @param {*} product_id
     * @param {*} this_size
     * @param {*} this_color
     * @returns
     */
    function set_cart_item_content(
      $clone,
      $variation_id,
      product_id,
      this_size,
      this_color
    ) {
      if (this_size != "" && this_size != "Any") {
        $clone.find(".atd-ui-qty-variation-color-text").text(this_color);
        $clone.find(".atd-ui-qty-variation-size-text").text(this_size);
        $clone
          .find(".atd-ui-qty-size-container")
          .append(
            "<div class='atd-input-field atd-ui-qty-wrapper' data-title=" +
              this_size +
              "></div>"
          );
        var this_section_label =
          "<label for='" +
          this_size +
          "' class='atd-ui-qty-label'>" +
          this_size +
          "</label>";
        var this_section_input =
          "<input type='number' id='" +
          this_size +
          "' class='atd-input-number' placeholder='00' name='variation_qty_" +
          $variation_id +
          "_" +
          this_size +
          "_" +
          product_id +
          "_" +
          this_color+
          "' min='0'>";
        $clone.find("#" + this_size + "").remove();
        $clone
          .find(
            ".atd-ui-qty-size-container div[data-title='" + this_size + "']"
          )
          .append(this_section_label);
        $clone
          .find(
            ".atd-ui-qty-size-container div[data-title='" + this_size + "']"
          )
          .append(this_section_input);
      }
      return $clone;
    }

    /**
     * Update cart quatity
     */

    $(document).on(
      "change mouseover click keyup keydown ",
      ".atd-preview-box-quantity",
      function () {
        var cart_elm_length = $(this).find(".atd-ui-qty-item").length;

        if (cart_elm_length < 2)
          $(this).find(".atd-icon-qty-cross").addClass("isHidden");
        else $(this).find(".atd-icon-qty-cross").removeClass("isHidden");

        var qty_data = $(this).find("input").serializeArray(),
          temp = 0;

        if (qty_data.length == 0) {
          $(".atd-btn-cart-go-add-to-cart").attr("disabled", "true");
          $(".atd-ui-qty-totals-numb").text(0);
        }
        if(typeof atd.query_vars["edit"]!=="undefined")
          $(".atd-btn-cart-go-add-to-cart").text("Update cart item");
        $.each(qty_data, function (index, data) {
          if (data.value != "") temp += parseInt(data.value);
          $(".atd-ui-qty-totals-numb").text(temp);

          if (temp == 0)
            $(".atd-btn-cart-go-add-to-cart").attr("disabled", "true");
          else $(".atd-btn-cart-go-add-to-cart").removeAttr("disabled");
        });
      }
    );
    // Empêche l'utilisation des fonctionnalité team et add product à la fois

    // $(".atd-section .atd-tab-item").click(function () {
    //   if (
    //     $(this).attr("data-title") == "team" &&
    //     $(".atd-container-add-cart-item .owl-item").length > 1
    //   ) {
    //     if ($("#atd-checkbox-add-name").is(":checked"))
    //       $("#atd-checkbox-add-name").click();
    //     if ($("#atd-checkbox-add-number").is(":checked"))
    //       $("#atd-checkbox-add-number").click();

    //     $(
    //       ".atd-preview-box-add-product-information, .atd-shadow-add-product-information"
    //     ).addClass("atd-show");
    //     $("body").css("overflow", "hidden");
    //   }
    // });

    $(document).on(
      "click mouseover mousedown keyup keydown",
      ".atd-container-add-cart",
      function () {
        var bar_variation_length = $(this).find(
          ".atd-add-cart-item-card"
        ).length;
        if (bar_variation_length < 2)
          $(this).find(".atd-icon-cart-cross").addClass("isHidden");
        else $(this).find(".atd-icon-cart-cross").removeClass("isHidden");
      }
    );
  });

  /**
   * 
   *  Desktop JS End
   * 
   */


  return atd_editor;
})(jQuery, atd_EDITOR);
