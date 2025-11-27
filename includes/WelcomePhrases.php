<?php
/**
 * Welcome Phrases Configuration
 * 
 * Customize the welcome messages that appear on the login page.
 * Each array contains variations that will be randomly selected.
 * Supports multiple languages (fr, en, etc.)
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Get welcome message phrases by language
 * 
 * @param string $lang Language code (default: 'fr')
 * @return array Array of phrases for the specified language
 */
function tnt_get_welcome_phrases($lang = null) {
	// If no language specified, try to detect it
	if ($lang === null || $lang === 'auto' || empty($lang)) {
		if (function_exists('tnt_get_welcome_language')) {
			$lang = tnt_get_welcome_language();
		} elseif (function_exists('get_locale')) {
			$locale = get_locale();
			$lang = substr($locale, 0, 2);
		} else {
			$lang = 'fr';
		}
	}
	
	$phrases = array(
		'fr' => array(
			'greeting' => array(
				'Salut&nbsp;!',
				'Allô&nbsp;!',
				'Bonjour&nbsp;!',
				'Hey&nbsp;!',
				'Coucou&nbsp;!',
				'Salutations&nbsp;!',
			),
			'main_message' => array(
				'Bienvenue sur votre site.',
				'Ravi de vous revoir.',
				'Prêt à continuer ?',
				'On reprend où on en était ?',
				'Bon retour&nbsp;!',
				'Tout est prêt pour vous.',
				'C\'est parti&nbsp;!',
			),
			'sub_message' => array(
				'👋',
				'🤘',
				'✌️',
			),
		),
		'en' => array(
			'greeting' => array(
				'Hi!',
				'Hello!',
				'Hey!',
				'Welcome!',
			),
			'main_message' => array(
				'Welcome to your site.',
				'Good to see you again.',
				'Ready to continue?',
				'Let\'s pick up where we left off?',
				'Nice to see you.',
				'Welcome back!',
				'Let\'s go!',
				'Everything is ready for you'
			),
			'sub_message' => array(
				'👋',
				'🤘',
				'✌️',
			),
		),
	);
	
	// Fallback to French if language not available
	if (!isset($phrases[$lang]) || empty($phrases[$lang])) {
		$lang = 'fr';
	}
	
	return $phrases[$lang];
}

/**
 * Get current language for welcome phrases
 * Checks URL parameter wp_lang first, then WordPress locale
 * 
 * @return string Language code
 */
function tnt_get_welcome_language() {
	// Allow filtering the language
	$lang = apply_filters('tnt_welcome_language', 'auto');
	
	if ($lang === 'auto' || empty($lang)) {
		// First, check URL parameter wp_lang (for language switcher)
		if (isset($_GET['wp_lang']) && !empty($_GET['wp_lang'])) {
			$url_lang = sanitize_text_field($_GET['wp_lang']);
			$lang = substr($url_lang, 0, 2); // Extract language code (e.g., 'en' from 'en_US')
		}
		// If no URL parameter, use WordPress locale
		elseif (function_exists('get_locale')) {
			$locale = get_locale();
			$lang = substr($locale, 0, 2); // Extract language code (e.g., 'fr' from 'fr_CA')
		} else {
			$lang = 'fr'; // Fallback if get_locale() not available
		}
	}
	
	// Ensure we return a valid language code
	return !empty($lang) ? $lang : 'fr';
}

