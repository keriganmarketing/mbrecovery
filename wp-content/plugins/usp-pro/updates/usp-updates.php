<?php // USP Pro - License & Activation

if (!defined('ABSPATH')) die();

define('USP_ITEM_NAME', 'USP Pro Personal');

if (!class_exists('EDD_SL_Plugin_Updater')) {
	include(dirname(__FILE__) . '/usp-updater.php');
}

if (!function_exists('usp_pro_plugin_updater')) : 
	function usp_pro_plugin_updater() {
		$license_key = trim(get_option('usp_license_key'));
		$edd_updater = new EDD_SL_Plugin_Updater(
			USP_PRO_URL, USP_PRO_FILE, 
			array(
				'license'   => $license_key,
				'item_name' => USP_ITEM_NAME,
				'author'    => USP_PRO_AUTHOR,
				'version'   => USP_PRO_VERSION,
				'url'       => USP_PRO_URL,
				// 'item_id'   => ,
			)
		);
	}
	add_action('admin_init', 'usp_pro_plugin_updater', 0);
endif;

// settings menu
if (!function_exists('usp_license_menu')) :
function usp_license_menu() {
	add_plugins_page('USP Pro License', 'USP Pro License', 'manage_options', 'usp-pro-license', 'usp_license_page');
}
add_action('admin_menu', 'usp_license_menu');
endif;

// settings page
if (!function_exists('usp_license_page')) :
function usp_license_page() {
	$license 	= get_option('usp_license_key');
	$status 	= get_option('usp_license_status'); ?>

	<?php if (isset($_GET['settings-updated'])) { ?>
		<div id="message" class="notice notice-success is-dismissible"><p><strong><?php esc_html_e('Settings saved.', 'usp-pro') ?></strong></p></div>
	<?php } ?>

	<div class="wrap">
		
		<h1 class="usp-title"><?php esc_html_e('USP Pro', 'usp-pro'); ?> <span><?php echo USP_PRO_VERSION; ?></span></h1>
		
		<h2><?php esc_html_e('USP Pro License', 'usp-pro'); ?></h2>
		
		<p class="intro">
			<?php echo esc_html__('Activate your license to enable USP Pro and free automatic updates.', 'usp-pro'); ?> 
			<a id="usp-toggle-steps" class="usp-toggle-steps" href="#usp-toggle-steps" title="<?php esc_attr_e('Show/hide instructions', 'usp-pro'); ?>"><?php esc_html_e('View the steps&nbsp;&raquo;', 'usp-pro'); ?></a>
		</p>
		<div class="usp-license-steps usp-toggle default-hidden">
			<p class="toggle-intro"><?php esc_html_e('Follow these steps to activate your license:', 'usp-pro'); ?></p>
			<ol>
				<li><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/get-license-key/"><?php esc_html_e('Get your License Key', 'usp-pro'); ?></a> </li>
				<li><?php esc_html_e('Enter your license in the field, &ldquo;License Key&rdquo;', 'usp-pro'); ?></li>
				<li><?php esc_html_e('Click &ldquo;Save Changes&rdquo;', 'usp-pro'); ?></li>
				<li><?php esc_html_e('Click &ldquo;Activate License&rdquo;', 'usp-pro'); ?></li>
			</ol>
			<p>
				<?php esc_html_e('If you need help,', 'usp-pro'); ?> 
				<a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/install-plugin/"><?php esc_html_e('check out this guide', 'usp-pro'); ?></a> <?php esc_html_e('and/or', 'usp-pro'); ?> 
				<a href="https://plugin-planet.com/get-license-key/#contact"><?php esc_html_e('contact us', 'usp-pro'); ?></a>.
			</p>
		</div>
		
		<h3 class="usp-activate-license"><?php esc_html_e('Activate License', 'usp-pro'); ?></h3>
		
		<form method="post" action="options.php">
			
			<?php settings_fields('usp_license_settings'); ?>
			
			<table class="form-table">
				<tbody>
					<?php if ($status === 'valid' || USP_PRO_CODE) : ?>
						
						<tr valign="top">	
							<th scope="row" valign="top"><?php esc_html_e('License Key', 'usp-pro'); ?></th>
							<td>
								<input type="hidden" id="usp_license_key" name="usp_license_key" value="<?php echo esc_attr($license); ?>" />
								<div class="usp-license-activated">
									<code><?php echo esc_attr($license); ?></code><br /><small><?php esc_html_e('Your USP Pro License is active.', 'usp-pro'); ?></small>
								</div>
							</td>
						</tr>
						<tr valign="top">	
							<th scope="row" valign="top"><?php esc_html_e('License Status', 'usp-pro'); ?></th>
							<td>
								<input type="submit" class="button-secondary" name="usp_license_deactivate" value="<?php esc_attr_e('Deactivate License', 'usp-pro'); ?>" />
								<?php wp_nonce_field('usp_license_nonce', 'usp_license_nonce'); ?>
							</td>
						</tr>
						
					<?php else : ?>
						
						<?php if (empty($license)) : ?>
						
						<tr valign="top">	
							<th scope="row" valign="top"><?php esc_html_e('License Key', 'usp-pro'); ?></th>
							<td>
								<input id="usp_license_key" name="usp_license_key" type="text" class="regular-text" value="<?php echo esc_attr($license); ?>" /><br />
								<small><label class="description" for="usp_license_key"><?php esc_html_e('Enter your License Key', 'usp-pro'); ?></label></small>
							</td>
						</tr>
						
						<?php else : ?>
						
						<tr valign="top">	
							<th scope="row" valign="top"><?php esc_html_e('License Key', 'usp-pro'); ?></th>
							<td>
								<input id="usp_license_key" name="usp_license_key" type="text" class="regular-text" value="<?php echo esc_attr($license); ?>" /><br />
								<div class="usp-license-deactivated">
									<small><label class="description" for="usp_license_key"><?php esc_html_e('Your license is inactive. To activate, click &ldquo;Activate License&rdquo;', 'usp-pro'); ?></label></small>
								</div>
							</td>
						</tr>
						<tr valign="top">	
							<th scope="row" valign="top"><?php esc_html_e('License Status', 'usp-pro'); ?></th>
							<td>
								<input type="submit" class="button-secondary" name="usp_license_activate" value="<?php esc_attr_e('Activate License', 'usp-pro'); ?>" />
								<?php wp_nonce_field('usp_license_nonce', 'usp_license_nonce'); ?>
							</td>
						</tr>
						
						<?php endif; ?>
						
					<?php endif; ?>
				</tbody>
			</table>	
			<?php submit_button(); ?>

			<p><a href="<?php get_admin_url(); ?>options-general.php?page=usp_options&tab=usp_license"><?php esc_html_e('Visit USP Pro Settings &raquo;', 'usp-pro'); ?></a></p>
		</form>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				$('.default-hidden').hide();
				$('.usp-toggle-steps').click(function(e){ e.preventDefault(); $('.usp-license-steps').slideToggle(300); });
			});
		</script>
	<?php 
}
endif;

// register option
if (!function_exists('usp_license_register_option')) :
function usp_license_register_option() {
	register_setting('usp_license_settings', 'usp_license_key', 'usp_sanitize_option');
}
add_action('admin_init', 'usp_license_register_option');
endif;

// sanitize option
if (!function_exists('usp_sanitize_option')) :
function usp_sanitize_option($new) {
	$old = get_option('usp_license_key');
	if ($old && $old != $new) delete_option('usp_license_status');
	return $new;
}
endif;

// activate license
if (!function_exists('usp_activate_license')) :
function usp_activate_license() {
	if (isset($_POST['usp_license_activate'])) {
	 	if (!check_admin_referer('usp_license_nonce', 'usp_license_nonce')) return;

		$license = trim(get_option('usp_license_key'));
		$api_params = array('edd_action' => 'activate_license', 'license' => $license, 'item_name' => urlencode(USP_ITEM_NAME));
		
		$add_args = add_query_arg($api_params, USP_PRO_URL);
		$response = wp_remote_get(esc_url_raw($add_args), array('timeout' => 15, 'sslverify' => false));

		if (is_wp_error($response)) return false;
		
		$license_data = json_decode(wp_remote_retrieve_body($response));
		update_option('usp_license_status', $license_data->license);
	}
}
add_action('admin_init', 'usp_activate_license');
endif;

// deactivate license
if (!function_exists('usp_deactivate_license')) :
function usp_deactivate_license() {
	if (isset($_POST['usp_license_deactivate'])) {
	 	if (!check_admin_referer('usp_license_nonce', 'usp_license_nonce')) return;

		$license = trim(get_option('usp_license_key'));
		$api_params = array('edd_action' => 'deactivate_license', 'license' => $license, 'item_name' => urlencode(USP_ITEM_NAME));
		
		$add_args = add_query_arg($api_params, USP_PRO_URL);
		$response = wp_remote_get(esc_url_raw($add_args), array('timeout' => 15, 'sslverify' => false));

		if (is_wp_error($response)) return false;
		
		$license_data = json_decode(wp_remote_retrieve_body($response));
		if ($license_data->license == 'deactivated') delete_option('usp_license_status');
	}
}
add_action('admin_init', 'usp_deactivate_license');
endif;

// check license
if (!function_exists('usp_check_license')) :
function usp_check_license() {
	$license = get_option('usp_license_key');
	$api_params = array( 
		'edd_action' => 'check_license', 
		'license'    => $license, 
		'item_name'  => urlencode(USP_ITEM_NAME) 
	);
	
	$add_args = add_query_arg($api_params, USP_PRO_URL);
	$response = wp_remote_get(esc_url_raw($add_args), array('timeout' => 15, 'sslverify' => false));
	
	if (is_wp_error($response)) return false;
	
	$license_data = json_decode(wp_remote_retrieve_body($response));
	if ($license_data->license == 'valid') {
		set_transient('license_status', 'valid', 60 * 60 * 24);
	} else {
		set_transient('license_status', 'invalid', 60 * 60 * 24);
	}
	$license_status = get_transient('license_status');
	return $license_status;
	exit;
}
endif;
