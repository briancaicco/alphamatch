<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Inventor_Filter
 *
 * @class Inventor_Filter
 * @package Inventor/Classes
 * @author Pragmatic Mates
 */
class Inventor_Filter {
	/**
	 * Initialize filtering
	 *
	 * @access public
	 * @return void
	 */
	public static function init() {
		add_filter( 'inventor_filter_fields', array( __CLASS__, 'default_fields' ) );
		add_filter( 'inventor_filter_sort_by_choices', array( __CLASS__, 'sort_by_choices' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'archive' ) );
	}

	/**
	 * List of default fields defined by plugin
	 *
	 * @access public
	 * @return array
	 */
	public static function default_fields() {
		return array(
			'listing_type'          => __( 'Listing type', 'inventor' ),
			'title'                 => __( 'Title', 'inventor' ),
			'description'           => __( 'Description', 'inventor' ),
			'keyword'               => __( 'Keyword', 'inventor' ),
			'distance'              => __( 'Distance', 'inventor' ),
			'price'                 => __( 'Price', 'inventor' ),
			'date'                 	=> __( 'Date', 'inventor' ),
			'geolocation'           => __( 'Geolocation', 'inventor' ),
			'locations'             => __( 'Locations', 'inventor' ),
			'listing_categories'    => __( 'Categories', 'inventor' ),
			'featured'              => __( 'Featured', 'inventor' ),
			'reduced'               => __( 'Reduced', 'inventor' ),
		);
	}

	/**
	 * Returns list of available filter fields templates
	 *
	 * @access public
	 * @return array
	 */
	public static function get_fields() {
		return apply_filters( 'inventor_filter_fields', array() );
	}

	/**
	 * Returns sort by choices form filter form
	 *
	 * @access public
	 * @return array
	 */
	public static function sort_by_choices( $choices ) {
		$choices['price'] = __( 'Price', 'inventor' );
		$choices['title'] = __( 'Title', 'inventor' );
		$choices['published'] = __( 'Published', 'inventor' );
//		$choices['date'] = __( 'Date', 'inventor' );
		return $choices;
	}

	/**
	 * Checks if in URI are filter conditions
	 *
	 * @access public
	 * @param bool $include_empty
	 * @return bool
	 */
	public static function has_filter( $include_empty = false ) {
		if ( ! empty( $_GET ) && is_array( $_GET ) ) {
			$filter_fields = Inventor_Filter::get_fields();

			foreach ( $_GET as $key => $value ) {
				if ( in_array( $key, array_keys( $filter_fields ) ) ) {
					if ( ! empty ( $value ) || $include_empty ) {
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Checks if in URI are sorting conditions
	 *
	 * @access public
	 * @return bool
	 */
	public static function is_sorted() {
		return ! empty( $_GET ) && is_array( $_GET ) && ! empty( $_GET['sort-by'] );
	}

	/**
	 * Returns field range from given params array
	 *
	 * @access public
	 * @param string $field
	 * @param array $params
	 * @return array
	 */
	public static function get_field_range( $field, $params ) {
		$range_from = null;
		$range_to = null;

		if ( ! empty( $params ) && is_array( $params ) ) {
			$price_range = array();
			if ( ! empty( $params[ $field ] ) ) {
				$price_range = explode( '-', $params[ $field ] );
			}

			if ( count( $price_range ) <= 2 ) {
				$range_from = count( $price_range ) > 1 ? $price_range[0] : ( empty( $params[ $field . '-from'] ) ? null : $params[ $field . '-from'] );
				$range_to = count( $price_range ) == 1 ? $price_range[0] : ( count( $price_range ) > 1 ? $price_range[1] : ( empty( $params[ $field . '-to'] ) ? null : $params[ $field . '-to'] ) );
			}

			if ( count( $price_range ) > 3 ) {
				$range_from = count( $price_range ) > 3 && $params[ $field ][0] != '-' ? join( '-', array_slice( $price_range, 0, 3 ) ) : ( empty( $params[ $field . '-from'] ) ? null : $params[ $field . '-from'] );
				$range_to = count( $price_range ) == 4 && $params[ $field ][0] == '-' ? join( '-', array_slice( $price_range, 1, 3 ) ) : ( count( $price_range ) > 4 ? join( '-', array_slice( $price_range, 3, 3 ) ) : ( empty( $params[ $field . '-to'] ) ? null : $params[ $field . '-to'] ) );
			}
		}

		return empty( $range_from ) && empty( $range_to ) ? null : array(
			'from' => $range_from,
			'to' => $range_to
		);
	}

	/**
	 * Gets filter form action
	 *
	 * @access public
	 * @return false|string
	 */
	public static function get_filter_action() {
		if ( is_post_type_archive( Inventor_Post_Types::get_listing_post_types() ) ) {
			return get_post_type_archive_link( get_post_type() );
		}

		if ( is_tax( Inventor_Taxonomies::get_listing_taxonomies() ) ) {
			global $wp_query;
			return get_term_link( $wp_query->queried_object );
		}

		return get_post_type_archive_link( 'listing' );
	}

	/**
	 * Filter listings on archive page
	 *
	 * @access public
	 * @param $query
     * @return WP_Query|null
	 */
	public static function archive( $query ) {
		if ( ( is_tax( Inventor_Taxonomies::get_listing_taxonomies() ) ||
		       is_post_type_archive( Inventor_Post_Types::get_listing_post_types( true ) ) ) &&
		       $query->is_main_query() &&
		       ! is_admin() ) {

			return self::filter_query( $query );
		}

		return null;
	}

	/**
	 * Add params into query object
	 *
	 * @access public
	 * @param $query
	 * @param $params array
	 * @return mixed
	 */
	public static function filter_query( $query = null, $params = null ) {
		global $wpdb;
		global $wp_query;

		if ( empty( $query) ) {
			$query = $wp_query;
		}

		if ( empty( $params ) ) {
			$params = $_GET;
		}

		// Filter params
		$params = apply_filters( 'inventor_filter_params', $params );

		// Initialize variables
		$meta = array();
		$taxonomies = array();
		$ids = null;

		// sort by
		if ( empty( $params['sort-by'] ) ) {
			$params['sort-by'] = get_theme_mod( 'inventor_general_default_listing_sort', 'published' );
		}

		if ( ! empty( $params['sort-by'] ) ) {
			switch ( $params['sort-by'] ) {
				case 'title':
					$query->set( 'orderby', 'title' );
					break;
				case 'published':
					$query->set( 'orderby', 'date' );
					break;
				case 'price':
					$query->set( 'meta_key', INVENTOR_LISTING_PREFIX . 'price' );
					$query->set( 'orderby', 'meta_value_num' );
					break;
				case 'rand':
					$query->set( 'orderby', 'rand' );
					break;
				case 'date':
					$orderby_date_field = apply_filters( 'inventor_order_query_field', INVENTOR_LISTING_PREFIX . 'date', 'date' );
					$query->set( 'meta_key', $orderby_date_field );
					$query->set( 'orderby', 'meta_value_num' );
					break;
			}
		}

		$featured_on_top = get_theme_mod( 'inventor_general_featured_on_top', false );
		$featured_always_on_top = get_theme_mod( 'inventor_general_featured_always_on_top', false );

		if ( $featured_on_top && ( ! ( self::has_filter() || self::is_sorted() ) || $featured_always_on_top ) ) {
			$query->set( 'meta_key', INVENTOR_LISTING_PREFIX . 'featured' );
			$query->set( 'orderby', 'meta_value' );
		}

		// order
		if ( empty( $params['order'] ) ) {
			$params['order'] = get_theme_mod( 'inventor_general_default_listing_order', 'desc' );
		}

		if ( ! empty( $params['order'] ) ) {
			$query->set( 'order', $params['order'] );
		}

		// Custom ordering
		$query = apply_filters( 'inventor_order_query', $query, $params );

		// Listing post type
		if ( ! empty( $params['listing_types'] ) ) {
			$query->set( 'post_type', $params['listing_types'] );
		}

		// Location
		if ( ! empty( $params['locations'] ) ) {
			$taxonomies[] = array(
				'taxonomy'  => 'locations',
				'field'     => 'slug',
				'terms'     => $params['locations'],
			);
		}

		// Category
		if ( ! empty( $params['listing_categories'] ) ) {
			$taxonomies[] = array(
				'taxonomy'  => 'listing_categories',
				'field'     => 'slug',
				'terms'     => $params['listing_categories'],
			);
		}

		// Term taxonomy
		if ( ! empty( $params['term-taxonomy'] ) ) {
			$taxonomy = $params['term-taxonomy'];
			$taxonomies[] = array(
				'taxonomy'  => $taxonomy,
				'field'     => 'slug',
				'terms'     => $params[ $taxonomy ],
			);
		}

		// Custom filter taxonomy
		$taxonomies = apply_filters( 'inventor_filter_query_taxonomies', $taxonomies, $params );

		// Title
		if ( ! empty( $params['title'] ) ) {
			$title_ids = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT ID FROM {$wpdb->posts} WHERE post_status = \"publish\" AND post_title LIKE '%s'", '%' . $params['title'] . '%' ) );
			$ids = self::build_post_ids( $ids, $title_ids );
		}

		// Description
		if ( ! empty( $params['description'] ) ) {
			$description_ids = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT ID FROM {$wpdb->posts} WHERE post_status = \"publish\" AND post_content LIKE '%s'", '%' . $params['description'] . '%' ) );
			$ids = self::build_post_ids( $ids, $description_ids );
		}

		// Keyword
		if ( ! empty( $params['keyword'] ) ) {
			$keyword_query = $wpdb->prepare( "SELECT DISTINCT ID FROM {$wpdb->posts} WHERE post_status = \"publish\" AND (post_content LIKE '%s' OR post_title LIKE '%s')", '%' . $params['keyword'] . '%',  '%' . $params['keyword'] . '%' );
			$keyword_query = apply_filters( 'inventor_filter_keyword_query', $keyword_query, $params['keyword'] );
			$keyword_ids = $wpdb->get_col( $keyword_query );
			$ids = self::build_post_ids( $ids, $keyword_ids );
		}

		// Find GPS coordinates if geolocation is set
		if ( ( empty( $params['latitude'] ) || empty( $params['longitude'] ) ) && ! empty( $params['geolocation'] ) ) {
			$geolocation = urlencode( $params['geolocation'] );
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
						$params['latitude'] = $location->lat;
						$params['longitude'] = $location->lng;

						// update $_GET params for map
						// TODO: move to inventor-google-map plugin
						if ( empty( $_GET['latitude'] ) ) {
							$_GET['latitude'] = $location->lat;
						}

						if ( empty( $_GET['longitude'] ) ) {
							$_GET['longitude'] = $location->lng;
						}
					}
				}
			}
		}

		// Geolocation
		if ( ! empty( $params['latitude'] ) && ! empty( $params['longitude'] ) ) {
			// Distance
			if ( empty( $params['distance'] ) ) {
				$params['distance'] = get_theme_mod( 'inventor_general_default_distance', 15 );
			}

			$distance_ids = array();
			$rows = self::filter_by_distance( $params['latitude'], $params['longitude'], $params['distance'] );

			foreach ( $rows as $row ) {
				$distance_ids[] = $row->ID;
			}

			$ids = self::build_post_ids( $ids, $distance_ids );

			// distance_ids have bigger order priority
			// TODO: check if user hasn't chosen different sorting
			if ( count( $distance_ids ) > 0 ) {
//				$query->set( 'orderby', 'post__in' );
			}
		}

		// Custom filtering
		$ids = apply_filters( 'inventor_filter_query_ids', $ids, $params );

		// Price
		$price_range = Inventor_Filter::get_field_range( 'price', $params );

		// Price from
		if ( ! empty( $price_range['from'] ) ) {
			$meta[] = array(
				'key'       =>  INVENTOR_LISTING_PREFIX . 'price',
				'value'     => $price_range['from'],
				'compare'   => '>=',
				'type'      => 'NUMERIC',
			);
		}

		// Price to
		if ( ! empty( $price_range['to'] ) ) {
			$meta[] = array(
				'key'       => INVENTOR_LISTING_PREFIX . 'price',
				'value'     => $price_range['to'],
				'compare'   => '<=',
				'type'      => 'NUMERIC',
			);
		}

		// Date
		$date_range = Inventor_Filter::get_field_range( 'date', $params );
		$filter_date_field = apply_filters( 'inventor_filter_query_field', INVENTOR_LISTING_PREFIX . 'date', 'date' );

		// Date from
		if ( ! empty( $date_range['from'] ) ) {
			$meta[] = array(
				'key'       =>  $filter_date_field,
				'value'     => strtotime( $date_range['from'] ),
				'compare'   => '>=',
				'type'      => 'NUMERIC',
			);
		}

		// Date to
		if ( ! empty( $date_range['to'] ) ) {
			$date_to_timestamp = strtotime( $date_range['to'] );
			$hour = intval( date( "G", $date_to_timestamp ) );
			$minute = intval( date( "i", $date_to_timestamp ) );
			$date_to_offset = $hour == 0 && $minute == 0 ? 60 * 60 * 24 - 1 : 0;

			$meta[] = array(
				'key'       => $filter_date_field,
				'value'     => $date_to_timestamp + $date_to_offset,
				'compare'   => '<=',
				'type'      => 'NUMERIC',
			);
		}

		// Featured
		if ( ! empty( $params['featured'] ) ) {
			$meta[] = array(
				'key'       => INVENTOR_LISTING_PREFIX . 'featured',
				'value'     => 'on',
			);
		}

		// Reduced
		if ( ! empty( $params['reduced'] ) ) {
			$meta[] = array(
				'key'       => INVENTOR_LISTING_PREFIX . 'reduced',
				'value'     => 'on',
			);
		}

		// Position
		if ( ! empty( $params['context'] ) && $params['context'] == 'map' ) {
			$meta[] = array(
				'key'       =>  INVENTOR_LISTING_PREFIX . 'map_location_latitude',
				'value'     => '',
				'compare'   => '!=',
			);

			$meta[] = array(
				'key'       =>  INVENTOR_LISTING_PREFIX . 'map_location_longitude',
				'value'     => '',
				'compare'   => '!=',
			);
		}

		// Parent
		if ( ! empty( $params['parent'] ) ) {
			$meta[] = array(
				'key'       => INVENTOR_LISTING_PREFIX . 'parent_listing',
				'value'     => $params['parent'],
			);
		}

		// Exclude sublistings
		$exclude_sublistings = get_theme_mod( 'inventor_general_exclude_sublistings', false );
		if ( $exclude_sublistings && empty( $params['parent'] ) ) {
			$meta[] = array(
				'key'       =>  INVENTOR_LISTING_PREFIX . 'parent_listing',
				'compare'   => 'NOT EXISTS',
			);
		}

		// Custom filter meta
		$meta = apply_filters( 'inventor_filter_query_meta', $meta, $params );

		// Post IDs
		if ( is_array( $ids ) ) {
			if ( count( $ids ) > 0 ) {
				if ( ! empty ( $params['count'] ) && count ( $params['count'] ) >= 0 ) {
					$ids = array_slice( $ids, 0, $params['count'] );
				}
				$query->set( 'post__in', $ids );
			} else {
				$query->set( 'post__in', array( 0 ) );
			}
		}

		$query->set( 'post_status', 'publish' );
		$query->set( 'meta_query', $meta );
		$query->set( 'tax_query', $taxonomies );

		$randomize_featured_listings = get_theme_mod( 'inventor_general_randomize_featured_listings', false );

		if ( ! self::has_filter() && ! self::is_sorted() && $featured_on_top && $randomize_featured_listings ) {
			$query = self::randomize_featured_listings( $query );
		}

		return apply_filters( 'inventor_filter_query', $query );
	}

	/**
	 * Helper method to build an array of post ids
	 *
	 * Purpose is to build proper array of post ids which will be used in WP_Query. For certain queries we need
	 * an array for post__in so we have to make array intersect, new array or just return null (post__in is not required).
	 *
	 * @access public
	 * @param object $query
	 * @return object
	 */
	public static function randomize_featured_listings( $query ) {
		$query_vars = $query->query_vars;

		$query_vars['posts_per_page'] = -1;
		$copy_query = new WP_Query( $query_vars );
		$posts = $copy_query->get_posts();

		if( count( $posts ) == 0 ) {
			return $query;
		}

		$query_ids = wp_list_pluck( $posts, 'ID' );

		$query_vars['meta_key'] = INVENTOR_LISTING_PREFIX . 'featured';
		$query_vars['meta_value'] = 'on';

		$featured_query = new WP_Query( $query_vars );
		$featured_posts = $featured_query->get_posts();
		$featured_ids = wp_list_pluck( $featured_posts, 'ID' );

		$current_featured_ids = array_intersect( $featured_ids, $query_ids );
		$current_non_featured_ids = array_diff( $query_ids, $current_featured_ids );
		shuffle( $current_featured_ids );
		$customized_ids = array_merge( $current_featured_ids, $current_non_featured_ids );

		$query->set( 'post__in', $customized_ids );
		$query->set( 'orderby', 'post__in' );
		return $query;
	}

	/**
	 * Helper method to build an array of post ids
	 *
	 * Purpose is to build proper array of post ids which will be used in WP_Query. For certain queries we need
	 * an array for post__in so we have to make array intersect, new array or just return null (post__in is not required).
	 *
	 * @access public
	 * @param null|array $haystack
	 * @param array $ids
	 * @return null|array
	 */
	public static function build_post_ids( $haystack, array $ids ) {
		if ( ! is_array( $haystack ) ) {
			$haystack = array();
		}

		if ( is_array( $haystack ) && count( $haystack ) > 0 ) {
			return array_intersect( $haystack, $ids );
		} else {
			$haystack = $ids;
		}

		return $haystack;
	}

	/**
	 * Gets array of post types for SQL
	 *
	 * @access public
	 * @throws Exception
	 * @return string
	 */
	public static function build_post_types_array_for_sql() {
		$post_types = Inventor_Post_Types::get_listing_post_types();

		if ( ! is_array( $post_types ) ) {
			throw new Exception( 'No listing post types found.' );
		}

		$string = implode( '","', $post_types );
		return sprintf( '("%s")', $string );
	}

	/**
	 * Find listings by GPS position matching the distance
	 *
	 * @access public
	 * @param $latitude
	 * @param $longitude
	 * @param $distance
	 *
	 * @return mixed
	 */
	public static function filter_by_distance( $latitude, $longitude, $distance ) {
		global $wpdb;

		$radius_km = 6371;
		$radius_mi = 3959;
		$radius = $radius_mi;

		if ( 'km' == get_theme_mod( 'inventor_measurement_distance_unit_long', 'mi' ) ) {
			$radius = $radius_km;
		}

		$sql = 'SELECT SQL_CALC_FOUND_ROWS ID, ( ' . $radius . ' * acos( cos( radians(' . $latitude . ') ) * cos(radians( latitude.meta_value ) ) * cos( radians( longitude.meta_value ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( latitude.meta_value ) ) ) ) AS distance
    				FROM ' . $wpdb->prefix . 'posts
                    INNER JOIN ' . $wpdb->prefix . 'postmeta ON (' . $wpdb->prefix . 'posts.ID = ' . $wpdb->prefix . 'postmeta.post_id)
                    INNER JOIN ' . $wpdb->prefix . 'postmeta AS latitude ON ' . $wpdb->prefix . 'posts.ID = latitude.post_id
                    INNER JOIN ' . $wpdb->prefix . 'postmeta AS longitude ON ' . $wpdb->prefix . 'posts.ID = longitude.post_id
                    WHERE ' . $wpdb->prefix . 'posts.post_type IN ' . self::build_post_types_array_for_sql() . '
                        AND ' . $wpdb->prefix . 'posts.post_status = "publish"
                        AND latitude.meta_key="' . INVENTOR_LISTING_PREFIX . 'map_location_latitude"
                        AND longitude.meta_key="' . INVENTOR_LISTING_PREFIX . 'map_location_longitude"
					GROUP BY ' . $wpdb->prefix . 'posts.ID HAVING distance <= ' . $distance . ' ORDER BY distance ASC;';

		return $wpdb->get_results( $sql );
	}
}

Inventor_Filter::init();