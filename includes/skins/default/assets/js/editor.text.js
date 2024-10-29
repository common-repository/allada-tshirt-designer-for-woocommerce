/* global atd */
var atd_EDITOR = (function($, atd_editor) {
    'use strict';
    $(document).ready(function() {
        //Text
        $(document).on('click', '#atd-add-text', function(event) {
            event.preventDefault();
            var new_text = $("#new-text").val();
            var is_curved = $("#cb-curved").is(":checked");
            if (new_text.length === 0)
                alert(atd.translated_strings.empty_txt_area_msg);
            else if (!is_curved) {
                atd_editor.add_text(new_text, false, false, false, false, false);
            } else {
                add_curved_text(new_text);
            }
        });

        atd_editor.add_text = function(txt, left, top, height = "", team_name_position = "", team_number_position = "") {
            var text = create_text_elmt(txt);
            if ("undefined" !== typeof team_name_position && "" !== team_name_position && team_name_position) {
                text.set("clipTeam", "#atd-team-name");
                text.set("fill", $("#team-name-color-selector").val());
            } else if ("undefined" !== typeof team_number_position && "" !== team_number_position && team_number_position) {
                text.set("clipTeam", "#atd-team-number");
                text.set("fill", $("#team-number-color-selector").val());
            }
            atd_editor.canvas[atd_editor.selected_part].add(text);
            if (left) {
                text.set("left", left);
                text.set("top", top);
            } else if ("undefined" !== typeof team_name_position && "" !== team_name_position && team_name_position) {
                text.set("editable", false);
                text.set("fontSize", height);
                text.set("top", team_name_position);
                text["lockScalingX"] = true;
                text["lockScalingY"] = true;
                atd_editor.centerObjectH(text);
            } else if ("undefined" !== typeof team_number_position && "" !== team_number_position && team_number_position) {
                text.set("editable", false);
                text.set("fontSize", height);
                text.set("top", team_number_position);
                text["lockScalingX"] = true;
                text["lockScalingY"] = true;
                atd_editor.centerObjectH(text);
            } else {
                atd_editor.centerObjectH(text);
                atd_editor.centerObjectV(text);
            }
            atd_editor.canvas[atd_editor.selected_part].setActiveObject(text);
            //var atd_canvas = atd_editor.canvas[atd_editor.selected_part];
            //wp.hooks.doAction('atd_EDITOR.after_adding_text_on_canvas', 
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            text.setCoords();

            $("#new-text").val("");
            atd_editor.save_canvas();
        };

        $(".atd-font-drop-down-item").click(function() {
            $(this).closest(".atd-font-container").find(".atd-font-label").text($(this).find(".atd-font-name").text());
            var selected_object = atd_editor.canvas[atd_editor.selected_part].getActiveObject();
            var font_size = parseInt($("#font-size-selector").val());
            var font_family = $(".atd-font-container .atd-font-label").text();
            $(this).css('font-family', font_family);

            if (!atd_editor.isEmpty(selected_object)) {
                if ((selected_object !== null) && (selected_object.type === "i-text")) {
                    selected_object.set('fontFamily', font_family);
                    if (font_size)
                        selected_object.setFontSize(parseInt(font_size));
                    atd_editor.canvas[atd_editor.selected_part].renderAll();
                    atd_editor.save_canvas();
                } else if ((selected_object !== null) && (selected_object.type === "group")) {
                    selected_object.forEachObject(function(a) {
                        a.set('fontFamily', font_family);
                        if (font_size)
                            a.setFontSize(parseInt(font_size));
                        atd_editor.canvas[atd_editor.selected_part].renderAll();
                        atd_editor.save_canvas();
                    });
                }
            }
        });

        $("#font-size-selector").change(function() {
            var selected_object = atd_editor.canvas[atd_editor.selected_part].getActiveObject();
            var font_size = parseInt($("#font-size-selector").val());
            if (!atd_editor.isEmpty(selected_object)) {
                if ((selected_object !== null) && (selected_object.type === "i-text")) {
                    selected_object.setFontSize(parseInt(font_size));
                    atd_editor.canvas[atd_editor.selected_part].renderAll();
                } else if ((selected_object !== null) && (selected_object.type === "group")) {
                    recreate_group(selected_object);
                }
            }
        });

        $("#txt-align-left").click(function() {
            update_active_object_text_align("left");
        });

        $("#txt-align-center").click(function() {
            update_active_object_text_align("center");
        });

        $("#txt-align-right").click(function() {
            update_active_object_text_align("right");
        });

        $("#txt-align-justify").click(function() {
            update_active_object_text_align("justify");
        });

        function update_active_object_text_align(align) {
            var selected_object = atd_editor.canvas[atd_editor.selected_part].getActiveObject();
            if (!atd_editor.isEmpty(selected_object)) {
                if ((selected_object !== null) && (selected_object.type === "i-text")) {
                    selected_object.set("textAlign", align);
                    atd_editor.canvas[atd_editor.selected_part].renderAll();
                } else if ((selected_object !== null) && (selected_object.type === "group")) {
                    selected_object.forEachObject(function(a) {
                        a.set("textAlign", align);
                        atd_editor.canvas[atd_editor.selected_part].renderAll();
                    });
                }
            }
        }

        //Bold styles
        $("#bold-cb").click(function() {
            var selected_object = atd_editor.canvas[atd_editor.selected_part].getActiveObject();
            var is_bold = $("#bold-cb").hasClass('atd-active');
            if (!atd_editor.isEmpty(selected_object)) {
                if ((selected_object !== null) && (selected_object.type === "i-text")) {
                    if (is_bold)
                        selected_object.set("fontWeight", "bold");
                    else
                        selected_object.set("fontWeight", "normal");
                    atd_editor.canvas[atd_editor.selected_part].renderAll();
                } else if ((selected_object !== null) && (selected_object.type === "group")) {
                    selected_object.forEachObject(function(a) {
                        if (is_bold)
                            a.set("fontWeight", "bold");
                        else
                            a.set("fontWeight", "normal");
                        atd_editor.canvas[atd_editor.selected_part].renderAll();
                    });
                }
            }
        });

        $("#italic-cb").click(function() {
            var selected_object = atd_editor.canvas[atd_editor.selected_part].getActiveObject();
            var is_italic = $("#italic-cb").hasClass('atd-active');
            if (!atd_editor.isEmpty(selected_object)) {
                if ((selected_object !== null) && (selected_object.type === "i-text")) {
                    if (is_italic)
                        selected_object.set("fontStyle", "italic");
                    else
                        selected_object.set("fontStyle", "normal");
                    atd_editor.canvas[atd_editor.selected_part].renderAll();
                } else if ((selected_object !== null) && (selected_object.type === "group")) {
                    selected_object.forEachObject(function(a) {
                        if (is_italic)
                            a.set("fontStyle", "italic");
                        else
                            a.set("fontStyle", "normal");
                        atd_editor.canvas[atd_editor.selected_part].renderAll();
                    });
                }
            }
        });

        $("#atd-outline-size").change(function() {
            var selected_object = atd_editor.canvas[atd_editor.selected_part].getActiveObject();
            if (!atd_editor.isEmpty(selected_object)) {
                if ((selected_object !== null) && (selected_object.type === "i-text")) {
                    if ($(this).val() > 0) {
                        var stroke = $("#txt-outline-color-selector").val();
                        selected_object.set("strokeWidth", parseInt($(this).val()));
                        selected_object.set("stroke", stroke);
                    } else
                        selected_object.set("stroke", false);
                    atd_editor.canvas[atd_editor.selected_part].renderAll();
                } else if ((selected_object !== null) && (selected_object.type === "group")) {
                    recreate_group(selected_object);
                }
            }
        });


        //Is curved checkbox
        function atd_get_curved_state() {
            if ($('.atd-text-curved-wrap #cb-curved').is(':checked')) {

                $('.atd-text-curved-wrap #cb-curved').parents('.atd-text-curved-wrap').find('.atd-text-curved-content').addClass('is-active');
            } else {

                $('.atd-text-curved-wrap #cb-curved').parents('.atd-text-curved-wrap').find('.atd-text-curved-content').removeClass('is-active');
            }
        }

        $("#cb-curved").change(function() {

            atd_get_curved_state();
            var is_curved = $("#cb-curved").is(":checked");
            var selected_object = atd_editor.canvas[atd_editor.selected_part].getActiveObject();

            if (is_curved) {
                if (selected_object !== null) {
                    var left = selected_object.get("left");
                    var top = selected_object.get("top");
                    if (selected_object.type === "i-text") {
                        var text = selected_object.get("text");
                        atd_editor.canvas[atd_editor.selected_part].remove(selected_object);
                        add_curved_text(text, top, left);
                        atd_editor.save_canvas();
                        atd_editor.canvas[atd_editor.selected_part].renderAll();
                        $("#cb-curved").prop('checked', true);
                    }
                }
            } else {
                if (selected_object !== null) {
                    var left = selected_object.get("left");
                    var top = selected_object.get("top");
                    if (selected_object.type === "group") {
                        var text = selected_object.get("originalText");
                        atd_editor.canvas[atd_editor.selected_part].remove(selected_object);
                        atd_editor.add_text(text, top, left, false, false, false);
                        atd_editor.save_canvas();
                        atd_editor.canvas[atd_editor.selected_part].renderAll();
                    }
                }
            }
        });

        $("#curved-txt-radius-slider, #curved-txt-spacing-slider").change(function() {
            var selected_object = atd_editor.canvas[atd_editor.selected_part].getActiveObject();
            if ((selected_object !== null) && (selected_object.type === "group"))
                recreate_group(selected_object);
        });

        $("[id$='opacity-slider']").change(function() {
            var selected_object = atd_editor.canvas[atd_editor.selected_part].getActiveObject();
            if (selected_object !== null) {
                selected_object.set("opacity", $(this).val());
                atd_editor.canvas[atd_editor.selected_part].renderAll();
                atd_editor.save_canvas();
            }
        });

        $("#underline-cb").click(function() {
            update_active_object_decoration("underline");
        });

        $("#strikethrough-cb").click(function() {
            update_active_object_decoration("line-through");
        });

        $("#overline-cb").click(function() {
            update_active_object_decoration("overline");
        });

        $("#txt-none-cb").click(function() {
            update_active_object_decoration("none");
        });

        function update_active_object_decoration(decoration) {
            var selected_object = atd_editor.canvas[atd_editor.selected_part].getActiveObject();
            if (!atd_editor.isEmpty(selected_object)) {
                if ((selected_object !== null) && (selected_object.type === "i-text")) {
                    selected_object.set("textDecoration", decoration);
                    atd_editor.canvas[atd_editor.selected_part].renderAll();
                }
            }
        }

        function add_curved_text(str, custom_top, custom_left) {
            var len = str.length;
            var s;
            var radius = $("#curved-txt-radius-slider").val();
            var spacing = $("#curved-txt-spacing-slider").val();
            var font_color = $("#txt-color-selector ").val();
            if (!radius)
                radius = 150;
            if (!spacing)
                spacing = 10;
            var curAngle = 0;
            var angleRadians = 0;
            var align = 0;
            var centerX = atd_editor.canvas[atd_editor.selected_part].getWidth() / 2;
            var centerY = atd_editor.canvas[atd_editor.selected_part].getHeight() - 30;
            align = (spacing / 2) * (len - 1);
            var reverse = false;
            var coef = 1;
            if (reverse)
                coef = -1;
            var items = [];
            for (var n = 0; n < len; n++) {
                s = str[n];

                var text = create_text_elmt(s);
                curAngle = (n * parseInt(spacing, 10)) - align;
                angleRadians = curAngle * (Math.PI / 180);
                if (reverse) {
                    curAngle = (-n * parseInt(spacing, 10)) + align;
                    angleRadians = curAngle * (Math.PI / 180);
                }

                var top = (centerX + (-Math.cos(angleRadians) * radius)) * coef;
                var left = (centerY + (Math.sin(angleRadians) * radius)) * coef;
                text.set('top', top);
                text.set('left', left);
                text.setAngle(curAngle);
                items.push(text);
            }
            var group = new fabric.Group(items, {
                left: 150,
                top: 100,
                fill: font_color
            });

            if (custom_top !== null)
                atd_editor.canvas[atd_editor.selected_part].setActiveObject(group);
            atd_editor.setCustomProperties(group);
            group["originalText"] = str;
            group["radius"] = radius;
            group["spacing"] = spacing;
            atd_editor.canvas[atd_editor.selected_part].add(group);

            if (custom_top === null)
                group.center();
            else {
                group.set("left", custom_left);
                group.set("top", custom_top);
            }

            atd_editor.save_canvas();
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            group.setCoords();
        }

        function create_text_elmt(txt) {
            var strokeWidth = $("#atd-outline-size").val();
            var fontWeight = "normal";
            var textDecoration = "";
            var fontStyle = "";
            var font_color = $("#txt-color-selector").val();
            var fontFamily = $(".atd-font-container .atd-font-label").text();
            if ($(".atd-font-label").text() === fontFamily) {
                $(".atd-font-container .atd-font-label").text($(".atd-font-drop-down-item:first .atd-font-name").text()) /*.trigger('click')*/ ;
                fontFamily = $(".atd-font-container .atd-font-label").text();
            }
            var font_size = parseInt($("#font-size-selector").val());
            var opacity = $("#opacity-slider").val();
            var strokeColor = $("#txt-outline-color-selector").val();
            var is_bold = $("#bold-cb").hasClass('atd-active');
            var is_italic = $("#italic-cb").hasClass('atd-active');
            var is_underlined = $("#underline-cb").is(":checked");

            if (is_bold)
                fontWeight = "bold";
            if (is_underlined)
                textDecoration = "underline";
            if (is_italic)
                fontStyle = "italic";
            if (!fontFamily)
                fontFamily = 'Arial';
            if (!font_size)
                font_size = 30;
            if (!fontWeight)
                fontWeight = "normal";
            if (!font_color)
                font_color = "rgb(198, 196, 196)";

            if (!opacity)
                opacity = 1;
            var text = new fabric.IText(txt, {
                left: 30,
                top: 70,
                fontFamily: fontFamily,
                fontSize: font_size,
                fontWeight: fontWeight,
                fontStyle: fontStyle,
                textDecoration: textDecoration,
                selectable: true,
                fill: font_color,
                opacity: opacity,
            });
            text.set("id", "toto");
            //text.set("originX", atd.originX);
            text.set("originX", "center");
            text.set("originY", "center");

            if (strokeWidth > 0) {
                text.set("stroke", strokeColor);
                text.set("strokeWidth", parseInt(strokeWidth));
            }
            atd_editor.setCustomProperties(text);

            return text;
        }

        function recreate_group(group) {
            var left = group.get("left");
            var top = group.get("top");
            atd_editor.canvas[atd_editor.selected_part].remove(group);
            add_curved_text(group.originalText, top, left);
        }

        $('#new-text').keyup(function() {
            var selected_object = atd_editor.canvas[atd_editor.selected_part].getActiveObject();
            var new_text = $('#new-text').val();
            if (!atd_editor.isEmpty(selected_object)) {
                if ((selected_object !== null) && (selected_object.type === "i-text")) {
                    selected_object.set("text", new_text);
                    atd_editor.save_canvas();
                    atd_editor.canvas[atd_editor.selected_part].renderAll();
                } else if ((selected_object !== null) && (selected_object.type === "group")) {
                    var left = selected_object.get("left");
                    var top = selected_object.get("top");
                    atd_editor.canvas[atd_editor.selected_part].remove(selected_object);
                    add_curved_text(new_text, top, left);
                }
            }
        });


        $('[id$="color-selector"]').each(function () {
            var id = $(this).attr("id");
            var palette_type = atd.palette_type;
            var palette_tpl = atd.palette_tpl;

            if ($('#' + id).hasClass("atd-team")) {
                if ("team-name-color-selector" == id) {
                    palette_type = atd.atd_team_name_colors_palette_type;
                    palette_tpl = atd.atd_team_name_palette_tpl;
                } else if ("team-number-color-selector" == id) {
                    palette_type = atd.atd_team_number_colors_palette_type;
                    palette_tpl = atd.atd_team_number_palette_tpl;
                }
            }

            if (palette_type === "unlimited") {
                $('#' + id).spectrum({
                    color: "#4f71b9",
                    showInput: true,
                    showAlpha: true,
                    showPalette: false,
                    showButtons: false,
                    preferredFormat: "hex",
                    move: function (color) {
                        if ("txt-outline-color-selector" === id) {
                            var selected_object = atd_editor.canvas[atd_editor.selected_part].getActiveObject();
                            if (!atd_editor.isEmpty(selected_object)) {
                                if ((selected_object !== null) && (selected_object.type === "i-text")) {
                                    if ($("#atd-outline-size").val() > 0) {
                                        var stroke = $("#txt-outline-color-selector").val();
                                        selected_object.set("strokeWidth", parseInt($("#atd-outline-size").val()));
                                        selected_object.set("stroke", stroke);
                                    } else
                                        selected_object.set("stroke", false);
                                    atd_editor.canvas[atd_editor.selected_part].renderAll();
                                } else if ((selected_object !== null) && (selected_object.type === "group")) {
                                    recreate_group(selected_object);
                                }
                            }
                        }
                        if ("team-name-color-selector" === id) {
                            atd_EDITOR.click_part("name");
                            var selected_object = atd_EDITOR.findByCliperName("#atd-team-name");
                            atd_EDITOR.canvas[atd_EDITOR.selected_part].setActiveObject(selected_object);
                            atd_EDITOR.canvas[atd_EDITOR.selected_part].renderAll();
                            atd_EDITOR.save_canvas();
                        } else if ("team-number-color-selector" === id) {
                            atd_EDITOR.click_part("number");
                            var selected_object = atd_EDITOR.findByCliperName("#atd-team-number");
                            atd_EDITOR.canvas[atd_EDITOR.selected_part].setActiveObject(selected_object);
                            atd_EDITOR.canvas[atd_EDITOR.selected_part].renderAll();
                            atd_EDITOR.save_canvas();
                        }
                        $('#' + id).val(color);
                        atd_EDITOR.change_item_color(id, $('#' + id).val());
                    }
                });
            } else {
                $('#' + id).spectrum({
                    color: "#4f71b9",
                    showInput: true,
                    showAlpha: true,
                    showPaletteOnly: true,
                    showPalette: true,
                    showButtons: false,
                    preferredFormat: "hex",
                    palette: palette_tpl,
                    move: function (color) {
                        if ("txt-outline-color-selector" === id) {
                            var selected_object = atd_editor.canvas[atd_editor.selected_part].getActiveObject();
                            if (!atd_editor.isEmpty(selected_object)) {
                                if ((selected_object !== null) && (selected_object.type === "i-text")) {
                                    if ($("#atd-outline-size").val() > 0) {
                                        var stroke = $("#txt-outline-color-selector").val();
                                        selected_object.set("strokeWidth", parseInt($("#atd-outline-size").val()));
                                        selected_object.set("stroke", stroke);
                                    } else
                                        selected_object.set("stroke", false);
                                    atd_editor.canvas[atd_editor.selected_part].renderAll();
                                } else if ((selected_object !== null) && (selected_object.type === "group")) {
                                    recreate_group(selected_object);
                                }
                            }
                        }
                        if ("team-name-color-selector" === id) {
                            atd_EDITOR.click_part("name");
                            var selected_object = atd_EDITOR.findByCliperName("#atd-team-name");
                            atd_EDITOR.canvas[atd_EDITOR.selected_part].setActiveObject(selected_object);
                            atd_EDITOR.canvas[atd_EDITOR.selected_part].renderAll();
                            atd_EDITOR.save_canvas();
                        } else if ("team-number-color-selector" === id) {
                            atd_EDITOR.click_part("number");
                            var selected_object = atd_EDITOR.findByCliperName("#atd-team-number");
                            atd_EDITOR.canvas[atd_EDITOR.selected_part].setActiveObject(selected_object);
                            atd_EDITOR.canvas[atd_EDITOR.selected_part].renderAll();
                            atd_EDITOR.save_canvas();
                        }
                        $('#' + id).val(color);
                        atd_EDITOR.change_item_color(id, $('#' + id).val());
                    }
                });
            }

        });
    });
    return atd_editor;
}(jQuery, (atd_EDITOR || {})));