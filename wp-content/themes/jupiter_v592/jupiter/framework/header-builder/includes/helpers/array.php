<?php
/**
 * Array helper functions.
 *
 * @package Header_Builder
 * @subpackage Utility
 * @since 0.1.0
 */

if ( ! function_exists( 'array_safe_get' ) ) :
	/**
	 * Safely get an item from an associative array using an array key,
	 * and provide a default value if key does not exist.
	 *
	 * @since 0.1.0
	 *
	 * @param array  $arr Array of any conceivable structure.
	 * @param string $key Array key to use in retrieving value.
	 * @param mixed  $default Default value to provide in case key is not in array.
	 * @return mixed Any value or another array.
	 */
	function array_safe_get( $arr, $key, $default = null ) {
		return array_key_exists( $key, $arr ) ? $arr[ $key ] : $default;
	}
endif;
