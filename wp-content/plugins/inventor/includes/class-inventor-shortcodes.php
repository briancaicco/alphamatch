<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Inventor_Shortcodes
 *
 * @class Inventor_Shortcodes
 * @package Inventor/Classes
 * @author Pragmatic Mates
 */
class Inventor_Shortcodes {
	/**
	 * Initialize shortcodes
	 *
	 * @access public
	 * @return void
	 */
	public static function init() {
		add_action( 'wp', array( __CLASS__, 'check_logout' ) );

		add_shortcode( 'inventor_breadcrumb', array( __CLASS__, 'breadcrumb' ) );
		add_shortcode( 'inventor_logout', array( __CLASS__, 'logout' ) );
		add_shortcode( 'inventor_login', array( __CLASS__, 'login' ) );
		add_shortcode( 'inventor_reset_password', array( __CLASS__, 'reset_password' ) );
		add_shortcode( 'inventor_register', array( __CLASS__, 'register' ) );
		add_shortcode( 'inventor_password', array( __CLASS__, 'password' ) );
		add_shortcode( 'inventor_payment', array( __CLASS__, 'payment' ) );
		add_shortcode( 'inventor_profile', array( __CLASS__, 'profile' ) );
		add_shortcode( 'inventor_public_profile_link', array( __CLASS__, 'public_profile_link' ) );
		add_shortcode( 'inventor_transactions', array( __CLASS__, 'transactions' ) );
		add_shortcode( 'inventor_report_form', array( __CLASS__, 'report_form' ) );
		add_shortcode( 'inventor_users', array( __CLASS__, 'users' ) );
		add_shortcode( 'inventor_widget_listings', array( __CLASS__, 'widget_listings' ) );

		add_action( 'wp', array( __CLASS__, 'check_payment_page_data' ) );
	}

	/**
	 * Logout checker
	 *
	 * @access public
	 * @param $wp
	 * @return void
	 */
	public static function check_logout( $wp ) {
		if ( is_admin() ) {
			return;
		}

		$post = get_post();

		if ( is_object( $post ) ) {
			if ( strpos( $post->post_content, '[inventor_logout]' ) !== false ) {
				Inventor_Utilities::show_message( 'success', __( 'You have been successfully logged out.', 'inventor' ) );
				wp_redirect( html_entity_decode( wp_logout_url( home_url( '/' ) ) ) );
				exit();
			}
		}
	}

	/**
	 * Checks if user is on a payment page without payment data
	 *
	 * @access public
	 * @return void
	 */
	public static function check_payment_page_data() {
		if ( is_admin() ) {
			return;
		}

		if ( ! empty( $_POST['payment_type'] ) ) {
			return;
		}

		$payment_page = get_theme_mod( 'inventor_general_payment_page' );

		if ( ! $payment_page || ! is_page( $payment_page ) ) {
			return;
		}

		if ( ! empty( $_POST ) ) {
			return;
		}

		$pricing_page = get_post_type_archive_link( 'pricing_table' );

		if ( $pricing_page ) {
			wp_redirect( $pricing_page );
			exit();
		}
	}

	/**
	 * Breadcrumb
	 *
	 * @access public
	 * @param $atts|array
	 * @return string
	 */
	public static function breadcrumb( $atts = array() ) {
		return Inventor_Template_Loader::load( 'misc/breadcrumb' );
	}

	/**
	 * Logout
	 *
	 * @access public
	 * @param $atts|array
	 * @return void
	 */
	public static function logout( $atts = array() ) {}

	/**
	 * Login
	 *
	 * @access public
	 * @param $atts|array
	 * @return string
	 */
	public static function login( $atts = array() ) {
		return Inventor_Template_Loader::load( 'accounts/login' );
	}

	/**
	 * Reset
	 *
	 * @access public
	 * @param $atts|array
	 * @return string
	 */
	public static function reset_password( $atts = array() ) {
		remove_filter( 'lostpassword_url', array( 'Inventor_Post_Type_User', 'custom_lost_password_page' ) );
		$form = Inventor_Template_Loader::load( 'accounts/reset' );
		add_filter( 'lostpassword_url', array( 'Inventor_Post_Type_User', 'custom_lost_password_page' ), 10, 2 );
		return $form;
	}

	/**
	 * Register
	 *
	 * @access public
	 * @param $atts|array
	 * @return string
	 */
	public static function register( $atts = array() ) {
		return Inventor_Template_Loader::load( 'accounts/register' );
	}

	/**
	 * Payment page
	 *
	 * @access public
	 * @param $atts
	 * @return string
	 */
	public static function payment( $atts = array() ) {
		if ( ! is_user_logged_in() ) {
			return Inventor_Template_Loader::load( 'misc/not-allowed' );
		}

		return Inventor_Template_Loader::load( 'payment/payment-form' );
	}

	/**
	 * Transactions
	 *
	 * @access public
	 * @param $atts
	 * @return string
	 */
	public static function transactions( $atts = array() ) {
		if ( ! is_user_logged_in() ) {
			return Inventor_Template_Loader::load( 'misc/not-allowed' );
		}

		return Inventor_Template_Loader::load( 'payment/transactions' );
	}

	/**
	 * Report form
	 *
	 * @access public
	 * @param $atts
	 * @return void
	 */
	public static function report_form( $atts = array() ) {
		$atts = array(
			'listing' => Inventor_Post_Types::get_listing( $_GET['id'] )
		);

		echo Inventor_Template_Loader::load( 'misc/report-form', $atts );
	}

	/**
	 * Users/Authors
	 *
	 * @access public
	 * @param $atts|array
	 * @return string
	 */
	public static function users( $atts = array() ) {
		// pagination is enabled by default, check if it is disabled explicitly
		$pagination = empty( $atts['pagination'] ) ? 'on' : $atts['pagination'];

		if ( $pagination == 'off' ) {
			$count = -1;
			$page = 1;
		} else {
			$count = get_option( 'posts_per_page' );
			$page = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		}

		$params = array(
			'users' => Inventor_Post_Type_User::get_users( 'author', $count, 'posts_count', array(), $page ),
			'count'	=> $count,
			'page'	=> $page,
		);

		return Inventor_Template_Loader::load( 'users/user-list', $params );
	}

	/**
	 * Change password
	 *
	 * @access public
	 * @param $atts
	 * @return string
	 */
	public static function password( $atts = array() ) {
		if ( ! is_user_logged_in() ) {
			return Inventor_Template_Loader::load( 'misc/not-allowed' );
		}

		return Inventor_Template_Loader::load( 'accounts/password' );
	}

	/**
	 * Change profile
	 *
	 * @access public
	 * @param $atts
	 * @return string
	 */
	public static function profile( $atts = array() ) {
		if ( ! is_user_logged_in() ) {
			return Inventor_Template_Loader::load( 'misc/not-allowed' );
		}

		$form = cmb2_get_metabox_form( INVENTOR_USER_PREFIX  . 'profile', get_current_user_id(), array(
			'form_format' => '<form action="//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="object_id" value="%2$s">%3$s<input type="submit" name="submit-profile" value="%4$s" class="button"></form>',
			'save_button' => __( 'Save profile', 'inventor' ),
		) );

		return Inventor_Template_Loader::load( 'accounts/profile', array(
			'form' => $form,
		) );
	}

	/**
	 * Show public profile page button
	 *
	 * @access public
	 * @param $atts
	 * @return string
	 */
	public static function public_profile_link( $atts = array() ) {
		if ( ! is_user_logged_in() ) {
			return '';
		}

		$user_id = get_current_user_id();

		$url = get_author_posts_url( $user_id );

		// remove http(s):// prefix (so it can be used in the navigation)
		$url = str_replace( array( 'http:', 'https:' ), array( '', '' ), $url );

		if ( is_array( $atts ) && array_key_exists( 'label', $atts ) ) {
			$label = $atts['label'];
			return '<a href="'. $url .'">'. $label .'</a>';
		} else {
			return $url;
		}
	}

	/**
	 * Widget: Listings
	 *
	 * @access public
	 * @param $atts
	 * @return string
	 */
	public static function widget_listings( $atts = array() ) {
		if ( ! empty( $atts['listing_categories'] ) ) {
			$atts['listing_categories'] = explode( ',', $atts['listing_categories'] );
		}

		$instance = array();
		$instance['geolocation'] = null;
		$instance['display'] = empty( $instance['display'] ) ? 'row' : $instance['per_row'];
		$instance['per_row'] = empty( $instance['per_row'] ) ? 1 : $instance['per_row'];
		$instance['count'] = empty( $instance['count'] ) ? get_option( 'posts_per_page' ) : $instance['count'];

		$instance = array_merge( $instance, $atts );

		$instance['paged'] = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$instance['hide_pagination'] = false;

		$widget = new Inventor_Widget_Listings();

		ob_start();
		$widget->widget( array(), $instance );
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
}

Inventor_Shortcodes::init();
