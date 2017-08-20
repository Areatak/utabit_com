<?php
/**
 * Render 'Social Media' element to the front end
 *
 * @package Header_Builder
 * @subpackage Elements_Generator
 * @since 0.1.0
 */

/**
 * Main class used for rendering 'Social Media' element to the front end.
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 *
 * @since 0.1.0
 */
class HB_Element_Social_Media extends HB_Element {
	/**
	 * Generate markup and style for the 'Social Media' element.
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
	 *           @type string $selectedIcon
	 *           @icons array $icons
	 *           @type string $color
	 *           @type string $hoverColor
	 *           @type string $iconSize
	 *           @type array $sizes
	 *     }
	 * }
	 * @param int   $row_index     Numeric index for the row.
	 * @param int   $column_index  Numeric index for the column.
	 * @param int   $element_index Numeric index for the element.
	 */
	public function __construct( array $element, $row_index, $column_index, $element_index ) {
		parent::__construct( $element, $row_index, $column_index, $element_index );

		$this->color = $this->get_option( 'color', '#008800' );
		$this->hover_color = $this->get_option( 'hoverColor', '#00ff00' );
		$this->icon_size = $this->get_option( 'iconSize', '50%' );
		$this->icon_style = 'mk-jupiter-icon-'; // Default for now. Fix this later.
		$this->icons = $this->get_option( 'icons', array() );
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

		/**
		 * Generate markup and style for the 'Button' element.
		 *
		 * @since 0.1.0
		 *
		 * @see Mk_SVG_Icons::get_svg_icon_by_class_name()
		 *
		 * @param string $carry Previously rendered result of the function that
		 *                      gets carried over to next iteration.
		 * @param array $icon {
		 *     The data to transform into button HTML/CSS.
		 *
		 *     @type string $className The 'currently clicked' social media GUI in the backend.
		 *     @type array  $url        Array of social media icons to display.
		 * @return string HTML anchor tag with SVG icon.
		 */
		$icons_markup = array_reduce( $this->icons, function( $carry, $icon ) {
			$icon_class_name = $this->icon_style . array_safe_get( $icon, 'className', '' );

			$url = array_safe_get( $icon, 'url', '#' );
			return $carry . sprintf( '<a class="%s" target="_blank" href="%s">%s</a>',
				esc_attr( "$icon_class_name {$this->class_name}-icon" ),
				esc_url( $url ),
				Mk_SVG_Icons::get_svg_icon_by_class_name( false, $icon_class_name, $this->icon_size )
			);

		}, '' );

		$markup = sprintf( '<div class="%s">%s</div>', esc_attr( $this->class_name ), $icons_markup );

		$style = "
			.{$this->class_name} {
				display: inline-block;
			}

			.{$this->class_name}-icon {
				display: inline-block;
			}

			.{$this->class_name}-icon > svg {
				fill: {$this->color};
			}

			.{$this->class_name}-icon:hover > svg {
				fill: {$this->hover_color};
			}";

		return compact( 'markup', 'style' );
	}
}
