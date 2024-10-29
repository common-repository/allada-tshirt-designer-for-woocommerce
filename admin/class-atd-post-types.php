<?php
/**
 * Atd post types class.
 *
 * @link       orionorigin@orionorigin.com
 * @since      1.0.0
 *
 * @package    Atd
 * @subpackage Atd/admin
 */

/**
 * Contains all methods and hooks callbacks related to the atd post types.
 *
 * @author HL
 *
 * @package    Atd
 * @subpackage Atd/admin
 * @author     orionorigin <orionorigin@orionorigin.com>
 */
class ATD_Post_Types {

    /**
     * Register the atd-colors-palettes postype.
     */
    public function register_cpt_colors_palette() {

        $labels = array(
            'name' => __('Colors palettes', 'allada-tshirt-designer-for-woocommerce'),
            'singular_name' => __('Colors palettes', 'allada-tshirt-designer-for-woocommerce'),
            'add_new' => __('New palette', 'allada-tshirt-designer-for-woocommerce'),
            'add_new_item' => __('New palette', 'allada-tshirt-designer-for-woocommerce'),
            'edit_item' => __('Edit palette', 'allada-tshirt-designer-for-woocommerce'),
            'new_item' => __('New palette', 'allada-tshirt-designer-for-woocommerce'),
            'view_item' => __('View palette', 'allada-tshirt-designer-for-woocommerce'),
            'not_found' => __('No palette found', 'allada-tshirt-designer-for-woocommerce'),
            'not_found_in_trash' => __('No palette in the trash', 'allada-tshirt-designer-for-woocommerce'),
            'menu_name' => __('Colors palettes', 'allada-tshirt-designer-for-woocommerce'),
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => false,
            'description' => 'Colors palettes for the product designer',
            'supports' => array('title'),
            'public' => false,
            'menu_icon' => 'dashicons-images-alt',
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'has_archive' => false,
            'query_var' => false,
            'can_export' => true,
        );

        register_post_type('atd-colors-palette', $args);
    }

    /**
     * Create the atd-colors-palette metabox.
     */
    public function get_colors_palette_metabox() {

        $screens = array('atd-colors-palette');

        foreach ($screens as $screen) {

            add_meta_box(
                    'atd-colors-palette-box', __('Colors palettes', 'allada-tshirt-designer-for-woocommerce'), array($this, 'get_colors_palettes_page'), $screen
            );
        }
    }

    /**
     * Get colors palette page.
     */
    public function get_colors_palettes_page() {

        $begin = array(
            'type' => 'sectionbegin',
            'id' => 'colors-palette-container',
        );

        $name = array(
            'title' => __('Name', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'name',
            'type' => 'text',
        );

        $code_hex = array(
            'title' => __('Hex code', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'code_hex',
            'class' => 'atd-color',
            'id' => 'colors-palette-code-hex',
            'type' => 'text',
        );

        $colors_palette = array(
            'title' => __('Components', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-colors-palette-data',
            'type' => 'repeatable-fields',
            'fields' => array($name, $code_hex),
            'desc' => __('Colors palettes', 'allada-tshirt-designer-for-woocommerce'),
            'ignore_desc_col' => true,
            'class' => 'striped',
            'add_btn_label' => __('Add color', 'allada-tshirt-designer-for-woocommerce'),
        );

        $end = array('type' => 'sectionend');
        $settings = array(
            $begin,
            $colors_palette,
            $end,
        );
        echo atd_o_admin_fields($settings);
        global $o_row_templates;
        ?>
        <input type="hidden" name="securite_nonce" value="<?php echo esc_html(wp_create_nonce('securite-nonce')); ?>"/>
        <script>
            var o_rows_tpl =<?php echo wp_json_encode($o_row_templates); ?>;
        </script>
        <?php
    }

    /**
     * Register the atd-cliparts postype.
     */
    public function register_cpt_cliparts() {

        $labels = array(
            'name' => __('Cliparts', 'allada-tshirt-designer-for-woocommerce'),
            'singular_name' => __('Cliparts', 'allada-tshirt-designer-for-woocommerce'),
            'add_new' => __('New cliparts group', 'allada-tshirt-designer-for-woocommerce'),
            'add_new_item' => __('New cliparts group', 'allada-tshirt-designer-for-woocommerce'),
            'edit_item' => __('Edit cliparts group', 'allada-tshirt-designer-for-woocommerce'),
            'new_item' => __('New cliparts group', 'allada-tshirt-designer-for-woocommerce'),
            'view_item' => __('View group', 'allada-tshirt-designer-for-woocommerce'),
            'not_found' => __('No cliparts group found', 'allada-tshirt-designer-for-woocommerce'),
            'not_found_in_trash' => __('No cliparts group in the trash', 'allada-tshirt-designer-for-woocommerce'),
            'menu_name' => __('Cliparts', 'allada-tshirt-designer-for-woocommerce'),
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => false,
            'description' => 'Cliparts for the product designer',
            'supports' => array('title'),
            'public' => false,
            'menu_icon' => 'dashicons-images-alt',
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'has_archive' => false,
            'query_var' => false,
            'can_export' => true,
        );

        register_post_type('atd-cliparts', $args);
    }

    /**
     * Create the atd-cliparts metabox.
     */
    public function get_cliparts_metabox() {

        $screens = array('atd-cliparts');

        foreach ($screens as $screen) {

            add_meta_box(
                    'atd-cliparts-box', __('Cliparts', 'allada-tshirt-designer-for-woocommerce'), array($this, 'get_cliparts_page'), $screen
            );
        }
    }

    /**
     * Get cliparts page.
     */
    public function get_cliparts_page() {
        wp_enqueue_media();
        ?>
        <div class='block-form'>
            <?php
            $begin = array(
                'type' => 'sectionbegin',
                'id' => 'cliparts-container',
            );

            $price = array(
                'title' => __('Price', 'allada-tshirt-designer-for-woocommerce'),
                'name' => 'price',
                'type' => 'number',
            );

            $name = array(
                'title' => __('Name', 'allada-tshirt-designer-for-woocommerce'),
                'name' => 'name',
                'type' => 'text',
            );

            $c_image = array(
                'title' => __('Icon', 'allada-tshirt-designer-for-woocommerce'),
                'name' => 'id',
                'type' => 'image',
                'set' => 'Set',
                'remove' => 'Remove',
                'desc' => __('Component icon', 'allada-tshirt-designer-for-woocommerce'),
            );

            $cliparts = array(
                'title' => __('Components', 'allada-tshirt-designer-for-woocommerce'),
                'name' => 'atd-cliparts-data',
                'type' => 'repeatable-fields',
                'fields' => array($c_image, $name, $price),
                'desc' => __('Cliparts', 'allada-tshirt-designer-for-woocommerce'),
                'ignore_desc_col' => true,
                'class' => 'striped',
                'add_btn_label' => __('Add clipart', 'allada-tshirt-designer-for-woocommerce'),
            );

            $end = array('type' => 'sectionend');
            $settings = apply_filters(
                    'atd_cliparts_settings', array(
                $begin,
                $cliparts,
                $end,
                    )
            );
            echo atd_o_admin_fields($settings);
            global $o_row_templates;
            ?>
        </div>
        <input type="hidden" name="securite_nonce" value="<?php echo esc_html(wp_create_nonce('securite-nonce')); ?>"/>
        <button class="button atd-add-cliparts"><?php esc_html_e('Add multiple cliparts', 'allada-tshirt-designer-for-woocommerce'); ?></button>
        <br><br><strong><?php esc_html_e('Note: ', 'allada-tshirt-designer-for-woocommerce'); ?></strong><?php esc_html_e('Please hold Ctrl or Cmd to select multiple cliparts at once from the medias popup.', 'allada-tshirt-designer-for-woocommerce'); ?> 
        <script>
            var o_rows_tpl =<?php echo wp_json_encode($o_row_templates); ?>;
        </script>
        <?php
    }

    /**
     * Register the custom post type of atd configurations.
     */
    public function register_cpt_config() {

        $labels = array(
            'name' => _x('Configurations', 'allada-tshirt-designer-for-woocommerce'),
            'singular_name' => _x('Configurations', 'allada-tshirt-designer-for-woocommerce'),
            'add_new' => _x('New configuration', 'allada-tshirt-designer-for-woocommerce'),
            'add_new_item' => _x('New configuration', 'allada-tshirt-designer-for-woocommerce'),
            'edit_item' => _x('Edit configuration', 'allada-tshirt-designer-for-woocommerce'),
            'new_item' => _x('New configuration', 'allada-tshirt-designer-for-woocommerce'),
            'view_item' => _x('View configuration', 'allada-tshirt-designer-for-woocommerce'),
            'not_found' => _x('No configuration found', 'allada-tshirt-designer-for-woocommerce'),
            'not_found_in_trash' => _x('No configuration in the trash', 'allada-tshirt-designer-for-woocommerce'),
            'menu_name' => _x('Product Designer', 'allada-tshirt-designer-for-woocommerce'),
            'all_items' => _x('Configurations', 'allada-tshirt-designer-for-woocommerce'),
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => false,
            'description' => 'Configurations',
            'supports' => array('title'),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'has_archive' => false,
            'query_var' => false,
            'can_export' => true,
        );

        register_post_type('atd-config', $args);
    }

    /**
     * Get meta box related to atd config post.
     */
    public function get_config_metabox() {

        $screens = array('atd-config');

        foreach ($screens as $screen) {
            add_meta_box(
                    'atd-metas-parts', __('Parts', 'allada-tshirt-designer-for-woocommerce'), array($this, 'get_config_parts_page'), $screen
            );
            add_meta_box(
                    'atd-metas-fonts', __('Fonts', 'allada-tshirt-designer-for-woocommerce'), array($this, 'get_config_fonts'), $screen
            );
            add_meta_box(
                    'atd-metas-cliparts', __('Cliparts', 'allada-tshirt-designer-for-woocommerce'), array($this, 'get_config_cliparts'), $screen
            );
            add_meta_box(
                    'atd-metas-team', __('Team', 'allada-tshirt-designer-for-woocommerce'), array($this, 'get_config_team'), $screen
            );

            if (class_exists('Ofb')) {
                add_meta_box(
                        'atd-config-form-builder', __('Form Builder', 'allada-tshirt-designer-for-woocommerce'), array($this, 'get_config_form_builder'), $screen
                );
            }
        }
    }

    /**
     * 
     * Get form builder config page
     */
    public function get_config_form_builder() {
        $begin = array(
            'type' => 'sectionbegin',
            'id' => 'atd-form-builder-container',
        );

        $args = array(
            'post_type' => 'ofb',
            'nopaging' => true,
        );

        $forms = array(
            'title' => __('Form Builder', 'allada-tshirt-designer-for-woocommerce'),
            'desc' => __('The selected form will be displayed on the customization page to gather additional informations from the customer.', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[form-builder]',
            'type' => 'post-type',
            'default' => 'default',
            'args' => $args,
        );

        $end = array(
            'type' => 'sectionend',
        );

        $settings = array(
            $begin,
            $forms,
            $end,
        );
        echo atd_o_admin_fields($settings);
    }

    /**
     * Get parts description page.
     */
    public function get_config_team() {
        $begin = array(
            'type' => 'sectionbegin',
            'id' => 'atd-team-settings',
        );

        $colors_palette = get_posts(
                array(
                    'post_status' => 'publish',
                    'post_type' => 'atd-colors-palette',
                    'nopaging' => true,
                )
        );
        $options = array('unlimited' => 'Unlimited');
        foreach ($colors_palette as $colors_palette) {
            $options[$colors_palette->ID] = $colors_palette->post_title;
        }

        $enable_team = array(
            'title' => __('Enable Team', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[team-settings][enable-team]',
            'default' => 'no',
            'desc' => esc_html__('Would you like to enable the teams features?', 'allada-tshirt-designer-for-woocommerce'),
            'type' => 'radio',
            'options' => array(
                'no' => esc_html__('No', 'allada-tshirt-designer-for-woocommerce'),
                'yes' => esc_html__('Yes', 'allada-tshirt-designer-for-woocommerce'),
            ),
            'row_class' => 'atd-enable-team',
        );

        $name_price = array(
            'title' => __('Price', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[team-settings][name][price]',
            'type' => 'number',
        );

        $name_min_height = array(
            'title' => esc_html__('Min height', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[team-settings][name][min-height]',
            'type' => 'number',
        );

        $name_max_height = array(
            'title' => esc_html__('Max height', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[team-settings][name][max-height]',
            'type' => 'number',
        );

        $name_step_height = array(
            'title' => esc_html__('Step height', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[team-settings][name][step-height]',
            'type' => 'number',
        );

        $name_default_height = array(
            'title' => esc_html__('Default height', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[team-settings][name][default-height]',
            'type' => 'number',
        );

        $name_height_unit = array(
            'title' => esc_html__('Unit', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[team-settings][name][height-unit]',
            'type' => 'select',
            'options' => array(
                'pt' => esc_html__('Point', 'allada-tshirt-designer-for-woocommerce'),
                'mm' => esc_html__('Millimeter', 'allada-tshirt-designer-for-woocommerce'),
                'px' => esc_html__('Pixels', 'allada-tshirt-designer-for-woocommerce'),
                'inch' => esc_html__('Inch', 'allada-tshirt-designer-for-woocommerce'),
            ),
            'default' => 'inch',
        );

        $names_colors_palette = array(
            'title' => __('Colors palette', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[team-settings][name][colors-palette]',
            'type' => 'select',
            'default' => '',
            'options' => $options,
        );

        $names = array(
            'title' => __('Names', 'allada-tshirt-designer-for-woocommerce'),
            'type' => 'groupedfields',
            'fields' => array($name_price, $name_min_height, $name_max_height, $name_step_height, $name_default_height, $name_height_unit, $names_colors_palette),
            'desc' => __('<b>Price</b>: Additional price per unit if enabled.</br><b>Max height</b>: Maximum height allowed for the letters in the defined unit.</br><b>Color palette</b>: Colors that can be used by the customer for each name.', 'allada-tshirt-designer-for-woocommerce'),
        );

        $number_price = array(
            'title' => __('Price', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[team-settings][number][price]',
            'type' => 'number',
        );

        $number_min_height = array(
            'title' => esc_html__('Min height', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[team-settings][number][min-height]',
            'type' => 'number',
        );

        $number_max_height = array(
            'title' => esc_html__('Max height', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[team-settings][number][max-height]',
            'type' => 'number',
        );

        $number_step_height = array(
            'title' => esc_html__('Step height', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[team-settings][number][step-height]',
            'type' => 'number',
        );

        $number_default_height = array(
            'title' => esc_html__('Default height', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[team-settings][number][default-height]',
            'type' => 'number',
        );

        $number_height_unit = array(
            'title' => esc_html__('Unit', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[team-settings][number][height-unit]',
            'type' => 'select',
            'options' => array(
                'pt' => esc_html__('Point', 'allada-tshirt-designer-for-woocommerce'),
                'mm' => esc_html__('Millimeter', 'allada-tshirt-designer-for-woocommerce'),
                'px' => esc_html__('Pixels', 'allada-tshirt-designer-for-woocommerce'),
                'inch' => esc_html__('Inch', 'allada-tshirt-designer-for-woocommerce'),
            ),
            'default' => 'inch',
        );

        $number_colors_palette = array(
            'title' => __('Colors palette', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[team-settings][number][colors-palette]',
            'type' => 'select',
            'default' => '',
            'options' => $options,
        );

        $numbers = array(
            'title' => __('Numbers', 'allada-tshirt-designer-for-woocommerce'),
            'type' => 'groupedfields',
            'fields' => array($number_price, $number_min_height, $number_max_height, $number_step_height, $number_default_height, $number_height_unit, $number_colors_palette),
            'desc' => __('<b>Price</b>: Additional price per unit if enabled.</br><b>Max height</b>: Maximum height allowed for the numbers in the defined unit.</br><b>Color palette</b>: Colors that can be used by the customer for each number.', 'allada-tshirt-designer-for-woocommerce'),
        );

        $end = array(
            'type' => 'sectionend',
        );

        $settings = array(
            $begin,
            $enable_team,
            $names,
            $numbers,
            $end,
        );
        echo atd_o_admin_fields($settings);
    }

    /**
     * Get parts description page.
     */
    public function get_config_cliparts() {
        $begin = array(
            'type' => 'sectionbegin',
            'id' => 'atd-cliparts-settings',
        );

        $use_global_cliparts = array(
            'title' => esc_html__('Use global cliparts', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[global-cliparts][use-global-cliparts]',
            'default' => 'yes',
            'desc' => esc_html__('Would you like to use all cliparts defined in Allada > Cliparts or only some of them?', 'allada-tshirt-designer-for-woocommerce'),
            'type' => 'radio',
            'options' => array(
                'no' => esc_html__('No', 'allada-tshirt-designer-for-woocommerce'),
                'yes' => esc_html__('Yes', 'allada-tshirt-designer-for-woocommerce'),
            ),
            'row_class' => 'atd-cliparts-group',
        );

        $clipart_groups = get_posts(
                array(
                    'post_status' => 'publish',
                    'post_type' => 'atd-cliparts',
                    'nopaging' => true,
                )
        );

        $options = array();
        foreach ($clipart_groups as $clipart_group) {
            $options[$clipart_group->ID] = $clipart_group->post_title;
        }

        $chosen_cliparts = array(
            'title' => esc_html__('Selected cliparts', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[global-cliparts][selected-cliparts]',
            'desc' => esc_html__('Please select the cliparts that should be available for use.', 'allada-tshirt-designer-for-woocommerce'),
            'type' => 'select',
            'options' => $options,
            'id' => 'atd_cliparts_selector',
            'custom_attributes' => array(
                'multiple' => 'multiple',
            ),
        );

        $end = array('type' => 'sectionend');
        $settings = array(
            $begin,
            $use_global_cliparts,
            $chosen_cliparts,
            $end,
        );
        echo atd_o_admin_fields($settings);
    }

    /**
     * Get configuration parts page.
     */
    public function get_config_parts_page() {
        wp_enqueue_media();

        $begin = array(
            'type' => 'sectionbegin',
            'id' => 'atd-design-area-settings',
        );

        $end = array('type' => 'sectionend');

        $front_enable = array(
            'title' => __('Enable front side', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[parts][front][enable]',
            'type' => 'radio',
            'options' => array(
                'no' => __('No', 'allada-tshirt-designer-for-woocommerce'),
                'yes' => __('Yes', 'allada-tshirt-designer-for-woocommerce')
            ),
            'default' => 'yes'
        );

        $front_label = array(
            'title' => __('Front side label', 'allada-tshirt-designer-for-woocommerce'),
            'type' => 'text',
            'name' => 'atd-metas[parts][front][name]',
            'default' => 'Front',
            'require' => 'required',
            'css' => 'width: 80px !important;',
        );

        $front_border_color = array(
            'title' => __('Border color', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[parts][front][border-color]',
            'class' => 'atd-color',
            'id' => 'design-area-border-color',
            'type' => 'text',
            'css' => 'width: 80px !important;',
        );

        $front = array(
            $begin,
            $front_enable,
            $front_label,
            $front_border_color,
            $end,
        );

        $back_enable = array(
            'title' => __('Enable back side', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[parts][back][enable]',
            'type' => 'radio',
            'options' => array(
                'no' => __('No', 'allada-tshirt-designer-for-woocommerce'),
                'yes' => __('Yes', 'allada-tshirt-designer-for-woocommerce')
            ),
            'default' => 'yes'
        );

        $back_label = array(
            'title' => __('Back side label', 'allada-tshirt-designer-for-woocommerce'),
            'type' => 'text',
            'name' => 'atd-metas[parts][back][name]',
            'default' => 'Back',
            'require' => 'required',
            'css' => 'width: 80px !important;',
        );

        $back_border_color = array(
            'title' => __('Border color', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[parts][back][border-color]',
            'class' => 'atd-color',
            'id' => 'design-area-border-color',
            'type' => 'text',
            'css' => 'width: 80px !important;',
        );

        $back = array(
            $begin,
            $back_enable,
            $back_label,
            $back_border_color,
            $end,
        );

        $left_enable = array(
            'title' => __('Enable left arm', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[parts][left][enable]',
            'type' => 'radio',
            'options' => array(
                'no' => __('No', 'allada-tshirt-designer-for-woocommerce'),
                'yes' => __('Yes', 'allada-tshirt-designer-for-woocommerce')
            ),
            'default' => 'yes'
        );

        $left_label = array(
            'title' => __('Left arm label', 'allada-tshirt-designer-for-woocommerce'),
            'type' => 'text',
            'name' => 'atd-metas[parts][left][name]',
            'default' => 'Left Arm',
            'require' => 'required',
            'css' => 'width: 80px !important;',
        );

        $left_border_color = array(
            'title' => __('Border color', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[parts][left][border-color]',
            'class' => 'atd-color',
            'id' => 'design-area-border-color',
            'type' => 'text',
            'css' => 'width: 80px !important;',
        );

        $left = array(
            $begin,
            $left_enable,
            $left_label,
            $left_border_color,
            $end,
        );

        $right_enable = array(
            'title' => __('Enable right arm', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[parts][right][enable]',
            'type' => 'radio',
            'options' => array(
                'no' => __('No', 'allada-tshirt-designer-for-woocommerce'),
                'yes' => __('Yes', 'allada-tshirt-designer-for-woocommerce')
            ),
            'default' => 'yes'
        );

        $right_label = array(
            'title' => __('Right arm label', 'allada-tshirt-designer-for-woocommerce'),
            'type' => 'text',
            'name' => 'atd-metas[parts][right][name]',
            'default' => 'Right Arm',
            'require' => 'required',
            'css' => 'width: 80px !important;',
        );

        $right_border_color = array(
            'title' => __('Border color', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[parts][right][border-color]',
            'class' => 'atd-color',
            'id' => 'design-area-border-color',
            'type' => 'text',
            'css' => 'width: 80px !important;',
        );

        $right = array(
            $begin,
            $right_enable,
            $right_label,
            $right_border_color,
            $end,
        );

        $chest_enable = array(
            'title' => __('Enable chest', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[parts][chest][enable]',
            'type' => 'radio',
            'options' => array(
                'no' => __('No', 'allada-tshirt-designer-for-woocommerce'),
                'yes' => __('Yes', 'allada-tshirt-designer-for-woocommerce')
            ),
            'default' => 'yes'
        );

        $chest_label = array(
            'title' => __('Chest label', 'allada-tshirt-designer-for-woocommerce'),
            'type' => 'text',
            'name' => 'atd-metas[parts][chest][name]',
            'default' => 'Chest',
            'require' => 'required',
            'css' => 'width: 80px !important;',
        );

        $chest_border_color = array(
            'title' => __('Border color', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[parts][chest][border-color]',
            'class' => 'atd-color',
            'id' => 'design-area-border-color',
            'type' => 'text',
            'css' => 'width: 80px !important;',
        );

        $chest = array(
            $begin,
            $chest_enable,
            $chest_label,
            $chest_border_color,
            $end,
        );

        echo atd_o_admin_fields($front);
        echo atd_o_admin_fields($back);
        echo atd_o_admin_fields($left);
        echo atd_o_admin_fields($right);
        echo atd_o_admin_fields($chest);
        global $o_row_templates;
        ?>
        <input type="hidden" name="securite_nonce" value="<?php echo esc_html(wp_create_nonce('securite-nonce')); ?>"/>
        <script>
            var o_rows_tpl =<?php echo wp_json_encode($o_row_templates); ?>;
        </script>
        <?php
    }

    /**
     * Get parts description page.
     */
    public function get_config_fonts() {
        $begin = array(
            'type' => 'sectionbegin',
            'id' => 'atd-fonts-settings',
        );

        $use_global_fonts = array(
            'title' => esc_html__('Use global fonts', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[global-fonts][use-global-fonts]',
            'default' => 'yes',
            'desc' => esc_html__('Would you like to use all fonts defined in Allada > Fonts or only some of them?', 'allada-tshirt-designer-for-woocommerce'),
            'type' => 'radio',
            'options' => array(
                'no' => esc_html__('No', 'allada-tshirt-designer-for-woocommerce'),
                'yes' => esc_html__('Yes', 'allada-tshirt-designer-for-woocommerce'),
            ),
            'row_class' => 'atd-fonts-type',
        );

        $atd_fonts = atd_refactor_fonts(get_option('atd-fonts'));
        $chosen_fonts = array(
            'title' => esc_html__('Selected fonts', 'allada-tshirt-designer-for-woocommerce'),
            'name' => 'atd-metas[global-fonts][selected-fonts]',
            'desc' => esc_html__('Please select the fonts that should be available for use.', 'allada-tshirt-designer-for-woocommerce'),
            'type' => 'select',
            'options' => $atd_fonts,
            'row_class' => 'show-if-fonts-global-no',
            'id' => 'atd_fonts_selector',
            'custom_attributes' => array(
                'multiple' => 'multiple',
            ),
        );

        $end = array('type' => 'sectionend');
        $settings = array(
            $begin,
            $use_global_fonts,
            $chosen_fonts,
            $end,
        );
        echo atd_o_admin_fields($settings);
    }

    /**
     * Get the order of meta box
     *
     * @param array $order The array of meta ordored.
     */
    public function get_metabox_order($order) {
        $order['advanced'] = 'atd-metas-parts,atd-metas-fonts,atd-metas-cliparts,atd-metas-team,atd-config-form-builder,submitdiv';
        return $order;
    }

    /**
     * Adds the Custom column to the default products list to help identify which ones are custom
     *
     * @param array $defaults Default columns.
     * @return array
     */
    public function get_product_columns($defaults) {
        $defaults['is_customizable'] = __('Custom', 'allada-tshirt-designer-for-woocommerce');
        return $defaults;
    }

    /**
     * Sets the Custom column value on the products list to help identify which ones are custom
     *
     * @param type $column_name Column name.
     * @param type $id Product ID.
     */
    public function get_products_columns_values($column_name, $id) {
        if ('is_customizable' === $column_name) {
            $atd_metas = get_post_meta($id, 'atd-metas', true);
            $product = wc_get_product($id);
            $product_type = $product->get_type();
            if ("simple" === $product_type) {
                if (isset($atd_metas[$id]['config-id'])) {
                    if (empty($atd_metas[$id]['config-id'])) {
                        esc_html_e('No', 'allada-tshirt-designer-for-woocommerce');
                    } else {
                        esc_html_e('Yes', 'allada-tshirt-designer-for-woocommerce');
                    }
                } else {
                    esc_html_e('No', 'allada-tshirt-designer-for-woocommerce');
                }
            } else {
                $is_customize = false;
                if(is_array($atd_metas)){
                    foreach ($atd_metas as $key => $atd_meta) {
                        if (isset($atd_meta['config-id'])) {
                            if (!empty($atd_meta['config-id'])) {
                                $is_customize = true;
                                break;
                            }
                        }
                    }
                }
                
                if ($is_customize) {
                    esc_html_e('Yes', 'allada-tshirt-designer-for-woocommerce');
                } else {
                    esc_html_e('No', 'allada-tshirt-designer-for-woocommerce');
                }
            }
        }
    }

}
