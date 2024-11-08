<?php

/**
 * Manage update messages and Plugins info for WPC in default WordPress plugins list.
 */
class ATD_Updating_Manager {

    /**
     * The plugin current version
     *
     * @var string
     */
    public $current_version;

    /**
     * The plugin remote update path
     *
     * @var string
     */
    public $update_path;

    /**
     * Plugin Slug (plugin_directory/plugin_file.php)
     *
     * @var string
     */
    public $plugin_slug;

    /**
     * Plugin name (plugin_file)
     *
     * @var string
     */
    public $slug;

    /**
     * Link to download VC.
     *
     * @var string
     */
    protected $url = '';

    /**
     * Initialize a new instance of the WordPress Auto-Update class
     *
     * @param string $current_version
     * @param string $update_path
     * @param string $plugin_slug
     */
    function __construct($current_version, $update_path, $plugin_slug) {
        // Set the class public variables
        $this->current_version = $current_version;
        $this->update_path = $update_path;
        $this->plugin_slug = $plugin_slug;
        $t = explode('/', $plugin_slug);
        $this->slug = str_replace('.php', '', $t[1]);

        // define the alternative API for updating checking
        //add_filter('pre_set_site_transient_update_plugins', array(&$this, 'check_update'));

        // Define the alternative response for information checking
        add_filter('plugins_api', array(&$this, 'check_info'), 10, 3);

        add_action('in_plugin_update_message-' . ATD_MAIN_FILE, array(&$this, 'addUpgradeMessageLink'));
    }

    /**
     * Add our self-hosted autoupdate plugin to the filter transient
     *
     * @param $transient
     * @return object $ transient
     */
    public function check_update($transient) {

        if (empty($transient->checked)) {
            return $transient;
        }

        // Get the remote version
        $remote_version = $this->getRemote_version();
        $plugin = new Atd();

        var_dump(version_compare($this->current_version, $remote_version, '<'));
        // If a newer version is available, add the update
        if (version_compare($this->current_version, $remote_version, '<')) {
            $plugin_admin = new ATD_Admin($plugin->get_plugin_name(), $plugin->get_version());
            $obj = new stdClass();
            $obj->slug = $this->slug;
            $obj->new_version = $remote_version;
            $obj->url = ''; // $this->update_path;
            $obj->package = ''; // $this->update_path;
            $obj->name = $plugin_admin->get_updater()->title;
            $transient->response[$this->plugin_slug] = $obj;
        }

        return $transient;
    }

    /**
     * Add our self-hosted description to the filter
     *
     * @param boolean $false
     * @param array   $action
     * @param object  $arg
     * @return bool|object
     */
    public function check_info($false, $action, $arg) {
        if (isset($arg->slug) && $arg->slug === $this->slug) {
            $information = $this->getRemote_information();
            $array_pattern = array(
                '/^([\*\s])*(\d\d\.\d\d\.\d\d\d\d[^\n]*)/m',
                '/^\n+|^[\t\s]*\n+/m',
                '/\n/',
            );
            $array_replace = array(
                '<h4>$2</h4>',
                '</div><div>',
                '</div><div>',
            );

            $formatted_info = new stdClass();
            $plugin = new Atd();
            $plugin_admin = new ATD_Admin($plugin->get_plugin_name(), $plugin->get_version());
            $formatted_info->name = $plugin_admin->get_updater()->title;
            $formatted_info->slug = 'atd.php';
            $formatted_info->plugin_name = 'atd.php';

            if ($information) {
                $formatted_info->new_version = "$information->latest";
                $formatted_info->last_updated = $information->lastupdated;
                $formatted_info->sections = array(
                    "description" => "A smart and flexible extension which lets you setup any customizable product your customers can configure visually prior to purchase.",
                    "changelog" => '<div>' . preg_replace($array_pattern, $array_replace, $information->changelog) . '</div>');
            } else {
                $formatted_info->new_version = "";
                $formatted_info->last_updated = "";
                $formatted_info->sections = array(
                    "description" => "A smart and flexible extension which lets you setup any customizable product your customers can configure visually prior to purchase.",
                    "changelog" => '<div>' . __("Unable to retrieve update details.", 'allada-tshirt-designer-for-woocommerce') . '</div>');
            }
            // $information->name = $plugin_admin->get_updater()->title;
            // $information->changelog = '<div>xxx' . preg_replace( $array_pattern, $array_replace, $information->changelog ) . '</div>';
            return $formatted_info;
        }
        return $false;
    }

    /**
     * Return the remote version
     *
     * @return string $remote_version
     */
    public function getRemote_version() {
        $information = $this->getRemote_information();
        if ($information) {
            return "$information->latest";
        } else {
            return false;
        }
    }

    /**
     * Get information about the remote version
     *
     * @return bool|object
     */
    public function getRemote_information() {
        $args = array(
            'timeout' => '10',
        );
        $response = wp_remote_get($this->update_path, $args);
        $http_code = wp_remote_retrieve_response_code($response);
        if (200 == $http_code) {
            $notifier_data = wp_remote_retrieve_body($response);
        } else {
            $notifier_data = false;
        }
        //$notifier_data = file_get_contents($this->update_path);

        if ($notifier_data) {
            // Let's see if the $xml data was returned as we expected it to.
            // If it didn't, use the default 1.0 as the latest version so that we don't have problems when the remote server hosting the XML file is down
            if (strpos((string) $notifier_data, '<notifier>') === false) {
                $notifier_data = '<?xml version="1.0" encoding="UTF-8"?><notifier><latest>1.0</latest><changelog></changelog></notifier>';
            }
        }
        // if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
        // $xml = simplexml_load_string( $notifier_data );
        // return $xml;
        // }
        $xml = simplexml_load_string($notifier_data);
        return $xml;
    }

    /**
     * Return the status of the plugin licensing
     *
     * @return boolean $remote_license
     */
    public function getRemote_license() {
        $request = wp_remote_post($this->update_path, array('body' => array('action' => 'license')));
        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
            return $request['body'];
        }
        return false;
    }

    /**
     * Shows message on Wp plugins page with a link for updating from envato.
     */
    public function addUpgradeMessageLink() {
        global $atd_settings;
        $purchase_code = $atd_settings['atd-licence']['purchase-code'];
        echo '<style type="text/css" media="all">tr#wpbakery-visual-composer + tr.plugin-update-tr a.thickbox + em { display: none; }</style>';
        if (empty($purchase_code)) {
            echo ' <a href="' . $this->url . '">' . __('Download new version from Desingersuite.', 'allada-tshirt-designer-for-woocommerce') . '</a>';
        } else {
            echo '<a href="' . wp_nonce_url(admin_url('update.php?action=upgrade-plugin&plugin=' . ATD_MAIN_FILE), 'upgrade-plugin_' . ATD_MAIN_FILE) . '">' . __('Update Ouidah Product Designer now.', 'allada-tshirt-designer-for-woocommerce') . '</a>';
        }
    }

}
