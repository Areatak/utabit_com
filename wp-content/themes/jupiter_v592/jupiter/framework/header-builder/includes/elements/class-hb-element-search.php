<?php
/**
 * Render 'Search' element to the front end
 *
 * @package Header_Builder
 * @subpackage Elements_Generator
 * @since 0.1.0
 */

/**
 * Main class used for rendering 'Search' element to the front end.
 *
 * @since 0.1.0
 */
class HB_Element_Search extends HB_Element {
	/**
	 * Generate markup and style for the 'Search' element.
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
	 *           @type string $textWidth
	 *           @type string $verticalPadding
	 *           @type string $textBgColor
	 *           @type string $textColor
	 *           @type string $borderColor
	 *           @type string $buttonStyle
	 *           @type string $buttonBgColor
	 *           @type string $buttonTextIconColor
	 *           @type string $buttonContent
	 *           @type string $iconColor
	 *           @type string $placeholderText
	 *     }
	 * }
	 * @param int   $row_index     Numeric index for the row.
	 * @param int   $column_index  Numeric index for the column.
	 * @param int   $element_index Numeric index for the element.
	 */
	public function __construct( array $element, $row_index, $column_index, $element_index ) {
		parent::__construct( $element, $row_index, $column_index, $element_index );

		$this->textfield_width = $this->get_option( 'textWidth', '150px' );
		$this->vertical_padding = $this->get_option( 'verticalPadding', '2px' );
		$this->text_bg_color = $this->get_option( 'textBgColor', '#ffffff' );
		$this->text_color = $this->get_option( 'textColor', '#444444' );
		$this->border_color = $this->get_option( 'borderColor', '#eeeeee' );
		$this->button_style = $this->get_option( 'buttonStyle', 'icon' );
		$this->placeholder_text = $this->get_option( 'placeholdeText', 'Search...' );
		$this->button_bg_color = $this->get_option( 'buttonBgColor', '#dddddd' );
		$this->button_text_icon_color = $this->get_option( 'buttonTextIconColor', '#00ff00' );
		$this->button_content = $this->get_option( 'buttonContent', 'text' );
		$this->icon_color = $this->get_option( 'iconColor', '#0000ff' );
	}

	/**
	 * The button content markup and style.
	 *
	 * @since 0.1.0
	 *
	 * @param string $btn_style Button style type.
	 * @param string $btn_content_type Button content type.
	 * @return array {
	 *      HTML and CSS for the button content.
	 *
	 *      @type string $markup Element HTML code.
	 *      @type string $style Element CSS code.
	 * }
	 */
	protected function get_button_style_src( $btn_style, $btn_content_type ) {
		$markup = '';
		$style = '';

		switch ( $btn_style ) {
			case 'button':
				switch ( $btn_content_type ) {
					case 'text':
						$button_content = 'Search';
						break;

					case 'icon':
						$button_content = '<span class="glyphicon glyphicon-search"></span>';
						break;

					default:
						$button_content = '';
						break;
				}

				$style = "
					.{$this->class_name}-button {
						background-color: {$this->button_bg_color};
						color: {$this->button_text_icon_color};
					}
				";

				$markup = sprintf( '<button type="button" class="%s">%s</button>',
					esc_attr( "{$this->class_name}-button" ),
					$button_content
				);
				break;

			case 'icon':
				$style = "
					.{$this->class_name}-icon {
						color: {$this->icon_color};
					}
				";

				$markup = sprintf( '<span class="glyphicon glyphicon-search %s"></span>',
					esc_attr( "{$this->class_name}-icon" )
				);
				break;

			default:
				break;
		}// End switch().

		return compact( 'markup', 'style' );
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
		$button = $this->get_button_style_src( $this->button_style, $this->button_content );

		$markup = sprintf( '<div class="%s"><input class="%s" type="text" placeholder="%s" />%s</div>',
			esc_attr( $this->class_name ),
			esc_attr( "{$this->class_name}-input" ),
			esc_attr( $this->placeholder_text ),
			$button['markup']
		);

		$style = "
			.{$this->class_name} {
				display: block;
			}

			.{$this->class_name}-input {
				width: {$this->textfield_width};
				padding-top: {$this->vertical_padding};
				padding-bottom: {$this->vertical_padding};
				background-color: {$this->text_bg_color};
				color: {$this->text_color};
				border-color: {$this->border_color};
			}
		";

		$style .= $button['style'];

		return compact( 'markup', 'style' );
	}
}
