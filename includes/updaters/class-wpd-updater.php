<?php

class ATD_Updater {

    protected $version_url = 'https://static.designersuiteforwp.com/atd-updater.xml';
    public $title = 'Allada';
    protected $auto_updater = false;
    protected $upgrade_manager = false;
    protected $iframe = false;

    public function init() {
        add_filter('upgrader_pre_download', array($this, 'upgradeFilter'), 10, 4);
        add_action('upgrader_process_complete', array($this, 'removeTemporaryDir'));
    }

    /**
     * Setter for manager updater.
     *
     * @param ATD_Updating_Manager $updater
     */
    public function setUpdateManager(ATD_Updating_Manager $updater) {
        $this->auto_updater = $updater;
    }

    /**
     * Getter for manager updater.
     *
     * @return ATD_Updating_Manager
     */
    public function updateManager() {
        return $this->auto_updater;
    }

    /**
     * Get url for version validation
     *
     * @return string
     */
    public function versionUrl() {
        return $this->version_url;
    }

    /**
     * Downloads new version from designersuiteforwp and unzips into temporary directory.
     *
     * @param $reply
     * @param $package
     * @param $updater
     * @return mixed|string|WP_Error
     */
    public function upgradeFilter($reply, $package, $updater) {
        global $wp_filesystem;
        if ((isset($updater->skin->plugin) && $updater->skin->plugin === ATD_MAIN_FILE) ||
                (isset($updater->skin->plugin_info) && htmlspecialchars_decode($updater->skin->plugin_info['Name']) === $this->title)
        ) {
            $updater->strings['download_from_servers'] = __('Downloading package from ORION servers...', 'allada-tshirt-designer-for-woocommerce');
            $updater->skin->feedback('download_from_servers');
            $package_filename = 'allada-woocommerce-custom-t-shirt-designer.zip';
            $res = $updater->fs_connect(array(WP_CONTENT_DIR));

            if (!$res) {
                return new WP_Error('no_credentials', __("Error! Can't connect to filesystem", 'allada-tshirt-designer-for-woocommerce'));
            }

            global $atd_settings;
            $purchase_code = $atd_settings['atd-licence']['purchase-code'];
            if (isset($purchase_code) && !empty($purchase_code)) {
                $license_key = $purchase_code;
            } else {
                return new WP_Error('no_credentials', __('To receive automatic updates, license activation is required. Please visit <a href="' . admin_url('admin.php?page=atd-manage-settings') . '' . '" target="_blank">Settings</a> to activate your Ouidah Product Designer.', 'allada-tshirt-designer-for-woocommerce'));
            }

            $args = array('timeout' => 600);
            $site_url = get_site_url();
            $url = "https://designersuiteforwp.com/service/olicenses/v1/checking/?license-key=" . urlencode($license_key) . "&siteurl=" . urlencode($site_url);
            //$url = "https://tests.designersuiteforwp.com/service/olicenses/v1/checking/?license-key=" . urlencode($license_key) . "&siteurl=" . urlencode($site_url);
            $response = wp_remote_get($url, $args);

            if (!is_wp_error($response)) {
                if (isset($response["body"]) && intval($response["body"]) == 200) {

                    $json = wp_remote_get($this->downloadUrl($this->title), $args);
                    if (is_wp_error($json)) {
                        return $json->get_error_message();
                    }
                    if (isset($json["body"])) {
                        $answer = $json["body"];
                    }
                    
                    $result = array();

                    if (is_array(json_decode($answer, true))) {
                        $result = json_decode($answer, true);
                    } else {
                        return new WP_Error('no_file', __('Error! No file found. Please contact the plugin owners.', 'allada-tshirt-designer-for-woocommerce'));
                    }

                    if (!isset($result['download_url'])) {
                        return new WP_Error('no_file', __('Error! No file found. Please contact the plugin owners.', 'allada-tshirt-designer-for-woocommerce'));
                    }

                    $download_file = download_url($result['download_url']);
                    if (is_wp_error($download_file)) {
                        return $download_file;
                    }
                    $uploads_dir_obj = wp_upload_dir();
                    $upgrade_folder = $uploads_dir_obj["basedir"] . '/atd_envato_package';
                    if (!is_dir($upgrade_folder)) {
                        mkdir($upgrade_folder);
                    }
                    //We rename the tmp file to a zip file
                    $new_zipname = str_replace(".tmp", ".zip", $download_file);
                    rename($download_file, $new_zipname);
                    //The upgrade is in the unique directory inside the upgrade folder
                    $new_version = "$upgrade_folder/$package_filename";
                    $result = copy($new_zipname, $new_version);
                    if ($result && is_file($new_version)) {
                        return $new_version;
                    }
                    return new WP_Error('no_credentials', __('Error on unzipping package', 'allada-tshirt-designer-for-woocommerce'));
                } else {
                    return new WP_Error('network_error', __('Wrong license key provided.', 'allada-tshirt-designer-for-woocommerce'));
                }
            } else {
                return $response->get_error_message();
            }
        }
        return $reply;
    }
    
    /**
     * Remove temporary folder.
     * @global type $wp_filesystem
     */
    public function removeTemporaryDir() {
        global $wp_filesystem;
        if (is_dir($wp_filesystem->wp_content_dir() . 'uploads/atd_envato_package')) {
            $wp_filesystem->delete($wp_filesystem->wp_content_dir() . 'uploads/atd_envato_package', true);
        }
    }

    /**
     * Get download URL.
     * @param type $title The title.
     * @return type The download URL.
     */
    protected function downloadUrl($title) {
        global $atd_settings;
        $site_url = get_site_url();
        $purchase_code = "";
        if(!empty($atd_settings['atd-licence']['purchase-code']) & isset($atd_settings['atd-licence']['purchase-code'])){
            $purchase_code = $atd_settings['atd-licence']['purchase-code'];
        }
        return "https://designersuiteforwp.com/service/oupdater/v1/update/?name=" . rawurlencode($title) . "&purchase-code=" . $purchase_code . "&siteurl=" . urlencode($site_url);
        //return "https://tests.designersuiteforwp.com/service/oupdater/v1/update/?name=" . rawurlencode($title) . "&purchase-code=" . $purchase_code . "&siteurl=" . urlencode($site_url);
    }

}
