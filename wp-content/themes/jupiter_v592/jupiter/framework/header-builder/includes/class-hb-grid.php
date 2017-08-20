<?php
/**
 * Header Builder: Elements Generator
 *
 * For use in front end integration with Jupiter.
 *
 * @author Mehdi Shojaei <mehdi@artbees.net>
 *
 * @package Header_Builder
 * @subpackage Elements_Generator
 * @since 0.1.0
 */

/**
 * Takes JSON saved in database and output HTML and CSS on the front end.
 *
 * We won't declare Mk_Header_Builder to be a singleton, as we might eventually find ourselves
 * needing different instances of it.
 */
class HB_Grid {
	/**
	 * Stores array data from JSON retrieved from options table.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var array $model The 'model' stored in 'artbees_header_builder' option.
	 */
	protected $model;

	/**
	 * Stores markup for all available data in $model.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var string $header_markup HTML output.
	 */
	protected $header_markup;

	/**
	 * Stores styles for all available data in $model.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var string $header_style CSS output.
	 */
	protected $header_style;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		$option = get_option( 'artbees_header_builder' );
		$option = json_decode( $option, true );
		$model = isset( $option['model'] ) ? $option['model'] : array();

		$this->model = $model;

		// Otherwise, throw an error if this file is missing.
		if ( ! class_exists( 'Mk_Header_Builder' ) ) {
			require_once( HB_INCLUDES_DIR . '/elements/class-hb-element.php' );
		}

		$output = $this->get_src( 'all' );

		$this->header_markup = $output['markup'];
		$this->header_style = $output['style'];
	}

	/**
	 * Output header builder markup on front-end.
	 *
	 * @since 0.1.0
	 */
	public function render_markup() {
		echo $this->header_markup; // WPCS: XSS OK.
	}

	/**
	 * Output header builder styles on front-end.
	 *
	 * @since 0.1.0
	 */
	public function render_style() {
		echo $this->header_style; // WPCS: XSS OK.
	}

	/**
	 * Output the header builder front end to Jupiter.
	 *
	 * @since 0.1.0
	 *
	 * @see get_device_src()
	 *
	 * @param string|array $devices List of devices to render. Accetps string values of
	 *     'desktop', 'tablet', 'mobile' or an array containing any combination of these values.
	 *     When 'all' is provided, it will be the equivalent of array('desktop', 'tablet', 'mobile').
	 * @return array {
	 *     @type string $markup Stringified HTML.
	 *     @type string $style Stringified CSS.
	 * }
	 */
	public function get_src( $devices = 'all' ) {
		$markup = '';
		$style = '';
		$accepted_device_list = array( 'desktop', 'tablet', 'mobile' );

		if ( 'all' === $devices ) {
			$devices = $accepted_device_list;
		}

		$devices = array_intersect( (array) $devices, $accepted_device_list );

		if ( empty( $devices ) ) {
			return compact( 'markup', 'style' );
		}

		foreach ( $devices as $device_name ) {
			$rendered = $this->get_device_src( $device_name );

			$markup .= sprintf( '<header class="%s">', esc_attr( "header $device_name" ) );
			$markup .= $rendered['markup'];
			$markup .= '</header>';

			$style .= $rendered['style'];
		}

		return compact( 'markup', 'style' );
	}

	/**
	 * Output the header builder front-end set for a specific device.
	 *
	 * @since 0.1.0
	 *
	 * @see get_row_src()
	 *
	 * @param string $device_name Device name. Accepts 'desktop', 'tablet', 'mobile'.
	 * @return array {
	 *     @type string $markup Stringified HTML.
	 *     @type string $style Stringified CSS.
	 * }
	 */
	public function get_device_src( $device_name ) {
		$device_model = array_safe_get( $this->model, $device_name, array() );

		$current_device_model = array_safe_get( $device_model, 'present', array() );

		$rows = array_safe_get( $current_device_model, 'rows', array() );
		$options = array_safe_get( $current_device_model, 'options', array() );
		$class_name = array_safe_get( $options, 'fullWidth', false ) ? 'container-fluid' : 'container';

		$markup = sprintf( '<div class="%s">', esc_attr( $class_name ) );
		$style = '';

		foreach ( $rows as $row_index => $row_model ) {
			$rendered = $this->get_row_src( $row_model, $row_index );

			$markup .= $rendered['markup'];
			$style .= $rendered['style'];
		}

		$markup .= '</div>';

		return compact( 'markup', 'style' );
	}

	/**
	 * Output the header builder front-end set for a specific row.
	 *
	 * @since 0.1.0
	 *
	 * @see get_column_src()
	 *
	 * @param array  $row {
	 *     The data to transform into a single row of columns in the front-end.
	 *
	 *     @type string $type Type of element. Value of 'Row'.
	 *     @type string $caption Caption of element. Value of 'Row'.
	 *     @type string $category Category of element. Value of 'Row'.
	 *     @type string $id Unique ID for this element.
	 *     @type array $columns Array of columns, each containing an array of its own elements.
	 * }
	 * @param string $row_index Numeric index for the row.
	 * @return array {
	 *     @type string $markup Stringified HTML.
	 *     @type string $style Stringified CSS.
	 * }
	 */
	public function get_row_src( array $row, $row_index ) {
		$style = '';
		$markup = '';
		$columns = array_safe_get( $row, 'columns', array(
			array(
				'width' => 12,
			),
		) );

		if ( empty( $columns ) || ! is_numeric( $row_index ) ) {
			return compact( 'markup', 'style' );
		}

		// Gather the column content.
		$markup_content = '';
		$style_content = '';
		foreach ( $columns as $column_index => $column_model ) {
			$rendered = $this->get_column_src( $column_model, $row_index, $column_index );

			$markup_content .= $rendered['markup'];
			$style_content  .= $rendered['style'];
		}

		// Construct dynamic class name from element type.
		$for_class = sanitize_key( $row['type'] );
		$for_file  = sanitize_key( $row['type'] );

		$class_name = 'HB_Element_' . ucwords( $for_class );
		$class_file = HB_INCLUDES_DIR . '/elements/class-hb-element-' . strtolower( $for_file ) . '.php';

		// Render row markup and style.
		$rendered = array();
		if ( ! class_exists( $class_name ) ) {
			if ( ! file_exists( $class_file ) ) {
				return compact( 'markup','style' );
			}

			include_once( $class_file );
		}

		$instance = new $class_name( $row, $row_index, $markup_content );
		$rendered = $instance->get_src();

		// Set the markup and style values.
		$markup = sprintf(
			'<div class="row %s">%s</div>',
			esc_attr( 'row-' . $row_index ),
			$markup_content
		);
		$style  = $rendered['style'];

		// Then merge with current content and style of columns.
		$style .= $style_content;

		return compact( 'markup', 'style' );
	}

	/**
	 * Output the header builder front-end set for a specific column.
	 *
	 * @since 0.1.0
	 *
	 * @see get_element_src()
	 *
	 * @param array  $column {
	 *     The data to transform into a single column in the front-end.
	 *
	 *     @type string $type Type of element. Value of 'Row'.
	 * }
	 * @param string $row_index    Numeric index for the row.
	 * @param string $column_index Numeric index for the column.
	 * @return array {
	 *     @type string $markup Stringified HTML.
	 *     @type string $style Stringified CSS.
	 * }
	 */
	public function get_column_src( array $column, $row_index, $column_index ) {
		$elements = array_safe_get( $column, 'elements', array() );
		$markup = '';
		$style = '';

		if ( empty( $elements ) ||
			! is_numeric( $row_index ) ||
			! is_numeric( $column_index ) ) {
			return  compact( 'markup', 'style' );
		}

		$markup = sprintf( '<div class="col-md-%s">', $column['width'] );

		foreach ( $elements as $element_index => $element_model ) {
			$rendered = $this->get_element_src( $element_model, $row_index, $column_index, $element_index );

			$markup .= $rendered['markup'];
			$style .= $rendered['style'];
		}

		$markup .= '</div>';

		return compact( 'markup', 'style' );
	}

	/**
	 * Output the header builder front-end set for a specific element type.
	 *
	 * @since 0.1.0
	 *
	 * @param array  $element {
	 *     The data to transform into a single column in the front-end.
	 *
	 *     @type string $type Type of element. Value of 'Row'.
	 *
	 * }
	 * @param string $row_index     Nth row to render (0 indexed).
	 * @param string $column_index  Nth column to render (0 indexed).
	 * @param string $element_index Nth column to render (0 indexed).
	 * @return array {
	 *     @type string $markup Stringified HTML.
	 *     @type string $style Stringified CSS.
	 * }
	 */
	public function get_element_src( array $element, $row_index, $column_index, $element_index ) {
		$style = '';
		$markup = '';

		if ( ! isset( $element['type'] ) ||
			! is_string( $element['type'] ) ||
			! is_numeric( $row_index ) ||
			! is_numeric( $column_index ) ||
			! is_numeric( $element_index ) ) {
			return compact( 'markup', 'style' );
		}

		// Construct dynamic class name from element type.
		$for_class = str_replace( '-', '_', sanitize_key( $element['type'] ) );
		$for_file = str_replace( '_', '-', sanitize_key( $element['type'] ) );

		$class_name = 'HB_Element_' . ucwords( $for_class, '_' );
		$class_file = HB_INCLUDES_DIR . '/elements/class-hb-element-' . strtolower( $for_file ) . '.php';

		if ( ! class_exists( $class_name ) ) {
			if ( ! file_exists( $class_file ) ) {
				return compact( 'markup','style' );
			}

			include_once( $class_file );
		}

		$element_instance = new $class_name( $element, $row_index, $column_index, $element_index );

		return $element_instance->get_src();
	}
}

$mk_hb = new HB_Grid();
