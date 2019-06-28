<?php
/**
 * Plugin Name: HYP Convert
 * Plugin URI: https://github.com/hypericumimpex/hyp-convert/
 * Author: Romeo C.
 * Author URI: https://github.com/hypericumimpex/
 * Version: 1.3.7
 * Description: HYP Convert, editor drag-and-drop care vă ajută să creați ferestre pop-up și formulare opt-in pentru a crește conversiile site-ului.
 * Text Domain: convertpro
 *
 * @package HYP Convert
 */

$brainstrom = get_option( 'brainstrom_products' );
$brainstrom['plugins']['convertpro']['status'] = 'registered';
update_option( 'brainstrom_products', $brainstrom );

add_action( 'plugins_loaded', 'cp_load_convertpro', 1 );

// Activation.
register_activation_hook( __FILE__, 'activation' );

if ( ! function_exists( 'cp_load_convertpro' ) ) {

	/**
	 * Function to load packages
	 *
	 * @since 1.0
	 */
	function cp_load_convertpro() {
		require_once 'classes/class-cp-v2-loader.php';

	}
}

/**
 * Function for activation hook
 *
 * @since 1.0
 */
function activation() {

	update_option( 'convert_pro_redirect', true );
	update_site_option( 'bsf_force_check_extensions', true );

	delete_option( 'cpro_hide_branding' );
	delete_site_option( '_cpro_hide_branding' );

	global $wp_version;
	$wp  = '3.5';
	$php = '5.3.2';
	if ( version_compare( PHP_VERSION, $php, '<' ) ) {
		$flag = 'PHP';
	} elseif ( version_compare( $wp_version, $wp, '<' ) ) {
		$flag = 'WordPress';
	} else {
		return;
	}
	$version = 'PHP' == $flag ? $php : $wp;
	deactivate_plugins( CP_V2_DIR_NAME );
	wp_die(
		'<p><strong>' . CP_PRO_NAME . ' </strong> requires <strong>' . $flag . '</strong> version <strong>' . $version . '</strong> or greater. Please contact your host.</p>',
		'Plugin Activation Error',
		array(
			'response'  => 200,
			'back_link' => true,
		)
	);
}