<?php
/**
 * Editor code.
 *
 * @link       http://orionorigin.com
 * @since      1.0
 *
 * @package    Atd
 * @subpackage Atd/includes
 */

/**
 * Editor class.
 *
 * This class defines all code necessary for editor
 *
 * @since      1.0.0
 * @package    Atd
 * @subpackage Atd/includes
 * @author     orionorigin <orionorigin@orionorigin.com>
 */
class ATD_Editor {

	/**
	 * The item id.
	 *
	 * @var type the item id.
	 */
	public $item_id;

	/**
	 * The root item id.
	 *
	 * @var type the root item id.
	 */
	public $root_item_id;

	/**
	 * The product.
	 *
	 * @var type the product.
	 */
	public $atd_product;

	/**
	 * The constructor.
	 *
	 * @param type $item_id the item id.
	 */
	public function __construct( $item_id ) {
		if ( $item_id ) {
			$this->item_id      = $item_id;
			$this->atd_product  = new ATD_Product( $item_id );
			$this->root_item_id = $this->atd_product->root_product_id;
		}
	}

	/**
	 * Get editor.
	 *
	 * @global type $atd_settings the settings.
	 * @return string output.
	 */
	public function get_editor() {
		global $atd_settings;

		ob_start();
		$product = wc_get_product( $this->item_id );
		// if ( !get_option( "atd-license-key" ) ) {
		//     _e( '<strong>Error: Your licence is not valid</strong>', 'allada-tshirt-designer-for-woocommerce' ) . '<br>';
		//     return;
		// }
		if ( ! $product ) {
			esc_html_e( 'Error: Invalid product: ', 'allada-tshirt-designer-for-woocommerce' );
			echo "$this->item_id<br>";
			esc_html_e( '1- Is your customization page defined as homepage?', 'allada-tshirt-designer-for-woocommerce' );
			echo '<br>';
			esc_html_e( '2- Is your customization page defined as one of woocommerce pages?', 'allada-tshirt-designer-for-woocommerce' );
			echo '<br>';
			esc_html_e( '3- Does the product you are trying to customize exists and is published in your shop?', 'allada-tshirt-designer-for-woocommerce' );
			echo '<br>';
			esc_html_e( '4- Are you accessing this page from one of the product designer buttons?', 'allada-tshirt-designer-for-woocommerce' ) . '<br>';
			return;
		}
		if ( ! $this->atd_product->has_part() ) {
			esc_html_e( 'Error: No active part defined for this product. A customizable product should have at least one part defined.', 'allada-tshirt-designer-for-woocommerce' );
			return;
		}
		$atd_metas = $this->atd_product->settings;
                
        atd_init_canvas_vars( $atd_metas, $product, $this );
		$ui_options     = atd_get_proper_value( $atd_settings, 'atd-ui-options', array() );
		$skin           = atd_get_proper_value( $ui_options, 'skin', 'ATD_Skin_Default' );
        $editor = new $skin( $this, $atd_metas );

		$raw_output = $editor->display();
		echo $raw_output;

		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

}
