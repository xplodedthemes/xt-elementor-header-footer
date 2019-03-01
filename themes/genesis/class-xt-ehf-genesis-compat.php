<?php
/**
 * Genesis_Compat setup
 *
 * @package xt-elementor-header-footer
 */

/**
 * Genesis theme compatibility.
 */
class XT_EHF_Genesis_Compat {

	/**
	 * Instance of XT_EHF_Genesis_Compat.
	 *
	 * @var XT_EHF_Genesis_Compat
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new XT_EHF_Genesis_Compat();

			add_action( 'wp', array( self::$instance, 'hooks' ) );
		}

		return self::$instance;
	}

	/**
	 * Run all the Actions / Filters.
	 */
	public function hooks() {

		if ( xt_ehf_header_enabled() ) {
			add_action( 'template_redirect', array( $this, 'genesis_setup_header' ) );
			add_action( 'genesis_header', array( $this, 'genesis_header_markup_open' ), 16 );
			add_action( 'genesis_header', array( $this, 'genesis_header_markup_close' ), 25 );
			add_action( 'genesis_header', array( 'XT_Elementor_Header_Footer', 'get_header_content' ), 16 );
		}

		if ( xt_ehf_footer_enabled() ) {
			add_action( 'template_redirect', array( $this, 'genesis_setup_footer' ) );
			add_action( 'genesis_footer', array( $this, 'genesis_footer_markup_open' ), 16 );
			add_action( 'genesis_footer', array( $this, 'genesis_footer_markup_close' ), 25 );
			add_action( 'genesis_footer', array( 'XT_Elementor_Header_Footer', 'get_footer_content' ), 16 );
		}

	}

	/**
	 * Disable header from the theme.
	 */
	public function genesis_setup_header() {

		for ( $priority = 0; $priority < 16; $priority ++ ) {
			remove_all_actions( 'genesis_header', $priority );
		}

	}

	/**
	 * Disable footer from the theme.
	 */
	public function genesis_setup_footer() {

		for ( $priority = 0; $priority < 16; $priority ++ ) {
			remove_all_actions( 'genesis_footer', $priority );
		}

	}

	/**
	 * Open markup for header.
	 */
	public function genesis_header_markup_open() {

		genesis_markup(
			array(
				'html5'   => '<header %s>',
				'xhtml'   => '<div id="header">',
				'context' => 'site-header',
			)
		);

		genesis_structural_wrap( 'header' );

	}

	/**
	 * Close MArkup for header.
	 */
	public function genesis_header_markup_close() {

		genesis_structural_wrap( 'header', 'close' );
		genesis_markup(
			array(
				'html5' => '</header>',
				'xhtml' => '</div>',
			)
		);

	}

	/**
	 * Open markup for footer.
	 */
	public function genesis_footer_markup_open() {

		genesis_markup(
			array(
				'html5'   => '<footer %s>',
				'xhtml'   => '<div id="footer" class="footer">',
				'context' => 'site-footer',
			)
		);
		genesis_structural_wrap( 'footer', 'open' );

	}

	/**
	 * Close markup for footer.
	 */
	public function genesis_footer_markup_close() {

		genesis_structural_wrap( 'footer', 'close' );
		genesis_markup(
			array(
				'html5' => '</footer>',
				'xhtml' => '</div>',
			)
		);

	}


}

XT_EHF_Genesis_Compat::instance();
