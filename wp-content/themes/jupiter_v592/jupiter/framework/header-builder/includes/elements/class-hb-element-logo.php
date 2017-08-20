<?php
/**
 * Render 'Logo' element to the front end
 *
 * @package Header_Builder
 * @subpackage Elements_Generator
 * @since 0.2.0
 */

/**
 * Main class used for rendering 'Logo' element to the front end.
 *
 * @since 0.2.0 Introduced.
 */
class HB_Element_Logo extends HB_Element {
	/**
	 * Constructor.
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
	 *           @type string $width         The width of the logo. Defaults to 48.
	 *           @type string $height        The height of the logo. Defaults to 48.
	 *           @type string $link_homepage Link the logo to Homepage. Defaults to false.
	 *           @type string $margin        The margin of the logo. Defaults to 0px.
	 *           @type string $padding       The padding of the logo. Defaults to 0px.
	 *     }
	 * }
	 * @param int   $row_index     Numeric index for the row.
	 * @param int   $column_index  Numeric index for the column.
	 * @param int   $element_index Numeric index for the element.
	 */
	public function __construct( array $element, $row_index, $column_index, $element_index ) {
		parent::__construct( $element, $row_index, $column_index, $element_index );

		$this->width   = $this->get_option( 'width', 'auto' );
		$this->height  = $this->get_option( 'height', 'auto' );
		$this->padding = $this->get_option( 'padding', array() );
		$this->margin  = $this->get_option( 'margin', array() );
		$this->image_src     = $this->get_logo_src();
		$this->description   = get_bloginfo( 'description' );
		$this->link_homepage = $this->get_option( 'linkHomepage', false );
	}

	/**
	 * Generate the element's markup and style for use on the front-end.
	 *
	 * @return array {
	 *      HTML and CSS for the element, based on all its given properties and settings.
	 *
	 *      @type string $markup Element HTML code.
	 *      @type string $style Element CSS code.
	 * }
	 */
	public function get_src() {

		$link = '';
		if ( true === $this->link_homepage ) {
			$link = 'href="' . get_home_url() . '"';
		}

		// Set width.
		$width_attr  = 'width="' . esc_attr( $this->width ) . '"';
		$width_style = '';
		if ( 'auto' === $this->width || '' === $this->width ) {
			$width_attr  = '';
			$width_style = 'width: auto;';
		}

		// Set height.
		$height_attr  = 'height="' . esc_attr( $this->height ) . '"';
		$height_style = '';
		if ( 'auto' === $this->height || '' === $this->height ) {
			$height_attr  = '';
			$height_style = 'height: auto;';
		}

		$markup = sprintf(
			'<a class="%s" %s>
				<img title="%s" alt="%s" src="%s" %s %s />
			</a>',
			esc_attr( $this->class_name ),
			$link,
			esc_attr( $this->description ),
			esc_attr( $this->description ),
			esc_url( $this->image_src ),
			$width_attr,
			$height_attr
		);

		$padding = $this->get_directions_style( 'padding', $this->padding );
		$margin  = $this->get_directions_style( 'margin', $this->margin );

		$style = "
		.{$this->class_name} {
			display: block;
			width: auto;
			{$margin}
			{$padding}
		}";

		// Set default value for logo image if user didn't set any value of width/height.
		if ( ! empty( $width_style ) || ! empty( $height_style ) ) {
			$style .= "
			.{$this->class_name} img {
				{$height_style}
				{$width_style}
			}";
		}

		return compact( 'markup', 'style' );
	}

	/**
	 * Temporary solution to get the logo image. There are several.
	 *
	 * @return string Logo image source URL.
	 */
	private function get_logo_src() {

		$options  = get_option( THEME_OPTIONS );

		$logo_src = THEME_IMAGES . '/jupiter-logo.png';
		if ( ! empty( $options['logo'] ) ) {
			$logo_src = $options['logo'];
		}

		return $logo_src;
	}

	/**
	 * Get margin or padding properties.
	 *
	 * @param  string $property Property name to check.
	 * @param  string $data     Property value to check.
	 * @return string           Properties list with the value.
	 */
	public function get_directions_style( $property, $data ) {
		$style = '';
		if ( ! empty( $data ) ) {
			foreach ( $data as $key => $value ) {
				$property_name = strtolower( str_replace( $property, $property . '-', $key ) );
				$style .= $property_name . ': ' . $value . 'px;';
			}
		}
		return $style;
	}

}
