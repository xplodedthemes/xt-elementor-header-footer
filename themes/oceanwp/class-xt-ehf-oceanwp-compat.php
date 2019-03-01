<?php
/**
 * XT_EHF_OceanWP_Compat setup
 *
 * @package xt-elementor-header-footer
 */

/**
 * OceanWP theme compatibility.
 */
class XT_EHF_OceanWP_Compat {

	/**
	 * Instance of XT_EHF_OceanWP_Compat.
	 *
	 * @var XT_EHF_OceanWP_Compat
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new XT_EHF_OceanWP_Compat();

			add_action( 'wp', array( self::$instance, 'hooks' ) );
		}

		return self::$instance;
	}

	/**
	 * Run all the Actions / Filters.
	 */
	public function hooks() {

		if ( xt_ehf_header_enabled() ) {
			add_action( 'template_redirect', array( $this, 'setup_header' ), 10 );
			add_action( 'ocean_header', 'xt_ehf_render_header' );
		}

		if ( xt_ehf_footer_enabled() ) {
			add_action( 'template_redirect', array( $this, 'setup_footer' ), 10 );
			add_action( 'ocean_footer', 'xt_ehf_render_footer' );
		}

	}

	/**
	 * Disable header from the theme.
	 */
	public function setup_header() {
		remove_action( 'ocean_top_bar', 'oceanwp_top_bar_template' );
		remove_action( 'ocean_header', 'oceanwp_header_template' );
		remove_action( 'ocean_page_header', 'oceanwp_page_header_template' );
	}

	/**
	 * Disable footer from the theme.
	 */
	public function setup_footer() {
		remove_action( 'ocean_footer', 'oceanwp_footer_template' );
	}

}

XT_EHF_OceanWP_Compat::instance();
