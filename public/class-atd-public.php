<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.orionorigin.com/
 * @since      1.0.0
 *
 * @package    Atd
 * @subpackage Atd/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Atd
 * @subpackage Atd/public
 * @author     ORION <support@orionorigin.com>
 */
class Atd_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		global $allowed_tags;
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$custom_tags       = apply_filters(
			'atd_custom_tags',
			array(
				'data-id',
				'data-title',
				'data-name',
				'data-size',
				'data-any-color',
				'data-any-size',
				'data-id-color',
				'data-color',
				'data-own-id',
				'data-index',
				'data-url',
				'data-src',
				'data-placement',
				'data-tooltip-title',
				'data-ov',
				'data-ovni',
				'data-opacity',
				'data-update-id',
				'data-img-name',
				'data-group-name',
				'data-groupid',
				'data-price',
				'data-original',
				'data-default-value',
				'data-unit',
				'data-target',
				'data-toggle',
				'tabindex',
				'variation_name',
				'tab-index',
				'aria-hidden',
				'aria-labelledby',
				'atd-related-product-id',
			)
		);

		$allowed_tags = atd_allowed_tags( $custom_tags );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Atd_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Atd_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/atd-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'atd-modal-css', ATD_URL . 'includes/skins/default/assets/css/modal.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Atd_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Atd_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/atd-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( 'atd-modal-js', ATD_URL . 'includes/skins/default/assets/js/modal/modal.min.js', array(), $this->version, false );
	}

	/**
	 * Get start designing button.
	 */
	public function get_start_designing_btn() {
		global $allowed_tags;
		$product_id  = get_the_ID();
		$product     = wc_get_product( $product_id );
		$atd_product = new ATD_Product( $product_id );
		echo wp_kses( $atd_product->get_buttons( true ), $allowed_tags );
	}

	/**
	 * Add query variables.
	 *
	 * @param type $a_vars the array variables.
	 * @return array.
	 */
	public function atd_add_query_vars( $a_vars ) {
		$a_vars[] = 'product_id';
		$a_vars[] = 'tpl';
		$a_vars[] = 'edit';
		$a_vars[] = 'design_index';
		$a_vars[] = 'oid';
		return $a_vars;
	}

	/**
	 * Rewrite rules.
	 *
	 * @global type $atd_settings the setting.
	 * @global type $wp_rewrite the WordPress rewrite.
	 * @param type $param the parameter.
	 */
	public function atd_add_rewrite_rules( $param ) {
		global $atd_settings;
		global $wp_rewrite;
		$options     = $atd_settings['atd-general-options'];
		$atd_page_id = $options['atd_page_id'];
		if ( function_exists( 'icl_object_id' ) ) {
			$atd_page_id = icl_object_id( $atd_page_id, 'page', false, ICL_LANGUAGE_CODE );
		}
		$atd_page = get_post( $atd_page_id );
		if ( is_object( $atd_page ) ) {
			$raw_slug = get_permalink( $atd_page->ID );
			$home_url = home_url( '/' );
			$slug     = str_replace( $home_url, '', $raw_slug );
			// If the slug does not have the trailing slash, we get 404 (ex postname = /%postname%).
			$sep = '';
			if ( '/' !== substr( $slug, -1 ) ) {
				$sep = '/';
			}
			add_rewrite_rule(
					// The regex to match the incoming URL.
				$slug . $sep . 'design' . '/([^/]+)/?$',
				// The resulting internal URL: `index.php` because we still use WordPress
					// `pagename` because we use this WordPress page
					// `designer_slug` because we assign the first captured regex part to this variable.
					'index.php?pagename=' . $slug . '&product_id=$matches[1]',
				// This is a rather specific URL, so we add it to the top of the list
					// Otherwise, the "catch-all" rules at the bottom (for pages and attachments) will "win".
					'top'
			);
			add_rewrite_rule(
					// The regex to match the incoming URL.
				$slug . $sep . 'design' . '/([^/]+)/([^/]+)/?$',
				// The resulting internal URL: `index.php` because we still use WordPress.
					// `pagename` because we use this WordPress page
					// `designer_slug` because we assign the first captured regex part to this variable.
					'index.php?pagename=' . $slug . '&product_id=$matches[1]&tpl=$matches[2]',
				// This is a rather specific URL, so we add it to the top of the list
					// Otherwise, the "catch-all" rules at the bottom (for pages and attachments) will "win".
					'top'
			);
			add_rewrite_rule(
					// The regex to match the incoming URL.
				$slug . $sep . 'edit' . '/([^/]+)/([^/]+)/?$',
				// The resulting internal URL: `index.php` because we still use WordPress.
					// `pagename` because we use this WordPress page.
					// `designer_slug` because we assign the first captured regex part to this variable.
					'index.php?pagename=' . $slug . '&product_id=$matches[1]&edit=$matches[2]',
				// This is a rather specific URL, so we add it to the top of the list.
					// Otherwise, the "catch-all" rules at the bottom (for pages and attachments) will "win".
					'top'
			);
			add_rewrite_rule(
					// The regex to match the incoming URL.
				$slug . $sep . 'ordered-design' . '/([^/]+)/([^/]+)/?$',
				// The resulting internal URL: `index.php` because we still use WordPress.
					// `pagename` because we use this WordPress page.
					// `designer_slug` because we assign the first captured regex part to this variable.
					'index.php?pagename=' . $slug . '&product_id=$matches[1]&oid=$matches[2]',
				// This is a rather specific URL, so we add it to the top of the list.
					// Otherwise, the "catch-all" rules at the bottom (for pages and attachments) will "win".
					'top'
			);

			add_rewrite_rule(
					// The regex to match the incoming URL.
				$slug . $sep . 'saved-design' . '/([^/]+)/([^/]+)/?$',
				// The resulting internal URL: `index.php` because we still use WordPress.
					// `pagename` because we use this WordPress page.
					// `designer_slug` because we assign the first captured regex part to this variable.
					'index.php?pagename=' . $slug . '&product_id=$matches[1]&design_index=$matches[2]',
				// This is a rather specific URL, so we add it to the top of the list.
					// Otherwise, the "catch-all" rules at the bottom (for pages and attachments) will "win".
					'top'
			);
			$wp_rewrite->flush_rules( false );
		}
	}

	/**
	 * Set variable action filters.
	 *
	 * @global type $atd_settings the settings.
	 */
	public function set_variable_action_filters() {
		$woo_version = $this->atd_get_woo_version_number();
		if ( $woo_version < 2.1 ) {
			// Old WC versions.
			add_filter( 'woocommerce_in_cart_product_title', array( $this, 'get_atd_data' ), 10, 3 );
		} else {
			// New WC versions.
			add_filter( 'woocommerce_cart_item_name', array( $this, 'get_atd_data' ), 10, 3 );
		}
		add_filter( 'the_content', array( $this, 'filter_content' ), 99 );
	}

	/**
	 * Get item class.
	 *
	 * @global type $atd_settings The settings.
	 * @param type $classes The classes.
	 * @param type $class The class.
	 * @param type $post_id The post id.
	 * @return type
	 */
	public function get_item_class( $classes, $class, $post_id ) {
		global $atd_settings;
		$general_options  = $atd_settings['atd-general-options'];
		$hide_cart_button = atd_get_proper_value( $general_options, 'atd-hide-cart-button', true );

		if ( in_array( 'product', $classes, true ) ) {
			$atd_product = new ATD_Product( $post_id );
			if ( $atd_product->is_customizable() ) {
				array_push( $classes, 'atd-is-customizable' );
			}
			if ( $hide_cart_button ) {
				array_push( $classes, 'atd-hide-cart-button' );
			}
		}
		return $classes;
	}

	/**
	 * Register the plugin shortcodes
	 */
	public function register_shortcodes() {
		add_shortcode( 'atd-editor', array( $this, 'get_editor_shortcode_handler' ) );
	}

	/**
	 * Get editor shortcode handler.
	 *
	 * @global type $wp_query the WordPress query.
	 * @return type object.
	 */
	public function get_editor_shortcode_handler() {
		global $wp_query;
		if ( ! isset( $wp_query->query_vars['product_id'] ) ) {
			return __( "You're trying to access the customization page whitout a product to customize. This page should only be accessed using one of the customization buttons.", 'allada-tshirt-designer-for-woocommerce' );
		}

		$item_id    = $wp_query->query_vars['product_id'];
		$editor_obj = new ATD_Editor( $item_id );
		return $editor_obj->get_editor();
	}

	/**
	 * Get the woocommerce version number.
	 *
	 * @return int Sversion The version.
	 */
	private function atd_get_woo_version_number() {
		// If get_plugins() isn't available, require it
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Create the plugins folder and file variables
		$plugin_folder = get_plugins( '/' . 'woocommerce' );
		$plugin_file   = 'woocommerce.php';

		// If the plugin version number is set, return it
		if ( isset( $plugin_folder[ $plugin_file ]['Version'] ) ) {
			return $plugin_folder[ $plugin_file ]['Version'];
		} else {
			// Otherwise return null
			return null;
		}
	}

	/**
	 * Filter content.
	 *
	 * @global type $atd_settings the settings.
	 * @global type $wp_query the WordPress query.
	 * @param type $content the content.
	 * @return type
	 */
	public function filter_content( $content ) {
		global $atd_settings, $wp_query;

		$options     = $atd_settings['atd-general-options'];
		$atd_page_id = $options['atd_page_id'];
		if ( function_exists( 'icl_object_id' ) ) {
			$atd_page_id = icl_object_id( $atd_page_id, 'page', false, ICL_LANGUAGE_CODE );
		}
		$current_page_id = get_the_ID();
		if ( ! is_admin() && $atd_page_id == $current_page_id && isset( $wp_query->query_vars['product_id'] ) && ! empty( $wp_query->query_vars['product_id'] ) ) {
			$editor_obj = new ATD_Editor( $wp_query->query_vars['product_id'] );
			$content   .= $editor_obj->get_editor();
		}
		return $content;
	}
	/**
	 * Get ATD data.
	 *
	 * @param type $thumbnail_code The thumbnail code.
	 * @param type $values The values.
	 * @param type $cart_item_key The cart item key.
	 */
	public function get_atd_data( $thumbnail_code, $values, $cart_item_key ) {
		global $allowed_tags;
		$selected_attr = atd_get_variation_attr_name( $values['product_id'] );
		if ( $selected_attr ) {
			$this_color = $values['variation'][ 'attribute_' . $selected_attr['color'] ];
			$this_size  = $values['variation'][ 'attribute_' . $selected_attr['size'] ];
		}

		$product = wc_get_product( $values['product_id'] );

		if ( $product->get_type() == 'variable' ) {
			$variation_id = $values['variation_id'];
		} else {
			$variation_id = $values['product_id'];
		}

		if ( isset( $values['atd_design_pricing_options'] ) && ! empty( $values['atd_design_pricing_options'] ) ) {
			$atd_design_pricing_options_data = ATD_Design::get_design_pricing_options_data( $values['atd_design_pricing_options'] );
			$thumbnail_code                 .= '<br>' . $atd_design_pricing_options_data;
		}

		if ( isset( $values['atd_generated_data'] ) && isset( $values['atd_generated_data']['output'] ) ) {
			$thumbnail_code    .= '<br>';
			$customization_list = $values['atd_generated_data'];
			$modals             = '';
			if ( class_exists( 'Ofb' ) ) {
				if ( isset( $customization_list['output']['form_fields'] ) ) {
					$form_fields = $customization_list['output']['form_fields'];
					foreach ( $form_fields as $key => $value ) {
						if ( ! is_array( $value ) ) {
							$thumbnail_code .= '<p>' . $key . ' : ' . $value . '</p>';
						} else {
							$thumbnail_code .= '<p>' . $key . ' : ';
							foreach ( $value as $data ) {
								$thumbnail_code .= $data . ' ';
							}
							$thumbnail_code .= ' </p>';
						}
					}
				}
			}
			$i = 0;
			foreach ( $customization_list['output']['files'] as $customisation_key => $customization ) {
				$tmp_dir        = $customization_list['output']['working_dir'];
				$generation_url = ATD_ORDER_UPLOAD_URL . "/$tmp_dir/$customisation_key/";
				if ( isset( $customization['preview'] ) ) {
					$image = $generation_url . $customization['preview'];
				} else {
					$image = $generation_url . $customization['image'];
				}
				if ( isset( $customization_list[ $customisation_key ]['original_part_img'] ) ) {
					$original_part_img_url = $customization_list[ $customisation_key ]['original_part_img'];
				} else {
					$original_part_img_url = '';
				}
				$modal_id = $variation_id . '_' . $cart_item_key . "$customisation_key-$i";
				if ( 'output' !== $customisation_key ) {
					// $thumbnail_code .= '<span><a class="button" data-part-name="' . $customisation_key . '" data-variation-id="' . $variation_id . '" data-toggle="o-modal" data-target="#' . $modal_id . '">' . ucfirst($customisation_key) . '</a></span>';
					$thumbnail_code .= '<span><a class="button atd-prev-cart-des" data-part-name="' . $customisation_key . '" data-variation-id="' . $variation_id . '" >' . ucfirst( $customisation_key ) . '</a></span>';
				}
				// $modals .= '<div class="omodal fade atd-modal atd_part" id="' . $modal_id . '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				// <div class="omodal-dialog">
				// <div class="omodal-content">
				// <div class="omodal-header">
				// <button type="button" class="close" data-dismiss="omodal" aria-hidden="true">&times;</button>
				// <h4 class="omodal-title">' . __('Preview', 'allada-tshirt-designer-for-woocommerce') . '</h4>
				// </div>
				// <div class="omodal-body txt-center">
				// <div style="background-image:url(' . $original_part_img_url . ')"><img src="' . $image . '"></div>
				// </div>
				// </div>
				// </div>
				// </div>';
				$modals .= '<div class="atd-preview-box-prev-cart-des" data-part-name="' . $customisation_key . '" data-variation-id="' . $variation_id . '">

                                <div class="atd-preview-title">' . __( 'Preview Cart Designs', 'allada-tshirt-designer-for-woocommerce' ) . '</div>
                    
                                <div class="atd-icon-prev-cart-cross"><i class="fas fa-times"></i></div>
                    
                                <div class="atd-preview-prev-cart-des-item">

                                    <header>' . ucfirst( $customisation_key ) . '</header>

                                    <div class="atd-preview-prev-cart-des-img-container">
                                    
                                        <div class="atd-preview-prev-cart-des-img" style="background-image:url(' . $original_part_img_url . ')">
                                        
                                            <img src="' . $image . '">
                                        
                                        </div>
                                        
                                    </div>
                    
                                </div>
                    
                            </div>
                    
                            <div class="atd-shadow-prev-cart-des" data-part-name="' . $customisation_key . '" data-variation-id="' . $variation_id . '"></div>';
				$i++;
			}
			array_push( atd_retarded_actions::$code, $modals );
			add_action( 'wp_footer', array( 'atd_retarded_actions', 'display_code' ), 10, 1 );

			$atd_product   = new ATD_Product( $variation_id );
			$edit_item_url = $atd_product->get_design_url( false, $cart_item_key, false, false, true );
			if ( isset( $this_color ) && isset( $this_size ) ) {
				$edit_item_url .= '&color=' . $this_color . '&size=' . $this_size;
			}
			$thumbnail_code .= '<a class="button alt" href="' . $edit_item_url . '">' . __( 'Edit', 'allada-tshirt-designer-for-woocommerce' ) . '</a>';
		} elseif ( isset( $values['atd-uploaded-designs'] ) ) {
			$thumbnail_code .= '<br>';
			foreach ( $values['atd-uploaded-designs'] as $custom_design ) {
				$thumbnail_code .= '<span class="atd-custom-design"><a class="button" href=' . $custom_design . '>' . __( 'Custom design', 'allada-tshirt-designer-for-woocommerce' ) . '</a></span>';
			}
		}
		echo wp_kses( $thumbnail_code, $allowed_tags );
	}

	/**
	 * Initialize the plugin sessions
	 */
	function init_sessions() {
		if ( ! session_id() ) {
			session_start();
		}
		if ( ! isset( $_SESSION['atd-data-to-load'] ) ) {
			$_SESSION['atd-data-to-load'] = '';
		}
		$_SESSION['atd_calculated_totals'] = false;
	}

	/**
	 * Handle picture upload.
	 */
	public function handle_picture_upload() {
		if ( ! check_ajax_referer( 'atd-picture-upload-nonce', 'nonce', false ) ) {
			$busted = __( 'Cheating huh?', 'allada-tshirt-designer-for-woocommerce' );
			die( esc_html( $busted ) );
		}

		$generation_path = ATD_TMP_UPLOAD_PATH;
		if ( ! is_dir( $generation_path ) ) {
			wp_mkdir_p( $generation_path );
		}
		$generation_url = ATD_TMP_UPLOAD_URL;
		$file_name      = uniqid();
		$options        = get_option( 'atd-upload-options' );
		$valid_formats  = $options['atd-upl-extensions'];
		if ( ! $valid_formats ) {
			$valid_formats = array( 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg' );
		}
		$name = sanitize_file_name( wp_unslash( $_FILES['userfile']['name'] ) );
		// $size = $_FILES['userfile']['size'];
		if ( isset( $_POST ) && 'POST' === filter_input( INPUT_SESSION, 'REQUEST_METHOD' ) ) {
			if ( strlen( $name ) ) {
				$success    = 0;
				$message    = '';
				$img_url    = '';
				$img_id     = uniqid();
				$path_parts = pathinfo( $name );
				$ext        = strtolower( $path_parts['extension'] );
				if ( in_array( $ext, $valid_formats, true ) && isset( $_FILES['userfile'] ) && isset( $_FILES['userfile']['tmp_name'] ) ) {
					$tmp = sanitize_file_name( wp_unslash( $_FILES['userfile']['tmp_name'] ) );
					if ( move_uploaded_file( $tmp, $generation_path . '/' . $file_name . ".$ext" ) ) {
						$min_width               = $options['atd-min-upload-width'];
						$min_height              = $options['atd-min-upload-height'];
						$valid_formats_for_thumb = array( 'psd', 'eps', 'pdf' );
						if ( $min_width > 0 || $min_height > 0 ) {
							list($width, $height, $type, $attr) = getimagesize( $generation_path . '/' . $file_name . ".$ext" );
							if ( ( $min_width > $width || $min_height > $height ) && $ext != 'svg' ) {
								$success = 0;
								$message = sprintf( __( 'Uploaded file dimensions: %1$spx x %2$spx, minimum required ', 'allada-tshirt-designer-for-woocommerce' ), $width, $height );
								if ( $min_width > 0 && $min_height > 0 ) {
									$message .= __( 'dimensions:', 'allada-tshirt-designer-for-woocommerce' ) . " $min_height" . 'px' . " x $min_height" . 'px';
								} elseif ( $min_width > 0 ) {
									$message .= "width: $min_width" . 'px';
								} elseif ( $min_height > 0 ) {
									$message .= "height: $min_height" . 'px';
								}
							} else {
								$success = 1;

								$message = "<div class='atd-preview-upolad-item atd-fade' data-title='" . $file_name . "' data-upload-id='" . $img_id . "' data-url='" . $generation_url . '/' . $file_name . '.' . $ext . "' data-img-name='" . $file_name . "'><div class='atd-preview-upolad' style='background-image:url(" . $generation_url . '/' . $file_name . '.' . $ext . ")'></div><div class='atd-icon-cross'><i class='fas fa-times'></i></div></div>";
								$img_url = "$generation_url/$file_name.$ext";
							}
						} else {
							$success = 1;
							$message = "<div class='atd-preview-upolad-item atd-fade' data-title='" . $file_name . "' data-upload-id='" . $img_id . "' data-url='" . $generation_url . '/' . $file_name . '.' . $ext . "' data-img-name='" . $file_name . "'><div class='atd-preview-upolad' style='background-image:url(" . $generation_url . '/' . $file_name . '.' . $ext . ")'></div><div class='atd-icon-cross'><i class='fas fa-times'></i></div></div>";
							$img_url = "$generation_url/$file_name.$ext";
						}
						if ( 0 === $success ) {
							unlink( $generation_path . '/' . $file_name . ".$ext" );
						}
					} else {
						$success = 0;
						$message = __( 'An error occured during the upload. Please try again later', 'allada-tshirt-designer-for-woocommerce' );
					}
				} else {
					$success = 0;
					$message = __( 'Incorrect file extension: ' . $ext . '. Allowed extensions: ', 'allada-tshirt-designer-for-woocommerce' ) . implode( ', ', $valid_formats );
				}

				if ( $success === 1 ) {
					$new_atd_upload = array(
						'img_url'  => $img_url,
						'img_id'   => $img_id,
						'img_name' => $name = sanitize_file_name( wp_unslash( $_FILES['userfile']['name'] ) ),
					);
					if ( ! is_user_logged_in() ) {
						if ( null !== filter_input( INPUT_COOKIE, 'atd-upload' ) ) {
							$atd_upload_json = filter_var( json_decode( base64_decode( filter_input( INPUT_COOKIE, 'atd-upload' ) ) ), FILTER_SANITIZE_ENCODED );
							if ( null === $atd_upload_json ) {
								$atd_upload = array();
							} else {
								$atd_upload_json_sanitize = array();
								foreach ( $atd_upload_json as $key => $value ) {
									$atd_upload_array_sanitize ['img_url']  = filter_var( $value->img_url, FILTER_VALIDATE_URL );
									$atd_upload_array_sanitize ['img_id']   = filter_var( $value->img_id, FILTER_SANITIZE_STRING );
									$atd_upload_array_sanitize ['img_name'] = filter_var( $value->img_name, FILTER_SANITIZE_STRING );
									$atd_upload_array_sanitize              = (object) $atd_upload_array_sanitize;
									$atd_upload_json_sanitize[ $key ]       = $atd_upload_array_sanitize;
								}
								$atd_upload = $atd_upload_json_sanitize;
							}
						} else {
							$atd_upload = array();
						}
						array_push( $atd_upload, $new_atd_upload );
						$atd_upload_json = base64_encode( json_encode( $atd_upload ) );
						setcookie( 'atd-upload', $atd_upload_json, time() + 365 * 24 * 3600, '/' );
					} else {
						global $current_user;
						$current_user_id = $current_user->ID;
						$user_uploads    = get_user_meta( $current_user_id, 'atd_saved_uploads' );
						if ( empty( $user_uploads ) ) {
							$user_uploads = array();
						} else {
							$user_uploads = $user_uploads[0];
						}
						$user_uploads[] = $new_atd_upload;
						update_user_meta( $current_user_id, 'atd_saved_uploads', $user_uploads );
					}
				}
				echo wp_json_encode(
					array(
						'success' => $success,
						'message' => $message,
						'img_url' => $img_url,
						'img_id'  => $img_id,
					)
				);
			}
		}
		die();
	}

	/**
	 * Handle delete picture upload.
	 *
	 * @global type $current_user
	 */
	public function handle_delete_picture_upload() {
		$success     = 0;
		$element_key = '';
		$img_id      = filter_input( INPUT_POST, 'img_id' );
		$img_url     = filter_input( INPUT_POST, 'img_url' );
		$img_name    = filter_input( INPUT_POST, 'img_name' );
		if ( ! is_user_logged_in() ) {
			if ( null !== filter_input( INPUT_COOKIE, 'atd-upload' ) ) {
				$atd_upload_json = filter_var( json_decode( base64_decode( filter_input( INPUT_COOKIE, 'atd-upload' ) ) ), FILTER_SANITIZE_ENCODED );
				if ( null === $atd_upload_json ) {
					$atd_upload = array();
				} else {
					$atd_upload_json_sanitize = array();
					foreach ( $atd_upload_json as $key => $value ) {
						$atd_upload_array_sanitize ['img_url']  = filter_var( $value->img_url, FILTER_VALIDATE_URL );
						$atd_upload_array_sanitize ['img_id']   = filter_var( $value->img_id, FILTER_SANITIZE_STRING );
						$atd_upload_array_sanitize ['img_name'] = filter_var( $value->img_name, FILTER_SANITIZE_STRING );
						$atd_upload_array_sanitize              = (object) $atd_upload_array_sanitize;
						$atd_upload_json_sanitize[ $key ]       = $atd_upload_array_sanitize;
					}
					$atd_upload = $atd_upload_json_sanitize;
				}
			} else {
				$atd_upload = array();
			}
			if ( is_array( $atd_upload ) && ! empty( $atd_upload ) ) {
				foreach ( $atd_upload as $key => $value ) {
					if ( $value->img_id === $img_id && $value->img_name === $img_name && $value->img_url === $img_url ) {
						$element_key = $key;
						break;
					}
				}
			}
			if ( ! empty( $element_key ) || 0 === $element_key ) {
				unset( $atd_upload[ $element_key ] );
				// array_splice( $atd_upload, $element_key );
				$atd_upload_json = base64_encode( json_encode( $atd_upload ) );
				setcookie( 'atd-upload', $atd_upload_json, time() + 365 * 24 * 3600, '/' );
				$success = 1;
			} else {
				$success = 0;
			}
		} else {
			global $current_user;
			$current_user_id = $current_user->ID;
			$user_uploads    = get_user_meta( $current_user_id, 'atd_saved_uploads' );
			if ( empty( $user_uploads ) ) {
				$user_uploads = array();
			} else {
				$user_uploads = $user_uploads[0];
			}
			if ( is_array( $user_uploads ) && ! empty( $user_uploads ) ) {
				foreach ( $user_uploads as $key => $value ) {
					if ( $value['img_id'] === $img_id && $value['img_name'] === $img_name && $value['img_url'] === $img_url ) {
						$element_key = $key;
						break;
					}
				}
			}
			if ( ! empty( $element_key ) || 0 === $element_key ) {
				unset( $user_uploads[ $element_key ] );
				// array_splice( $user_uploads, $element_key );
				update_user_meta( $current_user_id, 'atd_saved_uploads', $user_uploads );
				$success = 1;
			} else {
				$success = 0;
			}
		}
		echo wp_json_encode( $success );
		die();
	}

	/**
	 * Move user picture uploads.
	 *
	 * @param type $user_id The user id.
	 */
	public function move_user_picture_uplaods( $user_id ) {
		$user_uploads = array();
		if ( null !== filter_input( INPUT_COOKIE, 'atd-upload' ) ) {
			$atd_upload_json = filter_var( json_decode( base64_decode( filter_input( INPUT_COOKIE, 'atd-upload' ) ) ), FILTER_SANITIZE_ENCODED );
			if ( null === $atd_upload_json ) {
				$atd_upload = array();
			} else {
				$atd_upload_json_sanitize = array();
				foreach ( $atd_upload_json as $key => $value ) {
					$atd_upload_array_sanitize ['img_url']  = filter_var( $value->img_url, FILTER_VALIDATE_URL );
					$atd_upload_array_sanitize ['img_id']   = filter_var( $value->img_id, FILTER_SANITIZE_STRING );
					$atd_upload_array_sanitize ['img_name'] = filter_var( $value->img_name, FILTER_SANITIZE_STRING );
					$atd_upload_array_sanitize              = (object) $atd_upload_array_sanitize;
					$atd_upload_json_sanitize[ $key ]       = $atd_upload_array_sanitize;
				}
				$atd_upload = $atd_upload_json_sanitize;
			}
		} else {
			$atd_upload = array();
		}
		foreach ( $atd_upload as $image ) {
			$user_upload = array();
			if ( isset( $image->img_id ) && ! empty( $image->img_id ) ) {
				$user_upload['img_id'] = $image->img_id;
			}
			if ( isset( $image->img_url ) && ! empty( $image->img_url ) ) {
				$user_upload['img_url'] = $image->img_url;
			}
			if ( isset( $image->img_name ) && ! empty( $image->img_name ) ) {
				$user_upload['img_name'] = $image->img_name;
			}

			array_push( $user_uploads, $user_upload );
		}
		add_user_meta( $user_id, 'atd_saved_uploads', $user_uploads );
	}

	/**
	 * Store variation attributes.
	 */
	public function atd_store_variation_attributes() {
		if ( 2 !== session_status() ) {
			session_start();
		}

		$variations = filter_input( INPUT_POST, 'data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$transient  = uniqid( 'atd-' );
		set_transient( $transient, $variations, HOUR_IN_SECONDS );
		$_SESSION['atd_key'] = $transient;
		if ( 2 === session_status() ) {
			session_write_close();
		}

		$atd_product = new ATD_Product( $variations['product_id'] );
		$url         = $atd_product->get_design_url();
		echo wp_json_encode(
			array(
				'url' => $url,
			)
		);
		die;
	}

	/**
	 * Get and send custom product by ajax
	 */
	public function display_related_custom_products() {
		$product_id = filter_input( INPUT_POST, 'product_id' );
		if ( isset( $product_id ) ) {
			$dflt_variation_id = intval( filter_input( INPUT_POST, 'variation_id' ) );
			$product_id        = intval( $product_id );
			$result_data       = atd_get_related_custom_product();

			$active_product_meta_value = get_post_meta( $product_id, 'atd-metas', true );
			// $active_product_first_key = array_key_first($active_product_meta_value);
			$dflt_image_link = $this->get_variation_image_link( $dflt_variation_id );

			?>
				<div class="atd-preview-box-add-cart-card" data-id="<?php echo esc_attr( $product_id ); ?>">

					<div class="atd-preview-box-add-cart-img-inner">

						<img src="
						<?php
						if ( empty( $dflt_image_link ) ) {
							echo esc_attr( wc_placeholder_img_src() );
						} else {
							echo esc_attr( $dflt_image_link );}
						?>
						" class="atd-preview-box-add-cart-img" alt="<?php echo esc_attr( get_the_title( $product_id ), 'allada-tshirt-designer-for-woocommerce' ); ?>">

					</div>

					<div class="atd-preview-box-add-cart-card-body">

					<h4 class="atd-cart-product-title"><?php echo esc_attr( get_the_title( $product_id ), 'allada-tshirt-designer-for-woocommerce' ); ?></h4>

						<button type="button" class="atd-btn-cart-modal"><?php echo esc_attr( 'Add another colors', 'allada-tshirt-designer-for-woocommerce' ); ?></button>

					</div>

				</div>
			<?php

			foreach ( $result_data['data'] as $key => $value ) {

				$product_data = wc_get_product( $value->ID );

				if ( $product_data->is_in_stock() && $product_data->get_type() == 'variable' ) {
					$related_custom_product_details = atd_get_related_custom_product_details( $value->ID );
					if ( $related_custom_product_details ) {
						$first_variation_key   = array_key_first( $related_custom_product_details['variation_id'] );
						$first_variation_id    = $related_custom_product_details['variation_id'][ $first_variation_key ];
						$first_variation_image = $this->get_variation_image_link( $first_variation_id );
						$meta_value            = get_post_meta( $value->ID, 'atd-metas', true );
						if ( ! empty( $meta_value ) && is_array( $meta_value ) ) {

							$active_config = intval( $active_product_meta_value[ intval( $dflt_variation_id ) ]['config-id'] );
							if ( $this->atd_verif_if_as_active_configuration( $meta_value, $active_config ) && intval( $value->ID ) !== $product_id ) {

								$image_link = wp_get_attachment_image_src( get_post_thumbnail_id( intval( $value->ID ) ), 'thumbnail' );

								?>
									<div class="atd-preview-box-add-cart-card" data-id="<?php echo esc_attr( $value->ID ); ?>">

										<div class="atd-preview-box-add-cart-img-inner">

											<img src="
											<?php
											if ( ! empty( $first_variation_image ) ) {
												echo esc_attr( $first_variation_image );
											} elseif ( ! empty( $image_link ) ) {
												echo esc_attr( $image_link[0] );
											} else {
													echo esc_attr( wc_placeholder_img_src() );
											}

											?>
											" class="atd-preview-box-add-cart-img" alt="<?php echo esc_attr( get_the_title( $value->ID ) ); ?>">
										</div>

										<div class="atd-preview-box-add-cart-card-body">

											<h4 class="atd-cart-product-title"><?php echo esc_html__( get_the_title( $value->ID ), 'allada-tshirt-designer-for-woocommerce' ); ?></h4>

											<button type="button" class="atd-btn-cart-modal"><?php echo esc_html__( 'Add this product', 'allada-tshirt-designer-for-woocommerce' ); ?></button>

										</div>

									</div>
								 <?php
							}
						}
					}
				}
			}
			die();
		}
	}


	/**
	 * Check if configuration is define.
	 *
	 * @param array $meta The meta.
	 * @param int   $config_id The config id.
	 */
	public function atd_verif_if_as_active_configuration( array $meta, $config_id ) {
		foreach ( $meta as $key => $value ) {
			if ( isset( $value['config-id'] ) && '' !== $value['config-id'] && intval( $config_id ) === intval( $value['config-id'] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get and send custom product details by ajax.
	 */
	public function display_related_custom_product_details() {
		$product_id = filter_input( INPUT_POST, 'product_id' );
		if ( isset( $product_id ) ) {

			$product_id = intval( $product_id );

			$dflt_product_id = intval( filter_input( INPUT_POST, 'dflt_product_id' ) );
			// $dflt_variation_id= $_POST["dflt_variation_id"];
			$dflt_variation_id              = filter_input( INPUT_POST, 'dflt_variation_id' );
			$related_custom_product_details = atd_get_related_custom_product_details( $product_id );
			$dflt_image_link                = $this->get_variation_image_link( $dflt_variation_id );

			$selected_attr   = atd_get_variation_attr_name( $product_id );
			$attribute       = wc_get_product_variation_attributes( $dflt_variation_id );
			$variation_color = $attribute[ 'attribute_' . $selected_attr['color'] ];
			$variation_size  = $attribute[ 'attribute_' . $selected_attr['size'] ];

			$first_variation_key   = array_key_first( $related_custom_product_details['variation_id'] );
			$first_variation_id    = $related_custom_product_details['variation_id'][ $first_variation_key ];
			$first_variation_image = $this->get_variation_image_link( $first_variation_id );

			// $first_variation_attribute= wc_get_product_variation_attributes($first_variation_id);
			$first_variation_variation_color = $related_custom_product_details['variation_color'][ $first_variation_key ];
			$first_variation_variation_size  = $related_custom_product_details['variation_size'][ $first_variation_key ];

			if ( $dflt_product_id == $product_id ) {
				$default = true;
			} else {
				$default = false;
			}
			if ( is_array( $related_custom_product_details ) ) {
				$compt                 = 0;
				$meta_value            = get_post_meta( $product_id, 'atd-metas', true );
				$image_link            = wp_get_attachment_image_src( get_post_thumbnail_id( intval( $product_id ) ), 'thumbnail' );
				$global_variation_data = array(
					'product_id'            => $product_id,
					'product_title'         => get_the_title( $product_id ),
					'variation_id'          => $related_custom_product_details['variation_id'],
					'variation_sizes'       => $related_custom_product_details['variation_sizes'],
					'variation_color'       => $related_custom_product_details['variation_color'],
					'variation_combinaison' => $related_custom_product_details['variation_combinaison'],
				);

				ob_start();
				?>
						
					<div class="atd-modal-product-details">
						<div class="atd-preview-box-add-cart-img-inner">
							<?php
							if ( $default ) {
								?>
										<img src="
										<?php
										if ( empty( $dflt_image_link ) ) {
											echo esc_attr( wc_placeholder_img_src() );
										} else {
											echo esc_attr( $dflt_image_link );
										}
										?>
										" alt="<?php echo get_the_title( $product_id ); ?>" class="atd-preview-box-add-cart-img">
									<?php
									$image_link = $dflt_image_link;
							} else {
								?>
										<img src="
										<?php
										if ( empty( $first_variation_image ) ) {
											echo esc_attr( wc_placeholder_img_src() );
										} else {
											echo esc_attr( $first_variation_image );}
										?>
										" alt="<?php echo get_the_title( $product_id ); ?>" class="atd-preview-box-add-cart-img">
									<?php
							}

							?>
						</div>
						<h4 class="atd-cart-product-details-title"><?php echo esc_html__( get_the_title( $product_id ), 'allada-tshirt-designer-for-woocommerce' ); ?></h4>

						<p class="atd-cart-product-details-desc">
							<?php echo esc_html_e( $related_custom_product_details['short_description'], 'allada-tshirt-designer-for-woocommerce' ); ?>
						</p>

					</div>


					<div class="atd-modal-product-details">

						<h3 class="atd-product-question-text"><?php echo esc_html__( 'What color product would you like to add?', 'allada-tshirt-designer-for-woocommerce' ); ?></h3>

						<div class="atd-product-question-color-text">

							<span class="atd-product-select-color"><?php echo esc_html__( 'Selected color:&nbsp;', 'allada-tshirt-designer-for-woocommerce' ); ?></span>

							<div class="atd-img-swatch ">
								<?php
								if ( $default ) {
									?>
											<img src="
											<?php
											if ( empty( $dflt_image_link ) ) {
												echo esc_attr( wc_placeholder_img_src() );
											} else {
												echo esc_attr( $dflt_image_link );}
											?>
											" alt="<?php echo esc_attr( get_the_title( $product_id ) ); ?>" class="atd-preview-box-add-cart-img" data-src="
									<?php
									if ( empty( $dflt_image_link ) ) {
										echo esc_attr( wc_placeholder_img_src() );
									} else {
										echo esc_attr( $dflt_image_link );}
									?>
" data-id="<?php echo esc_attr( $dflt_variation_id ); ?>">
										<?php
								} else {
									?>
											<img src="
											<?php
											if ( empty( $first_variation_image ) ) {
												echo esc_attr( wc_placeholder_img_src() );
											} else {
												echo esc_attr( $first_variation_image );}
											?>
											" alt="<?php echo esc_attr( get_the_title( $product_id ) ); ?>" class="atd-preview-box-add-cart-img" data-src="
									<?php
									if ( empty( $first_variation_image ) ) {
										echo esc_attr( wc_placeholder_img_src() );
									} else {
										echo esc_attr( $first_variation_image );}
									?>
" data-id="<?php echo esc_attr( $first_variation_id ); ?>">
										<?php
								}
								?>
							</div>
							
							<span class="atd-product-variation-text" old-color="
							<?php
							if ( $default ) {
								echo esc_attr( $variation_color, 'allada-tshirt-designer-for-woocommerce' );
							} else {
								echo esc_attr( $first_variation_variation_color, 'allada-tshirt-designer-for-woocommerce' );}
							?>
							" data-name="<?php echo esc_attr( get_the_title( $product_id ) ); ?>" product-data-id="<?php echo esc_attr( $product_id ); ?>">
						  <?php
							if ( $default ) {
								echo esc_html__( $variation_color, 'allada-tshirt-designer-for-woocommerce' );
							} else {
								echo esc_html__( $first_variation_variation_color, 'allada-tshirt-designer-for-woocommerce' );}
							?>
</span>
						</div>
						
						<div class="atd-color-choices">
							
							<?php
									$part_data_bg = atd_get_part_image( $dflt_product_id, $dflt_variation_id, 'bg-inc' );

									$part_data_icon = atd_get_part_image( $dflt_product_id, $dflt_variation_id, 'icon' );

									$used_color_temp = array();

							if ( $default ) {
								// $variation_color=$_POST["active_color"];
								$variation_color = filter_input( INPUT_POST, 'active_color' );
								$variation_size  = filter_input( INPUT_POST, 'active_size' );
								// $variation_size=$_POST["active_size"];
								array_push( $used_color_temp, $variation_color );

								if ( empty( $dflt_image_link ) ) {
									?>
												<div class="atd-color-choice-item" atd-variation-id="<?php echo esc_attr( $dflt_variation_id ); ?>" data-title="<?php echo esc_attr( $variation_color ); ?>"  
												atd-id-color="<?php echo esc_attr( $variation_color ) . esc_attr( $dflt_variation_id ); ?>" >
													<img src="<?php echo esc_attr( wc_placeholder_img_src() ); ?>" alt="<?php echo esc_attr__( get_the_title( $dflt_product_id ), 'allada-tshirt-designer-for-woocommerce' ); ?>" 
													class="atd-preview-box-add-cart-img" data-src="<?php echo esc_attr( wc_placeholder_img_src() ); ?>" data-id="<?php echo esc_attr( $dflt_variation_id ); ?>" 
													data-size="<?php echo esc_attr( $variation_size ); ?>" data-own-id="<?php echo esc_attr( $product_id ); ?>">
												</div>
											<?php
								} else {
									?>
												<div class="atd-color-choice-item" atd-variation-id="<?php echo esc_attr( $dflt_variation_id ); ?>" data-title="<?php echo esc_attr( $variation_color ); ?>" atd-id-color="<?php echo esc_attr( $variation_color ) . esc_attr( $dflt_variation_id ); ?>" >
													<img src="<?php echo esc_attr( $dflt_image_link ); ?>" alt="<?php echo esc_attr( get_the_title( $dflt_product_id ), 'allada-tshirt-designer-for-woocommerce' ); ?>" 
													class="atd-preview-box-add-cart-img" data-src="<?php echo esc_attr( $dflt_image_link ); ?>" data-id="<?php echo esc_attr( $dflt_variation_id ); ?>" data-size="<?php echo esc_attr( $variation_size ); ?>" data-own-id="<?php echo esc_attr( $dflt_product_id ); ?>">
												</div>
											<?php
								}
							}

							foreach ( $part_data_bg as $xkey => $img_url ) {
								if ( $img_url != '' ) {
									$img_url = atd_o_get_proper_image_url( $img_url );
								}

								?>
											<span atd-variation-id="<?php echo esc_attr( $dflt_variation_id ); ?>" data-title="<?php echo esc_attr( $xkey ) . '_' . esc_attr( $dflt_variation_id ); ?>" style="display:none;">
												<img src="<?php echo esc_attr( $img_url ); ?>">
											</span>
										<?php
							}

							foreach ( $part_data_icon as $xkey => $img_url ) {
								if ( $img_url != '' ) {
									$img_url = atd_o_get_proper_image_url( $img_url );
								}

								?>
											<span atd-variation-id="<?php echo esc_attr( $dflt_variation_id ); ?>" data-title="<?php echo 'icon_' . esc_attr( $xkey ) . '_' . esc_attr( $dflt_variation_id ); ?>" style="display:none;">
												<img src="<?php echo esc_attr( $img_url ); ?>">
											</span>
										<?php
							}

							foreach ( $related_custom_product_details['variation_id'] as $key => $value ) {
								$image_url                              = $this->get_variation_image_link( $value );
								list($variation_color, $variation_size) = explode( ' / ', $related_custom_product_details['variation_combinaison'][ $key ] );
								$active_color                           = filter_input( INPUT_POST, 'active_color' );
								if ( $default ) {
									array_push( $used_color_temp, $active_color );
								}
								if ( ! in_array( $variation_color, $used_color_temp, true ) && '' !== $variation_color ) {
									array_push( $used_color_temp, $variation_color );
									if ( empty( $image_url ) ) {
										?>
													<div class="atd-color-choice-item 
													<?php
													if ( 0 === $key && $product_id !== $dflt_product_id ) {
														echo 'isActive'; }
													?>
													" 
													atd-variation-id="<?php echo esc_attr( $value ); ?>" data-title="<?php echo esc_attr( $variation_color ); ?>"  atd-id-color="<?php echo esc_attr( $variation_color ) . esc_attr( $value ); ?>" >
													<img src="<?php echo esc_attr( wc_placeholder_img_src() ); ?>" alt="<?php echo esc_attr( get_the_title( $value ), 'allada-tshirt-designer-for-woocommerce' ); ?>" class="atd-preview-box-add-cart-img" 
														data-src="<?php echo esc_attr( wc_placeholder_img_src() ); ?>" data-id="<?php echo esc_attr( $value ); ?>" data-size="<?php echo esc_attr( $variation_size ); ?>" data-own-id="<?php echo esc_attr( $product_id ); ?>">
													</div>
												<?php
									} else {
										?>
													<div class="atd-color-choice-item 
													<?php
													if ( 0 === $key && $product_id !== $dflt_product_id ) {
														echo 'isActive'; }
													?>
													" atd-variation-id="<?php echo esc_attr( $value ); ?>" 
													data-title="<?php echo esc_attr( $variation_color ); ?>" atd-id-color="<?php echo esc_attr( $variation_color ) . esc_attr( $value ); ?>" >
														<img src="<?php echo esc_attr( $image_url ); ?>" alt="<?php echo esc_attr( get_the_title( $value ), 'allada-tshirt-designer-for-woocommerce' ); ?>" class="atd-preview-box-add-cart-img" 
														data-src="<?php echo esc_attr( $image_url ); ?>" data-id="<?php echo esc_attr( $value ); ?>" data-size="<?php echo esc_attr( $variation_size ); ?>" data-own-id="<?php echo esc_attr( $product_id ); ?>">
													</div>
												<?php
									}

									$part_data_bg = atd_get_part_image( $product_id, $value, 'bg-inc' );

									$part_data_icon = atd_get_part_image( $product_id, $value, 'icon' );

									foreach ( $part_data_bg as $xkey => $img_url ) {
										if ( '' !== $img_url ) {
											$img_url = atd_o_get_proper_image_url( $img_url );
										}

										?>
													<span atd-variation-id="<?php echo esc_attr( $value ); ?>" data-title="<?php echo esc_attr( $xkey ) . '_' . esc_attr( $value ); ?>" style="display:none;">
														<img src="<?php echo esc_attr( $img_url ); ?>">
													</span>
												<?php
									}

									foreach ( $part_data_icon as $xkey => $img_url ) {
										if ( '' !== $img_url ) {
											$img_url = atd_o_get_proper_image_url( $img_url );
										}

										?>
													<span atd-variation-id="<?php echo esc_attr( $value ); ?>" data-title="<?php echo 'icon_' . esc_attr( $xkey ) . '_' . esc_attr( $value ); ?>" style="display:none;">
														<img src="<?php echo esc_attr( $img_url ); ?>">
													</span>
												<?php
									}
								}
							}
							?>

						</div>

						<div class="atd-add-product-notices">

							<div class="atd-color-choice-item-notice">

								<div class="atd-color-choice-item-notice-selected"></div>

								<span class="atd-color-choice-item-notice-text"><?php echo esc_html__( 'Denotes a color already in your order.', 'allada-tshirt-designer-for-woocommerce' ); ?></span>

							</div>

						</div>

						<div class="atd-empty-btn">

							<button type="button" 
							<?php
							if ( $default ) {
								echo 'disabled';}
							?>
							 class="atd-btn-product-add" data-id="<?php echo esc_attr( $product_id ); ?>" data-own-id="<?php echo esc_attr( $dflt_variation_id ); ?>"><?php echo esc_html__( 'Add Product', 'allada-tshirt-designer-for-woocommerce' ); ?></button>

						</div>

					</div>
					
				<?php
				$content = ob_get_clean();
				$result  = array(
					'content' => $content,
					'data'    => $global_variation_data,
					'success' => true,
				);

				echo wp_json_encode( $result );
			} else {
				echo wp_json_encode(
					array(
						'success' => false,
						'content' => __( 'An error has occurred', 'allada-tshirt-designer-for-woocommerce' ),
					)
				);
			}
		}
		die();
	}

	/**
	 * Récupère les informations sur la variarion par défaut.
	 */
	public function get_default_product_variation_details() {
		$product_id = filter_input( INPUT_POST, 'product_id' );
		if ( isset( $product_id ) ) {
			$details = atd_get_related_custom_product_details( $product_id );
			array_push( $details, get_the_title( $product_id ) );
			echo wp_json_encode( $details );
			die();
		}
	}


	/**
	 * Get variation image url.
	 *
	 * @param int $variation_id The product variation id.
	 * @return  string
	 */
	public function get_variation_image_link( $variation_id ) {
		$variation = new WC_Product_Variation( $variation_id );
		$img_id    = $variation->get_image_id( $variation_id );
		$image_url = wp_get_attachment_image_src( $img_id, 'thumbnail' );
		return $image_url[0];
	}

}
