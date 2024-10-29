<?php
/**
 * Cliparts code
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
 * This class defines all code necessary for colors palette.
 *
 * @since      1.0.0
 * @package    Atd
 * @subpackage Atd/includes
 * @author     orionorigin <orionorigin@orionorigin.com>
 */
class ATD_Colors_Palette {

	/**
	 * Save the clipart.
	 *
	 * @param int $post_id The post id.
	 */
	public function save_colors_palette( $post_id ) {
		if ( isset( $_POST['securite_nonce'] ) ) {
			if ( wp_verify_nonce( wp_unslash( sanitize_key( $_POST['securite_nonce'] ) ), 'securite-nonce' ) ) {
				if ( isset( $_POST['atd-colors-palette-data'] ) ) {
                                    update_post_meta( $post_id, 'atd-colors-palette-data', wp_unslash( filter_input(INPUT_POST, 'atd-colors-palette-data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) ) );
				}
			}
		}
	}
}
