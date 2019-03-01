<?php
/**
 * WPML Compatibility for XT Elementor Header Footer.
 *
 * @package     XT_EHF
 * @author      XT_EHF
 * @copyright   Copyright (c) 2018, XT_EHF
 * @since       XT_EHF 1.0.9
 */

defined( 'ABSPATH' ) or exit;

/**
 * Set up WPML Compatibiblity Class.
 */
class XT_EHF_WPML_Compatibility {

	/**
	 * Instance of XT_EHF_WPML_Compatibility.
	 *
	 * @since  1.0.9
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Get instance of XT_EHF_WPML_Compatibility
	 *
	 * @since  1.0.9
	 * @return XT_EHF_WPML_Compatibility
	 */
	public static function instance() {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Setup actions and filters.
	 *
	 * @since  1.0.9
	 */
	private function __construct() {
		add_filter( 'xt_ehf_get_settings_type_header', array( $this, 'get_wpml_object' ) );
		add_filter( 'xt_ehf_get_settings_type_footer', array( $this, 'get_wpml_object' ) );
	}

	/**
	 * Pass the final header and footer ID from the WPML's object filter to allow strings to be translated.
	 *
	 * @since  1.0.9
	 * @param  Int $id  Post ID of the template being rendered.
	 * @return Int $id  Post ID of the template being rendered, Passed through the `wpml_object_id` id.
	 */
	public function get_wpml_object( $id ) {
		$id = apply_filters( 'wpml_object_id', $id );

		if ( null === $id ) {
			$id = '';
		}

		return $id;
	}

}

/**
 * Initiate the class.
 */
XT_EHF_WPML_Compatibility::instance();
