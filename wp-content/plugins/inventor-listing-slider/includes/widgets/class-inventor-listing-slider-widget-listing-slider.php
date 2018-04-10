<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Inventor_Listing_Slider_Widget_Listing_Slider
 *
 * @class Inventor_Listing_Slider_Widget_Listing_Slider
 * @package Inventor_Listing_Slider/Classes/Widgets
 * @author Pragmatic Mates
 */
class Inventor_Listing_Slider_Widget_Listing_Slider extends WP_Widget {
	/**
	 * Initialize widget
	 *
	 * @access public
	 */
	function __construct() {
		parent::__construct(
			'listing_slider',
			__( 'Listing Slider', 'inventor-listing-slider' ),
			array(
				'description' => __( 'Displays listings in slider.', 'inventor-listing-slider' ),
			)
		);

		add_action( 'body_class', array( __CLASS__, 'add_body_class' ) );
	}

	/**
	 * Adds classes to body
	 *
	 * @param $classes array
	 *
	 * @access public
	 * @return array
	 */
	public static function add_body_class( $classes ) {
		$settings = get_option( 'widget_listing_slider' );

		if ( is_array( $settings ) ) {
			foreach ( $settings as $key => $value ) {
				if ( is_active_widget( false, 'listing_slider-' . $key, 'listing_slider' ) ) {
					if ( ! empty( $value['classes'] ) ) {
						$parts   = explode( ',', $value['classes'] );
						$classes = array_merge( $classes, $parts );
					}
				}
			}
		}

		return $classes;
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
		if ( class_exists( 'Inventor' ) ) {
			// TODO: replace functionality with the Inventor_Query::get_listings() helper

			$query = array(
				'post_type'         => Inventor_Post_Types::get_listing_post_types(),
				'post_status'       => 'publish',
				'posts_per_page'    => ! empty( $instance['count'] ) ? $instance['count'] : 3,
				'tax_query'         => array(
					'relation'      => 'AND',
				),
			);

			$meta = array();
			$ids = null;

			if ( ! empty( $instance['ids'] ) && ! empty( $instance['order'] ) && 'ids' == $instance['order'] ) {
				$ids = explode( ',', $instance['ids'] );
			}

			if ( ! empty( $instance['order'] ) ) {
				if ( 'alphabetical' == $instance['order'] ) {
					$query['orderby'] = 'post_title';
					$query['order'] = 'asc';
				}

				if ( 'rand' == $instance['order'] ) {
					$query['orderby'] = 'rand';
				}

				if ( 'ids' == $instance['order'] ) {
					$query['orderby'] = 'post__in';
				}
			}

			if ( ! empty( $instance['attribute'] ) ) {
				if ( 'featured' == $instance['attribute'] ) {
					$meta[] = array(
						'key'       => INVENTOR_LISTING_PREFIX . 'featured',
						'value'     => 'on',
						'compare'   => '=',
					);
				} elseif ( 'reduced' == $instance['attribute'] ) {
					$meta[] = array(
						'key'       => INVENTOR_LISTING_PREFIX . 'reduced',
						'value'     => 'on',
						'compare'   => '=',
					);
				}
			}

			// Listing types
			$listing_types = array();

			// Listing types: pickup
			if ( ! empty( $instance['listing_types'] ) && is_array( $instance['listing_types'] ) ) {
				$query['post_type'] = $instance['listing_types'];
			}

			if ( ! empty( $listing_types ) ) {
				$query['post_type'] = $listing_types;
			}

			// Listing categories
			$listing_categories = array();

			// Listing categories: pickup
			if ( ! empty( $instance['listing_categories'] ) && is_array( $instance['listing_categories'] ) ) {
				$listing_categories = $instance['listing_categories'];
			}

			if ( ! empty( $listing_categories ) ) {
				$listing_category_identifier = is_numeric( $listing_categories[0] ) ? 'id' : 'slug';

				$query['tax_query'][] = array(
					'taxonomy'  => 'listing_categories',
					'field'     => $listing_category_identifier,
					'terms'     => $listing_categories,
				);
			}

			// Listing location
			$location = array();

			// Location: pickup
			if ( ! empty( $instance['locations'] ) && is_array( $instance['locations'] ) ) {
				$location = $instance['locations'];
			}

			if ( ! empty( $location ) ) {
				$query['tax_query'][] = array(
					'taxonomy'  => 'locations',
					'field'     => 'id',
					'terms'     => $location,
				);
			}

			// Exclude sublistings
			$exclude_sublistings = get_theme_mod( 'inventor_general_exclude_sublistings', false );
			if ( $exclude_sublistings ) {
				$meta[] = array(
					'key'       =>  INVENTOR_LISTING_PREFIX . 'parent_listing',
					'compare'   => 'NOT EXISTS',
				);
			}

			// IDs
			if ( is_array( $ids ) ) {
				if ( count( $ids ) > 0 ) {
//				if ( ! empty( $instance['count'] ) && count( $instance['count'] ) >= 0 ) {
//					$ids = array_slice( $ids, 0, $instance['count'] );
//				}
					$query['post__in'] = $ids;
				} else {
					$query['post__in'] = array( 0 );
				}
			}

			// Meta
			if ( ! empty( $meta ) ) {
				$query['meta_query'] = $meta;
			}

			// Customising query
			$query = apply_filters( 'inventor_slider_widget_listings_query', $query, $instance );

			query_posts( $query );
			include Inventor_Template_Loader::locate( 'widgets/listing-slider', INVENTOR_LISTING_SLIDER_DIR );
			wp_reset_query();
		}
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
		if ( class_exists( 'Inventor_Template_Loader' ) ) {
			include Inventor_Template_Loader::locate( 'widgets/listing-slider-admin', INVENTOR_LISTING_SLIDER_DIR );
			include Inventor_Template_Loader::locate( 'widgets/visibility-admin' );
		}
	}
}