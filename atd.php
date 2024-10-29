<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.orionorigin.com/
 * @since             1.0.0
 * @package           Atd
 *
 * @wordpress-plugin
 * Plugin Name:       Allada t-shirt designer
 * Plugin URI:        https://designersuiteforwp.com/allada-woocommerce-custom-t-shirt-designer/features/
 * Description:       The ultimate t-shirt designer plugin for woocommerce.
 * Version:           1.1
 * Author:            ORION
 * Author URI:        https://www.orionorigin.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       atd
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ATD_VERSION', '1.1' );
define( 'ATD_URL', plugins_url( '/', __FILE__ ) );
define( 'ATD_DIR', dirname( __FILE__ ) );
define( 'ATD_MAIN_FILE', 'atd/atd.php' );
define( 'ATD_PLUGIN_NAME', 'Allalda t-shirt designer' );
define( 'ATD_CANVAS_UNIT', 'px');

$upload_dir = wp_upload_dir();
$generation_path = $upload_dir['basedir'] . "/ALLADA/";
$generation_url = $upload_dir['baseurl'] . "/ALLADA/";

define('ATD_TMP_UPLOAD_PATH', $generation_path . "TMP");
define('ATD_TMP_UPLOAD_URL', $generation_url . "TMP");

define('ATD_ORDER_UPLOAD_PATH', $generation_path . "ORDERS");
define('ATD_ORDER_UPLOAD_URL', $generation_url . "ORDERS");

define('ATD_SAVED_DESIGN_UPLOAD_PATH', $generation_path . "SAVED-DESIGN");
define('ATD_SAVED_DESIGN_UPLOAD_URL', $generation_url . "SAVED-DESIGN");

define( 'ATD_CANVAS', array( 
    "front" => array(
        "t-shirt-width" => 852,
        "t-shirt-height" => 1000, 
        "canvas-width" => 301, 
        "canvas-height" => 525, 
        "canvas-top" => 300, 
        "canvas-left" => 276, 
        ), 
    "back" => array(
        "t-shirt-width" => 852,
        "t-shirt-height" => 1000, 
        "canvas-width" => 301, 
        "canvas-height" => 525, 
        "canvas-top" => 300, 
        "canvas-left" => 276, 
        ),
    "right" => array(
        "t-shirt-width" => 100,
        "t-shirt-height" => 100, 
        "canvas-width" => 100, 
        "canvas-height" => 100,
        "canvas-top" => 100, 
        "canvas-left" => 100, 
        ),
    "left" => array(
        "t-shirt-width" => 100,
        "t-shirt-height" => 100, 
        "canvas-width" => 100,
        "canvas-height" => 100, 
        "canvas-top" => 100, 
        "canvas-left" => 100, 
        ),
    "chest" => array(
        "t-shirt-width" => 852,
        "t-shirt-height" => 1000, 
        "canvas-width" => 140, 
        "canvas-height" => 140, 
        "canvas-top" => 351, 
        "canvas-left" => 440, 
        ),
    ) 
);

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-atd-activator.php
 */
function activate_atd() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-atd-activator.php';
	Atd_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-atd-deactivator.php
 */
function deactivate_atd() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-atd-deactivator.php';
	Atd_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_atd' );
register_deactivation_hook( __FILE__, 'deactivate_atd' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-atd.php';

require_once ATD_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-atd-colors-palette.php';
require_once ATD_DIR . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-atd-post-types.php';
require_once ATD_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-atd-clipart.php';
require_once ATD_DIR . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-atd-config.php';
require_once ATD_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-atd-editor.php';
require_once ATD_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-atd-design.php';
require_once ATD_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-atd-retarded-actions.php';

// Default skins.
// require_once ATD_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'skins' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'class-atd-skin-default.php';

if ( wp_is_mobile() ) {
    // var_dump("hello mobile"); die ();
    require_once ATD_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'skins' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'class-atd-skin-default-mobile.php';
} else {
    // Default skins.
    // var_dump("hello desktop"); die ();
    require_once ATD_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'skins' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'class-atd-skin-default.php';
}

if ( ! function_exists( 'atd_o_admin_fields' ) ) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/utils.php';
}

require_once plugin_dir_path( __FILE__ ) . 'includes/functions.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_atd() {

	$plugin = new Atd();
	$plugin->run();

}
run_atd();
