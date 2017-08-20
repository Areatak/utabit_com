<?php
/**
 * Render 'Button' element to the front end
 *
 * @package Header_Builder
 * @subpackage Elements_Generator
 * @since 0.1.0
 */

/**
 * Main class used for rendering 'Button' element to the front end.
 *
 * @since 0.1.0
 */
class HB_Element_Button extends HB_Element {
	/**
	 * Constructor.
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
	 *           @type string $text         The button text to display. Defaults to 'Button'.
	 *           @type string $url          The button URL. Defaults to 'http://google.com'.
	 *           @type string $target       Specifies where to open the URL.
	 *           @type string $cornerRadius CSS property in pixels. Defaults to 3px.
	 *           @type string $width        CSS property in pixels. Defaults to 70px.
	 *           @type string $padding      CSS property in pixels. Defaults to 4px.
	 *     }
	 * }
	 * @param int   $row_index     Numeric index for the row.
	 * @param int   $column_index  Numeric index for the column.
	 * @param int   $element_index Numeric index for the element.
	 */
	public function __construct( array $element, $row_index, $column_index, $element_index ) {
		parent::__construct( $element, $row_index, $column_index, $element_index );

		$this->text = $this->get_option( 'text', '' );
		$this->url = $this->get_option( 'url', '#' );
		$target = $this->get_option( 'target', '_blank' );
		$this->target = ( '_' !== $target[0] ) ? '_' . $target : $target;
		$this->corner_radius = $this->get_option( 'cornerRadius', '3px' );
		$this->padding = $this->get_option( 'padding', '4px' );
		$this->width = $this->get_option( 'width', '70px' );
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
		$markup = sprintf('<a class="%s" href="%s" target="%s" role="button">%s</a>',
			esc_attr( $this->class_name ),
			esc_url( $this->url ),
			esc_attr( $this->target ),
			esc_html( $this->text )
		);

		$style = "
			.{$this->class_name} {
				background-color: #f4f4f4;
				color: #444;
				display: inline-block;
				text-align: center;

				border-radius: {$this->corner_radius};
				width: {$this->width};
				padding: {$this->padding};
			}

			.{$this->class_name}:hover,
			.{$this->class_name}:active,
			.{$this->class_name}:focus,
			.{$this->class_name}:visited {
				text-decoration: none;
				background-color: #eee;
				color: #444;
			}";

		return compact( 'markup', 'style' );
	}
}
