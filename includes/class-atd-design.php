<?php
/**
 * Cliparts code.
 *
 * @link       http://orionorigin.com
 * @since      3.0
 *
 * @package    Atd
 * @subpackage Atd/includes
 */

/**
 * Cliparts code
 *
 * This class defines all code necessary for design.
 *
 * @since      1.0.0
 * @package    Atd
 * @subpackage Atd/includes
 * @author     orionorigin <orionorigin@orionorigin.com>
 */
class ATD_Design {

	/**
	 * Save design for later.
	 *
	 * @global type $current_user The current user.
	 */
	public function save_design_for_later_ajax() {
		$final_canvas_parts = filter_input( INPUT_POST, 'final_canvas_parts', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$variation_id       = filter_input( INPUT_POST, 'variation_id' );
		$design_index       = filter_input( INPUT_POST, 'design_index' );
		$design_name        = filter_input( INPUT_POST, 'design_name' );
		$data_action        = filter_input( INPUT_POST, 'data_action' );
		$attr_color         = filter_input( INPUT_POST, 'attribute_color' );
		$attr_size          = filter_input( INPUT_POST, 'attribute_size' );

		$cart_item_key = '';
		if ( isset( $_POST['cart_item_key'] ) ) {
			$cart_item_key = filter_input( INPUT_POST, 'cart_item_key' );
		}
		$is_logged         = 0;
		$result            = 0;
		$message           = '';
		$atd_product       = new ATD_Product( $variation_id );
		$customization_url = $atd_product->get_design_url();
		$url               = wp_login_url( $customization_url );
		$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
		if ( $myaccount_page_id ) {
			$url = get_permalink( $myaccount_page_id );
		}
		if ( is_user_logged_in() ) {
			global $current_user;
			$message         = $current_user->ID;
			$is_logged       = 1;
			$today           = date( 'Y-m-d H:i:s' );
			$tmp_dir         = uniqid();
			$generation_path = ATD_SAVED_DESIGN_UPLOAD_PATH . "/$tmp_dir";
			$generation_url  = ATD_SAVED_DESIGN_UPLOAD_URL . "/$tmp_dir";
			if ( wp_mkdir_p( $generation_path ) ) {
				$generation_url = ATD_SAVED_DESIGN_UPLOAD_URL . "/$tmp_dir";

				$zip_name      = $this->get_output_zip_folder_name( $variation_id );
				$export_result = $this->export_data_to_files( $generation_path, $final_canvas_parts, $variation_id, $zip_name );
				if ( ! empty( $export_result ) && is_array( $export_result ) ) {
					$final_canvas_parts['output']['files']       = $export_result;
					$final_canvas_parts['output']['working_dir'] = $tmp_dir;
					$final_canvas_parts['output']['zip']         = $zip_name;
					$user_designs                                = get_user_meta( $current_user->ID, 'atd_saved_designs' );
					if ( $design_index !== -1 ) {
						if ( 'new-save' == $data_action ) {
							$design_name = $design_name . ' ' . $user_designs[ $design_index ][2];
						} elseif ( 'replace' == $data_action ) {
							$design_name = $user_designs[ $design_index ][2];
						}
					}
					$to_save = array( $variation_id, $today, $design_name, $final_canvas_parts );

					foreach ( $user_designs as $index => $design ) {
						foreach ( $design[3] as $key => $value ) {
							if ( isset( $value['json'] ) ) {
								$user_designs[ $index ][3][ $key ]['json'] = wp_slash( $value['json'] );
							}
						}
					}

					if ( $design_index !== -1 ) {
						if ( 'new-save' == $data_action ) {
							array_push( $user_designs, $to_save );
						} elseif ( 'replace' == $data_action ) {
							$user_designs[ $design_index ] = $to_save;
						}
					} else {
						array_push( $user_designs, $to_save );
					}

					delete_user_meta( $current_user->ID, 'atd_saved_designs' );

					foreach ( $user_designs as $index => $design ) {
						$result = add_user_meta( $current_user->ID, 'atd_saved_designs', $design );
						if ( ! $result ) {
							break;
						}
					}
					if ( $result ) {
						$result  = 1;
						$message = "<div class='atd_notification success'>" . __( 'The design has successfully been saved to your account.', 'allada-tshirt-designer-for-woocommerce' ) . '</div>';
						if ( $design_index === -1 ) {
							$design_index = count( $user_designs ) - 1;
						} else {
							if ( 'new-save' == $data_action ) {
								$design_index = count( $user_designs ) - 1;
							}
						}
						$atd_product = new ATD_Product( $variation_id );
						$url         = $atd_product->get_design_url( $design_index );
					} else {
						$result  = 0;
						$message = "<div class='atd_notification failure'>" . __( 'An error has occured. Please try again later or contact the administrator.', 'allada-tshirt-designer-for-woocommerce' ) . '</div>';
					}
				}
			}
		} else {
			if ( ! isset( $_SESSION['atd_designs_to_save'] ) ) {
				$_SESSION['atd_designs_to_save'] = array();
			}
			if ( ! isset( $_SESSION['atd_designs_to_save'][ $variation_id ] ) ) {
				$_SESSION['atd_designs_to_save'][ $variation_id ] = array();
			}

			array_push( $_SESSION['atd_designs_to_save'][ $variation_id ], $final_canvas_parts );
			$to_save = array();
			foreach ( $final_canvas_parts as $part_key => $part_data ) {
				if ( ! isset( $part_data['json'] ) ) {
					continue;
				}
				$to_save[ $part_key ] = $part_data['json'];
			}
			$_SESSION['atd-data-to-load'] = json_encode( $to_save );
		}
		echo wp_json_encode(
			array(
				'is_logged' => $is_logged,
				'success'   => $result,
				'message'   => $message,
				'url'       => $url,
			)
		);
		die();
	}

	/**
	 * Generate downloadable file.
	 */
	public function generate_downloadable_file() {
		$variation_id       = filter_input( INPUT_POST, 'variation_id' );
		$final_canvas_parts = filter_input( INPUT_POST, 'final_canvas_parts', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$tmp_dir            = uniqid();
		$generation_path    = ATD_SAVED_DESIGN_UPLOAD_PATH . "/$tmp_dir";
		$generation_url     = ATD_SAVED_DESIGN_UPLOAD_URL . "/$tmp_dir";
		$atd_product        = new ATD_Product( $variation_id );
		$output_settings    = $atd_product->settings['output-settings'];
		if ( wp_mkdir_p( $generation_path ) ) {
			$zip_name = $this->get_output_zip_folder_name( $variation_id );
			$result   = $this->export_data_to_files( $generation_path, $final_canvas_parts, $variation_id, $zip_name, true );
			if ( ! empty( $result ) && is_array( $result ) ) {
				$output_msg = '';
				if ( $output_settings['zip-output'] == 'yes' ) {
					$output_msg = '<div>' . __( 'The generation has been successfully completed. Please click ', 'allada-tshirt-designer-for-woocommerce' ) . "<a href='$generation_url/" . $zip_name . "' download='" . $zip_name . "'>" . __( 'here', 'allada-tshirt-designer-for-woocommerce' ) . '</a> ' . __( 'to download your design', 'allada-tshirt-designer-for-woocommerce' ) . '.</div>';
				} else {
					foreach ( $result as $part_key => $part_file_arr ) {
						$part_file   = $part_file_arr['file'];
						$output_msg .= '<div>' . ucfirst( $part_key ) . __( ': please click ', 'allada-tshirt-designer-for-woocommerce' ) . "<a href='$generation_url/$part_key/$part_file' download='$part_file'>" . __( 'here', 'allada-tshirt-designer-for-woocommerce' ) . '</a> ' . __( 'to download', 'allada-tshirt-designer-for-woocommerce' ) . '.</div>';
					}
				}
				echo wp_json_encode(
					array(
						'success' => 1,
						'message' => "<div class='atd-success'>" . $output_msg . '</div>',
					)
				);
			} else {
				echo wp_json_encode(
					array(
						'success' => 0,
						'message' => "<div class='atd-failure'>" . __( 'An error occured in the generation process. Please try again later.', 'allada-tshirt-designer-for-woocommerce' ) . '</div>',
					)
				);
			}
		} else {
			echo wp_json_encode(
				array(
					'success' => 0,
					'message' => "<div class='atd-failure'>" . __( "Can't create a generation directory...", 'allada-tshirt-designer-for-woocommerce' ) . '</div>',
				)
			);
		}
		die();
	}

	/**
	 * Delete saved design.
	 *
	 * @global type $current_user The current user.
	 */
	public function delete_saved_design_ajax() {
		$design_index = filter_input( INPUT_POST, 'design_index' );
		$variation_id = filter_input( INPUT_POST, 'variation_id' );
		$atd_product  = new ATD_Product( $variation_id );
		$url          = $atd_product->get_design_url();
		global $current_user;
		$user_designs = get_user_meta( $current_user->ID, 'atd_saved_designs' );
		unset( $user_designs[ $design_index ] );
		foreach ( $user_designs as $index => $design ) {
			foreach ( $design[3] as $key => $value ) {
				$user_designs[ $index ][3][ $key ]['json'] = wp_slash( $value['json'] );
			}
		}
		delete_user_meta( $current_user->ID, 'atd_saved_designs' );
		$result = true;
		foreach ( $user_designs as $index => $design ) {
			$result = add_user_meta( $current_user->ID, 'atd_saved_designs', $design );
			if ( ! $result ) {
				break;
			}
		}
		echo wp_json_encode(
			array(
				'url'             => $url,
				'success'         => $result,
				'success_message' => __( 'Design successfully deleted.', 'allada-tshirt-designer-for-woocommerce' ),
				'failure_message' => __( 'An error occured. Please try again later', 'allada-tshirt-designer-for-woocommerce' ),
			)
		);
		die();
	}

	/**
	 * Get output zip folder name.
	 *
	 * @param type $product_id The product id.
	 * @return type $zip_name The zip name.
	 */
	private function get_output_zip_folder_name( $product_id ) {
		$atd_product             = new ATD_Product( $product_id );
		$product_metas           = $atd_product->settings;
		$product_output_settings = atd_get_proper_value( $product_metas, 'output-settings' );
		$zip_name                = atd_get_proper_value( $product_output_settings, 'zip-folder-name' );
		if ( empty( $zip_name ) ) {
			$zip_name = uniqid( 'atd_' );
		}
		return $zip_name . '.zip';
	}

	/**
	 * Add custom designs to cart.
	 *
	 * @global type $woocommerce The woocommerce.
	 */
	public function add_custom_design_to_cart_ajax() {
		global $woocommerce;
		$message = '';
		if ( atd_woocommerce_version_check() ) {
			$cart_url = wc_get_cart_url();
		} else {
			$cart_url = $woocommerce->cart->get_cart_url();
		}

		$main_variation_id = filter_input( INPUT_POST, 'variation_id' );
		$cart_item_key     = filter_input( INPUT_POST, 'cart_item_key' );
		// $product_qty = filter_input(INPUT_POST, 'quantity');
		$atd_product      = new ATD_Product( $main_variation_id );
		$atd_metas        = $atd_product->settings;
		$data             = array();
		$total_price_form = 0;
		if ( class_exists( 'Ofb' ) ) {
			if ( isset( $atd_metas['form-builder'] ) ) {
				if ( $atd_metas['form-builder'] != '' ) {
					$form_fields = stripslashes_deep( filter_input( INPUT_POST, 'form_fields' ) );
					if ( isset( $form_fields ) && ! empty( $form_fields ) ) {
						if ( isset( $form_fields ) ) {
							$form_fields_decode = json_decode( $form_fields );
							foreach ( $form_fields_decode as $key => $value ) {
								$data[ $key ] = $value;
							}
						}
					}
					$total_price_form = get_form_data( $atd_metas['form-builder'], $data );
				}
			}
		}

		$atd_team_additional_price = 0;
		if ( 'yes' === $atd_metas['team-settings']['enable-team'] ) {
			if ( 'yes' === filter_input( INPUT_POST, 'atd_team_add_name' ) ) {
				$atd_team_additional_price += $atd_metas['team-settings']['name']['price'];
			}

			if ( 'yes' === filter_input( INPUT_POST, 'atd_team_add_number' ) ) {
				$atd_team_additional_price += $atd_metas['team-settings']['number']['price'];
			}
		}

		$newly_added_cart_item_key = false;

		$tmp_dir         = uniqid();
		$generation_path = ATD_ORDER_UPLOAD_PATH . "/$tmp_dir";
		$generation_url  = ATD_ORDER_UPLOAD_URL . "/$tmp_dir";
		if ( wp_mkdir_p( $generation_path ) ) {
			$generation_url     = ATD_ORDER_UPLOAD_URL . "/$tmp_dir";
			$zip_name           = $this->get_output_zip_folder_name( $main_variation_id );
			$final_canvas_parts = filter_input( INPUT_POST, 'final_canvas_parts' );
			$result             = $this->export_data_to_files( $generation_path, $final_canvas_parts, $main_variation_id, $zip_name );
			if ( ! empty( $result ) && is_array( $result ) ) {
				$final_canvas_parts['output']['files']       = $result;
				$final_canvas_parts['output']['working_dir'] = $tmp_dir;
				$final_canvas_parts['output']['zip']         = $zip_name;
				$final_canvas_parts['output']['tpl']         = filter_input( INPUT_POST, 'tpl' );

				if ( class_exists( 'Ofb' ) ) {
					if ( isset( $atd_metas['form-builder'] ) ) {
						if ( $atd_metas['form-builder'] != '' ) {
							if ( isset( $data ) && ! empty( $data ) ) {
								$final_canvas_parts['output']['form_fields'] = $data;
							}
							$final_canvas_parts['output']['total_price_form'] = $total_price_form;
						}
					}
				}

				if ( 'yes' === $atd_metas['team-settings']['enable-team'] ) {

					if ( 'yes' === filter_input( INPUT_POST, 'atd_team_add_number' ) || 'yes' === filter_input( INPUT_POST, 'atd_team_add_name' ) ) {
						$final_canvas_parts['output']['enable-team'] = 'yes';
					} else {
						$final_canvas_parts['output']['enable-team'] = 'no';
					}

					$final_canvas_parts['output']['atd_team_additional_price'] = $atd_team_additional_price;
					$final_canvas_parts['output']['atd_team_data_recap']       = (array) json_decode( stripslashes_deep( filter_input( INPUT_POST, 'atd_team_data_recap' ) ), true );
				}

				if ( ! empty( $cart_item_key ) ) {
					WC()->cart->remove_cart_item( $cart_item_key );
					$cart_item_key = true;
				} else {
					$cart_item_key = false;
				}

				$newly_added_cart_item_key = $this->add_designs_to_cart( $final_canvas_parts );

				if ( $newly_added_cart_item_key && $cart_item_key ) {
					$message = "<div class='atd_notification success f-right'>" . __( 'Item successfully updated.', 'allada-tshirt-designer-for-woocommerce' ) . " <a href='$cart_url'>" . __( 'View Cart', 'allada-tshirt-designer-for-woocommerce' ) . '</a></div>';
				} elseif ( $newly_added_cart_item_key ) {
					$message = "<div class='atd_notification success f-right'>" . __( 'Product successfully added to basket.', 'allada-tshirt-designer-for-woocommerce' ) . " <a href='$cart_url'>View Cart</a></div>";
				} else {
					$message = "<div class='atd_notification failure f-right'>" . __( 'A problem occured while adding the product to the cart. Please try again.', 'allada-tshirt-designer-for-woocommerce' ) . '</div>';
				}
			} else {
				$message = "<div class='atd_notification failure f-right'>" . __( 'A problem occured while generating the output files... Please try again.', 'allada-tshirt-designer-for-woocommerce' ) . '</div>';
			}
		} else {
			$message = "<div class='atd_notification failure f-right'>" . __( "The creation of the directory $generation_path failed. Make sure that the complete path is writeable and try again.", 'allada-tshirt-designer-for-woocommerce' ) . '</div>';
		}

		echo wp_json_encode(
			array(
				'success'     => $newly_added_cart_item_key,
				'message'     => $message,
				'url'         => $cart_url,
				'form_fields' => $data,
			)
		);
		die();
	}

	/**
	 * Add designs to cart.
	 *
	 * @global type $woocommerce The woocommerce.
	 * @param type $final_canvas_parts The final canvas parts parts.
	 * @return type The newly added cart item key.
	 */
	private function add_designs_to_cart( $final_canvas_parts, $related_variations = array() ) {
		global $woocommerce;
		$newly_added_cart_item_key = false;

		if ( isset( $_POST['variations'] ) ) {
			$variations_str = stripslashes_deep( filter_input( INPUT_POST, 'variations' ) );
			$variations     = json_decode( $variations_str, true );
		} else {
			$variations = $related_variations;
		}

		foreach ( $variations as $variation_name => $variation_info ) {
			$variation_id = $variation_info['id'];
			$quantity     = $variation_info['qty'];
			if ( $quantity <= 0 ) {
				continue;
			}

			$product   = wc_get_product( $variation_id );
			$variation = array();
			if ( 'simple' === $product->get_type() ) {
				$product_id = $variation_id;
			} else {
				$variation  = $product->get_variation_attributes();
				$product_id = $product->get_parent_id();
			}

			$variations = array();
			if ( isset( $_SESSION['atd_key'] ) ) {
				$variations = get_transient( $_SESSION['atd_key'] );
			}

			foreach ( $variation as $key => $value ) {
				if ( isset( $variations[ $key ] ) && '' === $value ) {
					$variation[ $key ] = $variations[ $key ];
				}
			}

			if ( isset( $_SESSION['combinaison'][ $variation_name ] ) ) {
				$variation = $_SESSION['combinaison'][ $variation_name ];
			} elseif ( 'variation' === $product->get_type() ) {
				$variation = $variation_info['choice'];
			}

			$product = wc_get_product( $product_id );

			if ( 'variable' === $product->get_type() ) {
				$newly_added_cart_item_key = $woocommerce->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation, array( 'atd_generated_data' => $final_canvas_parts ) );
			} else {
				$newly_added_cart_item_key = $woocommerce->cart->add_to_cart( $product_id, $quantity, '', '', array( 'atd_generated_data' => $final_canvas_parts ) );
			}

			if ( method_exists( $woocommerce->cart, 'maybe_set_cart_cookies' ) ) {
				$woocommerce->cart->maybe_set_cart_cookies();
			}
		}
		return $newly_added_cart_item_key;
	}

	/**
	 * Get design price.
	 */
	public function get_design_price_ajax() {
		if ( isset( $_POST['variations'] ) ) {
			$variations = filter_input(INPUT_POST, 'variations', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		} else {
			$variations = array();
		}

//		if ( isset( $_POST['dimensions'] ) ) {
//			$dimensions = $_POST['dimensions'];
//		} else {
//			$dimensions = array();
//		}

		$serialized_parts = (array) json_decode( stripslashes_deep( filter_input(INPUT_POST, 'serialized_parts', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) ) );
		$results          = array();
		if ( isset( $serialized_parts['variation_id'] ) ) {
			$variation_id              = $serialized_parts['variation_id'];
			$atd_product               = new ATD_Product( $variation_id );
			$atd_metas                 = $atd_product->settings;
			$get_form_data             = 0;
			$atd_team_additional_price = 0;
			if ( class_exists( 'Ofb' ) ) {
				if ( isset( $atd_metas['form-builder'] ) ) {
					if ( $atd_metas['form-builder'] != '' ) {
						$form_fields = $serialized_parts['form_fields'];

						$data = array();
						if ( isset( $form_fields ) ) {
							foreach ( $form_fields as $key => $value ) {
								$data[ $key ] = $value;
							}
						}

						$get_form_data = get_form_data( $atd_metas['form-builder'], $data );
					}
				}
			}
			if ( 'yes' === $atd_metas['team-settings']['enable-team'] ) {
				if ( 'yes' === $serialized_parts['atd_team_add_name'] ) {
					$atd_team_additional_price += $atd_metas['team-settings']['name']['price'];
				}
				if ( 'yes' === $serialized_parts['atd_team_add_number'] ) {
					$atd_team_additional_price += $atd_metas['team-settings']['number']['price'];
				}
			}

			$tpl_base_price = atd_get_template_price( filter_input( INPUT_POST, 'tpl' ) );
			foreach ( $variations as $variation_id => $quantity ) {
				$product       = wc_get_product( $variation_id );
				$product_price = $this->apply_quantity_based_discount_if_needed( $product, $product->get_price(), $variations );
				// $a_price = $this->get_additional_price($variation_id, $serialized_parts, $dimensions);
				$results[ $variation_id ] = $product_price + $tpl_base_price + $get_form_data + $atd_team_additional_price;
			}
		}

		echo wp_json_encode(
			array(
				'prices' => $results,
			)
		);
		die();
	}

	/**
	 * Export data to archive.
	 *
	 * @param string $generation_dir Working directory path.
	 * @param array  $data Data to export.
	 * @param int    $variation_id Product/Variation ID.
	 * @return boolean|string
	 */
	private function export_data_to_files( $generation_dir, $data, $variation_id, $zip_name, $pdf_watermark = false ) {
		global $atd_settings;
		$global_output_settings = $atd_settings['atd-output-options'];
		$generate_zip           = false;

		$atd_product             = new ATD_Product( $variation_id );
		$product_metas           = $atd_product->settings;
		$product_output_settings = atd_get_proper_value( $product_metas, 'output-settings', array() );
		$watermark               = false;

		if ( $product_output_settings['zip-output'] == 'yes' ) {
			$generate_zip = true;
		}

		$atd_img_format     = filter_input(INPUT_POST, 'format');

		$allowed_extensions = array( 'png', 'jpg' );
		if ( ! in_array( $atd_img_format, $allowed_extensions ) ) {
			return false;
		}

		$output_arr = array();
		foreach ( $data as $part_key => $part_data ) {
			if ( isset( $part_data['json'] ) && ! empty( $part_data['json'] ) && isset( $part_data['original_part_img'] ) && ! empty( $part_data['original_part_img'] ) ) {
				$part_dir = "$generation_dir/$part_key";
				if ( ! wp_mkdir_p( $part_dir ) ) {
					echo "Can't create part directory...";
					continue;
				}

				// Part image
				$output_file_path = $part_dir . "/$part_key.$atd_img_format";
				$moved                            = move_uploaded_file( $_FILES[ $part_key ]['tmp_name']['image'], $output_file_path );
				$output_arr[ $part_key ]['image'] = "$part_key.$atd_img_format";

				// Preview
				$output_arr[ $part_key ]['preview'] = $output_arr[ $part_key ]['image'];
				$output_arr[ $part_key ]['file']    = "$part_key.$atd_img_format";

				$fonts = array();
				// SVG
				if ( $product_output_settings['output-format'] == 'svg' ) {
					$svg_path = $part_dir . "/$part_key.svg";

					file_put_contents( $svg_path, stripcslashes( $part_data['svg'] ), FILE_APPEND | LOCK_EX );
					$this->embed_images_in_svg( $svg_path, $svg_path );
					$output_file_path = $svg_path;

					// Fonts extraction
					$raw_json       = $part_data['json'];
					$json           = str_replace( "\n", '|n', $raw_json );
					$unslashed_json = stripslashes_deep( $json );
					$decoded_json   = json_decode( $unslashed_json );
					if ( ! is_object( $decoded_json ) ) {
						continue;
					}
					$map = array_map( create_function( '$o', 'return $o->type;' ), $decoded_json->objects );
					foreach ( $decoded_json->objects as $object ) {
						$object_type = $object->type;
						if ( $object_type == 'text' || $object_type == 'i-text' ) {
							if ( ! in_array( $object->fontFamily, $fonts ) ) {
								array_push( $fonts, $object->fontFamily );
							}
						}
					}

					$output_arr[ $part_key ]['file'] = "$part_key.svg";
				}
			}
		}
		$this->generate_design_archive( $generation_dir, "$generation_dir/$zip_name" );
		return $output_arr;
	}

	/**
	 * Embed image in svg.
	 *
	 * @param type $input The input.
	 * @param type $output The output.
	 */
	private function embed_images_in_svg( $input, $output ) {
		$xdoc = new DomDocument();
		$xdoc->Load( $input );
		$images = $xdoc->getElementsByTagName( 'image' );
		for ( $i = 0; $i < $images->length; $i++ ) {
			$tagName    = $xdoc->getElementsByTagName( 'image' )->item( $i );
			$attribNode = $tagName->getAttributeNode( 'xlink:href' );
			$img_src    = $attribNode->value;
			if ( strpos( $img_src, 'data:image' ) !== false ) {
				continue;
			}

			$type   = pathinfo( $img_src, PATHINFO_EXTENSION );
			$data   = $this->url_get_contents( $img_src );
			$base64 = 'data:image/' . $type . ';base64,' . base64_encode( $data );

			$tagName->setAttribute( 'xlink:href', $base64 );

			$new_svg = $xdoc->saveXML();
			file_put_contents( $output, $new_svg );
		}
	}

	/**
	 * Creates a compressed zip file
	 *
	 * @param type $source Input directory path to zip
	 * @param type $destination Output file path
	 * @return boolean
	 */
	private function generate_design_archive( $source, $destination ) {
		if ( ! extension_loaded( 'zip' ) || ! file_exists( $source ) ) {
			return false;
		}
		$zip = new ZipArchive();
		if ( ! $zip->open( $destination, ZIPARCHIVE::CREATE ) ) {
			return false;
		}

		$source = str_replace( '\\', DIRECTORY_SEPARATOR, realpath( $source ) );

		if ( is_dir( $source ) === true ) {
			$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $source ), RecursiveIteratorIterator::SELF_FIRST );

			foreach ( $files as $file ) {
				$file = str_replace( '\\', DIRECTORY_SEPARATOR, $file );

				// Ignore "." and ".." folders
				if ( in_array( substr( $file, strrpos( $file, '/' ) + 1 ), array( '.', '..' ) ) ) {
					continue;
				}

				$file = realpath( $file );
				if ( is_dir( $file ) === true ) {
					$zip->addEmptyDir( str_replace( $source . DIRECTORY_SEPARATOR, '', $file . DIRECTORY_SEPARATOR ) );
				} elseif ( is_file( $file ) === true ) {
					$zip->addFromString( str_replace( $source . DIRECTORY_SEPARATOR, '', $file ), $this->url_get_contents( $file ) );
				}
			}
		} elseif ( is_file( $source ) === true ) {
			$zip->addFromString( basename( $source ), $this->url_get_contents( $source ) );
		}

		return $zip->close();
	}

	/**
	 * Replacement just in case file_get_contents fails.
	 *
	 * @param type $url The url.
	 * @return type $output The output.
	 */
	private function url_get_contents( $url ) {
		$response  = wp_remote_get( $url );
		$http_code = wp_remote_retrieve_response_code( $response );
		if ( 200 == $http_code ) {
			$output = wp_remote_retrieve_body( $response );
		} else {
			$output = false;
		}
		// $output = file_get_contents($Url);
		return $output;
	}

	/**
	 * Get user saved designs.
	 *
	 * @global type $current_user
	 * @return type
	 */
	public function get_user_saved_designs() {
		global $current_user;
		$user_designs = get_user_meta( $current_user->ID, 'atd_saved_designs' );
		if ( empty( $user_designs ) ) {
			esc_html__( 'No saved design.', 'allada-tshirt-designer-for-woocommerce' );
			return;
		}
		?>
		<h2><?php esc_html__( 'Saved Designs', 'allada-tshirt-designer-for-woocommerce' ); ?></h2>
		<table class="shop_table shop_table_responsive my_account_orders">

			<thead>
				<tr>
					<th class="order-date"><span class="nobr"><?php esc_html__( 'Name', 'allada-tshirt-designer-for-woocommerce' ); ?></span></th>
					<th class="order-date"><span class="nobr"><?php esc_html__( 'Date', 'allada-tshirt-designer-for-woocommerce' ); ?></span></th>
					<th class="order-status"><span class="nobr"><?php esc_html__( 'Preview', 'allada-tshirt-designer-for-woocommerce' ); ?></span></th>
					<th class="order-actions"><span class="nobr">&nbsp;</span></th>
				</tr>
			</thead>

			<tbody>
				<?php
				foreach ( $user_designs as $s_index => $user_design ) {
					if ( ! empty( $user_design ) ) {
						$variation_id  = $user_design[0];
						$save_time     = $user_design[1];
						$design_name   = $user_design[2];
						$design_data   = $user_design[3];
						$order_item_id = '';
						if ( count( $user_design ) >= 5 ) {
							$order_item_id = $user_design[4];
						}
						?>
						<tr class='atd_order_item' data-item='<?php echo esc_attr( $variation_id ); ?>'>
							<td ><?php echo esc_html( $design_name ); ?></td>
							<td ><?php echo esc_html( $save_time ); ?></td>
							<?php
							if ( is_array( $design_data ) ) {
								$tmp_dir     = $design_data['output']['working_dir'];
								$design_data = $design_data['output']['files'];
								?>
								<td>
									<?php
									foreach ( $design_data as $data_key => $data ) {
										if ( ! empty( $data ) ) {
											$generation_url        = ATD_SAVED_DESIGN_UPLOAD_URL . "/$tmp_dir/$data_key/";
											$img_src               = $generation_url . $data['image'];
											$original_part_img_url = $user_design[3][ $data_key ]['original_part_img'];
											if ( $order_item_id ) {
												$modal_id = $order_item_id . "_$variation_id" . "_$data_key";
											} else {
												$modal_id = $s_index . "_$variation_id" . "_$data_key";
											}
											?>
											<span><a class="atd-button button atd-prev-cart-des" data-part-name="<?php echo esc_attr( $data_key ); ?>" data-toggle="o-modal" data-target="#<?php echo esc_attr( $modal_id ); ?>"><?php echo  esc_attr( ucfirst( $data_key ) ); ?></a></span>
											<?php
											$modal = '<div class="omodal fade o-modal atd-modal atd_part" id="' . $modal_id . '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                        <div class="omodal-dialog">
                                                          <div class="omodal-content">
                                                            <div class="omodal-header">
                                                              <button type="button" class="close" data-dismiss="omodal" aria-hidden="true">&times;</button>
                                                              <h4 class="omodal-title" id="myModalLabel' . $modal_id . '">' . __( 'Preview', 'allada-tshirt-designer-for-woocommerce' ) . '</h4>
                                                            </div>
                                                            <div class="omodal-body">
                                                                <div style="background-image:url(' . $original_part_img_url . ')"><img src="' . $img_src . '"></div>
                                                            </div>
                                                          </div>
                                                        </div>
                                                      </div>';
											array_push( ATD_Retarded_Actions::$code, $modal );
											add_action( 'wp_footer', array( 'atd_retarded_actions', 'display_code' ), 10, 1 );
										}
									}
									?>
								</td>
								<td>
									<?php
									$atd_product = new ATD_Product( $variation_id );
                                                                        $allowed_html = atd_allowed_tags();
									if ( $order_item_id ) {      
                                                                                $url = $atd_product->get_design_url( false, false, $order_item_id );
										echo wp_kses( '<a class="atd-button button" href="' . esc_attr( $url ) . '">' . __( 'Load', 'allada-tshirt-designer-for-woocommerce' ) . '</a>', $allowed_html);
									} else {
                                                                                $url = $atd_product->get_design_url( $s_index );
										echo wp_kses( '<a class="atd-button button" href="' . esc_attr( $url ) . '">' . __( 'Load', 'allada-tshirt-designer-for-woocommerce' ) . '</a>', $allowed_html);
										echo wp_kses( '<a class="atd-button atd-delete-design button" data-index="' . $s_index . '">' . __( 'Delete', 'allada-tshirt-designer-for-woocommerce' ) . '</a>', $allowed_html);
									}
									?>
								</td>
								<?php
							}
							?>
						</tr>
						<?php
					}
				}
				?>
				<tr class="order">
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Save user temporary designs.
	 *
	 * @param type $user_login The user login.
	 * @param type $user The user.
	 */
	public function save_user_temporary_designs( $user_login, $user ) {
		if ( isset( $_SESSION['atd_designs_to_save'] ) ) {
			foreach ( $_SESSION['atd_designs_to_save'] as $variation_id => $design_array ) {
				foreach ( $design_array as $key => $design ) {
					$today = date( 'Y-m-d H:i:s' );
					add_user_meta( $user->ID, 'atd_saved_designs', array( $variation_id, $today, 'Save' . $today, $design ) );
				}
				unset( $_SESSION['atd_designs_to_save'][ $variation_id ] );
			}
			unset( $_SESSION['atd_designs_to_save'] );
		}
	}

	/**
	 * WAD apply quantity based discount.
	 *
	 * @param type $product The product.
	 * @param type $normal_price The normal price.
	 * @param type $products_qties The products quantities.
	 * @return type The price.
	 */
	private function apply_quantity_based_discount_if_needed( $product, $normal_price, $products_qties ) {
		// We check if there is a quantity based discount for this product
		$quantity_pricing = get_post_meta( $product->get_id(), 'o-discount', true );
		// $products_qties = $this->get_cart_item_quantities();
		$rules_type = atd_get_proper_value( $quantity_pricing, 'rules-type', 'intervals' );

		$id_to_check = $product->get_id();
		if ( ! isset( $products_qties[ "$id_to_check" ] ) || empty( $quantity_pricing ) || ! isset( $quantity_pricing['enable'] ) ) {
			return $normal_price;
		}

		if ( isset( $quantity_pricing['rules'] ) && $rules_type == 'intervals' ) {
			foreach ( $quantity_pricing['rules'] as $rule ) {
				if ( $rule['min'] <= $products_qties[ $id_to_check ] && $products_qties[ $id_to_check ] <= $rule['max'] ) {
					if ( $quantity_pricing['type'] == 'fixed' ) {
						$normal_price -= $rule['discount'];
					} elseif ( $quantity_pricing['type'] == 'percentage' ) {
						$normal_price -= ( $normal_price * $rule['discount'] ) / 100;
					}
					break;
				}
			}
		} elseif ( isset( $quantity_pricing['rules-by-step'] ) && $rules_type == 'steps' ) {

			foreach ( $quantity_pricing['rules-by-step'] as $rule ) {
				if ( $products_qties[ $id_to_check ] % $rule['every'] == 0 ) {
					if ( $quantity_pricing['type'] == 'fixed' ) {
						$normal_price -= $rule['discount'];
					} elseif ( $quantity_pricing['type'] == 'percentage' ) {
						$normal_price -= ( $normal_price * $rule['discount'] ) / 100;
					}
					break;
				}
			}
		}
		return $normal_price;
	}

	/**
	 * Save customized item meta.
	 *
	 * @global type $atd_settings The ATD settings.
	 * @param type $item_id The item id.
	 * @param type $order_item The order item.
	 * @param type $order_id The order id.
	 */
	public function save_customized_item_meta( $item_id, $order_item, $order_id ) {
		global $atd_settings;
		$output_options = atd_get_proper_value( $atd_settings, 'atd-output-options', array() );
		$use_order_id   = atd_get_proper_value( $output_options, 'use-order-id-as-zip-name', 'no' );
		if ( $use_order_id == 'yes' ) {
			$old_tmp_dir         = $order_item->legacy_values['atd_generated_data']['output']['working_dir'];
			$old_generation_path = ATD_ORDER_UPLOAD_PATH . "/$old_tmp_dir";
			$new_generation_path = ATD_ORDER_UPLOAD_PATH . "/$order_id-$item_id";
			// Suppression ancien zip
			unlink( $old_generation_path . '/' . $order_item->legacy_values['atd_generated_data']['output']['zip'] );
			// Renommage du répertoire
			rename( $old_generation_path, $new_generation_path );
			// Regénération du zip
			$new_zip_name = "$order_id-$item_id.zip";
			$this->generate_design_archive( $new_generation_path, "$new_generation_path/$new_zip_name" );
			// Upate metas
			$order_item->legacy_values['atd_generated_data']['output']['working_dir'] = "$order_id-$item_id";
			$order_item->legacy_values['atd_generated_data']['output']['zip']         = $new_zip_name;
		}
		if ( isset( $order_item->legacy_values['atd_generated_data'] ) ) {
			wc_add_order_item_meta( $item_id, 'atd_data', $order_item->legacy_values['atd_generated_data'] );
		}

		if ( isset( $order_item->legacy_values['atd-uploaded-designs'] ) ) {
			wc_add_order_item_meta( $item_id, 'atd_data_upl', $order_item->legacy_values['atd-uploaded-designs'] );
		}
		if ( isset( $order_item->legacy_values['atd_design_pricing_options'] ) && ! empty( $order_item->legacy_values['atd_design_pricing_options'] ) ) {
			// $atd_design_pricing_options_data = $this->get_design_pricing_options_data($order_item->legacy_values['atd_design_pricing_options']);
			$atd_design_pricing_options_data = self::get_design_pricing_options_data( $order_item->legacy_values['atd_design_pricing_options'] );
			wc_add_order_item_meta( $item_id, '_atd_design_pricing_options', $atd_design_pricing_options_data );
		}
	}

	/**
	 * Get design pricing options data.
	 *
	 * @param type $atd_design_pricing_options The ATD design pricing options.
	 * @return string The ATD design pricing options.
	 */
	public static function get_design_pricing_options_data( $atd_design_pricing_options ) {
		$atd_design_pricing_options_data = '';
		if ( ! empty( $atd_design_pricing_options ) && function_exists( 'ninja_forms_get_field_by_id' ) ) {
			$decoded_json = self::atd_json_decode( $atd_design_pricing_options );
			if ( is_object( $decoded_json ) ) {
				$atd_ninja_form_fields_to_hide_name = array( '_wpnonce', '_ninja_forms_display_submit', '_form_id', '_wp_http_referer' );
				$atd_ninja_form_fields_type_to_hide = array( '_calc', '_honeypot' );
				$atd_ninja_form_id                  = '';
				if ( isset( $decoded_json->atd_design_opt_list->_form_id ) ) {
					$atd_ninja_form_id = $decoded_json->atd_design_opt_list->_form_id;
				}
				$atd_design_pricing_options_data .= '<div class = "atd_cart_item_form_data_wrap mg-bot-10">';
				foreach ( $decoded_json->atd_design_opt_list as $ninja_forms_field_id => $ninja_forms_field_value ) {
					if ( ! in_array( $ninja_forms_field_id, $atd_ninja_form_fields_to_hide_name ) ) {
						$atd_get_ninjaform_field_arg = array(
							'id'      => str_replace( 'ninja_forms_field_', '', $ninja_forms_field_id ),
							'form_id' => $atd_ninja_form_id,
						);
						$atd_ninjaform_field         = ninja_forms_get_field_by_id( $atd_get_ninjaform_field_arg );
						if ( ! in_array( $atd_ninjaform_field['type'], $atd_ninja_form_fields_type_to_hide ) && ! ( empty( $atd_ninjaform_field['data']['label'] ) && empty( $ninja_forms_field_value ) ) ) {
							$atd_ninja_form_field_value       = $ninja_forms_field_value;
							$atd_design_pricing_options_data .= '<b>' . $atd_ninjaform_field['data']['label'] . '</b>: ' . $atd_ninja_form_field_value . '<br />';
						}
					}
				}
				$atd_design_pricing_options_data .= '<div class = "atd_cart_item_form_data_wrap">';
			}
		}
		return $atd_design_pricing_options_data;
	}

	/**
	 * Get order custom admin data.
	 *
	 * @global type $atd_settings The ATD settings.
	 * @param type $item_id The item id.
	 * @param type $item The item.
	 * @param type $_product The product.
	 * @return type The output.
	 */
	public function get_order_custom_admin_data( $item_id, $item, $_product ) {
		global $atd_settings;
                $allowed_tag = ['data-product-id', 'data-part-name', ];
                $allowed_html = atd_allowed_tags($allowed_tag);
		$output_options             = atd_get_proper_value( $atd_settings, 'atd-output-options', array() );
		$design_composition_visible = atd_get_proper_value( $output_options, 'design-composition', 'no' );
		$output                     = '';
		if ( null !== wc_get_product( $item['product_id'] ) ) {
			$product_id = $item['product_id'];
		} elseif ( null !== wc_get_product( $item['variation_id'] ) ) {
			$product_id = $item['variation_id'];
		}
		if ( ! is_object( $item ) ) {
			return;
		}

		$meta    = get_post_meta( $item['product_id'], 'atd-metas', true );
		$product = wc_get_product( $item['product_id'] );

		if ( $product->get_type() == 'variable' ) {
			$variation_id = $item['variation_id'];
		} else {
			$variation_id = $item['product_id'];
		}

		$config_id = $meta[ $variation_id ]['config-id'];
		$configs   = atd_get_configs();

		foreach ( $configs as $key => $value ) {
			if ( $value->ID == $config_id ) {
				$parts = maybe_unserialize( $value->meta_value );
				$parts = $parts['parts'];
			}
		}

		$uploaded_data = wc_get_order_item_meta( $item_id, 'atd_data_upl' );
		if ( $uploaded_data ) {
			$output .= "<div class='atd_order_item' data-item='$item_id'>";
			foreach ( $uploaded_data as $design_url ) {
				$output .= "<a class='button' href='" . $design_url . "' download='" . basename( $design_url ) . "'>" . __( 'Download custom design', 'allada-tshirt-designer-for-woocommerce' ) . '</a> ';
			}
			$output .= '</div>';
		} elseif ( isset( $item['atd_data'] ) ) {

			if ( 'yes' == $item['atd_data']['output']['enable-team'] ) {
				$output .= '<div>Team:' . __( 'yes', 'allada-tshirt-designer-for-woocommerce' ) . '</div>';
			} else {
				$output .= '<div>Team:' . __( 'no', 'allada-tshirt-designer-for-woocommerce' ) . '</div>';
			}

			$output           .= "<div class='atd_order_item' data-item='$item_id'>";
			$unserialized_data = $item['atd_data'];
			$design_data       = array();
			if ( isset( $item['atd_data']['output']['files'] ) ) {
				$design_data = $item['atd_data']['output']['files'];
			}
			$customization_list = $item['atd_data'];
			if ( class_exists( 'Ofb' ) ) {
				if ( isset( $customization_list['output']['form_fields'] ) ) {
					$form_fields = $customization_list['output']['form_fields'];
					foreach ( $form_fields as $key => $value ) {
						if ( ! is_array( $value ) ) {
							$output .= '<p>' . $key . ' : ' . $value . '</p>';
						} else {
							$output .= '<p>' . $key . ' : ';
							foreach ( $value as $item => $data ) {
								$output .= $data . ' ';
							}
							$output .= ' </p>';
						}
					}
				}
			}

			$design_details = '';
			if ( isset( $unserialized_data['output']['tpl'] ) && ! empty( $unserialized_data['output']['tpl'] ) ) {
				$tpl_name       = get_the_title( $unserialized_data['output']['tpl'] );
				$design_details = "<br><b>Used Template</b>: $tpl_name<br>";
			}
			if ( $design_composition_visible == 'yes' ) {
				$design_details .= $this->get_design_details_from_json( $unserialized_data );
			}
			$tmp_dir = $unserialized_data['output']['working_dir'];
			if ( count( $item['item_meta']['atd_data'] ) > 1 ) {
				foreach ( $design_data as $data_key => $data ) {
					$tmp_dir        = $unserialized_data['output']['working_dir'];
					$generation_url = ATD_ORDER_UPLOAD_URL . "/$tmp_dir/$data_key/";
					if ( is_admin() ) {
						$img_src = $generation_url . $data['image'];
					} else {
						if ( isset( $data['preview'] ) ) {
							$img_src = $generation_url . $data['preview'];
						} else {
							$img_src = $generation_url . $data['image'];
						}
					}
					if ( isset( $unserialized_data[ $data_key ]['original_part_img'] ) ) {
						$original_part_img_url = $unserialized_data[ $data_key ]['original_part_img'];
					} else {
						$original_part_img_url = '';
					}
					$modal_id = uniqid() . "_$item_id" . "_$data_key";
					if ( isset( $parts[ $data_key ]['enable'] ) && $parts[ $data_key ]['enable'] == 'yes' ) {
						$output .= '<span><a class="o-modal-trigger button" data-product-id="' . $product_id . '" data-part-name="' . $data_key . '" data-toggle="o-modal" data-target="#' . $modal_id . '">' . ucfirst( $data_key ) . '</a></span>';
					}
					$output .= '<div class="omodal fade o-modal atd_part" id="' . $modal_id . '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="omodal-dialog">
                                  <div class="omodal-content">
                                    <div class="omodal-header">
                                      <button type="button" class="close" data-dismiss="omodal" aria-hidden="true">&times;</button>
                                      <h4 class="omodal-title" id="myModalLabel' . $modal_id . '">Preview</h4>
                                    </div>
                                    <div class="omodal-body">
                                        <div style="background-image:url(' . $original_part_img_url . ')"><img src="' . $img_src . '"></div>
                                    </div>
                                  </div>
                                </div>
                              </div>';
				}
			}
			$zip_file = $unserialized_data['output']['zip'];
			if ( ! empty( $zip_file ) ) {
				$output .= "<a class='button' href='" . ATD_ORDER_UPLOAD_URL . "/$tmp_dir/$zip_file' download='" . basename( $zip_file ) . "'>" . __( 'Download design', 'allada-tshirt-designer-for-woocommerce' ) . '</a> ';
			}
			if ( isset( $item['atd_design_pricing_options'] ) ) {
				$output .= $item['atd_design_pricing_options'];
			}

			$output .= $design_details;

			$output .= '</div>';
		}

		echo wp_kses($output,$allowed_html);
	}

	/**
	 * Get design details from json.
	 *
	 * @param type $unserialized_design_data The unserialized design data.
	 * @return type The output.
	 */
	public function get_design_details_from_json( $unserialized_design_data ) {
		ob_start();
		?>
		<table class="atd-grid atd-design-composition">
			<div style="margin: 10px 0;"><?php esc_html_e( 'Design composition: ', 'allada-tshirt-designer-for-woocommerce' ); ?></div>
			<?php
			foreach ( $unserialized_design_data as $part_name => $part_data ) {
				if ( isset( $part_data['json'] ) && ! empty( $part_data['json'] ) ) {
					$part_details = json_decode( $part_data['json'] );
					?>
					<tr>
						<td class="atd-col-1-3 atd-part-name">
							<?php echo esc_html( $part_name ); ?>
						</td>
						<td class="atd-col-2-3 atd-part-details">
							<?php
							$this->get_design_part_details( $part_details );
							?>
						</td>
					</tr>
					<?php
				}
			}
			?>
		</table>
		<?php
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Get design part details.
	 *
	 * @param type $part_details The part details.
	 */
	public function get_design_part_details( $part_details ) {
		$to_exclude = apply_filters(
			'atd_part_details_to_exclude',
			array(
				'originX',
				'originY',
				'strokeLineCap',
				'strokeLineJoin',
				'fillRule',
				'globalCompositeOperation',
				'crossOrigin',
				'meetOrSlice',
				'strokeMiterLimit',
				'paths',
			)
		);
		?>
		<table>
			<?php
			foreach ( $part_details->objects as $i => $details ) {
				if ( ! empty( $details ) ) {
					?>
					<tr>
						<?php
						$item = get_object_vars( $details );
						if ( $details->type == 'group' ) {
							// Curved text
							if ( property_exists( $details, 'originalText' ) ) {
								$item['type']       = 'Curved Text';
								$item['FontSize']   = $item['objects'][0]->fontSize;
								$item['FontWeight'] = $item['objects'][0]->fontWeight;
								$item['FontFamily'] = $item['objects'][0]->fontFamily;
								unset( $item['originalText'] );
								unset( $item['objects'] );
							}
						}
						$this->get_part_item_details( $item, $to_exclude );
						?>
					</tr>
					<?php
				}
			}
			?>
		</table>
		<?php
	}

	/**
	 * Get part item details.
	 *
	 * @param type $item The item.
	 * @param type $to_exclude The exclude.
	 */
	public function get_part_item_details( $item, $to_exclude ) {
		?>
		<td class="atd-col-1-3">
			<?php echo wp_kses_post( ucfirst( $item['type'] ) ); ?>
		</td>
		<td class="atd-col-2-3">
			<?php
			foreach ( $item as $key => $value ) {
				if ( $key == 'type' ) {
					continue;
				}
				if ( $key == 'src' ) {
					$key   = 'Image';
					$value = "<img src='$value'><a class='button' href='$value' download='" . basename( $value ) . "'>Download</a>";
				}
				if ( in_array( $key, $to_exclude ) ) {
					continue;
				}
				if ( is_object( $value ) ) {
					continue;
				}
				if ( ! empty( $value ) ) {
					?>
					<span><strong><?php echo wp_kses_post( ucfirst( $key ) ); ?></strong>: <?php echo wp_kses_post( $value ); ?></span><br />
					<?php
				}
			}
			?>
		</td>
		<?php
	}

	/**
	 * Add order design to mail.
	 *
	 * @param type $attachments The attachments.
	 * @param type $status The status.
	 * @param type $order The order.
	 * @return type The attachments.
	 */
	public function add_order_design_to_mail( $attachments, $status, $order ) {
		$allowed_statuses = array( 'new_order', 'customer_invoice', 'customer_processing_order', 'customer_completed_order' );
		if ( isset( $status ) && in_array( $status, $allowed_statuses ) ) {
			$items = $order->get_items();
			foreach ( $items as $order_item_id => $item ) {
				$upload_dir = wp_upload_dir();
				if ( isset( $item['atd_data'] ) ) {
					$unserialized_data = $item['atd_data'];
					$tmp_dir           = $unserialized_data['output']['working_dir'];
					array_push( $attachments, ATD_ORDER_UPLOAD_PATH . "/$tmp_dir/" . $unserialized_data['output']['zip'] );
				} elseif ( isset( $item['atd_data_upl'] ) ) {
					// Looks like the structure changed for latest versions of WC (tested on 2.3.7)
					$design_url = $item['item_meta']['atd_data_upl'][0];
					if ( is_serialized( $design_url ) ) {
						$unserialized_urls = $design_url;
						foreach ( $unserialized_urls as $design_url ) {
							$design_path = str_replace( ATD_ORDER_UPLOAD_URL, ATD_ORDER_UPLOAD_PATH, $design_url );
							array_push( $attachments, $design_path );
						}
					} else {
						$design_path = str_replace( ATD_ORDER_UPLOAD_URL, ATD_ORDER_UPLOAD_PATH, $design_url );
						array_push( $attachments, $design_path );
					}
				}
			}
		}
		return str_replace( '"', '', $attachments );
	}

	/**
	 * Get user account products meta.
	 *
	 * @param type $item_id The item id.
	 * @param type $item The item.
	 * @param type $order The order.
	 * @return type The output.
	 */
	public function get_user_account_products_meta( $item_id, $item, $order ) {
		if ( ! is_account_page() ) {
			return;
		}
		$output           = '';
		$invalid_statuses = array( 'wc-cancelled', 'wc-refunded', 'wc-failed' );
		if ( ! in_array( $order->get_status(), $invalid_statuses, true ) && isset( $item['variation_id'] ) && ( ! empty( $item['variation_id'] ) || '0' === $item['variation_id'] ) ) {
			if ( null !== wc_get_product( $item['product_id'] ) ) {
				$product = wc_get_product( $item['product_id'] );
			} elseif ( null !== wc_get_product( $item['variation_id'] ) ) {
				$product = wc_get_product( $item['variation_id'] );
			}
			$item_id = uniqid();
			ob_start();
			$this->get_order_custom_admin_data( $item_id, $item, $product );
			$admin_data = ob_get_contents();
			ob_end_clean();
			$output .= $admin_data;
		}
		echo wp_kses_post($output);
	}

	/**
	 * Get cart item price.
	 *
	 * @param type $cart The cart.
	 * @return type
	 */
	public function get_cart_item_price( $cart ) {
		if ( true === $_SESSION['atd_calculated_totals'] ) {
			return;
		}
		foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {

			if ( $cart_item['variation_id'] ) {
				$variation_id = $cart_item['variation_id'];
			} else {
				$variation_id = $cart_item['product_id'];
			}

			if ( function_exists( 'icl_object_id' ) ) {
				// WPML runs the hook twice which doubles the price in cart.
				// We just need to make sure the plugin uses the original price so it won't matter
				$variation  = wc_get_product( $variation_id );
				$item_price = $variation->get_price();
			} else {
				$item_price = $cart_item['data']->get_price();
			}
			if ( isset( $cart_item['atd_generated_data'] ) ) {
				$data             = $cart_item['atd_generated_data'];
				$atd_product      = new ATD_Product( $variation_id );
				$atd_metas        = $atd_product->settings;
				$form_fields_data = array();
				$total_price_form = 0;
				if ( class_exists( 'Ofb' ) ) {
					if ( isset( $atd_metas['form-builder'] ) ) {
						if ( $atd_metas['form-builder'] != '' ) {
							if ( isset( $data['output']['total_price_form'] ) ) {
								$total_price_form = $data['output']['total_price_form'];
							}
						}
					}
				}

				// $a_price = $this->get_additional_price($variation_id, $data);
				$item_price += $total_price_form;

				$atd_team_additional_price = 0;
				if ( isset( $atd_metas['team-settings']['enable-team'] ) ) {
					if ( 'yes' === $atd_metas['team-settings']['enable-team'] ) {
						$atd_team_additional_price = $data['output']['atd_team_additional_price'];
					}
				}
				$item_price += $atd_team_additional_price;
			}
			if ( isset( $cart_item['atd_design_pricing_options'] ) && ! empty( $cart_item['atd_design_pricing_options'] ) ) {
				$a_price    = $this->get_design_options_prices( $cart_item['atd_design_pricing_options'] );
				$item_price = $item_price + $a_price;
			}
			$cart_item['data']->set_price( $item_price );
		}
		$_SESSION['atd_calculated_totals'] = true;
	}

	/**
	 * Get design options prices.
	 *
	 * @param type $json_atd_design_options The json design options.
	 * @return type The json design options.
	 */
	private function get_design_options_prices( $json_atd_design_options ) {
		$atd_design_options_prices = 0;
		if ( ! empty( $json_atd_design_options ) ) {
			$json           = str_replace( "\n", '|n', $json_atd_design_options );
			$unslashed_json = stripslashes_deep( $json );
			$decoded_json   = json_decode( $unslashed_json );
			if ( is_object( $decoded_json ) && property_exists( $decoded_json, 'opt_price' ) ) {
				$atd_design_options_prices = $decoded_json->opt_price;
			}
		}
		return $atd_design_options_prices;
	}

	/**
	 * Get user account load button.
	 *
	 * @param type $actions The actions.
	 * @param type $order The order.
	 * @return type The actions.
	 */
	public function get_user_account_load_order_button( $actions, $order ) {
		$items = $order->get_items();
		foreach ( $items as $order_item_id => $item ) {
			if ( isset( $item['atd_data'] ) ) {
				if ( isset( $item['variation_id'] ) && ! empty( $item['variation_id'] ) ) {
					$product_id = $item['variation_id'];
				} else {
					$product_id = $item['product_id'];
				}
				$atd_product = new ATD_Product( $product_id );

				$actions['atd-reload'] = array(
					'url'  => $atd_product->get_design_url( false, false, $order_item_id ),
					'name' => __( 'Reload Design: ', 'allada-tshirt-designer-for-woocommerce' ) . $item['name'],
				);
			}
		}
		return $actions;
	}

	/**
	 * Unset data upload meta.
	 *
	 * @param type $hidden_meta The hidden meta.
	 * @return type The hidden meta.
	 */
	public function unset_atd_data_upl_meta( $hidden_meta ) {
		array_push( $hidden_meta, 'atd_data_upl' );
		array_push( $hidden_meta, '_atd_design_pricing_options' );
		return $hidden_meta;
	}

	/**
	 * Force individual cart item.
	 *
	 * @param type $cart_item_data
	 * @param type $product_id
	 * @return type
	 */
	public function force_individual_cart_items( $cart_item_data, $product_id ) {
		if ( isset( $_SESSION['atd-user-uploaded-designs'][ $product_id ] ) ) {
			$unique_cart_item_key         = md5( microtime() . rand() );
			$cart_item_data['unique_key'] = $unique_cart_item_key;
		}
		return $cart_item_data;
	}

	/**
	 * Json decode.
	 *
	 * @param type $json The json.
	 * @return type The json decode.
	 */
	public static function atd_json_decode( $json ) {
		$decoded_json = '';
		if ( ! empty( $json ) ) {
			$json           = str_replace( "\n", '|n', $json );
			$unslashed_json = stripslashes_deep( $json );
			$decoded_json   = json_decode( $unslashed_json );
		}
		return $decoded_json;
	}

	public function iconic_cart_count_fragments( $fragments ) {
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$fragments['div.header-cart-count'] = '<div class="header-cart-count">' . $cart_item_key . '<br><br>' . $cart_item['product_id'] . '</div>';
			return $fragments;
		}
	}

	public function add_related_custom_products_to_carts() {
		global $woocommerce;
		$message = '';
		if ( atd_woocommerce_version_check() ) {
			$cart_url = wc_get_cart_url();
		} else {
			$cart_url = $woocommerce->cart->get_cart_url();
		}
                
		$final_canvas_parts = filter_input(INPUT_POST, 'final_canvas_parts', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$variations_data    = json_decode( filter_input( INPUT_POST, 'variations_data' ) );
		$format             = filter_input(INPUT_POST, 'format');
		$cart_item_key      = filter_input(INPUT_POST, 'cart_item_key');
		if ( ! empty( $cart_item_key ) ) {
			WC()->cart->remove_cart_item( $cart_item_key );
			$cart_item_key = true;
		} else {
			$cart_item_key = false;
		}
		$tmp_arr     = array();
		$add_success = array();
		foreach ( $variations_data as $key => $data ) {
			if ( $data->value != '' ) {
				$qty    = $data->value;
				$scinde = explode( 'variation_qty_', $data->name );
				list($availlable_variation_id, $size, $product_id, $color) = explode( '_', $scinde[1] );
				$availlable_variation_id                                   = absint( $availlable_variation_id );
				$product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $product_id ) );
				if ( wc_get_product( $product_id ) && wc_get_product( $availlable_variation_id ) ) {
					$part_image = atd_get_part_image( $product_id, $availlable_variation_id, 'bg-inc' );

					foreach ( $part_image as $part_index => $part_url ) {
						$complete_url     = atd_o_get_proper_image_url( $part_url );
						$final_part_index = strtolower( $part_index );
						$final_canvas_parts[ $final_part_index ]['original_part_img'] = $complete_url;
					}

					$selected_attr      = atd_get_variation_attr_name( $product_id );
					$related_variations = array(
						$size => array(
							'id'     => $availlable_variation_id,
							'qty'    => $qty,
							'choice' => array(
								'attribute_' . $selected_attr['color'] => $color,
								'attribute_' . $selected_attr['size'] => $size,
							),
						),
					);

					$product_qty      = $qty;
					$atd_product      = new ATD_Product( $availlable_variation_id );
					$atd_metas        = $atd_product->settings;
					$total_price_form = 0;
					$data             = array();

					$newly_added_cart_item_key = false;
					$atd_team_additional_price = 0;
					if ( 'yes' === $atd_metas['team-settings']['enable-team'] ) {
						if ( 'yes' === filter_input( INPUT_POST, 'atd_team_add_name' ) ) {
							$atd_team_additional_price += $atd_metas['team-settings']['name']['price'];
						}

						if ( 'yes' === filter_input( INPUT_POST, 'atd_team_add_number' ) ) {
							$atd_team_additional_price += $atd_metas['team-settings']['number']['price'];
						}
					}
					if ( ! in_array( $tmp_dir, $tmp_arr ) ) {
						$tmp_dir = uniqid();
					} else {
						$tmp_dir = $tmp_arr[0];
					}
					$tmp_arr = array();
					array_push( $tmp_arr, $tmp_dir );
					$generation_path = ATD_ORDER_UPLOAD_PATH . "/$tmp_dir";
					$generation_url  = ATD_ORDER_UPLOAD_URL . "/$tmp_dir";

					if ( wp_mkdir_p( $generation_path ) ) {
						$generation_url = ATD_ORDER_UPLOAD_URL . "/$tmp_dir";
						$zip_name       = $this->get_output_zip_folder_name( $availlable_variation_id );
						$result         = $this->export_data_to_files( $generation_path, $final_canvas_parts, $availlable_variation_id, $zip_name );

						if ( ! empty( $result ) && is_array( $result ) ) {
							$final_canvas_parts['output']['files']       = $result;
							$final_canvas_parts['output']['working_dir'] = $tmp_dir;
							$final_canvas_parts['output']['zip']         = $zip_name;
							$final_canvas_parts['output']['tpl']         = filter_input( INPUT_POST, 'tpl' );

							if ( class_exists( 'Ofb' ) ) {
								if ( isset( $atd_metas['form-builder'] ) ) {
									if ( $atd_metas['form-builder'] != '' ) {
										if ( isset( $data ) && ! empty( $data ) ) {
											$final_canvas_parts['output']['form_fields'] = $data;
										}
										$final_canvas_parts['output']['total_price_form'] = $total_price_form;
									}
								}
							}

							if ( 'yes' === $atd_metas['team-settings']['enable-team'] ) {

								if ( 'yes' === filter_input( INPUT_POST, 'atd_team_add_number' ) || 'yes' === filter_input( INPUT_POST, 'atd_team_add_name' ) ) {
									$final_canvas_parts['output']['enable-team'] = 'yes';
								} else {
									$final_canvas_parts['output']['enable-team'] = 'no';
								}

								$final_canvas_parts['output']['atd_team_additional_price'] = $atd_team_additional_price;
								$final_canvas_parts['output']['atd_team_data_recap']       = (array) json_decode( stripslashes_deep( filter_input( INPUT_POST, 'atd_team_data_recap' ) ), true );
							}
							$newly_added_cart_item_key = $this->add_designs_to_cart( $final_canvas_parts, $related_variations );
							array_push( $add_success, $newly_added_cart_item_key );

							if ( $newly_added_cart_item_key && $cart_item_key ) {
								$message = "<div class='atd_notification success f-right'>" . __( 'Item successfully updated.', 'allada-tshirt-designer-for-woocommerce' ) . " <a href='$cart_url'>" . __( 'View Cart', 'allada-tshirt-designer-for-woocommerce' ) . '</a></div>';
							} elseif ( $newly_added_cart_item_key ) {
								$message = "<div class='atd_notification success f-right'>" . __( 'Product successfully added to basket.', 'allada-tshirt-designer-for-woocommerce' ) . " <a href='$cart_url'>View Cart</a></div>";
							} else {
								$message = "<div class='atd_notification failure f-right'>" . __( 'A problem occured while adding the product to the cart. Please try again.', 'allada-tshirt-designer-for-woocommerce' ) . '</div>';
							}
						} else {
							$message = "<div class='atd_notification failure f-right'>" . __( 'A problem occured while generating the output files... Please try again.', 'allada-tshirt-designer-for-woocommerce' ) . '</div>';
						}
					} else {
						$message = "<div class='atd_notification failure f-right'>" . __( "The creation of the directory $generation_path failed. Make sure that the complete path is writeable and try again.", 'allada-tshirt-designer-for-woocommerce' ) . '</div>';
					}
				} else {
					array_push( $add_success, false );
					$message = "<div class='atd_notification failure f-right'>" . __( 'A problem occured while adding the product to the cart. Please try again.', 'allada-tshirt-designer-for-woocommerce' ) . '</div>';
				}
			}
		}
		if ( $newly_added_cart_item_key && in_array( false, $add_success ) ) {
			$message = "<div class='atd_notification success f-right'>" . __( 'An error has occurred. One or more products have not been added to the cart.', 'allada-tshirt-designer-for-woocommerce' ) . " <a href='$cart_url'>" . __( 'View Cart', 'allada-tshirt-designer-for-woocommerce' ) . '</a></div>';
		}
		echo wp_json_encode(
			array(
				'success'     => $newly_added_cart_item_key,
				'message'     => $message,
				'url'         => $cart_url,
				'form_fields' => $data,
			)
		);

		die();
	}

	/**
	 * Get custom button for each element of shop page
	 *
	 * @global type $atd_settings Array of settings.
	 * @param type $html Html output.
	 * @param type $product Product object.
	 * @return type
	 */
	public function atd_get_customize_btn_loop( $html, $product ) {
		global $atd_settings;
		$general_options                   = $atd_settings['atd-general-options'];
		$hide_cart_design_button_shop_page = atd_get_proper_value( $general_options, 'atd-hide-design-btn-shop-pages', 0 );
		$hide_buttons_shop_page            = atd_get_proper_value( $general_options, 'atd-hide-cart-button', 0 );
		$product_id                        = $product->get_id();
		$atd_product                       = new atd_Product( $product_id );
		$product_class                     = get_class( $product );
		if ( 'WC_Product_Simple' === $product_class && $atd_product->is_customizable() ) {
			if ( $hide_cart_design_button_shop_page ) {
				$html .= $atd_product->get_buttons();
			}

			if ( $hide_buttons_shop_page ) {
				$html .= '<script>
                    jQuery(".add_to_cart_button[data-product_id=' . $product_id . ']").hide();
                </script>';
			}
		}
		return $html;
	}

	/**
	 * Get additional price.
	 *
	 * @param type $product_id The product id.
	 * @param type $data The data.
	 * @param type $dimensions The dimensions.
	 * @return type Price.
	 */
	// public function get_additional_price($product_id, $data, $dimensions = array()) {
	// $atd_product = new ATD_Product($product_id);
	// $elements_analysis = $this->extract_priceable_elements($data, $dimensions);
	// $priceable_elements = $elements_analysis[0];
	// Sum of prices per item (cliparts for example)
	// $total_items_price = $elements_analysis[1];
	// $wpc_metas = $atd_product->settings;
	// $pricing_rules = array();
	// if (isset($wpc_metas['pricing-rules'])) {
	// $pricing_rules = $wpc_metas['pricing-rules'];
	// }
	//
	// $tpl_price = 0;
	// if (isset($data['output']['tpl']) && !empty($data['output']['tpl'])) {
	// $tpl_price = wpd_get_template_price($data['output']['tpl']);
	// }
	//
	// $total_additionnal_price = 0;
	// if (is_array($pricing_rules) && !empty($pricing_rules) && is_array($priceable_elements) && !empty($priceable_elements)) {
	// $rule_group = 0;
	// For each rule group
	// foreach ($pricing_rules as $rules_group) {
	// $rule_index = 0;
	// $rules = $rules_group['rules'];
	// $additionnal_price = $rules_group['a_price'];
	// $scope = $rules_group['scope'];
	//
	// $group_results = $this->get_group_results($priceable_elements, $rules);
	// $group_count = $this->get_group_valid_items_count($group_results);
	// If the rules are not valid for this group, we skip the count
	// if (!$group_count) {
	// continue;
	// }
	// if ($scope === 'item') {
	// foreach ($group_results as $key => $value) {
	// if ('i-text' === $key && isset($elements_analysis[0]['i-text'])) {
	//
	// foreach ($elements_analysis[0]['i-text'] as $index => $val) {
	//
	// foreach ($value as $group_result) {
	//
	// $total_additionnal_price += $additionnal_price * $group_result[$index]['nb_attr'];
	// }
	// }
	// } elseif ('text' === $key && isset($elements_analysis[0]['text'])) {
	// foreach ($elements_analysis[0]['text'] as $index => $val) {
	//
	// foreach ($value as $group_result) {
	//
	// $total_additionnal_price += $additionnal_price * $group_result[$index]['nb_attr'];
	// }
	// }
	// } elseif ('image' === $key) {
	// $total_additionnal_price += $additionnal_price * $elements_analysis[0]['image'][0]['img_nb'];
	// } else {
	// $total_additionnal_price += $additionnal_price;
	// }
	// }
	// } elseif ($scope === 'additional-items') {
	// $another_image = false;
	// foreach ($group_results as $value) {
	// foreach ($value[0] as $val) {
	// if (isset($val["type"]) && ("text" === $val["type"] || "i-text" === $val["type"])) {
	// $total_additionnal_price += $additionnal_price * $val['nb_additional_item'];
	// } elseif (isset($val["type"]) && "image" === $val["type"] && !$another_image) {
	// $total_additionnal_price += $additionnal_price * $val['nb_additional_item'];
	// $another_image = true;
	// }
	// }
	// }
	// } else {
	// $total_additionnal_price += $additionnal_price;
	// }
	// }
	// }
	// return $total_additionnal_price + $total_items_price + $tpl_price;
	// }
}
