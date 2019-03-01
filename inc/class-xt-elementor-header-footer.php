<?php
/**
 * Entry point for the plugin. Checks if Elementor is installed and activated and loads it's own files and actions.
 *
 * @package  xt-elementor-header-footer
 */

/**
 * Class XT_Elementor_Header_Footer
 */
class XT_Elementor_Header_Footer {

	/**
	 * Current theme template
	 *
	 * @var String
	 */
	public $template;

	/**
	 * Instance of Elemenntor Frontend class.
	 *
	 * @var \Elementor\Frontend()
	 */
	private static $elementor_instance;
	/**
	 * Constructor
	 */
	function __construct() {

		$this->template = get_template();

		if ( defined( 'ELEMENTOR_VERSION' ) && is_callable( 'Elementor\Plugin::instance' ) ) {

			self::$elementor_instance = Elementor\Plugin::instance();

			$this->includes();
			$this->load_textdomain();

			if ( 'genesis' == $this->template ) {

				require XT_EHF_DIR . 'themes/genesis/class-xt-ehf-genesis-compat.php';
				
			} elseif ( 'brava' == $this->template ) {

				require XT_EHF_DIR . 'themes/brava/class-xt-ehf-brava-compat.php';
				
			} elseif ( 'astra' == $this->template ) {

				require XT_EHF_DIR . 'themes/astra/class-xt-ehf-astra-compat.php';
				
			} elseif ( 'bb-theme' == $this->template || 'beaver-builder-theme' == $this->template ) {
				
				$this->template = 'beaver-builder-theme';
				require XT_EHF_DIR . 'themes/bb-theme/class-xt-ehf-bb-theme-compat.php';
				
			} elseif ( 'generatepress' == $this->template ) {

				require XT_EHF_DIR . 'themes/generatepress/class-xt-ehf-generatepress-compat.php';
				
			} elseif ( 'oceanwp' == $this->template ) {

				require XT_EHF_DIR . 'themes/oceanwp/class-xt-ehf-oceanwp-compat.php';
				
			} else {
				add_action( 'init', array( $this, 'setup_unsupported_theme_notice' ) );
			}

			// Scripts and styles.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

			add_filter( 'body_class', array( $this, 'body_class' ) );
			add_action( 'switch_theme', array( $this, 'reset_unsupported_theme_notice' ) );

			add_shortcode( 'xt_ehf_template', array( $this, 'render_template' ) );

		} else {

			add_action( 'admin_notices', array( $this, 'elementor_not_available' ) );
			add_action( 'network_admin_notices', array( $this, 'elementor_not_available' ) );
		}

	}

	/**
	 * Reset the Unsupported theme nnotice after a theme is switched.
	 *
	 * @since 1.0.16
	 *
	 * @return void
	 */
	public function reset_unsupported_theme_notice() {
		delete_user_meta( get_current_user_id(), 'xt-ehf-sites-notices-id-unsupported-theme' );
	}

	/**
	 * Prints the admin notics when Elementor is not installed or activated.
	 */
	public function elementor_not_available() {

		if ( file_exists( WP_PLUGIN_DIR . '/elementor/elementor.php' ) ) {
			$url = network_admin_url() . 'plugins.php?s=elementor';
		} else {
			$url = network_admin_url() . 'plugin-install.php?s=elementor';
		}

		echo '<div class="notice notice-error">';
		/* Translators: URL to install or activate Elementor plugin. */
		echo '<p>' . sprintf( esc_html__( 'The <strong>XT Elementor Header Footer</strong> plugin requires <strong><a href="%s">Elementor</strong></a> plugin installed & activated.', 'xt-elementor-header-footer' ) . '</p>', $url );
		echo '</div>';
	}

	/**
	 * Loads the globally required files for the plugin.
	 */
	public function includes() {
		require_once XT_EHF_DIR . 'admin/class-xt-ehf-admin.php';

		require_once XT_EHF_DIR . 'inc/xt-ehf-functions.php';

		// Load Elementor Canvas Compatibility.
		require_once XT_EHF_DIR . 'inc/class-xt-ehf-canvas-compat.php';

		// Load WPML Compatibility if WPML is installed and activated.
		if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			require_once XT_EHF_DIR . 'inc/compatibility/class-xt-ehf-wpml-compatibility.php';
		}

		// Load the Admin Notice Class.
		require_once XT_EHF_DIR . 'inc/class-xt-ehf-notices.php';
	}

	/**
	 * Loads textdomain for the plugin.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'xt-elementor-header-footer' );
	}

	/**
	 * Enqueue styles and scripts.
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'xt-ehf-style', XT_EHF_URL . 'assets/css/xt-elementor-header-footer.css', array(), XT_EHF_VER );

		if ( class_exists( '\Elementor\Plugin' ) ) {
			$elementor = \Elementor\Plugin::instance();
			$elementor->frontend->enqueue_styles();
		}

		if ( class_exists( '\ElementorPro\Plugin' ) ) {
			$elementor_pro = \ElementorPro\Plugin::instance();
			$elementor_pro->enqueue_styles();
		}

		if ( xt_ehf_header_enabled() ) {
			if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
				$css_file = new \Elementor\Core\Files\CSS\Post( get_xt_ehf_header_id() );
			} elseif ( class_exists( '\Elementor\Post_CSS_File' ) ) {
				$css_file = new \Elementor\Post_CSS_File( get_xt_ehf_header_id() );
			}

			$css_file->enqueue();
		}

		if ( xt_ehf_footer_enabled() ) {
			if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
				$css_file = new \Elementor\Core\Files\CSS\Post( get_xt_ehf_footer_id() );
			} elseif ( class_exists( '\Elementor\Post_CSS_File' ) ) {
				$css_file = new \Elementor\Post_CSS_File( get_xt_ehf_footer_id() );
			}

			$css_file->enqueue();
		}
	}

	/**
	 * Load admin styles on header footer elementor edit screen.
	 */
	public function enqueue_admin_scripts() {
		global $pagenow;
		$screen = get_current_screen();

		if ( ( 'xt-ehf' == $screen->id && ( 'post.php' == $pagenow || 'post-new.php' == $pagenow ) ) || ( 'edit.php' == $pagenow && 'edit-xt-ehf' == $screen->id ) ) {
			wp_enqueue_style( 'xt-ehf-admin-style', XT_EHF_URL . 'admin/assets/css/ehf-admin.css', array(), XT_EHF_VER );

			wp_enqueue_script( 'xt-ehf-admin-script', XT_EHF_URL . 'admin/assets/js/ehf-admin.js', array(), XT_EHF_VER );
		}
	}

	/**
	 * Adds classes to the body tag conditionally.
	 *
	 * @param  Array $classes array with class names for the body tag.
	 *
	 * @return Array          array with class names for the body tag.
	 */
	public function body_class( $classes ) {

		if ( xt_ehf_header_enabled() ) {
			$classes[] = 'xt-ehf-header';
		}

		if ( xt_ehf_footer_enabled() ) {
			$classes[] = 'xt-ehf-footer';
		}

		$classes[] = 'xt-ehf-template-' . $this->template;
		$classes[] = 'xt-ehf-stylesheet-' . get_stylesheet();

		return $classes;
	}

	/**
	 * Display Unsupported theme notice if the current theme does add support for 'xt-elementor-header-footer'
	 *
	 * @since  1.0.3
	 */
	public function setup_unsupported_theme_notice() {

		if ( ! current_theme_supports( 'xt-elementor-header-footer' ) ) {
			XT_EHF_Notices::add_notice(
				array(
					'id'          => 'unsupported-theme',
					'type'        => 'error',
					'dismissible' => true,
					'message'     => esc_html__( 'Hey, your current theme is not supported by XT Elementor Header Footer, click <a href="https://github.com/xplodedthemes/xt-elementor-header-footer#which-themes-are-supported-by-this-plugin">here</a> to check out the supported themes.', 'xt-elementor-header-footer' ),
				)
			);
		}

	}

	/**
	 * Prints the Header content.
	 */
	public static function get_header_content() {
		echo self::$elementor_instance->frontend->get_builder_content_for_display( get_xt_ehf_header_id() );
	}

	/**
	 * Prints the Footer content.
	 */
	public static function get_footer_content() {

		echo "<div class='footer-width-fixer'>";
		echo self::$elementor_instance->frontend->get_builder_content_for_display( get_xt_ehf_footer_id() );
		echo '</div>';
	}

	/**
	 * Get option for the plugin settings
	 *
	 * @param  mixed $setting Option name.
	 * @param  mixed $default Default value to be received if the option value is not stored in the option.
	 *
	 * @return mixed.
	 */
	public static function get_settings( $setting = '', $default = '' ) {
		if ( 'type_header' == $setting || 'type_footer' == $setting || 'type_before_footer' == $setting ) {
			$templates = self::get_template_id( $setting );

			$template = is_array( $templates ) ? $templates[0] : '';
			$template = apply_filters( "xt_ehf_get_settings_{$setting}", $template );

			return $template;
		}
	}

	/**
	 * Get header or footer template id based on the meta query.
	 *
	 * @param  String $type Type of the template header/footer.
	 *
	 * @return Mixed       Returns the header or footer template id if found, else returns string ''.
	 */
	public static function get_template_id( $type ) {

		$cached = wp_cache_get( $type );

		if ( false !== $cached ) {
			return $cached;
		}

		$args = array(
			'post_type'    => 'xt-ehf',
			'meta_key'     => 'xt_ehf_template_type',
			'meta_value'   => $type,
			'meta_type'    => 'post',
			'meta_compare' => '>=',
			'orderby'      => 'meta_value',
			'order'        => 'ASC',
			'meta_query'   => array(
				'relation' => 'OR',
				array(
					'key'     => 'xt_ehf_template_type',
					'value'   => $type,
					'compare' => '==',
					'type'    => 'post',
				),
			),
		);

		$args = apply_filters( 'xt_ehf_get_template_id_args', $args );

		$template = new WP_Query(
			$args
		);

		if ( $template->have_posts() ) {
			$posts = wp_list_pluck( $template->posts, 'ID' );
			wp_cache_set( $type, $posts );

			return $posts;
		}

		return '';
	}

	/**
	 * Callback to shortcode.
	 *
	 * @param array $atts attributes for shortcode.
	 */
	public function render_template( $atts ) {

		$atts = shortcode_atts(
			array(
				'id' => '',
			),
			$atts,
			'xt_ehf_template'
		);

		$id = ! empty( $atts['id'] ) ? intval( $atts['id'] ) : '';

		if ( empty( $id ) ) {
			return '';
		}

		if ( class_exists( '\Elementor\Post_CSS_File' ) ) {

			// Load elementor styles.
			$css_file = new \Elementor\Post_CSS_File( $id );
			$css_file->enqueue();
		}

		return self::$elementor_instance->frontend->get_builder_content_for_display( $id );

	}

}
