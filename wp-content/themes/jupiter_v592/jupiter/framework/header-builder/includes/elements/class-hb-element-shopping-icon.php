<?php
/**
 * Render 'Shopping Icon' element to the front end
 *
 * @package Header_Builder
 * @subpackage Elements_Generator
 * @since 0.1.0
 */

/**
 * Main class used for rendering 'Shopping Icon' element to the front end.
 *
 * @since 0.1.0
 */
class HB_Element_Shopping_Icon extends HB_Element {
	/**
	 * Generate markup and style for the 'Shopping Icon' element.
	 *
	 * @since 0.1.0
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
	 *           @type array $fileInfo {
	 *                 File information.
	 *
	 *                 @type string $name
	 *                 @type string $type
	 *                 @type string $size
	 *                 @type string $content
	 *           }
	 *           @type string $iconSize
	 *           @type string $iconColor
	 *           @type string $textColor
	 *     }
	 * }
	 * @param int   $row_index     Numeric index for the row.
	 * @param int   $column_index  Numeric index for the column.
	 * @param int   $element_index Numeric index for the element.
	 */
	public function __construct( array $element, $row_index, $column_index, $element_index ) {
		parent::__construct( $element, $row_index, $column_index, $element_index );

		$this->icon_size = $this->get_option( 'iconSize', '16px' );
		$this->file_info = $this->get_option( 'fileInfo', array() );
		$this->content = $this->get_option( 'content', '' );
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
		$markup = sprintf( '<img class="%s" src="%s" />',
			esc_attr( $this->class_name ),
			esc_url( $this->content )
		);

		$style = "
			.{$this->class_name} {
				width: {$this->icon_size};
				height: {$this->icon_size};
			}
		";

		return compact( 'markup', 'style' );
	}
}
