<?php
$theme = new Theme(true);
$theme->init(array(
    "theme_name" => "Jupiter",
    "theme_slug" => "JP",
));

if (!isset($content_width)) {
    $content_width = 1140;
}

class Theme
{

    public function __construct($check = false)
    {
        if ($check) {
            $this->theme_requirement_check();
        }
    }

    public function init($options)
    {
        $this->constants($options);
        $this->backward_compatibility();
        $this->post_types();
        $this->helpers();
        $this->functions();
        $this->menu_walkers();
        $this->admin();
        $this->theme_activated();

        add_action('admin_menu', array(&$this,
            'admin_menus',
        ));

        add_action('init', array(&$this,
            'language',
        ));

        add_action('init', array(&$this,
            'add_metaboxes',
        ));

        add_action('after_setup_theme', array(&$this,
            'supports',
        ));

        add_action('after_setup_theme', array(&$this,
            'mk_theme_setup',
        ));

        add_action('widgets_init', array(&$this,
            'widgets',
        ));

        $this->theme_dropins();
        
        $this->theme_options();
    }
    public function constants($options)
    {

        define("NEW_UI_LIBRARY", false);
        define("NEW_CUSTOM_ICON", false);
        define("V2ARTBEESAPI", 'http://artbees.net/api/v2/');
        define("THEME_DIR", get_template_directory());
        define("THEME_DIR_URI", get_template_directory_uri());
        define("THEME_NAME", $options["theme_name"]);

        $unify_theme_option = get_option('mk_unify_theme_options');

        if (defined("ICL_LANGUAGE_CODE") && !$unify_theme_option) {
            $lang = "_" . ICL_LANGUAGE_CODE;
        } else {
            $lang = "";
        }

        /* Use this constant in child theme functions.php to unify theme options across all languages in WPML
         *  add define('WPML_UNIFY_THEME_OPTIONS', true);
         */
        if (defined('WPML_UNIFY_THEME_OPTIONS')) {
            $lang = "";
        }

        define("THEME_OPTIONS", $options["theme_name"] . '_options' . $lang);

        define("THEME_OPTIONS_BUILD", $options["theme_name"] . '_options_build' . $lang);
        define("IMAGE_SIZE_OPTION", THEME_NAME . '_image_sizes');
        define("THEME_SLUG", $options["theme_slug"]);
        define("THEME_STYLES_SUFFIX", "/assets/stylesheet");
        define("THEME_STYLES", THEME_DIR_URI . THEME_STYLES_SUFFIX);
        define("THEME_JS", THEME_DIR_URI . "/assets/js");
        define("THEME_IMAGES", THEME_DIR_URI . "/assets/images");
        define('FONTFACE_DIR', THEME_DIR . '/fontface');
        define('FONTFACE_URI', THEME_DIR_URI . '/fontface');
        define("THEME_FRAMEWORK", THEME_DIR . "/framework");
        define("THEME_COMPONENTS", THEME_DIR_URI . "/components");
        define("THEME_ACTIONS", THEME_FRAMEWORK . "/actions");
        define("THEME_INCLUDES", THEME_FRAMEWORK . "/includes");
        define("THEME_INCLUDES_URI", THEME_DIR_URI . "/framework/includes");
        define("THEME_WIDGETS", THEME_FRAMEWORK . "/widgets");
        define("THEME_HELPERS", THEME_FRAMEWORK . "/helpers");
        define("THEME_FUNCTIONS", THEME_FRAMEWORK . "/functions");
        define("THEME_PLUGIN_INTEGRATIONS", THEME_FRAMEWORK . "/plugin-integrations");
        define('THEME_METABOXES', THEME_FRAMEWORK . '/metaboxes');
        define('THEME_POST_TYPES', THEME_FRAMEWORK . '/custom-post-types');

        define('THEME_ADMIN', THEME_FRAMEWORK . '/admin');
        define('THEME_FIELDS', THEME_ADMIN . '/theme-options/builder/fields');
        define('THEME_CONTROL_PANEL', THEME_ADMIN . '/control-panel');
        define('THEME_CONTROL_PANEL_ASSETS', THEME_DIR_URI . '/framework/admin/control-panel/assets');
        define('THEME_CONTROL_PANEL_ASSETS_DIR', THEME_DIR . '/framework/admin/control-panel/assets');
        define('THEME_GENERATORS', THEME_ADMIN . '/generators');
        define('THEME_ADMIN_URI', THEME_DIR_URI . '/framework/admin');
        define('THEME_ADMIN_ASSETS_URI', THEME_DIR_URI . '/framework/admin/assets');

        /**
         * This will point to the Theme Options version to use upon release. Use empty string ('')
         * to use the one in "framework/admin/theme-options/". Use "v2" to use the one
         * in "framework/admin/theme-options-v2/" and so on.
         *
         * If you set the value THEME_OPTIONS_VERSION to "v2", then
         * "wp-admin/admin.php?page=theme_options" will all the changes in "theme-options-v2" but still
         * keep "theme_options" as page slug.
         *
         * @since 5.6 Introduced.
         */
        define('THEME_OPTIONS_VERSION', '');

        /**
         * This value MUST be set to FALSE (or any falsy value) on Jupiter release candidates as this is for internal
         * developer use only.  By setting this to "v2", "v3", "v4" and so on, you are allowing
         * access to only a specific developement version of the theme options, as it does not make
         * sense to have access to all development versions.
         *
         * @since 5.6 Introduced.
         */
        define('THEME_OPTIONS_DEV_VERSION', '');

        // Just delete this constant before releasing Jupiter. This can be defined anywhere.
        //define('ARTBEES_HEADER_BUILDER', 'on');
    }
    public function backward_compatibility()
    {
        require_once THEME_HELPERS . "/php-backward-compatibility.php";
    }
    public function widgets()
    {
        include_once THEME_FUNCTIONS . '/widgets-filter.php';
        require_once locate_template("views/widgets/widgets-contact-form.php");
        require_once locate_template("views/widgets/widgets-contact-info.php");
        require_once locate_template("views/widgets/widgets-gmap.php");
        require_once locate_template("views/widgets/widgets-popular-posts.php");
        require_once locate_template("views/widgets/widgets-related-posts.php");
        require_once locate_template("views/widgets/widgets-recent-posts.php");
        require_once locate_template("views/widgets/widgets-social-networks.php");
        require_once locate_template("views/widgets/widgets-subnav.php");
        require_once locate_template("views/widgets/widgets-testimonials.php");
        require_once locate_template("views/widgets/widgets-twitter-feeds.php");
        require_once locate_template("views/widgets/widgets-video.php");
        require_once locate_template("views/widgets/widgets-flickr-feeds.php");
        require_once locate_template("views/widgets/widgets-instagram-feeds.php");
        require_once locate_template("views/widgets/widgets-news-slider.php");
        require_once locate_template("views/widgets/widgets-recent-portfolio.php");
        require_once locate_template("views/widgets/widgets-slideshow.php");
    }

    public function supports()
    {

        add_theme_support('automatic-feed-links');
        add_theme_support('title-tag');
        add_theme_support('menus');
        add_theme_support('automatic-feed-links');
        add_theme_support('editor-style');
        add_theme_support('post-thumbnails');
        add_theme_support('yoast-seo-breadcrumbs');

        register_nav_menus(array(
            'primary-menu'        => __('Primary Navigation', "mk_framework"),
            'second-menu'         => __('Second Navigation', "mk_framework"),
            'third-menu'          => __('Third Navigation', "mk_framework"),
            'fourth-menu'         => __('Fourth Navigation', "mk_framework"),
            'fifth-menu'          => __('Fifth Navigation', "mk_framework"),
            'sixth-menu'          => __('Sixth Navigation', "mk_framework"),
            "seventh-menu"        => __('Seventh Navigation', "mk_framework"),
            "eighth-menu"         => __('Eighth Navigation', "mk_framework"),
            "ninth-menu"          => __('Ninth Navigation', "mk_framework"),
            "tenth-menu"          => __('Tenth Navigation', "mk_framework"),
            'footer-menu'         => __('Footer Navigation', "mk_framework"),
            'toolbar-menu'        => __('Header Toolbar Navigation', "mk_framework"),
            'side-dashboard-menu' => __('Side Dashboard Navigation', "mk_framework"),
            'fullscreen-menu'     => __('Full Screen Navigation', "mk_framework"),
        ));

    }
    public function post_types()
    {
        require_once THEME_POST_TYPES . '/custom_post_types.helpers.class.php';
        require_once THEME_POST_TYPES . '/register_post_type.class.php';
        require_once THEME_POST_TYPES . '/register_taxonomy.class.php';
        require_once THEME_POST_TYPES . '/config.php';
    }
    public function functions()
    {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        require_once THEME_INCLUDES . "/sftp/sftp-init.php";

        include_once THEME_ADMIN . '/general/general-functions.php';

        if (!class_exists('phpQuery')) {
            require_once THEME_INCLUDES . "/phpquery/phpQuery.php";
        }

        require_once THEME_INCLUDES . "/otf-regen-thumbs/otf-regen-thumbs.php";

        require_once THEME_FUNCTIONS . "/general-functions.php";
        require_once THEME_FUNCTIONS . "/ajax-search.php";
        require_once THEME_FUNCTIONS . "/post-pagination.php";

        require_once THEME_FUNCTIONS . "/enqueue-front-scripts.php";
        require_once THEME_GENERATORS . '/sidebar-generator.php';
        require_once THEME_FUNCTIONS . "/dynamic-styles.php";

        require_once THEME_PLUGIN_INTEGRATIONS . "/woocommerce/init.php";
        require_once THEME_PLUGIN_INTEGRATIONS . "/visual-composer/init.php";

        require_once locate_template("framework/helpers/love-post.php");
        require_once locate_template("framework/helpers/load-more.php");
        require_once locate_template("framework/helpers/subscribe-mailchimp.php");
        require_once locate_template("components/shortcodes/mk_portfolio/ajax.php");
        require_once locate_template("components/shortcodes/mk_products/quick-view-ajax.php");
    }
    public function helpers()
    {
        require_once THEME_HELPERS . "/class-mk-fs.php";
        require_once THEME_HELPERS . "/class-logger.php";
        require_once THEME_HELPERS . "/survey-management.php";
        require_once THEME_HELPERS . "/db-management.php";
        require_once THEME_HELPERS . "/logic-helpers.php";
        require_once THEME_HELPERS . "/svg-icons.php";
        require_once THEME_HELPERS . "/image-resize.php";
        require_once THEME_HELPERS . "/template-part-helpers.php";
        require_once THEME_HELPERS . "/global.php";
        require_once THEME_HELPERS . "/wp_head.php";
        require_once THEME_HELPERS . "/wp_footer.php";
        require_once THEME_HELPERS . "/schema-markup.php";
        require_once THEME_HELPERS . "/wp_query.php";
        require_once THEME_HELPERS . "/send-email.php";
        require_once THEME_HELPERS . "/captcha.php";
    }

    public function menu_walkers()
    {
        require_once locate_template("framework/custom-nav-walker/fallback-navigation.php");
        require_once locate_template("framework/custom-nav-walker/main-navigation.php");
        require_once locate_template("framework/custom-nav-walker/menu-with-icon.php");
        require_once locate_template("framework/custom-nav-walker/responsive-navigation.php");
    }

    public function add_metaboxes()
    {
        include_once THEME_GENERATORS . '/metabox-generator.php';
    }

    public function theme_activated()
    {
        if ('themes.php' == basename($_SERVER['PHP_SELF']) && isset($_GET['activated']) && $_GET['activated'] == 'true') {
            flush_rewrite_rules();
            update_option(THEME_OPTIONS_BUILD, uniqid());
            wp_redirect(admin_url('admin.php?page=' . THEME_NAME));

        }
    }

    public function admin()
    {
        global $abb_phpunit;
        if (is_admin() || (empty($abb_phpunit) == false && $abb_phpunit == true)) {
            require_once THEME_DIR . "/vendor/autoload.php";
            require_once THEME_CONTROL_PANEL . "/logic/validator-class.php";
            require_once THEME_CONTROL_PANEL . "/logic/system-messages/js-messages-lib.php";
            require_once THEME_CONTROL_PANEL . "/logic/system-messages/logic-messages-lib.php";
            require_once THEME_CONTROL_PANEL . "/logic/compatibility.php";
            require_once THEME_CONTROL_PANEL . "/logic/functions.php";
            require_once THEME_CONTROL_PANEL . "/logic/addon-management.php";
            require_once THEME_CONTROL_PANEL . "/logic/plugin-management.php";
            require_once THEME_CONTROL_PANEL . "/logic/template-management.php";
            require_once THEME_CONTROL_PANEL . "/logic/updates-class.php";
            require_once THEME_CONTROL_PANEL . "/logic/icon-selector.php";
            require_once THEME_ADMIN . "/menus-custom-fields/menu-item-custom-fields.php";
            include_once THEME_ADMIN . '/theme-options/options-check.php';
            include_once THEME_ADMIN . '/general/mega-menu.php';
            include_once THEME_ADMIN . '/general/enqueue-assets.php';
            include_once THEME_ADMIN . '/theme-options/options-save.php';
            require_once THEME_INCLUDES . "/tgm-plugin-activation/request-plugins.php";

        }
        require_once THEME_CONTROL_PANEL . "/logic/tracking.php";
        require_once THEME_CONTROL_PANEL . "/logic/tracking-control-panel.php";
    }
    public function language()
    {

        load_theme_textdomain('mk_framework', get_stylesheet_directory() . '/languages');
    }

    public function admin_menus()
    {

        add_menu_page(THEME_NAME, THEME_NAME, 'edit_theme_options', THEME_NAME, array(&$this,
            'control_panel',
        ), 'dashicons-star-filled', 3);

        add_submenu_page(THEME_NAME, __('Control Panel', 'mk_framework'), __('Control Panel', 'mk_framework'), 'edit_theme_options', THEME_NAME, array(&$this,
            'control_panel',
        ));

        if (NEW_UI_LIBRARY) {
            add_submenu_page(THEME_NAME, __('New UI', 'mk_framework'), __('New UI', 'mk_framework'), 'edit_posts', 'ui-library', array(&$this,
                'ui_library',
            ));
            add_submenu_page(THEME_NAME, __('UI Page Options', 'mk_framework'), __('UI Page Options', 'mk_framework'), 'edit_posts', 'ui-page-options', array(&$this,
                'ui_page_options',
            ));
        }
    }


    public function ui_page_options()
    {
        include_once THEME_CONTROL_PANEL . '/logic/ui-page-options.php';
    }
    public function ui_library()
    {
        include_once THEME_CONTROL_PANEL . '/logic/ui-library.php';
    }


    public function control_panel()
    {
        include_once THEME_CONTROL_PANEL . '/v2/layout/master.php';
    }


    /**
     * This function maintains the table for actively used theme components.
     *
     * @author      UÄur Mirza ZEYREK
     * @copyright   Artbees LTD (c)
     * @link        http://artbees.net
     * @since       Version 5.0
     */
    public function mk_theme_setup()
    {
        global $wpdb;
        global $jupiter_db_version;
        global $jupiter_table_name;

        $wp_get_theme          = wp_get_theme();
        $current_theme_version = $wp_get_theme->get('Version');
        $jupiter_db_version    = $current_theme_version;
        $jupiter_table_name    = $wpdb->prefix . "mk_components";

        if ($jupiter_db_version != get_option('jupiter_db_version')) {
            $charset_collate = $wpdb->get_charset_collate();
            $sql             = " CREATE TABLE IF NOT EXISTS $jupiter_table_name (id bigint(20) NOT NULL primary key AUTO_INCREMENT,
                type varchar(20) NOT NULL,
                status tinyint(1) NOT NULL,
                name varchar(40) NOT NULL,
                added_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                last_update datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                KEY {$jupiter_table_name}_type (type),
                KEY {$jupiter_table_name}_status (status),
                KEY {$jupiter_table_name}_name (name)
                ) $charset_collate;";

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($sql);
            update_option('jupiter_db_version', $jupiter_db_version);
        }
    }

    /**
     * Compatibility check for hosting php version.
     * Returns error if php version is below v5.4
     * @author      Bob ULUSOY & Ugur Mirza ZEYREK
     * @copyright   Artbees LTD (c)
     * @link        http://artbees.net
     * @since       Version 5.0.5
     * @last_update Version 5.0.7
     */
    public function theme_requirement_check()
    {
        if (!in_array($GLOBALS['pagenow'], array('admin-ajax.php'))) {
            if (version_compare(phpversion(), '5.4', '<')) {
                echo '<h2>As stated in <a href="http://demos.artbees.net/jupiter5/jupiter-v5-migration/">Jupiter V5.0 Migration Note</a> your PHP version must be above V5.4. We no longer support php legacy versions (v5.2.X, v5.3.X).</h2>';
                echo 'Read more about <a href="https://wordpress.org/about/requirements/">WordPress environment requirements</a>. <br><br> Please contact with your hosting provider or server administrator for php version update. <br><br> Your current PHP version is <b>' . phpversion() . '</b>';
                wp_die();
            }
        }
    }

    public function theme_dropins()
    {
        include_once THEME_FUNCTIONS . '/dropins.php';
    }
    
    public function theme_options()
    {
        require_once THEME_ADMIN . '/theme-options/class-theme-options.php';
    }
}
