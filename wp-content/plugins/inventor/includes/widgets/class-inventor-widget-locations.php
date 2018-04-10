<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Inventor_Widget_Locations
 *
 * @class Inventor_Widget_Location
 * @package Inventor/Classes/Widgets
 * @author Pragmatic Mates
 */
class Inventor_Widget_Locations extends WP_Widget {
	/**
	 * Initialize widget
	 *
	 * @access public
	 */
	function __construct() {
		parent::__construct(
			'locations',
			__( 'Locations', 'inventor' ),
			array(
				'description' => __( 'Displays locations.', 'inventor' ),
			)
		);
	}

	/**
	 * Frontend
	 *
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {
		$data = array(
			'hide_empty'    => false,
			'parent'        => 0
		);

		if ( ! empty( $instance['locations'] ) && is_array( $instance['locations'] ) && count( $instance['locations'] ) > 0 ) {
			$data['include'] = implode( ',', $instance['locations'] );
		}

		$terms = get_terms( 'locations', $data );

		$appearance = empty( $instance['appearance'] ) ? 'posters' : $instance['appearance'];

		if ( 'posters' == $appearance ) {
			include Inventor_Template_Loader::locate( 'widgets/locations-posters' );
		}

		wp_reset_query();
	}

	/**
	 * Update
	 *
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	/**
	 * Backend
	 *
	 * @access public
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {
		include Inventor_Template_Loader::locate( 'widgets/locations-admin' );
		include Inventor_Template_Loader::locate( 'widgets/appearance-admin' );
		include Inventor_Template_Loader::locate( 'widgets/visibility-admin' );
	}
}