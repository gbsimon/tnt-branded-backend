<?php
/**
 * Admin Footer Customization
 * Handles admin footer text modification
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Modify Footer
 */
function tnt_admin_footer_text() {
	echo 'Un site <a href="http://www.wordpress.org" target="_blank">WordPress</a> créé et développé par <a href="https://www.tomtom.design" target="_blank">tom & tom</a> | studio de web et branding</p>';
}
add_filter('admin_footer_text', 'tnt_admin_footer_text');


