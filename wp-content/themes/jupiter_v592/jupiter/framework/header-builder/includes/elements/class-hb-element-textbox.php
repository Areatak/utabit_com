<?php
/**
 * Render 'Textbox' element to the front end
 *
 * @package Header_Builder
 * @subpackage Elements_Generator
 * @since 0.1.0
 */

/**
 * Main class used for rendering 'Textbox' element to the front end.
 *
 * @since 0.1.0
 */
class HB_Element_Textbox extends HB_Element {
	/**
	 * Generate markup and style for the 'Textbox' element.
	 *
	 * @since 0.1.0
	 *
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 *
	 * @param array $element {
	 *     The data to transform into HTML/CSS.
	 *
	 *     @type string $type
	 *     @type string $caption
	 *     @type string $id
	 *     @type string $category
	 *     @type array $options {
	 *           Array of element CSS properties and other settings.
	 *
	 *           @type string $text
	 *     }
	 * }
	 * @param int   $row_index     Numeric index for the row.
	 * @param int   $column_index  Numeric index for the column.
	 * @param int   $element_index Numeric index for the element.
	 */
	public function __construct( array $element, $row_index, $column_index, $element_index ) {
		parent::__construct( $element, $row_index, $column_index, $element_index );

		$this->text = $this->get_option( 'text', '' );
	}

	/**
	 * Generate the element's markup and style for use on the front-end.
	 *
	 * @since 0.1.0
	 *
	 * @return array {
	 *      HTML and CSS for the element, based on all its given properties and settings.
	 *
	 *      @type string $markup Element HTML code.
	 *      @type string $style Element CSS code.
	 * }
	 */
	public function get_src() {
		$markup = sprintf( '<input type="text" value="%s" />', esc_attr( $this->text ) );
		$style = '';

		return compact( 'markup', 'style' );
	}
}
