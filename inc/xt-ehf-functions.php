<?php
/**
 * XT Elementor Header Footer Function
 *
 * @package  xt-elementor-header-footer
 */

/**
 * Checks if Header is enabled from XT_EHF.
 *
 * @since  1.0.2
 * @return bool True if header is enabled. False if header is not enabled
 */
function xt_ehf_header_enabled() {
	$header_id = XT_Elementor_Header_Footer::get_settings( 'type_header', '' );
	$status    = false;

	if ( '' !== $header_id ) {
		$status = true;
	}

	return apply_filters( 'xt_ehf_header_enabled', $status );
}

/**
 * Checks if Footer is enabled from XT_EHF.
 *
 * @since  1.0.2
 * @return bool True if header is enabled. False if header is not enabled.
 */
function xt_ehf_footer_enabled() {
	$footer_id = XT_Elementor_Header_Footer::get_settings( 'type_footer', '' );
	$status    = false;

	if ( '' !== $footer_id ) {
		$status = true;
	}

	return apply_filters( 'xt_ehf_footer_enabled', $status );
}

/**
 * Get XT_EHF Header ID
 *
 * @since  1.0.2
 * @return (String|boolean) header id if it is set else returns false.
 */
function get_xt_ehf_header_id() {
	$header_id = XT_Elementor_Header_Footer::get_settings( 'type_header', '' );

	if ( '' === $header_id ) {
		$header_id = false;
	}

	return apply_filters( 'get_xt_ehf_header_id', $header_id );
}

/**
 * Get XT_EHF Footer ID
 *
 * @since  1.0.2
 * @return (String|boolean) header id if it is set else returns false.
 */
function get_xt_ehf_footer_id() {
	$footer_id = XT_Elementor_Header_Footer::get_settings( 'type_footer', '' );

	if ( '' === $footer_id ) {
		$footer_id = false;
	}

	return apply_filters( 'get_xt_ehf_footer_id', $footer_id );
}

/**
 * Display header markup.
 *
 * @since  1.0.2
 */
function xt_ehf_render_header() {

	if ( false == apply_filters( 'enable_xt_ehf_render_header', true ) ) {
		return;
	}

	?>
		<header id="masthead" itemscope="itemscope" itemtype="https://schema.org/WPHeader">
			<p class="main-title bhf-hidden" itemprop="headline"><a href="<?php echo bloginfo( 'url' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
			<?php XT_Elementor_Header_Footer::get_header_content(); ?>
		</header>

	<?php

}

/**
 * Display footer markup.
 *
 * @since  1.0.2
 */
function xt_ehf_render_footer() {

	if ( false == apply_filters( 'enable_xt_ehf_render_footer', true ) ) {
		return;
	}

	?>
		<footer itemtype="https://schema.org/WPFooter" itemscope="itemscope" id="colophon" role="contentinfo">
			<?php XT_Elementor_Header_Footer::get_footer_content(); ?>
		</footer>
	<?php

}
