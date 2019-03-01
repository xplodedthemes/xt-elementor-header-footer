<?php
/**
 * GeneratepressCompatibility.
 *
 * @package  xt-elementor-header-footer
 */

/**
 * XT_EHF_GeneratePress_Compat setup
 *
 * @since 1.0
 */
class XT_EHF_GeneratePress_Compat {

	/**
	 * Instance of XT_EHF_GeneratePress_Compat
	 *
	 * @var XT_EHF_GeneratePress_Compat
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new XT_EHF_GeneratePress_Compat();

			add_action( 'wp', array( self::$instance, 'hooks' ) );
		}

		return self::$instance;
	}

	/**
	 * Run all the Actions / Filters.
	 */
	public function hooks() {

		if ( xt_ehf_header_enabled() ) {
			add_action( 'template_redirect', array( $this, 'generatepress_setup_header' ) );
			add_action( 'generate_header', 'xt_ehf_render_header' );
		}

		if ( xt_ehf_footer_enabled() ) {
			add_action( 'template_redirect', array( $this, 'generatepress_setup_footer' ) );
			add_action( 'generate_footer', 'xt_ehf_render_footer' );
		}

	}

	/**
	 * Disable header from the theme.
	 */
	public function generatepress_setup_header() {
		remove_action( 'generate_header', 'generate_construct_header' );
	}

	/**
	 * Disable footer from the theme.
	 */
	public function generatepress_setup_footer() {
		remove_action( 'generate_footer', 'generate_construct_footer_widgets', 5 );
		remove_action( 'generate_footer', 'generate_construct_footer' );
	}

}

XT_EHF_GeneratePress_Compat::instance();
