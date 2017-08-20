<?php 

class MK_Theme_Options {
        
    public function __construct() {
        $this->add_hooks();
    }
    
    public function add_hooks() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
        add_action( 'admin_menu', array( $this, 'register_submenu_page' ) );
        add_action( 'wp_ajax_mk_save_theme_options', array( $this, 'save' ) );
        add_action( 'init', array( $this, 'set_defaults' ) );
        add_action( 'init', array( $this, 'set_missing_defaults' ) );
        
        // Causes conflicts for background selector angle icons.
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'admin_print_styles', 'print_emoji_styles' );
    }
    
    function register_submenu_page() {        
        add_submenu_page(
            THEME_NAME, 
            __( 'Theme Options', 'mk_framework' ), 
            __( 'Theme Options', 'mk_framework' ), 
            'edit_theme_options', 
            'theme_options', 
            array( $this, 'render' ) 
        );
    }
    
    public function enqueue( $hook ) {
        
        if ( $hook != 'jupiter_page_theme_options' ) {
            return;
        }
        
        $theme_version = get_option('mk_jupiter_theme_current_version');

        wp_enqueue_style( 
            'mk-vfg-css', 
            THEME_DIR_URI . '/framework/admin/theme-options/css/vfg-core.css', 
            array(),
            $theme_version
        );
        
        wp_enqueue_style( 
            'mk-flexbox-css', 
            THEME_DIR_URI . '/framework/admin/theme-options/css/flexbox.css', 
            array(),
            $theme_version
        );
        
    	wp_enqueue_style(
    		'mk-alpha-color-picker',
    		THEME_DIR_URI . '/framework/admin/theme-options/css/alpha-color-picker.css',
    		array( 'wp-color-picker' ),
            $theme_version
    	);
        
        wp_enqueue_style( 
            'mk-components-css', 
            THEME_DIR_URI . '/framework/admin/theme-options/css/components.css', 
            array(),
            $theme_version
        );
        
        wp_enqueue_script( 
            'mk-vue-js', 
            THEME_DIR_URI . '/framework/admin/theme-options/js/vue.min.js', 
            array(),
            $theme_version,
            true
        );

        wp_enqueue_script( 
            'mk-vue-ls-js', 
            THEME_DIR_URI . '/framework/admin/theme-options/js/vue-ls.js', 
            array(),
            $theme_version
        );
        
        wp_enqueue_script( 
            'mk-vfg-js', 
            THEME_DIR_URI . '/framework/admin/theme-options/js/vfg-core.js', 
            array(),
            $theme_version,
            true
        );
        
        wp_enqueue_script(
            'mk-alpha-color-picker',
            THEME_DIR_URI . '/framework/admin/theme-options/js/alpha-color-picker.js',
            array( 'jquery', 'wp-color-picker' ),
            $theme_version,
            true
        );
        
        wp_enqueue_script( 
            'mk-components-js', 
            THEME_DIR_URI . '/framework/admin/theme-options/js/components.js', 
            array( 'mk-alpha-color-picker', 'jquery-ui-position'),
            $theme_version,
            true
        );
        
        wp_enqueue_script( 
            'mk-theme-options-js', 
            THEME_DIR_URI . '/framework/admin/theme-options/js/theme-options.js', 
            array(),
            $theme_version,
            true
        );
        
        wp_localize_script( 'mk-theme-options-js', 'mk_data', $this->data() );
    }
    
    public function data() {
        $options = require_once THEME_ADMIN . '/theme-options/masterkey.php';
        return $options;
    }
    
    public function save() {
        
        $options = $_POST['options'];
        unset( $options['theme_export_options'] );
        $options = is_array( $options ) ? array_map( 'stripslashes_deep', $options ) : stripslashes( $options );
        
        if ( ! update_option( THEME_OPTIONS, $options ) ) {
            wp_send_json_error( "Error" );
        }
        
        wp_send_json_success( 'OK' );
        
        wp_die();
    }
    // We need to improve this to exclude some of the uncesasary post types
    public static function get_post_types() {
        $post_type = get_post_types();

        unset(
            $post_type['post'],
            $post_type['page'],
            $post_type['attachment'],
            $post_type['nav_menu_item'],
            $post_type['revision'],
            $post_type['clients'],
            $post_type['animated-columns'],
            $post_type['edge'],
            $post_type['portfolio'],
            $post_type['shop_order'],
            $post_type['shop_order_refund'],
            $post_type['shop_coupon'],
            $post_type['shop_webhook'],
            $post_type['banner_builder'],
            $post_type['banner_builder']
            );
        return $post_type;
    }
    
    public function set_defaults( $check = false ) {
        
        $GLOBALS['mk_options'] = get_option( THEME_OPTIONS );
        
        if ( ( empty( $GLOBALS['mk_options'] ) || sizeof( $GLOBALS['mk_options'] ) < 2 || $check ) && ! is_admin() ) {
            
            $options = array();
            $data    = $this->data();
            
            if ( empty( $data['schema'] ) ) {
                return;
            }
            
            foreach( $data['schema'] as $tab ) {
                
                foreach( $tab['sections'] as $section ) {
                    
                    foreach( $section['fields'] as $field ) {
                        
                        if ( empty( $field['model'] ) || is_array( $field['model'] ) ) {
                            continue;
                        }
                        
                        if ( empty( $field['default'] ) ) {
                            $field['default'] = '';
                        }
                        
                        $options[ $field['model'] ] = $field['default'];
                        
                    }
                    
                }
                
            }

            $GLOBALS['mk_options'] = array_merge( (array)$options, (array)$GLOBALS['mk_options'] );
            
            update_option( THEME_OPTIONS, $GLOBALS['mk_options'] );
            
            update_option( THEME_OPTIONS_BUILD, uniqid() );
            
            mk_purge_cache_actions();
            
        }
        
    }
    
    public function set_missing_defaults() {
        
        /*
         * It should not run in admin since the `set_defaults` method does nothing in admin pages.
         */ 
        if ( is_admin() ) {
            return;
        }
        
        $current_version  = get_option( 'jupiter_theme_version' );
        $imported_options = get_option( THEME_OPTIONS . '_imported' );
        
        if ( function_exists( 'wp_get_theme' ) ) {
            $theme_data     = wp_get_theme( get_option( 'template' ) );
            $latest_version = $theme_data->Version;  
        } else {
            $theme_data     = get_theme_data( TEMPLATEPATH . '/style.css' );
            $latest_version = $theme_data['Version'];
        }
        
        if ( $latest_version != $current_version || $imported_options == 'true' ) {
            
            $this->set_defaults( true );
            
            update_option( 'jupiter_theme_version', $latest_version );
            
            update_option( THEME_OPTIONS . '_imported', 'false' );
            
            update_option( THEME_OPTIONS_BUILD, uniqid() );
            
            mk_purge_cache_actions();
            
            flush_rewrite_rules();
            
        }
        
    }
    
    public function render() {
        wp_enqueue_media();
        
        ?>
            <div id="mk-theme-options" class="mka-to-wrap">
                <div class="mka-to-header-wrap">
                    <div class="mka-to-header mka-clearfix">
                        <div class="mka-to-header-logo"></div>
                        <div class="mka-to-header-actions">
                            <div class="mka-wrap mka-button-wrap">
                                <span class="mka-save-response"></span>
                                <a href="#" class="mka-button mka-button--green mka-button--medium mka-button--relative mka-to-save-button" data-nonce="<?php echo wp_create_nonce( 'mk-ajax-theme-options' ); ?>" @click="save">
                                    <span class="mka-button-icon">
                                        <span class="mka-button-icon-spinner">
                                            <svg class="mka-spinner" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                                               <circle class="mka-spinner-path" fill="none" stroke-width="7" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
                                            </svg>
                                        </span>
                                        <span class="mka-button-icon-success"></span>
                                    </span>
                                    Save Settings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mka-to-body mka-clearfix">
                    <div class="mka-to-sidebar">
                        <ul class="mka-to-nav">
                            <template v-for="( menu, menuKey ) in menu">
                                <li :data-id="menuKey" :class="[ 'mka-to-nav-item', { 'mka-to-nav-item--active': activeMenu == menuKey } ]" >
                                    <a :href="'#'+menuKey" @click="setActiveTab(menu.default)">
                                        <span :class="['mka-to-nav-item-icon', 'mka-to-nav-item-icon--' + menuKey]"></span>
                                        <span class="mka-to-nav-label" v-html="menu.label"></span>
                                    </a>
                                    <div class="mka-to-nav-sub" v-show="_.size(menu.submenu) > 1">
                                        <ul class="mka-to-nav-subnav">
                                            <li :class="[ 'mka-to-nav-subitem', { 'mka-to-nav-subitem--active': activeSubmenu == submenuKey } ]" v-for="( submenu, submenuKey ) in menu.submenu">
                                                <a :href="'#'+submenuKey" @click="setActiveTab( submenuKey )">{{ submenu }}</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </template>
                        </ul>
                        <div class="mka-to-sidebar-actions">
                            <div class="mka-wrap">
                                <button type="button" class="mka-button mka-button--steelblue mka-button--medium mka-to-sidebar-history">
                                    <span class="mka-to-nav-item-icon-float">
                                        <span class="mka-to-nav-item-icon mka-to-nav-item-icon--history"></span>
                                    </span>
                                    History
                                </button>
                                <button type="button" class="mka-button mka-button--darksteelblue mka-button--medium mka-to-sidebar-restore">
                                    Restore Defaults
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mka-to-main" :class="getClasses( tab )" v-for="( tab, tabKey ) in schema" v-if="activeTab == tabKey" :data-tab="tabKey">
    					<p class="mka-to-page-title" v-show="tab.label">
    						<span v-html="tab.label"></span>
    						<span class="mka-wrap mka-tip-wrap" v-show="tab.help">
    							<a href="#" class="mka-tip">
    								<span class="mka-tip-icon">
    									<span class="mka-tip-arrow"></span>
    								</span>
    								<span class="mka-tip-ripple"></span>
    							</a>
    							<span class="mka-tip-content" v-html="tab.help"></span>
    						</span>
    					</p>
                        <div class="mka-to-section" :class="getClasses( section )" v-for="section in tab.sections" v-if="sectionVisible( section )" :data-section="section.id">
    						<p class="mka-to-section-heading" v-show="section.label" >
    							<span v-html="section.label"></span>
    							<span class="mka-wrap mka-tip-wrap" v-show="section.help">
    								<a href="#" class="mka-tip">
    									<span class="mka-tip-icon">
    										<span class="mka-tip-arrow"></span>
    									</span>
    									<span class="mka-tip-ripple"></span>
    								</a>
    								<span class="mka-tip-content" v-html="section.help"></span>
    							</span>
    						</p>
                            <vue-form-generator :schema="section" :model="model" :class></vue-form-generator>
                        </div> 
                    </div>
                </div>
                <!-- <pre v-if="model" v-html="prettyJSON(model)"></pre> -->
            </div>
        <?php
    }
    
}

new MK_Theme_Options();
