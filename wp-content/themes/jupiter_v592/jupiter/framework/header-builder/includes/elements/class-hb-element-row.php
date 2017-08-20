<?php
/**
 * Render 'Row' element to the front end
 *
 * @package Header_Builder
 * @subpackage Elements_Generator
 * @since 0.1.0
 */

/**
 * Main class used for rendering 'Row' element to the front end.
 *
 * @since 0.2.0
 */
class HB_Element_Row extends HB_Element {
	/**
	 * Constructor.
	 *
	 * @since 0.2.0
	 *
	 * @param array $element {
	 *     The data to transform into HTML/CSS.
	 *
	 *     @type string $type
	 *     @type string $caption
	 *     @type string $id
	 *     @type string $category
	 *     @type array $options {
	 *           Array of element CSS properties.
	 *
	 *           @type array $padding {
	 *                The padding of the row. Default is 0 for all.
	 *
	 *                @type string marginTop
	 *                @type string marginRight
	 *                @type string marginBottom
	 *                @type string marginLeft
	 *           }
	 *           @type array $margin  {
	 *                The margin of the row. Default is 0 for all.
	 *
	 *                @type string marginTop
	 *                @type string marginRight
	 *                @type string marginBottom
	 *                @type string marginLeft
	 *           }
	 *     }
	 * }
	 * @param int   $row_index Numeric index for the row.
	 */
	public function __construct( array $element, $row_index ) {
		parent::__construct( $element, $row_index, false, false );

		$this->padding = $this->get_option( 'padding', 0 );
		$this->margin  = $this->get_option( 'margin', 0 );
		$this->class   = 'row-' . $row_index;
	}

	/**
	 * Generate the element's markup and style for use on the front-end.
	 *
	 * @since 0.2.0
	 *
	 * @return array {
	 *      HTML and CSS for the element, based on all its given properties and settings.
	 *
	 *      @type string $markup Element HTML code.
	 *      @type string $style Element CSS code.
	 * }
	 */
	public function get_src() {
		$markup  = '';

		// Replace paddingTop with padding-top, etc. Looks faster than using RegEx.
		$padding = '';
		if ( ! empty( $this->padding ) ) {
			foreach ( $this->padding as $key => $value ) {
				$property = strtolower( str_replace( 'padding', 'padding-', $key ) );
				$padding .= $property . ': ' . $value . 'px;';
			}
			unset( $key, $property, $value );
		}

		// Replace marginTop with margin-top, etc. Looks faster than using RegEx.
		$margin  = '';
		if ( ! empty( $this->margin ) ) {
			foreach ( $this->margin as $key => $value ) {
				$property = strtolower( str_replace( 'margin', 'margin-', $key ) );
				$margin .= $property . ': ' . $value . 'px;';
			}
			unset( $key, $property, $value );
		}

		$style = "
            .{$this->class} {
                {$padding}
                {$margin}
            }";

		return compact( 'markup', 'style' );
	}
}
