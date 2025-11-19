<?php
/**
 * Style Enqueuing
 * Handles CSS file enqueuing for admin and login pages
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Custom admin CSS
 */
function tnt_admin_theme_style() {
	$plugin_file = dirname(__FILE__) . '/../tnt-backend.php';
	$cms_css = plugin_dir_path($plugin_file) . 'assets/css/cms.css';
	$baseline_css = plugin_dir_path($plugin_file) . 'assets/css/baseline.css';
	$dependencies = array();
	
	// Enqueue baseline first if it exists and is compiled
	if (file_exists($baseline_css)) {
		wp_enqueue_style(
			'tnt-baseline',
			plugins_url('assets/css/baseline.css', $plugin_file),
			array(),
			filemtime($baseline_css)
		);
		$dependencies[] = 'tnt-baseline';
	}
	
	// Enqueue CMS styles
	if (file_exists($cms_css)) {
		wp_enqueue_style(
			'tnt-cms',
			plugins_url('assets/css/cms.css', $plugin_file),
			$dependencies,
			filemtime($cms_css)
		);
	}
}
add_action('admin_enqueue_scripts', 'tnt_admin_theme_style');

/**
 * Custom login panel styles
 */
function tnt_login_theme_style() {
	$plugin_file = dirname(__FILE__) . '/../tnt-backend.php';
	$login_css = plugin_dir_path($plugin_file) . 'assets/css/login.css';
	$baseline_css = plugin_dir_path($plugin_file) . 'assets/css/baseline.css';
	$dependencies = array();
	
	// Enqueue baseline first if it exists and is compiled
	if (file_exists($baseline_css)) {
		wp_enqueue_style(
			'tnt-baseline',
			plugins_url('assets/css/baseline.css', $plugin_file),
			array(),
			filemtime($baseline_css)
		);
		$dependencies[] = 'tnt-baseline';
	}
	
	// Enqueue login styles
	if (file_exists($login_css)) {
		wp_enqueue_style(
			'tnt-login',
			plugins_url('assets/css/login.css', $plugin_file),
			$dependencies,
			filemtime($login_css)
		);
	}
}
add_action('login_enqueue_scripts', 'tnt_login_theme_style');

/**
 * Output dynamic CSS based on settings
 */
function tnt_output_dynamic_css() {
	$settings = tnt_get_settings();
	$accent_color = $settings['accent_color'] ?: '#2158ff';
	$accent_color_2 = $settings['accent_color_2'] ?: '#f9f9f9';
	?>
	<style id="tnt-dynamic-css">
		:root {
			--tnt-accent-color: <?php echo esc_attr($accent_color); ?>;
			--tnt-accent-color-2: <?php echo esc_attr($accent_color_2); ?>;
		}
	</style>
	<?php
}
add_action('admin_head', 'tnt_output_dynamic_css');
add_action('login_head', 'tnt_output_dynamic_css');

