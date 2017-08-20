<?php
/**
 * Header Builder: Main loader file
 *
 * @since 0.1.0
 * @package Header_Builder
 */

// Load the constants, etc.
require_once dirname( __FILE__ ) . '/hb-config.php';
require_once HB_INCLUDES_DIR . '/helpers/array.php';
require_once HB_INCLUDES_DIR . '/class-hb-grid.php';

if ( is_admin() ) {
	require_once HB_ADMIN_DIR . '/class-hb-db.php';
	require_once HB_ADMIN_DIR . '/class-hb-screen.php';
}

add_action( 'admin_menu', '_hb_add_admin_menu' );
/**
 * Add's "Header Builder" to Jupiter WordPress menu.
 */
function _hb_add_admin_menu() {
	add_submenu_page( THEME_NAME, __( 'Header Builder', 'mk_framework' ), __( 'Header Builder', 'mk_framework' ), 'edit_theme_options', 'header-builder', '__return_null' );
}

add_filter( 'submenu_file', '_hb_add_return_query_tag' );
/**
 * Add the current page URL as the "return" parameter to our "Jupiter" > Header Builder" submenu.
 */
function _hb_add_return_query_tag() {
	global $submenu;

	$current_url        = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$header_builder_url = add_query_arg( 'return', rawurlencode( $current_url ), 'admin.php?page=header-builder' );

	if ( ! array_key_exists( 'Jupiter', $submenu ) ) {
		return;
	}

	// The following position needs update if the header builder submenu location changes.
	foreach ( $submenu['Jupiter'] as $submenu_key => $submenu_array ) {
		if ( 'header-builder' === $submenu_array[2] ) {
			break;
		}
	}
	$submenu['Jupiter'][ $submenu_key ][2] = $header_builder_url; // WPCS: override ok.
};

add_filter( 'query_vars', '_hb_add_query_vars_filter' );
/**
 * Add header-builder to query vars. This is used for the preview functionality.
 *
 * @param array $public_query_vars The array of whitelisted query variables.
 */
function _hb_add_query_vars_filter( $public_query_vars ) {
	$public_query_vars[] = 'header-builder';
	return $public_query_vars;
}

add_filter( 'template_include', '_hb_preview_template', 99 );
/**
 * Render the "Preview" template when the URL loaded is "?header-builder=preview"
 *
 * @param string $template The path of the template to include.
 */
function _hb_preview_template( $template ) {
	if ( 'preview' === get_query_var( 'header-builder' ) ) {
		return HB_INCLUDES_DIR . '/templates/preview.php';
	}

	return  $template;
}
