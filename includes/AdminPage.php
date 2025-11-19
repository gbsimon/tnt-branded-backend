<?php
/**
 * Admin Page and Dashboard Widget
 * Handles admin settings page, dashboard widget, and admin scripts
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add Settings link to plugin actions
 */
function tnt_add_plugin_action_links($links) {
	$settings_link = '<a href="' . admin_url('options-general.php?page=tnt-branded-backend') . '">' . __('Settings', 'tnt-branded-backend') . '</a>';
	array_unshift($links, $settings_link);
	return $links;
}
add_filter('plugin_action_links_' . plugin_basename(dirname(__FILE__) . '/../tnt-backend.php'), 'tnt_add_plugin_action_links');

/**
 * Add admin menu page
 */
function tnt_add_admin_menu() {
	add_options_page(
		'Admin sur mesure par tom & tom',
		'Admin sur mesure par tom & tom',
		'manage_options',
		'tnt-branded-backend',
		'tnt_admin_page'
	);
}
add_action('admin_menu', 'tnt_add_admin_menu');

/**
 * Admin page
 */
function tnt_admin_page() {
	if (!current_user_can('manage_options')) {
		return;
	}
	
	if (isset($_GET['settings-updated'])) {
		add_settings_error('tnt_messages', 'tnt_message', 'Paramètres enregistrés.', 'updated');
	}
	
	settings_errors('tnt_messages');
	?>
	<div class="wrap">
		<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields('tnt_branded_backend_settings');
			do_settings_sections('tnt-branded-backend');
			submit_button('Enregistrer les modifications');
			?>
		</form>
	</div>
	<?php
}

/**
 * Enqueue admin scripts and styles
 */
function tnt_admin_scripts($hook) {
	if ($hook !== 'settings_page_tnt-branded-backend') {
		return;
	}
	
	wp_enqueue_media();
	wp_enqueue_style('wp-color-picker');
	wp_enqueue_script('wp-color-picker');
	
	wp_add_inline_script('wp-color-picker', '
		jQuery(document).ready(function($) {
			$(".tnt-color-picker").wpColorPicker();
			
			var mediaUploader;
			
			$("#tnt_upload_logo_btn").on("click", function(e) {
				e.preventDefault();
				
				if (mediaUploader) {
					mediaUploader.open();
					return;
				}
				
				mediaUploader = wp.media({
					title: "Choisir un logo",
					button: {
						text: "Utiliser ce logo"
					},
					multiple: false
				});
				
				mediaUploader.on("select", function() {
					var attachment = mediaUploader.state().get("selection").first().toJSON();
					$("#tnt_logo_url").val(attachment.url);
					var previewHtml = "<div style=\"margin-bottom: 10px;\"><strong>Aperçu actuel :</strong></div>" +
						"<div style=\"background: white; padding: 20px; border-radius: 4px; display: inline-block; min-width: 320px; text-align: center;\">" +
						"<img src=\"" + attachment.url + "\" style=\"max-width: 320px; max-height: 100px; width: auto; height: auto; display: block; margin: 0 auto;\" id=\"tnt_logo_preview_img\" />" +
						"</div>";
					$("#tnt_logo_preview").html(previewHtml);
					$("#tnt_upload_logo_btn").text("Changer le logo");
					if (!$("#tnt_remove_logo_btn").length) {
						$("#tnt_upload_logo_btn").after("<button type=\"button\" class=\"button\" id=\"tnt_remove_logo_btn\">Supprimer</button>");
					}
				});
				
				mediaUploader.open();
			});
			
			$(document).on("click", "#tnt_remove_logo_btn", function(e) {
				e.preventDefault();
				$("#tnt_logo_url").val("");
				$("#tnt_logo_preview").html("<p style=\"color: #646970; margin: 0;\">Aucun logo sélectionné. Le logo par défaut sera utilisé.</p>");
				$("#tnt_upload_logo_btn").text("Choisir un logo");
				$(this).remove();
			});
		});
	');
}
add_action('admin_enqueue_scripts', 'tnt_admin_scripts');

/**
 * Add dashboard widget
 */
function tnt_add_dashboard_widget() {
	wp_add_dashboard_widget(
		'tnt_branded_backend_widget',
		'Admin sur mesure par tom & tom',
		'tnt_dashboard_widget_content'
	);
}
add_action('wp_dashboard_setup', 'tnt_add_dashboard_widget');

/**
 * Dashboard widget content
 */
function tnt_dashboard_widget_content() {
	$settings = tnt_get_settings();
	$logo_url = $settings['logo_url'];
	$accent_color = $settings['accent_color'] ?: '#2158ff';
	$accent_color_2 = $settings['accent_color_2'] ?: '#f9f9f9';
	$settings_url = admin_url('options-general.php?page=tnt-branded-backend');
	
	?>
	<div class="tnt-dashboard-widget">
		<?php if ($logo_url): ?>
			<div class="tnt-dashboard-logo" style="margin-bottom: 15px; text-align: center;">
				<img src="<?php echo esc_url($logo_url); ?>" alt="Logo" style="max-width: 200px; max-height: 60px; height: auto; display: block; margin: 0 auto;" />
			</div>
		<?php endif; ?>
		
		<div class="tnt-dashboard-colors" style="margin-bottom: 15px;">
			<h4 style="margin: 0 0 10px 0; font-size: 13px; font-weight: 600;">Couleurs d'accent actuelles</h4>
			<div style="display: flex; gap: 10px; align-items: center;">
				<div style="flex: 1;">
					<div style="background-color: <?php echo esc_attr($accent_color); ?>; height: 40px; border-radius: 4px; margin-bottom: 5px; border: 1px solid #ddd;"></div>
					<p style="margin: 0; font-size: 11px; color: #646970;">
						<strong>Principale:</strong><br>
						<code style="background: #f0f0f1; padding: 2px 4px; border-radius: 2px; font-size: 10px;"><?php echo esc_html($accent_color); ?></code>
					</p>
				</div>
				<div style="flex: 1;">
					<div style="background-color: <?php echo esc_attr($accent_color_2); ?>; height: 40px; border-radius: 4px; margin-bottom: 5px; border: 1px solid #ddd;"></div>
					<p style="margin: 0; font-size: 11px; color: #646970;">
						<strong>Secondaire:</strong><br>
						<code style="background: #f0f0f1; padding: 2px 4px; border-radius: 2px; font-size: 10px;"><?php echo esc_html($accent_color_2); ?></code>
					</p>
				</div>
			</div>
		</div>
		
		<div class="tnt-dashboard-actions" style="border-top: 1px solid #ddd; padding-top: 15px;">
			<a href="<?php echo esc_url($settings_url); ?>" class="button button-primary" style="width: 100%; text-align: center; margin-bottom: 10px;">
				⚙️ Configurer les paramètres
			</a>
			<p style="margin: 0; font-size: 12px; color: #646970; text-align: center;">
				Personnalisez le logo et les couleurs de votre backend WordPress.
			</p>
		</div>
		
		<div class="tnt-dashboard-footer" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd; text-align: center;">
			<p style="margin: 0; font-size: 11px; color: #8c8f94;">
				Un plugin par <a href="https://tomtom.design" target="_blank" style="text-decoration: none;">tom & tom</a>
			</p>
		</div>
	</div>
	<?php
}


