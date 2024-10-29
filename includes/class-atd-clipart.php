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
 * This class defines all code necessary for cliparts.
 *
 * @since      1.0.0
 * @package    Atd
 * @subpackage Atd/includes
 * @author     orionorigin <orionorigin@orionorigin.com>
 */
class ATD_Clipart {

	/**
	 * Save the clipart.
	 *
	 * @param int $post_id The post id.
	 */
	public function save_cliparts( $post_id ) {
		if ( isset( $_POST['securite_nonce'] ) ) {
			if ( wp_verify_nonce( wp_unslash( sanitize_key( $_POST['securite_nonce'] ) ), 'securite-nonce' ) ) {
				if ( isset( $_POST['atd-cliparts-data'] ) ) {
					update_post_meta( $post_id, 'atd-cliparts-data', wp_unslash( filter_input(INPUT_POST,'atd-cliparts-data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) ) );
				}
			}
		}
	}

	/**
	 * Set default category when no category is chosen.
	 *
	 * @param type $post_id the post id.
	 * @param type $post the post.
	 */
	public function set_default_object_terms( $post_id, $post ) {
		if ( 'publish' === $post->post_status ) {
			$defaults   = array( 'default', 'default' );
			$taxonomies = get_object_taxonomies( $post->post_type );
			foreach ( (array) $taxonomies as $taxonomy ) {
				if ( 'atd-categorie-cliparts' === $taxonomy ) {
					$terms = wp_get_post_terms( $post_id, $taxonomy );
					if ( empty( $terms ) ) {
						wp_set_object_terms( $post_id, $defaults, $taxonomy );
					}
				}
			}
		}
	}

}
