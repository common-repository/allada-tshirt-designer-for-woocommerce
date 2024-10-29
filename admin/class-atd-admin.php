<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.orionorigin.com/
 * @since      1.0.0
 *
 * @package    Atd
 * @subpackage Atd/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Atd
 * @subpackage Atd/admin
 * @author     ORION <support@orionorigin.com>
 */
class Atd_Admin {

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
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
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
        if (is_atd_admin_screen()) {
            wp_enqueue_style('atd-o-ui', plugin_dir_url(__FILE__) . 'css/UI.css', array(), $this->version, 'all');
            wp_enqueue_style('admin_css', plugin_dir_url(__FILE__) . 'css/atd-admin.css', false, '1.0.0');
            wp_enqueue_style('o-flexgrid', plugin_dir_url(__FILE__) . 'css/flexiblegs.css', array(), $this->version, 'all');
            wp_enqueue_style('select2-css', plugin_dir_url(__FILE__) . 'css/select2.min.css', array(), $this->version, 'all');
            wp_enqueue_style('atd-simplegrid', plugin_dir_url(__FILE__) . 'css/simplegrid.min.css', array(), $this->version, 'all');
            wp_enqueue_style('atd-tooltip-css', plugin_dir_url(__FILE__) . 'css/tooltip.min.css', array(), $this->version, 'all');
            wp_enqueue_style('atd-colorpicker-css', plugin_dir_url(__FILE__) . 'js/colorpicker/css/colorpicker.css', array(), $this->version, 'all');
            wp_enqueue_style('o-bs-modal-css', plugin_dir_url(__FILE__) . 'js/modal/modal.min.css', array(), $this->version, 'all');
        }
    }

    /**
     * Register the JavaScript for the admin area.
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
        if (is_atd_admin_screen()) {
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/atd-admin.js', array('jquery'), $this->version, false);
            wp_enqueue_script('atd-tabs-js', plugin_dir_url(__FILE__) . 'js/SpryTabbedPanels.min.js', array('jquery'), $this->version, false);
            wp_enqueue_script('atd-tooltip-js', plugin_dir_url(__FILE__) . 'js/tooltip.js', array('jquery'), $this->version, false);
            wp_enqueue_script('atd-colorpicker-js', plugin_dir_url(__FILE__) . 'js/colorpicker/js/colorpicker.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/atd-admin.js', array('jquery', 'select2-js'), $this->version, false);
            wp_localize_script($this->plugin_name, 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
            wp_enqueue_script('atd-jquery-cookie-js', plugin_dir_url(__FILE__) . 'js/jquery.cookie.min.js', array('jquery'), $this->version, false);
            wp_enqueue_script('o-admin', plugin_dir_url(__FILE__) . 'js/o-admin.js', array('jquery', 'jquery-ui-sortable'), $this->version, false);
            wp_localize_script('o-admin', 'home_url', array('ajax_url' => admin_url('admin-ajax.php')));
            wp_enqueue_script('select2-js', plugin_dir_url(__FILE__) . 'js/select2.min.js', array('jquery'), $this->version, 'all');
            wp_enqueue_script('o-modal-js', plugin_dir_url(__FILE__) . 'js/modal/modal.min.js', array('jquery'), $this->version, 'all');
        }

        wp_enqueue_script('o-extra-js', plugin_dir_url(__FILE__) . 'js/extra.js', array('jquery'), $this->version, 'all');
    }

    /**
     * Gets the settings and put them in a global variable.
     *
     * @global array $atd_settings Settings
     */
    public function init_globals() {
        global $atd_settings;
        $atd_settings['atd-general-options'] = get_option('atd-general-options');
        $atd_settings['atd-upload-options'] = get_option('atd-upload-options');
        $atd_settings['atd-colors-options'] = get_option('atd-colors-options');
        $atd_settings['atd-licence'] = get_option('atd-licence');
    }

    /**
     * Alerts the administrator if the minimum requirements are not met.
     */
    public function notify_prerequisites() {
        global $atd_settings;
        $messages = array();
        $options = $atd_settings['atd-general-options'];
        $atd_page_id = $options['atd_page_id'];
        $settings_url = get_bloginfo('url') . '/wp-admin/admin.php?page=atd-manage-settings';
        $minimum_required_parameters = array(
            'memory_limit' => array(128, 'M'),
            'post_max_size' => array(8, 'M'),
            'upload_max_filesize' => array(32, 'M'),
        );
        $permalinks_structure = get_option('permalink_structure');

        if (!class_exists('WooCommerce')) {
            $messages[] = "WooCommerce is not installed on your website. You will not be able to use the features of the plugin";
        } else if (empty($atd_page_id)) {
            $messages[] = "The design page is not defined. Please set one here <a href='" . esc_html($settings_url) . "'>plugin settings page</a>: .</p>";
        } else if (!extension_loaded('zip')) {
            $messages[] = "ZIP extension not loaded on this server. You won't be able to generate zip outputs.</p>";
        } else if (!class_exists('Imagick')) {
            $messages[] = "Imagick classes not installed on this server. You won't be able to generate cmyk outputs or handle adobe files conversion to image.</p>";
        }

        foreach ($minimum_required_parameters as $key => $min_arr) {
            $defined_value = ini_get($key);
            if (!stristr($defined_value, 'G', true)) {
                $defined_value_int = str_replace($min_arr[1], '', $defined_value);
                if ($defined_value_int < $min_arr[0]) {
                    $messages[] = "Your PHP setting <b>$key</b> is currently set to <b>$defined_value</b>. We recommand to set this value at least to <b>" . implode('', $min_arr) . "</b> to avoid any issue with our plugin.<br><br><b>" . esc_html__('How to fix this: You can edit your php.ini file to increase the specified variables to the recommanded values or you can ask your hosting company to make the changes for you.', 'allada-tshirt-designer-for-woocommerce') . "</b>";
                }
            }
        }

        if (strpos($permalinks_structure, 'index.php') !== false) {
            $message .= 'Your <a href="' . esc_url(admin_url() . 'options-permalink.php') . '">permalinks</a> structure is currently set to <b>custom</b> with index.php present in the structure. We recommand to set this value to <b>Post name</b> to avoid any issue with our plugin.<br>';
        }

        if (isset($messages) && !empty($messages)) {
            foreach ($messages as $key => $message) {
                ?>
                <div class="error">
                    <p>
                        <b>Allalda t-shirt designer: </b>
                        <br>
                        <?php esc_html_e($message); ?>
                    </p>
                </div>
                <?php
            }
        }
    }

    /**
     * Builds all the plugin menu and submenu.
     */
    public function add_submenu() {
        global $submenu;
        $icon = ATD_URL . 'admin/img/icons/allada-dashicon.png';
        if (class_exists('WooCommerce')) {
            add_menu_page('Allalda t-shirt designer', 'Allada', 'manage_product_terms', 'atd-manage-dashboard', array($this, 'get_fonts_page'), $icon);
            add_submenu_page('atd-manage-dashboard', __('Fonts', 'allada-tshirt-designer-for-woocommerce'), __('Fonts', 'allada-tshirt-designer-for-woocommerce'), 'manage_product_terms', 'atd-manage-fonts', array($this, 'get_fonts_page'));
            add_submenu_page('atd-manage-dashboard', __('Clipart', 'allada-tshirt-designer-for-woocommerce'), __('Clipart', 'allada-tshirt-designer-for-woocommerce'), 'manage_product_terms', 'edit.php?post_type=atd-cliparts', false);
            add_submenu_page('atd-manage-dashboard', __('Colors palettes', 'allada-tshirt-designer-for-woocommerce'), __('Colors palettes', 'allada-tshirt-designer-for-woocommerce'), 'manage_product_terms', 'edit.php?post_type=atd-colors-palette', false);
            add_submenu_page('atd-manage-dashboard', __('Configurations', 'allada-tshirt-designer-for-woocommerce'), __('Configurations', 'allada-tshirt-designer-for-woocommerce'), 'manage_product_terms', 'edit.php?post_type=atd-config', false);
            if (class_exists('Ofb')) {
                add_submenu_page('atd-manage-dashboard', __('Forms', 'allada-tshirt-designer-for-woocommerce'), __('Forms', 'allada-tshirt-designer-for-woocommerce'), 'manage_product_terms', 'edit.php?post_type=ofb', false);
            }
            add_submenu_page('atd-manage-dashboard', __('Settings', 'allada-tshirt-designer-for-woocommerce'), __('Settings', 'allada-tshirt-designer-for-woocommerce'), 'manage_product_terms', 'atd-manage-settings', array($this, 'get_settings_page'));
            $submenu['atd-manage-dashboard'][] = array('<div id="user-manual">User Manual</div>', 'manage_product_terms', 'https://designersuiteforwp.com/documentation/allada-woocommerce-custom-t-shirt-designer');
        }
    }

    /**
     * Builds the fonts management page
     */
    public function get_fonts_page() {
        include_once ATD_DIR . '/includes/atd-add-fonts.php';
        ?>
        <input type="hidden" name="securite_nonce" value="<?php echo esc_html(wp_create_nonce('securite-nonce')); ?>"/>
        <?php
        atd_add_fonts();
    }

    public function get_settings_page() {
        if (isset($_POST['securite_nonce'])) {
            if (wp_verify_nonce(wp_unslash(sanitize_key($_POST['securite_nonce'])), 'securite-nonce')) {
                $this->save_atd_tab_options();
                global $wp_rewrite;
                $wp_rewrite->flush_rules(false);
            }
        }

        // if (isset($_POST['atd-licence']) && empty(get_option("atd-license-key"))) {
        //     global $atd_settings;
        //     $purchase_code = $atd_settings['atd-licence']['purchase-code'];
        //     if (isset($purchase_code) && !empty($purchase_code)) {
        //         $license_activation_result = $this->activate_license(false);
        //         switch ($license_activation_result) {
        //             case '200':
                        ?>
                            <!-- <div class="notice notice-success">
                                <p><b>T-shirt Product Designer: </b><?php //_e("Activation succeded! Your product is now activated.", 'allada-tshirt-designer-for-woocommerce'); ?></p>
                            </div> -->
                        <?php
        //                 break;
        //             default:
                        ?>
                            <!-- <div class="notice notice-error">
                                <p><b>T-shirt Product Designer: </b><?php //_e($license_activation_result, 'allada-tshirt-designer-for-woocommerce'); ?></p>
                            </div> -->
                        <?php
        //                 break;
        //         }
        //     } else {
                ?>
                    <!-- <div class="notice notice-warning">
                        <p><b>T-shirt Product Designer: </b><?php //_e("No licence key found in the settings. Please click <a href='admin.php?page=atd-manage-settings'>here</a> to define one.", 'allada-tshirt-designer-for-woocommerce'); ?></p>
                    </div> -->
                <?php
        //     }
        // }
        wp_enqueue_media();
        ?>

        <form method="POST">
            <div id="atd-settings">
                <div class="wrap">
                    <h2><?php esc_html_e('Products Base Settings', 'allada-tshirt-designer-for-woocommerce'); ?></h2>
                </div>
                <div id="TabbedPanels1" class="TabbedPanels">
                    <ul class="TabbedPanelsTabGroup ">
                        <li class="TabbedPanelsTab " tabindex="1"><span><?php esc_html_e('General', 'allada-tshirt-designer-for-woocommerce'); ?></span> </li>
                        <li class="TabbedPanelsTab" tabindex="2"><span><?php esc_html_e('Uploads', 'allada-tshirt-designer-for-woocommerce'); ?> </span></li>
                        <li class="TabbedPanelsTab" tabindex="3"><span><?php esc_html_e('Colors', 'allada-tshirt-designer-for-woocommerce'); ?></span></li>
                        <!-- <li class="TabbedPanelsTab" tabindex="4"><span><?php esc_html_e('Licence', 'allada-tshirt-designer-for-woocommerce'); ?></span></li> -->

                    </ul>
                    <div class="TabbedPanelsContentGroup">
                        <div class="TabbedPanelsContent">
                            <div class='atd-grid atd-grid-pad'>
                                <?php
                                $this->get_general_settings();
                                ?>
                            </div>
                        </div>
                        <div class="TabbedPanelsContent">
                            <div class='atd-grid atd-grid-pad'>
                                <?php
                                $this->get_uploads_settings();
                                ?>
                            </div>
                        </div>
                        <div class="TabbedPanelsContent">
                            <div class="atd-grid atd-grid-pad">
                                <?php
                                $this->get_colors_settings();
                                ?>
                            </div> 
                        </div>
                        <!-- <div class="TabbedPanelsContent">
                            <div class='atd-grid atd-grid-pad'>
                                <?php
                                // $this->get_licence_settings();
                                ?>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
            <input type="submit" value="<?php esc_html_e('Save', 'allada-tshirt-designer-for-woocommerce'); ?>" class="button button-primary button-large mg-top-10-i">
        </form>
        <?php
    }

    /**
     * Save the settings
     */
    private function save_atd_tab_options() {
        if (isset($_POST['securite_nonce'])) {
            if (wp_verify_nonce(wp_unslash(sanitize_key($_POST['securite_nonce'])), 'securite-nonce')) {
                $key_table = ['atd-general-options', 'atd-upload-options', 'atd-colors-options', 'atd-licence'];
                foreach ($key_table as $key) {
                    if (isset($_POST[$key])) {
                        update_option($key, wp_unslash($_POST[$key]));
                    }
                }
                $this->init_globals();
                ?>
                <div id="message" class="updated below-h2"><p><?php esc_html_e('Settings successfully saved.', 'allada-tshirt-designer-for-woocommerce'); ?></p></div>
                <?php
            }
        }
    }

    /**
     * Builds the social networks settings options
     *
     * @return array Settings
     */
    private function get_licence_settings() {
        $options = array();
        $licence_begin = array(
            'type' => 'sectionbegin',
            'id' => 'atd-licence',
            'title' => __('Licence Settings', 'allada-tshirt-designer-for-woocommerce'),
            'table' => 'options',
        );

        $licence_end = array(
            'type' => 'sectionend',
            'id' => 'atd-licence',
        );
        $purchase_code = array(
            'title' => __('License key', 'allada-tshirt-designer-for-woocommerce'),
            'desc' => ' ' . __('Licence key received after your purchase. <a href="http://designersuiteforwp.com/my-account/orders/" target="blank">Where is my licence key?</a>.', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-licence[purchase-code]',
            'type' => 'text',
            'default' => '',
        );

        array_push($options, $licence_begin);
        array_push($options, $purchase_code);
        array_push($options, $licence_end);
        echo atd_o_admin_fields($options);
        ?>
        <input type="hidden" name="securite_nonce" value="<?php echo esc_html(wp_create_nonce('securite-nonce')); ?>"/>
        <?php
    }

    /**
     * Get Woocommerce Page.
     *
     * @return array
     */
    private function get_woocommerce_page() {
        $woocommerce_page = array();
        array_push($woocommerce_page, get_option('woocommerce_shop_page_id'));
        array_push($woocommerce_page, get_option('woocommerce_cart_page_id'));
        array_push($woocommerce_page, get_option('woocommerce_checkout_page_id'));
        array_push($woocommerce_page, get_option('woocommerce_myaccount_page_id'));
        array_push($woocommerce_page, get_option('woocommerce_terms_page_id'));
        array_push($woocommerce_page, get_option('woocommerce_thanks_page_id'));
        array_push($woocommerce_page, get_option('woocommerce_edit_address_page_id'));
        array_push($woocommerce_page, get_option('woocommerce_edit_address_page_id'));
        array_push($woocommerce_page, get_option('woocommerce_pay_page_id'));
        array_push($woocommerce_page, get_option('woocommerce_view_order_page_id'));
        return $woocommerce_page;
    }

    /**
     * Get general settings of product designer base
     */
    private function get_general_settings() {
        $options = array();

        $general_options_begin = array(
            'type' => 'sectionbegin',
            'id' => 'atd-general-options',
            'table' => 'options',
            'title' => __('General Settings', 'allada-tshirt-designer-for-woocommerce'),
        );

        $args = array(
            'post_type' => 'page',
            'nopaging' => true,
            'exclude' => $this->get_woocommerce_page(),
        );

        $customizer_page = array(
            'title' => __('Design page', 'allada-tshirt-designer-for-woocommerce'),
            'desc' => __('This setting allows the plugin to locate the page where customizations are made.', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-general-options[atd_page_id]',
            'type' => 'post-type',
            'default' => '',
            'class' => 'chosen_select_nostd',
            'args' => $args,
        );

        $hide_cart_button_for_custom_products = array(
            'title' => __('Disable add to cart button for custom products', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-general-options[atd-hide-cart-button]',
            'default' => '1',
            'type' => 'radio',
            'desc' => __('Show/hide the add to cart button for custom products on the store pages.', 'allada-tshirt-designer-for-woocommerce'),
            'options' => array(
                '1' => __('Yes', 'allada-tshirt-designer-for-woocommerce'),
                '0' => __('No', 'allada-tshirt-designer-for-woocommerce'),
            ),
            'class' => 'chosen_select_nostd',
        );

        $hide_design_buttons_cart_page = array(
            'title' => esc_html__('Hide design buttons on shop page', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-general-options[atd-hide-design-btn-shop-pages]',
            'default' => '0',
            'type' => 'radio',
            'desc' => esc_html__('This option allows you to show/hide the design button on the shop page', 'allada-tshirt-designer-for-woocommerce'),
            'options' => array(
                '1' => esc_html__('Yes', 'allada-tshirt-designer-for-woocommerce'),
                '0' => esc_html__('No', 'allada-tshirt-designer-for-woocommerce'),
            ),
            'class' => 'chosen_select_nostd',
        );

        $general_options_end = array('type' => 'sectionend');

        $conflicts_options_begin = array(
            'type' => 'sectionbegin',
            'id' => 'atd_conflicts_options',
            'title' => __('Scripts management', 'allada-tshirt-designer-for-woocommerce'),
            'table' => 'options',
        );

        $conflicts_options_end = array('type' => 'sectionend');

        array_push($options, $general_options_begin);
        array_push($options, $customizer_page);
        array_push($options, $hide_cart_button_for_custom_products);
        array_push($options, $hide_design_buttons_cart_page);
        array_push($options, $general_options_end);
        array_push($options, $conflicts_options_begin);
        array_push($options, $conflicts_options_end);

        $options = apply_filters('atd_general_options', $options);
        ?>
        <button id="atd-create-design-page" type="submit">Create</button>
        <?php
        echo atd_o_admin_fields($options);
        ?>

        <input type="hidden" name="securite_nonce" value="<?php echo esc_html(wp_create_nonce('securite-nonce')); ?>"/>
        <?php
    }

    /**
     * Get related products content.
     */
    public function create_design_page_ajx() {   
        $page_name = filter_input(INPUT_POST, 'name_page');
        $page_exist = get_page_by_title($page_name);
        if (!empty($page_exist)) {
            echo'Page already exists';
        } else {
            $page_id = wp_insert_post(
                    array(
                        'post_title' => ucwords($page_name),
                        'post_name' => strtolower(str_replace(' ', '-', trim($page_name))),
                        'post_status' => 'publish',
                        'post_type' => 'page',
                    )
            );
            if ($page_id) {
                $atd_general_option = get_option('atd-general-options');
                $atd_general_option['atd_page_id'] = $page_id;
                $bool = update_option('atd-general-options', $atd_general_option);
                if (!$bool) {
                    echo 'La page à été créer avec succès mais une erreur c\'est produite lors de l\'assignation.';
                } else {
                    echo 'Page créer et assigner avec succès';
                }
            } else {
                echo 'Un problème est survenu lors de la création de la page';
            }
        }
        die();
    }

    /**
     * Builds the uploads settings options
     */
    private function get_uploads_settings() {

        $min_upload_w = array(
            'title' => __('Uploads min width (px)', 'allada-tshirt-designer-for-woocommerce'),
            'desc' => __('Customers images minimum required width', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-upload-options[atd-min-upload-width]',
            'type' => 'number',
            'default' => '',
        );
        $min_upload_h = array(
            'title' => __('Uploads min height (px)', 'allada-tshirt-designer-for-woocommerce'),
            'desc' => __('Customers images minimum required height', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-upload-options[atd-min-upload-height]',
            'type' => 'number',
            'default' => '',
        );
        $upl_extensions = array(
            'title' => __('Allowed uploads extensions', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-upload-options[atd-upl-extensions]',
            'default' => array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'),
            'type' => 'multiselect',
            'desc' => __('Allowed extensions for uploads', 'allada-tshirt-designer-for-woocommerce'),
            'options' => array(
                'jpg' => __('jpg', 'allada-tshirt-designer-for-woocommerce'),
                'jpeg' => __('jpeg', 'allada-tshirt-designer-for-woocommerce'),
                'png' => __('png', 'allada-tshirt-designer-for-woocommerce'),
                'gif' => __('gif', 'allada-tshirt-designer-for-woocommerce'),
                'bmp' => __('bmp', 'allada-tshirt-designer-for-woocommerce'),
                'svg' => __('svg', 'allada-tshirt-designer-for-woocommerce'),
            ),
        );

        $upload_settings_begin = array(
            'type' => 'sectionbegin',
            'id' => 'atd-upload-options',
            'title' => __('Uploads Settings', 'allada-tshirt-designer-for-woocommerce'),
            'table' => 'options',
        );

        $upload_settings_end = array(
            'type' => 'sectionend',
            'id' => 'atd-upload-options',
        );

        $options = array();
        array_push($options, $upload_settings_begin);
        array_push($options, $min_upload_w);
        array_push($options, $min_upload_h);
        array_push($options, $upl_extensions);
        array_push($options, $upload_settings_end);
        echo atd_o_admin_fields($options);
        ?>
        <input type="hidden" name="securite_nonce" value="<?php echo esc_html(wp_create_nonce('securite-nonce')); ?>"/>
        <?php
    }

    /**
     * Builds the colors settings options
     */
    private function get_colors_settings() {
        $options = array();

        $svg_colors = array(
            'title' => __('Enable SVG colorization', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-colors-options[atd-svg-colorization]',
            'default' => '1',
            'type' => 'radio',
            'desc' => __('This option allows you to enable or disable the ability for customers to change the colors of the SVG files used in their designs.', 'allada-tshirt-designer-for-woocommerce'),
            'options' => array(
                '1' => __('Yes', 'allada-tshirt-designer-for-woocommerce'),
                '0' => __('No', 'allada-tshirt-designer-for-woocommerce'),
            ),
            'class' => 'chosen_select_nostd',
        );

        $cpt_colors_palettes = get_posts(
                array(
                    'post_status' => 'publish',
                    'post_type' => 'atd-colors-palette',
                    'nopaging' => true,
                )
        );
        $cpt_colors_palettes_options = array('unlimited' => 'Unlimited');
        foreach ($cpt_colors_palettes as $cpt_colors_palette) {
            $cpt_colors_palettes_options[$cpt_colors_palette->ID] = $cpt_colors_palette->post_title;
        }
        $colors_palette = array(
            'title' => __('Colors palettes', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-colors-options[atd-color-palette]',
            'default' => '',
            'desc' => __('This option allows you would like your clients to use in their designs', 'allada-tshirt-designer-for-woocommerce'),
            'type' => 'select',
            'options' => $cpt_colors_palettes_options,
        );
        $colors_options_begin = array(
            'type' => 'sectionbegin',
            'id' => 'atd-colors-options',
            'title' => __('Colors Settings', 'allada-tshirt-designer-for-woocommerce'),
            'table' => 'options',
        );

        $colors_options_end = array(
            'type' => 'sectionend',
            'id' => 'atd-colors-options',
        );
        array_push($options, $colors_options_begin);
        array_push($options, $svg_colors);
        array_push($options, $colors_palette);
        array_push($options, $colors_options_end);
        echo atd_o_admin_fields($options);
        ?>
        <input type="hidden" name="securite_nonce" value="<?php echo esc_html(wp_create_nonce('securite-nonce')); ?>"/>
        <?php
    }

    /**
     * Get the max input vars number of php ini file.
     */
    public function get_max_input_vars_php_ini() {
        $total_max_normal = ini_get('max_input_vars');
        $msg = 'Your max input var is <strong>' . $total_max_normal . '</strong> but this page contains <strong>{nb}</strong> fields. You may experience a lost of data after saving. In order to fix this issue, please increase <strong>the max_input_vars</strong> value in your php.ini file.';
        ?>
        <script type="text/javascript">
            var o_max_input_vars = <?php echo esc_html($total_max_normal); ?>;
            var o_max_input_msg = "<?php echo esc_html($msg); ?>";
        </script>         
        <?php
    }

    /*function get_license_activation_notice() {
        global $atd_settings;
        $purchase_code = $atd_settings['atd-licence']['purchase-code'];
        if (empty($purchase_code)) {
            ?>
            <div class="notice notice-warning">
                <p><b>T-shirt Product Designer: </b><?php _e("No licence key found in the settings. Please click <a href='admin.php?page=atd-manage-settings'>here</a> to define one.", 'allada-tshirt-designer-for-woocommerce'); ?></p>
            </div>
            <?php
        }
    }*/

    /*function activate_license($is_ajax = true) {
        global $atd_settings;
        $purchase_code = $atd_settings['atd-licence']['purchase-code'];
        if (isset($purchase_code) && !empty($purchase_code)) {
            $site_url = get_site_url();
            $url = 'https://designersuiteforwp.com/service/olicenses/v1/license/?purchase-code=' . $purchase_code . '&siteurl=' . urlencode($site_url);
            $args = array('timeout' => 60);
            $response = wp_remote_get($url, $args);
            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                if (is_ajax()) {
                    echo "Something went wrong: $error_message";
                } else {
                    return "Something went wrong: $error_message";
                }
            }
            if (isset($response['body'])) {
                $answer = $response['body'];
            }

            if (is_array(json_decode($answer, true))) {
                $data = json_decode($answer, true);
                if (isset($data['key']) && !empty($data['key'])) {
                    update_option('atd-license-key', $data['key']);
                    set_transient('atd-license-checking', 'valid', 1 * WEEK_IN_SECONDS);
                    if (is_ajax()) {
                        echo '200';
                    } else {
                        return '200';
                    }
                } else {
                    if (is_ajax()) {
                        echo $data['message'];
                    } else {
                        return $data['message'];
                    }
                }
            } else {
                if (is_ajax()) {
                    echo $answer;
                } else {
                    return $answer;
                }
            }
        } else {
            if (is_ajax()) {
                echo "Purchase code not found. Please, set your purchase code in the plugin's settings.";
            } else {
                return "Purchase code not found. Please, set your purchase code in the plugin's settings.";
            }
        }
        if (is_ajax()) {
            die();
        }
    }*/
    
    /*function o_verify_validity() {
	global $atd_settings;
	$purchase_code = $atd_settings[ 'atd-licence' ][ 'purchase-code' ];
	if ( is_admin() && get_transient( "atd-license-checking" ) !== 'valid' ) {
	    if ( isset( $purchase_code ) && ! empty( $purchase_code ) ) {
		$site_url	 = get_site_url();
		$url		 = "https://designersuiteforwp.com/service/olicenses/v1/checking/?license-key=" . $purchase_code . "&siteurl=" . urlencode( $site_url );
		//$url = "https://tests.designersuiteforwp.com/service/olicenses/v1/checking/?license-key=" . $purchase_code . "&siteurl=" . urlencode( $site_url );
		$args		 = array( 'timeout' => 60 );
		$response	 = wp_remote_get( $url, $args );
		if ( ! is_wp_error( $response ) ) {
		    if ( isset( $response[ "body" ] ) && intval( $response[ "body" ] ) == 403 ) {
			delete_option( "atd-license-key" );
		    }
		}
	    } else {
		if ( get_option( "atd-license-key" ) ) {
		    delete_option( "atd-license-key" );
		}
	    }
	    set_transient( 'atd-license-checking', 'valid', 1 * WEEK_IN_SECONDS );
	}
    }*/

    /**
     * Runs the new version check and upgrade process
     *
     * @return \ATD_Updater
     */
//    function get_updater() {
//        $process = apply_filters('atd_include_updater_files', true);
//        if ($process) {
//            require_once ATD_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'updaters' . DIRECTORY_SEPARATOR . 'class-atd-updater.php';
//            $updater = new ATD_Updater();
//            $updater->init();
//            require_once ATD_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'updaters' . DIRECTORY_SEPARATOR . 'class-atd-updating-manager.php';
//            $updater->setUpdateManager(new ATD_Updating_Manager(ATD_VERSION, $updater->versionUrl(), ATD_MAIN_FILE));
//
//            return $updater;
//        }
//    }
    

}
