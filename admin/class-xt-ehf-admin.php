<?php
/**
 * Entry point for the plugin. Checks if Elementor is installed and activated and loads it's own files and actions.
 *
 * @package  xt-elementor-header-footer
 */

defined( 'ABSPATH' ) or exit;

/**
 * XT_EHF_Admin setup
 *
 * @since 1.0
 */
class XT_EHF_Admin {

	/**
	 * Instance of XT_EHF_Admin
	 *
	 * @var XT_EHF_Admin
	 */
	private static $_instance = null;

	/**
	 * Instance of XT_EHF_Admin
	 *
	 * @return XT_EHF_Admin Instance of XT_EHF_Admin
	 */
	public static function instance() {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'header_footer_posttype' ) );
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 50 );
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
		add_action( 'save_post', array( $this, 'save_meta' ) );
		add_action( 'admin_notices', array( $this, 'location_notice' ) );
		add_action( 'template_redirect', array( $this, 'block_template_frontend' ) );
		add_filter( 'single_template', array( $this, 'load_canvas_template' ) );

		add_filter( 'manage_xt-ehf_posts_columns', array( $this, 'set_shortcode_columns' ) );

		add_action( 'manage_xt-ehf_posts_custom_column', array( $this, 'render_shortcode_column' ), 10, 2 );

	}

	/**
	 * Register Post type for header footer templates
	 */
	public function header_footer_posttype() {

		$labels = array(
			'name'               => esc_html__( 'Elementor Headers / Footers', 'xt-elementor-header-footer' ),
			'singular_name'      => esc_html__( 'Elementor Headers / Footers', 'xt-elementor-header-footer' ),
			'menu_name'          => esc_html__( 'Elementor Headers / Footers', 'xt-elementor-header-footer' ),
			'name_admin_bar'     => esc_html__( 'Elementor Headers / Footers', 'xt-elementor-header-footer' ),
			'add_new'            => esc_html__( 'Add New', 'xt-elementor-header-footer' ),
			'add_new_item'       => esc_html__( 'Add New Template', 'xt-elementor-header-footer' ),
			'new_item'           => esc_html__( 'New Template', 'xt-elementor-header-footer' ),
			'edit_item'          => esc_html__( 'Edit Template', 'xt-elementor-header-footer' ),
			'view_item'          => esc_html__( 'View Template', 'xt-elementor-header-footer' ),
			'all_items'          => esc_html__( 'All Templates', 'xt-elementor-header-footer' ),
			'search_items'       => esc_html__( 'Search Templates', 'xt-elementor-header-footer' ),
			'parent_item_colon'  => esc_html__( 'Parent Templates:', 'xt-elementor-header-footer' ),
			'not_found'          => esc_html__( 'No Templates found.', 'xt-elementor-header-footer' ),
			'not_found_in_trash' => esc_html__( 'No Templates found in Trash.', 'xt-elementor-header-footer' ),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'rewrite'             => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'exclude_from_search' => true,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'menu_icon'           => 'dashicons-editor-kitchensink',
			'supports'            => array( 'title', 'thumbnail', 'elementor' ),
		);

		register_post_type( 'xt-ehf', $args );
	}

	/**
	 * Register the admin menu for Header Footer builder.
	 *
	 * @since  1.0.0
	 * @since  1.0.1
	 *         Moved the menu under Appearance -> Header Footer Builder
	 */
	public function register_admin_menu() {
		add_submenu_page(
			'themes.php',
			__( 'Headers / Footers', 'xt-elementor-header-footer' ),
			__( 'Headers / Footers', 'xt-elementor-header-footer' ),
			'edit_pages',
			'edit.php?post_type=xt-ehf'
		);
	}

	/**
	 * Register meta box(es).
	 */
	function register_metabox() {
		add_meta_box(
			'xt-ehf-meta-box',
			__( 'Elementor Headers / Footers options', 'xt-elementor-header-footer' ),
			array(
				$this,
				'metabox_render',
			),
			'xt-ehf',
			'normal',
			'high'
		);
	}

	/**
	 * Render Meta field.
	 *
	 * @param  POST $post Currennt post object which is being displayed.
	 */
	function metabox_render( $post ) {
		$values            = get_post_custom( $post->ID );
		$template_type     = isset( $values['xt_ehf_template_type'] ) ? esc_attr( $values['xt_ehf_template_type'][0] ) : '';
		$display_on_canvas = isset( $values['display-on-canvas-template'] ) ? true : false;

		// We'll use this nonce field later on when saving.
		wp_nonce_field( 'xt_ehf_meta_nounce', 'xt_ehf_meta_nounce' );
		?>
		<table class="xt-ehf-options-table widefat">
			<tbody>
				<tr class="xt-ehf-options-row">
					<td class="xt-ehf-options-row-heading">
						<label for="ehf_template_type"><?php _e( 'Type of Template', 'xt-elementor-header-footer' ); ?></label>
					</td>
					<td class="xt-ehf-options-row-content">
						<select name="ehf_template_type" id="ehf_template_type">
							<option value="" <?php selected( $template_type, '' ); ?>><?php _e( 'Select Option', 'xt-elementor-header-footer' ); ?></option>
							<option value="type_header" <?php selected( $template_type, 'type_header' ); ?>><?php _e( 'Header', 'xt-elementor-header-footer' ); ?></option>
							<?php if ( 'astra' == get_template() ) { ?>
								<option value="type_before_footer" <?php selected( $template_type, 'type_before_footer' ); ?>><?php _e( 'Before Footer', 'xt-elementor-header-footer' ); ?></option>
							<?php } ?>
							<?php if ( 'brava' == get_template() ) { ?>
								<option value="type_before_footer" <?php selected( $template_type, 'type_before_footer' ); ?>><?php _e( 'Before Footer', 'xt-elementor-header-footer' ); ?></option>
							<?php } ?>
							<option value="type_footer" <?php selected( $template_type, 'type_footer' ); ?>><?php _e( 'Footer', 'xt-elementor-header-footer' ); ?></option>
							<option value="custom" <?php selected( $template_type, 'custom' ); ?>><?php _e( 'Custom Block', 'xt-elementor-header-footer' ); ?></option>
						</select>
					</td>
				</tr>
				<tr class="xt-ehf-options-row xt-ehf-shortcode">
					<td class="xt-ehf-options-row-heading">
						<label for="ehf_template_type"><?php _e( 'Shortcode', 'xt-elementor-header-footer' ); ?></label>
						<i class="xt-ehf-options-row-heading-help dashicons dashicons-editor-help" title="<?php _e( 'Copy this shortcode and paste it into your post, page, or text widget content.', 'xt-elementor-header-footer' ); ?>">
						</i>
					</td>
					<td class="xt-ehf-options-row-content">
						<span class="xt-ehf-shortcode-col-wrap">
							<input type="text" onfocus="this.select();" readonly="readonly" value="[xt_ehf_template id='<?php echo esc_attr( $post->ID ); ?>']" class="xt-ehf-large-text code">
						</span>
					</td>
				</tr>
				<tr class="xt-ehf-options-row">
					<td class="xt-ehf-options-row-heading">
						<label for="display-on-canvas-template">
							<?php _e( 'Enable Layout for Elementor Canvas Template?', 'xt-elementor-header-footer' ); ?>
						</label>
						<i class="xt-ehf-options-row-heading-help dashicons dashicons-editor-help" title="<?php _e( 'Enabling this option will display this layout on pages using Elementor Canvas Template.', 'xt-elementor-header-footer' ); ?>"></i>
					</td>
					<td class="xt-ehf-options-row-content">
						<input type="checkbox" id="display-on-canvas-template" name="display-on-canvas-template" value="1" <?php checked( $display_on_canvas, true ); ?> />
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Save meta field.
	 *
	 * @param  POST $post_id Currennt post object which is being displayed.
	 *
	 * @return Void
	 */
	public function save_meta( $post_id ) {

		// Bail if we're doing an auto save.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// if our nonce isn't there, or we can't verify it, bail.
		if ( ! isset( $_POST['xt_ehf_meta_nounce'] ) || ! wp_verify_nonce( $_POST['xt_ehf_meta_nounce'], 'xt_ehf_meta_nounce' ) ) {
			return;
		}

		// if our current user can't edit this post, bail.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		if ( isset( $_POST['xt_ehf_template_type'] ) ) {
			update_post_meta( $post_id, 'xt_ehf_template_type', esc_attr( $_POST['xt_ehf_template_type'] ) );
		}

		if ( isset( $_POST['display-on-canvas-template'] ) ) {
			update_post_meta( $post_id, 'display-on-canvas-template', esc_attr( $_POST['display-on-canvas-template'] ) );
		} else {
			delete_post_meta( $post_id, 'display-on-canvas-template' );
		}

	}

	/**
	 * Display notice when editing the header or footer when there is one more of similar layout is active on the site.
	 *
	 * @since 1.0.0
	 */
	public function location_notice() {

		global $pagenow;
		global $post;

		if ( 'post.php' != $pagenow || ! is_object( $post ) || 'xt-ehf' != $post->post_type ) {
			return;
		}

		$template_type = get_post_meta( $post->ID, 'xt_ehf_template_type', true );

		if ( '' !== $template_type ) {
			$templates = XT_Elementor_Header_Footer::get_template_id( $template_type );

			// Check if more than one template is selected for current template type.
			if ( is_array( $templates ) && isset( $templates[1] ) && $post->ID != $templates[0] ) {

				$post_title        = '<strong>' . get_the_title( $templates[0] ) . '</strong>';
				$template_location = '<strong>' . $this->template_location( $template_type ) . '</strong>';
				/* Translators: Post title, Template Location */
				$message = sprintf( esc_html__( 'Template %1$s is already assigned to the location %2$s', 'xt-elementor-header-footer' ), $post_title, $template_location );

				echo '<div class="error"><p>';
				echo $message;
				echo '</p></div>';
			}
		}

	}

	/**
	 * Convert the Template name to be added in the notice.
	 *
	 * @since  1.0.0
	 *
	 * @param  String $template_type Template type name.
	 *
	 * @return String $template_type Template type name.
	 */
	public function template_location( $template_type ) {
		$template_type = ucfirst( str_replace( 'type_', '', $template_type ) );

		return $template_type;
	}

	/**
	 * Don't display the elementor header footer templates on the frontend for non edit_posts capable users.
	 *
	 * @since  1.0.0
	 */
	public function block_template_frontend() {
		if ( is_singular( 'xt-ehf' ) && ! current_user_can( 'edit_posts' ) ) {
			wp_redirect( site_url(), 301 );
			die;
		}
	}

	/**
	 * Single template function which will choose our template
	 *
	 * @since  1.0.1
	 *
	 * @param  String $single_template Single template.
	 */
	function load_canvas_template( $single_template ) {

		global $post;

		if ( 'xt-ehf' == $post->post_type ) {

			$elementor_2_0_canvas = ELEMENTOR_PATH . '/modules/page-templates/templates/canvas.php';

			if ( file_exists( $elementor_2_0_canvas ) ) {
				return $elementor_2_0_canvas;
			} else {
				return ELEMENTOR_PATH . '/includes/page-templates/canvas.php';
			}
		}

		return $single_template;
	}

	/**
	 * Set shortcode column for template list.
	 *
	 * @param array $columns template list columns.
	 */
	function set_shortcode_columns( $columns ) {

		$date_column = $columns['date'];

		unset( $columns['date'] );

		$columns['shortcode'] = esc_html__( 'Shortcode', 'xt-elementor-header-footer' );
		$columns['date']      = $date_column;

		return $columns;
	}

	/**
	 * Display shortcode in template list column.
	 *
	 * @param array $column template list column.
	 * @param int   $post_id post id.
	 */
	function render_shortcode_column( $column, $post_id ) {

		switch ( $column ) {
			case 'shortcode':
				ob_start();
				?>
				<span class="xt-ehf-shortcode-col-wrap">
					<input type="text" onfocus="this.select();" readonly="readonly" value="[xt_ehf_template id='<?php echo esc_attr( $post_id ); ?>']" class="xt-ehf-large-text code">
				</span>

				<?php

				ob_get_contents();
				break;
		}
	}
}

XT_EHF_Admin::instance();
