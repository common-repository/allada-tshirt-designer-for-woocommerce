<?php
/**
 * Atd functions file.
 *
 * @link       orionorigin@orionorigin.com
 * @since      1.0.0
 *
 * @package    Atd
 * @subpackage Atd/includes
 */

/**
 * Check if is on admin screen.
 */
function is_atd_admin_screen() {
	$screen = get_current_screen();
	if (
			isset( $screen->base ) &&
			(
			strpos( $screen->base, 'atd' ) !== false || strpos( $screen->post_type, 'atd' ) !== false || 'product' === $screen->post_type || 'shop_order' === $screen->post_type || 'atd-config' === $screen->post_type
			)
	) {
		return true;
	} else {
		return false;
	}
}

/**
 * Function to refactor the fonts array
 *
 * @param type $original_fonts Array of fonts.
 * @return type
 * Refactor the fonts array to fill the select box
 */
function atd_refactor_fonts( $original_fonts ) {
	$fonts = array();
	if ( is_array( $original_fonts ) && ! empty( $original_fonts ) ) {
		foreach ( $original_fonts as $font ) {
			if ( isset( $font[0] ) && ! empty( $font[0] ) ) {
				$fonts[ wp_json_encode( $font ) ] = $font[0];
			}
		}
	}
	return $fonts;
}

/**
 * Get custom products.
 *
 * @global type $wpdb The data base instance.
 * @return type
 */
function atd_get_custom_products() {
	global $wpdb;
	$search   = "(
    pm.meta_value like '%config-id\";s:1%'
    or pm.meta_value like '%config-id\";s:2%'
    or pm.meta_value like '%config-id\";s:3%'
    or pm.meta_value like '%config-id\";s:4%'
    or pm.meta_value like '%config-id\";s:5%'
    or pm.meta_value like '%config-id\";s:6%'
    or pm.meta_value like '%config-id\";s:7%'
    or pm.meta_value like '%config-id\";s:8%'
    or pm.meta_value like '%config-id\";s:9%'
    or pm.meta_value like '%config-id\";i:%'
    )";
	$products = $wpdb->get_results(
		"
                               SELECT p.id
                               FROM $wpdb->posts p
                               JOIN $wpdb->postmeta pm on pm.post_id = p.id 
                               WHERE p.post_type = 'product'
                               AND pm.meta_key = 'atd-metas'
                               AND $search
                               "
	);
	return $products;
}

/**
 * Replace key in array.
 *
 * @param type $input_array Input.
 * @param type $search Search.
 * @param type $replace Remplace.
 * @return type Output.
 */
function atd_replace_key_in_array( $input_array, $search, $replace ) {
	$output_array             = $input_array;
	$output_array[ $replace ] = $output_array[ $search ];
	unset( $output_array[ $search ] );

	return $output_array;
}

/**
 * Convert value in pixel.
 *
 * @param type $to Unit To.
 * @param type $value
 * @return type
 */
function atd_convert_unit_to_in_px( $to, $value ) {
	$return = $value;
	if ( 'pt' === $to ) {
		$return = $value * 1.333333;
	} elseif ( 'mm' === $to ) {
		$return = $value * 3.7795275591;
	} elseif ( 'inch' === $to ) {
		$return = $value * 96;
	}
	return $return;
}

/**
 * Check woocommerce version.
 *
 * @global type $woocommerce The woocommerce.
 * @param type $version The version.
 * @return boolean return.
 */
function atd_woocommerce_version_check( $version = '2.5' ) {
	if ( class_exists( 'WooCommerce' ) ) {
		global $woocommerce;
		if ( version_compare( $woocommerce->version, $version, '>=' ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Returns user ordered designs
 *
 * @global object $wpdb
 * @param type $user_id
 * @return array
 */
function atd_get_user_orders_designs( $user_id ) {
	global $wpdb;
	$designs = array();
	$args    = array(
		'numberposts' => -1,
		'meta_key'    => '_customer_user',
		'meta_value'  => $user_id,
		'post_type'   => 'shop_order',
		'post_status' => array( 'wc-processing', 'wc-completed', 'wc-on-hold' ),
	);

	$orders = get_posts( $args );
	foreach ( $orders as $order ) {
		$sql_1          = 'select distinct order_item_id FROM ' . $wpdb->prefix . "woocommerce_order_items where order_id=$order->ID";
		$order_items_id = $wpdb->get_col( $sql_1 );
		foreach ( $order_items_id as $order_item_id ) {
			$sql_2                 = 'select meta_key, meta_value FROM ' . $wpdb->prefix . "woocommerce_order_itemmeta where order_item_id=$order_item_id and meta_key in ('_product_id', '_variation_id', 'wpc_data')";
			$order_item_metas      = $wpdb->get_results( $sql_2 );
			$normalized_item_metas = array();
			foreach ( $order_item_metas as $order_item_meta ) {
				$normalized_item_metas[ $order_item_meta->meta_key ] = $order_item_meta->meta_value;
			}
			if ( ! isset( $normalized_item_metas['wpc_data'] ) ) {
				continue;
			}

			if ( $normalized_item_metas['_variation_id'] ) {
				$product_id = $normalized_item_metas['_variation_id'];
			} else {
				$product_id = $normalized_item_metas['_product_id'];
			}
			array_push( $designs, array( $product_id, $order->post_date, unserialize( $normalized_item_metas['wpc_data'] ), $order_item_id ) );
		}
	}
	return $designs;
}

/**
 * Register fonts.
 */
function atd_register_fonts() {
	$fonts = get_option( 'atd-fonts' );
	if ( empty( $fonts ) ) {
		$fonts = atd_get_default_fonts();
	}

	foreach ( $fonts as $font ) {
		$font_label = $font[0];
		$font_url   = str_replace( 'http://', '//', $font[1] );
		if ( $font_url ) {
			$handler = sanitize_title( $font_label ) . '-css';
			wp_register_style( $handler, $font_url, array(), ATD_VERSION, 'all' );
			wp_enqueue_style( $handler );
		} elseif ( ! empty( $font[2] ) && is_array( $font[2] ) ) {
			atd_get_ttf_font_style( $font );
		}
	}
}

function atd_get_ttf_font_style( $font ) {
    $font_label	 = $font[ 0 ];
    $font_ttf_files	 = $font[ 2 ];
    foreach ( $font_ttf_files as $font_file ) {
	$font_styles	 = $font_file[ 'styles' ];
	$font_file_url	 = wp_get_attachment_url( $font_file[ 'file_id' ] );
	if ( ! $font_file_url ) {
	    continue;
	}
	foreach ( $font_styles as $font_style ) {
	    if ( $font_style == '' ) {
		$font_style_css = '';
	    } elseif ( $font_style == 'I' ) {
		$font_style_css = 'font-style:italic;';
	    } elseif ( $font_style == 'B' ) {
		$font_style_css = 'font-weight:bold;';
	    }
	    ?>
	    <style>
	        @font-face {
                    font-family: "<?php echo esc_html($font_label); ?>";
                    src: url('<?php echo esc_html($font_file_url); ?>') format('truetype');
		    <?php echo esc_html($font_style_css); ?>
	        }
	    </style>
	    <?php
	}
    }
}

/**
 * Get the default fonts.
 *
 * @return array The fonts.
 */
function atd_get_default_fonts() {
	$default = array(
		array( 'Shadows Into Light', 'http://fonts.googleapis.com/css?family=Shadows+Into+Light' ),
		array( 'Droid Sans', 'http://fonts.googleapis.com/css?family=Droid+Sans:400,700' ),
		array( 'Abril Fatface', 'http://fonts.googleapis.com/css?family=Abril+Fatface' ),
		array( 'Arvo', 'http://fonts.googleapis.com/css?family=Arvo:400,700,400italic,700italic' ),
		array( 'Lato', 'http://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic' ),
		array( 'Just Another Hand', 'http://fonts.googleapis.com/css?family=Just+Another+Hand' ),
	);

	return $default;
}

/**
 * Get price format.
 *
 * @return string
 */
function atd_get_price_format() {
	$currency_pos = get_option( 'woocommerce_currency_pos' );
	$format       = '%s%v';

	switch ( $currency_pos ) {
		case 'left':
			$format = '%s%v';
			break;
		case 'right':
			$format = '%v%s';
			break;
		case 'left_space':
			$format = '%s %v';
			break;
		case 'right_space':
			$format = '%v %s';
			break;
		default:
			$format = '%s%v';
			break;
	}
	return $format;
}

/**
 * Get the parts config.
 *
 * @param array $parts The parts.
 * @param array $output_w The output width.
 * @return array $parts_config The parts config.
 */
function atd_get_parts_config( $parts, $output_w ) {
	$parts_config = array();
	foreach ( $parts as $name => $part ) {
		if ( 'yes' === $part['enable'] ) {
			$parts_config[ $name ] = array(
				'name'           => $part['name'],
				't_shirt_width'  => atd_convert_unit_to_in_px( ATD_CANVAS_UNIT, ATD_CANVAS[ $name ]['t-shirt-width'] ),
				't_shirt_height' => atd_convert_unit_to_in_px( ATD_CANVAS_UNIT, ATD_CANVAS[ $name ]['t-shirt-height'] ),
				'canvas_width'   => atd_convert_unit_to_in_px( ATD_CANVAS_UNIT, ATD_CANVAS[ $name ]['canvas-width'] ),
				'canvas_height'  => atd_convert_unit_to_in_px( ATD_CANVAS_UNIT, ATD_CANVAS[ $name ]['canvas-height'] ),
				'canvas_top'     => atd_convert_unit_to_in_px( ATD_CANVAS_UNIT, ATD_CANVAS[ $name ]['canvas-top'] ),
				'canvas_left'    => atd_convert_unit_to_in_px( ATD_CANVAS_UNIT, ATD_CANVAS[ $name ]['canvas-left'] ),
				'border_color'   => $part['border-color'],
				'output_w'       => atd_convert_unit_to_in_px( ATD_CANVAS_UNIT, $output_w[ $name ]['canvas_width'] ),
			);
		}
	}
	return $parts_config;
}

/**
 * Get the palette templates.
 *
 * @param string $atd_palette The palette value.
 * @return string The palette template.
 */
function atd_get_palette_template( $atd_palette ) {
	$palette_tpl = array();
	if ( ! empty( $atd_palette ) && 'unlimited' !== $atd_palette ) {
		$colors_palettes_group = get_post_meta( $atd_palette, 'atd-colors-palette-data', true );
		if ( ! empty( $colors_palettes_group ) ) {
			foreach ( $colors_palettes_group as $i => $color_palette_group ) {
				$code_hex      = atd_get_proper_value( $color_palette_group, 'code_hex', '' );
				$palette_tpl[] = $code_hex;
			}
		}
	}
	return $palette_tpl;
}

/**
 * Initialize canvas variables.
 *
 * @global type $atd_settings the settings.
 * @global type $wp_query the WordPress query.
 * @global type $wpdb the data base instance.
 * @param type $atd_metas the meta.
 * @param type $product the product.
 * @param type $editor the editor.
 */
function atd_init_canvas_vars( $atd_metas, $product, $editor ) {
	// var_dump($atd_metas);
	global $atd_settings, $wp_query, $wpdb;
	$use_retina_mode    = 'yes';
	$atd_query_vars     = array();
	$general_options    = $atd_settings['atd-general-options'];
	$colors_options     = $atd_settings['atd-colors-options'];
	$atd_upload_options = $atd_settings['atd-upload-options'];

	$valid_formats = $atd_upload_options['atd-upl-extensions'];
	$output_w      = $editor->atd_product->get_output_image_width();
	$parts         = $editor->atd_product->get_option( $atd_metas, $general_options, 'parts', array() );
	$parts_config  = atd_get_parts_config( $parts, $output_w );

	/*
	 $generate_svg = false;
	  $output_format = atd_get_proper_value($atd_metas['output-settings'], 'output-format');
	  if ($output_format == 'svg') {
	  $generate_svg = 'svg';
	  $output_format = 'png';
	  } */

	$generate_svg      = false;
	$output_format     = 'png';
	$output_loop_delay = 1000;

	$svg_colorization = atd_get_proper_value( $colors_options, 'atd-svg-colorization', '1' );
	$disable_shortcut = atd_get_proper_value( $general_options, 'disable-keyboard-shortcuts', 0 );
	$atd_palette      = atd_get_proper_value( $colors_options, 'atd-color-palette', 'unlimited' );
	$palette_tpl      = atd_get_palette_template( $atd_palette );

	$atd_team_settings = atd_get_proper_value( $atd_metas, 'team-settings' );

	$atd_team_name_settings            = atd_get_proper_value( $atd_team_settings, 'name' );
	$atd_team_name_price               = $atd_team_name_settings['price'];
	$atd_team_name_max_height          = $atd_team_name_settings['max-height'];
	$atd_team_name_height_unit         = $atd_team_name_settings['height-unit'];
	$atd_team_name_colors_palette_type = $atd_team_name_settings['colors-palette'];
	$atd_team_name_palette_tpl         = atd_get_palette_template( $atd_team_name_colors_palette_type );

	$atd_team_number_settings            = atd_get_proper_value( $atd_team_settings, 'number' );
	$atd_team_number_price               = $atd_team_number_settings['price'];
	$atd_team_number_max_height          = $atd_team_number_settings['max-height'];
	$atd_team_number_height_unit         = $atd_team_number_settings['height-unit'];
	$atd_team_number_colors_palette_type = $atd_team_number_settings['colors-palette'];
	$atd_team_number_palette_tpl         = atd_get_palette_template( $atd_team_number_colors_palette_type );

	if ( isset( $wp_query->query_vars['edit'] ) ) {
		$cart_item_key          = $wp_query->query_vars['edit'];
		$atd_query_vars['edit'] = $cart_item_key;
		global $woocommerce;
		$cart = $woocommerce->cart->get_cart();
		if ( isset( $cart[ $cart_item_key ]['atd_generated_data'] ) ) {
			$data = $cart[ $cart_item_key ]['atd_generated_data'];
			// Useful when editing cart item
			if ( $data ) {
				$data = stripslashes_deep( $data );
			}
		}
	} elseif ( isset( $wp_query->query_vars['design_index'] ) ) {
		global $current_user;
		$design_index                   = $wp_query->query_vars['design_index'];
		$atd_query_vars['design_index'] = $design_index;
		$user_designs                   = get_user_meta( $current_user->ID, 'atd_saved_designs' );
		if ( isset( $user_designs[ $design_index ][3] ) && ! empty( $user_designs[ $design_index ][3] ) ) {
			$data = $user_designs[ $design_index ][3];
		}
	} elseif ( isset( $wp_query->query_vars['oid'] ) ) {
		$order_item_id         = $wp_query->query_vars['oid'];
		$atd_query_vars['oid'] = $order_item_id;
		$sql                   = 'select meta_value FROM ' . $wpdb->prefix . "woocommerce_order_itemmeta where order_item_id=$order_item_id and meta_key='atd_data'";
		// echo $sql;
		$atd_data = $wpdb->get_var( $sql );
		$data     = unserialize( $atd_data );
	}

	// Previous data to load overwrites everything
	if ( isset( $_SESSION['atd-data-to-load'] ) && ! empty( $_SESSION['atd-data-to-load'] ) ) {
		$previous_design_str = stripslashes_deep(filter_input(INPUT_SESSION, 'atd-data-to-load') );
		$previous_design     = json_decode( $previous_design_str );
		if ( is_object( $previous_design ) ) {
			$previous_design = (array) $previous_design;
		}
		// We make sure the structure of the data matches the one loaded by the plugin
		foreach ( $previous_design as $part_key => $part_data ) {
			$previous_design[ $part_key ] = array( 'json' => $part_data );
		}
		$data = $previous_design;
		unset( $_SESSION['atd-data-to-load'] );
	}

	if ( isset( $data ) && ! empty( $data ) ) {
		?>
		<script>
			var to_load =<?php echo json_encode( $data ); ?>;
		</script>
		<?php
	}

	$available_variations = array();
	if ( 'variable' === $product->get_type() ) {
		$available_variations = $product->get_available_variations();
	}

	$price_format  = atd_get_price_format();
	$editor_params = array(
		'is_beforeunload'                     => false,
		'canvas'                              => $parts_config,
		'generate_svg'                        => $generate_svg,
		'output_format'                       => $output_format,
		'output_loop_delay'                   => $output_loop_delay,
		'svg_colorization'                    => $svg_colorization,
		'palette_type'                        => $atd_palette,
		'global_variation_id'                 => $editor->item_id,
		'disable_shortcuts'                   => $disable_shortcut,
		'palette_tpl'                         => $palette_tpl,
		'atd_team_name_colors_palette_type'   => $atd_team_name_colors_palette_type,
		'atd_team_name_palette_tpl'           => $atd_team_name_palette_tpl,
		'atd_team_number_colors_palette_type' => $atd_team_number_colors_palette_type,
		'atd_team_number_palette_tpl'         => $atd_team_number_palette_tpl,
		'translated_strings'                  => array(
			'deletion_error_msg'        => __( 'The deletion of this object is not allowed', 'atd' ),
			'loading_msg'               => __( '<div id="loader-wrapper"><div id="loader"></div><div class="loader-section section-left"></div><div class="loader-section section-right"></div></div>', 'atd' ),
			'empty_object_msg'          => __( 'The edition area is empty.', 'atd' ),
			'delete_all_msg'            => __( 'Do you really want to delete all items in the design area ?', 'atd' ),
			'delete_msg'                => __( 'Do you really want to delete the selected items ?', 'atd' ),
			'empty_txt_area_msg'        => __( 'Please enter the text to add.', 'atd' ),
			'cart_item_edition_switch'  => __( "You're editing a cart item. If you switch to another product and update the cart, the previous item will be removed from the cart. Do you really want to continue?", 'atd' ),
			'svg_background_tooltip'    => __( 'Background color (SVG files only)', 'atd' ),
			'cliparts_search_no_result' => __( 'There are no results that match your search.', 'atd' ),
		),
		'query_vars'                          => $atd_query_vars,
		'thousand_sep'                        => wc_get_price_thousand_separator(),
		'decimal_sep'                         => wc_get_price_decimal_separator(),
		'nb_decimals'                         => wc_get_price_decimals(),
		'currency'                            => get_woocommerce_currency_symbol(),
		'price_format'                        => $price_format,
		'variations'                          => $available_variations,
		'product_id'                          => $editor->item_id,
		'lazy_placeholder'                    => ATD_URL . '/public/images/rolling.gif',
		'enable_retina'                       => $use_retina_mode,
		'valid_formats'                       => $valid_formats,
	);
	?>
	<script>
		var atd =<?php echo wp_json_encode( $editor_params ); ?>;
	</script>
	<?php
}

/**
 * Get template price.
 *
 * @param type $tpl_id The template id.
 * @return int The base price.
 */
function atd_get_template_price( $tpl_id ) {
	if ( empty( $tpl_id ) ) {
		return 0;
	}

	$tpl_base_price = get_post_meta( $tpl_id, 'base-price', true );
	if ( empty( $tpl_base_price ) ) {
		$tpl_base_price = 0;
	}

	return $tpl_base_price;
}

function atd_get_configs() {
	global $wpdb;
	$configs = $wpdb->get_results(
		"
                    SELECT ID, post_title, meta_value
                    FROM $wpdb->posts, $wpdb->postmeta
                    WHERE ID=post_id
                    AND meta_key='atd-metas'
                    AND post_type='atd-config'
                    AND post_status='publish' "
	);
	foreach ( $configs as $key => $value ) {
		$config_id              = $value->ID;
		$unserialise_meta_value = maybe_unserialize( $value->meta_value );
		foreach ( $unserialise_meta_value as $key => $value ) {
			if ( $key == 'parts' ) {
				$config_json[ $config_id ] = $value;
			}
		}
	}
	?>

	<script>
		var atd_configs =<?php echo wp_json_encode( $config_json ); ?>;
	</script>
	<?php
	return $configs;
}
/**
 * Retrive the custom product id
 *
 * @global $wpdb;
 */
function atd_get_related_custom_product() {
	 global $wpdb;

	$related_custom_product_availlable = $wpdb->get_results(
		"SELECT DISTINCT ID,post_title FROM $wpdb->posts,
    $wpdb->postmeta where meta_key='atd-metas' AND post_status='publish' AND post_type='product'"
	);
	$instock                           = array();
	$result                            = array( 'data', 'instock' );
	$result['data']                    = $related_custom_product_availlable;
	return $result;
}

/**
 * Get attribute slug.
 *
 * @param int           $product_id Product id.
 * @param string        $attr_name Attribute name.
 * @param string string $attr_val Reseach value.
 * @return array $search Result.
 */
function atd_get_attributes_slug( $product_id, $attr_name, $attr_val = '' ) {
	$product_terms = wc_get_product_terms( $product_id, $attr_name );
	$slug          = array();
	$search        = false;

	if ( ! empty( $product_terms ) ) {
		foreach ( $product_terms as $terms ) {
			array_push( $slug, $terms->slug );

			if ( $attr_val === $terms->name || $attr_val === $terms->slug ) {
				$search = $terms->slug;
			}
		}
	} else {
		$product          = wc_get_product( $product_id );
		$all_attr_options = $product->get_attribute( $attr_name );
		$slug             = explode( ' | ', $all_attr_options );

		foreach ( $slug as $key => $value ) {
			if ( $attr_val === $value ) {
				$search = $value;
			}
		}
	}
	if ( empty( $attr_val ) ) {
		return $slug;
	} else {
		return $search;
	}
}

/**
 * Get custom product details.
 *
 * @param int $product_id The product id.
 * @return array the product details
 */
function atd_get_related_custom_product_details( $product_id ) {
	$meta          = get_post_meta( $product_id, 'atd-metas', false );
	$selected_attr = atd_get_variation_attr_name( $product_id );
	if ( isset( $selected_attr['color'] ) && $selected_attr['size'] ) {
		$color                       = $selected_attr['color'];
		$size                        = $selected_attr['size'];
		$product                     = wc_get_product( $product_id );
		$result['short_description'] = $product->get_short_description();
		$result['description']       = $product->get_description();
		if ( ! $product ) {
			return false;
		}

		$config = new ATD_Config();

		if ( 'variable' === $product->get_type() ) {
			$availlable_variations = $product->get_available_variations();

			$color_data = atd_get_attributes_slug( $product_id, $color, false );
			$size_data  = atd_get_attributes_slug( $product_id, $size, false );

			$variation_id          = array();
			$variation_combinaison = array();
			$any_tmp               = array();
			$variation_color       = array();
			$variation_size        = array();

			foreach ( $availlable_variations as $key => $value ) {
				$product_variation    = wc_get_product( $value['variation_id'] );
				$variation_attributes = $product_variation->get_attributes();

				if ( isset( $meta[0][ $value['variation_id'] ]['config-id'] ) && '' !== $meta[0][ $value['variation_id'] ]['config-id'] ) {
					$attr_color = $value['attributes'][ 'attribute_' . $color ];
					$attr_size  = $value['attributes'][ 'attribute_' . $size ];

					if ( '' === $attr_color && '' !== $attr_size ) {
						foreach ( $color_data as $index => $any_color ) {
							if ( '' !== $any_color ) {
								array_push( $variation_color, $any_color );
								array_push( $variation_size, $attr_size );
								array_push( $variation_id, $value['variation_id'] );
								$label = $any_color . ' / ' . $attr_size;
								array_push( $variation_combinaison, $label );
							}
						}
					} elseif ( '' === $attr_size && '' !== $attr_color ) {

						foreach ( $size_data as $index => $any_size ) {
							if ( '' !== $any_size ) {
								array_push( $variation_size, $any_size );
								array_push( $variation_color, $attr_color );
								array_push( $variation_id, $value['variation_id'] );
								$label = $attr_color . ' / ' . $any_size;
								array_push( $variation_combinaison, $label );
							}
						}
					} elseif ( '' !== $attr_size && '' !== $attr_color ) {
							array_push( $variation_color, $attr_color );
							array_push( $variation_size, $attr_size );
							array_push( $variation_id, $value['variation_id'] );

						if ( $variation_attributes && is_array( $variation_attributes ) ) {
							$label = $config->create_variation_attributes_label( $variation_attributes );
							array_push( $variation_combinaison, $label );
						}
					} else {

						foreach ( $color_data as $index => $any_color ) {
							foreach ( $size_data as $index2 => $any_size ) {
								if ( '' !== $any_size && '' !== $any_color ) {
									array_push( $variation_size, $any_size );
									array_push( $variation_color, $any_color );
									array_push( $variation_id, $value['variation_id'] );
									$label = $any_color . ' / ' . $any_size;
									array_push( $variation_combinaison, $label );
								}
							}
						}
					}
				}
			}

			$result['variation_sizes']       = $variation_size;
			$result['variation_color']       = $variation_color;
			$result['variation_id']          = $variation_id;
			$result['variation_combinaison'] = $variation_combinaison;
			return $result;
		} else {
			return false;
		}
	}

	return false;
}

/**
 * Récupère le nom des l'attributs size et color
 *
 * @param int $product_id The product id.
 */
function atd_get_variation_attr_name( $product_id ) {
	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		return false;
	}

	if ( 'variation' === $product->get_type() ) {
		$product_id = $product->get_parent_id();
	}

	$meta = get_post_meta( $product_id, 'atd-metas', false );
	if ( isset( $meta[0]['attr-color'] ) && isset( $meta[0]['attr-size'] ) &&
	'your_attr_color' !== $meta[0]['attr-color'] && 'your_attr_size' !== $meta[0]['attr-size'] ) {
		$result = array(
			'color' => $meta[0]['attr-color'],
			'size'  => $meta[0]['attr-size'],
		);
		return $result;
	} else {
		return false;
	}
}

/**
 * Récupère l'url de l'image des parts
 *
 * @param int    $product_id The product id.
 * @param int    $variation_id The variation id.
 * @param string $image The part index.
 * @return array $part_data_arr Part image url.
 */
function atd_get_part_image( int $product_id, int $variation_id, $image ) {
	$meta_value   = get_post_meta( $product_id, 'atd-metas', true );
	$product      = wc_get_product( $product_id );
	$product_type = $product->get_type();
	if ( 'variable' !== $product_type ) {
		$variation_id = $product_id;
	}

	if ( isset( $meta_value[ $variation_id ] ) ) {
		$part = $meta_value[ $variation_id ];
	} elseif ( isset( $meta_value[ $variation_id ]['parts'] ) ) {
		$part = $meta_value[ $variation_id ]['parts'];
	}
	$part_data_arr = array();
	if ( isset( $part['front'] ) ) {
		$part_data_arr['Front'] = $part['front'][ $image ];
	} else {
		$part_data_arr['Front'] = '';
	}

	if ( isset( $part['back'] ) ) {
		$part_data_arr['Back'] = $part['back'][ $image ];
	} else {
		$part_data_arr['Back'] = '';
	}

	if ( isset( $part['left'] ) ) {
		$part_data_arr['Left'] = $part['left'][ $image ];
	} else {
		$part_data_arr['Left'] = '';
	}

	if ( isset( $part['right'] ) ) {
		$part_data_arr['Right'] = $part['right'][ $image ];
	} else {
		$part_data_arr['Right'] = '';
	}

	if ( isset( $part['chest'] ) ) {
		$part_data_arr['Chest'] = $part['chest'][ $image ];
	} else {
		$part_data_arr['Chest'] = '';
	}

	return $part_data_arr;
}


if ( ! function_exists( 'array_key_first' ) ) {
	function array_key_first( array $arr ) {
		foreach ( $arr as $key => $unused ) {
			return $key;
		}
		return null;
	}
}

add_action( 'wp_print_styles', 'remove_spectrum_style_script', 100 );

function remove_spectrum_style_script() {
	wp_dequeue_style( 'spectrum-style' );
	wp_dequeue_style( 'spectrum-style-css' );
	wp_deregister_style( 'spectrum-style' );
	wp_deregister_style( 'spectrum-style-css' );
	wp_dequeue_script( 'spectrum-script' );
	wp_dequeue_script( 'spectrum-script-js' );
	wp_deregister_script( 'spectrum-script' );
	wp_deregister_script( 'spectrum-script-js' );
}

function atd_allowed_tags( $attributes = '' ) {

	$default_attribs = array(
		'id'             => array(),
		'class'          => array(),
		'title'          => array(),
		'style'          => array(),
		'data'           => array(),
		'data-mce-id'    => array(),
		'data-mce-style' => array(),
		'data-mce-bogus' => array(),
                'data-item' => array(),
                'data-toggle' => array(),
                'data-target' => array(),
                'tabindex' => array(),
                'role' => array(),
                'aria-labelledby' => array(),
                'aria-hidden' => array(),
                'type' => array(),
                'data-dismiss' => array(),
                'download' => array(),
	);

	if ( is_array( $attributes ) && ! empty( $attributes ) ) {
		foreach ( $attributes as $value ) {
			$default_attribs[ $value ] = array();
		}
	}
	$allowed_tags = array(
		'div'        => $default_attribs,
		'img'        => array_merge(
			$default_attribs,
			array(
				'src'    => array(),
				'alt'    => array(),
				'title'  => array(),
				'height' => array(),
			)
		),
		'span'       => $default_attribs,
		'canvas'     => $default_attribs,
		'textarea'   => $default_attribs,
		'script'     => array(
			'type' => array(),
			'src'  => array(),
		),
		'p'          => $default_attribs,
		'a'          => array_merge(
			$default_attribs,
			array(
				'href'   => array(),
				'target' => array( '_blank', '_top' ),
			)
		),
		'input'      => array_merge(
			$default_attribs,
			array(
				'type'        => array(),
				'id'          => array(),
				'step'        => array(),
				'min'         => array(),
				'max'         => array(),
				'name'        => array(),
				'value'       => array(),
				'checked'     => array(),
				'placeholder' => array(),
				'multiple'    => array(),
				'accept'      => array(),
			)
		),

		'select'     => array_merge(
			$default_attribs,
			array(
				'value'   => array(),
				'checked' => array(),
			)
		),

		'option'     => array_merge(
			$default_attribs,
			array(
				'value'   => array(),
				'checked' => array(),
			)
		),

		'optgroup'   => array_merge(
			$default_attribs,
			array(
				'value'   => array(),
				'checked' => array(),
			)
		),
            
                'textarea'      => array_merge(
			$default_attribs,
			array(
				'name'        => array(),
				'placeholder' => array(),
			)
		),

		'u'          => $default_attribs,
		'i'          => $default_attribs,
		'q'          => $default_attribs,
		'b'          => $default_attribs,
		'ul'         => $default_attribs,
		'ol'         => $default_attribs,
		'li'         => $default_attribs,
		'br'         => $default_attribs,
		'hr'         => $default_attribs,
		'strong'     => $default_attribs,
		'blockquote' => $default_attribs,
		'del'        => $default_attribs,
		'strike'     => $default_attribs,
		'em'         => $default_attribs,
		'code'       => $default_attribs,
		'button'     => $default_attribs,
		'tr'         => $default_attribs,
		'td'         => $default_attribs,
		'h4'         => $default_attribs,
                
	);
	return $allowed_tags;
}
