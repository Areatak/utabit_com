<?php
/**
 * Retrieve and save data to the options table.
 *
 * @since 0.1.0
 * @package Header_Builder
 */

/**
 * Class file for handling AJAX requests from the frond builder.
 *
 * @author Reza Marandi <ross@artbees.net>
 *
 * @since 0.1.0 Introduced by Medhi S. from Reza Marandi.
 * @since 0.1.0 Update to use wp_send_json_success and wp_send_json_error.
 */
class HB_DB {
	/**
	 * Constructor.
	 *
	 * Create custom handlers for your own custom AJAX requests.
	 */
	public function __construct() {
		add_action( 'wp_ajax_abb_header_builder_store_data', array( &$this, 'store_data' ) );
		add_action( 'wp_ajax_abb_header_builder_retrieve_data', array( &$this, 'retrieve_data' ) );
	}

	/**
	 * Saves entire JSON data into "artbees_header_builder" option in WP Options table.
	 *
	 * @throws Exception On empty $_POST['fn_data'].
	 */
	public function store_data() {
		try {

			// WARNING: @todo Fix security issue: nonces.
			$fn_data = $_POST['fn_data']; // WPCS: CSRF ok.

			if ( empty( $fn_data ) ) {
				throw new Exception( 'Data field value is empty , Please check it.' );
			}

			update_option( 'artbees_header_builder' , str_replace( '\"', '"', $fn_data ) );

			wp_send_json_success( array(
				'message' => 'Successful',
				'data' => array(),
			) );
		} catch ( Exception $e ) {
			wp_send_json_error( array(
				'message' => $e->getMessage(),
				'data' => array(),
			) );
		}
	}

	/**
	 * Retrieves "artbees_header_builder" option in WP Options table from WP Options table
	 * and sents it over to front end as JSON data.
	 *
	 * @throws Exception On empty $_POST['fn_data'].
	 */
	public function retrieve_data() {
		try {
			$fn_data = get_option( 'artbees_header_builder' );

			if ( empty( $fn_data ) ) {
				throw new Exception( 'Data is empty.' );
			}
			wp_send_json_success( array(
				'message' => 'Successful',
				'data' => $fn_data,
			) );

		} catch ( Exception $e ) {
			wp_send_json_error( array(
				'message' => $e->getMessage(),
				'data' => array(),
			) );
		}
	}
}

new HB_DB;
