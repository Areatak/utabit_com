<?php
require_once( WPMFAD_PLUGIN_DIR . '/class/wpmfGoogle.php' );
require_once( WPMFAD_PLUGIN_DIR . '/class/wpmfHelper.php' );
require_once ( WPMFAD_PLUGIN_DIR . '/class/Google/autoload.php');
class WpmfAddonGoogle extends wpmfAddonGoogleDrive{

    function __construct() {
        if (is_plugin_active('wp-media-folder/wp-media-folder.php')) {
            add_action('admin_menu',array($this,'wpmf_add_menu_page'));
            add_action('admin_enqueue_scripts', array($this, 'wpmf_register_style_script'));
            add_filter( 'media_upload_tabs', array($this,'wpmf_add_upload_tab') );
            add_action('media_upload_wpmfgg', array($this,'media_upload_wpmfgg'));
        }
        add_action('wp_ajax_wpmf-get-filelist', array($this, 'wpmf_get_google_filelist'));
        add_action('wp_ajax_wpmf-google-addfolder', array($this, 'ajaxcreateFolder'));
        add_action('wp_ajax_wpmf-google-editfolder', array($this, 'changeFilename'));
        add_action('wp_ajax_wpmf-google-deletefolder', array($this, 'delete'));
        add_action('wp_ajax_wpmfaddon_move_file', array($this, 'moveFile'));
        add_action('wp_ajax_wpmf-upload-file', array($this, 'uploadFile'));
        add_action('wp_ajax_wpmf-download-file', array($this, 'wpmf_download_file'));
        add_action('wp_ajax_nopriv_wpmf-download-file', array($this, 'wpmf_download_file'));
        add_action('wp_ajax_wpmf_ggimport_file', array($this, 'wpmf_ggimport_file'));   
        add_action('wp_ajax_wpmf-preview-file', array($this, 'wpmf_preview_file'));
        add_filter('wpmfaddon_ggsettings', array($this,'wpmf_tab_google'),10,2);
    }
    
    public function wpmf_tab_google($googleDrive,$googleconfig ) {
        ob_start();
        require_once 'templates/settings_google_drive.php';
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
    
    public function wpmf_add_upload_tab( $tabs ) {
        $newtab = array( 'wpmfgg' => __('Insert Google Drive','wpmfAddon') );
        return array_merge( $tabs, $newtab );
    }

    public function media_upload_wpmfgg() {
        $errors = false;
        return wp_iframe( array($this,'media_upload_wpmfgg_form'), $errors );
    }
    
    public function media_upload_wpmfgg_form($errors) {
        $googleDrive = new wpmfAddonGoogleDrive();
        if ( ! $googleDrive->checkAuth()) {
            $message = __('The connection to Google Drive is not established, you can do that from the WP Media configuration','wpmfAddon');
            $link_setting = admin_url('options-general.php?page=option-folder&tab=wpmf-google-drive');
            $link_document = 'https://www.joomunited.com/documentation/93-wp-media-folder-addon-documentation';
            $open_new = true;
            require_once( WPMFAD_PLUGIN_DIR . '/class/templates/error_message.php' );
        }else{
            $this->load_style_script();
            $mediatype = 'google';
            require_once( WPMFAD_PLUGIN_DIR . '/class/templates/listfiles.php' );
        }
        
        
    }
    
    public function load_style_script(){
        wp_enqueue_style('wpmf-google-icon');
        wp_enqueue_style('wpmf-css-font-material-design');
        wp_enqueue_style('wpmf-css-googlefile');
        wp_enqueue_style('wpmf-css-popup');
        wp_enqueue_script(array('jquery-ui-draggable', 'jquery-ui-droppable'));
        wp_enqueue_script('wpmf-loadgooglefile');
        wp_enqueue_script('wpmf-imagesloaded');
        wp_enqueue_script('wpmf-popup');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style('wpmf-css-dialogs');
        
        wp_enqueue_script('jQuery.fileupload');
        wp_enqueue_script('jQuery.fileupload-process');
        wp_enqueue_style('wpmf-fileupload-jquery-ui');
        wp_enqueue_style('wpmftree');
    }
    
    /* Load scripts and style */
    public function wpmf_register_style_script(){
        wp_register_style('wpmf-google-icon', 'https://fonts.googleapis.com/icon?family=Material+Icons');
        wp_register_script('wpmf-imagesloaded', plugins_url('/assets/js/imagesloaded.pkgd.min.js', dirname(__FILE__)), array(), '3.1.5', true);
        wp_register_script('wpmf-popup', plugins_url('/assets/js/jquery.magnific-popup.min.js', dirname(__FILE__)), array('jquery'), '0.9.9', true);
        wp_register_script('wpmf-loadgooglefile', plugins_url('/assets/js/loadgooglefile.js', dirname(__FILE__)), array('jquery'), WPMFAD_VERSION);
        wp_register_script('jQuery.fileupload', plugins_url('/assets/js/fileupload/jquery.fileupload.js', dirname(__FILE__)), array('jquery'), false, true);
        wp_register_script('jQuery.fileupload-process', plugins_url('/assets/js/fileupload/jquery.fileupload-process.js', dirname(__FILE__)), array('jquery'), false, true);
        wp_register_style('wpmf-css-googlefile', plugins_url('/assets/css/style.css', dirname(__FILE__)), array(), WPMFAD_VERSION);
        wp_register_style('wpmf-css-font-material-design', plugins_url('/assets/css/material-design-iconic-font.min.css', dirname(__FILE__)), array(), WPMFAD_VERSION);
        wp_register_style('wpmf-css-popup', plugins_url('/assets/css/magnific-popup.css', dirname(__FILE__)), array(), '0.9.9');
        wp_register_style('wpmf-css-dialogs', plugins_url('/assets/css/jquery-ui-1.10.3.custom.css', dirname(__FILE__)), array(), '1.10.3');
        wp_register_style('wpmftree', plugins_url('/assets/css/jaofiletree.css', dirname(__FILE__)), array(), WPMFAD_VERSION);
        wp_register_style('wpmf-fileupload-jquery-ui', plugins_url('/assets/css/jquery.fileupload-ui.css', dirname(__FILE__)));
        wp_localize_script('wpmf-loadgooglefile', 'wpmfaddonparams', $this->wpmf_localize_script());
    }
    
    public function wpmf_localize_script() {
        $wpmfAddon_cloud_config = get_option('_wpmfAddon_cloud_config');
        if(isset($wpmfAddon_cloud_config['googleBaseFolder'])){
            $googleBaseFolder = $wpmfAddon_cloud_config['googleBaseFolder'];
        }else{
            $googleBaseFolder = 0;
        }
        return array(
            'googleBaseFolder' => $googleBaseFolder,
            'newfolder' => __('New Folder','wpmfAddon'),
            'addfolder' => __('Add Folder','wpmfAddon'),
            'editfolder' => __('Change Filename','wpmfAddon'),
            'cancelfolder' => __('Cancel','wpmfAddon'),
            'promt' => __('Please give a name to this new folder', 'wpmfAddon'),
            'save' => __('Save','wpmfAddon'),
            'delete' => __('Delete','wpmfAddon'),
            'deletefolder' => __('Delete Folder','wpmfAddon'),
            'upload_nonce' => wp_create_nonce("wpmf-upload-file"),
            'maxNumberOfFiles' => __('Maximum number of files exceeded','wpmfAddon'),
            'acceptFileTypes' => __('File type not allowed','wpmfAddon'),
            'maxFileSize' => __('File is too large','wpmfAddon'),
            'minFileSize' => __('File is too small','wpmfAddon'),
            'plugin_url' => plugins_url('/assets/images/icons/', dirname(__FILE__)),
            'str_inqueue' => __('In queue', 'wpmfAddon'),
            'str_uploading_local' => __('Uploading to Server', 'wpmfAddon'),
            'str_uploading_cloud' => __('Uploading', 'wpmfAddon'),
            'str_success' => __('Success', 'wpmfAddon'),
            'str_error' => __('Error', 'wpmfAddon'),
            'str_message_delete' => __('These items will be permanently deleted and cannot be recovered. Are you sure?','wpmfAddon'),
            'maxsize' => 104857600,
            'media_folder' => __('Media Library', 'wpmfAddon'),
            'message_import' => __('Files imported with success!','wpmfAddon')
        );
    }
    
    /* add menu media page */
    public function wpmf_add_menu_page(){
        add_media_page( 'Google Drive', 'Google Drive', 'activate_plugins', 'wpmf-google-drive-page', array($this,'showGoogleDriveFile'));
    }
    
    /* Google drive page */
    public function showGoogleDriveFile(){
        $googleDrive = new wpmfAddonGoogleDrive();
        if ( ! $googleDrive->checkAuth()) {
            $message = __('The connection to Google Drive is not established, you can do that from the WP Media configuration','wpmfAddon');
            $link_setting = admin_url('options-general.php?page=option-folder&tab=wpmf-google-drive');
            $link_document = 'https://www.joomunited.com/documentation/93-wp-media-folder-addon-documentation';
            $open_new = false;
            require_once( WPMFAD_PLUGIN_DIR . '/class/templates/error_message.php' );
        }else{
            if (isset($_GET['noheader'])){
                _wp_admin_html_begin();
                global $hook_suffix;
                do_action( 'admin_enqueue_scripts', $hook_suffix );
                do_action( "admin_print_scripts-$hook_suffix" );
                do_action( 'admin_print_scripts' );
                ?>
                <style>
                    #wpfooter {display: none;}
                </style>    
                <?php
            }
            $this->load_style_script();
            $mediatype = 'google';
            require_once( WPMFAD_PLUGIN_DIR . '/class/templates/listfiles.php' );
        }
    }
    
    public function wpmf_authenticated(){
       
        $google = new wpmfAddonGoogleDrive();
        $credentials = $google->authenticate();
        $google->storeCredentials($credentials);
        //Check if WPMF folder exists and create if not
        if(!$google->folderExists(wpmfAddonHelper::getDataConfigBySeverName('google'))){     
            $folder = $google->createFolder('WP Media Folder - '. get_bloginfo('name'));
            $data = $this->getParams();
            $data['googleBaseFolder'] = $folder->id;
            $this->setParams($data);
        }
        $this->redirect(admin_url('options-general.php?page=option-folder&tab=wpmf-google-drive'));
        
    }
    
    public function getParams(){
        return wpmfAddonHelper::getAllCloudConfigs();
    }
    
    public function setParams($data){
        wpmfAddonHelper::saveCloudConfigs($data);
    }
    
    public function redirect($location){
        if(!headers_sent()){
            header("Location: $location", true, 303);
           // wp_safe_redirect( $location, 303 );
        }else{
            echo "<script>document.location.href='" . str_replace("'", "&apos;", $location) . "';</script>\n";
        }
    }
    
    public function wpmf_gglogout(){
        $google = new wpmfAddonGoogleDrive();
        $google->logout();
        $data = $this->getParams();
        $data['googleBaseFolder'] = '';
        $data['googleCredentials'] = '';
        $this->setParams($data);
        $this->redirect(admin_url('options-general.php?page=option-folder&tab=wpmf-google-drive'));
    }

}
?>