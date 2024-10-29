<?php
/**
 * Description of class-atd-product.
 *
 * @link       orionorigin@orionorigin.com
 * @since      1.0.0
 *
 * @package    Atd
 * @subpackage Atd/admin
 */

/**
 *  Description of class-atd-product.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Atd
 * @subpackage Atd/admin
 * @author     HL
 */
class ATD_Product {

	/**
	 * The variation ID of this class.
	 *
	 * @access   public
	 * @var      string    $variation_id    The variation ID of this class.
	 */
	public $variation_id;

	/**
	 * The ID of this class.
	 *
	 * @access   public
	 * @var      string    $root_product_id The product ID.
	 */
	public $root_product_id;

	/**
	 * The product.
	 *
	 * @access   public
	 * @var      object    $product The product.
	 */
	public $product;

	/**
	 * The settings of the product.
	 *
	 * @access   public
	 * @var      array    $settings The settings of the product.
	 */
	public $settings;

	/**
	 * The settings of the variation.
	 *
	 * @access   public
	 * @var      array    $variation_settings The settings of the variation.
	 */
	public $variation_settings;

	/**
	 * The constructor of the class
	 *
	 * @param int $id The id of the product.
	 */
	public function __construct( $id ) {
		if ( $id ) {
			$this->root_product_id = $this->get_parent( $id );
			// If it's a variable product.
			if ( $id !== $this->root_product_id ) {
				$this->variation_id = $id;
				$type               = 'variable';
			} else {
				// Simple product and others.
				$this->variation_id = $this->root_product_id;
				$type               = 'simple';
			}
			$this->product = wc_get_product( $id );

			$config = get_post_meta( $this->root_product_id, 'atd-metas', true );

			if ( isset( $config[ $this->variation_id ] ) ) {
				$config_id = $config[ $this->variation_id ]['config-id'];
				if ( $config_id == -1 ) {
					$config_id = $config[ $this->root_product_id ]['config-id'];
				}
				if ( $config_id ) {
					$this->settings = get_post_meta( $config_id, 'atd-metas', true );
					$product_metas  = get_post_meta( $this->root_product_id, 'atd-metas', true );
				}
			}
		}
	}

	/**
	 * Returns a variation root product ID.
	 *
	 * @param type $variation_id Variation ID.
	 * @return int
	 */
	public function get_parent( $variation_id ) {
		$variable_product = wc_get_product( $variation_id );
		if ( ! $variable_product ) {
			return false;
		}
		if ( 'variation' !== $variable_product->get_type() ) {
			$product_id = $variation_id;
		} else {
			$product_id = $variable_product->get_parent_id();
		}

		return $product_id;
	}

	/**
	 * Hide cart button.
	 *
	 * @global type $product The product.
	 * @global type $atd_settings The settings.
	 */
	public function hide_cart_button() {
		global $product;
		global $atd_settings;
		$general_options     = $atd_settings['atd-general-options'];
		$hide_cart_button    = atd_get_proper_value( $general_options, 'atd-hide-cart-button', true );
		$custom_products     = atd_get_custom_products();
		$anonymous_function  = function ( $o ) {
			return $o->id;
		};
		$custom_products_ids = array_map( $anonymous_function, $custom_products );
		$pid                 = $product->get_id();
		if ( in_array( $pid, $custom_products_ids ) && 1 == $hide_cart_button ) {
			?>
			<script type="text/javascript">
				var hide_cart_button = <?php echo $hide_cart_button; ?>;
				jQuery('[value="<?php echo $pid; ?>"]').parent().find('.add_to_cart_button').hide();
				jQuery('[value="<?php echo $pid; ?>"]').parent().find('.single_add_to_cart_button').hide();
			</script>
			<?php
		}
	}

	/**
	 * Checks the product contains at least one active part
	 *
	 * @return boolean
	 */
	public function has_part() {
		$parts = atd_get_proper_value( $this->settings, 'parts' );
		return ! empty( $parts );
	}

	/**
	 * Returns the customization page URL
	 *
	 * @return String
	 */
	public function get_design_url( $design_index = false, $cart_item_key = false, $order_item_id = false, $tpl_id = false, $edit = false ) {

		global $atd_settings;

		if ( isset( $_SESSION['atd_key'] ) ) {
			$key       = $_SESSION['atd_key'];
			$attribute = get_transient( $key );
		}

		if ( $this->variation_id ) {
			$item_id = $this->variation_id;
		} else {
			$item_id = $this->root_product_id;
		}
		$options     = $atd_settings['atd-general-options'];
		$atd_page_id = $options['atd_page_id'];
		if ( function_exists( 'icl_object_id' ) ) {
			$atd_page_id = icl_object_id( $atd_page_id, 'page', false, ICL_LANGUAGE_CODE );
		}
		$atd_page_url = '';
		if ( $atd_page_id ) {
			$atd_page_url = get_permalink( $atd_page_id );
			if ( $item_id ) {
				$query = parse_url( $atd_page_url, PHP_URL_QUERY );
				// Returns a string if the URL has parameters or NULL if not
				if ( get_option( 'permalink_structure' ) ) {
					if ( substr( $atd_page_url, -1 ) != '/' ) {
						$atd_page_url .= '/';
					}
					if ( $design_index || $design_index === 0 ) {
						$atd_page_url .= "saved-design/$item_id/$design_index/";
					} elseif ( $cart_item_key ) {
						$qty_key = 'qty_' . $cart_item_key . '_' . $item_id;
						$qty     = get_option( $qty_key, $this->get_purchase_properties()['min_to_purchase'] );
						if ( $edit ) {
							foreach ( WC()->cart->get_cart() as $cart_item ) {
								if ( $cart_item['product_id'] == $item_id ) {
									$qty = $cart_item['quantity'];
									break; // stop the loop if product is found
								}
							}
						}
						$atd_page_url .= "edit/$item_id/$cart_item_key/" . '?custom_qty=' . $qty;
					} elseif ( $order_item_id ) {
						$atd_page_url .= "ordered-design/$item_id/$order_item_id/";
					} else {
						$atd_page_url .= 'design/' . $item_id . '/';
						if ( $tpl_id ) {
							$atd_page_url .= "$tpl_id/";
						}
					}
				} else {
					if ( $design_index !== false ) {
						$atd_page_url .= '&product_id=' . $item_id . '&design_index=' . $design_index;
					} elseif ( $cart_item_key ) {
						$atd_page_url .= '&product_id=' . $item_id . '&edit=' . $cart_item_key;
					} elseif ( $order_item_id ) {
						$atd_page_url .= '&product_id=' . $item_id . '&oid=' . $order_item_id;
					} else {
						$atd_page_url .= '&product_id=' . $item_id;
						if ( $tpl_id ) {
							$atd_page_url .= "&tpl=$tpl_id";
						}
					}
				}

				if ( isset( $attribute ) && isset( $attribute['data'] ) ) {
					$attr_name = atd_get_variation_attr_name( $item_id );
					if ( $attr_name ) {
						$spliter_url = explode( '?', $atd_page_url );
						$compt       = 0;
						foreach ( $attribute['data'] as $key => $value ) {
							if ( 1 <= count( $spliter_url ) ) {

								if ( 0 === $compt ) {
									$split = '?';
								} else {
									$split = '&';
								}

								if ( 'attribute_' . $attr_name['color'] === $key ) {
									$atd_page_url .= $split . 'color=' . $value;
								} elseif ( 'attribute_' . $attr_name['size'] === $key ) {
									$atd_page_url .= $split . 'size=' . $value;
								}
							} else {
								if ( 'attribute_' . $attr_name['color'] === $key ) {
									$atd_page_url .= '&color=' . $value;
								} elseif ( 'attribute_' . $attr_name['size'] === $key ) {
									$atd_page_url .= '&size=' . $value;
								}
							}

							$compt += 1;
						}
					}
				}
			}
		}

		return $atd_page_url;
	}

	/**
	 * Get buttons diplay in front.
	 *
	 * @param type $with_upload with upload.
	 * @return type
	 */
	public function get_buttons( $with_upload = false ) {
		ob_start();
		$content = '';
		$product = $this->product;

		if ( $this->variation_id ) {
			$item_id = $this->variation_id;
		} else {
			$item_id = $this->root_product_id;
		}
		if ( 'variable' === $product->get_type() ) {
			$variations = $product->get_available_variations();
			foreach ( $variations as $variation ) {
				if ( ! $variation['is_purchasable'] || ! $variation['is_in_stock'] ) {
					continue;
				}
				$atd_product = new ATD_Product( $variation['variation_id'] );
				echo $atd_product->get_buttons( $with_upload );
			}
		} else {
			$config       = get_post_meta( $this->root_product_id, 'atd-metas', true );
			$product_type = $product->get_type();

			if ( 'variation' === $product->get_type() ) {
				$product_type            = 'variable';
				$attr_name               = atd_get_variation_attr_name( $this->root_product_id );
				$related_product_details = atd_get_related_custom_product_details( $this->root_product_id );
			}
			if ( isset( $config[ $this->variation_id ]['config-id'] ) && ! empty( $config[ $this->variation_id ]['config-id'] ) ) {
				$has_parts = $this->has_part();
				if ( ! $has_parts ) {
					$output = ob_get_clean();
					return $output;
				}
				?>
				<div class="atd-buttons-wrap-<?php echo esc_html( $product->get_type() ); ?>" data-id="<?php echo esc_html( $this->variation_id ); ?>">
					<?php
					// Start designing.
					$start_designing_url = $this->get_design_url();
					if ( ! isset( $_COOKIE['atd-current-product-id'] ) || $_COOKIE['atd-current-product-id'] != $item_id ) {
						if ( isset( $attr_name ) ) {
							$content .= '<script>
                                            var $atd_attr_name= ' . json_encode( $attr_name ) . ',
                                                $atd_allowed= ' . json_encode( $related_product_details ) . ';
                                    </script>';
						}
						if ( isset( $_COOKIE['atd-add-multiple-product'] ) && $_COOKIE['atd-add-multiple-product'] ) {
							$content .= '<button class="mg-top-10 atd-add-multiple-product" data-id="' . $item_id . '">' . __( 'Add this product', 'allada-tshirt-designer-for-woocommerce' ) . '</button>';
						} elseif ( isset( $_COOKIE['atd-change-product'] ) && $_COOKIE['atd-change-product'] ) {
							$content .= '<a  href="' . $start_designing_url . '" class="mg-top-10 source">' . __( 'Change with this product', 'allada-tshirt-designer-for-woocommerce' ) . '</a>';
						} else {
							$options     = get_option( 'atd-general-options' );
							$atd_page_id = $options['atd_page_id'];
							if ( ! empty( $atd_page_id ) && isset( $atd_page_id ) ) {
								$content .= '<a  href="' . $start_designing_url . '" class="mg-top-10 atd-customize-product button alt">' . __( 'Design now', 'allada-tshirt-designer-for-woocommerce' ) . '</a>';
							}
						}
					}

					if ( ! isset( $item_id ) ) {
						$item_id = '';
					}
					if ( ! isset( $start_designing_url ) ) {
						$start_designing_url = '';
					}
					echo $content;
					?>
				</div>
				<?php
			}
		}
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Is customizable.
	 *
	 * @return type The settings of the product.
	 */
	public function is_customizable() {
		return ( ! empty( $this->settings ) );
	}

	/**
	 * Get custom products body class.
	 *
	 * @global type $atd_settings The ATD settings.
	 * @param type $classes The classes.
	 * @param type $class The class.
	 * @return type The classes.
	 */
	public function get_custom_products_body_class( $classes, $class ) {
		if ( is_singular( array( 'product' ) ) ) {
			global $atd_settings;
			$general_options  = $atd_settings['atd-general-options'];
			$hide_cart_button = atd_get_proper_value( $general_options, 'atd-hide-cart-button', true );

			$custom_products     = atd_get_custom_products();
			$anonymous_function  = function ( $o ) {
				return $o->id;
			};
			$custom_products_ids = array_map( $anonymous_function, $custom_products );
			$pid                 = get_the_ID();
			$product             = new ATD_Product( $pid );
			if ( in_array( $pid, $custom_products_ids ) ) {
				array_push( $classes, 'atd-is-customizable' );
				if ( $hide_cart_button ) {
					array_push( $classes, 'atd-hide-cart-button' );
				}
			}
		}
		return $classes;
	}

	/**
	 * Duplicate product metas.
	 *
	 * @param type $new_product The new product.
	 * @param type $old_product The old product.
	 */
	public function duplicate_product_metas( $new_product, $old_product ) {
		$meta_key  = 'atd-metas';
		$old_metas = get_post_meta( $old_product->get_id(), $meta_key, true );
		$new_metas = atd_replace_key_in_array( $old_metas, $old_product->get_id(), $new_product->get_id() );
		update_post_meta( $new_product->get_id(), $meta_key, $new_metas );
	}

	/**
	 * Get the output image width.
	 *
	 * @return array $output_w The output width.
	 */
	public function get_output_image_width() {
		$canvas_w_config = array();
		foreach ( ATD_CANVAS as $name => $canvas ) {
			$canvas_w_config[ $name ] = array(
				'canvas_width' => $canvas['canvas-width'],
			);
		}
		$output_settings = atd_get_proper_value( $this->settings, 'output-settings', array() );
		$output_w        = atd_get_proper_value( $output_settings, 'atd-min-output-width', $canvas_w_config );
		return $output_w;
	}

	/**
	 * Returns the minimum and maximum order quantities
	 *
	 * @return type
	 */
	public function get_purchase_properties() {
		if ( $this->variation_id ) {
			$defined_min_qty = get_post_meta( $this->variation_id, 'variation_minimum_allowed_quantity', true );
			// We consider the values defined for the all of them
			if ( ! $defined_min_qty ) {
				$defined_min_qty = get_post_meta( $this->root_product_id, 'minimum_allowed_quantity', true );
			}
			$product_metas = get_post_meta( $this->root_product_id, 'atd-metas', true );

			if ( ! $defined_min_qty && isset( $product_metas['related-products'] ) && ! empty( $product_metas['related-products'] ) ) {
				$defined_min_qty = 0;
			} elseif ( ! isset( $product_metas['related-products'] ) || empty( $product_metas['related-products'] ) ) {
				$defined_min_qty = 1;
			}

			$defined_max_qty = get_post_meta( $this->variation_id, 'variation_maximum_allowed_quantity', true );
			// We consider the values defined for the all of them
			if ( ! $defined_max_qty ) {
				$defined_max_qty = get_post_meta( $this->root_product_id, 'maximum_allowed_quantity', true );
			}
		} else {
			$defined_min_qty = get_post_meta( $this->root_product_id, 'minimum_allowed_quantity', true );
			if ( ! $defined_min_qty ) {
				$defined_min_qty = 1;
			}

			$defined_max_qty = get_post_meta( $this->root_product_id, 'variation_maximum_allowed_quantity', true );
		}

		if ( ! $defined_max_qty ) {
			$defined_max_qty = apply_filters( 'woocommerce_quantity_input_max', $this->product->backorders_allowed() ? '' : $this->product->get_stock_quantity(), $this->product );
		}

		$min_to_purchase = $defined_min_qty;
		if ( ! $defined_min_qty ) {
			$min_to_purchase = 1;
		}

		$defaults = array(
			'max_value' => $defined_max_qty,
			'min_value' => $defined_min_qty,
			'step'      => '1',
		);
		$args     = apply_filters( 'woocommerce_quantity_input_args', wp_parse_args( array(), $defaults ), $this->product );

		return array(
			'min'             => $args['min_value'],
			'min_to_purchase' => $args['min_value'],
			'max'             => $args['max_value'],
			'step'            => $args['step'],
		);
	}

	/**
	 * Returns the defined value for a product setting which can be local(product metas) or global (options).
	 *
	 * @param array  $product_settings Product options.
	 * @param array  $global_settings Global options.
	 * @param string $option_name Option name / Meta key.
	 * @param int    $field_value Default value to return if empty.
	 * @return string
	 */
	public function get_option( $product_settings, $global_settings, $option_name, $field_value = '' ) {
		if ( isset( $product_settings[ $option_name ] ) && ( ( ! empty( $product_settings[ $option_name ] ) ) || '0' === $product_settings[ $option_name ] ) ) {
			$field_value = $product_settings[ $option_name ];
		} elseif ( isset( $global_settings[ $option_name ] ) && ! empty( $global_settings[ $option_name ] ) ) {
			$field_value = $global_settings[ $option_name ];
		}

		return $field_value;
	}

}
