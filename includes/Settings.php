<?php
/**
 * Settings Management
 * Handles plugin settings retrieval, registration, and sanitization
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get plugin settings
 */
function tnt_get_settings() {
	return wp_parse_args(
		get_option('tnt_branded_backend_settings', array()),
		array(
			'logo_url' => '',
			'accent_color' => '#2158ff',
			'accent_color_2' => '#f9f9f9',
		)
	);
}

/**
 * Register settings
 */
function tnt_register_settings() {
	register_setting('tnt_branded_backend_settings', 'tnt_branded_backend_settings', 'tnt_sanitize_settings');
	
	add_settings_section(
		'tnt_settings_section',
		'Paramètres de personnalisation',
		'tnt_settings_section_callback',
		'tnt-branded-backend'
	);
	
	add_settings_field(
		'logo_url',
		'Logo',
		'tnt_logo_field_callback',
		'tnt-branded-backend',
		'tnt_settings_section'
	);
	
	add_settings_field(
		'accent_color',
		'Couleur d\'accent principale',
		'tnt_accent_color_field_callback',
		'tnt-branded-backend',
		'tnt_settings_section'
	);
	
	add_settings_field(
		'accent_color_2',
		'Couleur d\'accent secondaire',
		'tnt_accent_color_2_field_callback',
		'tnt-branded-backend',
		'tnt_settings_section'
	);
}
add_action('admin_init', 'tnt_register_settings');

/**
 * Sanitize settings
 */
function tnt_sanitize_settings($input) {
	$sanitized = array();
	
	if (isset($input['logo_url'])) {
		$sanitized['logo_url'] = esc_url_raw($input['logo_url']);
	}
	
	if (isset($input['accent_color'])) {
		$sanitized['accent_color'] = sanitize_hex_color($input['accent_color']);
	}
	
	if (isset($input['accent_color_2'])) {
		$sanitized['accent_color_2'] = sanitize_hex_color($input['accent_color_2']);
	}
	
	return $sanitized;
}

/**
 * Settings section callback
 */
function tnt_settings_section_callback() {
	echo '<p>Personnalisez l\'apparence du backend WordPress.</p>';
	echo '<p><strong>Note sur les logos :</strong> Les logos de toutes tailles et proportions sont acceptés. Le logo sera automatiquement redimensionné pour s\'adapter (max. 320px de largeur, 100px de hauteur). Pour de meilleurs résultats, utilisez un logo avec un ratio largeur/hauteur entre 2:1 et 4:1.</p>';
}

/**
 * Logo field callback
 */
function tnt_logo_field_callback() {
	$settings = tnt_get_settings();
	$logo_url = $settings['logo_url'];
	?>
	<div class="tnt-logo-upload">
		<input type="hidden" id="tnt_logo_url" name="tnt_branded_backend_settings[logo_url]" value="<?php echo esc_attr($logo_url); ?>" />
		<div id="tnt_logo_preview" style="margin-bottom: 15px; padding: 15px; background: #f0f0f1; border: 1px solid #c3c4c7; border-radius: 4px;">
			<?php if ($logo_url): ?>
				<div style="margin-bottom: 10px;">
					<strong>Aperçu actuel :</strong>
				</div>
				<div style="background: white; padding: 20px; border-radius: 4px; display: inline-block; min-width: 320px; text-align: center;">
					<img src="<?php echo esc_url($logo_url); ?>" style="max-width: 320px; max-height: 100px; width: auto; height: auto; display: block; margin: 0 auto;" id="tnt_logo_preview_img" />
				</div>
			<?php else: ?>
				<p style="color: #646970; margin: 0;">Aucun logo sélectionné. Le logo par défaut sera utilisé.</p>
			<?php endif; ?>
		</div>
		<button type="button" class="button" id="tnt_upload_logo_btn"><?php echo $logo_url ? 'Changer le logo' : 'Choisir un logo'; ?></button>
		<?php if ($logo_url): ?>
			<button type="button" class="button" id="tnt_remove_logo_btn">Supprimer</button>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Accent color field callback
 */
function tnt_accent_color_field_callback() {
	$settings = tnt_get_settings();
	$color = $settings['accent_color'];
	?>
	<input type="text" name="tnt_branded_backend_settings[accent_color]" value="<?php echo esc_attr($color); ?>" class="tnt-color-picker" data-default-color="#2158ff" />
	<?php
}

/**
 * Accent color 2 field callback
 */
function tnt_accent_color_2_field_callback() {
	$settings = tnt_get_settings();
	$color = $settings['accent_color_2'];
	?>
	<input type="text" name="tnt_branded_backend_settings[accent_color_2]" value="<?php echo esc_attr($color); ?>" class="tnt-color-picker" data-default-color="#f9f9f9" />
	<?php
}


