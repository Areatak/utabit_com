<?php
/**
 * Render 'Image' element to the front end
 *
 * @package Header_Builder
 * @subpackage Elements_Generator
 * @since 0.1.0
 */

/**
 * Main class used for rendering 'Image' element to the front end.
 *
 * @since 0.1.0
 */
class HB_Element_Image extends HB_Element {
	/**
	 * Generate markup and style for the 'Image' element.
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
	 *                 File information
	 *
	 *                 @type string $name
	 *                 @type string $type
	 *                 @type string $size
	 *                 @type string $content
	 *           }
	 *           @type string $url
	 *           @type string $width
	 *           @type string $height
	 *           @type string $align
	 *     }
	 * }
	 * @param int   $row_index     Numeric index for the row.
	 * @param int   $column_index  Numeric index for the column.
	 * @param int   $element_index Numeric index for the element.
	 * @return array {
	 *     @type string $markup Stringified HTML.
	 *     @type string $style Stringified CSS.
	 * }
	 */
	public function __construct( array $element, $row_index, $column_index, $element_index ) {
		parent::__construct( $element, $row_index, $column_index, $element_index );

		$this->width = $this->get_option( 'width', array() );
		$this->height = $this->get_option( 'height', array() );
		$this->url = $this->get_option( 'url', array() );
		$this->file_info = $this->get_option( 'fileInfo', array() );
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
		$markup = sprintf( '<a href="%s" style="display: inline-block"><img class="%s" src="%s"/></a>',
			esc_url( $this->url ),
			esc_attr( $this->class_name ),
		 	esc_attr( array_key_exists( 'content', $this->file_info ) ? $this->file_info['content'] : '' )
		);

		$style = ".{$this->class_name} {
			width: {$this->width};
			height: {$this->height};
		}";

		return compact( 'markup', 'style' );
	}
}
