/* global atd, ajax_object */
var atd_EDITOR = (function($, atd_editor) {
    'use strict';
    $(document).ready(function() {
        atd_editor = atd_EDITOR;
        //Lazy load
        function init_lazy_loader(container) {
            $("img.o-lazy").lazyload({
                placeholder: atd.lazy_placeholder,
                container: $(container),
                skip_invisible: true
            });
        }

        init_lazy_loader("#atd-cliparts-wrapper");

        $(document).on("click", ".atd-cliparts-groups>li,#atd-search-cliparts-group-results > li", function(e) {
            show_all_cliparts();
            var group_id = $(this).data("groupid");
            var group_name = $(this).html();
            $(".atd-cliparts-container").hide();
            $(".atd-cliparts-groups>li").removeClass("selected");
            $(this).addClass("selected");
            $(".atd-cliparts-container[data-groupid=" + group_id + "]").css('display', 'contents');
            setTimeout(function() {
                $(".atd-cliparts-container[data-groupid=" + group_id + "]").trigger("scroll");
            }, 500);
            $('#atd-clipart-group-selected').html(group_name);
            $('#cliparts-panel-content-child').addClass('is-open');

        });

        $(".atd-cliparts-container").bind('scrollstop', function(e) {
            $(".atd-cliparts-container:visible").trigger("scroll");
        });

        $(document).on("click", "#atd-cliparts-opener", function(e) {
            $(".atd-cliparts-groups>li").first().click();
        });

        //setup before functions
        var typingTimer; //timer identifier
        var doneTypingInterval = 1000; //time in ms, 5 second for example
        var $input = $('#atd-cliparts-search');
        var $input_group = $('#atd-cliparts-group-search');

        //on keyup, start the countdown
        $input.on('keyup', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
        });

        //clipart_group
        $input_group.on('keyup', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(doneTyping_group, doneTypingInterval);
        });

        //on keydown, clear the countdown 
        $input.on('keydown', function() {
            clearTimeout(typingTimer);
        });

        //clipart_group
        $input_group.on('keydown', function() {
            clearTimeout(typingTimer);
        });

        //user is "finished typing," do something
        function doneTyping() {
            $("#atd-search-cliparts-results").html("");
            var preview_list = $('#atd-all-cliparts').html();
            var searched_value = $input.val().toLowerCase();
            if (searched_value.length > 0) {
                var results = $('.atd-cliparts-groups li').filter(function() {
                    return $(this).attr('data-group-name').toLowerCase().indexOf(searched_value) > -1;
                });
                if (results.length) {
                    var get_result_list = {};
                    var result = [];
                    var get_id;
                    $.each(results, function(index, value) {
                        get_result_list = value;
                        get_id = $(this).attr('data-groupid');
                        result.push(get_id);
                    });

                    var unique = result.filter(function(get_id, i, a) {
                        return i === result.indexOf(get_id);
                    });
                    if (unique.length) {
                        var recup = $('#atd-all-cliparts .atd-cliparts-container').filter(function() {
                            if ($.inArray($(this).attr('data-groupid'), unique) >= 0)
                                return 1;
                        });
                        if (recup.length) {
                            var get_result_clone = recup.clone();
                            $("#atd-search-cliparts-results").html(get_result_clone);
                        }
                    } else {
                        $("#atd-search-cliparts-results").html(atd.translated_strings.cliparts_search_no_result);
                    }

                } else {
                    $("#atd-search-cliparts-results").html(atd.translated_strings.cliparts_search_no_result);
                }
                show_cliparts_search_results();
            } else {
                $("#atd-all-cliparts").css('display', 'contents');
                $("#atd-search-cliparts-results").hide();
            }

        }

        //clipart_group
        function doneTyping_group() {
            $("#atd-search-cliparts-group-results").html("");
            var preview_list = $('.atd-cliparts-groups').html();
            var searched_value = $input_group.val().toLowerCase();
            if (searched_value.length > 0) {
                var results = $('.atd-cliparts-groups li').filter(function() {
                    return $(this).attr('data-group-name').toLowerCase().indexOf(searched_value) > -1;
                });
                if (results.length) {
                    var get_result_list = {};
                    var result = [];
                    var get_id;
                    $.each(results, function(index, value) {
                        get_result_list = value;
                        get_id = $(this).attr('data-groupid');
                        result.push(get_id);
                    });
                    var unique = result.filter(function(get_id, i, a) {
                        return i === result.indexOf(get_id);
                    });
                    if (unique.length) {
                        var recup = $('#atd-all-cliparts .atd-cliparts-container').filter(function() {
                            if ($.inArray($(this).attr('data-groupid'), unique) >= 0)
                                return 1;
                        });
                        if (recup.length) {
                            var get_result_clone = recup.clone();
                            $("#atd-search-cliparts-group-results").html(get_result_clone);
                        }
                    } else {
                        $("#atd-search-cliparts-group-results").html(atd.translated_strings.cliparts_search_no_result);
                    }

                } else {
                    $("#atd-search-cliparts-group-results").html(atd.translated_strings.cliparts_search_no_result);
                }

            } else {
                $("#atd-search-cliparts-group-results").html(preview_list);
            }

            show_cliparts_group_search_results();
        }

        function show_cliparts_group_search_results() {
            $(".atd-cliparts-groups").hide();
            $("#atd-search-cliparts-group-results").css('display', 'contents');
            $("#atd-search-cliparts-group-results .atd-cliparts-container").css('display', 'contents');
            init_lazy_loader("#atd-search-cliparts-group-results");
        }

        function show_cliparts_search_results() {
            $("#atd-all-cliparts").hide();
            $("#atd-search-cliparts-results").css('display', 'contents');
            $("#atd-search-cliparts-results .atd-cliparts-container").css('display', 'contents');
            init_lazy_loader("#atd-search-cliparts-results");
        }

        function show_all_cliparts() {
            $("#atd-all-cliparts").css('display', 'contents');
            $("#atd-search-cliparts-results").hide();
        }

        $(document).on('click', '.atd-preview-clippart-item, .atd-preview-upolad-item', function() {
            var medium_url = $(this).attr("data-url");
            if (typeof medium_url === "undefined")
                medium_url = $(this).attr("data-original");
            var price = $(this).data("price");
            add_img_on_editor(medium_url, price);
            //$('#atd-cliparts-modal').omodal("hide");
        });

        $(document).on("click", "#atd-add-img", function(e) {
            e.preventDefault();
            var selector = $(this).attr('data-selector');
            var trigger = $(this);
            var uploader = wp.media({
                    title: 'Add image on the design area',
                    button: {
                        text: "Add image"
                    },
                    multiple: false
                })
                .on('select', function() {
                    var selection = uploader.state().get('selection');
                    selection.map(
                        function(attachment) {
                            attachment = attachment.toJSON();
                            add_img_on_editor(attachment.url);
                        }
                    );
                })
                .open();
        });

        $("#userfile").change(function() {
            var file = $(this).val().toLowerCase();
            if (file !== "") {
                $("#userfile_upload_form").ajaxForm({
                    success: upload_image_callback
                }).submit();
            }
        });

        $('.drop a').click(function() {
            // Simulate a click on the file input button
            // to show the file browser dialog
            $(this).parent().find('input').click();
        });
        if ($('#userfile_upload_form').length) {
            // var userfile_ul = $('#userfile_upload_form.custom-uploader .acd-upload-info');
            // Initialize the jQuery File Upload plugin
            $('#userfile_upload_form').fileupload({

                // This element will accept file drag/drop uploading
                dropZone: $('.drop'),
                url: ajax_object.ajax_url,
                // This function is called when a file is added to the queue;
                // either via the browse button, or via drag/drop:
                add: function(e, data) {
                    var split = data.files[0].name.split(".");
                    var extension = split[split.length - 1];
                    if ($.inArray(extension, atd.valid_formats) !== -1) {
                        // console.log(atd.valid_formats);
                        // console.log(extension);
                        // var tpl = $('<div class="working"><div class="acd-info"></div><div class="acd-progress-bar"><div class="acd-progress"></div></div></div>');
                        // // Append the file name and file size
                        // tpl.find('.acd-info').text(data.files[0].name).append('<i>' + formatFileSize(data.files[0].size) + '</i>');
                        // // Add the HTML to the UL element
                        // userfile_ul.html("");
                        $(".atd-loader-label").addClass("atd-show");
                        // data.context = tpl.appendTo(userfile_ul);
                        // // Initialize the knob plugin
                        // tpl.find('input').knob();
                        // // Listen for clicks on the cancel icon
                        // tpl.find('span').click(function() {

                        //     if (tpl.hasClass('working')) {
                        //         jqXHR.abort();
                        //     }

                        //     tpl.fadeOut(function() {
                        //         tpl.remove();
                        //     });
                        // });

                        // Automatically upload the file once it is added to the queue
                        var jqXHR = data.submit();
                    } else {
                        alert("Incorrect file extension: " + extension + ". Allowed extensions: " + atd.valid_formats.join(","));
                    }
                },
                progress: function(e, data) {
                    // Calculate the completion percentage of the upload
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    // Update the hidden input field and trigger a change
                    // so that the jQuery knob plugin knows to update the dial
                    // data.context.find('.acd-progress').css("width", progress + "%");
                    if (progress === 100) {
                        // data.context.removeClass('working');
                        $(".atd-loader-label").removeClass("atd-show");
                    }
                },
                fail: function(e, data) {
                    // Something has gone wrong!
                    data.context.addClass('error');
                },
                done: function(e, data) {
                    var name = data.files[0].name;
                    upload_image_callback(data.result, false, false, false);
                }
            });
        }

        //delete image on click on close icon
        $(document).on('touchstart click', 'div.atd-icon-cross', function(e) {

            e.stopPropagation();

            var remove = $(this);
            var img_id = $(this).closest(".atd-preview-upolad-item").attr("data-upload-id");
            var img_name = $(this).closest(".atd-preview-upolad-item").attr("data-img-name");
            var img_url = $(this).closest(".atd-preview-upolad-item").attr("data-url");

            if (undefined !== img_id && undefined !== img_name && undefined !== img_url) {
                var frm_data = new FormData();
                frm_data.append("img_id", img_id);
                frm_data.append("img_name", img_name);
                frm_data.append("img_url", img_url);
                frm_data.append("action", "handle_delete_picture_upload");
                $.ajax({
                    type: 'POST',
                    url: ajax_object.ajax_url,
                    data: frm_data,
                    processData: false,
                    contentType: false
                }).done(function(data) {
                    if (atd_editor.is_json(data)) {
                        var response = JSON.parse(data);
                        if (1 === response) {
                            remove.closest(".atd-preview-upolad-item").remove();
                            alert("Upload delete successfuly!");
                        } else {
                            alert("Unable to delete image");
                        }
                    }
                });
            }
        });

        // Prevent the default action when a file is dropped on the window
        $(document).on('drop dragover', function(e) {
            e.preventDefault();
        });

        $('.acd-grayscale').change(function() {
            apply_filter(new fabric.Image.filters.Grayscale(), $(this).is(':checked'));

        });

        $('.acd-invert').change(function() {
            apply_filter(new fabric.Image.filters.Invert(), $(this).is(':checked'));
        });

        $('.acd-sepia').change(function() {
            apply_filter(new fabric.Image.filters.Sepia(), $(this).is(':checked'));
        });

        $('.acd-sepia2').change(function() {
            apply_filter(new fabric.Image.filters.Sepia2(), $(this).is(':checked'));
        });

        $('.acd-blur').change(function() {
            if ($(this).is(':checked'))
                $("#sharpen, #emboss").removeAttr('checked');

            apply_filter(new fabric.Image.filters.Convolute({
                    matrix: [1 / 9, 1 / 9, 1 / 9,
                        1 / 9, 1 / 9, 1 / 9,
                        1 / 9, 1 / 9, 1 / 9
                    ]
                }),
                $(this).is(':checked'));
        });

        $('.acd-sharpen').change(function() {
            if ($(this).is(':checked'))
                $("#blur, #emboss").removeAttr('checked');

            apply_filter(new fabric.Image.filters.Convolute({
                    matrix: [0, -1, 0, -1, 5, -1,
                        0, -1, 0
                    ]
                }),
                $(this).is(':checked'));
        });

        $('.acd-emboss').change(function() {
            if ($(this).is(':checked'))
                $("#sharpen, #blur").removeAttr('checked');

            apply_filter(new fabric.Image.filters.Convolute({
                matrix: [1, 1, 1,
                    1, 0.7, -1, -1, -1, -1
                ]
            }), $(this).is(':checked'));
        });

        function apply_filter(filter, toApply) {

            var selected_object = atd_editor.canvas[atd_editor.selected_part].getActiveObject();

            var filter_index = jQuery.inArray(filter.type.toLowerCase(), atd_editor.arr_filters);
            if (!atd_editor.isEmpty(selected_object)) {
                if ((selected_object !== null) && (selected_object.type === "image")) {
                    if (toApply)
                        selected_object.filters[filter_index] = filter;
                    else
                        selected_object.filters[filter_index] = false;

                    selected_object.applyFilters(atd_editor.canvas[atd_editor.selected_part].renderAll.bind(atd_editor.canvas[atd_editor.selected_part]));
                    atd_editor.save_canvas();
                }
            }
        }

        atd_editor.is_json = function(data) {
            if (/^[\],:{}\s]*$/.test(data.replace(/\\["\\\/bfnrtu]/g, '@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').replace(/(?:^|:|,)(?:\s*\[)+/g, '')))
                return true;
            else
                return false;
        };

        function add_img_on_editor(url, price) {
            var ext = url.split('.').pop();
            if (typeof price === "undefined")
                price = 0;
            if (ext === "svg") {
                fabric.loadSVGFromURL(url, function(objects, options) {
                    var obj = fabric.util.groupSVGElements(objects, options);
                    optimize_img_width(obj);
                    atd_editor.setCustomProperties(obj);
                    obj.set("price", price);
                    obj.set("originX", "center");
                    obj.set("originY", "center");
                    wp.hooks.doAction('atd_EDITOR.before_adding_img_on_canvas', obj);
                    if (atd_editor.box_center_x) {
                        atd_editor.centerObject(obj);
                        atd_editor.canvas[atd_editor.selected_part].add(obj).calcOffset().renderAll();
                    } else {
                        atd_editor.canvas[atd_editor.selected_part].add(obj);
                        atd_editor.centerObject(obj);
                        atd_editor.canvas[atd_editor.selected_part].calcOffset().renderAll();
                    }
                    obj.setCoords();
                    atd_editor.save_canvas();
                });
            } else {
                fabric.Image.fromURL(url, function(img) {
                    optimize_img_width(img);
                    atd_editor.setCustomProperties(img);
                    img.set("price", price);
                    img.set("originX", "center");
                    img.set("originY", "center");
                    wp.hooks.doAction('atd_EDITOR.before_adding_img_on_canvas', img);
                    if (atd_editor.box_center_x) {
                        atd_editor.canvas[atd_editor.selected_part].add(img.set({
                            angle: 0
                        })).renderAll();
                        atd_editor.centerObject(img);
                        atd_editor.canvas[atd_editor.selected_part].calcOffset().renderAll();
                    } else {
                        atd_editor.canvas[atd_editor.selected_part].add(img.set({
                            angle: 0
                        }));
                        atd_editor.centerObject(img);
                        atd_editor.canvas[atd_editor.selected_part].calcOffset().renderAll();
                    }
                    wp.hooks.doAction('atd_EDITOR.after_adding_img_on_canvas', img);
                    atd_editor.canvas[atd_editor.selected_part].setActiveObject(img);
                    img.setCoords();
                    atd_editor.save_canvas();
                }, { crossOrigin: 'anonymous' });
            }
        }

        function optimize_img_width(obj) {
            var displayable_area_width = atd_editor.canvas[atd_editor.selected_part].getWidth();
            var displayable_area_height = atd_editor.canvas[atd_editor.selected_part].getHeight();
            if (atd.clip_w && atd.clip_h && atd.clip_w > 0 && atd.clip_h > 0 && atd.clip_type === "rect") {
                displayable_area_width = atd.clip_w;
                displayable_area_height = atd.clip_h;
            } else if (atd.clip_r && atd.clip_r > 0 && atd.clip_type === "arc") {
                displayable_area_width = atd.clip_r;
                displayable_area_height = atd.clip_r;
            }
            var dimensions = atd_editor.get_img_best_fit_dimensions(obj, displayable_area_width, displayable_area_height);
            var scaleW = displayable_area_width / dimensions[0];
            var scaleH = displayable_area_height / dimensions[1];
            if (scaleW > scaleH)
                obj.scaleToWidth(dimensions[0]);
            else
                obj.scaleToHeight(dimensions[1]);
            return dimensions;
        }

        function upload_image_callback(responseText, statusText, xhr, form) {
            if (atd_editor.is_json(responseText)) {
                var response = $.parseJSON(responseText);
                if (response.success) {
                    if (0 === $(".atd-preview-upolad-container .atd-preview-upolad-item").length) {
                        $(".atd-preview-upolad-container").html("");
                    }
                    $(".atd-preview-upolad-container").append(response.message);
                    if (response.img_url)
                        add_img_on_editor(response.img_url, 0);
                    wp.hooks.applyFilters('atd.after_upload_image');
                } else
                    alert(response.message);
            } else
                $("#debug").html(responseText);
            $("#userfile").val("");
        }

        // Helper function that formats the file sizes
        function formatFileSize(bytes) {
            if (typeof bytes !== 'number') {
                return '';
            }

            if (bytes >= 1000000000) {
                return (bytes / 1000000000).toFixed(2) + ' GB';
            }

            if (bytes >= 1000000) {
                return (bytes / 1000000).toFixed(2) + ' MB';
            }

            return (bytes / 1000).toFixed(2) + ' KB';
        }
    });

    return atd_editor;
}(jQuery, (atd_EDITOR || {})));