<?php
/**
 * Atd add font functions file.
 *
 * @link       orionorigin@orionorigin.com
 * @since      1.0.0
 * @package    Atd
 * @subpackage Atd/includes
 * @author     orionorigin <orionorigin@orionorigin.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Add fonts.
 */
function atd_add_fonts() {
	if ( isset( $_COOKIE['error'] ) && ! empty( $_COOKIE['error'] ) ) {
			echo wp_kses_post(filter_input(INPUT_COOKIE, 'error'));
			setcookie( 'error', '' );
	}
	// Action to perform: add, edit, delete or none.
	$action               = '';
		$action_completed = false;
	if ( ! empty( $_POST['add_new_font'] ) ) {
		$action = 'add';
	} elseif ( ! empty( $_POST['save_font'] ) && ! empty( $_GET['edit'] ) ) {
		$action = 'edit';
	} elseif ( ! empty( $_GET['delete'] ) ) {
		$action = 'delete';
	}
	// Add or edit an attribute.
	if ( 'add' === $action || 'edit' === $action ) {
		// Security check.
		if ( 'add' === $action ) {
			check_admin_referer( 'woocommerce-add-new_font' );
		}
		if ( 'edit' === $action ) {
			$font_key = absint( $_GET['edit'] );
			check_admin_referer( 'woocommerce-save-font_' . $font_key );
		}
		// Grab the submitted data.
		$font_label = ( isset( $_POST['font_label'] ) ) ? (string) stripslashes( filter_input(INPUT_POST, 'font_label') ) : '';
		$font_url   = ( isset( $_POST['font_url'] ) ) ? (string) stripslashes( filter_input(INPUT_POST, 'font_url') ) : '';
		$font_file  = ( isset( $_POST['font_file'] ) ) ? (array) filter_input(INPUT_POST, 'font_file',FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) : '';
		if ( 'add' === $action ) {
			if ( ! empty( $font_label ) ) {
				$fonts = get_option( 'atd-fonts' );
				if ( empty( $fonts ) ) {
					$i                         = 1;
					$fonts[ $i ]               = array( $font_label, $font_url, $font_file );
										$error = __( '<div class=updated>Font add successfully.</div>', 'allada-tshirt-designer-for-woocommerce' );
				} else {
					$font_labels = array_map(
						function( $o ) {
								return $o[0];
						},
						$fonts
					);
					if ( in_array( $font_label, $font_labels, true ) ) {
						$error = __( '<div class=error>This font exist !</div>', 'allada-tshirt-designer-for-woocommerce' );
					} else {
						$fonts[]                       = array( $font_label, $font_url, $font_file );
												$error = __( '<div class=updated>Font add successfully.</div>', 'allada-tshirt-designer-for-woocommerce' );
					}
				}
				update_option( 'atd-fonts', $fonts );
				$action_completed = true;
			} else {
				$error            = __( '<div class=error>Missing font name.</div>', 'allada-tshirt-designer-for-woocommerce' );
				$action_completed = true;
			}
		}
		// Edit existing attribute.
		if ( 'edit' === $action ) {
			$fonts          = get_option( 'atd-fonts' );
			$edit           = wp_unslash( sanitize_key( $_GET['edit'] ) );
			$fonts[ $edit ] = array( $font_label, $font_url, $font_file );
			update_option( 'atd-fonts', $fonts );
			$action_completed = true;
		}
	}

	// Delete an attribute.
	if ( 'delete' === $action ) {
		// Security check.
		$font_id = absint( $_GET['delete'] );
		$fonts   = get_option( 'atd-fonts' );
		unset( $fonts[ $font_id ] );
		update_option( 'atd-fonts', $fonts );
	}

	// If an attribute was added, edited or deleted: clear cache and redirect.
	if ( $action_completed ) {
			setcookie( 'error', $error );
			wp_safe_redirect( get_admin_url() . 'admin.php?page=atd-manage-fonts' );
			exit;
	}
	// Show admin interface.
	if ( ! empty( $_GET['edit'] ) ) {
		?>
		<input type="hidden" name="securite_nonce" value="<?php echo esc_html( wp_create_nonce( 'securite-nonce' ) ); ?>"/>
		<?php
		atd_edit_font();
	} else {
		atd_add_font();
	}
}

/**
 * Edit font
 */
function atd_edit_font() {
	if ( isset( $_GET['edit'] ) ) {
			$edit = absint( $_GET['edit'] );
	}
	$fonts      = get_option( 'atd-fonts' );
	$font_label = $fonts[ $edit ][0];
	$font_url   = $fonts[ $edit ][1];
	$font_file  = ( isset( $fonts[ $edit ][2] ) ) ? (array) ( $fonts[ $edit ][2] ) : '';
        $new_allowed_tags = ['data-selector'];
        $allowed_html = atd_allowed_tags($new_allowed_tags);
	wp_enqueue_media();
	?>
	<div class="wrap woocommerce">
		<div class="icon32 icon32-attributes" id="icon-woocommerce"><br/></div>
		<h2><?php esc_html_e( 'Edit Font', 'allada-tshirt-designer-for-woocommerce' ); ?></h2>
		<form action="admin.php?page=atd-manage-fonts&amp;edit=<?php echo absint( $edit ); ?>&amp;noheader=true" method="post">
			<?php atd_add_font_select2( $font_label ); ?>
			<table class="form-table">
				<tbody>
					<tr class="form-field form-required">
						<th scope="row" valign="top">
							<label for="font_label"><?php esc_html_e( 'Name', 'allada-tshirt-designer-for-woocommerce' ); ?></label>
						</th>
						<td>
													<input name="font_label" id="font_label" class="font_auto_name" type="text" value="<?php echo esc_attr( $font_label ); ?>" required/>
							<p class="description"><?php esc_html_e( 'Name for the attribute (shown on the front-end).', 'allada-tshirt-designer-for-woocommerce' ); ?></p>
						</td>
					</tr>
					<tr class="form-field">
					</tr>
					<tr class="form-field">
						<th scope="row" valign="top">
							<label for="font_label"><?php esc_html_e( 'URL', 'allada-tshirt-designer-for-woocommerce' ); ?></label>
						</th>
						<td>
							<input name="font_url" id="font_label" class="font_auto_url" type="text" value="<?php echo esc_attr( $font_url ); ?>" />
							<p class="description"><?php esc_html_e( 'Google font URL. Leave this field empty if the font is already loaded by the theme.', 'allada-tshirt-designer-for-woocommerce' ); ?></p>
						</td>
					</tr>
					<tr class="form-field">
						<th scope="row" valign="top">
							<label for="font_file"><?php esc_html_e( 'TTF font file', 'allada-tshirt-designer-for-woocommerce' ); ?></label>
						</th>
						<td>
							<div>
								<textarea id='atd-font-tpl' style='display: none;'>
									<?php echo wp_kses(atd_get_font_tpl(), $allowed_html); ?>
								</textarea>
								<div><a class='o-add-font-file button'><?php esc_html_e( 'Add font file', 'allada-tshirt-designer-for-woocommerce' ); ?></a><br>
									<span style="color:red;"><?php esc_html_e( 'Make sure you select at least one style per file.', 'allada-tshirt-designer-for-woocommerce' ); ?></span>
								</div>
								<table class="font_style_table">
									<thead>
									<th>Style</th>
									<th>File</th>
									<th>Action</th>
									</thead>
									<tbody>                                                        
										<?php
										echo wp_kses(atd_get_font_tpl( $font_file ), $allowed_html);
										?>
									</tbody>
								</table>                                               
							</div>
							<p class="description"><?php esc_html_e( 'TrueType font file (can be used if the url is not provided and while generating the output vector.', 'allada-tshirt-designer-for-woocommerce' ); ?></p>
						</td>                                            
					</tr>
				</tbody>
			</table>
			<input type="hidden" name="securite_nonce" value="<?php echo esc_html( wp_create_nonce( 'securite-nonce' ) ); ?>"/>
			<p class="submit"><input type="submit" name="save_font" id="submit" class="button-primary" value="<?php esc_html_e( 'Update', 'allada-tshirt-designer-for-woocommerce' ); ?>"></p>
			<?php wp_nonce_field( 'woocommerce-save-font_' . $edit ); ?>
		</form>
	</div>
	<?php
}

/**
 * Add font
 */
function atd_add_font() {
	wp_enqueue_media();
	?>
	<div class="wrap woocommerce">
		<div class="icon32 icon32-attributes" id="icon-woocommerce"><br/></div>
		<h2><?php esc_html_e( 'Add Fonts', 'allada-tshirt-designer-for-woocommerce' ); ?></h2>
		<br class="clear" />
		<div id="col-container">
			<div id="col-right">
				<div class="col-wrap">
					<table class="widefat fixed" style="width:100%">
						<thead>
							<tr>
								<th scope="col"><?php esc_html_e( 'Name', 'allada-tshirt-designer-for-woocommerce' ); ?></th>
								<th scope="col"><?php esc_html_e( 'Url', 'allada-tshirt-designer-for-woocommerce' ); ?></th>
								<th scope="col"><?php esc_html_e( 'Style name', 'allada-tshirt-designer-for-woocommerce' ); ?></th>
								<th scope="col"><?php esc_html_e( 'TTF font file', 'allada-tshirt-designer-for-woocommerce' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$fonts = get_option( 'atd-fonts' );
							if ( $fonts ) :
								foreach ( $fonts as $key => $font_arr ) :
									$font     = $font_arr[0];
									$font_url = $font_arr[1];
									if ( ! isset( $font_arr[2] ) ) {
										$font_arr[2] = array();
									}
									?>
									<tr>

										<td><a href="<?php echo esc_url( add_query_arg( 'edit', $key, 'admin.php?page=atd-manage-fonts' ) ); ?>"><?php echo esc_html( $font ); ?></a>

											<div class="row-actions"><span class="edit"><a href="<?php echo esc_url( add_query_arg( 'edit', $key, 'admin.php?page=atd-manage-fonts' ) ); ?>"><?php esc_html_e( 'Edit', 'allada-tshirt-designer-for-woocommerce' ); ?></a> | </span><span class="delete"><a class="delete" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'delete', $key, 'admin.php?page=atd-manage-fonts' ), 'woocommerce-delete-attribute_' . $key ) ); ?>"><?php esc_html_e( 'Delete', 'allada-tshirt-designer-for-woocommerce' ); ?></a></span></div>
										</td>
										<td><?php echo esc_html( $font_url ); ?> </td>
										<td>
											<?php
											if ( is_array( $font_arr[2] ) ) :
												foreach ( $font_arr[2] as $key => $fonts_styles ) :
													$i = 1;
													if ( isset( $fonts_styles['styles'] ) ) {
														foreach ( $fonts_styles['styles'] as $style ) :
															if ( count( $fonts_styles['styles'] ) === $i ) {
																echo esc_html( atd_fonts_array( $style ) );
															} else {
																echo esc_html( atd_fonts_array( $style ) ) . '+';
															}
																												$i++;
															endforeach;
													}
													echo '<br>';
												endforeach;
											endif;
											?>
										</td>
										<td>
											<?php
											if ( is_array( $font_arr[2] ) ) :
												foreach ( $font_arr[2] as $key => $fonts_styles ) :
													$font_file_url = wp_get_attachment_url( $fonts_styles['file_id'] );
													echo esc_html( basename( $font_file_url ) ) . '<br>';
												endforeach;
											endif;
											?>
											</ul>

										</td>
									</tr>
									<?php
								endforeach;
							else :
								?>
								<tr><td colspan="4"><?php esc_html_e( 'No fonts currently exist.', 'allada-tshirt-designer-for-woocommerce' ); ?></td></tr>
								<?php
							endif;
							?>
						</tbody>
					</table>
				</div>
			</div>
			<div id="col-left">
				<div class="col-wrap">
					<div class="form-wrap">
						<h3><?php esc_html_e( 'Add new font', 'allada-tshirt-designer-for-woocommerce' ); ?></h3>
						<form action="admin.php?page=atd-manage-fonts&amp;noheader=true" method="post">
							<?php atd_add_font_select2(); ?>
							<div class="form-field">
								<label for="font_label"><?php esc_html_e( 'Name', 'allada-tshirt-designer-for-woocommerce' ); ?></label>
																<input name="font_label" class="font_auto_name" id="font_label" type="text" value="" required/>
								<p class="description"><?php esc_html_e( 'Name for the font (shown on the front-end).', 'allada-tshirt-designer-for-woocommerce' ); ?></p>
							</div>
							<div class="form-field">
								<label for="font_url"><?php esc_html_e( 'URL', 'allada-tshirt-designer-for-woocommerce' ); ?></label>
								<input name="font_url" id="font_label" class="font_auto_url" type="text" value="" />
								<p class="description"><?php esc_html_e( 'Google font URL. Leave this field empty if the font is already loaded by the theme.', 'allada-tshirt-designer-for-woocommerce' ); ?></p>
							</div>
							<div class="form-field">
								<textarea id='atd-font-tpl' style='display: none;'>
									<?php echo esc_html( atd_get_font_tpl() ); ?>
								</textarea>
								<label for="font_file"><?php esc_html_e( 'TTF font file', 'allada-tshirt-designer-for-woocommerce' ); ?></label>
								<div>
									<div>
										<a class='o-add-font-file button'><?php esc_html_e( 'Add font file', 'allada-tshirt-designer-for-woocommerce' ); ?></a><br>
										<span style="color:red;"><?php esc_html_e( 'Make sure you select at least one style per file.', 'allada-tshirt-designer-for-woocommerce' ); ?></span>
									</div>
									<table class="font_style_table">
										<thead>
										<th>Style</th>
										<th>File</th>
										<th>Action</th>
										</thead>
										<tbody>                                                        

										</tbody>
									</table>

								</div>
								<p class="description"><?php esc_html_e( 'TrueType font (can be used if the url is not provided and while generating the output vector.', 'allada-tshirt-designer-for-woocommerce' ); ?></p>
							</div>
							<p class="submit"><input type="submit" name="add_new_font" id="submit" class="button" value="<?php esc_html_e( 'Add Font', 'allada-tshirt-designer-for-woocommerce' ); ?>"></p>
							<?php wp_nonce_field( 'woocommerce-add-new_font' ); ?>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			jQuery('a.delete').click(function () {
				var answer = confirm("<?php esc_html_e( 'Are you sure you want to delete this font?', 'allada-tshirt-designer-for-woocommerce' ); ?>");
				if (answer)
					return true;
				return false;
			});
		</script>
	</div>
	<?php
}

/**
 * Get font tpl
 *
 * @param array $font_array Array of fonts.
 */
function atd_get_font_tpl( $font_array = false ) {
	if ( $font_array ) {
		$tpl = '';
		foreach ( $font_array as $key => $fonts_styles ) :
			if ( ! empty( $fonts_styles ) ) :
				if ( isset( $fonts_styles['file_id'] ) ) :
					$file_id       = $fonts_styles['file_id'];
					$font_file_url = wp_get_attachment_url( $file_id );
					$file_name     = basename( $font_file_url );
				else :
					$file_name = '';
				endif;
				$tpl .= "<tr>
                        <td>
                            <ul class='radio'>";

				if ( isset( $fonts_styles['styles'] ) && in_array( '', $fonts_styles['styles'], true ) ) {
					$tpl .= "<li><input type='checkbox' name='font_file[$key][styles][]' value='' checked='checked'/>Regular</li>";
				} else {
					$tpl .= "<li><input type='checkbox' name='font_file[$key][styles][]' value='' />Regular</li>";
				}
				if ( isset( $fonts_styles['styles'] ) && in_array( 'B', $fonts_styles['styles'], true ) ) {
					$tpl .= "<li><input type='checkbox' name='font_file[$key][styles][]' value='B' checked='checked'/>Bold</li>";
				} else {
					$tpl .= "<li><input type='checkbox' name='font_file[$key][styles][]' value='B' />Bold</li>";
				}
				if ( isset( $fonts_styles['styles'] ) && in_array( 'U', $fonts_styles['styles'], true ) ) {
					$tpl .= "<li><input type='checkbox' name='font_file[$key][styles][]' value='U' checked='checked'/>Underline</li>";
				} else {
					$tpl .= "<li><input type='checkbox' name='font_file[$key][styles][]' value='U' />Underline</li>";
				}
				if ( isset( $fonts_styles['styles'] ) && in_array( 'D', $fonts_styles['styles'], true ) ) {
					$tpl .= "<li><input type='checkbox' name='font_file[$key][styles][]' value='D' checked='checked'/>Line Through</li>";
				} else {
					$tpl .= "<li><input type='checkbox' name='font_file[$key][styles][]' value='D' />Line Through</li>";
				}
				if ( isset( $fonts_styles['styles'] ) && in_array( 'I', $fonts_styles['styles'], true ) ) {
					$tpl .= "<li><input type='checkbox' name='font_file[$key][styles][]' value='I' checked='checked'/>Italic</li>";
				} else {
					$tpl .= "<li><input type='checkbox' name='font_file[$key][styles][]' value='I' />Italic</li>";
				}
				if ( isset( $fonts_styles['styles'] ) && in_array( 'O', $fonts_styles['styles'], true ) ) {
					$tpl .= "<li><input type='checkbox' name='font_file[$key][styles][]' value='O' checked='checked'/>Overline</li>";
				} else {
					$tpl .= "<li><input type='checkbox' name='font_file[$key][styles][]' value='O' />Overline</li>";
				}
				$tpl .= " </ul> 
                           </td>                    
                           <td>
                               <div class='media-name'>$file_name</div>
                           </td>
                           <td id='file_data_$key'>
                               <button class='button o-remove-font-file' data-selector='file_container_$key'>" . __( 'Remove font', 'allada-tshirt-designer-for-woocommerce' ) . "</button>
                               <input type='hidden' id='font_file' name='font_file[$key][file_id]' value='$file_id'>
                        </td>                    
                    </tr>";
			endif;
		endforeach;
	} else {
		$tpl = "<tr>
                 <td>
                          <ul class='radio'> 
                            <li><input type='checkbox' name='font_file[{index}][styles][]' value='' />Regular</li> 
                            <li><input type='checkbox' name='font_file[{index}][styles][]' value='B' />Bold</li> 
                            <li><input type='checkbox' name='font_file[{index}][styles][]' value='U' />Underline</li> 
                            <li><input type='checkbox' name='font_file[{index}][styles][]' value='D' />Line Through</li> 
                            <li><input type='checkbox' name='font_file[{index}][styles][]' value='I' />Italic</li> 
                            <li><input type='checkbox' name='font_file[{index}][styles][]' value='O' />Overline</li> 
                          </ul> 
                    </td>                    
                    <td>
                        <div class='media-name'>
                        </div>
                    </td>
                    <td id='file_data_{index}'>
                        <button class='button o-remove-font-file' data-selector='file_container_{index}'>" . __( 'Remove font', 'allada-tshirt-designer-for-woocommerce' ) . "</button>
                        <input type='hidden' id='font_file' name='font_file[{index}][file_id]' value=''>
                 </td>                    
             </tr>";
	}
	return $tpl;
}

/**
 * Add value to fonts array
 *
 * @param string $value An font.
 */
function atd_fonts_array( $value ) {
	$fonts_array = array(
		''  => 'Regular',
		'B' => 'Bold',
		'I' => 'Italic',
		'U' => 'Underline',
		'D' => 'Line Through',
		'O' => 'Overline',
	);
	return $fonts_array[ $value ];
}

/**
 * Select font
 *
 * @param string $selected_font Select font.
 */
function atd_add_font_select2( $selected_font = false ) {
	global $wp_filesystem;
	$url          = 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyBwjhzcfEEHD0cL0S90wDyvoKHLGJdwWvY';
	$wp_nonce_url = wp_nonce_url( $url );
	$test_url     = request_filesystem_credentials( $wp_nonce_url, '', false, false, null );

	if ( $test_url ) {
		$url        = wp_remote_get( $url, array( 'timeout' => 120 ) );
		$url_decode = '';
		if ( is_array( $url ) ) {
			$url_decode = json_decode( $url['body'], true );
		}
	} else {
		$url        = wp_remote_get( plugin_dir_path( dirname( __FILE__ ) ) . 'admin/js/google-fonts.json', array( 'timeout' => 120 ) );
		$url_decode = '';
		if ( is_array( $url ) ) {
			$url_decode = json_decode( $url['body'], true );
		}
	}
	?>
	<select id="font">
		<?php
		echo '<option></option>';
		foreach ( $url_decode['items'] as $font ) {
			if ( isset( $font['family'] ) && isset( $font['files'] ) && isset( $font['files']['regular'] ) ) {
				$selected = '';
				if ( $selected_font === $font['family'] ) {
					$selected = 'selected';
				}
                                $allowed_html = atd_allowed_tags();
				echo wp_kses('<option value="http://fonts.googleapis.com/css?family=' . rawurlencode( $font['family'] ) . '" ' . $selected . '>' . $font['family'] . '</option> ',$allowed_html);
			}
		}
		?>
	</select>
	<?php esc_html_e( 'Choose a google font', 'allada-tshirt-designer-for-woocommerce' ); ?>
	<?php
	return $url_decode['items'];
}
