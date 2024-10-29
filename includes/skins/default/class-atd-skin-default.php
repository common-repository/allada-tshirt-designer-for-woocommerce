<?php
/**
 * Default skin class.
 *
 * This class defines all code necessary for default skin.
 *
 * @since      1.0
 * @package    Atd
 * @subpackage Atd/includes
 * @author     ORION <support@orionorigin.com>
 */
class ATD_Skin_Default {

	/**
	 * The editor.
	 *
	 * @var type the editor.
	 */
	public $editor;

	/**
	 * The metta.
	 *
	 * @var type the meta.
	 */
	public $atd_metas;

	/**
	 * The constructor.
	 *
	 * @param type $editor_obj the editor object.
	 * @param type $atd_metas the meta.
	 */
	public function __construct( $editor_obj, $atd_metas ) {
		if ( $editor_obj ) {
			$this->editor    = $editor_obj;
			$this->atd_metas = $atd_metas;
		}
	}

	/**
	 * Display skin.
	 */
	public function display() {
		global $atd_settings;
		global $allowed_tags;
		$custom_tags  = apply_filters(
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
		$atd_settings = apply_filters( 'atd_global_settings', $atd_settings );

		$root_product_id = $this->editor->atd_product->root_product_id;
		$product         = wc_get_product( $root_product_id );
		$set_attr_name   = atd_get_variation_attr_name( $root_product_id );

		if ( 'variable' === $product->get_type() && ! $set_attr_name ) {
			$cnd_is_valid = true;
		} else {
			$cnd_is_valid = false;
		}

		ob_start();

		$this->register_styles();
		$this->register_scripts();

		$uploads_options = atd_get_proper_value( $atd_settings, 'atd-upload-options', array() );
		?>

		<div class="atd-container <?php echo ( $cnd_is_valid ) ? 'isHidden' : ''; ?> " >

			<!-- First Section Begin -->

			<?php

			/** Function Call Content First Section */
			echo wp_kses( $this->get_first_section_content(), $allowed_tags );
			?>

			<!-- First Section End -->

			<!-- Second Section Begin -->

			<?php
			/** Function Call Content Second Section */
			echo wp_kses( $this->get_second_section_content( $uploads_options ), $allowed_tags );
			?>

			<!-- Second Section End -->

			<!-- Third Section Begin -->

			<?php
			/** Function Call Content Third Section */
			echo wp_kses( $this->get_third_section_content(), $allowed_tags );
			?>

			<!-- Third Section End -->

			<!-- Previex Box Team Begin -->

			<?php
			/** Function Call Content Preview Modal Team */
			echo wp_kses( $this->get_preview_modal_team(), $allowed_tags );
			?>

			<!-- Previex Box Team End -->

			<!-- Previex Box Team Begin -->

			<?php
			/** Function Call Content Preview Modal Save */
			echo wp_kses( $this->get_preview_modal_save(), $allowed_tags );
			?>

			<!-- Previex Box Team End -->

			<!-- Preview Box Preview Design Begin -->

			<?php
			/** Function Call Content Preview Modal Design */
			echo wp_kses( $this->get_preview_modal_design(), $allowed_tags );
			?>

			<!-- Preview Box Preview Design End -->

			<!-- Preview Box Save Design Begin -->

			<?php
			/** Function Call Content Preview Modal Saved */
			echo wp_kses( $this->get_preview_modal_saved(), $allowed_tags );
			?>

			<!-- Preview Box Save Design End -->

			<!-- Preview Box Add Product Information Design Begin -->

			<?php
			/** Function Call Content Preview Modal Add Product Information */
			echo wp_kses( $this->get_preview_modal_add_product_information(), $allowed_tags );
			?>

			<!-- Preview Box Add Product Information Design End -->

			<!-- Preview Box Team Information Design Begin -->

			<?php
			/** Function Call Content Preview Modal Team Information */
			echo wp_kses( $this->get_preview_modal_team_information(), $allowed_tags );
			?>

			<!-- Preview Box Team Information Design End -->

			<!-- Preview Box Debug Begin -->

			<?php
			/** Function Call Content Preview Modal Debug */
			echo wp_kses( $this->get_preview_modal_debug(), $allowed_tags );
			?>

			<!-- Preview Box Debug End -->

		</div>

		<!-- Section Bottom Add To Cart Begin -->


				<!--  Add To Cart Bottom Section Begin -->

				<?php
				/** Function Call Content Bottom Add To Cart */

				if ( 'variable' === $product->get_type() ) {

					if ( ! $set_attr_name ) {
						?>
								<ul>
									<li><?php echo esc_html__( 'Please defined size and color name.', 'allada-tshirt-designer-for-woocommerce' ); ?></li>
									<li><?php echo esc_html__( 'Set color and size name : Admin -> Products -> T-shirt configuration', 'allada-tshirt-designer-for-woocommerce' ); ?></li>
								</ul>
							<?php

					} else {
						?>
							 
							<div class="atd-container-add-cart" atd-related-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
								<?php

								echo wp_kses( $this->get_bottom_add_to_cart_content(), $allowed_tags );

								/** Function Call Content Preview Modal Add Product */
								echo wp_kses( $this->get_preview_modal_add_product(), $allowed_tags );

								/** Function Call Content Preview Modal Quantity */
								echo wp_kses( $this->get_preview_modal_quantity(), $allowed_tags );

								?>
							</div>
							<?php
					}
				}
				?>

		<!-- Section Bottom Add To Cart End -->

		
		<?php
		echo wp_kses( $this->get_preview_modal(), $allowed_tags );
		$output = ob_get_clean();
		return $output;
	}


	/**
	 * Get first section content.
	 *
	 * @return Sting $output The output.
	 */
	private function get_first_section_content() {
		ob_start();
		?>

		<div class="atd-section atd-tab-tools">

			<div class="atd-menu">

				<li class="atd-tab-item active" data-title="text">

					<div class="atd-icon"><i class="icon-261"></i></div>

					<span><?php echo esc_html_e( 'Add Text', 'allada-tshirt-designer-for-woocommerce' ); ?></span>

				</li>

				<li class="atd-tab-item" data-title="clippart">

					<div class="atd-icon"><i class="icon-1143"></i></div>

					<span><?php echo esc_html_e( 'Add clippart', 'allada-tshirt-designer-for-woocommerce' ); ?></span>

				</li>

				<li class="atd-tab-item" data-title="upload">

					<div class="atd-icon"><i class="icon-1197"></i></div>

					<span><?php echo esc_html_e( 'Upload art', 'allada-tshirt-designer-for-woocommerce' ); ?></span>

				</li>

				<?php

				$product               = wc_get_product( $this->editor->atd_product->root_product_id );
				$get_product_team_data = $this->get_product_team_data();

				if ( 'variable' === $product->get_type() && 'yes' === $get_product_team_data['enable-team'] ) {
					?>
					<li class="atd-tab-item" data-title="team">

						<div class="atd-icon"><i class="atd-icon-01-01"></i></div>

						<span><?php echo esc_html_e( 'Team', 'allada-tshirt-designer-for-woocommerce' ); ?></span>

					</li>
					<?php
				}
				?>

				<li class="atd-tab-item" data-title="design">

					<div class="atd-icon"><i class="icon-342"></i></div>

					<span><?php echo esc_html_e( 'My designs', 'allada-tshirt-designer-for-woocommerce' ); ?></span>

				</li>

				<?php
				if ( 'simple' === $product->get_type() ) {
					?>
						<li class="atd-tab-item  " data-title="cart"  >

							<div class="atd-icon"><i class="icon-1325"></i></div>

							<span><?php echo esc_html_e( 'Cart', 'allada-tshirt-designer-for-woocommerce' ); ?></span>

						</li>
					<?php
				}
				?>

			</div>

		</div>

		<?php
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Get Second section content.
	 *
	 * @param array $uploads_options The uploads options.
	 * @return string $output The output.
	 */
	private function get_second_section_content( $uploads_options ) {
		global $allowed_tags;
		ob_start();
		?>

		<div class="atd-section atd-tab-tools-container">

			<!-- Begin Section Text Designs -->

			<div class="atd-tab-tools-content active" data-title="text">

				<?php
				/** Function Call Content Text */
				echo wp_kses( $this->get_text_tools(), $allowed_tags );
				?>
				 

			</div>

			<!-- End Section Text Designs -->

			<!-- Begin Section Clippart Designs -->

			<div class="atd-tab-tools-content" data-title="clippart">

				<?php
				/** Function Call Content Clippart */
				echo wp_kses( $this->get_cliparts_tools(), $allowed_tags );
				?>

			</div>

			<!-- End Section Clippart Designs -->

			<!-- Begin Section Upload Designs -->

			<div class="atd-tab-tools-content" data-title="upload">

				<?php
				/** Function Call Content Upload */
				echo wp_kses( $this->get_uploads_tools( $uploads_options ), $allowed_tags );
				?>

			</div>

			<!-- End Section Upload Designs -->

			<!-- Begin Section Team Designs -->

			<div class="atd-tab-tools-content" data-title="team">

				<?php
				/** Function Call Content Team */
				$product               = wc_get_product( $this->editor->atd_product->root_product_id );
				$get_product_team_data = $this->get_product_team_data();

				if ( 'variable' === $product->get_type() && 'yes' === $get_product_team_data['enable-team'] ) {
					echo wp_kses( $this->get_team_tools(), $allowed_tags );
				}
				?>

			</div>

			<!-- End Section Team Designs -->

			<!-- Begin Section Design -->

			<div class="atd-tab-tools-content" data-title="design">

				<?php
				/** Function Call Content Design */
				echo wp_kses( $this->get_design_tools(), $allowed_tags );
				?>

			</div>

			<!-- End Section Design -->

			<!-- Begin Section Cart Designs -->

			<div class="atd-tab-tools-content" data-title="cart">

				<?php
				/** Function Call Content Cart */
				echo wp_kses( $this->get_cart_tools(), $allowed_tags );
				?>

			</div>

			<!-- End Section Cart Designs -->

		</div>

		<?php
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Get Third section content.
	 *
	 * @return Sting $output The output.
	 */
	private function get_third_section_content() {
		global $allowed_tags;
		ob_start();
		$is_icon_image = $this->check_icon_image_part();
		?>

		<div class="atd-section atd-canvas-box <?php echo ( $is_icon_image ) ? 'atd-with-img' : ''; ?>" id="atd-canvas-box">

			<div class="atd-canvas-container">

				<div class="atd-canvas-item atd-column-left">

					<?php
					/** Function Call Content Toolbar */
					echo wp_kses( $this->get_toolbar(), $allowed_tags );
					?>

				</div>

				<!-- Content Switch Between Back and Front Begin -->

				<div id="atd-editor-container" class="atd-canvas-item atd-column-center">

					<?php
					/** Function Call Content Canvas Parts */
					echo wp_kses( $this->get_canvas_parts(), $allowed_tags );
					?>

				</div>

				<!-- Content Switch Between Back and Front End -->

				<div class="atd-canvas-item atd-column-right">

					<?php
					/** Function Call Content Toolbar Right */
					echo wp_kses( $this->get_output_toolbar(), $allowed_tags );
					?>

				</div>

			</div>

			<!-- Navigation Switch Between Back and Front Begin -->

			<?php
			/** Function Call Content Parts */
			echo wp_kses( $this->get_parts(), $allowed_tags );
			?>

			<!-- Navigation Switch Between Back and Front End -->

		</div>

		<?php
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Get Bottom Add To Cart content.
	 *
	 * @return Sting $output The output.
	 */
	private function get_bottom_add_to_cart_content() {

		$root_variation_id = $this->editor->atd_product->variation_id;
		$root_product_id   = $this->editor->atd_product->root_product_id;

		$selected_attr = atd_get_variation_attr_name( $root_product_id );
		// $attribute= wc_get_product_variation_attributes($root_variation_id);
		// $variation_color=$attribute["attribute_".$selected_attr["color"]];
		// $variation_size=$attribute["attribute_".$selected_attr["size"]];

		$variation           = new WC_Product_Variation( $root_variation_id );
		$img_id              = $variation->get_image_id( $root_variation_id );
		$variation_image_url = wp_get_attachment_image_src( $img_id, 'thumbnail' );
		if ( isset( $variation_image_url[0] ) ) {
			$variation_image_url = $variation_image_url[0];
		}

		if ( empty( $variation_image_url ) ) {
			$variation_image_url = wc_placeholder_img_src();
		}

		$meta_value = get_post_meta( $root_product_id, 'atd-metas', true );

		$get_color = atd_get_attributes_slug( $root_product_id, $selected_attr['color'], filter_input( INPUT_GET, 'color' ) );
		$get_size  = atd_get_attributes_slug( $root_product_id, $selected_attr['size'], filter_input( INPUT_GET, 'size' ) );
		$qty       = filter_input( INPUT_GET, 'custom_qty' );

		if ( isset( $qty ) || empty( $qty ) ) {
			$qty = 1;
		}

		ob_start();
		?>

		<div class="atd-container-add-cart-wrap" data-any-color="<?php echo esc_attr( $get_color ); ?>" data-any-size="<?php echo esc_attr( $get_size ); ?>" data-id="<?php echo esc_attr( $root_variation_id ); ?>"
		custom-qty="<?php echo esc_attr( $qty ); ?>">
			
			<div class="atd-container-add-cart-item">
			
				<div class="atd-btn-box">

					<button type="button" class="atd-btn-product"><i class="atd-icon-btn-cart fas fa-plus-circle" aria-hidden="true"></i> <?php echo esc_html__( 'Add Products', 'allada-tshirt-designer-for-woocommerce' ); ?></button>

				</div>

			</div>

			<div class="atd-container-add-cart-item">

				<div class="atd-container-add-cart-item-carousel owl-carousel">
			
					<div class="atd-add-cart-item-card wb-isActive" data-id="<?php echo esc_attr( $root_variation_id ); ?>" data-color="<?php echo esc_attr( $get_color ); ?>" 
					data-name="<?php echo esc_attr( get_the_title( $root_product_id ) ); ?>" data-id-color="<?php echo esc_attr( $get_color ) . esc_attr( $root_variation_id ); ?>"
					data-size="<?php echo esc_attr( $get_size ); ?>" data-own-id="<?php echo esc_attr( $root_product_id ); ?>">

						<div class="atd-add-cart-item-card-content">
						
							<div class="atd-add-cart-item-img">
							
								<img src="<?php echo esc_attr( $variation_image_url ); ?>" alt="image" class="atd-add-cart-item-img-self">
							</div>

							<div class="atd-add-cart-underline"></div>

						</div>

						<div class="atd-icon-cart-cross"><i class="fas fa-times"></i></div>

					</div>

				</div>        

			</div>

			<div class="atd-container-add-cart-item">
			
				<div class="atd-add-cart-details">
				
					<div class="atd--add-cart-info">

						<p class="atd-cart-p">
							
							<span class="atd-cart-product-name"><?php echo esc_html__( get_the_title( $root_product_id ), 'allada-tshirt-designer-for-woocommerce' ); ?></span>
							
					
						</p>
						
						<p class="atd-cart-p">
							
							<span class="atd-cart-product-color"><?php echo esc_html__( $get_color, 'allada-tshirt-designer-for-woocommerce' ); ?></span>
							
						
						</p>

					</div>

					<div class="atd-btn-box">

						<button type="button" class="atd-btn-cart atd-btn-cart-continue"><?php echo esc_html__( 'Continue', 'allada-tshirt-designer-for-woocommerce' ); ?></button>

					</div>
				
				</div>
			
			</div>

		</div>

		<?php
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Get preview modal Add Product.
	 *
	 * @return Sting $output The output.
	 */
	private function get_preview_modal_add_product() {
		$root_variation_id = $this->editor->atd_product->variation_id;
		$root_product_id   = $this->editor->atd_product->root_product_id;
		$part_data         = atd_get_part_image( $root_product_id, $root_variation_id, 'bg-inc' );

		ob_start();
		?>

		<div class="atd-preview-box-add-cart-first">
			
			<div class="atd-preview-title"><?php echo esc_html__( 'Add Products', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

			<div class="atd-icon-add-cart-back isHidden"><i class="fas fa-chevron-left"></i></div>

			<div class="atd-icon-add-cart-first-cross"><i class="fas fa-times"></i></div>

			<div class="atd-icon-add-cart-content">

				<div class="atd-icon-add-cart-content-outer">

					<div class="atd-icon-add-cart-content-modal-item atd-products-choices">

						<div class="atd-header-wrapper"><header><?php echo esc_html__( 'Add your design to more great products.', 'allada-tshirt-designer-for-woocommerce' ); ?></header></div>

						<div class="atd-preview-box-add-cart-inner">

							<div class="atd-preview-box-add-cart-wrap">

							</div>

						</div>

						<div class="atd-btn-cart-modal-add-another-product">

							<button class="atd-btn-cart-modal-load-more-product"><?php echo esc_html__( 'Add another Product', 'allada-tshirt-designer-for-woocommerce' ); ?></button>

						</div>

					</div>

					<div class="atd-icon-add-cart-content-modal-item atd-add-cart-content-details">

						

					</div>

				</div>

			</div>

		</div>

		<div class="atd-shadow-add-cart-first"></div>
		<div class="atd-selected-part-image-section isHidden">
			<div data-id="<?php echo esc_attr( $root_variation_id ); ?>">
				<?php
				foreach ( $part_data as $xkey => $img_url ) {
					if ( ! empty( $img_url ) ) {
						?>
								<img src="<?php echo esc_attr( atd_o_get_proper_image_url( $img_url ) ); ?>" data-title="<?php echo esc_attr( $xkey ); ?>">
							<?php
					}
				}
				?>
			</div>
		</div>
		<div class="atd-add-to-cart-origin-elm isHidden">
			<div class="atd-ui-qty-item">
				
			<div class="atd-ui-qty-item-inner">

		<div class="atd-ui-qty-details">

			<div class="atd-ui-qty-img">
			
				<img src="<?php echo esc_attr( plugin_dir_url( __DIR__ ) ) . 'default/assets/images/product/product-2.jpg'; ?>" alt="" class="atd-ui-qty-img-self">
			
			</div>

		</div>

		<div class="atd-ui-qty-details">

			<div class="atd-ui-qty-details-content">

				<h4 class="atd-ui-qty-product-name"><?php echo esc_html__( 'Hanes Authentic T-shirt', 'allada-tshirt-designer-for-woocommerce' ); ?></h4>

				<div class="atd-ui-qty-text-color"><?php echo esc_html__( 'Color:&nbsp;', 'allada-tshirt-designer-for-woocommerce' ); ?><span class="atd-ui-qty-variation-color-text"></span></div>

				<div class="atd-ui-qty-text-size"><?php echo esc_html__( 'Size:&nbsp;', 'allada-tshirt-designer-for-woocommerce' ); ?><span class="atd-ui-qty-variation-size-text"></span></div>

				<div class="atd-ui-qty-size-container">

				</div>

			</div>

		</div>

		</div>

		<div class="atd-icon-qty-cross"><i class="fas fa-times"></i></div>


				</div>
		</div>
		
		<?php

		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get preview modal Quantity.
	 *
	 * @return Sting $output The output.
	 */
	private function get_preview_modal_quantity() {

		ob_start();
		?>

		<div class="atd-preview-box-quantity">
			
			<div class="atd-preview-title"><?php echo esc_html__( 'Quantity', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

			<div class="atd-icon-quantity-cross"><i class="fas fa-times"></i></div>

			<header><?php echo esc_html__( 'How many do you need?', 'allada-tshirt-designer-for-woocommerce' ); ?></header>

			<div class="atd-preview-box-quantity-inner">

				<div class="atd-loader"><div class="atd-loader-ring"></div></div>

				<div class="atd-preview-box-quantity-wrap">

				</div>

			</div>

			<div class="atd-ui-qty-totals">

			<div class="atd-ui-qty-totals-text"><?php echo esc_html__( 'Total Quantity:&nbsp;', 'allada-tshirt-designer-for-woocommerce' ); ?><span class="atd-ui-qty-totals-numb">0</span>&nbsp;-&nbsp;<span class="atd-ui-price">45$</span></div>

				<div class="atd-btn-box">

					<button type="button" class="atd-btn-cart atd-btn-cart-go-add-to-cart"><?php echo esc_html__( 'Add to cart', 'allada-tshirt-designer-for-woocommerce' ); ?></button>

				</div>

			</div>

		</div>

		<div class="atd-shadow-quantity"></div>
		
		<?php

		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get toolbar.
	 *
	 * @return String output.
	 */
	private function get_toolbar() {
		global $allowed_tags;
		ob_start();
		?>

		<div class="atd-canvas-item-top">

			<div class="atd-icon-tooltip" id="atd-icon-tooltip-alignment">

				<div class="atd-tooltip atd-tooltip-left"><?php echo esc_html__( 'Alignments', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<span class="atd-icon-span"><i class="icon-443"></i></span>

			</div>

			<div class="atd-icon-tooltip">

				<div class="atd-tooltip atd-tooltip-left"><?php echo esc_html__( 'Duplicate', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<span id="copy_paste_btn" class="atd-icon-span"><i class="icon-683"></i></span>

			</div>

			<!-- Context Menu Alignments Begin -->

			<?php
			/** Function Call Content Menu Alignment */
			echo wp_kses( $this->get_menu_alignment(), $allowed_tags );
			?>

			<!-- Context Menu Alignments End -->    

		</div>

		<div class="atd-canvas-item-bottom">

			<div class="atd-icon-tooltip">

				<div class="atd-tooltip atd-tooltip-left"><?php echo esc_html__( 'Delete', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<span id="delete_btn" class="atd-icon-span"><i class="icon-014"></i></span>

			</div>

			<div class="atd-icon-tooltip">

				<div class="atd-tooltip atd-tooltip-left"><?php echo esc_html__( 'Clear&nbsp;all', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<span id="clear_all_btn" class="atd-icon-span"><i class="icon-1407"></i></span>

			</div>

		</div>

		<?php
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Get menu alignment.
	 *
	 * @return String $output The output.
	 */
	private function get_menu_alignment() {

		ob_start();
		?>

		<div class="atd-context-menu-alignment">

			<div class="atd-row-context">

				<div class="atd-icon-tooltip">

					<div class="atd-tooltip atd-tooltip-top"><?php echo esc_html__( 'Send&nbsp;to&nbsp;back', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

					<span id="send_to_back_btn" class="atd-icon-span"><i class="icon-677"></i></span>

				</div>

				<div class="atd-icon-tooltip">

					<div class="atd-tooltip atd-tooltip-top"><?php echo esc_html__( 'Bring&nbsp;to&nbsp;front', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

					<span id="bring_to_front_btn" class="atd-icon-span"><i class="icon-676"></i></span>

				</div>

			</div>

			<div class="atd-row-context">

				<div class="atd-icon-tooltip">

					<div class="atd-tooltip atd-tooltip-top"><?php echo esc_html__( 'Flip&nbsp;H', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

					<span id="flip_h_btn" class="atd-icon-span"><i class="icon-687"></i></span>

				</div>

				<div class="atd-icon-tooltip">

					<div class="atd-tooltip atd-tooltip-top"><?php echo esc_html__( 'Flip&nbsp;V', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

					<span  id="flip_v_btn" class="atd-icon-span"><i class="icon-691"></i></span>

				</div>

			</div>

			<div class="atd-row-context">

				<div class="atd-icon-tooltip">

					<div class="atd-tooltip atd-tooltip-top"><?php echo esc_html__( 'Center&nbsp;H', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

					<span id="align_h_btn" class="atd-icon-span"><i class="icon-680"></i></span>

				</div>

				<div class="atd-icon-tooltip">

					<div class="atd-tooltip atd-tooltip-top"><?php echo esc_html__( 'Center&nbsp;V', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

					<span id="align_v_btn" class="atd-icon-span"><i class="icon-682"></i></span>

				</div>

			</div>

		</div>

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get output toolbar.
	 *
	 * @return String output.
	 */
	private function get_output_toolbar() {
		ob_start();
		global $wp_query;
		$design_index = -1;
		if ( isset( $wp_query->query_vars['design_index'] ) ) {
			$design_index = $wp_query->query_vars['design_index'];
		}
		?>

		<div class="atd-canvas-item-top">

			<div class="atd-icon-tooltip">

				<div class="atd-tooltip atd-tooltip-right"><?php echo esc_html__( 'Undo', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<span id="undo-btn" class="atd-icon-span"><i class="atd-icon-09"></i></span>

			</div>

			<div class="atd-icon-tooltip">

				<div class="atd-tooltip atd-tooltip-right"><?php echo esc_html__( 'Redo', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<span id="redo-btn" class="atd-icon-span"><i class="atd-icon-08"></i></span>

			</div>

		</div>

		<div class="atd-canvas-item-bottom">

			<div class="atd-icon-tooltip atd-prev-des" id="preview-btn">

				<div class="atd-tooltip atd-tooltip-right"><?php echo esc_html__( 'Preview', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<span class="atd-icon-span"><i class="icon-491"></i></span>

			</div>

			<div class="atd-icon-tooltip">

				<div class="atd-tooltip atd-tooltip-right"><?php echo esc_html__( 'Download', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<span class="atd-icon-span" id="download-btn"><i class="icon-1166"></i></span>

			</div>

			<div class="atd-icon-tooltip atd-save-des">

				<div class="atd-tooltip atd-tooltip-right"><?php echo esc_html__( 'Save', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<span class="atd-icon-span" data-toggle="o-modal" data-target="#atd_save_design"><i class="icon-612"></i></span>

			</div>

		</div>

		<?php
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Get parts.
	 *
	 * @return String output.
	 */
	private function get_parts() {
		ob_start();
		$is_icon_image = $this->check_icon_image_part();
		?>

		<div id="atd-parts-bar" class="atd-navigation-canvas <?php echo ( $is_icon_image ) ? 'atd-with-img-part' : ''; ?>">
			<?php
			$parts                = $this->atd_metas['parts'];
			$is_first             = true;
			$root_product_id      = $this->editor->atd_product->root_product_id;
			$variation_product_id = $this->editor->atd_product->variation_id;
			$product              = wc_get_product( $root_product_id );
			$product_type         = $product->get_type();
			$config               = get_post_meta( $root_product_id, 'atd-metas', true );
			if ( 'variation' === $product->get_type() ) {
				$product_type = 'variable';
			}
			foreach ( $parts as $key => $part_data ) {
				if ( 'yes' === $part_data['enable'] ) {
					$icon               = $config[ $variation_product_id ][ $key ]['icon'];
					$bg_not_included_id = $config[ $variation_product_id ][ $key ]['bg-inc'];
					$class              = '';
					if ( $is_first ) {
						$class = 'active';
					}
					$is_first = false;

					$bg_not_included_src = '';
					if ( ! empty( $bg_not_included_id ) ) {
						$bg_not_included_src = atd_o_get_proper_image_url( $bg_not_included_id );
					}

					$part_img = $part_data['name'];
					if ( ! $icon ) {
						$part_img = '';
					} else {
						$icon_src = atd_o_get_proper_image_url( $icon );
						if ( $icon_src ) {
							$part_img = '<img alt="image" src="' . $icon_src . '" class="mCS_img_loaded">';
						}
					}
					?>

			<li class="atd-navigation-canvas-item <?php echo esc_attr( $class ); ?> " data-title="<?php echo esc_attr( ucfirst( $key ) ); ?>" data-id="<?php echo esc_attr( $key ); ?>" data-url="<?php echo esc_attr( $bg_not_included_src ); ?>" data-placement="top" data-tooltip-title="<?php echo esc_attr( $part_data['name'] ); ?>" data-ov="" data-ovni="-1">

				<div class="atd-part-name"><?php echo esc_html( $part_data['name'] ); ?></div>

						<div class="atd-part-img">

							<?php echo wp_kses_post( $part_img ); ?>

						</div>

					</li>

					<?php
				}
			}
			?>

		</div>

		<?php
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Get canvas parts.
	 *
	 * @return String output.
	 */
	private function get_canvas_parts() {
		ob_start();

		$parts = $this->atd_metas['parts'];
		$style = '';
		foreach ( $parts as $part_key => $part_data ) {
			if ( ucfirst( $part_key ) === 'Front' ) {
				$style = 'style="left: 50%"';
			} else {
				$style = 'style="left: 150%"';
			}
			if ( 'yes' === $part_data['enable'] ) {
				?>
		<div class="atd-canvas-inner" <?php echo esc_html( $style ); ?> data-title="<?php echo esc_attr( ucfirst( $part_key ) ); ?>">

					<canvas id="atd-editor-<?php echo esc_attr( $part_key ); ?>"></canvas>

				</div>
				<?php
			}
		}

		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Get text tools.
	 *
	 * @return Sting $output The output.
	 */
	private function get_text_tools() {

		ob_start();
		?>

		<div class="atd-add-text atd-active">

			<!-- Create a shortcode to display each tab content -->

			<div class="atd-title"><?php echo esc_html__( 'Add Text', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

			<form>

				<div class="atd-textarea-field">

					<textarea id="new-text" placeholder="<?php echo esc_html__( 'Enter your text here', 'allada-tshirt-designer-for-woocommerce' ); ?>" name="atd-text-textarea"></textarea>

				</div>

				<div class="atd-btn-box">

					<button type="button" id="atd-add-text" class="atd-btn"><?php echo esc_html__( 'Add text', 'allada-tshirt-designer-for-woocommerce' ); ?></button>

				</div>

			</form>

		</div>

		<?php
		/** Function Call Content Text Editing */
		echo $this->get_editing_text_tools();

		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get add products tools.
	 *
	 * @return Sting $output The output.
	 */
	private function get_editing_text_tools() {
		global $allowed_tags;
		ob_start();
		?>

		<div class="atd-editing-text">

			<div class="add-row-text">

				<?php
				/** Function Call Content Text Fonts Family */
				echo wp_kses( $this->get_text_fonts_family(), $allowed_tags );
				?>

			</div>

			<div class="add-row-text">

				<div class="add-row-text-style">

					<?php
					/** Function Call Content Text Style */
					echo wp_kses( $this->get_text_style(), $allowed_tags );
					?>

				</div>

				<div class="add-row-text-style">

					<?php
					/** Function Call Content Text Decoration */
					echo wp_kses( $this->get_text_decoration(), $allowed_tags );
					?>

				</div>

				<div class="add-row-text-style">

					<?php
					/** Function Call Content Text Alignment */
					echo wp_kses( $this->get_text_alignement(), $allowed_tags );
					?>

				</div>

			</div>

			<div class="add-row-text">

				<div class="add-row-text-color">

					<?php
					/** Function Call Content Text Color */
					echo wp_kses( $this->get_text_color(), $allowed_tags );
					?>

				</div>

			</div>

			<div class="add-row-text">

				<div class="add-row-text-outline atd-range-container">

					<?php
					/** Function Call Content Text outline size */
					echo wp_kses( $this->get_text_outline_size(), $allowed_tags );
					?>

				</div>

				<div class="add-row-text-outline-color atd-range-container">

					<?php
					/** Function Call Content Text outline Color */
					echo wp_kses( $this->get_text_outline_color(), $allowed_tags );
					?>

				</div>

				<div class="add-row-text-color">

					<?php
					/** Function Call Content Text Background Color */
					echo wp_kses( $this->get_text_bg_color(), $allowed_tags );
					?>

				</div>

			</div>

			<div class="add-row-text">

				<div class="atd-range-container atd-range-container-text-size">

					<?php
					/** Function Call Content Text size */
					echo wp_kses( $this->get_text_size(), $allowed_tags );
					?>

				</div>

				<div class="atd-range-container atd-range-container-text-opacity">

					<?php
					/** Function Call Content Text size */
					echo wp_kses( $this->get_text_opacity(), $allowed_tags );
					?>

				</div>

				<div class="atd-range-container atd-p-0">

					<?php
					/** Function Call Content Text curved */
					echo wp_kses( $this->get_text_curved(), $allowed_tags );
					?>

				</div>

				<div class="atd-curved-content">

					<div class="atd-range-container atd-pt-20  atd-range-container-text-radius">

						<?php
						/** Function Call Content Text Curved Content Checked */
						echo wp_kses( $this->get_text_curved_content_checked(), $allowed_tags );
						?>

					</div>

				</div>

			</div>

		</div>

		<?php
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Get text fonts family.
	 *
	 * @return Sting $output The output.
	 */
	private function get_text_fonts_family() {

		ob_start();
		?>

		<div class="atd-text-title atd-font-title"><?php echo esc_html__( 'Font :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

		<div class="atd-font-container">

			<div class="atd-font-field">

				<div class="atd-font-label"><?php echo esc_html__( 'Choose your font', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<span class="fas fa-chevron-down"></span>

			</div>

			<div class="atd-font-drop-down">

				<?php
				$fonts = get_option( 'atd-fonts' );
				if ( empty( $fonts ) ) {
					$fonts = atd_get_default_fonts();
				}

				if ( isset( $this->atd_metas['global-fonts'] ) && 'no' === $this->atd_metas['global-fonts']['use-global-fonts'] && isset( $this->atd_metas['global-fonts']['selected-fonts'] ) && ! empty( $this->atd_metas['global-fonts']['selected-fonts'] ) ) {
					$fonts = $this->atd_metas['global-fonts']['selected-fonts'];

					foreach ( $fonts as $font ) {
						$font_label = json_decode( $font )[0];
						?>
						<li class="atd-font-drop-down-item" style="font-family: <?php echo esc_attr( $font_label ); ?>">

							<div class="atd-font-preview"><?php echo esc_html__( 'Hello', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

							<div class="atd-font-name"><?php echo esc_html( $font_label ); ?></div>

						</li>
						<?php
					}
				} else {
					foreach ( $fonts as $font ) {
						$font_label = $font[0];
						?>
						<li class="atd-font-drop-down-item" style="font-family: <?php echo esc_attr( $font_label ); ?>">

							<div class="atd-font-preview"><?php echo esc_html__( 'Hello', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

							<div class="atd-font-name"><?php echo esc_html( $font_label ); ?></div>

						</li>
						<?php
					}
				}
				?>

			</div>

		</div>

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get text style.
	 *
	 * @return Sting $output The output.
	 */
	private function get_text_style() {

		ob_start();
		?>

		<div class="atd-text-title"><?php echo esc_html__( 'Style :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

		<div class="atd-text-style-option">

			<li id="bold-cb" class="atd-text-style-option-item atd-icon-tooltip">

				<div class="atd-tooltip atd-tooltip-top"><?php echo esc_html__( 'Bold', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<span class="atd-icon-span"><i class="fas fa-bold"></i></span>

			</li>

			<li id="italic-cb" class="atd-text-style-option-item atd-icon-tooltip">

				<div class="atd-tooltip atd-tooltip-top"><?php echo esc_html__( 'Italic', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<span class="atd-icon-span"><i class="fas fa-italic"></i></span>

			</li>

		</div>

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get text decoration.
	 *
	 * @return Sting $output The output.
	 */
	private function get_text_decoration() {

		ob_start();
		?>

		<div class="atd-text-title"><?php echo esc_html__( 'Decoration :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

		<div class="atd-text-decoration-option">

			<li class="atd-text-decoration-option-item atd-icon-tooltip">

				<div class="atd-tooltip atd-tooltip-top"><?php echo esc_html__( 'Underline', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<span id="underline-cb"  class="atd-icon-span"><i class="fas fa-underline"></i></span>

			</li>

			<li class="atd-text-decoration-option-item atd-icon-tooltip">

				<div class="atd-tooltip atd-tooltip-top"><?php echo esc_html__( 'Line&nbsp;through', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<span id="strikethrough-cb" class="atd-icon-span"><i class="fas fa-strikethrough"></i></span>

			</li>

			<li class="atd-text-decoration-option-item atd-icon-tooltip">

				<div class="atd-tooltip atd-tooltip-top"><?php echo esc_html__( 'Overline', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<span id="overline-cb" class="atd-icon-span" style="transform: rotate(180deg)"><i class="fas fa-underline"></i></span>

			</li>

			<li class="atd-text-decoration-option-item atd-icon-tooltip">

				<div class="atd-tooltip atd-tooltip-top"><?php echo esc_html__( 'Normal', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<span id="txt-none-cb" class="atd-icon-span"><i class="fas fa-minus"></i></span>

			</li>

		</div>

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get text alignement.
	 *
	 * @return Sting $output The output.
	 */
	private function get_text_alignement() {

		ob_start();
		?>

		<div class="atd-text-title"><?php echo esc_html__( 'Alignment :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

		<div class="atd-text-alignement-option">

			<li class="atd-text-alignement-option-item atd-icon-tooltip">

				<div class="atd-tooltip atd-tooltip-bottom"><?php echo esc_html__( 'Text&nbsp;align&nbsp;left', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<span id="txt-align-left" class="atd-icon-span"><i class="fas fa-align-left"></i></span>

			</li>

			<li class="atd-text-alignement-option-item atd-icon-tooltip">

				<div class="atd-tooltip atd-tooltip-bottom"><?php echo esc_html__( 'Text&nbsp;align&nbsp;center', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<span id="txt-align-center" class="atd-icon-span"><i class="fas fa-align-center"></i></span>

			</li>

			<li class="atd-text-alignement-option-item atd-icon-tooltip">

				<div class="atd-tooltip atd-tooltip-bottom"><?php echo esc_html__( 'Text&nbsp;align&nbsp;right', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<span id="txt-align-right" class="atd-icon-span"><i class="fas fa-align-right"></i></span>

			</li>

			<li class="atd-text-alignement-option-item atd-icon-tooltip">

				<div class="atd-tooltip atd-tooltip-right"><?php echo esc_html__( 'Text&nbsp;align&nbsp;justify', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<span id="txt-align-justify" class="atd-icon-span"><i class="fas fa-align-justify"></i></span>

			</li>

		</div>

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get text color.
	 *
	 * @return Sting $output The output.
	 */
	private function get_text_color() {

		ob_start();
		?>

		<div class="atd-text-title"><?php echo esc_html__( 'Text&nbsp;color :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

		<div class="atd-color-picker">

			<input id="txt-color-selector"/>

		</div>

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get text outline size.
	 *
	 * @return Sting $output The output.
	 */
	private function get_text_outline_size() {

		ob_start();
		?>

		<div class="atd-text-title"><?php echo esc_html__( 'Outiline&nbsp;size :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

		<div class="atd-range-right">

			<div class="atd-range-slider-inner">

				<div class="atd-slider-value">

					<span>0</span>

				</div>

				<div class="atd-range-slider" id="atd-range-slider-size">

					<div class="atd-thumb"><span></span></div>

					<div class="atd-progress-bar"></div>

					<input id="atd-outline-size" type="range" min="0" max="5" step="0.1" value="0">

				</div>

			</div>

			<div class="atd-range-text">0</div>

		</div>

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get text outline color.
	 *
	 * @return Sting $output The output.
	 */
	private function get_text_outline_color() {
		ob_start();
		?>

		<div class="atd-text-title"><?php echo esc_html__( 'Outline&nbsp;color :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

		<div class="atd-color-picker">

			<input id="txt-outline-color-selector"/>

		</div>

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get text background color.
	 *
	 * @return Sting $output The output.
	 */
	private function get_text_bg_color() {
		ob_start();
		?>

		<div class="atd-text-title atd-bg-title"><?php echo esc_html__( 'Background&nbsp;color :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

		<div class="atd-color-picker atd-bg">

			<input id="txt-bg-color-selector"/>

		</div>

		<div class="atd-checkbox-container atd-bg-checkbox">

			<input type="checkbox" id="atd-checkbox-none-bg" class="atd-checkbox-input atd-checkbox-none-bg" checked>

			<label for="atd-checkbox-none-bg" class="atd-checkbox-label"><?php echo esc_html__( 'None', 'allada-tshirt-designer-for-woocommerce' ); ?></label>

		</div>

		<!-- <div class="atd-radio-container atd-radio-bg">

			<input type="radio" id="atd-none-bg" class="atd-radio-input">

			<label for="atd-none-bg" class="atd-radio-label">

				<div class="atd-dot"></div>

				<span class="atd-radio-span">None</span>

			</label>

		</div> -->

		<!-- <span id="txt-bg-color-selector-none">None</span> -->

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get text size.
	 *
	 * @return Sting $output The output.
	 */
	private function get_text_size() {
		ob_start();
		?>

		<div class="atd-text-title"><?php echo esc_html__( 'Size :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

		<div class="atd-range-right">

			<div class="atd-range-slider-inner">

				<div class="atd-slider-value">

					<span>0</span>

				</div>

				<div class="atd-range-slider" id="atd-range-slider-size">

					<div class="atd-thumb"><span></span></div>

					<div class="atd-progress-bar"></div>

					<input id="font-size-selector" type="range" min="25" max="100" value="30">

				</div>

			</div>

			<div class="atd-range-text">0</div>

		</div>

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get text opacity.
	 *
	 * @return Sting $output The output.
	 */
	private function get_text_opacity() {
		ob_start();
		?>
		<div class="atd-text-title"><?php echo esc_html__( 'Opacity :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

		<div class="atd-range-right">

			<div class="atd-range-slider-inner">

				<div class="atd-slider-value">

					<span>0</span>

				</div>

				<div class="atd-range-slider" id="atd-range-slider-size">

					<div class="atd-thumb"><span></span></div>

					<div class="atd-progress-bar"></div>

					<input type="range" id="opacity-slider" data-opacity="true" step="0.1" min="0" max="1" value="1">

				</div>

			</div>

			<div class="atd-range-text">0</div>

		</div>

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get text curved.
	 *
	 * @return Sting $output The output.
	 */
	private function get_text_curved() {
		ob_start();
		?>

		<div class="atd-text-title"><?php echo esc_html__( 'Curved :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

		<div class="atd-range-right">

			<div class="atd-checkbox-container">

				<input type="checkbox" id="cb-curved" class="atd-checkbox-input atd-checkbox-curved">

				<label for="cb-curved" class="atd-checkbox-label"></label>

			</div>

			<!-- <span class="atd-text-curved-label"> 

				<label class="atd-switch" for="cb-curved" id="cb-curved-label"> 

				<input type="checkbox" id="cb-curved" class="custom-cb checkmark"> 

				<span class="atd-slider round"></span> 

				</label> 

			</span> -->

		</div>
		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get text curved content checked.
	 *
	 * @return Sting $output The output.
	 */
	private function get_text_curved_content_checked() {

		ob_start();
		?>

		<div class="atd-text-title"><?php echo esc_html__( 'Radius :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

		<div class="atd-range-right">

			<div class="atd-range-slider-inner">

				<div class="atd-slider-value">

					<span>0</span>

				</div>

				<div class="atd-range-slider" id="atd-range-slider-radius">

					<div class="atd-thumb"><span></span></div>

					<div class="atd-progress-bar"></div>

					<input id="curved-txt-radius-slider" type="range" step="10" min="0" max="300" value="150">

				</div>

			</div>

			<div class="atd-range-text">0</div>

		</div>

		</div>

		<div class="atd-range-container  atd-range-container-text-spacing atd-p-0">

			<div class="atd-text-title"><?php echo esc_html__( 'Spacing :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

			<div class="atd-range-right">

				<div class="atd-range-slider-inner">

					<div class="atd-slider-value">

						<span>0</span>

					</div>

					<div class="atd-range-slider" id="atd-range-slider-spacing">

						<div class="atd-thumb"><span></span></div>

						<div class="atd-progress-bar"></div>

						<input id="curved-txt-spacing-slider" type="range" step="1" min="0" max="20" value="9">

					</div>

				</div>

				<div class="atd-range-text">0</div>

			</div>

			<?php
			$output = ob_get_clean();

			return $output;
	}

	/**
	 * Get uploads tools.
	 *
	 * @param type $facebook_app_id The facebook app id.
	 * @param type $instagram_app_id The instragram app id.
	 * @param type $facebook_app_secret The facebook app secret.
	 * @param type $instagram_app_secret The instragram app secret.
	 * @param type $uploads_options The uploads options.
	 *
	 * @return string $output The output.
	 */
	private function get_uploads_tools( $uploads_options ) {

		ob_start();

		$support_formated = implode( ',', $uploads_options['atd-upl-extensions'] );

		$min_width = '';

		$min_height = '';

		if ( ! empty( $uploads_options['atd-min-upload-width'] ) ) {

			$min_width = $uploads_options['atd-min-upload-width'];
		}

		if ( ! empty( $uploads_options['atd-min-upload-height'] ) ) {

			$min_height = $uploads_options['atd-min-upload-height'];
		}
		?>

		<div class="atd-upload-inner atd-active">

			<div class="atd-title"><?php echo esc_html__( 'Upload', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

			<form id="userfile_upload_form" method="post" action="<?php echo esc_html( admin_url( 'admin-ajax.php' ) ); ?>"  enctype="multipart/form-data">

				<input type = "hidden" name = "nonce" value = "<?php echo esc_html( wp_create_nonce( 'atd-picture-upload-nonce' ) ); ?>">

				<input type = "hidden" name = "action" value="handle_picture_upload">

				<div class="atd-p-center">

					<p class="atd-p-first"><?php echo esc_html__( 'Choose a file', 'allada-tshirt-designer-for-woocommerce' ); ?></p>

					<p class="atd-p-second"><?php echo sprintf( esc_html__( 'Supported format: %1$s (up to 10MB) Min width: %2$s; Min height: %3$s', 'allada-tshirt-designer-for-woocommerce' ), $support_formated, $min_width, $min_height ); ?></p>

				</div>

				<div class="atd-input-text-field">

					<label for="userfile" class="atd-label-upload"><i class="atd-loader-label fas fa-spinner fa-spin"></i><span><?php echo esc_html__( 'Upload new image', 'allada-tshirt-designer-for-woocommerce' ); ?></span></label>

					<input type="file" id="userfile" name="userfile">


				</div>

			</form>

			<div class="atd-input-search-field">

				<div class="atd-icon-search"><i class="fas fa-search"></i></div>

				<input type="text" name="atd-search-upload" placeholder="<?php echo esc_html__( 'Search...', 'allada-tshirt-designer-for-woocommerce' ); ?>" class="atd-input-search-upload">

				<div class="atd-underline-search"></div>

			</div>

			<div class="atd-preview-upolad-container">

				<?php
				if ( ! is_user_logged_in() ) {

					if ( isset( $_COOKIE['atd-upload'] ) && ! empty( $_COOKIE['atd-upload'] ) ) {

						$atd_upload_json = json_decode( base64_decode( $_COOKIE['atd-upload'] ) );

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

						foreach ( $atd_upload as $image ) {

							if ( isset( $image->img_url ) && ! empty( $image->img_url ) && isset( $image->img_id ) && ! empty( $image->img_id ) ) {
								?>

								<div class="atd-preview-upolad-item atd-fade" data-title="<?php echo esc_attr( $image->img_name ); ?>" data-upload-id="<?php echo esc_attr( $image->img_id ); ?>" data-url="<?php echo esc_attr( $image->img_url ); ?>" data-img-name="<?php echo esc_attr( $image->img_name ); ?>">

									<div class="atd-preview-upolad" style="background-image:url('<?php echo esc_attr( $image->img_url ); ?>')"></div>

									<div class="atd-icon-cross"><i class="fas fa-times"></i></div>

								</div>

								<?php
							}
						}
					} else {
						?>

						<p><?php echo esc_html__( 'No image found', 'allada-tshirt-designer-for-woocommerce' ); ?></p>

						<?php
					}
				} else {

					global $current_user;

					$current_user_id = $current_user->ID;

					$user_uploads = ( isset( get_user_meta( $current_user_id, 'atd_saved_uploads' )[0] ) && ! empty( get_user_meta( $current_user_id, 'atd_saved_uploads' )[0] ) ) ? get_user_meta( $current_user_id, 'atd_saved_uploads' )[0] : array();

					if ( is_array( $user_uploads ) && ! empty( $user_uploads ) ) {

						$i = 0;

						foreach ( $user_uploads as $image ) {

							if ( isset( $image['img_url'] ) && ! empty( $image['img_url'] ) && isset( $image['img_id'] ) && ! empty( $image['img_id'] ) ) {
								?>

								<div class="atd-preview-upolad-item atd-fade" data-title="<?php echo esc_attr( $image['img_name'] ); ?>" data-upload-id="<?php echo esc_attr( $image['img_id'] ); ?>" data-url="<?php echo esc_attr( $image['img_url'] ); ?>" data-img-name="<?php echo esc_attr( $image['img_name'] ); ?>">

									<div class="atd-preview-upolad" style="background-image:url('<?php echo esc_attr( $image['img_url'] ); ?>')"></div>

									<div class="atd-icon-cross"><i class="fas fa-times"></i></div>

								</div> 

								<?php
							}
						}
					} else {
						?>

						<p><?php echo esc_html__( 'No image found', 'allada-tshirt-designer-for-woocommerce' ); ?></p>

						<?php
					}
				}
				?>

			</div>

		</div>

		<div class="atd-upload-edit-inner">

			<?php
			/** Function Call Content Uploads Filters */
			echo $this->get_uploads_filters_tools();
			?>

		</div>

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Check If at least one of the parts has a defined icon image.
	 *
	 * @return boolean $is_icon_image.
	 */
	// private function check_icon_image_part() {
	// $parts = $this->atd_metas['parts'];
	// $root_product_id = $this->editor->atd_product->variation_id;
	// $product = wc_get_product($root_product_id);
	// $product_type = $product->get_type();
	// $config = get_post_meta($root_product_id, 'atd-metas', true);
	// $is_icon_image = false;
	// foreach ($parts as $key => $part_data) {
	// if ("yes" === $part_data['enable'] && isset($config[$product_type][$key]['icon']) && !empty($config[$product_type][$key]['icon'])) {
	// $is_icon_image = true;
	// break;
	// }
	// }
	// return $is_icon_image;
	// }

	private function check_icon_image_part() {
		$root_variation_id   = $this->editor->atd_product->variation_id;
		$root_product_id     = $this->editor->atd_product->root_product_id;
		$this_variation_icon = atd_get_part_image( $root_product_id, $root_variation_id, 'icon' );
		$is_icon_image       = false;
		foreach ( $this_variation_icon as $key => $icon ) {
			if ( $icon != '' ) {
				$is_icon_image = true;
				break;
			}
		}
		return $is_icon_image;
	}

	/**
	 * Get clippart tools.
	 *
	 * @return Sting $output The output.
	 */
	private function get_cliparts_tools() {
		global $allowed_tags;
		ob_start();
		?>

		<div class="atd-clippart-inner atd-active">

			<div class="atd-title"><?php echo esc_html__( 'Clippart', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

			<div class="atd-input-search-field">

				<div class="atd-icon-search"><i class="fas fa-search"></i></div>

				<input type="text" name="atd-search-upload" placeholder="<?php echo esc_html__( 'Search...', 'allada-tshirt-designer-for-woocommerce' ); ?>" class="atd-input-search-upload">

				<div class="atd-underline-search"></div>

			</div>

			<div class="atd-clippart-group-container atd-active">

				<?php
				$args = array(
					'numberposts' => -1,
					'post_type'   => 'atd-cliparts',
				);

				$cliparts_groups = get_posts( $args );

				if ( isset( $this->atd_metas['global-cliparts'] ) && 'no' === $this->atd_metas['global-cliparts']['use-global-cliparts'] && isset( $this->atd_metas['global-cliparts']['selected-cliparts'] ) && ! empty( $this->atd_metas['global-cliparts']['selected-cliparts'] ) ) {

					$cliparts_groups_id = $this->atd_metas['global-cliparts']['selected-cliparts'];

					$cliparts_groups = array();

					foreach ( $cliparts_groups_id as $cliparts_group_id ) {

						array_push( $cliparts_groups, get_post( $cliparts_group_id ) );
					}
				}

				foreach ( $cliparts_groups as $cliparts_group ) {

					$cliparts = get_post_meta( $cliparts_group->ID, 'atd-cliparts-data', true );

					if ( ! empty( $cliparts ) ) {
						?>

						<li class="atd-clippart-group-item" data-group-name="<?php echo esc_attr( $cliparts_group->post_title ); ?>" data-title="<?php echo esc_attr( $cliparts_group->post_title ); ?>" data-groupid="<?php echo esc_attr( $cliparts_group->ID ); ?>"><?php echo esc_attr( $cliparts_group->post_title ); ?></li>

						<?php
					}
				}
				?>

			</div>

		</div>

		<div class="atd-clippart-container">

			<div class="atd-clipart-head">

				<div class="atd-title"><?php echo esc_html__( 'Emojis', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<div class="atd-input-search-field">

					<div class="atd-icon-search"><i class="fas fa-search"></i></div>

					<input type="text" name="atd-search-upload" placeholder="<?php echo esc_html__( 'Search...', 'allada-tshirt-designer-for-woocommerce' ); ?>" class="atd-input-search-upload">

					<div class="atd-underline-search"></div>

				</div>

				<div class="atd-icon-cli-cross"><i class="fas fa-times"></i></div>

			</div>

			<div class="atd-preview-clipart-container">

				<?php
				foreach ( $cliparts_groups as $cliparts_group ) {

					$cliparts = get_post_meta( $cliparts_group->ID, 'atd-cliparts-data', true );
					?>

					<div class="atd-preview-clippart-group" data-title="<?php echo esc_attr( $cliparts_group->post_title ); ?>">

						<?php
						if ( ! empty( $cliparts ) ) {

							foreach ( $cliparts as $clipart ) {

								$attachment_url = atd_o_get_proper_image_url( $clipart['id'] );

								$name = $clipart['name'];

								$price = $clipart['price'];

								if ( empty( $price ) ) {

									$price = 0;
								}
								?>

								<div class="atd-preview-clippart-item" data-title="<?php echo esc_attr( $name ); ?>" data-groupid="<?php echo esc_attr( $cliparts_group->ID ); ?>" data-img-name="<?php echo esc_attr( $name ); ?>" data-price="<?php echo esc_attr( $price ); ?>" data-original="<?php echo esc_attr( $attachment_url ); ?>" data-url="<?php echo esc_attr( $attachment_url ); ?>" data-src="<?php echo esc_attr( $attachment_url ); ?>">

									<div class="atd-preview-clippart" style="background-image:url('<?php echo esc_attr( $attachment_url ); ?>')"></div>

									<div class="atd-preview-clippart-item-details">

										<span class="atd-clippart-name"><?php echo esc_attr( $name ); ?></span>

										<span class="atd-clippart-price">$&nbsp;<?php echo esc_attr( $price ); ?></span>

									</div>

								</div>

								<?php
							}
						}
						?>

					</div>

					<?php
				}
				?>
								

			</div>

		</div>

		<div class="atd-clippart-edit-inner">

			<?php
			/** Function Call Content Clipparts Filters */
			echo wp_kses( $this->get_clipparts_filters_tools(), $allowed_tags );
			?>

		</div>

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get cliparts filters tools.
	 *
	 * @return String output.
	 */
	private function get_clipparts_filters_tools() {

		ob_start();
		?>

		<div class="atd-title"><?php echo esc_html__( 'Edit Clippart Image', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

		<div class="atd-icon-edit-cli-cross"><i class="fas fa-times"></i></div>

		<div class="atd-filter-title"><?php echo esc_html__( 'Filters :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

		<div class="atd-filter-container">

			<input type="checkbox" id="grayscale-1" class="custom-cb filter-cb acd-grayscale">
			<label for="grayscale-1" class="grayscale-1">Grayscale</label>
			<input type="checkbox" id="invert-1" class="custom-cb filter-cb acd-invert">
			<label for="invert-1" class="invert-1">Invert</label>
			<input type="checkbox" id="sepia-1" class="custom-cb filter-cb acd-sepia">
			<label for="sepia-1" class="sepia-1">Sepia 1</label>
			<input type="checkbox" id="sepia2-1" class="custom-cb filter-cb acd-sepia2">
			<label for="sepia2-1" class="sepia2-1">Sepia 2</label>
			<input type="checkbox" id="blur-1" class="custom-cb filter-cb acd-blur">
			<label for="blur-1" class="blur-1">Blur</label>
			<input type="checkbox" id="sharpen-1" class="custom-cb filter-cb acd-sharpen">
			<label for="sharpen-1" class="sharpen-1">Sharpen</label>
			<input type="checkbox" id="emboss-1" class="custom-cb filter-cb acd-emboss">
			<label for="emboss-1" class="emboss-1">Emboss</label>
			<div class="clipart-bg-color-container"></div>

		</div>
		<div class="atd-range-container">

			<div class="atd-text-title"><?php echo esc_html__( 'Opacity :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

			<div class="atd-range-right">

				<div class="atd-range-slider-inner">

					<div class="atd-slider-value">

						<span>0</span>

					</div>

					<div class="atd-range-slider" id="atd-range-slider-size">

						<div class="atd-thumb"><span></span></div>

						<div class="atd-progress-bar"></div>

						<input type="range" min="0" max="10" value="0">

					</div>

				</div>

				<div class="atd-range-text">0</div>

			</div>

		</div>

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get uploads filters tools.
	 *
	 * @return String output.
	 */
	private function get_uploads_filters_tools() {

		ob_start();
		?>

		<div class="atd-title"><?php echo esc_html__( 'Edit Upload Image', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

		<div class="atd-icon-edit-uplo-cross"><i class="fas fa-times"></i></div>

		<div class="atd-filter-title"><?php echo esc_html__( 'Filters :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

		<div class="atd-filter-container">

			<input type="checkbox" id="grayscale-2" class="custom-cb filter-cb acd-grayscale">
			<label for="grayscale-2" class="grayscale-2">Grayscale</label>
			<input type="checkbox" id="invert-2" class="custom-cb filter-cb acd-invert">
			<label for="invert-2" class="invert-2">Invert</label>
			<input type="checkbox" id="sepia-2" class="custom-cb filter-cb acd-sepia">
			<label for="sepia-2" class="sepia-2">Sepia 1</label>
			<input type="checkbox" id="sepia2-2" class="custom-cb filter-cb acd-sepia2">
			<label for="sepia2-2" class="sepia2-2">Sepia 2</label>
			<input type="checkbox" id="blur-2" class="custom-cb filter-cb acd-blur">
			<label for="blur-2" class="blur-2">Blur</label>
			<input type="checkbox" id="sharpen-2" class="custom-cb filter-cb acd-sharpen">
			<label for="sharpen-2" class="sharpen-2">Sharpen</label>
			<input type="checkbox" id="emboss-2" class="custom-cb filter-cb acd-emboss">
			<label for="emboss-2" class="emboss-2">Emboss</label>
			<div class="clipart-bg-color-container"></div>

		</div>

		<div class="atd-range-container">

			<div class="atd-text-title"><?php echo esc_html__( 'Opacity :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

			<div class="atd-range-right">

				<div class="atd-range-slider-inner">

					<div class="atd-slider-value">

						<span>0</span>

					</div>

					<div class="atd-range-slider">

						<div class="atd-thumb"><span></span></div>

						<div class="atd-progress-bar"></div>

						<input type="range" min="0" max="10" value="0">

					</div>

				</div>

				<div class="atd-range-text">0</div>

			</div>

		</div>

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get team tools.
	 *
	 * @return Sting $output The output.
	 */
	private function get_team_tools() {
		global $allowed_tags;

		ob_start();

		echo wp_kses( $this->get_team_details_content(), $allowed_tags );

		echo wp_kses( $this->get_team_tools_content(), $allowed_tags );

		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get team details content.
	 *
	 * @return Sting $output The output.
	 */
	private function get_team_details_content() {
		ob_start();
		?>
		<div class = "atd-team-inner atd-active">

			<div class = "atd-title"><?php echo esc_html__( 'Names&nbsp;and&nbsp;Numbers', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

			<div class="atd-img-wrap">

				<img src="<?php echo esc_attr( ATD_URL ) . 'includes/skins/default/assets/images/namesNumbers.jpg'; ?>" class="atd-img-team" alt="">

			</div>

			<p class="atd-team-text">Use personalized Names & Numbers for projects like team winokush where you need a unique name and/or number for each item.</p>

			<div class="atd-btn-team-wrap">

				<button type="button" class="atd-btn-team atd-step-1"><?php echo esc_html__( 'Add&nbsp;Names&nbsp;And&nbsp;Numbers', 'allada-tshirt-designer-for-woocommerce' ); ?></button>

			</div>

		</div>
		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get team tools content.
	 *
	 * @return Sting $output The output.
	 */
	private function get_team_tools_content() {
		global $allowed_tags;
		ob_start();
		?>
		<div class="atd-team-container">

			<div class="atd-title"><?php echo esc_html__( 'Names&nbsp;and&nbsp;Numbers&nbsp;Tools', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

			<div class="atd-icon-team-cross"><i class="fas fa-times"></i></div>

			<div class="atd-team-tools-wrap">

				<div class="atd-range-container">

					<div class="atd-text-title"><?php echo esc_html__( 'Add&nbsp;Names :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

					<div class="atd-range-right">

						<div class="atd-checkbox-container">

							<input type="checkbox" id="atd-checkbox-add-name" class="atd-checkbox-input atd-checkbox-add-name">

						</div>

					</div>

				</div>

				<div class="atd-team-name-content" id="atd-team-name-tool">

					<?php
					/** Function Call Content Team Name Tools */
					echo wp_kses( $this->get_team_name_tools(), $allowed_tags );
					?>

				</div>

				<div class="atd-range-container">

					<div class="atd-text-title"><?php echo esc_html__( 'Add&nbsp;Numbers :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

					<div class="atd-range-right">

						<div class="atd-checkbox-container">

							<input type="checkbox" id="atd-checkbox-add-number" class="atd-checkbox-input atd-checkbox-add-number">

						</div>

					</div>

				</div>

				<div class="atd-team-number-content" id="atd-team-number-tool">

					<?php
					/** Function Call Content Team Number Tools */
					echo wp_kses( $this->get_team_number_tools(), $allowed_tags );
					?>

				</div>

				<div class="atd-btn-team-wrap">

					<button type="button" class="atd-btn-team atd-step-2"><?php echo esc_html__( 'Enter&nbsp;Names/&nbsp;Numbers', 'allada-tshirt-designer-for-woocommerce' ); ?></button>

				</div>

			</div>  

		</div>
		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get team name tools.
	 *
	 * @return String output.
	 */
	private function get_team_name_tools() {

		ob_start();
		?>

		<div class="atd-input-select-container">

			<div class="atd-text-title"><?php echo esc_html__( 'Side :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

			<div class="atd-input-select-field">

				<select class="atd-select atd-team-name-side" name="atd-team-name-side">

					<?php
					$parts = $this->atd_metas['parts'];
					if ( isset( $parts ) && ! empty( $parts ) ) {
						foreach ( $parts as $part_key => $part_data ) {
							if ( 'yes' === $part_data['enable'] ) {
								?>
								<option value="<?php echo esc_attr( $part_key ); ?>" class="atd-option"><?php echo esc_html__( $part_data['name'], 'allada-tshirt-designer-for-woocommerce' ); ?></option>
								?>
								<?php
							}
						}
					}
					?>

				</select>

			</div>

		</div>

		<div class="atd-input-select-container">

			<div class="atd-text-title"><?php echo esc_html__( 'Height :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

			<div class="atd-input-select-field">

				<?php
				$product_team_data = $this->get_product_team_data();
				if ( isset( $product_team_data['name'] ) && ! empty( $product_team_data['name'] ) ) {
					if (
							isset( $product_team_data['name']['min-height'] ) && ! empty( $product_team_data['name']['min-height'] ) &&
							isset( $product_team_data['name']['max-height'] ) && ! empty( $product_team_data['name']['max-height'] ) &&
							isset( $product_team_data['name']['step-height'] ) && ! empty( $product_team_data['name']['step-height'] ) &&
							isset( $product_team_data['name']['height-unit'] ) && ! empty( $product_team_data['name']['height-unit'] )
					) {
						?>
						<select class="atd-select atd-team-name-height" name="atd-team-name-height">

							<?php
							for ( $i = $product_team_data['name']['min-height']; $i <= $product_team_data['name']['max-height']; $i = $i + $product_team_data['name']['step-height'] ) {
								if ( isset( $product_team_data['name']['default-height'] ) && ( ! empty( $product_team_data['name']['default-height'] ) || 0 == $product_team_data['name']['default-height'] ) && $i == $product_team_data['name']['default-height'] ) {
									?>
									<option selected data-default-value="yes" value="<?php echo esc_attr( $i ); ?>" data-unit="<?php echo esc_attr( $product_team_data['name']['height-unit'] ); ?>" class="atd-option"> <?php echo esc_attr( $i ) . ' ' . esc_attr( $product_team_data['name']['height-unit'] ); ?></option>
									<?php
								} else {
									?>
									<option value="<?php echo esc_attr( $i ); ?>" data-unit="<?php echo esc_attr( $product_team_data['name']['height-unit'] ); ?>" class="atd-option"> <?php echo esc_attr( $i ) . ' ' . esc_attr( $product_team_data['name']['height-unit'] ); ?></option>
									<?php
								}
							}
							?>
						</select>
						<?php
					}
				}
				?>

			</div>

		</div>

		<div class="add-row-text">

			<div class="add-row-text-color">

				<div class="atd-text-title"><?php echo esc_html__( 'Color :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<div class="atd-color-picker">

					<input id="team-name-color-selector" class="atd-team atd-team-name-color" name="atd-team-name-color"/>

				</div>

			</div>

		</div>

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get team number tools.
	 *
	 * @return String output.
	 */
	private function get_team_number_tools() {

		ob_start();
		?>

		<div class="atd-input-select-container">

			<div class="atd-text-title"><?php echo esc_html__( 'Side :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

			<div class="atd-input-select-field">

				<select class="atd-select atd-team-number-side" name="atd-team-number-side">

					<?php
					$parts = $this->atd_metas['parts'];
					foreach ( $parts as $part_key => $part_data ) {
						if ( 'yes' === $part_data['enable'] ) {
							?>
							<option value="<?php echo esc_attr( $part_key ); ?>" class="atd-option"><?php echo esc_html__( $part_data['name'], 'allada-tshirt-designer-for-woocommerce' ); ?></option>
							?>
							<?php
						}
					}
					?>

				</select>

			</div>

		</div>

		<div class="atd-input-select-container">

			<div class="atd-text-title"><?php echo esc_html__( 'Height :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

			<div class="atd-input-select-field">

				<?php
				$product_team_data = $this->get_product_team_data();
				if ( isset( $product_team_data['number'] ) && ! empty( $product_team_data['number'] ) ) {
					if (
							isset( $product_team_data['number']['min-height'] ) && ! empty( $product_team_data['number']['min-height'] ) &&
							isset( $product_team_data['number']['max-height'] ) && ! empty( $product_team_data['number']['max-height'] ) &&
							isset( $product_team_data['number']['step-height'] ) && ! empty( $product_team_data['number']['step-height'] ) &&
							isset( $product_team_data['number']['height-unit'] ) && ! empty( $product_team_data['number']['height-unit'] )
					) {
						?>
						<select class="atd-select atd-team-number-height" name="atd-team-number-height">

							<?php
							for ( $i = $product_team_data['number']['min-height']; $i <= $product_team_data['number']['max-height']; $i = $i + $product_team_data['number']['step-height'] ) {
								if ( isset( $product_team_data['number']['default-height'] ) && ( ! empty( $product_team_data['number']['default-height'] ) || 0 == $product_team_data['number']['default-height'] ) && $i == $product_team_data['number']['default-height'] ) {
									?>
									<option selected value="<?php echo esc_attr( $i ); ?>" data-unit="<?php echo esc_attr( $product_team_data['number']['height-unit'] ); ?>" class="atd-option"> <?php echo esc_attr( $i ) . ' ' . esc_attr( $product_team_data['number']['height-unit'] ); ?></option>
									<?php
								} else {
									?>
									<option value="<?php echo esc_attr( $i ); ?>" data-unit="<?php echo esc_attr( $product_team_data['number']['height-unit'] ); ?>" class="atd-option"> <?php echo esc_attr( $i ) . ' ' . esc_attr( $product_team_data['number']['height-unit'] ); ?></option>
									<?php
								}
							}
							?>
						</select>
						<?php
					}
				}
				?>

			</div>

		</div>

		<div class="add-row-text">

			<div class="add-row-text-color">

				<div class="atd-text-title"><?php echo esc_html__( 'Color :', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<div class="atd-color-picker">

					<input id="team-number-color-selector" class="atd-team atd-team-number-color" name="atd-team-number-color"/>

				</div>

			</div>

		</div>

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get preview modal team.
	 *
	 * @return Sting $output The output.
	 */
	private function get_preview_modal_team() {
		$product_id          = $this->editor->atd_product->root_product_id;
		$atd_product_details = atd_get_related_custom_product_details( $product_id );
		$var_sizes           = $atd_product_details['variation_sizes'];
		$sizes               = array();
		$sizes['']           = '';
		if ( is_array( $var_sizes ) || is_object( $var_sizes ) ) {
			$n = count( $var_sizes );
			for ( $i = 0; $i < $n; $i++ ) {
				$sizes[ $var_sizes[ $i ] ] = $var_sizes[ $i ];
			}
		}

		ob_start();
		?>

		<div class="atd-preview-box-team">

			<div class="atd-preview-title"><?php echo esc_html__( 'Names&nbsp;&&nbsp;Numbers', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

			<div class="atd-icon-team-cross"><i class="fas fa-times"></i></div>

			<header><?php echo esc_html__( 'Enter your full list and sizes for accurate pricing.', 'allada-tshirt-designer-for-woocommerce' ); ?></header>

			<div class="atd-team-list-section">

				<div class="atd-team-list-info">

					<div class="wb-div-empty">

						<div class="wb-team-help-header"><?php echo esc_html__( 'Helpful Hints', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

						<p class="wb-team-help-duplicate-desc"><?php echo esc_html__( 'We’ve noticed the following potential problems with your list. Please double-check them to ensure accuracy.', 'allada-tshirt-designer-for-woocommerce' ); ?></p>    

						<div class="wb-team-help-duplicates isHidden">

							<div class="wb-team-help-missing-name wb-team-help-missing-none isHidden">

								<div class="wb-team-help-duplicate-header"><?php echo esc_html__( 'Missing Names&nbsp;', 'allada-tshirt-designer-for-woocommerce' ); ?><a href="#" class="wb-missing-link">(<span class="wb-missing-action">Hide</span>)</a></div>

								<ul class="wb-team-help-duplicate-list">

									<li><span class="wb-missing-numb">#10</span>&nbsp;&nbsp;<?php echo esc_html__( 'doesn’t have a name', 'allada-tshirt-designer-for-woocommerce' ); ?></li>

									<li><span class="wb-missing-numb">#20</span>&nbsp;&nbsp;<?php echo esc_html__( 'doesn’t have a name', 'allada-tshirt-designer-for-woocommerce' ); ?></li>

								</ul>

							</div>

							<div class="wb-team-help-missing-numb wb-team-help-missing-none isHidden">

								<div class="wb-team-help-duplicate-header"><?php echo esc_html__( 'Missing Numbers&nbsp;', 'allada-tshirt-designer-for-woocommerce' ); ?><a href="#" class="wb-missing-link">(<span class="wb-missing-action">Hide</span>)</a></div>

								<ul class="wb-team-help-duplicate-list">

									<li><span class="wb-missing-name">#wino</span>&nbsp;&nbsp;<?php echo esc_html__( 'doesn’t have a number', 'allada-tshirt-designer-for-woocommerce' ); ?></li>

									<li><span class="wb-missing-name">#boy</span>&nbsp;&nbsp;<?php echo esc_html__( 'doesn’t have a number', 'allada-tshirt-designer-for-woocommerce' ); ?></li>

								</ul>

							</div>

						</div>

					</div>

				</div>

				<div class="atd-team-list-container">

					<table class="atd-styled-table">

						<thead>

							<tr>

								<th><?php echo esc_html__( 'Name', 'allada-tshirt-designer-for-woocommerce' ); ?></th>

								<th><?php echo esc_html__( 'Number', 'allada-tshirt-designer-for-woocommerce' ); ?></th>

								<th><?php echo esc_html__( 'Size', 'allada-tshirt-designer-for-woocommerce' ); ?></th>

								<th></th>

							</tr>

						</thead>

						<tbody>

							<tr class="atd-team-row">

								<td>

									<div class="atd-input-field">

										<input type="text" class="atd-input-text atd-team-names-list" name="atd-team-names-list[]" placeholder="Enter Name">

									</div>

								</td>

								<td>

									<div class="atd-input-field">

										<input type="number" class="atd-input-number atd-team-numbers-list"  name="atd-team-numbers-list[]" placeholder="00" min="0">

									</div>

								</td>

								<td>

									<div class="atd-input-select-field">

										<?php if ( isset( $sizes ) && ! empty( $sizes ) ) { ?>

											<select class="atd-select atd-team-sizes-list" name="atd-team-sizes-list[]">

												<?php foreach ( $sizes as $key => $size ) { ?>

													<option value="<?php echo esc_attr( $key ); ?>" class="atd-option"><?php echo esc_attr( $size ); ?></option>

												<?php } ?>

											</select>

										<?php } ?>

										<i class="atd-icon-chevron fas fa-chevron-down"></i>

									</div>

								</td>

								<td>

									<span class="atd-icon-team fas fa-trash"></span>

								</td>

							</tr>

							<tr class="atd-team-row">

								<td>

									<div class="atd-input-field">

										<input type="text" class="atd-input-text atd-team-names-list" name="atd-team-names-list[]" placeholder="Enter Name">

									</div>

								</td>

								<td>

									<div class="atd-input-field">

										<input type="number" class="atd-input-number atd-team-numbers-list"  name="atd-team-numbers-list[]" placeholder="00" min="0">

									</div>

								</td>

								<td>

									<div class="atd-input-select-field">

										<?php if ( isset( $sizes ) && ! empty( $sizes ) ) { ?>

											<select class="atd-select atd-team-sizes-list" name="atd-team-sizes-list[]">

												<?php foreach ( $sizes as $key => $size ) { ?>

													<option value="<?php echo esc_attr( $key ); ?>" class="atd-option"><?php echo esc_attr( $size ); ?></option>

												<?php } ?>

											</select>

										<?php } ?>

										<i class="atd-icon-chevron fas fa-chevron-down"></i>

									</div>

								</td>

								<td>

									<span class="atd-icon-team fas fa-trash"></span>

								</td>

							</tr>

							<tr class="atd-team-row">

								<td>

									<div class="atd-input-field">

										<input type="text" class="atd-input-text atd-team-names-list" name="atd-team-names-list[]" placeholder="Enter Name">

									</div>

								</td>

								<td>

									<div class="atd-input-field">

										<input type="number" class="atd-input-number atd-team-numbers-list"  name="atd-team-numbers-list[]" placeholder="00" min="0">

									</div>

								</td>

								<td>

									<div class="atd-input-select-field">

										<?php if ( isset( $sizes ) && ! empty( $sizes ) ) { ?>

											<select class="atd-select atd-team-sizes-list" name="atd-team-sizes-list[]">

												<?php foreach ( $sizes as $key => $size ) { ?>

													<option value="<?php echo esc_attr( $key ); ?>" class="atd-option"><?php echo esc_attr( $size ); ?></option>

												<?php } ?>

											</select>

										<?php } ?>

										<i class="atd-icon-chevron fas fa-chevron-down"></i>

									</div>

								</td>

								<td>

									<span class="atd-icon-team fas fa-trash"></span>

								</td>

							</tr>

							<tr class="atd-team-row">

								<td>

									<div class="atd-input-field">

										<input type="text" class="atd-input-text atd-team-names-list" name="atd-team-names-list[]" placeholder="Enter Name">

									</div>

								</td>

								<td>

									<div class="atd-input-field">

										<input type="number" class="atd-input-number atd-team-numbers-list"  name="atd-team-numbers-list[]" placeholder="00" min="0">

									</div>

								</td>

								<td>

									<div class="atd-input-select-field">

										<?php if ( isset( $sizes ) && ! empty( $sizes ) ) { ?>

											<select class="atd-select atd-team-sizes-list" name="atd-team-sizes-list[]">

												<?php foreach ( $sizes as $key => $size ) { ?>

													<option value="<?php echo esc_attr( $key ); ?>" class="atd-option"><?php echo esc_attr( $size ); ?></option>

												<?php } ?>

											</select>

										<?php } ?>

										<i class="atd-icon-chevron fas fa-chevron-down"></i>

									</div>

								</td>

								<td>

									<span class="atd-icon-team fas fa-trash"></span>

								</td>

							</tr>

						</tbody>

					</table>

					<div class="atd-team-container-btn">

						<div class="atd-team-btn-more">

							<button type="button" class="atd-btn-team-action"><i class="fas fa-plus-circle"></i> Add more</button>

						</div>

						<div class="atd-team-btn-done">

							<button type="button" class="atd-btn-team-action"><i class="fas fa-check-circle"></i> Done</button>

						</div>

					</div>

				</div>

			</div>

			<div class="wb-totals-team-inner">

				<div class="wb-totals-team-wrap">

					<div class="atd-totals-team">

						<p class="atd-totals-team-text">

							<?php echo esc_html__( 'Totals :', 'allada-tshirt-designer-for-woocommerce' ); ?>
							<span class="atd-total-name atd-badge"><?php echo esc_html__( '00', 'allada-tshirt-designer-for-woocommerce' ); ?></span>
							<span class="atd-total-name-label"> <?php echo esc_html__( 'names', 'allada-tshirt-designer-for-woocommerce' ); ?> </span> 
							<?php echo esc_html__( 'and', 'allada-tshirt-designer-for-woocommerce' ); ?>
							<span class="atd-total-number atd-badge"><?php echo esc_html__( '00', 'allada-tshirt-designer-for-woocommerce' ); ?></span>
							<span class="atd-total-number-label"> <?php echo esc_html__( 'numbers', 'allada-tshirt-designer-for-woocommerce' ); ?> </span> 
							<?php echo esc_html__( 'on', 'allada-tshirt-designer-for-woocommerce' ); ?>
							<span class="atd-total-item atd-badge"><?php echo esc_html__( '00', 'allada-tshirt-designer-for-woocommerce' ); ?></span>
							<span class="atd-total-item-label"> <?php echo esc_html__( 'items', 'allada-tshirt-designer-for-woocommerce' ); ?> </span> 

						</p>

					</div>

					<div class="atd-totals-team-variations"><?php echo esc_html__( 'Sizes : ', 'allada-tshirt-designer-for-woocommerce' ); ?><span class="wb-item-variation-text"></span></div>

				</div>

			</div>

		</div>

		<div class="atd-shadow-team"></div>
		<script>
			var team_size_details = [];
			team_size_details[<?php echo esc_attr( $product_id ); ?>] = <?php echo wp_json_encode( $var_sizes ); ?>;
		</script>
		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get preview modal save.
	 *
	 * @return Sting $output The output.
	 */
	private function get_preview_modal_save() {

		ob_start();

		global $current_user;

		$user_designs = get_user_meta( $current_user->ID, 'atd_saved_designs' );
		if ( isset( $user_designs ) && ! empty( $user_designs ) && is_array( $user_designs ) ) {
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

					<div class="atd-preview-box-saved" data-name="<?php echo esc_attr( $design_name ); ?>">

						<div class="atd-preview-title"><?php echo esc_html__( 'Saved Designs', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

						<div class="atd-icon-des-cross"><i class="fas fa-times"></i></div>

						<div class="atd-preview-saved-inner owl-carousel">

							<?php
							if ( is_array( $design_data ) ) {
								$tmp_dir = $design_data['output']['working_dir'];
								$datas   = $design_data['output']['files'];
								foreach ( $datas as $data_key => $data ) {
									if ( ! empty( $data ) ) {
										$generation_url        = ATD_SAVED_DESIGN_UPLOAD_URL . "/$tmp_dir/$data_key/";
										$img_src               = $generation_url . $data['image'];
										$original_part_img_url = $design_data[ $data_key ]['original_part_img'];
										?>

										<div class="atd-preview-saved-item">

											<header><?php echo esc_html__( "$data_key", 'allada-tshirt-designer-for-woocommerce' ); ?></header>

											<div class="atd-preview-saved-img-container">

												<div data-name="<?php echo esc_attr( $data_key ); ?>" data-variation-id="<?php echo esc_attr( $variation_id ); ?>" data-save-time="<?php echo esc_attr( $save_time ); ?>" data-design-name="<?php echo esc_attr( $design_name ); ?>" data-order-item-id="<?php echo esc_attr( $order_item_id ); ?>" style="background-image:url('<?php echo esc_attr( $original_part_img_url ); ?>')" class="my-design-bg-image atd-preview-saved-img">
													<img src="<?php echo esc_attr( $img_src ); ?>" alt="part">
												</div>

											</div>

										</div>
										<?php
									}
								}
								?>

							</div>

							<div class="atd-btns-saved">
								<?php
								$atd_product = new ATD_Product( $variation_id );
								if ( $order_item_id ) {
									$btn_load_url = $atd_product->get_design_url( false, false, $order_item_id );
									?>
									<a class="atd-btn-effect atd-btn-saved atd-btn-load" href="<?php echo esc_attr( $btn_load_url ); ?>" > <?php echo esc_html__( 'Load', 'allada-tshirt-designer-for-woocommerce' ); ?> </a>
									<?php
								} else {
									$btn_load_url = $atd_product->get_design_url( $s_index );
									?>
									<a class="atd-btn-effect atd-btn-saved atd-btn-load" href="<?php echo esc_attr( $btn_load_url ); ?>" > Load </a>
									<a class="atd-btn-effect atd-delete-design atd-btn-saved atd-btn-delete" data-index="<?php echo esc_attr( $s_index ); ?>"> Delete </a>
									<?php
								}
								?>

							</div>

						</div>
								<?php
							}
				}
			}
		}
		?>

		<div class="atd-shadow"></div>

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get preview modal debug.
	 *
	 * @return Sting $output The output.
	 */
	private function get_preview_modal_debug() {

		ob_start();

		?>

		<div class="atd-debug-wrap">

			<div class="atd-debug-cart" id="debug">


			</div>

			<div class="atd-debug-icon debug-icon ti-close">

				<i class="fas fa-times"></i>

			</div>

		</div>

		<?php

		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get cart tools.
	 *
	 * @return string $output The output.
	 */
	private function get_cart_tools() {

		global $wp_query;

		$product = wc_get_product( $this->editor->item_id );

		$tpl_price = 0;

		if ( isset( $wp_query->query_vars['tpl'] ) ) {

			$tpl_id = $wp_query->query_vars['tpl'];

			$tpl_price = atd_get_template_price( $tpl_id );
		}

		$add_to_cart_label = esc_html__( 'ADD TO CART', 'allada-tshirt-designer-for-woocommerce' );

		if ( isset( $wp_query->query_vars['edit'] ) ) {

			$add_to_cart_label = esc_html__( 'UPDATE CART ITEM', 'allada-tshirt-designer-for-woocommerce' );
		}

		ob_start();
		?>

		<div class="atd-tab-tools-content" data-title="cart">

			<div class="atd-title"><?php echo esc_html__( 'Cart', 'allada-tshirt-designer-for-woocommerce' ); ?></div>
			<?php
			if ( isset( $this->atd_meta['related-quantities'] ) && ! empty( $this->atd_meta['related-quantities'] ) && $product->get_type() == 'variation' ) {
				$related_attributes = $this->atd_metas['related-quantities'];

				$atd_root_product = new ATD_Product( $this->editor->root_item_id );

				$usable_attributes = $atd_root_product->extract_usable_attributes();

				$variation = wc_get_product( $this->editor->item_id );

				$variation_to_load_tab = atd_get_variation_from_attributes( $variation->get_parent_id() );

				$_SESSION['combinaison'] = array();
				if ( ! empty( $variation_to_load_tab ) ) {
					foreach ( $variation_to_load_tab as $variation_to_load ) {
						$variation_to_load_ob = wc_get_product( $variation_to_load );
						$quantity_display     = '';
						if ( $variation_to_load_ob->is_sold_individually() ) {
							$quantity_display = "style='display: none;'";
						}

						$atd_variation       = new ATD_Product( $variation_to_load );
						$purchase_properties = $atd_variation->get_purchase_properties();
						$selected_attributes = wc_get_product( $variation_to_load )->get_variation_attributes();
						$price               = $variation_to_load_ob->get_price() + $tpl_price;
						$price_html          = ' <span class="total_order atd-cart-price">' . wc_price( $price * $purchase_properties['min_to_purchase'] ) . '</span>';
						$to_search           = $selected_attributes;
						foreach ( $usable_attributes as $attribute_name => $attribute_data ) {
							$attribute_key = $attribute_data['key'];
							if ( in_array( $attribute_key, $related_attributes ) ) {
								$to_search[ $attribute_key ] = array();
								foreach ( $attribute_data['values'] as $attribute_value ) {
									if ( is_object( $attribute_value ) ) {
										$sanitized_value = $attribute_value->slug;
										$label           = $attribute_value->name;
									} else {
										$sanitized_value = sanitize_title( $attribute_value );
										$label           = $attribute_value;
									}
									array_push( $to_search[ $attribute_key ], $label );
								}
							}
						}
						$combinaisons   = array();
						$combinaisons[] = $selected_attributes;
						$combine        = false;
						foreach ( $selected_attributes as $key => $value ) {
							if ( '' === $value && ! empty( $to_search[ $key ] ) ) {
								$combine      = true;
								$combinaisons = atd_make_variation_combine( $key, $to_search[ $key ], $combinaisons );
							}
						}
						// Variation properties
						if ( $combine ) {
							foreach ( $combinaisons as $combinaison ) {
								$array_key = array_keys( $combinaison );
								$end       = end( $array_key );
								$key       = '';
								foreach ( $combinaison as $combinaison_key => $value ) {
									if ( $end !== $combinaison_key ) {
										$key .= $value . '+';
									} else {
										$key .= $value;
									}
								}
								$_SESSION['combinaison'][ $key ] = $combinaison;
								?>

								<div class="atd-qty-container" data-id="<?php echo esc_attr( $variation_to_load ); ?>" <?php echo esc_attr( $quantity_display ); ?>>

									<div class="atd-cart-item">
										<label><?php echo $key; ?></label>
										<span class="atd-badge total-price"><?php echo esc_html( $price_html ); ?></span>


										<input type="button" class="atd-icon-plus atd-icon-btn-item plus atd-custom-right-quantity-input-set" value="+">

										<input type="number" variation_name="<?php echo esc_attr( $key ); ?>" step="<?php echo esc_attr( $purchase_properties['step'] ); ?>" class="atd-cart-qty atd-qty atd-custom-right-quantity-input" value="<?php echo esc_attr( $purchase_properties['min_to_purchase'] ); ?>" min="<?php echo esc_attr( $purchase_properties['min'] ); ?>" max="<?php echo esc_attr( $purchase_properties['max'] ); ?>" uprice="<?php echo esc_attr( $price ); ?>" >


										<input type="button" class="atd-icon-minus atd-icon-btn-item minus atd-custom-right-quantity-input-set" value="-">



									</div>
								</div>
								<?php
							}
						} else {
							$variation_to_load_attributes = $variation_to_load_ob->get_variation_attributes();
							$attribute_str                = '';

							foreach ( $variation_to_load_attributes as $variation_to_load_attribute_key => $variation_to_load_attribute ) {
								if ( in_array( $variation_to_load_attribute_key, $related_attributes, true ) ) {
									if ( ! empty( $attribute_str ) && '' !== $variation_to_load_attribute ) {
										$attribute_str .= '+';
									}
									$attribute_str .= $variation_to_load_attribute;
								}
							}
							$_SESSION['combinaison'][ $key ] = $combinaison;
							?>
							<div class="atd-qty-container" data-id="<?php echo esc_attr( $variation_to_load ); ?>" <?php echo esc_attr( $quantity_display ); ?>>

								<div class="atd-cart-item">

									<label><?php echo esc_attr( $attribute_str ); ?></label>

									<span class="atd-badge total-price"><?php echo esc_attr( $price_html ); ?></span>

									<input type="button" class="atd-icon-plus atd-icon-btn-item plus atd-custom-right-quantity-input-set" value="+">

									<input type="number" variation_name="<?php echo esc_attr( $attribute_str ); ?>" step="<?php echo esc_attr( $purchase_properties['step'] ); ?>" class="atd-cart-qty atd-qty atd-custom-right-quantity-input" value="<?php echo esc_attr( $purchase_properties['min_to_purchase'] ); ?>" min="<?php echo esc_attr( $purchase_properties['min'] ); ?>" max="<?php echo esc_attr( $purchase_properties['max'] ); ?>" uprice="<?php echo esc_attr( $price ); ?>" >


									<input type="button" class="atd-icon-minus atd-icon-btn-item minus atd-custom-right-quantity-input-set" value="-">

								</div>
							</div>
							<?php
						}
					}
				}
			} else {
				$purchase_properties = $this->editor->atd_product->get_purchase_properties();
				$price               = $product->get_price() + $tpl_price;
				$price_html          = ' <span class="total_order atd-cart-price">' . wc_price( $price * $purchase_properties['min_to_purchase'] ) . '</span>';
				$custom_qty          = filter_input( INPUT_GET, 'custom_qty' );
				$custom_quantity     = isset( $custom_qty ) ? $custom_qty : $purchase_properties['min_to_purchase'];
				$quantity_display    = '';
				if ( $product->is_sold_individually() ) {
					$quantity_display = "style='display: none;'";
				}
				?>

				<div class="atd-qty-container" data-id="<?php echo esc_attr( $this->editor->item_id ); ?>" <?php echo esc_attr( $quantity_display ); ?>>

					<div class="atd-cart-item">
						<span class="atd-badge total-price"><?php echo esc_attr( $price_html ); ?></span>

						<input type="button" class="atd-icon-plus atd-icon-btn-item plus atd-custom-right-quantity-input-set" value="+">

						<input type="number" step="<?php echo esc_attr( $purchase_properties['step'] ); ?>" class="atd-cart-qty atd-qty atd-custom-right-quantity-input" value="<?php echo esc_attr( $custom_quantity ); ?>" min="<?php echo esc_attr( $purchase_properties['min'] ); ?>" max="<?php echo esc_attr( $purchase_properties['max'] ); ?>" uprice="<?php echo esc_attr( $price ); ?>" >

						<input type="button" class="atd-icon-minus atd-icon-btn-item minus atd-custom-right-quantity-input-set" value="-">

					</div>
				</div>
				<?php
			}
			?>
			<div class="atd-btn-cart-config">

				<button type="button" class="atd-btn-cart-self" id="add-to-cart-btn"><?php echo esc_attr( $add_to_cart_label ); ?></button>

			</div>

		</div>

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get preview modal.
	 *
	 * @return Sting $output The output.
	 */
	private function get_preview_modal() {
		ob_start();
		?>
		<div class="omodal fade o-modal atd-modal" id="atd-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="omodal-dialog">
				<div class="omodal-content">
					<div class="omodal-header">
						<button type="button" class="close ti-close" data-dismiss="omodal" aria-hidden="true"></button>
						<h4 class="omodal-title" id="myModalLabel">PREVIEW</h4>
					</div>
					<div class="omodal-body txt-center">
					</div>
				</div>
			</div>
		</div>  
		<?php
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Get design tools.
	 *
	 * @return Sting $output The output.
	 */
	private function get_design_tools() {

		ob_start();

		global $current_user;

		$user_designs = get_user_meta( $current_user->ID, 'atd_saved_designs' );

		$user_orders_designs = atd_get_user_orders_designs( $current_user->ID );
		?>
		  

		<div class="atd-title"><?php echo esc_html__( 'My Designs', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

		<?php
		if ( is_user_logged_in() ) {
			?>

			<div class="atd-my-design-tabs">

				<li class="atd-my-design-tab-item atd-active" data-title="saved-design"><?php echo esc_html__( 'Saved Designs', 'allada-tshirt-designer-for-woocommerce' ); ?></li>

				<li class="atd-my-design-tab-item" data-title="past-order"><?php echo esc_html__( 'Past Orders', 'allada-tshirt-designer-for-woocommerce' ); ?></li>

				<div class="atd-underline-design"></div>

			</div>

			<div class="atd-my-design-container">

				<div class="atd-my-design-item atd-my-design-saved" data-title="saved-design">

					<?php echo $this->get_user_design_output_block( $user_designs ); ?>

				</div>

				<div class="atd-my-design-item atd-my-design-past" data-title="past-order"></div>

			</div>

			<?php
		} else {
			?>

			<p><?php echo esc_html__( 'You need to be logged in before loading your designs.', 'allada-tshirt-designer-for-woocommerce' ); ?></p>

			<?php
		}
		?>

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get user design output block.
	 *
	 * @param type $user_designs The user design.
	 * @return type The output.
	 */
	private function get_user_design_output_block( $user_designs ) {
		ob_start();
		?>
		<div>
			<?php
			if ( ! empty( $user_designs ) ) {
				foreach ( $user_designs as $s_index => $user_design ) {
					if ( ! empty( $user_design ) ) {
						$variation_id  = $user_design[0];
						$save_time     = $user_design[1];
						$design_name   = $user_design[2];
						$order_item_id = '';
						if ( count( $user_design ) >= 5 ) {
							$order_item_id = $user_design[4];
						}
						?>
						<li class="atd-my-design-saved-item" data-name="<?php echo esc_attr( $design_name ); ?>"> 
							<div class="atd_order_item" data-variation-id="<?php echo esc_attr( $variation_id ); ?>" data-save-time="<?php echo esc_attr( $save_time ); ?>" data-design-name="<?php echo esc_attr( $design_name ); ?>" data-order-item-id="<?php echo esc_attr( $order_item_id ); ?>"><div><?php echo esc_attr( $design_name ) . ' - ' . esc_attr( $save_time ); ?></div></div>
						</li>                                
						<?php
					}
				}
			}
			?>
		</div>
		<?php
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Get preview modal design.
	 *
	 * @return Sting $output The output.
	 */
	private function get_preview_modal_design() {

		ob_start();
		?>

		<div class="atd-preview-box-prev-des">

			<div class="atd-preview-title"><?php echo esc_html__( 'Preview Designs', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

			<div class="atd-icon-prev-cross"><i class="fas fa-times"></i></div>

			<div class="atd-preview-prev-des-inner owl-carousel">

				

			</div>

		</div>

		<div class="atd-shadow-prev-des"></div>

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get preview modal saved.
	 *
	 * @return Sting $output The output.
	 */
	private function get_preview_modal_saved() {

		ob_start();

		global $wp_query;

		$design_index = -1;

		if ( isset( $wp_query->query_vars['design_index'] ) ) {
			$design_index = $wp_query->query_vars['design_index'];
		}
		?>

		<div class="atd-preview-box-save">

			<div class="atd-preview-title"><?php echo esc_html__( 'Save Your Designs', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

			<div class="atd-icon-save-cross"><i class="fas fa-times"></i></div>

			<?php
			if ( isset( $design_index ) && $design_index >= 0 ) {
				?>
				<p><?php echo esc_html__( 'You try to make a backup from another save, Would you like to overwrite the previous backup by this one?', 'allada-tshirt-designer-for-woocommerce' ); ?></p>
				<div class="atd-btns-save">
					<button id="save-btn" class="atd-btn-save atd-btn-replace" data-index="<?php echo esc_attr( $design_index ); ?>" data-action="replace"><?php echo esc_html__( 'Replace', 'allada-tshirt-designer-for-woocommerce' ); ?></button>
					<button id="save-btn" class="atd-btn-save atd-btn-replace" data-index="<?php echo esc_attr( $design_index ); ?>" data-action="new-save"><?php echo esc_html__( 'New Save', 'allada-tshirt-designer-for-woocommerce' ); ?></button>
				</div>

				<?php
			} else {
				?>
				<div class="atd-input-field">

					<input type="text" id="design-name" class="atd-input-text" placeholder="Design Name" required>

				</div>

				<div class="atd-btns-save">

					<button class="atd-btn-save atd-btn-save-save" data-index="<?php echo esc_attr( $design_index ); ?>" id="save-btn"><?php echo esc_html__( 'Save', 'allada-tshirt-designer-for-woocommerce' ); ?></button>

				</div>

				<?php
			}
			?>

		</div>

		<div class="atd-shadow-save"></div>

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get preview modal Add Product Information.
	 *
	 * @return Sting $output The output.
	 */
	private function get_preview_modal_add_product_information() {

		ob_start();

		?>

		<div class="atd-preview-box-add-product-information">

		<div class="atd-preview-title"><?php echo esc_html__( 'Add Product Information', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

		<div class="atd-icon-add-product-information-cross"><i class="fas fa-times"></i></div>

		<div class="atd-add-product-information-content">

			<div class="atd-add-product-information-text"><?php echo esc_html__( 'Our Design Lab can’t place a multiple color/item order with names and numbers.', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

			<a href="#" class="atd-add-product-information-link"><?php echo esc_html__( 'Remove names and numbers.', 'allada-tshirt-designer-for-woocommerce' ); ?></a>

		</div>

		</div>

		<div class="atd-shadow-add-product-information"></div>

		<?php

		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get preview modal Team Information.
	 *
	 * @return Sting $output The output.
	 */
	private function get_preview_modal_team_information() {

		ob_start();

		?>

		<div class="atd-preview-box-add-team-information">

			<div class="atd-preview-title"><?php echo esc_html__( 'Add Team Information', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

			<div class="atd-icon-add-team-information-cross"><i class="fas fa-times"></i></div>

			<div class="atd-add-team-information-content">

				<div class="atd-add-team-information-text"><?php echo esc_html__( 'Our Design Lab can’t place a multiple color/item order with names and numbers.', 'allada-tshirt-designer-for-woocommerce' ); ?></div>

				<a href="#" class="atd-add-team-information-link"><?php echo esc_html__( 'Remove extra colors and items.', 'allada-tshirt-designer-for-woocommerce' ); ?></a>

			</div>
				
		</div>

		<div class="atd-shadow-add-team-information"></div>

		<?php

		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Get product team data.
	 *
	 * @return array $team_data The team data.
	 */
	private function get_product_team_data() {
		return $this->atd_metas['team-settings'];
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	private function register_styles() {
		wp_enqueue_style( 'atd-allada', ATD_URL . 'includes/skins/default/assets/css/atd-allada.css', array(), ATD_VERSION, 'all' );
		wp_enqueue_style( 'atd-font-awesome', ATD_URL . 'includes/skins/default/assets/css/atd-font.awesome.min.css', array(), ATD_VERSION, 'all' );
		wp_enqueue_style( 'atd-line-icons', ATD_URL . 'includes/skins/default/assets/css/line-icons.css', array(), ATD_VERSION, 'all' );
		wp_enqueue_style( 'atd-icons', ATD_URL . 'includes/skins/default/assets/css/atd-icons.css', array(), ATD_VERSION, 'all' );
		wp_enqueue_style( 'atd-spectrum', ATD_URL . 'includes/skins/default/assets/css/atd-spectrum.min.css', array(), ATD_VERSION, 'all' );
		wp_enqueue_style( 'atd-perfect-scrollbar', ATD_URL . 'includes/skins/default/assets/css/atd-perfect-scrollbar.min.css', array(), ATD_VERSION, 'all' );
		wp_enqueue_style( 'atd-carousel', ATD_URL . 'includes/skins/default/assets/css/atd-carousel.min.css', array(), ATD_VERSION, 'all' );
		atd_register_fonts();
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 */
	private function register_scripts() {
		wp_enqueue_script( 'atd-allada', ATD_URL . 'includes/skins/default/assets/js/atd-allada.js', array( 'jquery' ), ATD_VERSION, false );
		wp_enqueue_script( 'atd-fabric-js', ATD_URL . 'includes/skins/default/assets/js/fabric.all.min.js', array(), ATD_VERSION, false );
		wp_enqueue_script( 'atd-accounting-js', ATD_URL . 'includes/skins/default/assets/js/accounting.min.js', array(), ATD_VERSION, false );
		wp_enqueue_script( 'atd-editor-js', ATD_URL . 'includes/skins/default/assets/js/editor.js', array(), ATD_VERSION, false );
		wp_enqueue_script( 'atd-editor-text-js', ATD_URL . 'includes/skins/default/assets/js/editor.text.js', array(), ATD_VERSION, false );
		wp_enqueue_script( 'atd-ui-widget-js', ATD_URL . 'includes/skins/default/assets/js/atd.ui.widget.min.js', array(), ATD_VERSION, false );
		wp_enqueue_script( 'atd-iframe-transport-js', ATD_URL . 'includes/skins/default/assets/js/atd.iframe.transport.min.js', array(), ATD_VERSION, false );
		wp_enqueue_script( 'atd-fileupload-js', ATD_URL . 'includes/skins/default/assets/js/atd.fileupload.min.js', array(), ATD_VERSION, false );
		wp_enqueue_script( 'atd-editor-img-js', ATD_URL . 'includes/skins/default/assets/js/editor.img.js', array(), ATD_VERSION, false );
		wp_enqueue_script( 'atd-wp-js-hooks-js', ATD_URL . 'includes/skins/default/assets/js/wp-js-hooks.min.js', array(), ATD_VERSION, false );
		wp_enqueue_script( 'atd-lodash-js', ATD_URL . 'includes/skins/default/assets/js/lodash.js', array(), ATD_VERSION, false );
		wp_enqueue_script( 'atd-jquery-serializejson-js', ATD_URL . 'includes/skins/default/assets/js/jquery.lazyload.min.js', array(), ATD_VERSION, false );
		wp_enqueue_script( 'atd-jquery-lazyload-js', ATD_URL . 'includes/skins/default/assets/js/jquery.serializejson.js', array(), ATD_VERSION, false );
		wp_enqueue_script( 'atd-jquery-form-js', ATD_URL . 'includes/skins/default/assets/js/jquery.form.js', array(), ATD_VERSION, false );
		wp_enqueue_script( 'atd-editor-toolbar-js', ATD_URL . 'includes/skins/default/assets/js/editor.toolbar.js', array(), ATD_VERSION, false );
		// wp_enqueue_script('atd-font-awesome-js', ATD_URL . 'includes/skins/default/assets/js/atd.font.awesome.min.js', array(), ATD_VERSION, false);
		wp_enqueue_script( 'atd-spectrum', ATD_URL . 'includes/skins/default/assets/js/atd.spectrum.min.js', array(), ATD_VERSION, false );
		wp_enqueue_script( 'atd-perfect-scrollbar-js', ATD_URL . 'includes/skins/default/assets/js/atd.perfect.scrollbar.min.js', array(), ATD_VERSION, false );
		wp_enqueue_script( 'atd-carousel-js', ATD_URL . 'includes/skins/default/assets/js/atd.carousel.min.js', array(), ATD_VERSION, false );

	}

}
