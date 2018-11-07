<?php // USP Pro - About/Infos (Settings Tab)

if (!defined('ABSPATH')) die();

/*
	About USP Pro
*/
if (!function_exists('usp_about_plugin')) : 
function usp_about_plugin() {
	$plugin_data = get_plugin_data(USP_PRO_PATH .'/usp-pro.php', false, false);
	
	if (isset($plugin_data['PluginURI']))  $plugin_data['PluginURI']  = '<a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro/">https://plugin-planet.com/usp-pro/</a>';
	if (isset($plugin_data['Author']))     $plugin_data['Author']     = '<a target="_blank" rel="noopener noreferrer" href="https://twitter.com/perishable" title="Jeff Starr @perishable on Twitter">Jeff Starr</a>';
	if (isset($plugin_data['AuthorURI']))  $plugin_data['AuthorURI']  = '<a target="_blank" rel="noopener noreferrer" href="https://monzillamedia.com/">Monzilla Media</a> '. esc_html__('and', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://perishablepress.com/">Perishable Press</a>';
	if (isset($plugin_data['DomainPath'])) $plugin_data['DomainPath'] = '<code>'. $plugin_data['DomainPath'] .'</code>';
	if (isset($plugin_data['Name']))       $plugin_data['Name']       = 'USP Pro (User Submitted Posts Pro)';
	
	if (isset($plugin_data['Network']))   unset($plugin_data['Network']); // MultiSite not yet supported
	if (isset($plugin_data['Title']))     unset($plugin_data['Title']); // redundant info, same as Name
	
	$about_plugin = '<ul>';
	foreach ($plugin_data as $key => $value) {
		$about_plugin .= '<li><strong>' . $key .':</strong> '. $value .'</li>';
	}
	$about_plugin .= '<li><strong>'. esc_html__('Donate Link:', 'usp-pro') .' </strong> <a target="_blank" rel="noopener noreferrer" href="https://monzillamedia.com/donate.html">https://monzillamedia.com/donate.html</a></li>';
	$about_plugin .= '<li><strong>'. esc_html__('Minimum WP Version:', 'usp-pro') .' </strong> '. USP_PRO_REQUIRES .'</li>';
	$about_plugin .= '<li><strong>'. esc_html__('Tested up to:', 'usp-pro') .' </strong>'. USP_PRO_TESTED .'</li>';
	$about_plugin .= '</ul>';
	
	return $about_plugin;
}
endif;

/*
	About WordPress
*/
if (!function_exists('usp_about_wp')) : 
function usp_about_wp() {
	global $wpdb;
	$wp_version = get_bloginfo('version');
	$current_user = wp_get_current_user();
	if (current_user_can('manage_options')) {
		$default = esc_html__('Undefined', 'usp-pro');
		$current_theme = wp_get_theme();
		$current_time = current_time('mysql');
		$active_plugins = count(get_option('active_plugins'));
		$language_locale = get_locale();
		
		$multisite = usp_is_multisite() ? esc_html__('MultiSite Installation', 'usp-pro') : esc_html__('Standard Installation', 'usp-pro');
		$home_url = get_home_url();
		$site_url = get_site_url();
		$content_url = content_url();
		$language = get_bloginfo('language');
		$language_dir = defined('WP_LANG_DIR') ? WP_LANG_DIR : $default;
		$temp_dir = !defined('WP_TEMP_DIR') ? $default : WP_TEMP_DIR;
		$default_theme = defined('WP_DEFAULT_THEME') ? WP_DEFAULT_THEME : $default;
		$memory_limit = defined('WP_MEMORY_LIMIT') ? WP_MEMORY_LIMIT : $default;
		$autosave_int = AUTOSAVE_INTERVAL == false ? $default : esc_html__('Enabled: ', 'usp-pro') . AUTOSAVE_INTERVAL . esc_html__(' seconds', 'usp-pro');
		$empty_trash = !defined('EMPTY_TRASH_DAYS') || EMPTY_TRASH_DAYS == 0  ? $default : esc_html__('Enabled: ', 'usp-pro') . EMPTY_TRASH_DAYS . esc_html__(' days', 'usp-pro');
		
		if (defined('UPLOADS')) $uploads_directory = UPLOADS;
		else $wp_uploads_directory = wp_upload_dir(); $uploads_directory = $wp_uploads_directory['baseurl'];
		
		$post_revisions = esc_html__('-- n/a --', 'usp-pro');
		if ((WP_POST_REVISIONS === false) || (WP_POST_REVISIONS === 0)) $post_revisions = $default;
		elseif ((WP_POST_REVISIONS === true) || (WP_POST_REVISIONS === -1)) $post_revisions = esc_html__('Enabled: no limit', 'usp-pro');
		else $post_revisions = WP_POST_REVISIONS;
		
		$filesystem_method = get_filesystem_method(array());
		$filesystem_message = $filesystem_method !== 'direct' ? esc_html__(' (FTP/SSH access only)', 'usp-pro') : esc_html__(' (Direct access allowed)', 'usp-pro');
	
		$about_wp = '<ul>';
		$about_wp .= '<li><strong>' . esc_html__('WordPress Version:', 'usp-pro') . ' </strong> ' . $wp_version . '</li>';
		$about_wp .= '<li><strong>' . esc_html__('Installation Type:', 'usp-pro') . ' </strong> ' . $multisite . '</li>';
		$about_wp .= '<li><strong>' . esc_html__('Update Method:', 'usp-pro') . ' </strong> ' . $filesystem_method . $filesystem_message . '</li>';
		$about_wp .= '<li><strong>' . esc_html__('WP Memory Limit:', 'usp-pro') . ' </strong> ' . $memory_limit . '</li>';
		$about_wp .= '<li><strong>' . esc_html__('Site Address (URL):', 'usp-pro') . ' </strong> ' . $home_url . '</li>';
		$about_wp .= '<li><strong>' . esc_html__('WordPress Address (URL):', 'usp-pro') . ' </strong> ' . $site_url . '</li>';
		$about_wp .= '<li><strong>' . esc_html__('Language:', 'usp-pro') . ' </strong> ' . $language . '</li>';
		$about_wp .= '<li><strong>' . esc_html__('Locale:', 'usp-pro') . ' </strong> ' . $language_locale . '</li>';
		$about_wp .= '<li><strong>' . esc_html__('WP Post Revisions:', 'usp-pro') . ' </strong> ' . $post_revisions . '</li>';
		$about_wp .= '<li><strong>' . esc_html__('WP Autosave Interval:', 'usp-pro') . ' </strong> ' . $autosave_int . '</li>';
		$about_wp .= '<li><strong>' . esc_html__('WP Empty Trash Interval:', 'usp-pro') . ' </strong> ' . $empty_trash . '</li>';
		$about_wp .= '<li><strong>' . esc_html__('Current Theme:', 'usp-pro') . ' </strong> ' . $current_theme . '</li>';
		$about_wp .= '<li><strong>' . esc_html__('WordPress Time:', 'usp-pro') . ' </strong> ' . $current_time . '</li>';
		$about_wp .= '<li><strong>' . esc_html__('Active Plugins:', 'usp-pro') . ' </strong> ' . $active_plugins . '</li>';
		$about_wp .= '<li><strong>' . esc_html__('WP Content Directory:', 'usp-pro') . ' </strong> ' . $content_url . '</li>';
		$about_wp .= '<li><strong>' . esc_html__('WP Uploads Directory:', 'usp-pro') . ' </strong> ' . $uploads_directory . '</li>';
		$about_wp .= '<li><strong>' . esc_html__('WP Language Directory:', 'usp-pro') . ' </strong> ' . $language_dir . '</li>';
		$about_wp .= '<li><strong>' . esc_html__('WP Temp Directory:', 'usp-pro') . ' </strong> ' . $temp_dir . '</li>';
		$about_wp .= '<li><strong>' . esc_html__('Default Theme:', 'usp-pro') . ' </strong> ' . $default_theme . '</li>';	
		$about_wp .= '<ul>';
	} else {
		$about_wp = esc_html__('Adminstrator-level access required to view WordPress information.', 'usp-pro');
	}
	return $about_wp;
}
endif;

/*
	WordPress Constants
	Thanks to Heiko Rabe's awesome plugin "WP System Health" @ http://www.code-styling.de/english/
*/
if (!function_exists('usp_about_constants')) : 
function usp_about_constants() {
	if (current_user_can('manage_options')) {
		$default = esc_html__('Undefined', 'usp-pro');
		$proxy_username = defined('WP_PROXY_USERNAME') ? esc_html__('Defined', 'usp-pro') . esc_html__(' (not displayed for security reasons)', 'usp-pro') : $default;
		$proxy_password = defined('WP_PROXY_PASSWORD') ? esc_html__('Defined', 'usp-pro') . esc_html__(' (not displayed for security reasons)', 'usp-pro') : $default;

		$wp_constants = '<ul>';
		$wp_constants .= '<li><strong>' . esc_html__('ABSPATH:', 'usp-pro') . ' </strong> ' . usp_return_defined('ABSPATH') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('SUBDOMAIN_INSTALL:', 'usp-pro') . ' </strong> ' . usp_return_defined('SUBDOMAIN_INSTALL') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('WPMU_PLUGIN_DIR:', 'usp-pro') . ' </strong> ' . usp_return_defined('WPMU_PLUGIN_DIR') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('WP_ALLOW_REPAIR:', 'usp-pro') . ' </strong> ' . usp_return_defined('WP_ALLOW_REPAIR') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('COOKIE_DOMAIN:', 'usp-pro') . ' </strong> ' . usp_return_defined('COOKIE_DOMAIN') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('VHOST:', 'usp-pro') . ' </strong> ' . usp_return_defined('VHOST') . '</li>';
	
		$wp_constants .= '<li><strong>' . esc_html__('WP_CACHE:', 'usp-pro') . ' </strong> ' . usp_return_defined('WP_CACHE') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('WP_CRON_LOCK_TIMEOUT:', 'usp-pro') . ' </strong> ' . usp_return_defined('WP_CRON_LOCK_TIMEOUT') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('DISABLE_WP_CRON:', 'usp-pro') . ' </strong> ' . usp_return_defined('DISABLE_WP_CRON') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('ALTERNATE_WP_CRON:', 'usp-pro') . ' </strong> ' . usp_return_defined('ALTERNATE_WP_CRON') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('SAVEQUERIES:', 'usp-pro') . ' </strong> ' . usp_return_defined('SAVEQUERIES') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('MEDIA_TRASH:', 'usp-pro') . ' </strong> ' . usp_return_defined('MEDIA_TRASH') . '</li>';
		
		$wp_constants .= '<li><strong>' . esc_html__('CUSTOM_USER_META_TABLE:', 'usp-pro') . ' </strong> ' . usp_return_defined('CUSTOM_USER_META_TABLE') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('CUSTOM_USER_TABLE:', 'usp-pro') . ' </strong> ' . usp_return_defined('CUSTOM_USER_TABLE') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('NOBLOGREDIRECT:', 'usp-pro') . ' </strong> ' . usp_return_defined('NOBLOGREDIRECT') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('WP_ACCESSIBLE_HOSTS:', 'usp-pro') . ' </strong> ' . usp_return_defined('WP_ACCESSIBLE_HOSTS') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('WP_HTTP_BLOCK_EXTERNAL:', 'usp-pro') . ' </strong> ' . usp_return_defined('WP_HTTP_BLOCK_EXTERNAL') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('FS_METHOD:', 'usp-pro') . ' </strong> ' . usp_return_defined('FS_METHOD') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('DO_NOT_UPGRADE_GLOBAL_TABLES:', 'usp-pro') . ' </strong> ' . usp_return_defined('DO_NOT_UPGRADE_GLOBAL_TABLES') . '</li>';
		
		$wp_constants .= '<li><strong>' . esc_html__('WP_PROXY_HOST:', 'usp-pro') . ' </strong> ' . usp_return_defined('WP_PROXY_HOST') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('WP_PROXY_PORT:', 'usp-pro') . ' </strong> ' . usp_return_defined('WP_PROXY_PORT') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('WP_PROXY_USERNAME:', 'usp-pro') . ' </strong> ' . $proxy_username . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('WP_PROXY_PASSWORD:', 'usp-pro') . ' </strong> ' . $proxy_password . '</li>';
		
		$wp_constants .= '<li><strong>' . esc_html__('FTP_BASE:', 'usp-pro') . ' </strong> ' . usp_return_defined('FTP_BASE') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('FTP_CONTENT_DIR:', 'usp-pro') . ' </strong> ' . usp_return_defined('FTP_CONTENT_DIR') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('FTP_PLUGIN_DIR:', 'usp-pro') . ' </strong> ' . usp_return_defined('FTP_PLUGIN_DIR') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('FTP_PUBKEY:', 'usp-pro') . ' </strong> ' . usp_return_defined('FTP_PUBKEY') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('FTP_PRIKEY:', 'usp-pro') . ' </strong> ' . usp_return_defined('FTP_PRIKEY') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('FTP_USER:', 'usp-pro') . ' </strong> ' . usp_return_defined('FTP_USER') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('FTP_PASS:', 'usp-pro') . ' </strong> ' . usp_return_defined('FTP_PASS') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('FTP_HOST:', 'usp-pro') . ' </strong> ' . usp_return_defined('FTP_HOST') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('FTP_SSL:', 'usp-pro') . ' </strong> ' . usp_return_defined('FTP_SSL') . '</li>';
		
		$wp_constants .= '<li><strong>' . esc_html__('WP_DEBUG:', 'usp-pro') . ' </strong> ' . usp_return_defined('WP_DEBUG') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('WP_DEBUG_LOG:', 'usp-pro') . ' </strong> ' . usp_return_defined('WP_DEBUG_LOG') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('WP_DEBUG_DISPLAY:', 'usp-pro') . ' </strong> ' . usp_return_defined('WP_DEBUG_DISPLAY') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('SCRIPT_DEBUG:', 'usp-pro') . ' </strong> ' . usp_return_defined('SCRIPT_DEBUG') . '</li>';
		
		$wp_constants .= '<li><strong>' . esc_html__('ENFORCE_GZIP:', 'usp-pro') . ' </strong> ' . usp_return_defined('ENFORCE_GZIP') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('COMPRESS_CSS:', 'usp-pro') . ' </strong> ' . usp_return_defined('COMPRESS_CSS') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('COMPRESS_SCRIPTS:', 'usp-pro') . ' </strong> ' . usp_return_defined('COMPRESS_SCRIPTS') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('CONCATENATE_SCRIPTS:', 'usp-pro') . ' </strong> ' . usp_return_defined('CONCATENATE_SCRIPTS') . '</li>';
		
		$wp_constants .= '<li><strong>' . esc_html__('DISALLOW_FILE_MODS:', 'usp-pro') . ' </strong> ' . usp_return_defined('DISALLOW_FILE_MODS') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('DISALLOW_FILE_EDIT:', 'usp-pro') . ' </strong> ' . usp_return_defined('DISALLOW_FILE_EDIT') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('DISALLOW_UNFILTERED_HTML:', 'usp-pro') . ' </strong> ' . usp_return_defined('DISALLOW_UNFILTERED_HTML') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('ALLOW_UNFILTERED_UPLOADS:', 'usp-pro') . ' </strong> ' . usp_return_defined('ALLOW_UNFILTERED_UPLOADS') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('FORCE_SSL_LOGIN:', 'usp-pro') . ' </strong> ' . usp_return_defined('FORCE_SSL_LOGIN') . '</li>';
		$wp_constants .= '<li><strong>' . esc_html__('FORCE_SSL_ADMIN:', 'usp-pro') . ' </strong> ' . usp_return_defined('FORCE_SSL_ADMIN') . '</li>';
		$wp_constants .= '</ul>';
	} else {
		$wp_constants = esc_html__('Adminstrator-level access required to view WordPress information.', 'usp-pro');
	}
	return $wp_constants;
}
endif;
	
/*
	About Server
	Thanks to Heiko Rabe's awesome plugin "WP System Health" @ http://www.code-styling.de/english/
*/
if (!function_exists('usp_about_server')) : 
function usp_about_server() {
	if (current_user_can('manage_options')) {
		global $wpdb;
		$default = esc_html__('Undefined', 'usp-pro');
		$max_execution_time = ini_get('max_execution_time'); 
		if ($max_execution_time > 1000) $max_execution_time /= 1000;
		
		$server_software  = isset($_SERVER['SERVER_SOFTWARE'])   && !empty($_SERVER['SERVER_SOFTWARE'])   ? $_SERVER['SERVER_SOFTWARE']   : $default;
		$server_signature = isset($_SERVER['SERVER_SIGNATURE'])  && !empty($_SERVER['SERVER_SIGNATURE'])  ? $_SERVER['SERVER_SIGNATURE']  : $default;
		$server_name      = isset($_SERVER['SERVER_NAME'])       && !empty($_SERVER['SERVER_NAME'])       ? $_SERVER['SERVER_NAME']       : $default;
		$server_address   = isset($_SERVER['SERVER_ADDR'])       && !empty($_SERVER['SERVER_ADDR'])       ? $_SERVER['SERVER_ADDR']       : $default;
		$server_port      = isset($_SERVER['SERVER_PORT'])       && !empty($_SERVER['SERVER_PORT'])       ? $_SERVER['SERVER_PORT']       : $default;
		$server_gateway   = isset($_SERVER['GATEWAY_INTERFACE']) && !empty($_SERVER['GATEWAY_INTERFACE']) ? $_SERVER['GATEWAY_INTERFACE'] : $default;
		
		$wp_server = '<ul>';
		$wp_server .= '<li><strong>' . esc_html__('Server Operating System:', 'usp-pro') . ' </strong> ' . php_uname() . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Server Software:', 'usp-pro') . ' </strong>' . $server_software . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Server Signature:', 'usp-pro') . ' </strong>' . $server_signature . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Server Name:', 'usp-pro') . ' </strong>' . $server_name . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Server Address:', 'usp-pro') . ' </strong>' . $server_address . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Server Port:', 'usp-pro') . ' </strong>' . $server_port . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Server Gateway:', 'usp-pro') . ' </strong>' . $server_gateway . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('PHP Version:', 'usp-pro') . ' </strong>' . PHP_VERSION . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Zend Version:', 'usp-pro') . ' </strong> ' . zend_version() . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Platform:', 'usp-pro') . ' </strong> ' . (PHP_INT_SIZE * 8) . '-bit' . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Loaded Extensions:', 'usp-pro') . ' </strong> ' . implode(', ', get_loaded_extensions()) . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('MySQL Server:', 'usp-pro') . ' </strong> ' . $wpdb->get_var("SELECT VERSION() AS version") . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Memory Limit:', 'usp-pro') . ' </strong> ' . size_format(usp_return_bytes(@ini_get('memory_limit'))) . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Server Time:', 'usp-pro') . ' </strong> ' . date('Y-m-d H:i:s') . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Include Path:', 'usp-pro') . ' </strong> ' . ini_get('include_path') . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Display Errors:', 'usp-pro') . ' </strong> ' . ini_get('display_errors') . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Register Globals:', 'usp-pro') . ' </strong> ' . ini_get('register_globals') . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Max Post Size:', 'usp-pro') . ' </strong> ' . ini_get('post_max_size') . ' (' . usp_return_bytes(ini_get('post_max_size')) .' bytes)</li>';
		$wp_server .= '<li><strong>' . esc_html__('Max Input Time:', 'usp-pro') . ' </strong> ' . ini_get('max_input_time') . esc_html__(' seconds', 'usp-pro') . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Max Execution Time:', 'usp-pro') . ' </strong> ' . $max_execution_time . esc_html__(' seconds', 'usp-pro') . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('File Uploads:', 'usp-pro') . ' </strong> ' . usp_return_bool(ini_get('file_uploads')) . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Temp Uploads Directory:', 'usp-pro') . ' </strong> ' . ini_get('upload_tmp_dir') . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Max Upload File Size:', 'usp-pro') . ' </strong> ' . size_format(usp_return_bytes(ini_get('upload_max_filesize'))) . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Multibyte Function Overload:', 'usp-pro') . ' </strong> ' . ini_get('mbstring.func_overload') . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Short Open Tag:', 'usp-pro') . ' </strong> ' . usp_return_bool(ini_get('short_open_tag')) . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('ASP Tags:', 'usp-pro') . ' </strong> ' . usp_return_bool(ini_get('asp_tags')) . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Zend Compatibility:', 'usp-pro') . ' </strong> ' . usp_return_bool(ini_get('zend.ze1_compatibility_mode')) . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Remote Open Files:', 'usp-pro') . ' </strong> ' . usp_return_bool(ini_get('allow_url_fopen')) . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Remote Include Files:', 'usp-pro') . ' </strong> ' . usp_return_bool(ini_get('allow_url_include')) . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('PHP Safe Mode:', 'usp-pro') . ' </strong> ' . usp_return_bool(ini_get('safe_mode')) . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Open Basedir:', 'usp-pro') . ' </strong> ' . ini_get('open_basedir') . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Disabled Functions:', 'usp-pro') . ' </strong> ' . ini_get('disable_functions') . '</li>';
		$wp_server .= '<li><strong>' . esc_html__('Disabled Classes:', 'usp-pro') . ' </strong> ' . ini_get('disable_classes') . '</li>';
		$wp_server .= '</ul>';
	} else {
		$wp_server = esc_html__('Adminstrator-level access required to view Server information.', 'usp-pro');
	}
	return $wp_server;
}
endif;

/*
	About User
*/
if (!function_exists('usp_about_user')) : 
function usp_about_user() {
	if (current_user_can('manage_options')) {
		
		$current_user = wp_get_current_user();
		$user_name = $current_user->user_login;
		$user_display = $current_user->display_name;
		$user_email = $current_user->user_email;
		
		$user_ip = usp_get_ip(true);
		$default = esc_html__('Undefined', 'usp-pro');
		
		$user_agent     = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT']            : $default;
		$remote_address = isset($_SERVER['REMOTE_ADDR'])     ? gethostbyaddr($_SERVER['REMOTE_ADDR']) : $default;
		$user_port      = isset($_SERVER['REMOTE_PORT'])     ? $_SERVER['REMOTE_PORT']                : $default;
		$user_prot      = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL']            : $default;
		$user_http      = isset($_SERVER['HTTP_CONNECTION']) ? $_SERVER['HTTP_CONNECTION']            : $default;
		
		$wp_user = '<ul>';
		$wp_user .= '<li><strong>' . esc_html__('Login/Username:', 'usp-pro') . ' </strong> ' . $user_name . '</li>';
		$wp_user .= '<li><strong>' . esc_html__('Display Name:', 'usp-pro') . ' </strong> ' . $user_display . '</li>';
		$wp_user .= '<li><strong>' . esc_html__('Email Address:', 'usp-pro') . ' </strong> ' . $user_email . '</li>';
		$wp_user .= '<li><strong>' . esc_html__('IP Address:', 'usp-pro') . ' </strong> ' . $user_ip . '</li>';
		$wp_user .= '<li><strong>' . esc_html__('User Agent:', 'usp-pro') . ' </strong> ' . $user_agent . '</li>';
		$wp_user .= '<li><strong>' . esc_html__('Remote Address:', 'usp-pro') . ' </strong> ' . $remote_address . '</li>';
		$wp_user .= '<li><strong>' . esc_html__('Remote Port:', 'usp-pro') . ' </strong> ' . $user_port . '</li>';
		$wp_user .= '<li><strong>' . esc_html__('Server Protocol:', 'usp-pro') . ' </strong> ' . $user_prot . '</li>';
		$wp_user .= '<li><strong>' . esc_html__('HTTP Connection:', 'usp-pro') . ' </strong> ' . $user_http . '</li>';
		$wp_user .= '</ul>';
	} else {
		$wp_user = esc_html__('Adminstrator-level access required to view Server information.', 'usp-pro');
	}
	return $wp_user;
}
endif;


