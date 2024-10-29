/* global atd, canvas */
var atd_EDITOR = (function ($, atd_editor) {

    $(document).on("touchstart click", "#grid-btn", function (e)
    {
        $(".upper-canvas").toggleClass("atd-canvas-grid");
    });

    $(document).on("touchstart click", "#clear_all_btn", function (e)
    {
        e.preventDefault();
        var is_confirmed = confirm(atd.translated_strings.delete_all_msg);
        if ($(".atd-checkbox-add-name").is(':checked')) {
            $(".atd-checkbox-add-name").click();
        }

        if ($(".atd-checkbox-add-number").is(':checked')) {
            $(".atd-checkbox-add-number").click();
        }
    });

    $(document).on("touchstart click", "#delete_btn", function (e)
    {
        e.preventDefault();
        var selected_object = atd_editor.canvas[atd_editor.selected_part].getActiveObject();
        var selected_group = atd_editor.canvas[atd_editor.selected_part].getActiveGroup();
        var is_confirmed;
        if (selected_object !== null)
        {
            if (selected_object['lockDeletion']) {
                alert(atd.translated_strings.deletion_error_msg);
            } else
            {
                is_confirmed = confirm(atd.translated_strings.delete_msg);
                if (is_confirmed)
                {
                    if ("#atd-team-name" === selected_object.clipTeam) {
                        $(".atd-checkbox-add-name").click();
                    } else if ("#atd-team-number" === selected_object.clipTeam) {
                        $(".atd-checkbox-add-number").click();
                    }
                    atd_editor.canvas[atd_editor.selected_part].remove(selected_object);
                    atd_editor.canvas[atd_editor.selected_part].calcOffset();
                    atd_editor.canvas[atd_editor.selected_part].renderAll();
                    atd_editor.save_canvas();
                }
            }
            ;
        } else if (selected_group !== null)
        {
            if (selected_group['lockDeletion']) {
                alert(atd.translated_strings.deletion_error_msg);
            } else
            {
                is_confirmed = confirm('Do you really want to delete the selected items?');
                if (is_confirmed)
                {
                    selected_group.forEachObject(function (a) {
                        if ("#atd-team-name" === a.clipTeam) {
                            $(".atd-checkbox-add-name").click();
                        } else if ("#atd-team-number" === a.clipTeam) {
                            $(".atd-checkbox-add-number").click();
                        }
                        atd_editor.canvas[atd_editor.selected_part].remove(a);
                    });
                    atd_editor.canvas[atd_editor.selected_part].discardActiveGroup();
                    atd_editor.canvas[atd_editor.selected_part].calcOffset();
                    atd_editor.canvas[atd_editor.selected_part].renderAll();
                    atd_editor.save_canvas();
                }

            }
            ;
        }
    });

    $(document).on("touchstart click", "#copy_paste_btn", function (e)
    {
        e.preventDefault();
        var selected_object = atd_editor.canvas[atd_editor.selected_part].getActiveObject();
        var selected_group = atd_editor.canvas[atd_editor.selected_part].getActiveGroup();
        if (selected_group !== null)
        {
            var new_group = new fabric.Group();
            atd_editor.canvas[atd_editor.selected_part].discardActiveGroup();
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            var objects = atd_editor.canvas[atd_editor.selected_part].getObjects();
            $.each(objects, function (key, current_item)
            {
                if (selected_group.contains(current_item))
                {
                    cloneObject(current_item, false);
                }
            });
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            atd_editor.save_canvas();
        } else if (selected_object !== null)
        {
            atd_editor.canvas[atd_editor.selected_part].discardActiveObject();
            cloneObject(selected_object, true);
            atd_editor.save_canvas();
        }
    });

    function cloneObject(object, render_after)
    {
        var new_object = fabric.util.object.clone(object);
        new_object.set("top", new_object.top + 5);
        new_object.set("left", new_object.left + 5);
        atd_editor.setCustomProperties(new_object);
        atd_editor.canvas[atd_editor.selected_part].add(new_object);
        if (render_after)
        {
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            atd_editor.save_canvas();
        }

    }

    $(document).on("touchstart click", "#bring_to_front_btn", function (e)
    {
        e.preventDefault();
        var selected_object = atd_editor.canvas[atd_editor.selected_part].getActiveObject();
        var selected_group = atd_editor.canvas[atd_editor.selected_part].getActiveGroup();
        if (selected_object !== null)
        {
            atd_editor.canvas[atd_editor.selected_part].bringForward(selected_object);
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            atd_editor.save_canvas();

        } else if (selected_group !== null)
        {
            selected_group.forEachObject(function (a) {
                atd_editor.canvas[atd_editor.selected_part].bringForward(a);
            });
            atd_editor.canvas[atd_editor.selected_part].discardActiveGroup();
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            atd_editor.save_canvas();
        }
    });

    $(document).on("touchstart click", "#send_to_back_btn", function (e)
    {
        e.preventDefault();
        var selected_object = atd_editor.canvas[atd_editor.selected_part].getActiveObject();
        var selected_group = atd_editor.canvas[atd_editor.selected_part].getActiveGroup();
        if (selected_object !== null)
        {
            atd_editor.canvas[atd_editor.selected_part].sendBackwards(selected_object);
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            atd_editor.save_canvas();
        } else if (selected_group !== null)
        {
            selected_group.forEachObject(function (a) {
                atd_editor.canvas[atd_editor.selected_part].sendBackwards(a);
            });
            atd_editor.canvas[atd_editor.selected_part].discardActiveGroup();
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            atd_editor.save_canvas();

        }
    });

    $(document).on("touchstart click", "#flip_h_btn", function (e)
    {
        e.preventDefault();
        var selected_object = atd_editor.canvas[atd_editor.selected_part].getActiveObject();
        var selected_group = atd_editor.canvas[atd_editor.selected_part].getActiveGroup();
        if (selected_object !== null)
        {
            if (selected_object.get("flipX") === true)
                selected_object.set("flipX", false);
            else
                selected_object.set("flipX", true);
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            atd_editor.save_canvas();
        } else if (selected_group !== null)
        {
            if (selected_group.get("flipX") === true)
                selected_group.set("flipX", false);
            else
                selected_group.set("flipX", true);
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            atd_editor.save_canvas();
        }
    });

    $(document).on("touchstart click", "#flip_v_btn", function (e)
    {
        e.preventDefault();
        var selected_object = atd_editor.canvas[atd_editor.selected_part].getActiveObject();
        var selected_group = atd_editor.canvas[atd_editor.selected_part].getActiveGroup();
        if (selected_object !== null)
        {
            if (selected_object.get("flipY") === true)
                selected_object.set("flipY", false);
            else
                selected_object.set("flipY", true);
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            atd_editor.save_canvas();
        } else if (selected_group !== null)
        {
            if (selected_group.get("flipY") === true)
                selected_group.set("flipY", false);
            else
                selected_group.set("flipY", true);
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            atd_editor.save_canvas();
        }

    });

    $(document).on("touchstart click", "#align_h_btn", function (e)
    {
        e.preventDefault();
        var selected_object = atd_editor.canvas[atd_editor.selected_part].getActiveObject();
        var selected_group = atd_editor.canvas[atd_editor.selected_part].getActiveGroup();
        if (selected_object !== null)
        {
            atd_editor.centerObjectH(selected_object);
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            selected_object.setCoords();
            atd_editor.save_canvas();
        } else if (selected_group !== null)
        {
            atd_editor.centerObjectH(selected_group);
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            selected_group.setCoords();
            atd_editor.save_canvas();
        }
    });

    $(document).on("touchstart click", "#align_v_btn", function (e)
    {
        e.preventDefault();
        var selected_object = atd_editor.canvas[atd_editor.selected_part].getActiveObject();
        var selected_group = atd_editor.canvas[atd_editor.selected_part].getActiveGroup();
        if (selected_object !== null)
        {
            atd_editor.centerObjectV(selected_object);
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            selected_object.setCoords();
            atd_editor.save_canvas();
        } else if (selected_group !== null)
        {
            atd_editor.centerObjectV(selected_group);
            atd_editor.canvas[atd_editor.selected_part].renderAll();
            selected_group.setCoords();
            atd_editor.save_canvas();
        }
    });

    $(document).on("touchstart click", "#undo-btn", function (e)
    {
        e.preventDefault();
        var current_data_id = $("#atd-parts-bar > li:eq(" + atd_editor.selected_part + ")").attr("data-id");
        if (!$(this).hasClass("disabled") && atd_editor.canvasManipulationsPosition[current_data_id] > 0)
        {
            atd_editor.canvas[atd_editor.selected_part].clear();
            atd_editor.canvasManipulationsPosition[current_data_id]--;
            atd_editor.canvas[atd_editor.selected_part].loadFromJSON(atd_editor.serialized_parts[current_data_id][atd_editor.canvasManipulationsPosition[current_data_id]]);
            atd_editor.refresh_undo_redo_status();
        }
    });

    $(document).on("touchstart click", "#redo-btn", function (e)
    {
        e.preventDefault();
        var current_data_id = $("#atd-parts-bar > li:eq(" + atd_editor.selected_part + ")").attr("data-id");
        var next_index = atd_editor.canvasManipulationsPosition[current_data_id] + 1;
        if (!$(this).hasClass("disabled") && typeof atd_editor.serialized_parts[current_data_id][next_index] !== "undefined")
        {
            atd_editor.canvas[atd_editor.selected_part].clear();
            atd_editor.canvasManipulationsPosition[current_data_id] = next_index;
            atd_editor.canvas[atd_editor.selected_part].loadFromJSON(atd_editor.serialized_parts[current_data_id][next_index]);
            atd_editor.refresh_undo_redo_status();
        }
    });
    return atd_editor;
}(jQuery, (atd_EDITOR || {})));