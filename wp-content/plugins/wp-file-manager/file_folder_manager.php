<?php 
/**
  Plugin Name: WP File Manager
  Plugin URI: https://wordpress.org/plugins/wp-file-manager
  Description: Manage your WP files.
  Author: mndpsingh287
  Version: 1.6
  Author URI: https://profiles.wordpress.org/mndpsingh287
  License: GPLv2
**/
if(!class_exists('mk_file_folder_manager')):	
	class mk_file_folder_manager
	{
		/* Auto Load Hooks */
		public function __construct()
		{
			 add_action('admin_menu', array(&$this, 'ffm_menu_page'));
			 add_action( 'admin_enqueue_scripts', array(&$this,'ffm_admin_things'));
			 add_action( 'wp_ajax_mk_file_folder_manager', array(&$this, 'mk_file_folder_manager_action_callback'));
			 add_action( 'wp_ajax_nopriv_mk_file_folder_manager', array(&$this, 'mk_file_folder_manager_action_callback') );
			 add_action("wp_ajax_mk_fm_close_fm_help", array($this, "mk_fm_close_fm_help"));
			 add_filter( 'plugin_action_links', array(&$this, 'mk_file_folder_manager_action_links'), 10, 2 );
			 do_action('load_filemanager_extensions');
		}
		/* Menu Page */
		public function ffm_menu_page()
		{
			 add_menu_page(
			__( 'WP File Manager', 'ffm' ),
			__( 'WP File Manager', 'ffm' ),
			'manage_options',
			'wp_file_manager',
			array(&$this, 'ffm_settings_callback'),
			plugins_url( 'images/wp_file_manager.png', __FILE__ )
			);
			/* Only for admin */
			add_submenu_page( 'wp_file_manager', 'Settings', 'Settings', 'manage_options', 'wp_file_manager_settings', array(&$this, 'wp_file_manager_settings'));
			/* Only for admin */
			add_submenu_page( 'wp_file_manager', 'Shortcode - PRO', 'Shortcode - PRO', 'manage_options', 'wp_file_manager_shortcode_doc', array(&$this, 'wp_file_manager_shortcode_doc'));
			/* Only for admin */
			add_submenu_page( 'wp_file_manager', 'Extensions', 'Extensions', 'manage_options', 'wp_file_manager_extension', array(&$this, 'wp_file_manager_extension'));
		}
		/* Main Role */
		public function ffm_settings_callback()
		{ 
			if(is_admin()):
			 include('lib/wpfilemanager.php');
			endif;
		}
		/*Settings */
		public function wp_file_manager_settings()
		{
			if(is_admin()):
			 include('inc/settings.php');
			endif;
		}
		/* Shortcode Doc */
		public function wp_file_manager_shortcode_doc()
		{
		   if(is_admin()):		  
			 include('inc/shortcode_docs.php');
			endif;	
		}
		/* Extesions - Show */
		public function wp_file_manager_extension()
		{
			if(is_admin()):		  
			 include('inc/extensions.php');
			endif;
		}
		/* Admin  Things */
		public function ffm_admin_things()
		{
				$getPage = isset($_GET['page']) ? $_GET['page'] : '';
				$allowedPages = array(
									  'wp_file_manager'
									  );
					if(!empty($getPage) && in_array($getPage, $allowedPages)):
						wp_enqueue_style( 'jquery-ui', plugins_url('css/jquery-ui.css', __FILE__));
						wp_enqueue_style( 'elfinder.min', plugins_url('lib/css/elfinder.min.css', __FILE__)); 
						wp_enqueue_style( 'theme', plugins_url('lib/css/theme.css', __FILE__));
						wp_enqueue_script( 'jquery_min', plugins_url('js/jquery-ui.min.js', __FILE__));	
						wp_enqueue_script( 'elfinder_min', plugins_url('lib/js/elfinder.full.js',  __FILE__ ));	
					endif;				
		}
		/*
		* Admin Links
		*/
		public function mk_file_folder_manager_action_links($links, $file)
		{
		if ( $file == plugin_basename( __FILE__ ) ) {
				$mk_file_folder_manager_links = '<a href="http://filemanager.webdesi9.com/product/file-manager/" title="Buy Pro Now" target="_blank" style="font-weight:bold">'.__('Buy Pro').'</a>';
				$mk_file_folder_manager_donate = '<a href="http://www.webdesi9.com/donate/?plugin=wp-file-manager" title="Donate Now" target="_blank" style="font-weight:bold">'.__('Donate').'</a>';
				array_unshift( $links, $mk_file_folder_manager_donate );
				array_unshift( $links, $mk_file_folder_manager_links );
			}
		
			return $links;	
		}
		/*
		* Ajax request handler
		* Run File Manager
		*/
		public function mk_file_folder_manager_action_callback()
		{
			$nonce = $_REQUEST['_wpnonce'];
            if ( wp_verify_nonce( $nonce, 'wp-file-manager' ) ) {
				 require 'lib/php/autoload.php';
						$opts = array(
					   'debug' => false,
					   'roots' => array(
						array(
							'driver'        => 'LocalFileSystem',           // driver for accessing file system (REQUIRED)
							'path'          => ABSPATH, // path to files (REQUIRED)
							'URL'           => site_url(), // URL to files (REQUIRED)
							'uploadDeny'    => array(),                // All Mimetypes not allowed to upload
							'uploadAllow'   => array('image', 'text/plain'),// Mimetype `image` and `text/plain` allowed to upload
							'uploadOrder'   => array('deny', 'allow'),      // allowed Mimetype `image` and `text/plain` only
							'accessControl' => 'access',                     // disable and hide dot starting files (OPTIONAL)
							'acceptedName' => 'validName'
						)
					)
				);
				//run elFinder
				$connector = new elFinderConnector(new elFinder($opts));
				$connector->run();
			}
			die;
		}
		public function permissions()
		{
			 $permissions = 'manage_options';
			 return $permissions;
		}
		public function load_help_desk() {
			$mkcontent = '';
			$mkcontent .='<div class="wfmrs">';
			$mkcontent .='<div class="l_wfmrs">';
			$mkcontent .='';
			$mkcontent .='</div>';
            $mkcontent .='<div class="r_wfmrs">';
            $mkcontent .='<a class="close_fm_help fm_close_btn" href="javascript:void(0)" data-ct="rate_later" title="close">X</a><strong>WP File Manager</strong><p>We love and care about you. Our team is putting maximum efforts to provide you the best functionalities. It would be highly appreciable if you could spend a couple of seconds to give a Nice Review to the plugin to appreciate our efforts. So we can work hard to provide new features regularly :)</p><a class="close_fm_help fm_close_btn_1" href="javascript:void(0)" data-ct="rate_later" title="Remind me later">Later</a> <a class="close_fm_help fm_close_btn_2" href="https://wordpress.org/support/plugin/wp-file-manager/reviews/?filter=5" data-ct="rate_now" title="Rate us now" target="_blank">Rate Us</a> <a class="close_fm_help fm_close_btn_3" href="javascript:void(0)" data-ct="rate_never" title="Not interested">Never</a>';
			$mkcontent .='</div></div>';
           if ( false === ( $mk_fm_close_fm_help_c = get_transient( 'mk_fm_close_fm_help_c' ) ) ) {
			  	echo apply_filters('the_content', $mkcontent);  
		   } 
		}
	   public function mk_fm_close_fm_help() {
		   $what_to_do = sanitize_text_field($_POST['what_to_do']);
		   $expire_time = 15;
		  if($what_to_do == 'rate_now' || $what_to_do == 'rate_never') {
			 $expire_time = 365;
		  } else if($what_to_do == 'rate_later') {
			 $expire_time = 15;
		  }	
		  if ( false === ( $mk_fm_close_fm_help_c = get_transient( 'mk_fm_close_fm_help_c' ) ) ) {
			   $set =  set_transient( 'mk_fm_close_fm_help_c', 'mk_fm_close_fm_help_c', 60 * 60 * 24 * $expire_time );
				 if($set) {
					 echo 'ok';
				 } else {
					 echo 'oh';
				 }
			   } else {
				    echo 'ac';
			   }
		   die;
	   }
	   public function load_custom_assets() {
		   echo '<script src="'.plugins_url('js/fm_script.js', __FILE__).'"></script>';
		   echo "<link rel='stylesheet' href='".plugins_url('css/fm_script.css', __FILE__)."' type='text/css' media='all' />
		   ";
	   }
	}
	$filemanager = new mk_file_folder_manager;	
	global $filemanager;
	/* end class */	
endif;	