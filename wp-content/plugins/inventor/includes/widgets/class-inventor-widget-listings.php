<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Inventor_Widget_Listings
 *
 * @class Inventor_Widget_Listings
 * @package Inventor/Classes/Widgets
 * @author Pragmatic Mates
 */
class Inventor_Widget_Listings extends WP_Widget {
	/**
	 * Initialize widget
	 *
	 * @access public
	 */
	function __construct() {
		parent::__construct(
			'listings',
			__( 'Listings', 'inventor' ),
			array(
				'description' => __( 'Displays listings.', 'inventor' ),
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
		// TODO: replace functionality with the Inventor_Query::get_listings() helper

		$query = array(
			'post_type'         => Inventor_Post_Types::get_listing_post_types(),
			'posts_per_page'    => ! empty( $instance['count'] ) ? $instance['count'] : 3,
			'tax_query'         => array(
				'relation'      => 'AND',
			),
		);

		if ( ! empty( $instance['paged'] ) ) {
			$query['paged'] = $instance['paged'];
		}

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

		// Similar type
		if ( ! empty( $instance['order'] ) && 'similar' == $instance['order'] ) {
			if ( ! empty( $instance['similar_type'] ) ) {
				$post_type = get_post_type();
				$listing_types = array_merge( $listing_types, array( $post_type ) );
			}
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

		// Similar category
		if ( ! empty( $instance['order'] ) && 'similar' == $instance['order'] ) {
			if ( ! empty( $instance['similar_category'] ) ) {
				$categories = wp_get_post_terms( get_the_ID(), 'listing_categories' );
				$categories_ids = wp_list_pluck( $categories, 'term_id' );
				$listing_categories = array_merge( $listing_categories, $categories_ids );
			}
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

		// Similar location
		if ( ! empty( $instance['order'] ) && 'similar' == $instance['order'] ) {
			if ( ! empty( $instance['similar_location'] ) ) {
				$locations = wp_get_post_terms( get_the_ID(), 'locations' );
				$locations_ids = wp_list_pluck( $locations, 'term_id' );
				$location = array_merge( $location, $locations_ids );
			}
		}

		if ( ! empty( $location ) ) {
			$query['tax_query'][] = array(
				'taxonomy'  => 'locations',
				'field'     => 'id',
				'terms'     => $location,
			);
		}

		// Find GPS coordinates if geolocation is set
		if ( ( empty( $instance['latitude'] ) || empty( $instance['longitude'] ) ) && ! empty( $instance['geolocation'] ) ) {
			$geolocation = urlencode( $instance['geolocation'] );
			$server_key = get_theme_mod( 'inventor_general_google_server_key' );
			$key = empty( $server_key ) ? '' : 'key='. $server_key . '&';
			$url = 'https://maps.googleapis.com/maps/api/geocode/json?'. $key . 'address='. $geolocation;
			$gps_result = file_get_contents( $url );

			if ( ! empty( $gps_result ) ) {
				$gps_result = json_decode( $gps_result );

				if ( ! empty( $gps_result->results ) ) {
					$results = $gps_result->results;

					if ( is_array( $results ) && count( $results ) > 0 ) {
						$first_place = $results[0];
						$location = $first_place->geometry->location;
						$instance['latitude'] = $location->lat;
						$instance['longitude'] = $location->lng;
					}
				}
			}
		}

		// Geolocation
		if ( ! empty( $instance['latitude'] ) && ! empty( $instance['longitude'] ) && ! empty( $instance['geolocation'] ) ) {
			// Distance
			if ( empty( $instance['distance'] ) ) {
				$instance['distance'] = get_theme_mod( 'inventor_general_default_distance', 15 );
			}

			$distance_ids = array();
			$rows = Inventor_Filter::filter_by_distance( $instance['latitude'], $instance['longitude'], $instance['distance'] );

			foreach ( $rows as $row ) {
				$distance_ids[] = $row->ID;
			}

			$ids = Inventor_Filter::build_post_ids( $ids, $distance_ids );
		}

		// Similar price
		if ( ! empty( $instance['order'] ) && 'similar' == $instance['order'] ) {
			if ( ! empty( $instance['similar_price'] ) ) {
				$price = Inventor_Price::get_listing_price( get_the_ID() );

				if ( ! empty( $price ) ) {
					$price_range = array(
						'from'	=> (int)((float) $price / 1.2),
						'to'	=> $price * 1.2
					);

					// Price from
					$meta[] = array(
						'key'       =>  INVENTOR_LISTING_PREFIX . 'price',
						'value'     => $price_range['from'],
						'compare'   => '>=',
						'type'      => 'NUMERIC',
					);

					// Price to
					$meta[] = array(
						'key'       => INVENTOR_LISTING_PREFIX . 'price',
						'value'     => $price_range['to'],
						'compare'   => '<=',
						'type'      => 'NUMERIC',
					);
				}
			}
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
		$query = apply_filters( 'inventor_widget_listings_query', $query, $instance );

		if ( ! empty( $instance['order'] ) && 'similar' == $instance['order'] ) {
			if ( empty( $instance['ids'] ) ) {
				$query['post__not_in'] = array( get_the_ID() );
			}
		}

		query_posts( $query );
		include Inventor_Template_Loader::locate( 'widgets/listings' );
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
		include Inventor_Template_Loader::locate( 'widgets/listings-admin' );
		include Inventor_Template_Loader::locate( 'widgets/appearance-admin' );
		include Inventor_Template_Loader::locate( 'widgets/visibility-admin' );
	}
}