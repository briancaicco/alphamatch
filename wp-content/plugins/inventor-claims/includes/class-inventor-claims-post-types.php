<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Claims_Post_Types
 *
 * @class Inventor_Claims_Post_Types
 * @package Inventor/Classes/Post_Types
 * @author Pragmatic Mates
 */
class Inventor_Claims_Post_Types {
    /**
     * Initialize listing types
     *
     * @access public
     * @return void
     */
    public static function init() {
        self::includes();
    }

    /**
     * Loads listing types
     *
     * @access public
     * @return void
     */
    public static function includes() {
        require_once INVENTOR_CLAIMS_DIR . 'includes/post-types/class-inventor-claims-post-type-claim.php';
    }
}

Inventor_Claims_Post_Types::init();