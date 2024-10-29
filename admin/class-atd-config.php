<?php
/**
 * The atd config functionality of the plugin.
 *
 * @link       orionorigin@orionorigin.com
 * @since      1.0.0
 *
 * @package    Atd
 * @subpackage Atd/admin
 */

/**
 * Contains all methods and hooks callbacks related to the user design.
 *
 * @author HL
 *
 * @package    Atd
 * @subpackage Atd/admin
 * @author     orionorigin <orionorigin@orionorigin.com>
 */
class ATD_Config {

	/**
	 * Get the link of duplicate post
	 *
	 * @param array  $actions The array of differents links.
	 * @param object $post The post.
	 */
	public function get_duplicate_post_link( $actions, $post ) {
		if ( 'atd-config' === $post->post_type && current_user_can( 'edit_posts' ) ) {
			$actions['duplicate'] = '<a href="admin.php?action=atd_duplicate_config&amp;post=' . $post->ID . '&amp;duplicate_nonce=' . wp_create_nonce( basename( __FILE__ ) ) . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
		}
		return $actions;
	}

	/**
	 * Function creates post duplicate as a draft and redirects then to the edit post screen.
	 */
	public function atd_duplicate_config() {
		global $wpdb;
		if ( ! ( isset( $_GET['post'] ) || isset( $_POST['post'] ) || ( isset( $_REQUEST['action'] ) && 'atd_duplicate_config' === wp_verify_nonce( sanitize_key( $_REQUEST['action'] ) ) ) ) ) {
			wp_die( 'No post to duplicate has been supplied!' );
		}

		/*
		 * Nonce verification
		 */
		if ( ! isset( $_GET['duplicate_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_GET['duplicate_nonce'] ), basename( __FILE__ ) ) ) {
			return;
		}

		/*
		 * get the original post id
		 */
		$post_id = ( isset( $_GET['post'] ) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );

		/*
		 * and all the original post data then
		 */
		$post = get_post( $post_id );

		/*
		 * if you don't want current user to be the new post author,
		 * then change next couple of lines to this: $new_post_author = $post->post_author;
		 */
		$current_user    = wp_get_current_user();
		$new_post_author = $current_user->ID;

		/*
		 * if post data exists, create the post duplicate
		 */
		if ( isset( $post ) && null !== $post ) {
			/*
			 * new post data array
			 */
			$args = array(
				'comment_status' => $post->comment_status,
				'ping_status'    => $post->ping_status,
				'post_author'    => $new_post_author,
				'post_content'   => $post->post_content,
				'post_excerpt'   => $post->post_excerpt,
				'post_name'      => $post->post_name,
				'post_parent'    => $post->post_parent,
				'post_password'  => $post->post_password,
				'post_status'    => 'draft',
				'post_title'     => $post->post_title . __( ' - copy', 'allada-tshirt-designer-for-woocommerce' ),
				'post_type'      => $post->post_type,
				'to_ping'        => $post->to_ping,
				'menu_order'     => $post->menu_order,
			);

			/*
			 * insert the post by wp_insert_post() function
			 */
			$new_post_id = wp_insert_post( $args );

			/*
			 * get all current post terms ad set them to the new post draft
			 */
			$taxonomies = get_object_taxonomies( $post->post_type ); // returns array of taxonomy names for post type, ex array("category", "post_tag").
			foreach ( $taxonomies as $taxonomy ) {
				$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
				wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
			}

			/*
			 * duplicate all post meta just in two SQL queries
			 */
			$db              = $wpdb;
			$post_meta_infos = $db->get_results( "SELECT meta_key, meta_value FROM $db->postmeta WHERE post_id=$post_id" );
			if ( 0 !== count( $post_meta_infos ) ) {
				$sql_query = "INSERT INTO $db->postmeta (post_id, meta_key, meta_value) ";
				foreach ( $post_meta_infos as $meta_info ) {
					$meta_key = $meta_info->meta_key;
					if ( '_wp_old_slug' === $meta_key ) {
						continue;
					}
					$meta_value      = addslashes( $meta_info->meta_value );
					$sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
				}
				$sql_query .= implode( ' UNION ALL ', $sql_query_sel );
				$db->query( $sql_query );
			}

			/*
			 * finally, redirect to the edit post screen for the new draft
			 */
			wp_safe_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
			exit;
		} else {
			echo esc_html( 'Post creation failed, could not find original post: ' . $post_id );
			wp_die();
		}
	}

	/**
	 * Display the tshirt-configuartion tab label
	 *
	 * @param type $tabs the variable who content the all information to create a tab.
	 * @return array
	 */
	public function add_tshirt_configuration_tab_label( $tabs ) {
		$tabs['atd_configuration'] = array(
			'label'    => 'T-shirt configuration',
			'target'   => 'atd_tshirt_configuration',
			'class'    => array( 'show_if_variable'),
			'priority' => 65,
		);
		return $tabs;
	}

	/**
	 * Return the atd metas value from produc_id
	 *
	 * @param type $product_id the product id on the page.
	 * @return boolean
	 */
	public function get_meta_value( $product_id ) {
		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return false;
		}

		if ( 'variation' !== $product->get_type() ) {
			$root_product_id = $product_id;
		} else {
			$root_product_id = $product->get_parent_id();
		}

		$meta_value = get_post_meta( $root_product_id, 'atd-metas', true );
		if ( empty( $meta_value ) ) {
			return false;
		}

		return $meta_value;
	}

	/**
	 * Create the select input for configuration
	 *
	 * @param type $configs the qll configs id.
	 * @param type $product_id the product id on the page.
	 * @return type
	 */
	public function build_configs_dropdown( $configs, $product_id = '' ) {
		ob_start();
		$config_id  = '';
		$meta_value = $this->get_meta_value( $product_id );

		if ( isset( $meta_value[ $product_id ]['config-id'] ) ) {
			$config_id = $meta_value[ $product_id ]['config-id'];
		}
		?>
<select name="atd-metas[<?php echo esc_attr($product_id); ?>][config-id]" class="atd_variation_actions atd_target_variation" style="max-width: 50%;width: 40%;">
			<option data-global="true" value=""><?php esc_html_e( 'None', 'allada-tshirt-designer-for-woocommerce' ); ?></option>
			<?php
			if ( isset( $configs ) && ! empty( $configs ) ) {
				foreach ( $configs as $key ) {
					?>
					<option data-global="true" <?php echo ( $config_id === $key->ID ) ? 'selected' : ''; ?> value="<?php echo esc_attr($key->ID); ?>"><?php esc_html_e( $key->post_title, 'allada-tshirt-designer-for-woocommerce' ); ?></option>
					<?php
				}
			}
			?>
		</select>
		<?php
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Display part config depending on the config id
	 *
	 * @param type $variation_id the variation or the product id.
	 * @return type
	 */
	public function display_part_config( $variation_id ) {
		$product= wc_get_product($variation_id);

		if($product->get_type()=="variation")$product_id = $product->get_parent_id();
		else $product_id=$variation_id;

		$atd_part_bg_src = atd_get_part_image($product_id, $variation_id, "bg-inc");
		$atd_part_icon_src = atd_get_part_image($product_id, $variation_id, "icon");

		foreach ($atd_part_bg_src as $key => $value) {
			$part_image[strtolower($key)]["bg-inc"]=atd_o_get_proper_image_url($value);
		}
		foreach ($atd_part_icon_src as $key => $value) {
			$part_image[strtolower($key)]["icon"]=atd_o_get_proper_image_url($value);
		}

		ob_start();
		?>

		<div class="woocommerce_variable_attributes wc-metabox-content" style="display: none;">
			<div class="data" style="display: flex;flex-wrap: wrap;">
				<div class="atd-data-item atd-front" style="width: calc(100% / 2 - 10px);margin-bottom: 30px;">
					<header><strong style="font-size: 18px;font-weight: 700;">Front Side</strong></header>
					<div class="atd-data-container" style="display: flex;">
						<div class="upload_image" style="width: calc(100% / 2);">
							<p><strong>Icon</strong></p>
							<a href="#" class="upload_image_button 
							<?php
							if (!empty( $part_image['front']['icon']) ) {
								echo 'atd-remove-media remove';
							} else {
								echo 'atd-add-media tips';
							}
							?>
							atd-media-preview" data-tip="" >
							   <?php
								if (!empty( $part_image['front']['icon'] ) ) {
									echo ("<img src='" . esc_attr($part_image['front']['icon']) . "'>");
								}
								?>
                                                            <input type="hidden" name="atd-metas[<?php echo esc_attr($variation_id); ?>][front][icon]" value="<?php
								if ( !empty( $part_image['front']['icon'] ) ) {
									echo esc_attr($atd_part_icon_src["Front"]);
								}
								?>">
							</a>

						</div>
						<div class="upload_image" style="width: calc(100% / 2);">
							<p><strong>Background</strong></p>
							<a href="#" class="upload_image_button 
							<?php
							if ( !empty( $part_image['front']['bg-inc'] ) ) {
								echo 'atd-remove-media remove';
							} else {
								echo 'atd-add-media tips';
							}
							?>
							atd-media-preview" data-tip="" >
							<?php
							if ( !empty( $part_image['front']['bg-inc'] ) ) {
								echo "<img src='" . esc_attr($part_image['front']['bg-inc']) . "'>";
							}
							?>
                                                            <input type="hidden" name="atd-metas[<?php echo esc_attr($variation_id); ?>][front][bg-inc]" value="<?php
								if ( !empty( $part_image['front']['bg-inc'] ) ) {
									echo $atd_part_bg_src["Front"];
								}
								?>">
							</a>                            
						</div>
					</div>
				</div>
				<div class="atd-data-item atd-back" style="width: calc(100% / 2 - 10px);margin-bottom: 30px;">
					<header><strong style="font-size: 18px;font-weight: 700;">Back Side</strong></header>
					<div class="atd-data-container" style="display: flex;">
						<div class="upload_image" style="width: calc(100% / 2);">
							<p><strong>Icon</strong></p>
							<a href="#" class="upload_image_button 
							<?php
							if ( !empty( $part_image['back']['icon'] ) ) {
								echo 'atd-remove-media remove';
							} else {
								echo 'atd-add-media tips';
							}
							?>
							atd-media-preview" data-tip="" >
							<?php
							if ( !empty( $part_image['back']['icon'] ) ) {
								echo "<img src='" . $part_image['back']['icon'] . "'>";
							}
							?>
								<input type="hidden" name="atd-metas[<?php echo $variation_id; ?>][back][icon]" value="<?php
								if ( !empty( $part_image['back']['icon'] ) ) {
									echo $atd_part_icon_src["Back"];
								}
								?>">
							</a>  

						</div>
						<div class="upload_image" style="width: calc(100% / 2);">
							<p><strong>Background</strong></p>
							<a href="#" class="upload_image_button 
							<?php
							if ( !empty( $part_image['back']['bg-inc'] ) ) {
								echo 'atd-remove-media remove';
							} else {
								echo 'atd-add-media tips';
							}
							?>
							   atd-media-preview" data-tip="" >
							   <?php
								if ( !empty( $part_image['back']['bg-inc'] ) ) {
									echo "<img src='" . $part_image['back']['bg-inc'] . "'>";
								}
								?>
								<input type="hidden" name="atd-metas[<?php echo $variation_id; ?>][back][bg-inc]" value="<?php
								if ( !empty( $part_image['back']['bg-inc'] ) ) {
									echo $atd_part_bg_src['Back'];
								}
								?>">
							</a> 
						</div>
					</div>
				</div>
				<div class="atd-data-item atd-left" style="width: calc(100% / 2 - 10px);margin-bottom: 30px;">
					<header><strong style="font-size: 18px;font-weight: 700;">Left Side</strong></header>
					<div class="atd-data-container" style="display: flex;">
						<div class="upload_image" style="width: calc(100% / 2);">
							<p><strong>Icon</strong></p>
							<a href="#" class="upload_image_button 
							<?php
							if ( !empty( $part_image['left']['icon'] ) ) {
								echo 'atd-remove-media remove';
							} else {
								echo 'atd-add-media tips';
							}
							?>
							   atd-media-preview" data-tip="" >
							   <?php
								if ( !empty( $part_image['left']['icon'] ) ) {
									echo "<img src='" . $part_image['left']['icon'] . "'>";
								}
								?>
								<input type="hidden" name="atd-metas[<?php echo $variation_id; ?>][left][icon]" value="<?php
								if ( isset( $part_image['left']['icon'] ) ) {
									echo $atd_part_icon_src["Left"];
								}
								?>">
							</a>

						</div>
						<div class="upload_image" style="width: calc(100% / 2);">
							<p><strong>Background</strong></p>
							<a href="#" class="upload_image_button 
							<?php
							if ( !empty( $part_image['left']['bg-inc'] ) ) {
								echo 'atd-remove-media remove';
							} else {
								echo 'atd-add-media tips';
							}
							?>
							   atd-media-preview" data-tip="" >
							   <?php
								if ( !empty( $part_image['left']['bg-inc'] ) ) {
									echo "<img src='" . $part_image['left']['bg-inc'] . "'>";
								}
								?>
								<input type="hidden" name="atd-metas[<?php echo $variation_id; ?>][left][bg-inc]" value="<?php
								if ( !empty( $part_image['left']['bg-inc'] ) ) {
									echo $atd_part_bg_src["Left"];
								}
								?>">
							</a>

						</div>
					</div>
				</div>
				<div class="atd-data-item atd-right" style="width: calc(100% / 2 - 10px);margin-bottom: 30px;">
					<header><strong style="font-size: 18px;font-weight: 700;">Right Side</strong></header>
					<div class="atd-data-container" style="display: flex;">
						<div class="upload_image" style="width: calc(100% / 2);">
							<p><strong>Icon</strong></p>
							<a href="#" class="upload_image_button 
							<?php
							if ( !empty( $part_image['right']['icon'] ) ) {
								echo 'atd-remove-media remove';
							} else {
								echo 'atd-add-media tips';
							}
							?>
							   atd-media-preview" data-tip="" >
							   <?php
								if ( !empty( $part_image['right']['icon'] ) ) {
									echo "<img src='" . $part_image['right']['icon'] . "'>";
								}
								?>
								<input type="hidden" name="atd-metas[<?php echo $variation_id; ?>][right][icon]" value="<?php
								if ( !empty( $part_image['right']['icon'] ) ) {
									echo $atd_part_icon_src["Right"];
								}
								?>">
							</a>

						</div>
						<div class="upload_image" style="width: calc(100% / 2);">
							<p><strong>Background</strong></p>
							<a href="#" class="upload_image_button 
							<?php
							if ( !empty( $part_image['right']['bg-inc'] ) ) {
								echo 'atd-remove-media remove';
							} else {
								echo 'atd-add-media tips';
							}
							?>
							   atd-media-preview" data-tip="" >
							   <?php
								if ( !empty( $part_image['right']['bg-inc'] ) ) {
									echo "<img src='" . $part_image['right']['bg-inc'] . "'>";
								}
								?>
								<input type="hidden" name="atd-metas[<?php echo $variation_id; ?>][right][bg-inc]" value="<?php
								if ( !empty( $part_image['right']['bg-inc'] ) ) {
									echo $atd_part_bg_src['Right'];
								}
								?>">
							</a>

						</div>
					</div>
				</div>
				<div class="atd-data-item atd-chest" style="width: calc(100% / 2 - 10px);margin-bottom: 30px;">
					<header><strong style="font-size: 18px;font-weight: 700;">Chest Side</strong></header>
					<div class="atd-data-container" style="display: flex;">
						<div class="upload_image" style="width: calc(100% / 2);">
							<p><strong>Icon</strong></p>
							<a href="#" class="upload_image_button 
							<?php
							if ( !empty( $part_image['chest']['icon'] ) ) {
								echo 'atd-remove-media remove';
							} else {
								echo 'atd-add-media tips';
							}
							?>
							   atd-media-preview" data-tip="" >
							   <?php
								if ( !empty( $part_image['chest']['icon'] ) ) {
									echo "<img src='" . $part_image['chest']['icon'] . "'>";
								}
								?>
								<input type="hidden" name="atd-metas[<?php echo $variation_id; ?>][chest][icon]" value="<?php
								if ( !empty( $part_image['chest']['icon'] ) ) {
									echo $atd_part_icon_src["Chest"];
								}
								?>">
							</a>

						</div>
						<div class="upload_image" style="width: calc(100% / 2);">
							<p><strong>Background</strong></p>
							<a href="#" class="upload_image_button 
							<?php
							if ( !empty( $part_image['chest']['bg-inc'] ) ) {
								echo 'atd-remove-media remove';
							} else {
								echo 'atd-add-media tips';
							}
							?>
							   atd-media-preview" data-tip="" >
							   <?php
								if ( !empty( $part_image['chest']['bg-inc'] ) ) {
									echo "<img src='" . $part_image['chest']['bg-inc'] . "'>";
								}
								?>
								<input type="hidden" name="atd-metas[<?php echo $variation_id; ?>][chest][bg-inc]" value="<?php
								if ( !empty( $part_image['chest']['bg-inc'] ) ) {
									echo $atd_part_bg_src['Chest'];
								}
								?>">
							</a>

						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		$output = ob_get_clean();
		return $output;
	}
		/**
		 * Display configs dropdown and part
		 *
		 * @param type $configs all configs existing.
		 * @param type $product_id product id  in the page.
		 * @param type $label variation label.
		 */
	public function display_dropdown_config_and_part( $configs, $product_id, $label = '' ) {
		?>
		<div class="woocommerce_variations wc-metaboxes">
			<div class='woocommerce_variation wc-metabox closed'>
				<h3>
                                    <div class="atd-h3-flex">
                                        <div>
                                            <strong>#</strong>
                                            <strong><?php echo $label; ?></strong>
                                        </div>
                                        <?php
                                        echo $this->build_configs_dropdown( $configs, $product_id );
                                        ?>
                                    </div>
                                </h3> 
				<?php echo $this->display_part_config( $product_id ); ?>
			</div>
		</div>
		<input type="hidden" name="securite_nonce" value="<?php echo esc_html( wp_create_nonce( 'securite-nonce' ) ); ?>"/>
		<?php
	}

		/**
		 * Create label for varaition_attributes
		 *
		 * @param type $variation_attributes the attributes for variation.
		 * @return string
		 */
	public function create_variation_attributes_label( $variation_attributes ) {
		$label     = '';
				$i = 0;
		foreach ( $variation_attributes as $key => $value ) {
			if ( empty( $value ) )
				$value = wc_attribute_label( sprintf( __( 'Any %s', 'allada-tshirt-designer-for-woocommerce' ), wc_attribute_label( $key ) ) );

						$terms = get_terms( $key );
//                                                var_dump($variation_attributes);
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {

				foreach ( $terms as $term ) {

					if ( strtolower( $value ) == strtolower( $term->name ) ) {
						$value = $term->name;
					}
				}
			}

			if ( (int) count( $variation_attributes ) - 1 === $i ) {
						$label .= $value;
			} else {
					$label .= $value . ' / ';
			}
					$i++;
		}
									return $label;

	}

	/**
	 * Display all information depending of the product variable or simple
	 *
	 * @param type $configs all configs.
	 * @return boolean
	 */
	public function display_configs_assignation_and_part_setup( $configs ) {
		$product_id = get_the_ID();
		$product    = wc_get_product( $product_id );
		if ( ! $product ) {
			return false;
		}
		if ( 'variable' === $product->get_type() ) {
			$variations = $product->get_available_variations();

			foreach ( $variations as $variation ) {
				$product_variation    = wc_get_product( $variation['variation_id'] );
				$variation_attributes = $product_variation->get_attributes();
				if ( $variation_attributes && is_array( $variation_attributes ) ) {
					$label = $this->create_variation_attributes_label( $variation_attributes );
					$this->display_dropdown_config_and_part( $configs, $variation['variation_id'], $label );
				}
			}
		} else {
			$this->display_dropdown_config_and_part( $configs, $product_id );
		}
	}

	/**
	 * Display the thsirt configuration tab content
	 */
	public function show_tshirt_configuration_tab_content() {
		$product_id = get_the_ID();
		$product= wc_get_product($product_id);
		if($product->get_type()=="variable")
			$set_select = true;
		else
			$set_select = false;

		$meta_value = get_post_meta($product_id, "atd-metas", false);
		if(isset($meta_value[0]["attr-color"]))
		{
			$attr_color=$meta_value[0]["attr-color"];
			$gl_color_attr=explode("pa_", $attr_color);
			if($gl_color_attr[0]=="") $color_options=$gl_color_attr[1];
			else $color_options= $attr_color;
		}

		if(isset($meta_value[0]["attr-size"]))
		{
			$attr_size=$meta_value[0]["attr-size"];
			$gl_size_attr=explode("pa_", $attr_size);
			if($gl_size_attr[0]=="") $size_options=$gl_size_attr[1];
			else $size_options=$attr_size;
		}
		

		$configs = atd_get_configs();
		?>
		<div id="atd_tshirt_configuration" class="panel wc-metaboxes-wrapper">
                    <div id="atd-attr-selected-section" style="display: <?php ($set_select) ? esc_html_e('initial') : esc_html_e('none'); ?>">
					<label for="atd-attr-color"><?php esc_html_e( 'Color', 'allada-tshirt-designer-for-woocommerce' ); ?></label>
					<select id="atd-attr-color" class="" name="atd-metas[attr-color]">
						<?php 
							if(isset($attr_color))
							{
							?>
                                            <option value="<?php esc_attr_e($attr_color); ?>"><?php esc_html_e($color_options); ?></option>
							<?php
							}
							else{
								?><option value="your_attr_color"><?php esc_html_e( 'Your attribute color', 'allada-tshirt-designer-for-woocommerce' ); ?></option><?php	
							}
							echo $this->contruct_select_option_with_attr_define($product_id);
						?>
					</select>
					<label for="atd-attr-size"><?php esc_html_e( 'Size', 'allada-tshirt-designer-for-woocommerce' ); ?></label>
					<select id="atd-attr-size" class="" name="atd-metas[attr-size]">
						<?php
							if(isset($attr_size))
							{
								?>
									<option value="<?php esc_attr_e($attr_size) ?>"><?php esc_html_e($size_options) ?></option>
								<?php
							}
							else{
								?><option value="your_attr_size"><?php esc_html_e( 'Your attribute size', 'allada-tshirt-designer-for-woocommerce' ); ?></option><?php	
							}
							echo $this->contruct_select_option_with_attr_define($product_id); 
						?>
					</select>
				</div>
			<div class="toolbar toolbar-top" id="hide_toolbar">
				<div id="hide_select" style="display: initial;">
					<select id="atd-global-action" class="">
						<option value="set_default_config"><?php esc_html_e( 'Set default configuration', 'allada-tshirt-designer-for-woocommerce' ); ?></option>
					</select>

					<button type="button" class="wc_btn" id="atdConfModal"><?php esc_html_e( 'Go', 'allada-tshirt-designer-for-woocommerce' ); ?></button>

				</div>
				<div class="variations-pagenav">

					<span class="expand-close">
						(<a href="#" class="expand_all"><?php esc_html_e( 'Expand', 'allada-tshirt-designer-for-woocommerce' ); ?></a> / <a href="#" class="close_all"><?php esc_html_e( 'Close', 'allada-tshirt-designer-for-woocommerce' ); ?></a>)
					</span>
				</div>
				<div class="clear"></div>
			</div>
			<p style="padding-left: 1em;"><?php esc_html_e( 'Select your t-shirt configuration', 'allada-tshirt-designer-for-woocommerce' ); ?></p>
			<?php
			$this->display_configs_assignation_and_part_setup( $configs );
			?>
		</div>

		<!-- Modal Load Configuration Begin -->

		<div class="wb-modal" id="atd-modal-configs" data-wb-target="atdConfModal">

			<div class="wb-modal-header">

				<div class="wb-modal-title">Please&nbsp;select&nbsp;a&nbsp;configuration</div>

			</div>

			<div class="wb-modal-body">

				<form>

					<div class="wb-form-group">
						<?php
						echo $this->build_configs_dropdown( $configs );
						?>

					</div>

				</form>

			</div>

			<div class="wb-modal-footer">

				<div class="wb-row-all-center wb-form-group wb-modal-btns">

					<button type="button" class="wb-btn wb-btn-cancel">Cancel</button>

					<button type="button" class="wb-btn wb-btn-assign">Assign</button>

				</div>

			</div>

		</div>

		<div class="wb-modal-overlay" data-wb-target="atdConfModal"></div>


		<!-- Modal Load Configuration End -->   

		<?php
	}

	/**
	 * Save configuration product
	 *
	 * @param type $post_id the post id when page is update or save.
	 */
	public function save_metas( $post_id ) {
		$meta_key = 'atd-metas';
		if ( isset( $_POST['securite_nonce'] ) ) {

			if ( wp_verify_nonce( wp_unslash( sanitize_key( $_POST['securite_nonce'] ) ), 'securite-nonce' ) ) {
				if ( isset( $_POST[ $meta_key ] ) ) {

					update_post_meta( $post_id, $meta_key, $_POST[ $meta_key ] );
				}
			}
		}
	}
	
	public function contruct_select_option_with_attr_define($product_id)
	{
		$product= wc_get_product($product_id);
		$product_data_attr=$product->get_attributes();
		$content="";
		foreach ($product_data_attr as $key => $value)
		{
			    $gl_attr=explode("pa_", $key);
				if($gl_attr[0]=="") $options=$gl_attr[1];
				else $options=$key;
				$content.= "<option value='".$key."' >".$options."</option>";
		}
		return $content;
	}

}
