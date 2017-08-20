<?php
require_once( WPMFAD_PLUGIN_DIR . '/class/wpmfDropbox.php' );
require_once( WPMFAD_PLUGIN_DIR . '/class/wpmfHelper.php' );
require_once ( WPMFAD_PLUGIN_DIR . '/class/Dropbox/autoload.php');
class WpmfAddonDropboxAdmin extends wpmfAddonDropbox{

    function __construct() {
        if (is_plugin_active('wp-media-folder/wp-media-folder.php')) {
            add_action('admin_menu',array($this,'wpmf_add_menu_page'));
            add_action('admin_enqueue_scripts', array($this, 'wpmf_register_style_script'));
            add_filter( 'media_upload_tabs', array($this,'wpmf_add_upload_tab') );
            add_action('media_upload_wpmfdbx', array($this,'media_upload_wpmfdbx'));
        }
        
        add_action('wp_ajax_wpmf-get-dropboxfilelist', array($this, 'listDropboxFiles'));
        add_action('wp_ajax_wpmf-dropbox-addfolder', array($this, 'createDropFolder'));
        add_action('wp_ajax_wpmf-dropbox-editfolder', array($this, 'changeDropboxFilename'));
        add_action('wp_ajax_wpmf-dropbox-deletefolder', array($this, 'deleteDropbox'));
        add_action('wp_ajax_wpmf_dropbox_movefile', array($this, 'moveDropboxFile'));
        add_action('wp_ajax_wpmf-dbxupload-file', array($this, 'uploadFile'));
        add_action('wp_ajax_wpmf-dbxdownload-file', array($this, 'wpmf_download_file'));
        add_action('wp_ajax_wpmf-dbx-getThumb', array($this, 'dbx_getThumb'));        
        add_action('wp_ajax_wpmf_dbximport_file', array($this, 'wpmf_dbximport_file'));        
        add_action('wp_ajax_wpmf_get-detailFile', array($this, 'wpmf_get_detailFile'));
        add_filter('wpmfaddon_dbxsettings', array($this,'wpmf_tab_drive'),10,2);
    }
    
    public function wpmf_tab_drive($Dropbox,$dropboxconfig) {
        ob_start();
        require_once 'templates/settings_dropbox.php';
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
    
    public function wpmf_add_upload_tab( $tabs ) {
        $newtab = array( 'wpmfdbx' => __('Insert Dropbox File','wpmfAddon') );
        return array_merge( $tabs, $newtab );
    }
    
    public function media_upload_wpmfdbx() {
        $errors = false;
        return wp_iframe( array($this,'media_upload_wpmfdbx_form'), $errors );
    }
    
    public function media_upload_wpmfdbx_form($errors) {
        $dropbox = new wpmfAddonDropbox();
        if ( $dropbox->checkAuth()) {
            $message = __('The connection to Dropbox is not established, you can do that from the WP Media configuration','wpmfAddon');
            $link_setting = admin_url('options-general.php?page=option-folder&tab=wpmf-dropbox');
            $link_document = 'https://www.joomunited.com/documentation/93-wp-media-folder-addon-documentation';
            $open_new = true;
            require_once( WPMFAD_PLUGIN_DIR . '/class/templates/error_message.php' );
        }else{
            $this->load_style_script();
            $mediatype = 'dropbox';
            require_once( WPMFAD_PLUGIN_DIR . '/class/templates/listfiles.php' );
        }
    }
    
    public function load_style_script(){
        wp_enqueue_style('wpmf-google-icon');
        wp_enqueue_style('wpmf-css-font-material-design');
        wp_enqueue_style('wpmf-css-googlefile');
        wp_enqueue_style('wpmf-css-popup');
        wp_enqueue_script(array('jquery-ui-draggable', 'jquery-ui-droppable'));
        wp_enqueue_script('wpmf-loaddropboxfile');
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
        wp_register_script('wpmf-imagesloaded', plugins_url('/assets/js/imagesloaded.pkgd.min.js', dirname(__FILE__)), array(), '3.1.5', true);
        wp_register_script('wpmf-popup', plugins_url('/assets/js/jquery.magnific-popup.min.js', dirname(__FILE__)), array('jquery'), '0.9.9', true);
        wp_register_script('wpmf-loaddropboxfile', plugins_url('/assets/js/loaddropboxfile.js', dirname(__FILE__)), array('jquery'), WPMFAD_VERSION);
        wp_register_script('jQuery.fileupload', plugins_url('/assets/js/fileupload/jquery.fileupload.js', dirname(__FILE__)), array('jquery'), false, true);
        wp_register_script('jQuery.fileupload-process', plugins_url('/assets/js/fileupload/jquery.fileupload-process.js', dirname(__FILE__)), array('jquery'), false, true);
        wp_register_style('wpmf-css-googlefile', plugins_url('/assets/css/style.css', dirname(__FILE__)), array(), WPMFAD_VERSION);
        wp_register_style('wpmf-css-font-material-design', plugins_url('/assets/css/material-design-iconic-font.min.css', dirname(__FILE__)), array(), WPMFAD_VERSION);
        wp_register_style('wpmf-css-popup', plugins_url('/assets/css/magnific-popup.css', dirname(__FILE__)), array(), '0.9.9');
        wp_register_style('wpmf-css-dialogs', plugins_url('/assets/css/jquery-ui-1.10.3.custom.css', dirname(__FILE__)), array(), '1.10.3');
        wp_register_style('wpmftree', plugins_url('/assets/css/jaofiletree.css', dirname(__FILE__)), array(), WPMFAD_VERSION);
        wp_register_style('wpmf-fileupload-jquery-ui', plugins_url('/assets/css/jquery.fileupload-ui.css', dirname(__FILE__)));
        wp_localize_script('wpmf-loaddropboxfile', 'wpmfaddonparams', $this->wpmf_localize_script());
    }
    
    public function wpmf_localize_script() {
        $wpmfAddon_dropbox_config = get_option('_wpmfAddon_dropbox_config');
        if(!empty($wpmfAddon_dropbox_config['dropboxToken'])){
            $dropboxToken = $wpmfAddon_dropbox_config['dropboxToken'];
        }else{
            $dropboxToken = '';
        }
        return array(
            'plugin_url' => WPMFAD_PLUGIN_URL,
            'img_path' => WPMFAD_PLUGIN_URL . 'assets/images/',
            'plugin_url_icon' => plugins_url('/assets/images/icons/', dirname(__FILE__)),
            'dropboxToken' => $dropboxToken,
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
        add_media_page( 'Dropbox', 'Dropbox', 'activate_plugins', 'wpmf-dropbox-page', array($this,'showDropboxFile'));
    }
    
    /* Google drive page */
    public function showDropboxFile(){
        $dropbox = new wpmfAddonDropbox();
        if ( $dropbox->checkAuth()) {
            $message = __('The connection to Dropbox is not established, you can do that from the WP Media configuration','wpmfAddon');
            $link_setting = admin_url('options-general.php?page=option-folder&tab=wpmf-dropbox');
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
            $mediatype = 'dropbox';
            require_once( WPMFAD_PLUGIN_DIR . '/class/templates/listfiles.php' );
        }
    }
    
    public function dbxlogout() {
        $dropbox = new wpmfAddonDropbox();
        $dropbox->logout();
    }
}
?>