<?php
/**
 * Plugin Name: Admin Wordpress sur mesure par tom & tom 
 * Plugin URI: https://tomtom.design
 * Description: Un backend juste pour vous!
 * Version: 2.0.0
 * Author: Simon Gauthier Boudreau (tom & tom)
 * Author URI: https://tomtom.design
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Text Domain: tnt-branded-backend
 * Domain Path: /languages
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Check WordPress version
function tnt_check_wp_version() {
	global $wp_version;
	$required_wp_version = '5.0';
	
	if ( version_compare( $wp_version, $required_wp_version, '<' ) ) {
		add_action( 'admin_notices', 'tnt_wp_version_notice' );
		return false;
	}
	return true;
}

// Check PHP version
function tnt_check_php_version() {
	$required_php_version = '7.4';
	
	if ( version_compare( PHP_VERSION, $required_php_version, '<' ) ) {
		add_action( 'admin_notices', 'tnt_php_version_notice' );
		return false;
	}
	return true;
}

// WordPress version notice
function tnt_wp_version_notice() {
	?>
	<div class="notice notice-error">
		<p><?php _e( 'Admin sur mesure par tom & tom nécessite WordPress 5.0 ou supérieur. Veuillez mettre à jour WordPress.', 'tnt-branded-backend' ); ?></p>
	</div>
	<?php
}

// PHP version notice
function tnt_php_version_notice() {
	?>
	<div class="notice notice-error">
		<p><?php _e( 'Admin sur mesure par tom & tom nécessite PHP 7.4 ou supérieur. Veuillez contacter votre hébergeur pour mettre à jour PHP.', 'tnt-branded-backend' ); ?></p>
	</div>
	<?php
}

// Initialize plugin only if requirements are met
if ( tnt_check_wp_version() && tnt_check_php_version() ) {
	// Include plugin files
	$plugin_files = array(
		'includes/WelcomePhrases.php',
		'includes/Settings.php',
		'includes/Styles.php',
		'includes/LoginPage.php',
		'includes/AdminPage.php',
		'includes/AdminFooter.php',
	);
	
	foreach ( $plugin_files as $file ) {
		$file_path = plugin_dir_path( __FILE__ ) . $file;
		if ( file_exists( $file_path ) ) {
			require_once $file_path;
		}
	}
}

?>