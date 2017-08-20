<?php
/**
 * Render full-screen header-builder admin area.
 *
 * @package Header_Builder
 * @subpackage UI
 * @since 0.2.0
 */

/**
 * Create at isolated environment for displaying "wp-admin/admin.php?page=header-builder"
 *
 * @author Dominique Mariano <dominique@artbees.net>
 *
 * @since 0.2.0 Introduced to replace screen.php.
 */
class HB_Screen {
	/**
	 * Constructor.
	 *
	 * Execute header_builder_screen() before any other hook when a user accesses the admin area.
	 * This allows us to create an isolated environment for the Header Builder, where only the assets
	 * needed for Header Builder are loaded and nothing else.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'header_builder_screen' ), 100 );
	}

	/**
	 * Renders the entire Header Builder screen on the front end, with default values.
	 *
	 * @SuppressWarnings(PHPMD.ExitExpression)
	 */
	public function header_builder_screen() {
		// If we are not on "wp-admin/admin.php?page=header-builder", then bail.
		if ( ! isset( $_GET['page'] ) || 'header-builder' !== $_GET['page'] ) { // WPCS: CSRF ok.
			return;
		}

		wp_enqueue_style( 'hb-font-awesome', get_template_directory_uri() . '/framework/header-builder/admin/fonts/font-awesome.min.css' );
		wp_enqueue_style( 'hb-bootstrap', get_template_directory_uri() . '/framework/header-builder/admin/css/bootstrap.min.css' );
		wp_enqueue_style( 'hb', get_template_directory_uri() . '/framework/header-builder/admin/css/screen.css', array( 'hb-font-awesome', 'hb-bootstrap' ) );

	    wp_register_script( 'hb',  get_template_directory_uri() . '/framework/header-builder/admin/js/screen.js', array( 'jquery' ), false, true );
		wp_localize_script( 'hb', 'ajax_object', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		) );

		// These default values need to be reconsidered. Ideally, we should start blank.
		$state = '{"model":{"activeDevice":"desktop","desktop":{"past":[],"future":[],"present":{"id":"ciyk0megh00063j5le1zw9dbi","highlightedId":"ciyk0nmvw000k3j5lq2qzzseo","category":"CONTAINER","rows":[{"type":"row","caption":"Row","category":"ROW","id":"ciyk0megh00073j5lbaqqngk4","columns":[{"category":"COLUMN","id":"ciyk0megh00083j5lvqd5ozsc","elements":[],"width":12,"options":{"backgroundColor":"#ffffff","padding":0}}],"options":{}},{"type":"row","caption":"Row","category":"ROW","id":"ciyk0megi00093j5l6kh7ipkm","columns":[{"category":"COLUMN","id":"ciyk0megi000a3j5l0h4s601f","elements":[],"width":2,"options":{"backgroundColor":"#ffffff","padding":0}},{"category":"COLUMN","id":"ciyk0megi000b3j5l1jmtfidp","elements":[],"width":8,"options":{"backgroundColor":"#ffffff","padding":0}},{"category":"COLUMN","id":"ciyk0megi000c3j5l7yzl2rae","elements":[],"width":2,"options":{"backgroundColor":"#ffffff","padding":0}}],"options":{}}],"options":{"fullWidth":false,"fixed":false}}},"tablet":{"past":[],"future":[],"present":{"id":"ciyk0megi000d3j5lz393bj0b","highlightedId":"ciyk0megi000d3j5lz393bj0b","category":"CONTAINER","rows":[{"type":"row","caption":"Row","category":"ROW","id":"ciyk0megi000e3j5lkmn1osno","columns":[{"category":"COLUMN","id":"ciyk0megi000f3j5lq44rjd0s","elements":[],"width":4,"options":{"backgroundColor":"#ffffff","padding":0}},{"category":"COLUMN","id":"ciyk0megi000g3j5ldd1qsxhy","elements":[],"width":8,"options":{"backgroundColor":"#ffffff","padding":0}}],"options":{}}],"options":{"fullWidth":false,"fixed":false}}},"mobile":{"past":[],"future":[],"present":{"id":"ciyk0megi000h3j5lvd8iw4om","highlightedId":"ciyk0megi000h3j5lvd8iw4om","category":"CONTAINER","rows":[{"type":"row","caption":"Row","category":"ROW","id":"ciyk0megi000i3j5l2ik0dom2","columns":[{"category":"COLUMN","id":"ciyk0megi000j3j5le0527rma","elements":[],"width":12,"options":{"backgroundColor":"#ffffff","padding":0}}],"options":{}}],"options":{"fullWidth":false,"fixed":false}}}},"viewModel":{"dropPreviewIndexPath":null,"desktop":{},"tablet":{},"mobile":{},"toolbox":{"elements":[{"type":"row","caption":"Row"},{"type":"divider","caption":"Divider","options":{"color":"#00ff00","width":"5%","verticalPadding":"4px"}},{"type":"logo","caption":"Logo","options":{"dark":{"title":"Default & Dark Logo","description":"This logo will be used as your default logo, and if the transparent header is enabled and your header skin is dark.","dataURL":null},"light":{"title":"Light Logo","description":"This logo will be used when the transparent header is enabled and your header skin is light.","dataURL":null},"sticky":{"title":"Syicky Logo","description":"Use this option to upload the logo which will be used when the header is on sticky state.","dataURL":null},"mobile":{"title":"Mobile Version Logo","description":"Use this option to change your logo for mobile devices if your logo width is quite long to fit in mobile device screen.","dataURL":null}}},{"type":"social-media","caption":"Social Media","options":{"color":"#008800","hoverColor":"#00ff00","iconSize":"32px","sizes":[{"value":"16px","text":"16"},{"value":"24px","text":"24"},{"value":"32px","text":"32"},{"value":"48px","text":"48"},{"value":"64px","text":"64"},{"value":"128px","text":"128"}],"selectedIcon":"facebook","icons":[{"className":"facebook","url":"http://facebook.com"},{"className":"instagram","url":"http://instagram.com"},{"className":"twitter","url":"http://twitter.com"}]}},{"type":"menu","caption":"Menu"},{"type":"button","caption":"Button","options":{"text":"Button","url":"http://google.com","target":"blank","cornerRadius":"3px","width":"70px","padding":"4px"}},{"type":"image","caption":"Image","options":{"fileInfo":{},"width":"48px","height":"48px","align":"left","url":"http://msn.com"}},{"type":"search","caption":"Search","options":{"textWidth":"150px","verticalPadding":"2px","textBgColor":"#ffffff","textColor":"#444444","borderColor":"#eeeeee","buttonStyle":"icon","buttonBgColor":"#dddddd","buttonTextIconColor":"#00ff00","buttonContent":"text","iconColor":"#0000ff","placeholderText":"Search..."}},{"type":"language-selector","caption":"Lang. Selector","options":{"enableFlag":false,"selectedLanguage":"EN","langs":[{"name":"EN","title":"English","url":""},{"name":"FR","title":"French","url":""},{"name":"ES","title":"Espanol","url":""}]}},{"type":"shopping-icon","caption":"Shopping Icon","options":{"fileInfo":{},"iconSize":"16px","iconColor":"#ff0000","textColor":"#eeeeee"}},{"type":"navigation","caption":"Navigation","options":{"menu":"Primary Navigation","menus":[{"value":"primary-navigation","text":"Primary Navigation"},{"value":"second-navigation","text":"Second Navigation"},{"value":"third-navigation","text":"Third Navigation"},{"value":"fourth-navigation","text":"Fourth Navigation"},{"value":"fifth-navigation","text":"Fifth Navigation"},{"value":"sixth-navigation","text":"Sixth Navigation"},{"value":"seventh-navigation","text":"Seventh Navigation"},{"value":"eighth-navigation","text":"Eighth Navigation"},{"value":"ninth-navigation","text":"Ninth Navigation"},{"value":"tenth-navigation","text":"Tenth Navigation"}],"style":"","styles":[{"value":"navigation-stye1","text":"Style 1"},{"value":"navigation-stye2","text":"Style 2"},{"value":"navigation-stye3","text":"Style 3"},{"value":"navigation-stye4","text":"Style 4"},{"value":"navigation-stye5","text":"Style 5"}],"textColor":"#888888","textHoverColor":"#4444cc","accentColor":"#FEA733"}},{"type":"textbox","caption":"Text Box","options":{"text":""}}]}}}';

	    add_option( 'artbees_header_builder', $state );

	    ob_start();
	    $this->setup_hb_admin_header();
	    $this->setup_hb_admin_body();
	    $this->setup_hb_admin_footer();
	    exit;
	}

	/**
	 * Render the main application header and all associated metas, scripts, links, etc.
	 */
	public function setup_hb_admin_header() {
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title><?php esc_html_e( 'Header Builder', 'mk_framework' ); ?></title>
			<script>
		    	var bsData = <?php echo get_option( 'artbees_header_builder' ); // WPCS: XSS ok. ?>;
		    </script>
		    <?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="hb" style="position: fixed; display: block; width: 100vw; height: 100vh; overflow: hidden;">
		<?php
	}

	/**
	 * Render the main application body. This contains all the main parts visible on the front end.
	 */
	public function setup_hb_admin_body() {
		?>
		<div id="header-builder" style="position: fixed; top: 0; height: 100%; bottom: 0; left: 0; background: #d6d6d6; overflow: visible; right: 0; min-width: 0;"></div>
		<?php
	}

	/**
	 * Render the main application footer and all associated metas, scripts, links, etc.
	 */
	public function setup_hb_admin_footer() {
		wp_print_scripts( 'hb' ); ?>
			</body>
		</html>
		<?php
	}
}

new HB_Screen();
