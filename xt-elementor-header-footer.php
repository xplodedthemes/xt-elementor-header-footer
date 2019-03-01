<?php
/**
 * Plugin Name:     Headers, Footers & Blocks for Elementor
 * Plugin URI:      https://github.com/xplodedthemes/xt-elementor-header-footer
 * Description:     Create Header and Footer for your site using Elementor Page Builder.
 * Author:          XplodedThemes
 * Author URI:      https://xplodedthemes.com/
 * Text Domain:     xt-elementor-header-footer
 * Domain Path:     /languages
 * Version:         1.1.2
 *
 * @package         xt-elementor-header-footer
 */

define( 'XT_EHF_VER', '1.1.2' );
define( 'XT_EHF_DIR', plugin_dir_path( __FILE__ ) );
define( 'XT_EHF_URL', plugins_url( '/', __FILE__ ) );
define( 'XT_EHF_PATH', plugin_basename( __FILE__ ) );

/**
 * Load the class loader.
 */
require_once XT_EHF_DIR . '/inc/class-xt-elementor-header-footer.php';

/**
 * Load the Plugin Class.
 */
function xt_ehf_init() {
	new XT_Elementor_Header_Footer();
}

add_action( 'plugins_loaded', 'xt_ehf_init' );
