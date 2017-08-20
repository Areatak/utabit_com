<?php
/**
 * Render elements to the front end
 *
 * @package Header_Builder
 * @subpackage Elements_Generator
 * @since 0.1.0
 */

/**
 * Main class used for rendering element to the front end.
 *
 * @since 0.1.0
 */
class HB_Element {
	/**
	 * Array of element properties.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var array $options Array of element CSS properties and other settings.
	 */
	protected $options;

	/**
	 * Index of row containing this element in Header Builder.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var int $options Any whole number.
	 */
	protected $row_index;

	/**
	 * Index of column containing this element in Header Builder.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var int $options Any whole number.
	 */
	protected $column_index;

	/**
	 * Index of element within it's containing column in Header Builder.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var int $options Any whole number.
	 */
	protected $element_index;

	/**
	 * Constructor.
	 *
	 * Sets options, all indices and CSS class name for all elements.
	 *
	 * @since 0.1.0
	 *
	 * @param array $element {
	 *       Array data must at least contain element "type", list of CSS properies, etc.
	 *
	 *       @type string $type    Type of element.
	 *       @type array $options  Array of element CSS properties and other settings.
	 * }
	 * @param int   $row_index     0-indexed nth row.
	 * @param int   $column_index  0-indexed position of element inside row.
	 * @param int   $element_index 0-indexed position of element inside column.
	 */
	public function __construct( array $element, $row_index, $column_index, $element_index ) {
		$options = array_safe_get( $element, 'options', array() );

		$this->options = $options;
		$this->row_index = $row_index;
		$this->column_index = $column_index;
		$this->element_index = $element_index;
		$this->type = str_replace( '_', '-', sanitize_key( array_safe_get( $element, 'type', 'element' ) ) );
		$this->class_name = $this->generate_indexed_class_name( $this->type );
	}

	/**
	 * Safely get value from $option property.
	 *
	 * @since 0.1.0
	 *
	 * @param  string $key     Option to search.
	 * @param  string $default Default value if key is not found. Defaults to empty string.
	 * @return mixed The element property stored in $options.
	 */
	public function get_option( $key, $default = '' ) {
		return array_safe_get( $this->options, (string) $key, $default );
	}

	/**
	 * Create the element class using element "type" and element indices.
	 *
	 * @since 0.1.0
	 *
	 * @param string $base The base name to suffix with element indices. Defualts to "element".
	 */
	protected function generate_indexed_class_name( $base = 'element' ) {
		if ( ! is_string( $base ) ) {
			$base = 'element';
		}

		return sprintf( "$base-%s-%s-%s", $this->row_index, $this->column_index, $this->element_index );
	}
}
