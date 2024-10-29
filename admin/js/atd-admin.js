(function ($) {
	'use strict';
	$( document ).ready(
		function () {

				$( document ).on(
					"change",
					".atd_variation_actions",
					function () {
							var $this_Section = $( this ).closest( ".woocommerce_variation" );
							show_hide_config_part();
							$this_Section.find( '.wc-metabox-content' ).css( 'display', '' );
					}
				);

			function show_hide_config_part() {
				var get_variationselect = $( ".atd_variation_actions" );
				get_variationselect.each(
					function () {
							var this_Section = $( this ).closest( ".woocommerce_variation" );
							var config_id    = $( this ).val();
						if (config_id !== "") {
							config_id          = parseInt( config_id );
							var get_atd_config = atd_configs[config_id];
							if (get_atd_config != undefined) {
								$.each(
									get_atd_config,
									function (index, value) {
											if(!isNaN) var child_section = (index).toLowerCase();
											else var child_section = index;
											
											child_section     = $( this_Section ).find( ".atd-" + child_section );
										if (value.enable !== "yes") {
											$( child_section ).hide();
										} else {
											$( child_section ).show();
										}

									}
								);
							}
						} else {
							$( this_Section ).find( ".atd-data-item" ).hide();
						}

					}
				);
			}

				show_hide_config_part();

				$( ".atd_target_variation" ).click(
					function (e) {
						e.stopPropagation();
					}
				);

				$( document ).on(
					"click",
					".atd-add-media",
					function (e) {
							e.preventDefault();
							var trigger  = $( this );
							var uploader = wp.media(
								{
									title: 'Please set the picture',
									button: {
										text: "Select picture(s)"
									},
									multiple: false
									}
							)
									.on(
										'select',
										function () {
												var selection = uploader.state().get( 'selection' );
												selection.map(
													function (attachment) {
															attachment           = attachment.toJSON();
															var url_without_root = attachment.url.replace( home_url, "" );
															trigger.find( "input[type=hidden]" ).val( url_without_root );
															trigger.prepend( "<img src='" + attachment.url + "'>" );
															trigger.find( ".media-name" ).html( attachment.filename );
															trigger.attr( "class", "upload_image_button atd-remove-media atd-media-preview remove" );
														if (trigger.hasClass( "trigger-change" )) {
															trigger.find( "input[type=hidden]" ).trigger( "propertychange" );
														}
													}
												);
										}
									)
									.open();
					}
				);

				$( document ).on(
					"click",
					".atd-remove-media",
					function (e) {
							e.preventDefault();
							$( this ).find( "img" ).remove();
							$( this ).find( "input[type=hidden]" ).val( "" );
							$( this ).find( ".media-name" ).html( "" );
							$( this ).attr( "class", "upload_image_button atd-add-media atd-media-preview tips" );
						if ($( this ).hasClass( "trigger-change" )) {
							$( this ).find( "input[type=hidden]" ).trigger( "propertychange" );
						}

					}
				);
				var isSimpleProduct = $( "#product-type" ).val() === "simple";
			if (isSimpleProduct) {
				$( "#hide_toolbar" ).css( "display", "none" );
			}
				$( "#product-type" ).change(
					function () {
							var isSimProduct = $( "#product-type" ).val() === "simple";
							var isVarProduct = $( "#product-type" ).val() === "variable";
						if (isVarProduct) {
							$( "#hide_toolbar" ).css( "display", "inherit" );
							$("#atd-attr-selected-section").css( "display", "initial" );
						}
						if (isSimProduct) {
							$( "#hide_toolbar" ).css( "display", "none" );
							$("#atd-attr-selected-section").css( "display", "none" );
						}
					}
				);
				/** Begin Modal Script */

				$( "#atdConfModal" ).click(
					function () {
							var optionSelected = $( "#atd-global-action" ).find( ":selected" ).val();

						if (optionSelected === "set_default_config") {
							var target = "atdConfModal";

							var $modalTarget = $( ".wb-modal[data-wb-target='" + target + "']" );

							var $overlayTarget = $(
								".wb-modal-overlay[data-wb-target='" + target + "']"
							);

							$modalTarget.addClass( "wb-show" );

							$overlayTarget.addClass( "wb-show" );
						}
					}
				);

				$( '.wb-modal-overlay[data-wb-target="atdConfModal"], .wb-btn-cancel' ).click(
					function () {
							var target = "atdConfModal";

							var $modalTarget = $( ".wb-modal[data-wb-target='" + target + "']" );

							var $overlayTarget = $(
								".wb-modal-overlay[data-wb-target='" + target + "']"
							);

							$modalTarget.removeClass( "wb-show" );

							$overlayTarget.removeClass( "wb-show" );
					}
				);

				$( '#atd-modal-configs select' ).addClass( "wb-form-field wb-field-select" );

				$( ".wb-btn.wb-btn-assign" ).click(
					function () {
							$( ".woocommerce_variation select" ).val( $( "#atd-modal-configs select" ).val() );
							$( ".wb-btn-cancel" ).click();
                                                        show_hide_config_part();

					}
				);

				$( document ).on(
					"click",
					"#atd-create-design-page",
					function (e) {
							e.preventDefault();
							var page_name = '';
						while (page_name == '') {
							page_name = prompt( "Please enter your page name" );
						}

							$.post(
								ajax_object.ajax_url,
								{
									action: "create_design_page",
									name_page: page_name,
									},
								function (data) {
										alert( data );
										location.reload();

								}
							);
					}
				);
				$( document ).on(
					'woocommerce_variations_saved',
					function (event) {
							alert( "Your variations have been changed.Your page will be updated so that these variations are taken into account in the configuration of your t-shirt" );
							location.reload();
					}
				);

				setTimeout(
					function () {
							$( "#font" ).select2( {allowClear: true, placeholder: "Pick a google font"} );
					},
					500
				);

				load_select2();

			function load_select2(container)
				{
				if (typeof container == "undefined") {
					container = "";
				}
				$( container + " select.o-select2" ).each(
					function () {
							$( this ).select2( {allowClear: true} );
					}
				);
			}

				$( document ).on(
					'change',
					'#font',
					function () {
							var name = $( '#font  option:selected' ).text();
							var url  = $( '#font   option:selected' ).val();
							$( '.font_auto_name' ).val( name );
							$( '.font_auto_url' ).val( url );

					}
				);

				$( document ).on(
					"click",
					".o-add-font-file",
					function (e) {
							e.preventDefault();
							var uploader = wp.media(
								{
									title: 'Please set the file',
									button: {
										text: "Select file(s)"
									},
									multiple: false
									}
							)
									.on(
										'select',
										function () {
												var selection = uploader.state().get( 'selection' );
												selection.map(
													function (attachment) {
															attachment         = attachment.toJSON();
															var new_rule_index = $( ".font_style_table tbody tr" ).length;
															var font_tpl       = $( "#atd-font-tpl" ).val();
															var tpl            = font_tpl.replace( /{index}/g, new_rule_index );
															$( '.font_style_table tbody' ).prepend( tpl );
															$( '#file_data_' + new_rule_index ).find( "input[type=hidden]" ).val( attachment.id );
															$( '#file_data_' + new_rule_index ).parent().find( ".media-name" ).html( attachment.filename );
													}
												);
										}
									)
									.open();
					}
				);

				$( document ).on(
					"click",
					".o-remove-font-file",
					function (e) {
							e.preventDefault();
							$( this ).parent().find( "input[type=hidden]" ).val( "" );
							$( this ).parent().parent().find( ".media-name" ).html( "" );
							$( this ).parent().parent().remove();
					}
				);

				$( document ).on(
					"click",
					"#atd-colors-palette-box .button.mg-top.add-rf-row",
					function () {
							setTimeout(
								function () {
										load_colorpicker();
								},
								100
							);
					}
				);

				$( document ).on(
					"keyup",
					".color_field",
					function (e) {
							var color = $( this ).val();
							$( this ).css( "background-color", color );
					}
				);

				window.load_colorpicker = function ()
				{
					$( '.atd-color' ).each(
						function (index, element)
							{
								var e             = $( this );
								var initial_color = e.val();
								e.css( "background-color", initial_color );
								$( this ).ColorPicker(
									{
										color: initial_color,
										onShow: function (colpkr) {
											$( colpkr ).fadeIn( 500 );
											return false;
										},
										onChange: function (hsb, hex, rgb) {
											e.css( "background-color", "#" + hex );
											e.val( "#" + hex );
										}
										}
								);
						}
					);
				}
				load_colorpicker();
			function load_tabbed_panels(container)
				{
				$( container + " .TabbedPanels" ).each(
					function ()
							{
							var cookie_id  = 'tabbedpanels_' + $( this ).attr( "id" );
							var defaultTab = ($.cookie( cookie_id ) ? parseInt( $.cookie( cookie_id ) ) : 0);
							new Spry.Widget.TabbedPanels( $( this ).attr( "id" ), {defaultTab: defaultTab - 1} );
					}
				);
			}

				load_tabbed_panels( "body" );

				$( '.TabbedPanelsTab' ).click(
					function (event) {
							var cookie_id = 'tabbedpanels_' + $( this ).parent().parent( '.TabbedPanels' ).attr( 'id' );
							$.cookie( cookie_id, parseInt( $( this ).attr( 'tabindex' ) ) );
					}
				);

				// Cliparts add image.
				$( document ).on(
					"click",
					"#atd-add-clipart",
					function (e) {
							e.preventDefault();
							var selector = $( this ).attr( 'data-selector' );
							var trigger  = $( this );
							var uploader = wp.media(
								{
									title: 'Please set the picture',
									button: {
										text: "Set Image"
									},
									multiple: true
									}
							)
									.on(
										'select',
										function () {
												var selection = uploader.state().get( 'selection' );
												selection.map(
													function (attachment) {
															attachment = attachment.toJSON();
															var code   = "<input type='hidden' value='" + attachment.id + "' name='selected-cliparts[]'>";
															code       = code + "<span class='atd-clipart-holder'><img src='" + attachment.url + "'>";
															code       = code + "<label>Price: <input type='text' value='0' name='atd-cliparts-prices[]'></label>";
															code       = code + "<a href='#' class='button atd-remove-clipart' data-id='" + attachment.id + "'>Remove</a></span>";
															$( "#cliparts-container" ).prepend( code );
													}
												);
										}
									)
									.open();
					}
				);

				$( document ).on(
					"click",
					".atd-remove-clipart",
					function (e) {
							e.preventDefault();
							var id = $( this ).data( "id" );
							$( '#cliparts-form > input[value="' + id + '"]' ).remove();
							$( this ).parent().remove();
					}
				);

				$( document ).on(
					"click",
					".atd-add-cliparts",
					function (e) {
							e.preventDefault();
							var uploader = wp.media(
								{
									title: 'Please set the picture',
									button: {
										text: "Select picture(s)"
									},
									multiple: true
									}
							)
									.on(
										'select',
										function () {
												var selection = uploader.state().get( 'selection' );
												selection.map(
													function (attachment) {
															attachment           = attachment.toJSON();
															var url_without_root = attachment.url.replace( home_url, "" );
															setTimeout(
																function ()
																	{
																		$( '.add-rf-row' ).click();
																		var trigger = $( '#cliparts-container table .o-rf-row' ).last().find( '.o-add-media' );
																		trigger.parent().find( "input[type=hidden]" ).val( url_without_root );
																		trigger.parent().find( ".media-preview" ).html( "<img src='" + attachment.url + "'>" );
																		trigger.parent().find( ".media-name" ).html( attachment.filename );
																	if (trigger.parent().hasClass( "trigger-change" )) {
																		trigger.parent().find( "input[type=hidden]" ).trigger( "propertychange" );
																	}
																},
																200
															);

													}
												);
										}
									)
									.open();
					}
				);

				/*
				 * font type
				 */
				$( '#atd_fonts_selector, #atd_cliparts_selector' ).select2(
					{
						tags: true,
						width: 500
						}
				);

				$( "ul.select2-selection__rendered" ).sortable(
					{
						containment: 'parent'
						}
				);

				$( document ).on(
					"change",
					".atd-fonts-type",
					function () {
							get_fonts_field_based_on_type();
					}
				);

			if ($( '.atd-fonts-type' ).length) {
				get_fonts_field_based_on_type();
			}

			function get_fonts_field_based_on_type()
				{
				$( "select[name='atd-metas[global-fonts][selected-fonts][]']" ).parent().parent().hide();
				var selected_value = $( '.atd-fonts-type input:checked' ).val();

				if (selected_value.indexOf( "no" ) >= 0) {
					$( "select[name='atd-metas[global-fonts][selected-fonts][]']" ).parent().parent().show();
				}
			}

				$( document ).on(
					"change",
					".atd-cliparts-group",
					function () {
							get_cliparts_field_based_on_type();
					}
				);

			if ($( '.atd-cliparts-group' ).length) {
				get_cliparts_field_based_on_type();
			}

			function get_cliparts_field_based_on_type()
				{
				$( "select[name='atd-metas[global-cliparts][selected-cliparts][]']" ).parent().parent().hide();
				var selected_value = $( '.atd-cliparts-group input:checked' ).val();

				if (selected_value.indexOf( "no" ) >= 0) {
					$( "select[name='atd-metas[global-cliparts][selected-cliparts][]']" ).parent().parent().show();
				}
			}

				$( document ).on(
					"change",
					".atd-enable-team",
					function () {
							hide_show_team_config();
					}
				);

			if ($( '.atd-enable-team' ).length) {
				hide_show_team_config();
			}

			function hide_show_team_config()
				{
				$( "#atd-team-settings" ).find( "tr:eq(1)" ).hide();
				$( "#atd-team-settings" ).find( "tr:eq(2)" ).hide();
				var selected_value = $( '.atd-enable-team input:checked' ).val();
				if (selected_value.indexOf( "yes" ) >= 0) {
					$( "#atd-team-settings" ).find( "tr:eq(1)" ).show();
					$( "#atd-team-settings" ).find( "tr:eq(2)" ).show();
				}
			}

		}
	);
})( jQuery );
