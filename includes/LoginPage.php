<?php
/**
 * Login Page Customization
 * Handles login page modifications: welcome messages, layout, footer, and logo
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get random phrase from array
 */
function tnt_get_random_phrase($phrases) {
	if (empty($phrases)) {
		return '';
	}
	return $phrases[array_rand($phrases)];
}

/**
 * Add welcome message before login form
 */
function tnt_login_welcome_message() {
	// Get phrases (auto-detects language or can be filtered)
	$phrases = tnt_get_welcome_phrases('auto');
	
	// Ensure we have valid phrases
	if (empty($phrases) || !is_array($phrases) || !isset($phrases['greeting'])) {
		$phrases = tnt_get_welcome_phrases('fr'); // Fallback to French
	}
	
	$greeting = tnt_get_random_phrase($phrases['greeting']);
	$main_message = tnt_get_random_phrase($phrases['main_message']);
	$sub_message = tnt_get_random_phrase($phrases['sub_message']);
	
	echo '<div class="tnt-login-left">';
	echo '<div class="tnt-login-welcome">';
	echo '<p>' . esc_html($sub_message) . '</p>';
	echo '<h2>' . esc_html($greeting) . '</h2>';
	echo '<h3>' . esc_html($main_message) . '</h3>';
	echo '</div>';
	echo '</div>';
}
add_action('login_message', 'tnt_login_welcome_message', 5);

/**
 * Restructure login page layout with JavaScript
 */
function tnt_login_page_structure() {
	?>
	<script>
	(function() {
		// Wait for DOM to be fully ready
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', restructureLogin);
		} else {
			restructureLogin();
		}
		
		function restructureLogin() {
			var loginContainer = document.getElementById('login');
			if (!loginContainer) return;
			
			// Check if already restructured
			if (loginContainer.querySelector('.tnt-login-right')) return;
			
			// Find the welcome message (left section)
			var leftSection = loginContainer.querySelector('.tnt-login-left');
			if (!leftSection) return;
			
			// Create right section wrapper
			var rightSection = document.createElement('div');
			rightSection.className = 'tnt-login-right';
			
			// Get all elements that should be in the right section
			var elementsToMove = [];
			var child = loginContainer.firstChild;
			var nextSibling;
			
			while (child) {
				nextSibling = child.nextSibling;
				// Skip the left section, script tags, and footer
				if (child !== leftSection && 
				    child.nodeType === 1 && 
				    child.tagName !== 'SCRIPT' &&
				    !child.classList.contains('tnt-login-footer')) {
					elementsToMove.push(child);
				}
				child = nextSibling;
			}
			
			// Move elements to right section
			elementsToMove.forEach(function(el) {
				rightSection.appendChild(el);
			});
			
			// Store footer if it exists
			var footer = loginContainer.querySelector('.tnt-login-footer');
			
			// Clear login container
			loginContainer.innerHTML = '';
			
			// Add both sections
			loginContainer.appendChild(leftSection);
			loginContainer.appendChild(rightSection);
			
			// Re-append footer if it exists
			if (footer) {
				loginContainer.appendChild(footer);
			}
		}
	})();
	</script>
	<?php
}
add_action('login_footer', 'tnt_login_page_structure', 20);

/**
 * Add footer text to login page
 */
function tnt_login_footer_text() {
	$logo_path = plugin_dir_path(__FILE__) . '../assets/images/logo-tomtom.php';
	echo '<div class="tnt-login-footer">';
	echo '<p>Site web développé par <a href="https://tomtom.design" target="_blank" class="tnt-footer-logo-link">';
	if (file_exists($logo_path)) {
		load_template($logo_path, false);
	}
	echo '</a></p>';
	echo '</div>';
}
add_action('login_footer', 'tnt_login_footer_text', 30);

/**
 * Change Logo URL
 */
function tnt_login_logo_url() {
	return home_url();
}
add_filter('login_headerurl', 'tnt_login_logo_url');

/**
 * Update login logo to use custom logo
 * Only sets background-image, letting SCSS handle all other properties
 */
function tnt_custom_login_logo() {
	$settings = tnt_get_settings();
	$logo_url = $settings['logo_url'];
	
	if ($logo_url) {
		?>
		<style type="text/css">
			#login h1 a {
				background-image: url(<?php echo esc_url($logo_url); ?>) !important;
			}
		</style>
		<?php
	}
}
add_action('login_head', 'tnt_custom_login_logo', 25);


