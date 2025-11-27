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
		'Un site sur mesure par tom & tom',
		'tnt_dashboard_widget_content'
	);
}
// Use high priority to ensure widget is added first
add_action('wp_dashboard_setup', 'tnt_add_dashboard_widget', 1);

/**
 * Force dashboard widget to appear first
 */
function tnt_force_dashboard_widget_first() {
	$screen = get_current_screen();
	// Only run on dashboard
	if (!$screen || $screen->id !== 'dashboard') {
		return;
	}
	
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		// Move our widget to the top of the dashboard
		var $widget = $('#tnt_branded_backend_widget').closest('.postbox');
		var $normalContainer = $('#normal-sortables');
		
		if ($widget.length && $normalContainer.length) {
			// Remove from current position
			$widget.detach();
			// Insert at the beginning of normal sortables
			$normalContainer.prepend($widget);
		}
		
		// Handle contact form submission
		$('#tnt-contact-form').on('submit', function(e) {
			e.preventDefault();
			
			var $form = $(this);
			var $submitBtn = $form.find('button[type="submit"]');
			var $submitContainer = $form.find('.tnt-form-submit');
			
			// Disable button and show loading
			$submitBtn.prop('disabled', true).text('Envoi en cours...');
			
			// Get form data
			var formData = {
				action: 'tnt_send_contact_form',
				nonce: '<?php echo wp_create_nonce('tnt_contact_form_nonce'); ?>',
				name: $('#tnt-contact-name').val(),
				email: $('#tnt-contact-email').val(),
				message: $('#tnt-contact-message').val(),
				to_email: $form.find('input[name="to_email"]').val(),
				site_url: $form.find('input[name="site_url"]').val()
			};
			
			// Send AJAX request
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: formData,
				success: function(response) {
					if (response.success) {
						// Show success message
						$submitContainer.html('<p style="color: #46b450; margin: 0; font-size: 13px;">✓ ' + response.data.message + '</p>');
						
						// Reset form
						$form[0].reset();
						
						// Restore button after delay
						setTimeout(function() {
							$submitContainer.html('<button type="submit" class="button button-primary tnt-dashboard-settings-btn">📧 Envoyer</button>');
						}, 5000);
					} else {
						// Show error message
						$submitContainer.html('<p style="color: #dc3232; margin: 0; font-size: 13px;">✗ ' + (response.data.message || 'Erreur lors de l\'envoi.') + '</p>');
						
						// Restore button
						setTimeout(function() {
							$submitContainer.html('<button type="submit" class="button button-primary tnt-dashboard-settings-btn">📧 Envoyer</button>');
						}, 5000);
					}
				},
				error: function(xhr, status, error) {
					// Show error message
					$submitContainer.html('<p style="color: #dc3232; margin: 0; font-size: 13px;">✗ Erreur de connexion. Veuillez réessayer.</p>');
					
					// Restore button
					setTimeout(function() {
						$submitContainer.html('<button type="submit" class="button button-primary tnt-dashboard-settings-btn">📧 Envoyer</button>');
					}, 5000);
					
					console.error('TNT Contact Form Error:', error);
				}
			});
		});
	});
	</script>
	<?php
}
add_action('admin_footer', 'tnt_force_dashboard_widget_first');

/**
 * Handle contact form submission via AJAX
 */
function tnt_handle_contact_form_submission() {
	// Verify nonce for security
	check_ajax_referer('tnt_contact_form_nonce', 'nonce');
	
	// Check user permissions
	if (!current_user_can('read')) {
		wp_send_json_error(array('message' => 'Permissions insuffisantes.'));
		return;
	}
	
	// Get and sanitize form data
	$name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
	$email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
	$message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
	$to_email = isset($_POST['to_email']) ? sanitize_email($_POST['to_email']) : '';
	$site_url = isset($_POST['site_url']) ? sanitize_text_field($_POST['site_url']) : '';
	
	// Validate required fields
	if (empty($name) || empty($email) || empty($message) || empty($to_email)) {
		wp_send_json_error(array('message' => 'Tous les champs sont requis.'));
		return;
	}
	
	// Validate email format
	if (!is_email($email)) {
		wp_send_json_error(array('message' => 'Adresse courriel invalide.'));
		return;
	}
	
	// Prepare email
	$subject = 'Demande de soutien - ' . $site_url;
	$email_body = "Nouvelle demande de soutien depuis le tableau de bord WordPress\n\n";
	$email_body .= "Site web: " . $site_url . "\n";
	$email_body .= "Nom: " . $name . "\n";
	$email_body .= "Courriel: " . $email . "\n\n";
	$email_body .= "Message:\n" . $message . "\n";
	
	// Set email headers
	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'From: ' . $name . ' <' . $email . '>',
		'Reply-To: ' . $email
	);
	
	// Send email
	$mail_sent = wp_mail($to_email, $subject, $email_body, $headers);
	
	// Debug logging (remove in production if not needed)
	if (defined('WP_DEBUG') && WP_DEBUG) {
		error_log('TNT Contact Form - Email sent: ' . ($mail_sent ? 'Yes' : 'No'));
		error_log('TNT Contact Form - To: ' . $to_email);
		error_log('TNT Contact Form - Subject: ' . $subject);
	}
	
	if ($mail_sent) {
		wp_send_json_success(array('message' => 'Message envoyé avec succès. Nous vous répondrons dans les meilleurs délais.'));
	} else {
		// Check if wp_mail failed
		global $phpmailer;
		if (isset($phpmailer) && isset($phpmailer->ErrorInfo)) {
			error_log('TNT Contact Form - PHPMailer Error: ' . $phpmailer->ErrorInfo);
		}
		wp_send_json_error(array('message' => 'Erreur lors de l\'envoi. Veuillez réessayer ou utiliser le lien courriel ci-dessous.'));
	}
}
add_action('wp_ajax_tnt_send_contact_form', 'tnt_handle_contact_form_submission');

/**
 * Dashboard widget content
 */
function tnt_dashboard_widget_content() {
	$settings = tnt_get_settings();
	$accent_color = $settings['accent_color'] ?: '#2158ff';
	$settings_url = admin_url('options-general.php?page=tnt-branded-backend');
	
	?>
	<div class="tnt-dashboard-widget">
		<div class="tnt-dashboard-widget-content">
			<div class="tnt-dashboard-icon">
				<svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="currentColor"/>
				</svg>
			</div>
			
			<h3 class="tnt-dashboard-title">Besoin d'aide avec votre site web?</h3>
			
			<p class="tnt-dashboard-message">
				<strong>tom & tom</strong>, l'équipe derrière ce site web sur mesure est là pour vous aider! Que ce soit pour des modifications, des améliorations ou simplement parce que vous avez des questions, n'hésitez pas à nous contacter.
			</p>
			
			<div class="tnt-dashboard-contact">
				<form class="tnt-contact-form" id="tnt-contact-form">
					<?php
					$site_url = home_url();
					// Remove protocol (http:// or https://)
					$site_url = preg_replace('#^https?://#', '', $site_url);
					// Remove trailing slash
					$site_url = rtrim($site_url, '/');
					$email_address = 'soutien_web+' . $site_url . '@tomtom.design';
					?>
					<input type="hidden" name="to_email" value="<?php echo esc_attr($email_address); ?>">
					<input type="hidden" name="site_url" value="<?php echo esc_attr($site_url); ?>">
					
					<p class="tnt-form-field">
						<label for="tnt-contact-name">Nom</label>
						<input type="text" id="tnt-contact-name" name="name" required>
					</p>
					
					<p class="tnt-form-field">
						<label for="tnt-contact-email">Courriel</label>
						<input type="email" id="tnt-contact-email" name="email" required>
					</p>
					
					<p class="tnt-form-field">
						<label for="tnt-contact-message">Message</label>
						<textarea id="tnt-contact-message" name="message" rows="3" required></textarea>
					</p>
					
					<p class="tnt-form-submit">
						<button type="submit" class="button button-primary tnt-dashboard-settings-btn">
							📧 Envoyer
						</button>
					</p>
					<p class="tnt-form-email-link">
						Ou écrivez-nous un courriel à <a href="mailto:<?php echo esc_attr($email_address); ?>">soutien_web@tomtom.design</a>
					</p>
				</form>
			</div>
			
			<div class="tnt-dashboard-actions">
				<a href="<?php echo esc_url($settings_url); ?>" class="button button-secondary">
					⚙️ Paramètres
				</a>
			</div>
		</div>
	</div>
	<?php
}


