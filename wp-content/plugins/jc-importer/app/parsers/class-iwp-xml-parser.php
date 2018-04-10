<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * ImportWP XML Parser
 */
class IWP_XML_Parser extends IWP_Parser {

	public $name = 'xml';
	public $_xml = null;
	public $field_parser = false;
	protected $_config = array();

	/**
	 * Setup Actions and filters
	 */
	public function __construct() {
		parent::__construct();

		add_filter( 'jci/parse_xml_field', array( $this, 'parse_field' ), 10, 4 );
		add_filter( 'jci/process_xml_map_field', array( $this, 'process_map_field' ), 10, 2 );
		add_filter( 'jci/load_xml_settings', array( $this, 'load_settings' ), 10, 2 );
		add_filter( 'jci/ajax_xml/preview_record', array( $this, 'ajax_preview_record' ), 10, 4 );
		add_filter( 'jci/ajax_xml/record_count', array( $this, 'ajax_record_count' ), 10, 1 );

		add_action( 'jci/save_template', array( $this, 'save_template' ), 10, 2 );
		add_action( 'jci/output_' . $this->get_name() . '_general_settings', array(
			$this,
			'output_general_settings'
		) );
		add_action( 'jci/output_' . $this->get_name() . '_group_settings', array(
			$this,
			'output_group_settings'
		), 10, 2 );
	}

	/**
	 * Display Addon General Fields, Replacing register_settings
	 * @return void
	 */
	public function output_general_settings( $id ) {

		$import_base = ImporterModel::getImporterMetaArr( $id, array( '_parser_settings', 'import_base' ) );
		echo JCI_FormHelper::text( 'parser_settings[import_base]', array(
			'label'   => 'Record Base',
			'default' => $import_base,
			'after'   => ' <a href="#" class="base-node-select base button button-small button-iwp">Select</a>',
			'class'   => 'jc-importer_general-base',
			'tooltip' => JCI()->text()->get( 'import.settings.xml_base_node'  )
		) );
	}

	/**
	 * Display Addon Group Fields, Replacing register_settings
	 * @return void
	 */
	public function output_group_settings( $id, $group ) {

		$import_base = ImporterModel::getImporterMetaArr( $id, array( '_parser_settings', 'group_base', $group ) );

		$importer_version = ImporterModel::getImporterMetaArr($id, array('_import_settings', 'version'));
		if(version_compare($importer_version, '0.6.0', '>=') || empty($import_base)){
			echo JCI_FormHelper::hidden("parser_settings[group][{$group}][base]", array(
				'default' => $import_base,
				'class'   => 'jc-importer_general-group',
			));
		}else {
			echo JCI_FormHelper::text( "parser_settings[group][{$group}][base]", array(
				'label'   => 'Record Base',
				'default' => $import_base,
				'after'   => ' <a href="#" class="base-node-select group button button-small button-iwp">Select</a>',
				'class'   => 'jc-importer_general-group',
				'tooltip' => JCI()->text()->get( 'import.settings.xml_base_node' )
			) );
		}
	}

	/**
	 * Load parser settings into the addon array
	 *
	 * @param  array $settings
	 *
	 * @return void
	 */
	public function load_settings( $settings, $id ) {

		$settings['import_base'] = ImporterModel::getImporterMetaArr( $id, array( '_parser_settings', 'import_base' ) );
		$settings['group_base']  = ImporterModel::getImporterMetaArr( $id, array( '_parser_settings', 'group_base' ) );

		return $settings;
	}

	/**
	 * Save XML fields into database
	 *
	 * @param  int $id
	 *
	 * @return void
	 */
	public function save_template( $id, $parser_type ) {

		if ( $parser_type == 'xml' ) {
			$parser_settings = $_POST['jc-importer_parser_settings'];

			$base        = $parser_settings['import_base'];
			$temp_groups = $parser_settings['group'];

			$groups = array();
			foreach ( $temp_groups as $group_id => $group ) {
				$groups[ $group_id ] = $group['base'];
			}

			$result = array(
				'import_base' => $base,
				'group_base'  => $groups
			);

			ImporterModel::setImporterMeta( $id, '_parser_settings', $result );
		}
	}

	/**
	 * Load XML Node for current record
	 *
	 * @param  int $group_id
	 * @param  integer $row
	 *
	 * @return string
	 */
	public function process_map_field( $group_id, $row ) {

		/**
		 * @global JC_Importer $jcimporter
		 */
		global $jcimporter;

		// setup current group node
		$group      = isset( $jcimporter->importer->addon_settings['group_base'][ $group_id ] ) ? $jcimporter->importer->addon_settings['group_base'][ $group_id ] : '';
		$group_node = isset( $group['base'] ) ? $group['base'] : '';

		// setup template base node
		$base      = $jcimporter->importer->addon_settings['import_base'];
		$base_node = $base . "[$row]$group_node/";

		return $base_node;
	}

	/**
	 * Parse XML Field
	 *
	 * @param  string $field
	 * @param  string $base_node
	 * @param  SimpleXMLElement $xml
	 *
	 * @return string
	 */
	public function parse_field( $field, $map, $base_node, $xml ) {

		$field_parser = new IWP_XML_Field_Parser( $xml );

		return $field_parser->parse_field( $field, $base_node );
	}

	/**
	 * Get the total of rows matching the Importers settings
	 *
	 * @param  integer $importer_id
	 *
	 * @return integer
	 */
	public function get_total_rows( $importer_id = 0 ) {

		/**
		 * @global JC_Importer $jcimporter
		 */
		global $jcimporter;

		if ( $importer_id > 0 ) {
			$id          = $importer_id;
			$file        = ImporterModel::getImportSettings( $id, 'import_file' );
			$import_base = ImporterModel::getImporterMetaArr( $id, array( '_parser_settings', 'import_base' ) );
		} else {
			$id          = $jcimporter->importer->ID;
			$file        = $jcimporter->importer->get_file();
			$import_base = $jcimporter->importer->addon_settings['import_base'];
		}

		// todo: throw error
		if ( ! is_file( $file ) ) {
			return 0;
		}

		// load xml and count records
		$xml = simplexml_load_file( $file );
		if ( isset( $import_base ) && ! empty( $import_base ) ) {
			$result = $this->count_rows( $xml, $import_base );
		} else {
			$result = 1;
		}

		return $result;
	}

	/**
	 * Count XML Records
	 *
	 * @param  SimpleXML $xml
	 * @param  string $node
	 *
	 * @return integer
	 */
	public function count_rows( $xml, $node = '/' ) {
		return count( $xml->xpath( $node ) );
	}

	/**
	 * Parse XML from ImportWP Config
	 *
	 * @param  integer $row
	 *
	 * @return array         Parsed XML
	 */
	public function parse( $row = null ) {

		/**
		 * @global JC_Importer $jcimporter
		 */
		global $jcimporter;

		// setup template base node
		$base_node = $jcimporter->importer->addon_settings['import_base'];

		// set groups with addon settings
		$fields = $jcimporter->importer->addon_settings['group_base']; //[$group_id]['base'];
		$groups = $jcimporter->importer->get_template_groups();
		foreach ( $groups as $key => $group ) {
			$groups[ $key ]['base_node'] = isset( $fields[ $key ] ) ? $fields[ $key ] : '';
		}

		$xml = simplexml_load_file( $this->file );

		return $this->parse_xml( $xml, $row, $base_node, $groups );
	}

	/**
	 * New XML Parse function
	 *
	 * Accept xml as SimpleXML Object, without config
	 *
	 * @param  SimpleXML Object  $xml
	 * @param  integer $row
	 * @param  String $base_node
	 * @param  Array $groups
	 *
	 * @return array    Parsed XML
	 */
	public function parse_xml( $xml, $row = 0, $base_node, $groups = array() ) {

		$output     = array();
		$this->_xml = $xml;

		// process selected row
		if ( intval( $row ) > 0 ) {
			$base_node = $base_node . "[$row]";
		}

		// load xml to parse
		$xml_test = $this->_xml->xpath( $base_node );
		foreach ( $xml_test as $id => $x ) {

			$output_row = array();

			// parse all groups
			foreach ( $groups as $group_id => $group ) {

				$output_group = array();

				if ( $group['type'] == 'single' ) {

					// parse single group
					$output_group = $this->parse_single_group( $id, $xml, $base_node, $group['base_node'], $group['fields'] );
				} elseif ( $group['type'] == 'repeater' ) {

					// parse repeater group
					$output_group = $this->parse_repeater_group( $id, $xml, $base_node, $group['base_node'], $group['fields'] );
				}
				$output_row[ $group_id ] = $output_group;
			}
			$output[] = $output_row;
		}

		return $output;
	}

	/**
	 * Parse Single Groups
	 *
	 * @param  int $record_id
	 * @param  SimpleXML Obhect $xml
	 * @param  string $base_node
	 * @param  string $group_node
	 * @param  array $fields
	 *
	 * @return array    Parsed XML
	 */
	public function parse_single_group( $record_id, $xml, $base_node = '', $group_node = '', $fields = array() ) {

		$output_group_record = array();

		foreach ( $fields as $field_id => $map ) {

			$output_base_node                 = $base_node . '[' . ( $record_id + 1 ) . ']' . $group_node . '/';
			$terms                            = apply_filters( 'jci/parse_xml_field', $map, $map, $output_base_node, $xml );
			$terms                            = apply_filters( 'jci/parse_xml_field/' . $field_id, $terms, $map, $output_base_node, $xml );
			$output_group_record[ $field_id ] = (string) $terms;
		}

		return $output_group_record;
	}

	/**
	 * Parse Repeater Groups
	 *
	 * @param  int $record_id
	 * @param  SimpleXML Object $xml
	 * @param  string $base_node
	 * @param  string $group_node
	 * @param  array $fields
	 *
	 * @return array    Parsed XML
	 */
	public function parse_repeater_group( $record_id, $xml, $base_node = '', $group_node = '', $fields = array() ) {

		$output_group_record = array();
		$elems               = $xml->xpath( $base_node . '[' . ( $record_id + 1 ) . ']' . $group_node );

		foreach ( $elems as $elem_id => $elem_data ) {
			$test_row = array();
			foreach ( $fields as $field_id => $map ) {

				$output_base_node      = $base_node . '[' . ( $record_id + 1 ) . ']' . $group_node . '[' . ( $elem_id + 1 ) . ']/';
				$value                 = apply_filters( 'jci/parse_xml_field', $map, $map, $output_base_node, $xml );
				$value                 = apply_filters( 'jci/parse_xml_field/' . $field_id, $value, $map, $output_base_node, $xml );
				$test_row[ $field_id ] = (string) $value;
			}
			$output_group_record[] = $test_row;
		}

		return $output_group_record;
	}

	public function ajax_preview_record( $result = '', $row, $map, $field ) {

		$general_base = isset( $_POST['general_base'] ) ? $_POST['general_base'] : null;
		$group_base   = isset( $_POST['group_base'] ) ? $_POST['group_base'] : null;

		return $this->preview_field( $map, $row, $field, $general_base, $group_base );
	}

	public function preview_field( $map = '', $selected_row = null, $field, $general_base = null, $group_base = null ) {

		/**
		 * @global JC_Importer $jcimporter
		 */
		global $jcimporter;

		$xml = simplexml_load_file( $this->file );

		$base_node  = is_null( $general_base ) ? $jcimporter->importer->addon_settings['import_base'] : $general_base; // set general xml base
		$group_base = is_null( $group_base ) ? '' : $group_base; // set xml group base if one is provided

		$output_base_node = $base_node . "[$selected_row]" . $group_base;

		$result = apply_filters( 'jci/parse_xml_field', $map, $map, $output_base_node, $xml );
		$result = apply_filters( 'jci/parse_xml_field/' . $field, $result, $map, $output_base_node, $xml );

		return $result;
	}

	public function ajax_record_count( $result = 0 ) {

		$xml = simplexml_load_file( $this->file );

		$general_base = isset( $_POST['general_base'] ) ? $_POST['general_base'] : '';
		$result       = $this->count_rows( $xml, $general_base );

		return $result;
	}
}

/**
 * Autoload XML Parser
 */
add_filter( 'jci/register_parser', 'register_xml_parser', 10, 1 );
function register_xml_parser( $parsers = array() ) {
	$parsers['xml'] = new IWP_XML_Parser(); // 'JC_XML_Parser';

	return $parsers;
}