<?php
/**
 * Google Recaptcha Page.
 *
 * @package ConvertPro
 */

?>

<div class="cp-google-recaptcha-wrap">
	<form method="post" class="cp-settings-form">
	<?php
	$cp_google_recaptcha_site_key   = get_option( 'cp_google_recaptcha_site_key' );
	$cp_google_recaptcha_secret_key = get_option( 'cp_google_recaptcha_secret_key' );

	?>
		<h3 class="cp-gen-set-title" ><?php _e( 'Google Recaptcha Settings', 'convertpro' ); ?></h3>
		<p>
		<?php
		_e( 'Google reCAPTCHA is a free service that protects your site from spam and abuse. It uses advanced risk analysis techniques to tell humans and bots apart. In Convert Pro Google reCAPTCHA v2 is used.', 'convertpro' );
		_e( ' Get a Google reCAPTCHA keys from <a class="cp-google-recaptcha-page-link" href="https://www.google.com/recaptcha/intro/v3.html" target="_blank" rel="noopener">here</a> and paste it below.', 'convertpro' );
		?>
		</p>
		<table class="cp-postbox-table form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="option-admin-menu-site-key-page"><?php _e( 'Site Key', 'convertpro' ); ?></label>
					</th>
					<td>
						<input type="text" id="cp_google_recaptcha_site_key" name="cp_google_recaptcha_site_key" value="<?php echo esc_attr( $cp_google_recaptcha_site_key ); ?>" placeholder="<?php echo CP_GOOGLE_RECAPTCHA_SITE_KEY; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="option-admin-menu-secret-key-page"><?php _e( 'Secret Key', 'convertpro' ); ?></label>
					</th>
					<td>
						<input type="text" id="cp_google_recaptcha_secret_key" name="cp_google_recaptcha_secret_key" value="<?php echo esc_attr( $cp_google_recaptcha_secret_key ); ?>" placeholder="<?php echo CP_GOOGLE_RECAPTCHA_SECRET_KEY; ?>" />
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
		<input type="hidden" name="curr_tab" value="0">
		<input type="hidden" name="cp-update-settings-nonce" id="cp-update-settings-nonce" value="<?php echo wp_create_nonce( 'cp-update-settings-nonce' ); ?>" />
		<button type="submit" class="cp-btn-primary cp-md-btn cp-button-style button-update-settings cp-submit-settings"><?php _e( 'Save Settings', 'convertpro' ); ?></button>
	</form>
</div> <!-- End Wrapper -->
