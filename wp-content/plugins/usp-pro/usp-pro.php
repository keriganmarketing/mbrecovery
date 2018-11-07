<?php 
/*
	Plugin Name: USP Pro
	Plugin URI: https://plugin-planet.com/usp-pro/
	Description: Create unlimited forms and let visitors submit content, register, and more from the front-end.
	Tags: submit, submitted, publish, published, generated, front-end, frontend, content, posts, upload, uploader
	Author: Jeff Starr
	Author URI: https://plugin-planet.com/
	Donate link: https://monzillamedia.com/donate.html
	Contributors: specialk
	Requires at least: 4.1
	Tested up to: 4.9
	Stable tag: 3.0
	Version: 3.0
	Requires PHP: 5.2
	Text Domain: usp-pro
	Domain Path: /languages
	
	License: The USP Pro license is comprised of two parts:
	
	* Part 1: Its PHP code is licensed under the GPL (v2 or later), like WordPress. More info @ http://www.gnu.org/licenses/
	* Part 2: Everything else (e.g., CSS, HTML, JavaScript, images, design) is licensed according to the purchased license. More info @ https://plugin-planet.com/usp-pro/
	
	Without prior written consent from Monzilla Media, you must NOT directly or indirectly: license, sub-license, sell, resell, or provide for free any aspect or component of Part 2.
	
	Further license information is available in the plugin directory, /license/, and online @ https://plugin-planet.com/wp/files/usp-pro/license.txt
	
	Upgrades: Your purchase of USP Pro includes free lifetime upgrades, which include new features, bug fixes, and other improvements. 
	
	Copyright 2018 Monzilla Media. All rights reserved.
*/

if (!defined('ABSPATH')) die();

define('USP_PRO_NAME', 'USP Pro');
define('USP_PRO_REQUIRES', '4.1');
define('USP_PRO_TESTED',   '5.0');
define('USP_PRO_VERSION',  '3.0');
define('USP_PRO_AUTHOR', 'Jeff Starr');
define('USP_PRO_URL', 'https://plugin-planet.com');
define('USP_PRO_PATH', WP_PLUGIN_DIR . '/usp-pro');
define('USP_PRO_FILE', plugin_basename(__FILE__));
define('USP_PRO_CODE', false);

if (!class_exists('USP_Pro')) {
	class USP_Pro {
		
		private $settings_about    = 'usp_about';
		private $settings_admin    = 'usp_admin';
		private $settings_advanced = 'usp_advanced';
		private $settings_general  = 'usp_general';
		private $settings_license  = 'usp_license';
		private $settings_style    = 'usp_style';
		private $settings_tools    = 'usp_tools';
		private $settings_uploads  = 'usp_uploads';
		private $settings_more     = 'usp_more';
		
		private $settings_page = 'usp_options';
		private $settings_tabs = array();

		public function __construct() {
			
			$target_page = isset($_REQUEST['_wp_http_referer']) ? strpos($_REQUEST['_wp_http_referer'], 'page=usp_options') : false;
			
			if ((isset($_GET['page']) && $_GET['page'] === 'usp_options') || $target_page !== false) { 
				
				add_action('admin_init', array(&$this, 'register_general_settings'));
				add_action('admin_init', array(&$this, 'register_style_settings'));
				add_action('admin_init', array(&$this, 'register_uploads_settings'));
				add_action('admin_init', array(&$this, 'register_admin_settings'));
				add_action('admin_init', array(&$this, 'register_advanced_settings'));
				add_action('admin_init', array(&$this, 'register_more_settings'));
				add_action('admin_init', array(&$this, 'register_tools_settings'));
				add_action('admin_init', array(&$this, 'register_about_settings'));
				add_action('admin_init', array(&$this, 'register_license_settings'));
				add_action('admin_init', array(&$this, 'load_settings'));
				
				require_once(sprintf("%s/inc/usp-about.php",     dirname(__FILE__)));
				require_once(sprintf("%s/inc/usp-backup.php",    dirname(__FILE__)));
				require_once(sprintf("%s/inc/usp-callbacks.php", dirname(__FILE__)));
				require_once(sprintf("%s/inc/usp-options.php",   dirname(__FILE__)));
				require_once(sprintf("%s/inc/usp-settings.php",  dirname(__FILE__)));
				require_once(sprintf("%s/inc/usp-tools.php",     dirname(__FILE__)));
				require_once(sprintf("%s/inc/usp-validate.php",  dirname(__FILE__)));
			}
			
			if (isset($_GET['activate']) && $_GET['activate'] === 'true') {
				add_action('admin_init', array(&$this, 'require_wp_version'));
			}
			
			add_action('plugins_loaded', array(&$this, 'usp_i18n_init'));
			add_action('admin_init', array(&$this, 'check_usp_free'));
			add_action('admin_init', array(&$this, 'register_post_status'));
			add_action('parse_query', array(&$this, 'add_status_clause'));
			add_action('restrict_manage_posts', array(&$this, 'add_post_filter_button'));
			
			add_filter('the_author', array(&$this, 'usp_replace_author'));
			add_filter('author_link', array(&$this, 'usp_replace_author_link'), 10, 3);
			
			add_action('admin_menu', array(&$this, 'add_admin_menus'));
			add_filter('plugin_action_links', array(&$this, 'plugin_link_settings'), 10, 2);
			add_filter('plugin_row_meta', array(&$this, 'add_plugin_links'), 10, 2);
			add_action('admin_enqueue_scripts', array(&$this, 'enqueue_admin_scripts'));
			add_action('admin_enqueue_scripts', array(&$this, 'add_admin_styles'));
			
			require_once(sprintf("%s/inc/usp-shortcodes.php", dirname(__FILE__)));
			$USP_Custom_Fields = new USP_Custom_Fields();
			
			require_once(sprintf("%s/inc/usp-forms.php", dirname(__FILE__)));
			$USP_Pro_Forms = new USP_Pro_Forms();
			
			require_once(sprintf("%s/inc/usp-posts.php", dirname(__FILE__)));
			$USP_Pro_Posts = new USP_Pro_Posts();
			
			require_once(sprintf("%s/inc/usp-process.php", dirname(__FILE__)));
			$USP_Pro_Process = new USP_Pro_Process();
			
			require_once(sprintf("%s/inc/usp-functions.php", dirname(__FILE__)));
			require_once(sprintf("%s/inc/usp-widget.php", dirname(__FILE__)));
			require_once(sprintf("%s/updates/usp-updates.php", dirname(__FILE__)));
			require_once(sprintf("%s/inc/usp-dashboard.php", dirname(__FILE__)));
			
		}
		
		public static function deactivate() {
			
			flush_rewrite_rules();
			
		}
		
		public static function activate() {
			
			require_once(sprintf("%s/inc/usp-forms.php", dirname(__FILE__)));
			USP_Pro_Forms::create_post_type();
			flush_rewrite_rules();
			
			require_once(sprintf("%s/inc/usp-posts.php", dirname(__FILE__)));
			USP_Pro_Posts::create_post_type();
			flush_rewrite_rules();
			
			$role_obj = get_role('administrator');
			$caps_form = USP_Pro_Forms::default_caps();
			$caps_post = USP_Pro_Posts::default_caps();
			
			foreach ($caps_form as $cap) $role_obj->add_cap($cap);
			foreach ($caps_post as $cap) $role_obj->add_cap($cap);
			
		}
		
		function check_usp_free() {
			
			if (function_exists('usp_checkForPublicSubmission')) { 
				
				$file = plugin_basename(__FILE__);
				
				if (is_plugin_active($file)) {
					
					deactivate_plugins($file);
					
					$msg  = '<strong>'. __('USP Pro', 'usp-pro') .'</strong> ';
					$msg .= esc_html__('should not be run with the free version of USP (there is no need for both plugins). ', 'usp-pro');
					$msg .= esc_html__('Please return to the', 'usp-pro') .' <a href="'. admin_url('plugins.php') .'">'. esc_html__('WP Admin Area', 'usp-pro') .'</a> ';
					$msg .= esc_html__('to deactivate the free version and try again. ', 'usp-pro');
					$msg .= esc_html__('Note: before uninstalling/deleting the free version, you may want to make a backup of your settings.', 'usp-pro');
					
					wp_die($msg);
					
				}
				
			}
			
		}
		
		function load_settings() {
			
			$this->admin_settings    = (array) get_option($this->settings_admin);
			$this->advanced_settings = (array) get_option($this->settings_advanced);
			$this->general_settings  = (array) get_option($this->settings_general);
			$this->style_settings    = (array) get_option($this->settings_style);
			$this->uploads_settings  = (array) get_option($this->settings_uploads);
			$this->more_settings     = (array) get_option($this->settings_more);
			$this->tools_settings    = (array) get_option($this->settings_tools);
			//
			$this->admin_settings    = wp_parse_args($this->admin_settings,    $this->admin_defaults());
			$this->advanced_settings = wp_parse_args($this->advanced_settings, $this->advanced_defaults());
			$this->general_settings  = wp_parse_args($this->general_settings,  $this->general_defaults());
			$this->style_settings    = wp_parse_args($this->style_settings,    $this->style_defaults());
			$this->uploads_settings  = wp_parse_args($this->uploads_settings,  $this->uploads_defaults());
			$this->more_settings     = wp_parse_args($this->more_settings,     $this->more_defaults());
			$this->tools_settings    = wp_parse_args($this->tools_settings,    $this->tools_defaults());
			
		}
		
		function require_wp_version() {
			$wp_version = get_bloginfo('version');
			if (version_compare($wp_version, USP_PRO_REQUIRES, '<')) {
				if (is_plugin_active(USP_PRO_FILE)) {
					deactivate_plugins(USP_PRO_FILE);
					$msg = '<strong>'. USP_PRO_NAME .'</strong> '. esc_html__('has been deactivated because it requires WordPress version ', 'usp-pro') . USP_PRO_REQUIRES . esc_html__(' or higher. ', 'usp-pro');
					$msg .= esc_html__('Please', 'usp-pro') .' <a href="'. admin_url() .'">'. esc_html__('return to the Admin Area', 'usp-pro') .'</a> '. esc_html__('to upgrade WordPress and try again.', 'usp-pro');
					wp_die($msg);
				}
			}
		}
		
		function usp_i18n_init() {
			
			$domain = 'usp-pro';
			
			$locale = apply_filters('usp_i18n_locale', get_locale(), $domain);
			
			$dir    = trailingslashit(WP_LANG_DIR);
			
			$file   = $domain .'-'. $locale .'.mo';
			
			$path_1 = $dir . $file;
			
			$path_2 = $dir . $domain .'/'. $file;
			
			$path_3 = $dir .'plugins/'. $file;
			
			$path_4 = $dir .'plugins/'. $domain .'/'. $file;
			
			$paths = array($path_1, $path_2, $path_3, $path_4);
			
			foreach ($paths as $path) {
				
				if ($loaded = load_textdomain($domain, $path)) {
					
					return $loaded;
					
				} else {
					
					return load_plugin_textdomain($domain, false, basename(dirname(__FILE__)) .'/languages');
					
				}
				
			}
			
		}
		
		function register_post_status(){
			global $usp_general;
			$custom_status = esc_html__('Undefined', 'usp-pro');
			if (!empty($usp_general['custom_status'])) {
				$custom_status = $usp_general['custom_status'];
			}
			$enable_status = $usp_general['number_approved'];
			if ($enable_status == -3) {
				register_post_status($custom_status, array(
					'label'                     => $custom_status,
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop($custom_status .' <span class="count">(%s)</span>', $custom_status .' <span class="count">(%s)</span>'),
					// note: Custom Post Status is not yet fully implemented in WP, see: https://codex.wordpress.org/Function_Reference/register_post_status
					// at this point, we can register CPS and use them for submitted posts, but they will not be displayed in the Admin Area until WP adds it	 
				));
			}
		}
		
		function add_status_clause($wp_query) {
			global $usp_general, $pagenow;
			
			if (!is_admin()) return;
			if ($pagenow !== 'edit.php') return;
			
			if (isset($_GET['user_submitted']) && $_GET['user_submitted'] === '1') {
				set_query_var('meta_key', 'is_submission');
				set_query_var('meta_value', 1);
			}
		}
		
		function add_post_filter_button() {
			global $usp_advanced, $pagenow, $typenow, $post_status;
			
			if (!is_admin()) return;
			if ($pagenow !== 'edit.php') return;
			if ($post_status === 'trash') return;
			
			$other_cpt = isset($usp_advanced['other_type']) ? $usp_advanced['other_type'] : false;
			
			if (($typenow === 'post' || $typenow === 'page' || $typenow === 'usp_post') || $typenow === $other_cpt) {
				echo '<a id="usp-admin-filter" class="button" href="'. admin_url('edit.php?post_type='. $typenow .'&user_submitted=1') .'">'. esc_html__('USP', 'usp-pro') .'</a>';
			}
		}
		
		function usp_replace_author($author) {
			global $post, $usp_general;
			if (is_object($post)) {
				
				$is_submission = get_post_meta($post->ID, 'is_submission', true);
				$usp_author    = get_post_meta($post->ID, 'usp-author', true);
				
				if (isset($usp_general['replace_author']) && $usp_general['replace_author']) {
					if ($is_submission && !empty($usp_author)) return $usp_author;
				}
			}
			return $author;
		}
		
		function usp_replace_author_link($link, $author_id, $author_nicename) {
			global $post, $usp_general;
			if (is_object($post)) {
				
				$is_submission = get_post_meta($post->ID, 'is_submission', true);
				$usp_url       = get_post_meta($post->ID, 'usp-url', true);
				
				if (isset($usp_general['replace_author']) && $usp_general['replace_author']) {
					if ($is_submission && !empty($usp_url)) return $usp_url;
				}
			}
			return $link;
		}
		
		public static function get_user_infos() {
			global $current_user;
			if ($current_user) $admin_id = $current_user->ID;
			else $admin_id = '1';
			$admin_name  = get_bloginfo('name');
			$admin_email = get_bloginfo('admin_email');
			$admin_url   = home_url();
			$user_info = array('admin_id' => $admin_id, 'admin_name' => $admin_name, 'admin_email' => $admin_email, 'admin_url' => $admin_url);
			return $user_info;
		}
		
		public static function admin_defaults() {
			require_once(sprintf("%s/inc/usp-defaults.php", dirname(__FILE__)));
			return usp_default_options_admin();
		}
		
		public static function advanced_defaults() {
			require_once(sprintf("%s/inc/usp-defaults.php", dirname(__FILE__)));
			return usp_default_options_advanced();
		}
		
		public static function more_defaults() {
			require_once(sprintf("%s/inc/usp-defaults.php", dirname(__FILE__)));
			return usp_default_options_more();
		}
		
		public static function general_defaults() {
			require_once(sprintf("%s/inc/usp-defaults.php", dirname(__FILE__)));
			return usp_default_options_general();
		}
		
		public static function style_defaults() {
			require_once(sprintf("%s/inc/usp-defaults.php", dirname(__FILE__)));
			return usp_default_options_style();
		}
		
		public static function uploads_defaults() {
			require_once(sprintf("%s/inc/usp-defaults.php", dirname(__FILE__)));
			return usp_default_options_uploads();
		}
		
		public static function tools_defaults() {
			require_once(sprintf("%s/inc/usp-defaults.php", dirname(__FILE__)));
			return usp_default_options_tools();
		}
		
		
		
		// GENERAL SETTINGS
		
		function register_general_settings() {
			
			$this->settings_tabs[$this->settings_general] = esc_html__('General', 'usp-pro');
			register_setting($this->settings_general, $this->settings_general, 'validate_general');
			add_settings_section('section_general_0', '', 'section_general_0_desc', $this->settings_general);
			
			// 1
			add_settings_section('section_general_1', esc_html__('Basic Settings', 'usp-pro'), 'section_general_1_desc', $this->settings_general);
			add_settings_field('number_approved',  esc_html__('Default Post Status', 'usp-pro'),      array(&$this, 'callback_dropdown'),   $this->settings_general, 'section_general_1', array('id' => 'number_approved',  'type' => 'general'));
			add_settings_field('custom_status',    esc_html__('Custom Post Status', 'usp-pro'),       array(&$this, 'callback_input_text'), $this->settings_general, 'section_general_1', array('id' => 'custom_status',    'type' => 'general'));
			add_settings_field('redirect_post',    esc_html__('Redirect to Post', 'usp-pro'),         array(&$this, 'callback_checkbox'),   $this->settings_general, 'section_general_1', array('id' => 'redirect_post',    'type' => 'general'));
			add_settings_field('enable_stats',     esc_html__('Enable Basic Statistics', 'usp-pro'),  array(&$this, 'callback_checkbox'),   $this->settings_general, 'section_general_1', array('id' => 'enable_stats',     'type' => 'general'));
			add_settings_field('character_min',    esc_html__('Min Character Limit', 'usp-pro'),      array(&$this, 'callback_input_text'), $this->settings_general, 'section_general_1', array('id' => 'character_min',    'type' => 'general'));
			add_settings_field('character_max',    esc_html__('Max Character Limit', 'usp-pro'),      array(&$this, 'callback_input_text'), $this->settings_general, 'section_general_1', array('id' => 'character_max',    'type' => 'general'));
			add_settings_field('titles_unique',    esc_html__('Unique Post Titles', 'usp-pro'),       array(&$this, 'callback_checkbox'),   $this->settings_general, 'section_general_1', array('id' => 'titles_unique',    'type' => 'general'));
			add_settings_field('content_unique',   esc_html__('Unique Post Content', 'usp-pro'),      array(&$this, 'callback_checkbox'),   $this->settings_general, 'section_general_1', array('id' => 'content_unique',   'type' => 'general'));
			// 2
			add_settings_section('section_general_2', esc_html__('Memory Settings', 'usp-pro'), 'section_general_2_desc', $this->settings_general);
			add_settings_field('sessions_on',      esc_html__('Remember Form Values', 'usp-pro'),     array(&$this, 'callback_checkbox'),   $this->settings_general, 'section_general_2', array('id' => 'sessions_on',      'type' => 'general'));
			add_settings_field('sessions_scope',   esc_html__('Memory Duration', 'usp-pro'),          array(&$this, 'callback_checkbox'),   $this->settings_general, 'section_general_2', array('id' => 'sessions_scope',   'type' => 'general'));
			add_settings_field('sessions_default', esc_html__('Memory Default', 'usp-pro'),           array(&$this, 'callback_checkbox'),   $this->settings_general, 'section_general_2', array('id' => 'sessions_default', 'type' => 'general'));
			// 3
			add_settings_section('section_general_3', esc_html__('User Settings', 'usp-pro'), 'section_general_3_desc', $this->settings_general);
			add_settings_field('assign_author',   esc_html__('Default Assigned Author', 'usp-pro'),  array(&$this, 'callback_dropdown'), $this->settings_general, 'section_general_3', array('id' => 'assign_author',   'type' => 'general'));
			add_settings_field('assign_role',     esc_html__('Default Assigned Role', 'usp-pro'),    array(&$this, 'callback_dropdown'), $this->settings_general, 'section_general_3', array('id' => 'assign_role',     'type' => 'general'));
			add_settings_field('use_author',      esc_html__('Use Registered Author', 'usp-pro'),    array(&$this, 'callback_checkbox'), $this->settings_general, 'section_general_3', array('id' => 'use_author',      'type' => 'general'));
			add_settings_field('replace_author',  esc_html__('Replace Author &amp; URL', 'usp-pro'), array(&$this, 'callback_checkbox'), $this->settings_general, 'section_general_3', array('id' => 'replace_author',  'type' => 'general'));
			// 4
			add_settings_section('section_general_4', esc_html__('Antispam/Captcha', 'usp-pro'), 'section_general_4_desc', $this->settings_general);
			add_settings_field('captcha_question',  esc_html__('Challenge Question', 'usp-pro'),    array(&$this, 'callback_input_text'), $this->settings_general, 'section_general_4', array('id' => 'captcha_question',  'type' => 'general'));
			add_settings_field('captcha_response',  esc_html__('Challenge Response', 'usp-pro'),    array(&$this, 'callback_input_text'), $this->settings_general, 'section_general_4', array('id' => 'captcha_response',  'type' => 'general'));
			add_settings_field('captcha_casing',    esc_html__('Case-sensitivity', 'usp-pro'),      array(&$this, 'callback_checkbox'),   $this->settings_general, 'section_general_4', array('id' => 'captcha_casing',    'type' => 'general'));
			add_settings_field('recaptcha_public',  esc_html__('reCAPTCHA Public Key', 'usp-pro'),  array(&$this, 'callback_input_text'), $this->settings_general, 'section_general_4', array('id' => 'recaptcha_public',  'type' => 'general'));
			add_settings_field('recaptcha_private', esc_html__('reCAPTCHA Private Key', 'usp-pro'), array(&$this, 'callback_input_text'), $this->settings_general, 'section_general_4', array('id' => 'recaptcha_private', 'type' => 'general'));
			add_settings_field('recaptcha_version', esc_html__('reCAPTCHA Version', 'usp-pro'),     array(&$this, 'callback_select'),     $this->settings_general, 'section_general_4', array('id' => 'recaptcha_version', 'type' => 'general'));
			// 5
			add_settings_section('section_general_5', esc_html__('Category Settings', 'usp-pro'), 'section_general_5_desc', $this->settings_general);
			add_settings_field('cats_menu',     esc_html__('Category Menu', 'usp-pro'),         array(&$this, 'callback_radio'),      $this->settings_general, 'section_general_5', array('id' => 'cats_menu',     'type' => 'general'));
			add_settings_field('cats_multiple', esc_html__('Multiple Categories', 'usp-pro'),   array(&$this, 'callback_checkbox'),   $this->settings_general, 'section_general_5', array('id' => 'cats_multiple', 'type' => 'general'));
			add_settings_field('cats_nested',   esc_html__('Nested Categories', 'usp-pro'),     array(&$this, 'callback_checkbox'),   $this->settings_general, 'section_general_5', array('id' => 'cats_nested',   'type' => 'general'));
			add_settings_field('hidden_cats',   esc_html__('Hide Category Field', 'usp-pro'),   array(&$this, 'callback_checkbox'),   $this->settings_general, 'section_general_5', array('id' => 'hidden_cats',   'type' => 'general'));
			add_settings_field('use_cat',       esc_html__('Required Categories', 'usp-pro'),   array(&$this, 'callback_checkbox'),   $this->settings_general, 'section_general_5', array('id' => 'use_cat',       'type' => 'general'));
			add_settings_field('use_cat_id',    esc_html__('Required Category IDs', 'usp-pro'), array(&$this, 'callback_input_text'), $this->settings_general, 'section_general_5', array('id' => 'use_cat_id',    'type' => 'general'));
			add_settings_field('categories',    esc_html__('Post Categories', 'usp-pro'),       array(&$this, 'callback_checkboxes'), $this->settings_general, 'section_general_5', array('id' => 'categories',    'type' => 'general'));
			// 6
			add_settings_section('section_general_6', esc_html__('Tag Settings', 'usp-pro'), 'section_general_6_desc', $this->settings_general);
			add_settings_field('tags_menu',     esc_html__('Tag Menu', 'usp-pro'),            array(&$this, 'callback_radio'),      $this->settings_general, 'section_general_6', array('id' => 'tags_menu',     'type' => 'general'));
			add_settings_field('tags_multiple', esc_html__('Allow Multiple Tags', 'usp-pro'), array(&$this, 'callback_checkbox'),   $this->settings_general, 'section_general_6', array('id' => 'tags_multiple', 'type' => 'general'));
			add_settings_field('hidden_tags',   esc_html__('Hide Tags Field', 'usp-pro'),     array(&$this, 'callback_checkbox'),   $this->settings_general, 'section_general_6', array('id' => 'hidden_tags',   'type' => 'general'));
			add_settings_field('tags_order',    esc_html__('Tag Order', 'usp-pro'),           array(&$this, 'callback_radio'),      $this->settings_general, 'section_general_6', array('id' => 'tags_order',    'type' => 'general'));
			add_settings_field('tags',          esc_html__('Post Tags', 'usp-pro'),           array(&$this, 'callback_checkboxes'), $this->settings_general, 'section_general_6', array('id' => 'tags',          'type' => 'general'));
			add_settings_field('tags_number',   esc_html__('Number of Tags', 'usp-pro'),      array(&$this, 'callback_input_text'), $this->settings_general, 'section_general_6', array('id' => 'tags_number',   'type' => 'general'));
			add_settings_field('tags_empty',    esc_html__('Hide Empty Tags', 'usp-pro'),     array(&$this, 'callback_checkbox'),   $this->settings_general, 'section_general_6', array('id' => 'tags_empty',    'type' => 'general'));
			// 7
			add_settings_section('section_general_7', esc_html__('Extra Form Security', 'usp-pro'), 'section_general_7_desc', $this->settings_general);
			add_settings_field('enable_form_lock',  esc_html__('Enable this feature', 'usp-pro'),     array(&$this, 'callback_checkbox'),   $this->settings_general, 'section_general_7', array('id' => 'enable_form_lock',  'type' => 'general'));
			add_settings_field('submit_form_ids',   esc_html__('Post-Submission Forms', 'usp-pro'),   array(&$this, 'callback_input_text'), $this->settings_general, 'section_general_7', array('id' => 'submit_form_ids',   'type' => 'general'));
			add_settings_field('register_form_ids', esc_html__('User-Registration Forms', 'usp-pro'), array(&$this, 'callback_input_text'), $this->settings_general, 'section_general_7', array('id' => 'register_form_ids', 'type' => 'general'));
			add_settings_field('contact_form',      esc_html__('Contact Forms', 'usp-pro'),           array(&$this, 'callback_input_text'), $this->settings_general, 'section_general_7', array('id' => 'contact_form',      'type' => 'general'));
		
		}
		
		// STYLE SETTINGS
		
		function register_style_settings() {
			
			$this->settings_tabs[$this->settings_style] = esc_html__('CSS/JS', 'usp-pro');
			register_setting($this->settings_style, $this->settings_style, 'validate_style');
			add_settings_section('section_style_0', '', 'section_style_0_desc', $this->settings_style);
			
			// 1
			add_settings_section('section_style_1', 'CSS/Styles', 'section_style_1_desc', $this->settings_style);
			add_settings_field('form_style',   esc_html__('Select Form Style', 'usp-pro'),   array(&$this, 'callback_radio'),    $this->settings_style, 'section_style_1', array('id' => 'form_style',   'type' => 'style'));
			add_settings_field('style_simple', esc_html__('Simple Style', 'usp-pro'),        array(&$this, 'callback_textarea'), $this->settings_style, 'section_style_1', array('id' => 'style_simple', 'type' => 'style'));
			add_settings_field('style_min',    esc_html__('Minimal Style', 'usp-pro'),       array(&$this, 'callback_textarea'), $this->settings_style, 'section_style_1', array('id' => 'style_min',    'type' => 'style'));
			add_settings_field('style_small',  esc_html__('Smaller Form', 'usp-pro'),        array(&$this, 'callback_textarea'), $this->settings_style, 'section_style_1', array('id' => 'style_small',  'type' => 'style'));
			add_settings_field('style_large',  esc_html__('Larger Form', 'usp-pro'),         array(&$this, 'callback_textarea'), $this->settings_style, 'section_style_1', array('id' => 'style_large',  'type' => 'style'));
			add_settings_field('style_custom', esc_html__('Custom Style', 'usp-pro'),        array(&$this, 'callback_textarea'), $this->settings_style, 'section_style_1', array('id' => 'style_custom', 'type' => 'style'));
			add_settings_field('include_css',  esc_html__('External Stylesheet', 'usp-pro'), array(&$this, 'callback_checkbox'), $this->settings_style, 'section_style_1', array('id' => 'include_css',  'type' => 'style'));
			// 2
			add_settings_section('section_style_2', 'JavaScript/jQuery', 'section_style_2_desc', $this->settings_style);
			add_settings_field('script_custom',   esc_html__('Custom JavaScript', 'usp-pro'),      array(&$this, 'callback_textarea'), $this->settings_style, 'section_style_2', array('id' => 'script_custom',   'type' => 'style'));
			add_settings_field('include_js',      esc_html__('Include USP JavaScript', 'usp-pro'), array(&$this, 'callback_checkbox'), $this->settings_style, 'section_style_2', array('id' => 'include_js',      'type' => 'style'));
			add_settings_field('include_parsley', esc_html__('Include Parsley.js', 'usp-pro'),     array(&$this, 'callback_checkbox'), $this->settings_style, 'section_style_2', array('id' => 'include_parsley', 'type' => 'style'));
			// 3
			add_settings_section('section_style_3', 'Optimization', 'section_style_3_desc', $this->settings_style);
			add_settings_field('include_url', esc_html__('Targeted Loading', 'usp-pro'), array(&$this, 'callback_input_text'), $this->settings_style, 'section_style_3', array('id' => 'include_url', 'type' => 'style'));
		
		}
		
		// UPLOADS SETTINGS
		
		function register_uploads_settings() {
			
			$this->settings_tabs[$this->settings_uploads] = esc_html__('Uploads', 'usp-pro');
			register_setting($this->settings_uploads, $this->settings_uploads, 'validate_uploads');
			add_settings_section('section_uploads_0', '', 'section_uploads_0_desc', $this->settings_uploads);
			
			add_settings_section('section_uploads_1', 'File Uploads', 'section_uploads_1_desc', $this->settings_uploads);
			add_settings_field('post_images',     esc_html__('Auto-Display Images', 'usp-pro'),   array(&$this, 'callback_radio'),      $this->settings_uploads, 'section_uploads_1', array('id' => 'post_images',     'type' => 'uploads'));
			add_settings_field('display_size',    esc_html__('Auto-Display Size', 'usp-pro'),     array(&$this, 'callback_select'),     $this->settings_uploads, 'section_uploads_1', array('id' => 'display_size',    'type' => 'uploads'));
			add_settings_field('min_files',       esc_html__('Min number of files', 'usp-pro'),   array(&$this, 'callback_select'),     $this->settings_uploads, 'section_uploads_1', array('id' => 'min_files',       'type' => 'uploads'));
			add_settings_field('max_files',       esc_html__('Max number of files', 'usp-pro'),   array(&$this, 'callback_select'),     $this->settings_uploads, 'section_uploads_1', array('id' => 'max_files',       'type' => 'uploads'));
			add_settings_field('files_allow',     esc_html__('Allowed File Types', 'usp-pro'),    array(&$this, 'callback_input_text'), $this->settings_uploads, 'section_uploads_1', array('id' => 'files_allow',     'type' => 'uploads'));
			add_settings_field('min_size',        esc_html__('Minimum file size', 'usp-pro'),     array(&$this, 'callback_input_text'), $this->settings_uploads, 'section_uploads_1', array('id' => 'min_size',        'type' => 'uploads'));
			add_settings_field('max_size',        esc_html__('Maximum file size', 'usp-pro'),     array(&$this, 'callback_input_text'), $this->settings_uploads, 'section_uploads_1', array('id' => 'max_size',        'type' => 'uploads'));
			add_settings_field('min_width',       esc_html__('Min width for images', 'usp-pro'),  array(&$this, 'callback_input_text'), $this->settings_uploads, 'section_uploads_1', array('id' => 'min_width',       'type' => 'uploads'));
			add_settings_field('max_width',       esc_html__('Max width for images', 'usp-pro'),  array(&$this, 'callback_input_text'), $this->settings_uploads, 'section_uploads_1', array('id' => 'max_width',       'type' => 'uploads'));
			add_settings_field('min_height',      esc_html__('Min height for images', 'usp-pro'), array(&$this, 'callback_input_text'), $this->settings_uploads, 'section_uploads_1', array('id' => 'min_height',      'type' => 'uploads'));
			add_settings_field('max_height',      esc_html__('Max height for images', 'usp-pro'), array(&$this, 'callback_input_text'), $this->settings_uploads, 'section_uploads_1', array('id' => 'max_height',      'type' => 'uploads'));
			add_settings_field('featured_image',  esc_html__('Featured Images', 'usp-pro'),       array(&$this, 'callback_checkbox'),   $this->settings_uploads, 'section_uploads_1', array('id' => 'featured_image',  'type' => 'uploads'));
			add_settings_field('featured_key',    esc_html__('Featured Image Key', 'usp-pro'),    array(&$this, 'callback_input_text'), $this->settings_uploads, 'section_uploads_1', array('id' => 'featured_key',    'type' => 'uploads'));
			add_settings_field('unique_filename', esc_html__('Unique File Names', 'usp-pro'),     array(&$this, 'callback_checkbox'),   $this->settings_uploads, 'section_uploads_1', array('id' => 'unique_filename', 'type' => 'uploads'));
			add_settings_field('user_shortcodes', esc_html__('User Shortcodes', 'usp-pro'),       array(&$this, 'callback_checkbox'),   $this->settings_uploads, 'section_uploads_1', array('id' => 'user_shortcodes', 'type' => 'uploads'));
			add_settings_field('enable_media',    esc_html__('Non-Admin Media', 'usp-pro'),       array(&$this, 'callback_checkbox'),   $this->settings_uploads, 'section_uploads_1', array('id' => 'enable_media',    'type' => 'uploads'));
			add_settings_field('square_image',    esc_html__('Require Square Images', 'usp-pro'), array(&$this, 'callback_checkbox'),   $this->settings_uploads, 'section_uploads_1', array('id' => 'square_image',    'type' => 'uploads'));
			add_settings_field('auto-rotate',     esc_html__('Auto-Rotate Images', 'usp-pro'),    array(&$this, 'callback_checkbox'),   $this->settings_uploads, 'section_uploads_1', array('id' => 'auto-rotate',     'type' => 'uploads'));
			
		}
		
		// ADMIN SETTINGS
		
		function register_admin_settings() {
			
			$this->settings_tabs[$this->settings_admin] = esc_html__('Admin', 'usp-pro');
			register_setting($this->settings_admin, $this->settings_admin, 'validate_admin');
			add_settings_section('section_admin_0', '', 'section_admin_0_desc', $this->settings_admin);
			
			// 1
			add_settings_section('section_admin_1', 'Email Settings', 'section_admin_1_desc', $this->settings_admin);
			add_settings_field('admin_email', esc_html__('Admin Email To', 'usp-pro'),   array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_1', array('id' => 'admin_email', 'type' => 'admin'));
			add_settings_field('admin_from',  esc_html__('Admin Email From', 'usp-pro'), array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_1', array('id' => 'admin_from',  'type' => 'admin'));
			add_settings_field('admin_name',  esc_html__('Admin Email Name', 'usp-pro'), array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_1', array('id' => 'admin_name',  'type' => 'admin'));
			// 2
			add_settings_section('section_admin_2', 'Email Alerts', 'section_admin_2_desc', $this->settings_admin);
			add_settings_field('send_mail',   esc_html__('Email Alerts', 'usp-pro'), array(&$this, 'callback_radio'),  $this->settings_admin, 'section_admin_2', array('id' => 'send_mail',   'type' => 'admin'));
			add_settings_field('mail_format', esc_html__('Email Format', 'usp-pro'), array(&$this, 'callback_select'), $this->settings_admin, 'section_admin_2', array('id' => 'mail_format', 'type' => 'admin'));
			// 3
			add_settings_section('section_admin_3', 'Email Alerts for Admin', 'section_admin_3_desc', $this->settings_admin);
			add_settings_field('send_mail_admin',         esc_html__('Submission Alerts', 'usp-pro'),        array(&$this, 'callback_checkbox'),   $this->settings_admin, 'section_admin_3', array('id' => 'send_mail_admin',         'type' => 'admin'));
			add_settings_field('send_approval_admin',     esc_html__('Approval Alerts', 'usp-pro'),          array(&$this, 'callback_checkbox'),   $this->settings_admin, 'section_admin_3', array('id' => 'send_approval_admin',     'type' => 'admin'));
			add_settings_field('send_denied_admin',       esc_html__('Denied Alerts', 'usp-pro'),            array(&$this, 'callback_checkbox'),   $this->settings_admin, 'section_admin_3', array('id' => 'send_denied_admin',       'type' => 'admin'));
			add_settings_field('send_scheduled_admin',    esc_html__('Scheduled Alerts', 'usp-pro'),         array(&$this, 'callback_checkbox'),   $this->settings_admin, 'section_admin_3', array('id' => 'send_scheduled_admin',    'type' => 'admin'));
			add_settings_field('alert_subject_admin',     esc_html__('Submission Alert Subject', 'usp-pro'), array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_3', array('id' => 'alert_subject_admin',     'type' => 'admin'));
			add_settings_field('post_alert_admin',        esc_html__('Submission Alert Message', 'usp-pro'), array(&$this, 'callback_textarea'),   $this->settings_admin, 'section_admin_3', array('id' => 'post_alert_admin',        'type' => 'admin'));
			add_settings_field('approval_subject_admin',  esc_html__('Approval Alert Subject', 'usp-pro'),   array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_3', array('id' => 'approval_subject_admin',  'type' => 'admin'));
			add_settings_field('approval_message_admin',  esc_html__('Approval Alert Message', 'usp-pro'),   array(&$this, 'callback_textarea'),   $this->settings_admin, 'section_admin_3', array('id' => 'approval_message_admin',  'type' => 'admin'));
			add_settings_field('denied_subject_admin',    esc_html__('Denied Alert Subject', 'usp-pro'),     array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_3', array('id' => 'denied_subject_admin',    'type' => 'admin'));
			add_settings_field('denied_message_admin',    esc_html__('Denied Alert Message', 'usp-pro'),     array(&$this, 'callback_textarea'),   $this->settings_admin, 'section_admin_3', array('id' => 'denied_message_admin',    'type' => 'admin'));
			add_settings_field('scheduled_subject_admin', esc_html__('Scheduled Alert Subject', 'usp-pro'),  array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_3', array('id' => 'scheduled_subject_admin', 'type' => 'admin'));
			add_settings_field('scheduled_message_admin', esc_html__('Scheduled Alert Message', 'usp-pro'),  array(&$this, 'callback_textarea'),   $this->settings_admin, 'section_admin_3', array('id' => 'scheduled_message_admin', 'type' => 'admin'));
			add_settings_field('cc_submit',               esc_html__('CC Submission Alerts', 'usp-pro'),     array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_3', array('id' => 'cc_submit',               'type' => 'admin'));
			add_settings_field('cc_approval',             esc_html__('CC Approval Alerts', 'usp-pro'),       array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_3', array('id' => 'cc_approval',             'type' => 'admin'));
			add_settings_field('cc_denied',               esc_html__('CC Denied Alerts', 'usp-pro'),         array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_3', array('id' => 'cc_denied',               'type' => 'admin'));
			add_settings_field('cc_scheduled',            esc_html__('CC Scheduled Alerts', 'usp-pro'),      array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_3', array('id' => 'cc_scheduled',            'type' => 'admin'));
			// 4
			add_settings_section('section_admin_4', 'Email Alerts for User', 'section_admin_4_desc', $this->settings_admin);
			add_settings_field('send_mail_user',      esc_html__('Submission Alerts', 'usp-pro'),        array(&$this, 'callback_checkbox'),   $this->settings_admin, 'section_admin_4', array('id' => 'send_mail_user',      'type' => 'admin'));
			add_settings_field('send_approval_user',  esc_html__('Approval Alerts', 'usp-pro'),          array(&$this, 'callback_checkbox'),   $this->settings_admin, 'section_admin_4', array('id' => 'send_approval_user',  'type' => 'admin'));
			add_settings_field('send_denied_user',    esc_html__('Denied Alerts', 'usp-pro'),            array(&$this, 'callback_checkbox'),   $this->settings_admin, 'section_admin_4', array('id' => 'send_denied_user',    'type' => 'admin'));
			add_settings_field('send_scheduled_user', esc_html__('Scheduled Alerts', 'usp-pro'),         array(&$this, 'callback_checkbox'),   $this->settings_admin, 'section_admin_4', array('id' => 'send_scheduled_user', 'type' => 'admin'));
			add_settings_field('alert_subject_user',  esc_html__('Submission Alert Subject', 'usp-pro'), array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_4', array('id' => 'alert_subject_user',  'type' => 'admin'));
			add_settings_field('post_alert_user',     esc_html__('Submission Alert Message', 'usp-pro'), array(&$this, 'callback_textarea'),   $this->settings_admin, 'section_admin_4', array('id' => 'post_alert_user',     'type' => 'admin'));
			add_settings_field('approval_subject',    esc_html__('Approval Alert Subject', 'usp-pro'),   array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_4', array('id' => 'approval_subject',    'type' => 'admin'));
			add_settings_field('approval_message',    esc_html__('Approval Alert Message', 'usp-pro'),   array(&$this, 'callback_textarea'),   $this->settings_admin, 'section_admin_4', array('id' => 'approval_message',    'type' => 'admin'));
			add_settings_field('denied_subject',      esc_html__('Denied Alert Subject', 'usp-pro'),     array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_4', array('id' => 'denied_subject',      'type' => 'admin'));
			add_settings_field('denied_message',      esc_html__('Denied Alert Message', 'usp-pro'),     array(&$this, 'callback_textarea'),   $this->settings_admin, 'section_admin_4', array('id' => 'denied_message',      'type' => 'admin'));
			add_settings_field('scheduled_subject',   esc_html__('Scheduled Alert Subject', 'usp-pro'),  array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_4', array('id' => 'scheduled_subject',   'type' => 'admin'));
			add_settings_field('scheduled_message',   esc_html__('Scheduled Alert Message', 'usp-pro'),  array(&$this, 'callback_textarea'),   $this->settings_admin, 'section_admin_4', array('id' => 'scheduled_message',   'type' => 'admin'));
			// 5
			add_settings_section('section_admin_5', 'Contact Form', 'section_admin_5_desc', $this->settings_admin);
			add_settings_field('contact_sub_prefix', esc_html__('Subject Line Prefix', 'usp-pro'),   array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_5', array('id' => 'contact_sub_prefix', 'type' => 'admin'));
			add_settings_field('contact_subject',    esc_html__('Subject Line', 'usp-pro'),          array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_5', array('id' => 'contact_subject',    'type' => 'admin'));
			add_settings_field('contact_from',       esc_html__('Email From', 'usp-pro'),            array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_5', array('id' => 'contact_from',       'type' => 'admin'));
			add_settings_field('custom_content',     esc_html__('Custom Content', 'usp-pro'),        array(&$this, 'callback_textarea'),   $this->settings_admin, 'section_admin_5', array('id' => 'custom_content',     'type' => 'admin'));
			add_settings_field('contact_cc',         esc_html__('CC Emails', 'usp-pro'),             array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_5', array('id' => 'contact_cc',         'type' => 'admin'));
			add_settings_field('contact_cc_user',    esc_html__('CC User', 'usp-pro'),               array(&$this, 'callback_checkbox'),   $this->settings_admin, 'section_admin_5', array('id' => 'contact_cc_user',    'type' => 'admin'));
			add_settings_field('contact_cc_note',    esc_html__('CC User Message', 'usp-pro'),       array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_5', array('id' => 'contact_cc_note',    'type' => 'admin'));
			add_settings_field('contact_stats',      esc_html__('Include User Stats', 'usp-pro'),    array(&$this, 'callback_checkbox'),   $this->settings_admin, 'section_admin_5', array('id' => 'contact_stats',      'type' => 'admin'));
			add_settings_field('contact_custom',     esc_html__('Include Custom Fields', 'usp-pro'), array(&$this, 'callback_checkbox'),   $this->settings_admin, 'section_admin_5', array('id' => 'contact_custom',     'type' => 'admin'));
			// 6
			add_settings_section('section_admin_6', 'Custom Recipients', 'section_admin_6_desc', $this->settings_admin);
			add_settings_field('custom_contact_1', esc_html__('Custom Recipient 1', 'usp-pro'), array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_6', array('id' => 'custom_contact_1', 'type' => 'admin'));
			add_settings_field('custom_contact_2', esc_html__('Custom Recipient 2', 'usp-pro'), array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_6', array('id' => 'custom_contact_2', 'type' => 'admin'));
			add_settings_field('custom_contact_3', esc_html__('Custom Recipient 3', 'usp-pro'), array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_6', array('id' => 'custom_contact_3', 'type' => 'admin'));
			add_settings_field('custom_contact_4', esc_html__('Custom Recipient 4', 'usp-pro'), array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_6', array('id' => 'custom_contact_4', 'type' => 'admin'));
			add_settings_field('custom_contact_5', esc_html__('Custom Recipient 5', 'usp-pro'), array(&$this, 'callback_input_text'), $this->settings_admin, 'section_admin_6', array('id' => 'custom_contact_5', 'type' => 'admin'));
			
		}
		
		// ADVANCED SETTINGS
		
		function register_advanced_settings() {
			global $usp_advanced;
			
			$this->settings_tabs[$this->settings_advanced] = esc_html__('Advanced', 'usp-pro');
			register_setting($this->settings_advanced, $this->settings_advanced, 'validate_advanced');
			add_settings_section('section_advanced_0', '', 'section_advanced_0_desc', $this->settings_advanced);
			
			// 1
			add_settings_section('section_advanced_1', esc_html__('Form Configuration', 'usp-pro'), 'section_advanced_1_desc', $this->settings_advanced);
			add_settings_field('success_form',     esc_html__('Display Form on Success', 'usp-pro'),    array(&$this, 'callback_checkbox'),   $this->settings_advanced, 'section_advanced_1', array('id' => 'success_form',     'type' => 'advanced'));
			add_settings_field('enable_autop',     esc_html__('Enable Auto-Formatting', 'usp-pro'),     array(&$this, 'callback_checkbox'),   $this->settings_advanced, 'section_advanced_1', array('id' => 'enable_autop',     'type' => 'advanced'));
			add_settings_field('fieldsets',        esc_html__('Auto-Include Fieldsets', 'usp-pro'),     array(&$this, 'callback_checkbox'),   $this->settings_advanced, 'section_advanced_1', array('id' => 'fieldsets',        'type' => 'advanced'));
			add_settings_field('form_demos',       esc_html__('Auto-Generate Form Demos', 'usp-pro'),   array(&$this, 'callback_checkbox'),   $this->settings_advanced, 'section_advanced_1', array('id' => 'form_demos',       'type' => 'advanced'));
			add_settings_field('post_demos',       esc_html__('Auto-Generate Post Demos', 'usp-pro'),   array(&$this, 'callback_checkbox'),   $this->settings_advanced, 'section_advanced_1', array('id' => 'post_demos',       'type' => 'advanced'));
			add_settings_field('submit_button',    esc_html__('Auto-Include Submit Button', 'usp-pro'), array(&$this, 'callback_checkbox'),   $this->settings_advanced, 'section_advanced_1', array('id' => 'submit_button',    'type' => 'advanced'));
			add_settings_field('disable_ip',       esc_html__('Disable IP Collection', 'usp-pro'),      array(&$this, 'callback_checkbox'),   $this->settings_advanced, 'section_advanced_1', array('id' => 'disable_ip',       'type' => 'advanced'));
			add_settings_field('submit_text',      esc_html__('Text for Submit Button', 'usp-pro'),     array(&$this, 'callback_input_text'), $this->settings_advanced, 'section_advanced_1', array('id' => 'submit_text',      'type' => 'advanced'));
			add_settings_field('html_content',     esc_html__('Post Formatting', 'usp-pro'),            array(&$this, 'callback_input_text'), $this->settings_advanced, 'section_advanced_1', array('id' => 'html_content',     'type' => 'advanced'));
			add_settings_field('form_atts',        esc_html__('Custom Form Attributes', 'usp-pro'),     array(&$this, 'callback_input_text'), $this->settings_advanced, 'section_advanced_1', array('id' => 'form_atts',        'type' => 'advanced'));
			add_settings_field('redirect_success', esc_html__('Redirect URL for Success', 'usp-pro'),   array(&$this, 'callback_input_text'), $this->settings_advanced, 'section_advanced_1', array('id' => 'redirect_success', 'type' => 'advanced'));
			add_settings_field('redirect_failure', esc_html__('Redirect URL for Failure', 'usp-pro'),   array(&$this, 'callback_input_text'), $this->settings_advanced, 'section_advanced_1', array('id' => 'redirect_failure', 'type' => 'advanced'));
			add_settings_field('blacklist_terms',  esc_html__('Content Filter', 'usp-pro'),             array(&$this, 'callback_textarea'),   $this->settings_advanced, 'section_advanced_1', array('id' => 'blacklist_terms',  'type' => 'advanced'));
			// 2
			add_settings_section('section_advanced_2', esc_html__('Custom Post Type', 'usp-pro'), 'section_advanced_2_desc', $this->settings_advanced);
			add_settings_field('post_type',      esc_html__('Submitted Post Type', 'usp-pro'),         array(&$this, 'callback_radio'),      $this->settings_advanced, 'section_advanced_2', array('id' => 'post_type',      'type' => 'advanced'));
			add_settings_field('post_type_slug', esc_html__('Slug for USP Post', 'usp-pro'),           array(&$this, 'callback_input_text'), $this->settings_advanced, 'section_advanced_2', array('id' => 'post_type_slug', 'type' => 'advanced'));
			add_settings_field('other_type',     esc_html__('Slug for Existing Post Type', 'usp-pro'), array(&$this, 'callback_input_text'), $this->settings_advanced, 'section_advanced_2', array('id' => 'other_type',     'type' => 'advanced'));
			add_settings_field('post_type_role', esc_html__('Roles for USP Posts', 'usp-pro'),         array(&$this, 'callback_checkboxes'), $this->settings_advanced, 'section_advanced_2', array('id' => 'post_type_role', 'type' => 'advanced'));
			add_settings_field('form_type_role', esc_html__('Roles for USP Forms', 'usp-pro'),         array(&$this, 'callback_checkboxes'), $this->settings_advanced, 'section_advanced_2', array('id' => 'form_type_role', 'type' => 'advanced'));
			// 3
			add_settings_section('section_advanced_3', esc_html__('Default Form Fields', 'usp-pro'), 'section_advanced_3_desc', $this->settings_advanced);
			add_settings_field('default_title',   esc_html__('Default Post Title', 'usp-pro'),   array(&$this, 'callback_input_text'), $this->settings_advanced, 'section_advanced_3', array('id' => 'default_title',   'type' => 'advanced'));
			add_settings_field('default_content', esc_html__('Default Post Content', 'usp-pro'), array(&$this, 'callback_textarea'),   $this->settings_advanced, 'section_advanced_3', array('id' => 'default_content', 'type' => 'advanced'));
			// 4
			add_settings_section('section_advanced_4', esc_html__('Before/After USP Forms', 'usp-pro'), 'section_advanced_4_desc', $this->settings_advanced);
			add_settings_field('custom_before', esc_html__('Custom Before Forms', 'usp-pro'), array(&$this, 'callback_textarea'), $this->settings_advanced, 'section_advanced_4', array('id' => 'custom_before', 'type' => 'advanced'));
			add_settings_field('custom_after',  esc_html__('Custom After Forms', 'usp-pro'),  array(&$this, 'callback_textarea'), $this->settings_advanced, 'section_advanced_4', array('id' => 'custom_after',  'type' => 'advanced'));
			// 5
			add_settings_section('section_advanced_5', esc_html__('Success Message', 'usp-pro'), 'section_advanced_5_desc', $this->settings_advanced);
			add_settings_field('success_reg',        esc_html__('Register User', 'usp-pro'),                array(&$this, 'callback_textarea'), $this->settings_advanced, 'section_advanced_5', array('id' => 'success_reg',        'type' => 'advanced'));
			add_settings_field('success_post',       esc_html__('Submit Post', 'usp-pro'),                  array(&$this, 'callback_textarea'), $this->settings_advanced, 'section_advanced_5', array('id' => 'success_post',       'type' => 'advanced'));
			add_settings_field('success_both',       esc_html__('Register and Submit', 'usp-pro'),          array(&$this, 'callback_textarea'), $this->settings_advanced, 'section_advanced_5', array('id' => 'success_both',       'type' => 'advanced'));
			add_settings_field('success_contact',    esc_html__('Contact Form', 'usp-pro'),                 array(&$this, 'callback_textarea'), $this->settings_advanced, 'section_advanced_5', array('id' => 'success_contact',    'type' => 'advanced'));
			add_settings_field('success_email_reg',  esc_html__('Contact Form and Register', 'usp-pro'),    array(&$this, 'callback_textarea'), $this->settings_advanced, 'section_advanced_5', array('id' => 'success_email_reg',  'type' => 'advanced'));
			add_settings_field('success_email_post', esc_html__('Contact Form and Post', 'usp-pro'),        array(&$this, 'callback_textarea'), $this->settings_advanced, 'section_advanced_5', array('id' => 'success_email_post', 'type' => 'advanced'));
			add_settings_field('success_email_both', esc_html__('Contact, Register, and Post', 'usp-pro'),  array(&$this, 'callback_textarea'), $this->settings_advanced, 'section_advanced_5', array('id' => 'success_email_both', 'type' => 'advanced'));
			add_settings_field('success_before',     esc_html__('Custom Before Message', 'usp-pro'),        array(&$this, 'callback_textarea'), $this->settings_advanced, 'section_advanced_5', array('id' => 'success_before',     'type' => 'advanced'));
			add_settings_field('success_after',      esc_html__('Custom After Message', 'usp-pro'),         array(&$this, 'callback_textarea'), $this->settings_advanced, 'section_advanced_5', array('id' => 'success_after',      'type' => 'advanced'));
			// 6
			add_settings_section('section_advanced_6', esc_html__('Primary Form Fields', 'usp-pro'), 'section_advanced_6_desc', $this->settings_advanced);
			for ( $i = 1; $i < 20; $i++ ) {
				add_settings_field('usp_error_'. strval($i), esc_html__('Primary Field ', 'usp-pro'). strval($i), array(&$this, 'callback_input_text'), $this->settings_advanced, 'section_advanced_6', array('id' => 'usp_error_'. strval($i), 'type' => 'advanced'));
			}
			// 7
			add_settings_section('section_advanced_7', esc_html__('User-Registration Fields', 'usp-pro'), 'section_advanced_7_desc', $this->settings_advanced);
			$user_fields = array('a' => esc_html__('Nicename', 'usp-pro'), 'b' => esc_html__('Display Name', 'usp-pro'), 'c' => esc_html__('Nickname', 'usp-pro'), 'd' => esc_html__('First Name', 'usp-pro'), 'e' => esc_html__('Last Name', 'usp-pro'), 'f' => esc_html__('Description', 'usp-pro'), 'g' => esc_html__('Password', 'usp-pro'));
			foreach ($user_fields as $key => $value) {
				add_settings_field('usp_error_'. $key, $value, array(&$this, 'callback_input_text'), $this->settings_advanced, 'section_advanced_7', array('id' => 'usp_error_'. $key, 'type' => 'advanced'));
			}
			// 8
			add_settings_section('section_advanced_8', esc_html__('Custom Fields', 'usp-pro'), 'section_advanced_8_desc', $this->settings_advanced);
			add_settings_field('custom_fields', esc_html__('Custom Fields', 'usp-pro'), array(&$this, 'callback_number'), $this->settings_advanced, 'section_advanced_8', array('id' => 'custom_fields', 'type' => 'advanced'));
			// 9
			add_settings_section('section_advanced_9', esc_html__('Custom Field Names', 'usp-pro'), 'section_advanced_9_desc', $this->settings_advanced);
			if (isset($usp_advanced['custom_fields']) && is_numeric($usp_advanced['custom_fields'])) {
				$max = 1 + intval($usp_advanced['custom_fields']);
				if ($max > 0) {
					for ($i = 1; $i < $max; $i++) {
						add_settings_field('usp_label_c'. strval($i), esc_html__('Custom Field ', 'usp-pro'). strval($i), array(&$this, 'callback_input_text'), $this->settings_advanced, 'section_advanced_9', array('id' => 'usp_label_c'. strval($i), 'type' => 'advanced'));
					}
				}
			}
			// 10
			add_settings_section('section_advanced_10', esc_html__('Custom Field Prefix', 'usp-pro'), 'section_advanced_10_desc', $this->settings_advanced);
			add_settings_field('custom_prefix', esc_html__('Custom Prefix', 'usp-pro'),  array(&$this, 'callback_input_text'), $this->settings_advanced, 'section_advanced_10', array('id' => 'custom_prefix', 'type' => 'advanced'));
			// 11
			add_settings_section('section_advanced_11', esc_html__('Custom Custom Fields', 'usp-pro'), 'section_advanced_11_desc', $this->settings_advanced);
			add_settings_field('custom_optional', esc_html__('Optional Fields', 'usp-pro'),  array(&$this, 'callback_input_text'), $this->settings_advanced, 'section_advanced_11', array('id' => 'custom_optional', 'type' => 'advanced'));
			add_settings_field('custom_required', esc_html__('Required Fields', 'usp-pro'),  array(&$this, 'callback_input_text'), $this->settings_advanced, 'section_advanced_11', array('id' => 'custom_required', 'type' => 'advanced'));
			// 12
			add_settings_section('section_advanced_12', esc_html__('Custom Custom Field Names', 'usp-pro'), 'section_advanced_12_desc', $this->settings_advanced);
			$custom_merged = usp_merge_custom_fields();
			if ($custom_merged) {
				foreach($custom_merged as $value) {
					$label_val = $value;
					if (strlen($value) > 24) $label_val = substr($value, 0, 25) .'&hellip;';
					add_settings_field('usp_custom_label_'. $value, 'Custom Field: '. esc_html__($label_val, 'usp-pro'), array(&$this, 'callback_input_text'), $this->settings_advanced, 'section_advanced_12', array('id' => 'usp_custom_label_'. $value, 'type' => 'advanced'));
				}
			}
		}
		
		// MORE SETTINGS
		
		function register_more_settings() {
			global $usp_more;
			
			$this->settings_tabs[$this->settings_more] = esc_html__('Errors', 'usp-pro');
			register_setting($this->settings_more, $this->settings_more, 'validate_more');
			add_settings_section('section_more_0', '', 'section_more_0_desc', $this->settings_more);
			
			// 1
			add_settings_section('section_more_1', esc_html__('Default Error Message', 'usp-pro'), 'section_more_1_desc', $this->settings_more);
			add_settings_field('error_before', esc_html__('Custom Before Errors', 'usp-pro'), array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_1', array('id' => 'error_before', 'type' => 'more'));
			add_settings_field('error_after',  esc_html__('Custom After Errors', 'usp-pro'),  array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_1', array('id' => 'error_after',  'type' => 'more'));
			// 2
			add_settings_section('section_more_2', esc_html__('Primary Field Errors', 'usp-pro'), 'section_more_2_desc', $this->settings_more);
			add_settings_field('usp_error_1_desc',  esc_html__('Name', 'usp-pro'),           array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_2', array('id' => 'usp_error_1_desc',  'type' => 'more'));
			add_settings_field('usp_error_2_desc',  esc_html__('URL', 'usp-pro'),            array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_2', array('id' => 'usp_error_2_desc',  'type' => 'more'));
			add_settings_field('usp_error_3_desc',  esc_html__('Title', 'usp-pro'),          array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_2', array('id' => 'usp_error_3_desc',  'type' => 'more'));
			add_settings_field('usp_error_4_desc',  esc_html__('Tags', 'usp-pro'),           array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_2', array('id' => 'usp_error_4_desc',  'type' => 'more'));
			add_settings_field('usp_error_5_desc',  esc_html__('Captcha', 'usp-pro'),        array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_2', array('id' => 'usp_error_5_desc',  'type' => 'more'));
			add_settings_field('usp_error_6_desc',  esc_html__('Category', 'usp-pro'),       array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_2', array('id' => 'usp_error_6_desc',  'type' => 'more'));
			add_settings_field('usp_error_7_desc',  esc_html__('Content', 'usp-pro'),        array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_2', array('id' => 'usp_error_7_desc',  'type' => 'more'));
			add_settings_field('usp_error_8_desc',  esc_html__('Files', 'usp-pro'),          array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_2', array('id' => 'usp_error_8_desc',  'type' => 'more'));
			add_settings_field('usp_error_9_desc',  esc_html__('Email Address', 'usp-pro'),  array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_2', array('id' => 'usp_error_9_desc',  'type' => 'more'));
			add_settings_field('usp_error_10_desc', esc_html__('Email Subject', 'usp-pro'),  array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_2', array('id' => 'usp_error_10_desc', 'type' => 'more'));
			add_settings_field('usp_error_11_desc', esc_html__('Alt Text', 'usp-pro'),       array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_2', array('id' => 'usp_error_11_desc', 'type' => 'more'));
			add_settings_field('usp_error_12_desc', esc_html__('Caption', 'usp-pro'),        array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_2', array('id' => 'usp_error_12_desc', 'type' => 'more'));
			add_settings_field('usp_error_13_desc', esc_html__('Description', 'usp-pro'),    array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_2', array('id' => 'usp_error_13_desc', 'type' => 'more'));
			add_settings_field('usp_error_14_desc', esc_html__('Taxonomy', 'usp-pro'),       array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_2', array('id' => 'usp_error_14_desc', 'type' => 'more'));
			add_settings_field('usp_error_15_desc', esc_html__('Post Format', 'usp-pro'),    array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_2', array('id' => 'usp_error_15_desc', 'type' => 'more'));
			add_settings_field('usp_error_16_desc', esc_html__('Media Title', 'usp-pro'),    array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_2', array('id' => 'usp_error_16_desc', 'type' => 'more'));
			add_settings_field('usp_error_17_desc', esc_html__('File Name', 'usp-pro'),      array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_2', array('id' => 'usp_error_17_desc', 'type' => 'more'));
			add_settings_field('usp_error_18_desc', esc_html__('Agree to Terms', 'usp-pro'), array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_2', array('id' => 'usp_error_18_desc', 'type' => 'more'));
			add_settings_field('usp_error_19_desc', esc_html__('Post Excerpt', 'usp-pro'),   array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_2', array('id' => 'usp_error_19_desc', 'type' => 'more'));
			// 3
			add_settings_section('section_more_3', esc_html__('Form Submission Errors', 'usp-pro'), 'section_more_3_desc', $this->settings_more);
			add_settings_field('error_username',   esc_html__('Username Error', 'usp-pro'),        array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_3', array('id' => 'error_username',   'type' => 'more'));
			add_settings_field('error_email',      esc_html__('User Email Error', 'usp-pro'),      array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_3', array('id' => 'error_email',      'type' => 'more'));
			add_settings_field('user_exists',      esc_html__('User Exists', 'usp-pro'),           array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_3', array('id' => 'user_exists',      'type' => 'more'));
			add_settings_field('error_register',   esc_html__('Registration Disabled', 'usp-pro'), array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_3', array('id' => 'error_register',   'type' => 'more'));
			add_settings_field('post_required',    esc_html__('Post Required', 'usp-pro'),         array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_3', array('id' => 'post_required',    'type' => 'more'));
			add_settings_field('post_duplicate',   esc_html__('Duplicate Post', 'usp-pro'),        array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_3', array('id' => 'post_duplicate',   'type' => 'more'));
			add_settings_field('name_restrict',    esc_html__('Name Restriction', 'usp-pro'),      array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_3', array('id' => 'name_restrict',    'type' => 'more'));
			add_settings_field('spam_response',    esc_html__('Incorrect Captcha', 'usp-pro'),     array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_3', array('id' => 'spam_response',    'type' => 'more'));
			add_settings_field('content_min',      esc_html__('Content Minimum', 'usp-pro'),       array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_3', array('id' => 'content_min',      'type' => 'more'));
			add_settings_field('content_max',      esc_html__('Content Maximum', 'usp-pro'),       array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_3', array('id' => 'content_max',      'type' => 'more'));
			add_settings_field('excerpt_min',      esc_html__('Excerpt Minimum', 'usp-pro'),       array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_3', array('id' => 'excerpt_min',      'type' => 'more'));
			add_settings_field('excerpt_max',      esc_html__('Excerpt Maximum', 'usp-pro'),       array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_3', array('id' => 'excerpt_max',      'type' => 'more'));
			add_settings_field('email_restrict',   esc_html__('Incorrect Address', 'usp-pro'),     array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_3', array('id' => 'email_restrict',   'type' => 'more'));
			add_settings_field('subject_restrict', esc_html__('Subject Restriction', 'usp-pro'),   array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_3', array('id' => 'subject_restrict', 'type' => 'more'));
			add_settings_field('form_allowed',     esc_html__('Incorrect Form Type', 'usp-pro'),   array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_3', array('id' => 'form_allowed',     'type' => 'more'));
			add_settings_field('content_filter',   esc_html__('Content Filter', 'usp-pro'),        array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_3', array('id' => 'content_filter',   'type' => 'more'));
			// 4
			add_settings_section('section_more_4', esc_html__('File Submission Errors', 'usp-pro'), 'section_more_4_desc', $this->settings_more);
			add_settings_field('files_required',  esc_html__('Files Required', 'usp-pro'),        array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_4', array('id' => 'files_required',  'type' => 'more'));
			add_settings_field('file_required',   esc_html__('File Required', 'usp-pro'),         array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_4', array('id' => 'file_required',   'type' => 'more'));
			add_settings_field('file_type_not',   esc_html__('File Type Not Allowed', 'usp-pro'), array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_4', array('id' => 'file_type_not',   'type' => 'more'));
			add_settings_field('file_dimensions', esc_html__('File Dimensions', 'usp-pro'),       array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_4', array('id' => 'file_dimensions', 'type' => 'more'));
			add_settings_field('file_max_size',   esc_html__('Maximum File Size', 'usp-pro'),     array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_4', array('id' => 'file_max_size',   'type' => 'more'));
			add_settings_field('file_min_size',   esc_html__('Minimum File Size', 'usp-pro'),     array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_4', array('id' => 'file_min_size',   'type' => 'more'));
			add_settings_field('file_name',       esc_html__('File Name Length', 'usp-pro'),      array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_4', array('id' => 'file_name',       'type' => 'more'));
			add_settings_field('min_req_files',   esc_html__('Min Number of Files', 'usp-pro'),   array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_4', array('id' => 'min_req_files',   'type' => 'more'));
			add_settings_field('max_req_files',   esc_html__('Max Number of Files', 'usp-pro'),   array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_4', array('id' => 'max_req_files',   'type' => 'more'));
			add_settings_field('file_square',     esc_html__('Require Square Images', 'usp-pro'), array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_4', array('id' => 'file_square',     'type' => 'more'));
			// 5
			add_settings_section('section_more_5', esc_html__('User Registration Errors', 'usp-pro'), 'section_more_5_desc', $this->settings_more);
			$user_fields = array('a' => esc_html__('Nicename', 'usp-pro'), 'b' => esc_html__('Display Name', 'usp-pro'), 'c' => esc_html__('Nickname', 'usp-pro'), 'd' => esc_html__('First Name', 'usp-pro'), 'e' => esc_html__('Last Name', 'usp-pro'), 'f' => esc_html__('Description', 'usp-pro'), 'g' => esc_html__('Password', 'usp-pro'));
			foreach ($user_fields as $key => $value) {
				add_settings_field('usp_error_'.$key.'_desc', $value, array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_5', array('id' => 'usp_error_'.$key.'_desc', 'type' => 'more'));
			}
			// 6
			add_settings_section('section_more_6', esc_html__('Miscellaneous Errors', 'usp-pro'), 'section_more_6_desc', $this->settings_more);
			add_settings_field('error_sep',           esc_html__('Error Separator', 'usp-pro'),     array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_6', array('id' => 'error_sep',           'type' => 'more'));
			add_settings_field('tax_before',          esc_html__('Before Taxonomy', 'usp-pro'),     array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_6', array('id' => 'tax_before',          'type' => 'more'));
			add_settings_field('tax_after',           esc_html__('After Taxonomy', 'usp-pro'),      array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_6', array('id' => 'tax_after',           'type' => 'more'));
			add_settings_field('custom_field_before', esc_html__('Before Custom Field', 'usp-pro'), array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_6', array('id' => 'custom_field_before', 'type' => 'more'));
			add_settings_field('custom_field_after',  esc_html__('After Custom Field', 'usp-pro'),  array(&$this, 'callback_textarea'), $this->settings_more, 'section_more_6', array('id' => 'custom_field_after',  'type' => 'more'));
			
		}
		
		// TOOLS SETTINGS
		
		function register_tools_settings() {
			global $usp_tools;
			
			$this->settings_tabs[$this->settings_tools] = esc_html__('Tools', 'usp-pro');
			register_setting($this->settings_tools, $this->settings_tools, 'validate_tools');
			add_settings_section('section_tools_0', '', 'section_tools_0_desc', $this->settings_tools);
			
			// 1
			add_settings_section('section_tools_1', esc_html__('Restore Default Settings', 'usp-pro'), 'section_tools_1_desc', $this->settings_tools);
			add_settings_field('default_options',  esc_html__('Restore Default Settings', 'usp-pro'), array(&$this, 'callback_checkbox'), $this->settings_tools, 'section_tools_1', array('id' => 'default_options', 'type' => 'tools'));
			
		}
		
		// ABOUT SETTINGS
		
		function register_about_settings() {
			$this->settings_tabs[$this->settings_about] = esc_html__('About', 'usp-pro');
		}
		
		// LICENSE SETTINGS
		
		function register_license_settings() {
			$this->settings_tabs[$this->settings_license] = esc_html__('License', 'usp-pro');
		}
		
		
		
		// CALLBACKS
		
		function callback_input_text($args) {
			global $usp_advanced;
			
			$id   = $args['id'];
			$type = $args['type'];
			
			$label = usp_callback_input_text_label($id);
			
			if ($type == 'admin') {
				
				if ($id == 'cc_submit' || $id == 'contact_cc') {
					
					$cc_submit_emails = trim(esc_attr($this->admin_settings[$id]));
					$cc_submit_emails = explode(',', $cc_submit_emails);
					$cc_submit_list = '';
					
					foreach ($cc_submit_emails as $email) $cc_submit_list .= trim($email) . ', ';
					$value = rtrim(trim($cc_submit_list), ',');
					
				} else {
					
					$value = esc_attr($this->admin_settings[$id]);
				}
				
			} elseif ($type == 'advanced') {
				
				if     (preg_match("/^usp_label_c([0-9]+)$/i",            $id, $match)) $label = esc_html__('Name for Custom Field #', 'usp-pro') . $match[1];
				elseif (preg_match("/^usp_custom_label_([0-9a-z_-]+)$/i", $id, $match)) $label = esc_html__('Name for Custom Field:', 'usp-pro') .' <code>'. $match[1] .'</code>';
				
				if (isset($this->advanced_settings[$id])) $value = esc_attr($this->advanced_settings[$id]);
				else $value = '';
				
			} elseif ($type == 'general') {
				
				if ($id == 'use_cat_id') {
					
					$cat_ids = trim(esc_attr($this->general_settings[$id]));
					$cat_ids = explode(',', $cat_ids);
					$cat_id_list = '';
					
					foreach ($cat_ids as $cat_id) $cat_id_list .= trim($cat_id) . ', ';
					$value = rtrim(trim($cat_id_list), ',');
					
				} else {
					
					$value = esc_attr($this->general_settings[$id]);
				}
				
			} elseif ($type == 'uploads') {
				
				$value = esc_attr($this->uploads_settings[$id]);
				
			} elseif ($type == 'style') {
				
				$value = esc_attr($this->style_settings[$id]);
			}
			
			$width     = 'width:377px;';
			$break     = '<br />';
			$form_type = 'text';
			$form_min  = '';
			
			if ($id == 'use_cat_id' || $id == 'custom_status') {
				
				$width = 'width:77px;';
				$break = ' ';
			
			} elseif ($id == 'character_min' || $id == 'character_max' || $id == 'tags_number') {
				
				$width     = 'width:77px;';
				$break     = ' ';
				$form_type = 'number';
				$form_min  = ' min="-1"';
				
			}
			
			echo '<input name="usp_'. $type .'['. $id .']" id="usp_'. $type .'['. $id .']" type="'. $form_type .'" value="'. $value .'" style="'. $width .'"'. $form_min .' />';
			echo $break .'<label for="usp_'. $type .'['. $id .']">'. $label .'</label>';
		}
		
		function callback_textarea($args) {
			
			$id   = $args['id'];
			$type = $args['type'];
			
			$label = usp_callback_textarea_label($id);
			
			if ($type == 'admin') {
				echo '<textarea name="usp_'. $type .'['. $id .']" id="usp_'. $type .'['. $id .']" rows="3" cols="70">'. esc_attr(stripslashes($this->admin_settings[$id])) .'</textarea>';
			
			} elseif ($type === 'advanced') {
				echo '<textarea name="usp_'. $type .'['. $id .']" id="usp_'. $type .'['. $id .']" rows="3" cols="70">'. esc_attr(stripslashes($this->advanced_settings[$id])) .'</textarea>';
			
			} elseif ($type === 'general') {
				echo '<textarea name="usp_'. $type .'['. $id .']" id="usp_'. $type .'['. $id .']" rows="3" cols="70">'. esc_attr(stripslashes($this->general_settings[$id])) .'</textarea>';
			
			} elseif ($type === 'style') {
				echo '<textarea name="usp_'. $type .'['. $id .']" id="usp_'. $type .'['. $id .']" rows="10" cols="70" class="large-text code">'. esc_attr(stripslashes($this->style_settings[$id])) .'</textarea>';
			
			} elseif ($type === 'more') {
				echo '<textarea name="usp_'. $type .'['. $id .']" id="usp_'. $type .'['. $id .']" rows="3" cols="70">'. esc_attr(stripslashes($this->more_settings[$id])) .'</textarea>';
			}
			
			echo '<br /><label for="usp_'. $type .'['. $id .']">'. $label .'</label>';
		}
		
		function callback_select($args) {
			
			$id = $args['id'];
			
			$label = usp_callback_select_label($id);
			
			if ($id == 'min_files' || $id == 'max_files') {
				echo '<select name="usp_uploads['. $id .']" id="usp_uploads['. $id .']">';
				echo '<option value="-1">'. esc_html__('No Limit', 'usp-pro') .'</option>';
				foreach(range(0, 99) as $number) {
					echo '<option '. selected($number, $this->uploads_settings[$id], false) .' value="'. $number .'">'. $number .'</option>';
				}
				echo '</select> <label for="usp_uploads['. $id .']">'. $label .'</label>';
				
			} elseif ($id == 'display_size') {
				echo '<select name="usp_uploads['. $id .']" id="usp_uploads['. $id .']">';
				$display_sizes = display_size_options();
				foreach ($display_sizes as $value) {
					echo '<option '. selected($value['value'], $this->uploads_settings[$id], false) .' value="'. $value['value'] .'">'. $value['label'] .'</option>';
				}
				echo '</select> <label for="usp_uploads['. $id .']">'. $label .'</label>';
				
			} elseif ($id == 'mail_format') {
				echo '<select name="usp_admin['. $id .']" id="usp_admin['. $id .']">';
				$mail_format = mail_format();
				foreach ($mail_format as $value) {
					echo '<option '. selected($value['value'], $this->admin_settings[$id], false) .' value="'. $value['value'] .'">'. $value['label'] .'</option>';
				}
				echo '</select> <label for="usp_admin['. $id .']">'. $label .'</label>';
				
			} elseif ($id == 'recaptcha_version') {
				echo '<select name="usp_general['. $id .']" id="usp_general['. $id .']">';
				$recaptcha = recaptcha_options();
				foreach ($recaptcha as $value) {
					echo '<option '. selected($value['value'], $this->general_settings[$id], false) .' value="'. $value['value'] .'">'. $value['label'] .'</option>';
				}
				echo '</select> <label for="usp_general['. $id .']">'. $label .'</label>';
				
			}
			
		}
		
		function callback_checkboxes($args) {
			global $usp_general;
			
			$id = $args['id'];
			
			if ($id == 'tags') {
				
				if (isset($usp_general['tags_order'])) $tags_order = $usp_general['tags_order'];
				else $tags_order = 'name_asc';
				if ($tags_order == 'id_asc' || $tags_order == 'name_asc' || $tags_order == 'count_asc') $order = 'ASC';
				else $order = 'DESC';

				if     ($tags_order == 'id_asc' || $tags_order == 'id_desc') $order_by = 'id';
				elseif ($tags_order == 'name_asc' || $tags_order == 'name_desc') $order_by = 'name';
				elseif ($tags_order == 'count_asc' || $tags_order == 'count_desc') $order_by = 'count';
				else $order_by = 'name';
				
				if (isset($usp_general['tags_number'])) $number = $usp_general['tags_number'];
				else $number = '-1';
				if ($number == '-1' || $number == '0' || $number == 'all') $number = '';
				
				if (isset($usp_general['tags_empty'])) $empty = $usp_general['tags_empty'];
				else $empty = 0;

				$args = array(
					'orderby'    => $order_by,
					'order'      => $order,
					'number'     => $number,
					'hide_empty' => $empty, 
				); 
				$tags = get_terms('post_tag', $args);
				
				echo '<p><label>' . esc_html__('Select which tags may be assigned to submitted posts (see next two options). ', 'usp-pro');
				echo '<a id="usp-toggle-tags" class="usp-toggle-tags" href="#usp-toggle-tags">'. esc_html__('Show/Hide Tags&nbsp;&raquo;', 'usp-pro') .'</a></label></p>';
				echo '<div class="usp-tags default-hidden"><ul>';
				foreach ((array) $tags as $tag) {
					echo '<li><input type="checkbox" name="usp_general[tags][]" id="usp_general[tags][]" value="'. esc_attr($tag->term_id) .'" '. checked(true, in_array($tag->term_id, $this->general_settings['tags']), false) .' /> ';
					echo '<label for="usp_general[tags][]"><a href="'. get_tag_link($tag->term_id) .'" title="Tag ID: '. esc_attr($tag->term_id) .'" target="_blank" rel="noopener noreferrer">'. sanitize_text_field($tag->name) .'</a></label></li>';
				}
				echo '</ul></div>';
				
			} elseif ($id == 'categories') {
				
				$usp_cats = array();
				$cats = get_categories(array('parent' => 0, 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => 0));
				if (!empty($cats)) {
					echo '<style type="text/css">ul.usp-cats ul { margin: 5px 0 5px 30px; } ul.usp-cats li { margin: 0; }</style>';
					echo '<p><label>'. esc_html__('Select which categories may be assigned to submitted posts. ', 'usp-pro');
					echo '<a id="usp-toggle-cats" class="usp-toggle-cats" href="#usp-toggle-cats">'. esc_html__('Show/Hide Categories&nbsp;&raquo;', 'usp-pro') .'</a></label></p>';
					echo '<div class="usp-cats default-hidden"><ul>';
					foreach ($cats as $c) {

						// parents
						echo '<li><input type="checkbox" name="usp_general[categories][]" id="usp_general[categories][]" value="'. esc_attr($c->term_id) .'" '. checked(true, in_array($c->term_id, $this->general_settings['categories']), false) .' /> ';
						echo '<label for="usp_general[categories][]"><a href="'. esc_url(get_category_link($c->term_id)) .'" title="Cat ID: '. esc_attr($c->term_id) .'" target="_blank" rel="noopener noreferrer">'. sanitize_text_field($c->name) .'</a></label></li>';
						$usp_cats['c'][] = array('id' => esc_attr($c->term_id), 'c1' => array());
						$children = get_terms('category', array('parent' => esc_attr($c->term_id), 'hide_empty' => 0));
						if (!empty($children)) {
							echo '<li><ul>';
							foreach ($children as $c1) {

								// children
								$usp_cats['c'][]['c1'][] = array('id' => esc_attr($c1->term_id), 'c2' => array());
								$grandchildren = get_terms('category', array('parent' => esc_attr($c1->term_id), 'hide_empty' => 0));
								if (!empty($grandchildren)) {
									echo '<li><input type="checkbox" name="usp_general[categories][]" id="usp_general[categories][]" value="'. esc_attr($c1->term_id) .'" '. checked(true, in_array($c1->term_id, $this->general_settings['categories']), false) .' /> ';
									echo '<label for="usp_general[categories][]"><a href="'. esc_url(get_category_link($c1->term_id)) .'" title="Cat ID: '. esc_attr($c1->term_id) .'" target="_blank" rel="noopener noreferrer">'. sanitize_text_field($c1->name) .'</a></label>';
									echo '<ul>';
									foreach ($grandchildren as $c2) {

										// grandchildren
										$usp_cats['c'][]['c1'][]['c2'][] = array('id' => esc_attr($c2->term_id), 'c3' => array());
										$great_grandchildren = get_terms('category', array('parent' => esc_attr($c2->term_id), 'hide_empty' => 0));
										if (!empty($great_grandchildren)) {
											echo '<li><input type="checkbox" name="usp_general[categories][]" id="usp_general[categories][]" value="'. esc_attr($c2->term_id) .'" '. checked(true, in_array($c2->term_id, $this->general_settings['categories']), false) .' /> ';
											echo '<label for="usp_general[categories][]"><a href="'. esc_url(get_category_link($c2->term_id)) .'" title="Cat ID: '. esc_attr($c2->term_id) .'" target="_blank" rel="noopener noreferrer">'. sanitize_text_field($c2->name) .'</a></label>';
											echo '<ul>';
											foreach ($great_grandchildren as $c3) {
												
												// great enkelkinder
												$usp_cats['c'][]['c1'][]['c2'][]['c3'][] = array('id' => esc_attr($c3->term_id), 'c4' => array());
												$great_great_grandchildren = get_terms('category', array('parent' => esc_attr($c3->term_id), 'hide_empty' => 0));
												if (!empty($great_great_grandchildren)) {
													echo '<li><input type="checkbox" name="usp_general[categories][]" id="usp_general[categories][]" value="'. esc_attr($c3->term_id) .'" '. checked(true, in_array($c3->term_id, $this->general_settings['categories']), false) .' /> ';
													echo '<label for="usp_general[categories][]"><a href="'. esc_url(get_category_link($c3->term_id)) .'" title="Cat ID: '. esc_attr($c3->term_id) .'" target="_blank" rel="noopener noreferrer">'. sanitize_text_field($c3->name) .'</a></label>';
													echo '<ul>';
													foreach ($great_great_grandchildren as $c4) {
														
														// great great grandchildren
														$usp_cats['c'][]['c1'][]['c2'][]['c3'][]['c4'][] = array('id' => esc_attr($c4->term_id));
														echo '<li><input type="checkbox" name="usp_general[categories][]" id="usp_general[categories][]" value="'. esc_attr($c4->term_id) .'" '. checked(true, in_array($c4->term_id, $this->general_settings['categories']), false) .' /> ';
														echo '<label for="usp_general[categories][]"><a href="'. esc_url(get_category_link($c4->term_id)) .'" title="Cat ID: '. esc_attr($c4->term_id) .'" target="_blank" rel="noopener noreferrer">'. sanitize_text_field($c4->name) .'</a></label></li>';
													}
													echo '</ul></li>'; // great great grandchildren
												} else {
													echo '<li><input type="checkbox" name="usp_general[categories][]" id="usp_general[categories][]" value="'. esc_attr($c3->term_id) .'" '. checked(true, in_array($c3->term_id, $this->general_settings['categories']), false) .' /> ';
													echo '<label for="usp_general[categories][]"><a href="'. esc_url(get_category_link($c3->term_id)) .'" title="Cat ID: '. esc_attr($c3->term_id) .'" target="_blank" rel="noopener noreferrer">'. sanitize_text_field($c3->name) .'</a></label></li>';
												}
											}
											echo '</ul></li>'; // great grandchildren
										} else {
											echo '<li><input type="checkbox" name="usp_general[categories][]" id="usp_general[categories][]" value="'. esc_attr($c2->term_id) .'" '. checked(true, in_array($c2->term_id, $this->general_settings['categories']), false) .' /> ';
											echo '<label for="usp_general[categories][]"><a href="'. esc_url(get_category_link($c2->term_id)) .'" title="Cat ID: '. esc_attr($c2->term_id) .'" target="_blank" rel="noopener noreferrer">'. sanitize_text_field($c2->name) .'</a></label></li>';
										}
									}
									echo '</ul></li>'; // grandchildren
								} else {
									echo '<li><input type="checkbox" name="usp_general[categories][]" id="usp_general[categories][]" value="'. esc_attr($c1->term_id) .'" '. checked(true, in_array($c1->term_id, $this->general_settings['categories']), false) .' /> ';
									echo '<label for="usp_general[categories][]"><a href="'. esc_url(get_category_link($c1->term_id)) .'" title="Cat ID: '. esc_attr($c1->term_id) .'" target="_blank" rel="noopener noreferrer">'. sanitize_text_field($c1->name) .'</a></label></li>';
								}
							}
							echo '</ul></li>'; // children
						}
					}
					echo '</ul></div>'; // parents
				}
				
			} elseif ($id == 'post_type_role') {
				
				$roles = array('administrator', 'editor', 'author', 'contributor');
				echo '<p><label>' . esc_html__('Which user roles should have access to USP Posts (when applicable): ', 'usp-pro') . '</label></p>';
				echo '<ul>';
				foreach ($roles as $role) {
					echo '<li><input type="checkbox" name="usp_advanced[post_type_role][]" id="usp_advanced[post_type_role][]" value="'. $role .'" '. checked(true, in_array($role, $this->advanced_settings['post_type_role']), false) .' /> ';
					echo '<label for="usp_advanced[post_type_role][]">'. ucfirst(sanitize_text_field($role)) .'</label></li>';
				}
				echo '</ul>';
				
			} elseif ($id == 'form_type_role') {
				
				$roles = array('administrator', 'editor', 'author', 'contributor');
				echo '<p><label>' . esc_html__('Which user roles should have access to USP Forms: ', 'usp-pro') . '</label></p>';
				echo '<ul>';
				foreach ($roles as $role) {
					echo '<li><input type="checkbox" name="usp_advanced[form_type_role][]" id="usp_advanced[form_type_role][]" value="'. $role .'" '. checked(true, in_array($role, $this->advanced_settings['form_type_role']), false) .' /> ';
					echo '<label for="usp_advanced[form_type_role][]">'. ucfirst(sanitize_text_field($role)) .'</label></li>';
				}
				echo '</ul>';
				
			}
		}
		
		function callback_checkbox($args) {
			
			$id   = $args['id'];
			$type = $args['type'];
			
			$label = usp_callback_checkbox_label($id);
			
			if     ($type == 'admin')    $checked = isset($this->admin_settings[$id])    ? checked($this->admin_settings[$id],    1, false) : '';
			elseif ($type == 'style')    $checked = isset($this->style_settings[$id])    ? checked($this->style_settings[$id],    1, false) : '';
			elseif ($type == 'advanced') $checked = isset($this->advanced_settings[$id]) ? checked($this->advanced_settings[$id], 1, false) : '';
			elseif ($type == 'general')  $checked = isset($this->general_settings[$id])  ? checked($this->general_settings[$id],  1, false) : '';
			elseif ($type == 'uploads')  $checked = isset($this->uploads_settings[$id])  ? checked($this->uploads_settings[$id],  1, false) : '';
			elseif ($type == 'more')     $checked = isset($this->more_settings[$id])     ? checked($this->more_settings[$id],     1, false) : '';
			elseif ($type == 'tools')    $checked = isset($this->tools_settings[$id])    ? checked($this->tools_settings[$id],    1, false) : '';
			
			echo '<input name="usp_'. $type .'['. $id .']" id="usp_'. $type .'['. $id .']" type="checkbox" value="1" '. $checked .' /> <label for="usp_'. $type .'['. $id .']">'. $label .'</label>';
		}
		
		function callback_number($args) {
			
			$id   = $args['id'];
			$type = $args['type'];
			
			$label = usp_callback_number_label($id);
			
			$value = $this->advanced_settings[$id];
			
			echo '<input name="usp_'. $type .'['. $id .']" id="usp_'. $type .'['. $id .']" type="number" step="1" min="0" max="999" maxlength="3" value="'. $value .'" /> <label for="usp_'. $type .'['. $id .']">'. $label .'</label>';
		}
		
		function callback_dropdown($args) {
			global $wpdb, $wp_roles;
			
			$id   = $args['id'];
			$type = $args['type'];
			
			$label = usp_callback_dropdown_label($id);
			
			echo '<select name="usp_'. $type .'['. $id .']" id="usp_'. $type .'['. $id .']">';
			
			if ($id == 'assign_author') {
				
				$list_authors = usp_callback_author_menu();
				
				foreach ($list_authors as $author) {
					echo '<option '. selected($this->general_settings[$id], $author->ID, false) .' value="'. esc_attr($author->ID) .'">'. esc_attr($author->display_name) .'</option>';		
				}
				
				echo '</select> <label for="usp_'. $type .'['. $id .']">'. $label .'</label>';
				
			} elseif ($id == 'assign_role') { 
				
				$roles = $wp_roles->roles;
				
				foreach ($roles as $key => $value) {
					echo '<option '. selected($this->general_settings[$id], strtolower($key), false) .' value="'. strtolower($key) .'">'. $value['name'] .'</option>';		
				}
				
				echo '</select> <label for="usp_'. $type .'['. $id .']">'. $label .'</label>';
				
			} elseif ($id == 'number_approved') {
				
				echo '<option '. selected(-6, $this->general_settings[$id], false) .' value="-6">'. esc_html__('Future (Scheduled Post)', 'usp-pro') . '</option>';
				echo '<option '. selected(-5, $this->general_settings[$id], false) .' value="-5">'. esc_html__('Always publish (via Password)', 'usp-pro') . '</option>';
				echo '<option '. selected(-4, $this->general_settings[$id], false) .' value="-4">'. esc_html__('Always publish (via Private)', 'usp-pro') . '</option>';
				echo '<option '. selected(-3, $this->general_settings[$id], false) .' value="-3">'. esc_html__('Always moderate (via Custom Status, defined below)', 'usp-pro') . '</option>';
				echo '<option '. selected(-2, $this->general_settings[$id], false) .' value="-2">'. esc_html__('Always moderate (via Pending)', 'usp-pro') . '</option>';
				echo '<option '. selected(-1, $this->general_settings[$id], false) .' value="-1">'. esc_html__('Always moderate (via Draft)', 'usp-pro') . '</option>';
				echo '<option '. selected( 0, $this->general_settings[$id], false) .' value="0">'.  esc_html__('Always publish immediately', 'usp-pro') .'</option>';
				
				foreach(range(1, 20) as $value) {
					echo '<option '. selected($value, $this->general_settings[$id], false) .' value="'. $value .'">'. $value .'</option>';
				}
				
				echo '</select><br /><label for="usp_'. $type .'['. $id .']">'. $label .'</label>';
			}
		}
		
		function callback_radio($args) {
			global $usp_admin, $usp_advanced, $usp_general, $usp_uploads;
			
			$id   = $args['id'];
			$type = $args['type'];
			
			$label = usp_callback_radio_label($id);
			
			if ($id == 'send_mail') {
				$radio_options = send_mail_options();
				if (isset($usp_admin['send_mail'])) $default = $usp_admin['send_mail'];
				else $default = $this->admin_settings[$id];
				
			} elseif ($id == 'post_type') {
				$radio_options = post_type_options();
				if (isset($usp_advanced['post_type'])) $default = $usp_advanced['post_type'];
				else $default = $this->advanced_settings[$id];
				
			} elseif ($id == 'cats_menu') {
				$radio_options = cats_menu_options();
				if (isset($usp_general['cats_menu'])) $default = $usp_general['cats_menu'];
				else $default = $this->general_settings[$id];
				
			} elseif ($id == 'tags_order') {
				$radio_options = tags_order_options();
				if (isset($usp_general['tags_order'])) $default = $usp_general['tags_order'];
				else $default = $this->general_settings[$id];
				
			} elseif ($id == 'tags_menu') {
				$radio_options = tags_menu_options();
				if (isset($usp_general['tags_menu'])) $default = $usp_general['tags_menu'];
				else $default = $this->general_settings[$id];
				
			} elseif ($id == 'form_style') {
				$radio_options = style_options();
				if (isset($usp_style['form_style'])) $default = $usp_style['form_style'];
				else $default = $this->style_settings[$id];
				
			} elseif ($id == 'post_images') {
				$radio_options = display_images_options();
				if (isset($usp_uploads['post_images'])) $default = $usp_uploads['post_images'];
				else $default = $this->uploads_settings[$id];
			}
			
			echo '<p><label for="usp_' . $type . '['. $id .']">' . $label . '</label></p>';
			if (!isset($checked)) $checked = '';
			echo '<ul>';
			foreach ($radio_options as $radio_option) {
				if ($default) {
					$radio_setting = $default;
				} else {
					if ($type == 'admin') {
						$radio_setting = $this->admin_settings[$id];
					} elseif ($type == 'advanced') {
						$radio_setting = $this->advanced_settings[$id];
					} elseif ($type == 'general') {
						$radio_setting = $this->general_settings[$id];
					} elseif ($type == 'style') {
						$radio_setting = $this->style_settings[$id];
					} elseif ($type == 'uploads') {
						$radio_setting = $this->uploads_settings[$id];
					}
				}
				if ($radio_setting == $radio_option['value']) {
					$checked = ' checked="checked"';
				} else {
					$checked = '';
				}
				echo '<li><input type="radio" name="usp_' . $type .'['. $id .']" id="usp_' . $type .'['. $id .']" value="'. esc_attr($radio_option['value']) .'"'. $checked .' /> '. $radio_option['label'] .'</li>';
			}
			echo '<ul>';
		}
		
		
		
		// SETTINGS PAGE
		
		function plugin_link_settings($links, $file) {
			if ($file == USP_PRO_FILE) {
				$usp_links = '<a href="'. get_admin_url() .'options-general.php?page='. $this->settings_page .'">'. esc_html__('Settings', 'usp-pro') .'</a>';
				array_unshift($links, $usp_links);
			}
			return $links;
		}
		
		function add_plugin_links($links, $file) {
			if ($file == plugin_basename(__FILE__)) {
				$links[]  = '<a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-quick-start/" title="'. esc_attr__('USP Pro Quick Start Guide', 'usp-pro') .'">'. esc_html__('Getting started', 'usp-pro') .'</a>';
			}
			return $links;
		}
		
		function add_admin_menus() {
			add_options_page('USP Pro', 'USP Pro', 'manage_options', $this->settings_page, array(&$this, 'plugin_options_page'));
		}
		
		function plugin_options_tabs() {
			$current_tab = isset($_GET['tab']) ? $_GET['tab'] : $this->settings_general;
			
			foreach ($this->settings_tabs as $tab_key => $tab_caption) {
				$active = ($current_tab == $tab_key) ? 'nav-tab-active' : '';
				echo '<a class="nav-tab '. $active .'" href="?page='. $this->settings_page .'&tab=' . $tab_key .'">'. $tab_caption .'</a>';	
			}
		}
		
		function enqueue_admin_scripts($hook) {
			global $post_type;
			if ('usp_form' === $post_type) {
				wp_enqueue_script('usp_quicktags', plugins_url(basename(dirname(__FILE__))) .'/js/usp-quicktags.js', array('jquery'), USP_PRO_VERSION, false);
			}
		}
		
		function add_admin_styles() {
			global $usp_advanced, $pagenow, $current_screen, $post_type;
			
			if (!is_admin()) return;
			
			$other_cpt = isset($usp_advanced['other_type']) ? $usp_advanced['other_type'] : false;
			
			if (
				($current_screen->post_type === 'post') || 
				($current_screen->post_type === 'page') || 
				($current_screen->post_type === 'usp_post') || 
				($current_screen->post_type === $other_cpt) || 
				(isset($_GET['page']) && $_GET['page'] === 'usp-pro-license') || 
				(isset($_GET['page']) && $_GET['page'] === 'usp_options')
				
			) {
				wp_enqueue_style('usp_style_admin', plugins_url(basename(dirname(__FILE__))) .'/css/usp-admin.css', array(), USP_PRO_VERSION, 'all');
			}
			
			if ('usp_form' === $post_type) {
				wp_enqueue_style('usp_quicktags', plugins_url(basename(dirname(__FILE__))) .'/css/usp-quicktags.css', array(), USP_PRO_VERSION, 'all');
			}
		}
		
		function plugin_options_page() {
			$tab = isset($_GET['tab']) ? $_GET['tab'] : $this->settings_general; 
			$status = get_option('usp_license_status'); ?>
			
			<div class="wrap">
				
				<h1 class="usp-title"><?php esc_html_e('USP Pro', 'usp-pro'); ?> <span><?php echo USP_PRO_VERSION; ?></span></h1>
				
				<?php if (isset($_GET['settings_restored']) && $_GET['settings_restored'] == 'true') 
						echo '<div class="notice notice-success is-dismissible"><p><strong>'. esc_html__('Your settings have been restored.', 'usp-pro') .'</strong></p></div>'; ?>
				
				<h2 class="nav-tab-wrapper"><?php $this->plugin_options_tabs(); ?></h2>
				
				<?php if ($tab !== 'usp_about' && $tab !== 'usp_license') : ?>
					
					<form method="post" action="options.php">
						
						<?php if ($status === 'valid' || USP_PRO_CODE) : ?>
						
							<?php wp_nonce_field('update-options'); ?>
							<?php settings_fields($tab); ?>
							<?php do_settings_sections($tab); ?>
							<?php submit_button(); ?>
							
						<?php else : ?>
							
							<h3><?php esc_html_e('Welcome to USP Pro!', 'usp-pro'); ?></h3>
							<p class="intro">
								<?php esc_html_e('Thank you for installing USP Pro. To begin using the plugin,', 'usp-pro'); ?> 
								<a href="<?php get_admin_url(); ?>plugins.php?page=usp-pro-license"><?php esc_html_e('enter your license key &raquo;', 'usp-pro'); ?></a>
							</p>
							
						<?php endif; ?>
						
					</form>
					
					<?php if ($tab == 'usp_tools') : ?>
						
						<?php echo '<div class="usp-pro-tools">'. usp_tools_display() .'</div>'; ?>
						
					<?php endif; ?>
					
				<?php else : ?>
					
					<?php if ($tab == 'usp_about')   section_about_desc(); ?>
					<?php if ($tab == 'usp_license') section_license_desc(); ?>
					
				<?php endif; ?>
				
			</div>
			
			<script type="text/javascript">
				jQuery(document).ready(function($){
					
					$('.default-hidden').hide();
					
					<?php if ($tab === 'usp_general') : ?>
					
					$('.usp-toggle-cats').click(function(e){ e.preventDefault(); $('.usp-cats').slideToggle(300); });
					$('.usp-toggle-tags').click(function(e){ e.preventDefault(); $('.usp-tags').slideToggle(300); });
					
					<?php elseif ($tab === 'usp_tools' || $tab === 'usp_about') : ?>
					
					$('.usp-toggle-s1').click(function(e){ e.preventDefault(); $('.usp-s1').slideToggle(300); });
					$('.usp-toggle-s2').click(function(e){ e.preventDefault(); $('.usp-s2').slideToggle(300); });
					$('.usp-toggle-s3').click(function(e){ e.preventDefault(); $('.usp-s3').slideToggle(300); });
					$('.usp-toggle-s4').click(function(e){ e.preventDefault(); $('.usp-s4').slideToggle(300); });
					$('.usp-toggle-s5').click(function(e){ e.preventDefault(); $('.usp-s5').slideToggle(300); });
					$('.usp-toggle-s6').click(function(e){ e.preventDefault(); $('.usp-s6').slideToggle(300); });
					
					<?php elseif ($tab === 'usp_admin') : ?>
					
					$('.usp-toggle-regex-1').click(function(e){ e.preventDefault(); $('.usp-regex-1').slideToggle(300); });
					$('.usp-toggle-regex-2').click(function(e){ e.preventDefault(); $('.usp-regex-2').slideToggle(300); });
					$('.usp-toggle-regex-3').click(function(e){ e.preventDefault(); $('.usp-regex-3').slideToggle(300); });
					
					<?php elseif ($tab === 'usp_advanced') : ?>
					
					$('.usp-toggle-a1').click(function(e){ e.preventDefault(); $('.usp-a1').slideToggle(300); });
					$('.usp-toggle-a2').click(function(e){ e.preventDefault(); $('.usp-a2').slideToggle(300); });
					$('.usp-toggle-a3').click(function(e){ e.preventDefault(); $('.usp-a3').slideToggle(300); });
					$('.usp-toggle-a4').click(function(e){ e.preventDefault(); $('.usp-a4').slideToggle(300); });
					$('.usp-toggle-a5').click(function(e){ e.preventDefault(); $('.usp-a5').slideToggle(300); });
					
					<?php endif; ?>
					
				});
			</script>
			
	<?php }
	}
}

if (class_exists('USP_Pro')) {
	function usp_pro_init() { 
		$USP_Pro = new USP_Pro;
	}
	add_action('init', 'usp_pro_init', 0); 
	register_activation_hook  (__FILE__, array('USP_Pro', 'activate'));
	register_deactivation_hook(__FILE__, array('USP_Pro', 'deactivate'));
	//
	$usp_admin    = get_option('usp_admin',    USP_Pro::admin_defaults());
	$usp_advanced = get_option('usp_advanced', USP_Pro::advanced_defaults());
	$usp_general  = get_option('usp_general',  USP_Pro::general_defaults());
	$usp_style    = get_option('usp_style',    USP_Pro::style_defaults());
	$usp_uploads  = get_option('usp_uploads',  USP_Pro::uploads_defaults());
	$usp_more     = get_option('usp_more',     USP_Pro::more_defaults());
	$usp_tools    = get_option('usp_tools',    USP_Pro::tools_defaults());
	//
	function usp_pro_delete_plugin_options() {
		include_once('uninstall.php');
	}
	if ($usp_tools['default_options'] == 1) {
		register_deactivation_hook(__FILE__, 'usp_pro_delete_plugin_options');
	}
	//
	if (!function_exists('exif_imagetype')) {
		function exif_imagetype($filename) {
			if ((list($width, $height, $type, $attr) = getimagesize($filename)) !== false) { 
				return $type;
			} 
			return false; 
		} 
	}
	if (!function_exists('usp_is_session_started')) {
		function usp_is_session_started() {
			if (php_sapi_name() !== 'cli') {
				if (version_compare(phpversion(), '5.4.0', '>=')) {
					return session_status() === PHP_SESSION_NONE ? false : true;
				} else {
					return session_id() === '' ? false : true;
				}
			}
			return false;
		}
	}

}
