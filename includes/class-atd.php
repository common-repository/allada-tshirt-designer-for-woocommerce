<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.orionorigin.com/
 * @since      1.0.0
 *
 * @package    Atd
 * @subpackage Atd/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Atd
 * @subpackage Atd/includes
 * @author     ORION <support@orionorigin.com>
 */
class Atd {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Atd_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if (defined('ATD_VERSION')) {
            $this->version = ATD_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'allada-tshirt-designer-for-woocommerce';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Atd_Loader. Orchestrates the hooks of the plugin.
     * - Atd_i18n. Defines internationalization functionality.
     * - Atd_Admin. Defines all hooks for the admin area.
     * - Atd_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-atd-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-atd-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-atd-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-atd-public.php';

        /**
         * The class responsible for defining all actions that occur in the admin area and related to products.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-atd-product.php';

        $this->loader = new Atd_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Atd_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Atd_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Atd_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        //Gets the settings and put them in a global variable.
        $this->loader->add_action('init', $plugin_admin, 'init_globals');

        //Alerts the administrator if the minimum requirements are not met.
        $this->loader->add_action('admin_notices', $plugin_admin, 'notify_prerequisites');
        $this->loader->add_action('admin_notices', $plugin_admin, 'get_max_input_vars_php_ini');

        //Builds all the plugin menu and submenu.
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_submenu');
        $this->loader->add_action('wp_ajax_create_design_page', $plugin_admin, 'create_design_page_ajx');
        $this->loader->add_action('wp_ajax_nopriv_create_design_page', $plugin_admin, 'create_design_page_ajx');

        //Licence Gestion        
       // $this->loader->add_action('admin_notices', $plugin_admin, 'get_license_activation_notice');
        
        // License key verification
        // $this->loader->add_action( 'init', $plugin_admin, 'o_verify_validity', 99 );

        // Atd post types hooks hooks.
        $atd_post = new ATD_Post_Types();
        $this->loader->add_action('init', $atd_post, 'register_cpt_colors_palette');
        $this->loader->add_action('add_meta_boxes', $atd_post, 'get_colors_palette_metabox');
        $this->loader->add_action('init', $atd_post, 'register_cpt_cliparts');
        $this->loader->add_action('add_meta_boxes', $atd_post, 'get_cliparts_metabox');
        $this->loader->add_action('init', $atd_post, 'register_cpt_config');
        $this->loader->add_action('add_meta_boxes', $atd_post, 'get_config_metabox');
        $this->loader->add_filter('get_user_option_meta-box-order_atd-config', $atd_post, 'get_metabox_order');
        $this->loader->add_filter('manage_edit-product_columns', $atd_post, 'get_product_columns');
        $this->loader->add_action('manage_product_posts_custom_column', $atd_post, 'get_products_columns_values', 5, 2);



        // Colors palette hooks.
        $colors_palette = new ATD_Colors_Palette();
        $this->loader->add_action('save_post_atd-colors-palette', $colors_palette, 'save_colors_palette');

        // Cliparts hooks.
        $clipart = new ATD_Clipart();
        $this->loader->add_action('save_post_atd-cliparts', $clipart, 'save_cliparts');
        $this->loader->add_action('save_post', $clipart, 'set_default_object_terms', 100, 2);

        // Configurations hooks.
        $atd_config = new ATD_Config();
        $this->loader->add_action('save_post_atd-config', $atd_config, 'save_metas');
        $this->loader->add_action('admin_action_atd_duplicate_config', $atd_config, 'atd_duplicate_config');
        $this->loader->add_filter('post_row_actions', $atd_config, 'get_duplicate_post_link', 10, 2);
        $this->loader->add_filter('woocommerce_product_data_tabs', $atd_config, 'add_tshirt_configuration_tab_label');
        $this->loader->add_action('woocommerce_product_data_panels', $atd_config, 'show_tshirt_configuration_tab_content');

        $this->loader->add_action('wp_ajax_get_product_config_parts', $atd_config, 'get_product_config_parts');

        $this->loader->add_action('save_post_product', $atd_config, 'save_metas');

        // Products.
        $product_admin = new ATD_Product(false);
        $this->loader->add_action('woocommerce_after_add_to_cart_button', $product_admin, 'hide_cart_button');

        //save meta

        $this->loader->add_action('save_post_product', $atd_config, 'save_metas');

    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Atd_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_action('woocommerce_after_add_to_cart_button', $plugin_public, 'get_start_designing_btn');
        $this->loader->add_filter('query_vars', $plugin_public, 'atd_add_query_vars');
        $this->loader->add_filter('init', $plugin_public, 'atd_add_rewrite_rules', 99);
        $this->loader->add_action('init', $plugin_public, 'set_variable_action_filters', 99);
        $this->loader->add_filter('post_class', $plugin_public, 'get_item_class', 10, 3);
        $this->loader->add_action('init', $plugin_public, 'register_shortcodes');
        $this->loader->add_action('init', $plugin_public, 'init_sessions', 1);
         
        $this->loader->add_action('wp_ajax_display_related_custom_products', $plugin_public, 'display_related_custom_products');
        $this->loader->add_action('wp_ajax_nopriv_display_related_custom_products', $plugin_public, 'display_related_custom_products');

        $this->loader->add_action('wp_ajax_display_related_custom_product_details', $plugin_public, 'display_related_custom_product_details');
        $this->loader->add_action('wp_ajax_nopriv_display_related_custom_product_details', $plugin_public, 'display_related_custom_product_details');

        $this->loader->add_action('wp_ajax_get_default_product_variation_details', $plugin_public, 'get_default_product_variation_details');
        $this->loader->add_action('wp_ajax_nopriv_get_default_product_variation_details', $plugin_public, 'get_default_product_variation_details');

        // Products
        $atd_product = new ATD_Product(false);
        $this->loader->add_filter('body_class', $atd_product, 'get_custom_products_body_class', 10, 2);
        $this->loader->add_action('woocommerce_product_duplicate', $atd_product, 'duplicate_product_metas', 10, 2);

        // Handle user pictures upload.
        $this->loader->add_action('wp_ajax_handle_picture_upload', $plugin_public, 'handle_picture_upload');
        $this->loader->add_action('wp_ajax_nopriv_handle_picture_upload', $plugin_public, 'handle_picture_upload');


        // Handle delete user pictures upload.
        $this->loader->add_action('wp_ajax_handle_delete_picture_upload', $plugin_public, 'handle_delete_picture_upload');
        $this->loader->add_action('wp_ajax_nopriv_handle_delete_picture_upload', $plugin_public, 'handle_delete_picture_upload');

        // Move user pictures upload from cookie in user account.
        $this->loader->add_action('user_register', $plugin_public, 'move_user_picture_uplaods', 10, 1);

        $atd_design = new ATD_Design();
        //Add  custom design to cart.
        $this->loader->add_action('wp_ajax_add_custom_design_to_cart', $atd_design, 'add_custom_design_to_cart_ajax');
        $this->loader->add_action('wp_ajax_nopriv_add_custom_design_to_cart', $atd_design, 'add_custom_design_to_cart_ajax');

        //Get design price.
        $this->loader->add_action('wp_ajax_get_design_price', $atd_design, 'get_design_price_ajax');
        $this->loader->add_action('wp_ajax_nopriv_get_design_price', $atd_design, 'get_design_price_ajax');

        // User my account page
        $this->loader->add_action('woocommerce_order_item_meta_end', $atd_design, 'get_user_account_products_meta', 11, 4);
        $this->loader->add_action('woocommerce_before_calculate_totals', $atd_design, 'get_cart_item_price', 10);

        //Save variation attributes in transients
        $this->loader->add_action('wp_ajax_atd_store_variation_attributes', $plugin_public, 'atd_store_variation_attributes');
        $this->loader->add_action('wp_ajax_nopriv_atd_store_variation_attributes', $plugin_public, 'atd_store_variation_attributes');

        //Generate downloadable file. Download design.
        $this->loader->add_action('wp_ajax_generate_downloadable_file', $atd_design, 'generate_downloadable_file');
        $this->loader->add_action('wp_ajax_nopriv_generate_downloadable_file', $atd_design, 'generate_downloadable_file');

        //Save design for later.
        $this->loader->add_action('wp_ajax_save_design_for_later', $atd_design, 'save_design_for_later_ajax');
        $this->loader->add_action('wp_ajax_nopriv_save_design_for_later', $atd_design, 'save_design_for_later_ajax');

        //Delete saved designs.
        $this->loader->add_action('wp_ajax_delete_saved_design', $atd_design, 'delete_saved_design_ajax');
        $this->loader->add_action('wp_ajax_nopriv_delete_saved_design', $atd_design, 'delete_saved_design_ajax');
        
        //Relatated custom product cart

        $this->loader->add_action('wp_ajax_add_related_custom_products_to_carts', $atd_design, 'add_related_custom_products_to_carts');
        $this->loader->add_action('wp_ajax_nopriv_add_related_custom_products_to_carts', $atd_design, 'add_related_custom_products_to_carts');

        //save order metas
        $this->loader->add_action('woocommerce_new_order_item', $atd_design, 'save_customized_item_meta', 10, 3);

        //get order meta
        $this->loader->add_action('woocommerce_after_order_itemmeta', $atd_design, 'get_order_custom_admin_data', 10, 3);

        //User account page
        $this->loader->add_action('woocommerce_order_item_meta_end', $atd_design, 'get_user_account_products_meta', 11, 4);
 
        //Send design as attachement.
        $this->loader->add_filter('woocommerce_email_attachments', $atd_design, 'add_order_design_to_mail', 10, 3);

        // Show custumize btn
        $this->loader->add_filter('woocommerce_loop_add_to_cart_link', $atd_design, 'atd_get_customize_btn_loop', 10, 2);


    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Atd_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
