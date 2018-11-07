<?php // USP Pro - Form Processing

if (!defined('ABSPATH')) die();

if (!class_exists('USP_Pro_Process')) {
	
	class USP_Pro_Process {
		
		public function __construct() {
			
			add_action('init', array(&$this, 'init'));
			add_action('admin_init', array(&$this, 'admin_init'));
			
			require_once (ABSPATH . '/wp-admin/includes/media.php');
			require_once (ABSPATH . '/wp-admin/includes/file.php');
			require_once (ABSPATH . '/wp-admin/includes/image.php');
			
			require_once(dirname(__FILE__) .'/usp-contact.php');
			require_once(dirname(__FILE__) .'/usp-utility.php');
			
		}
		public function init() {
			
			add_action('parse_request', array(&$this, 'insert_post'));
			add_action('parse_request', array(&$this, 'reset_form'));
			add_filter('query_vars', array(&$this, 'add_query_vars'));
			
		}
		public function admin_init() {
			
			add_action('new_to_publish', array(&$this, 'send_email_approval'));
			add_action('draft_to_publish', array(&$this, 'send_email_approval'));
			add_action('pending_to_publish', array(&$this, 'send_email_approval'));
			
			add_action('new_to_future', array(&$this, 'send_email_scheduled'));
			add_action('draft_to_future', array(&$this, 'send_email_scheduled'));
			add_action('pending_to_future', array(&$this, 'send_email_scheduled'));
			
			if (isset($_GET['post']) && !empty($_GET['post'])) { 
				
				$post_id = is_array($_GET['post']) ? current($_GET['post']) : $_GET['post'];
				$post_id = sanitize_text_field($post_id);
				$post_type = get_post_type($post_id);
				
				add_action('trash_'. $post_type, array(&$this, 'send_email_denied'), 1, 1);
				
			}
			
			add_action('transition_post_status', array(&$this, 'send_email_future'), 10, 3); // 
			
		}
		public function insert_post() {
			
			global $usp_admin, $usp_general;
			
			if (!isset($_POST['usp_form_submit']) || !isset($_POST['PHPSESSID']) || !empty($_POST['usp-verify']) || !wp_verify_nonce($_POST['usp_form_submit'], 'usp_form_submit')) {
				
				return false;
				
			} else {
				
				$args = $this->get_field_val();
				do_action('usp_insert_post_before', $args);
				
				$submitted_form = $_POST['usp_form_submit'];
				if ($submitted_form) $this->set_session_vars();
				
				$session_check  = $_POST['PHPSESSID'];
				if ($session_check) $this->check_session();
				
				$use_author     = $usp_general['use_author'];
				$assign_author  = $usp_general['assign_author'];
				
				$fields         = $args['fields'];
				$errors         = $args['errors'];
				$contact        = $args['contact'];
				$register       = $args['register'];
				$post_submit    = $args['post_submit'];
				$logged_id      = $args['logged_id'];
				$logged_cats    = $args['logged_cats'];
				$default_tags   = $args['default_tags'];
				$default_cats   = $args['default_cats'];
				$usp_redirect   = $args['usp_redirect'];
				$custom_type    = $args['usp_custom_type'];
				$contact_ids    = $args['contact_ids'];
				
				$errors_active  = $errors;
				$errors_display = array();
				
				$user_id        = $assign_author;
				$redirect_type  = 'redirect_failure';
				$include_path   = ABSPATH . 'wp-admin/includes/user.php';
				$custom_content = '';
				$error_register = '';
				$error_post     = '';
				$error_mail     = '';
				$check_id       = '';
				$post_id        = '';
				
				$errors_display = array();
				foreach ($errors_active as $error) {
					if (isset($error) && !empty($error)) {
						if (is_array($error)) {
							foreach ($error as $k => $v) $errors_display[(string)$k] = $v;
						} else {
							$errors_display[$error] = $error;
						}
					}
				}
				
				if (isset($fields['post_limit']) && !empty($fields['post_limit'])) {
					if (function_exists('usp_limit_posts_check')) { // addon
						if ($post_limit = usp_limit_posts_check()) {
							 $errors_display['post_limit'] = $post_limit;
						}
					}
				}
				
				if (empty($errors_display)) {
					
					// register
					if ($register) {
						if (!empty($logged_id)) {
							if ($use_author) $user_id = $logged_id;
							if (!$post_submit && !$contact) $error_register = 'user_exists';
						} else {
							$user_data = $this->register_user($fields);
							if (isset($user_data['usp_error_register']) && !empty($user_data['usp_error_register'])) {
								$error_register = $user_data['usp_error_register'];
							} else {
								if (isset($user_data['user_id']) && !empty($user_data['user_id'])) {
									$user_id  = $user_data['user_id'];
									$check_id = $user_data['user_id'];
								}
								$redirect_type = 'redirect_register';
							}
						}
					}
					// submit
					if (empty($error_register)) {
						if ($post_submit) {
							if (!empty($logged_id)) {
								if ($use_author) $user_id = $logged_id;
							}
							$post_id = $this->submit_post($fields, $user_id, $logged_cats, $default_tags, $default_cats, $custom_type);
							if (!empty($post_id) && is_numeric($post_id)) {
								if ($redirect_type == 'redirect_register') $redirect_type = 'redirect_both';
								else $redirect_type = 'redirect_post';
							} else {
								if ($post_id == 'duplicate') $error_post = 'post_duplicate';
								else $error_post = 'post_required';
								$redirect_type  = 'redirect_failure';
								if (is_file($include_path) && is_numeric($check_id)) {
									require_once($include_path);
									wp_delete_user($check_id);
								}
							}
						}
					}
					// contact
					if (empty($error_register) && empty($error_post)) {
						if ($contact) {
							if (isset($usp_admin['custom_content']) && !empty($usp_admin['custom_content'])) {
								$custom_content = $usp_admin['custom_content'];
							}
							$email_sent = send_email_form($fields, $contact_ids, $custom_content, $post_id);
							if ($email_sent) {
								if ($redirect_type == 'redirect_register') $redirect_type = 'redirect_email_register';
								elseif ($redirect_type == 'redirect_post') $redirect_type = 'redirect_email_post';
								elseif ($redirect_type == 'redirect_both') $redirect_type = 'redirect_email_both';
								else $redirect_type = 'redirect_email';
							} else { 
								$error_mail = 'email_error';
							}
						}
					}
					// errors
					if (!empty($error_register)) $errors_display['usp_error_register'] = $error_register;
					if (!empty($error_post))     $errors_display['usp_error_post']     = $error_post;
					if (!empty($error_mail))     $errors_display['usp_error_mail']     = $error_mail;
				}
				do_action('usp_insert_post_after', $errors_display, $redirect_type, $usp_redirect, $post_id);
				$errors_args = array('errors_display' => $errors_display, 'redirect_type' => $redirect_type);
				$this->submission_redirect($usp_redirect, $errors_args, $post_id);
			}
		}
		// REGISTER
		public function register_user($fields) {
			
			global $usp_general;
			$wp_version = get_bloginfo('version');
			$usp_role = $usp_general['assign_role'];
			$usp_role = apply_filters('usp_register_user_role', $usp_role);
			
			$user_id = $usp_general['assign_author'];
			$usp_pass = $this->generate_password($fields);
			$error_register = '';
			$usp_user = array(
				'role'          => $usp_role,
				'user_pass'     => $usp_pass, // dep.
				'user_login'    => $fields['usp_author'],
				'user_email'    => $fields['usp_email'], 
				'user_url'      => $fields['usp_url'],
				'user_nicename' => $fields['usp_nicename'],
				'display_name'  => $fields['usp_displayname'],
				'nickname'      => $fields['usp_nickname'],
				'first_name'    => $fields['usp_firstname'],
				'last_name'     => $fields['usp_lastname'],
				'description'   => $fields['usp_description'],
			);
			do_action('usp_register_user_before', $usp_user);
			
			if (!username_exists($fields['usp_author'])) {
				if (!email_exists($fields['usp_email'])) {
					$user_id = wp_insert_user($usp_user);
					if (is_numeric($user_id)) {
						if (version_compare($wp_version, '4.3', '>=')) {
							$notify = apply_filters('usp_register_user_notify', 'both');
							wp_new_user_notification($user_id, null, $notify);
						} else {
							wp_new_user_notification($user_id, $usp_pass);
						}
					}
				} else {
					$error_register = 'error_email';
				}
			} else {
				$error_register = 'error_username';
			}
			$args = array('user_id' => $user_id, 'usp_error_register' => $error_register);
			
			do_action('usp_register_user_after', $args);
			
			return apply_filters('usp_register_user', $args);
		}
		// POST
		public function submit_post($fields, $user_id, $logged_cats, $default_tags, $default_cats, $custom_type) {
			global $wpdb, $usp_advanced, $usp_general;
			
			do_action('usp_submit_post_before', $fields, $user_id, $logged_cats, $default_tags, $default_cats, $custom_type);
			
			if ($usp_general['titles_unique']) {
				$check_title = get_page_by_title($fields['usp_title'], OBJECT, $usp_advanced['post_type']);
				if ($check_title && $check_title->ID) return 'duplicate';
			}
			
			if ($usp_general['content_unique']) {
				$check_content = $wpdb->get_var($wpdb->prepare("SELECT post_content FROM ". $wpdb->prefix ."posts WHERE post_content = %s AND post_type = %s", $fields['usp_content'], $usp_advanced['post_type']));
				if (!empty($check_content)) return 'duplicate';
			}
			
			if (!empty($custom_type)) $post_type = $custom_type;
			else $post_type = $this->post_type();

			$post_tags = $this->post_tags($fields, $default_tags);
			$post_status = $this->post_status($fields);
			$post_id = $this->post_content($fields, $user_id, $post_tags, $post_status, $post_type);
			if (!empty($post_id)) {
				$post_meta = $this->post_meta($fields, $user_id, $post_id);
				$post_files = $this->insert_attachments($fields, $post_id);
				$post_categories = $this->post_categories($fields['usp_category'], $logged_cats, $default_cats, $post_id, $post_type);
				$post_taxonomies = $this->post_taxonomies($fields['usp_taxonomy'], $post_id);
				//
				$submit_email = $fields['usp_email'];
				$meta_email   = get_post_meta($post_id, 'usp-email', true);
				$user_info    = get_userdata($user_id);
				
				if (!empty($submit_email)) $user_email = $submit_email;
				elseif (!empty($meta_email)) $user_email = $meta_email;
				elseif (!empty($user_id) && !empty($user_info->user_email)) $user_email = $user_info->user_email;
				else $user_email = '';
				
				$this->send_email_alert(array('user_id' => $user_id, 'usp_email' => $user_email, 'post_id' => $post_id));
			} else {
				$post_id = '';
			}
			return apply_filters('usp_submit_post', $post_id, $user_id, $fields);
		}
		public function post_taxonomies($taxonomy, $post_id) {
			if (!empty($taxonomy)) {
				$terms = array(); $term_ids = array();
				foreach ($taxonomy as $key => $value) {
					if (is_array($value)) {
						foreach ($value as $val) {
							if (is_numeric($val)) {
								$terms[] = (int) $val;
							} else {
								$tax_term = get_term_by('name', trim($val), $key, ARRAY_A);
								$new = wp_insert_term(trim($val), $key);
								if (is_array($new)) $terms[] = $new['term_id'];
								else $terms[] = intval($tax_term['term_id']);
							}
						}
					} else {
						if (is_numeric($value)) {
							$terms[] = (int) $value;
						} else {
							if (strstr($value, ',') !== false) {
								$taxes = explode(',', $value);
								foreach ($taxes as $tax) {
									$tax_term = get_term_by('name', trim($tax), $key, ARRAY_A);
									$new = wp_insert_term(trim($tax), $key);
									if (is_array($new)) $terms[] = $new['term_id'];
									else $terms[] = intval($tax_term['term_id']);
								}
							} else {
								$tax_term = get_term_by('name', trim($value), $key, ARRAY_A);
								$new = wp_insert_term(trim($value), $key);
								if (is_array($new)) $terms[] = $new['term_id'];
								else $terms[] = intval($tax_term['term_id']);
							}
						}
					}
					$term_ids = wp_set_object_terms($post_id, $terms, $key);
				}
				if (!is_wp_error($term_ids)) return apply_filters('usp_post_taxonomies', $term_ids);
			}
		}
		public function post_type() {
			global $usp_advanced;
			if ($usp_advanced['post_type'] == 'other') {
				if (post_type_exists($usp_advanced['other_type'])) $post_type = $usp_advanced['other_type'];
				else $post_type = 'post';
			} else {
				$post_type = $usp_advanced['post_type'];
			}
			return apply_filters('usp_post_type', $post_type);
		}
		public function post_tags($fields, $default_tags) {
			$post_tags = '';
			if (!empty($default_tags)) {
				$default_tags = trim($default_tags);
				$default_tags = explode("|", $default_tags);
				foreach ($default_tags as $default_tag) {
					$post_tag = get_term_by('id', intval(trim($default_tag)), 'post_tag', ARRAY_A);
					if (!$post_tag) continue;
					$post_tags .= $post_tag['name'] . ', ';
				}
			}
			if (!empty($fields['usp_tags'])) {
				$user_tags = $fields['usp_tags'];
				if (is_array($user_tags)) {
					foreach ($user_tags as $user_tag) {
						$post_tag = get_term_by('id', intval($user_tag), 'post_tag', ARRAY_A);
						if (!$post_tag) continue;
						$post_tags .= $post_tag['name'] . ', ';
					}
				} else {
					$user_tags = trim($fields['usp_tags']);
					$user_tags = explode(",", $user_tags);
					foreach ($user_tags as $user_tag) {
						if (is_numeric($user_tag)) {
							$post_tag = get_term_by('id', intval($user_tag), 'post_tag', ARRAY_A);
							$post_tags .= $post_tag['name'] . ', ';
						} else {
							$post_tags .= $user_tag . ', ';
						}
					}
				}
			}
			$post_tags = rtrim(trim($post_tags), ',');
			return apply_filters('usp_post_tags', $post_tags);
		}
		public function post_status($fields) {
			global $usp_general;
			$usp_post_status = '';
			if (isset($fields['usp_form_id']) && !empty($fields['usp_form_id'])) {
				$form_id = sanitize_text_field($fields['usp_form_id']);
				$usp_post_status = get_post_meta($form_id, 'usp-post-status', true);
				if (is_numeric($usp_post_status)) $usp_post_status = (int) $usp_post_status;
			}
			if (isset($usp_post_status) && $usp_post_status !== '') $setting = $usp_post_status;
			else $setting = $usp_general['number_approved'];
			
			$custom = $usp_general['custom_status'];
			if ($setting == -6) {
				$post_status = 'future';
			} elseif ($setting == -5) {
				$post_status = 'password';
			} elseif ($setting == -4) {
				$post_status = 'private';
			} elseif ($setting == -3) {
				$post_status = $custom;
			} elseif ($setting == -2) {
				$post_status = 'pending';
			} elseif ($setting == -1) {
				$post_status = 'draft';
			} elseif ($setting == 0) {
				$post_status = 'publish';
			} else {
				$author_name = (isset($fields['usp_author']) && !empty($fields['usp_author'])) ? $fields['usp_author'] : '';
				$author_id = (isset($usp_general['assign_author']) && !empty($usp_general['assign_author'])) ? $usp_general['assign_author'] : null;
				
				$author_default = get_user_by('ID', $author_id);
				$author_display_name = $author_default->display_name;
				
				$author = (!empty($author_name)) ? $author_name : $author_display_name;
				$posts = get_posts(array('post_status' => 'publish', 'meta_key' => 'usp-author', 'meta_value' => $author));
				
				if (empty($posts)) $posts = get_posts(array('post_status' => 'publish', 'meta_key' => 'usp-author-id', 'meta_value' => $author_id));
				
				$counter = 0;
				foreach ($posts as $post) $counter++;
				if ($counter >= $setting) $post_status = 'publish';
				else $post_status = 'draft'; // default
			}
			return apply_filters('usp_post_status', $post_status);
		}
		public function post_date($usp_date = '') {
			$post_date = ''; $post_date_gmt = '';
			if (!empty($usp_date)) {
				$date          = strtotime($usp_date);
				$offset_date   = apply_filters('usp_post_date_offset', 0);
				$post_date     = date('Y-m-d H:i:s', $date + $offset_date);
				$post_date_gmt = get_gmt_from_date($post_date);
			}
			return array('post_date' => $post_date, 'post_date_gmt' => $post_date_gmt);
		}
		public function post_content($fields, $user_id, $post_tags, $post_status, $post_type) {
			global $usp_advanced, $usp_more;
			
			$args = $this->post_password($post_status, $fields);
			$post_content = $this->sanitize_content($fields['usp_content']);
			$post_excerpt = $this->sanitize_content($fields['usp_excerpt']);
			$post_parent = (int) sanitize_text_field($fields['post_parent']);
			$post_title = $fields['usp_title'];
			
			$date = array();
			if (isset($fields['usp_custom']['usp-custom-post-date'])) $date = $this->post_date($fields['usp_custom']['usp-custom-post-date']);
			
			if (empty($post_title)   && isset($usp_advanced['default_title'])   && !empty($usp_advanced['default_title']))   $post_title   = $usp_advanced['default_title'];
			if (empty($post_content) && isset($usp_advanced['default_content']) && !empty($usp_advanced['default_content'])) $post_content = $usp_advanced['default_content'];
			
			$post_title   = apply_filters('usp_default_title',   $post_title);
			$post_content = apply_filters('usp_default_content', $post_content);
			
			$post_status   = isset($args['post_status'])   ? $args['post_status']   : '';
			$password      = isset($args['password'])      ? $args['password']      : '';
			$post_date     = isset($date['post_date'])     ? $date['post_date']     : '';
			$post_date_gmt = isset($date['post_date_gmt']) ? $date['post_date_gmt'] : '';
			
			$usp_post = array(
				'post_author'   => $user_id,
				'post_content'  => $post_content,
				'post_excerpt'  => $post_excerpt,
				'post_type'     => $post_type,
				'tags_input'    => $post_tags,
				'post_title'    => $post_title,
				'post_status'   => $post_status,
				'post_password' => $password,
				'post_date'     => $post_date,
				'post_date_gmt' => $post_date_gmt,
				'post_parent'   => $post_parent,
			);
			
			$usp_post = apply_filters('usp_post_array', $usp_post);
			
			if ($usp_advanced['html_content'] !== '') {
				remove_filter('content_save_pre', 'wp_filter_post_kses');
				remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
				
				$post_id = wp_insert_post($usp_post);
				
				add_filter('content_save_pre', 'wp_filter_post_kses');
				add_filter('content_filtered_save_pre', 'wp_filter_post_kses');
			} else {
				$post_id = wp_insert_post($usp_post);
			}
			if (isset($fields['usp_format']) && !empty($fields['usp_format'])) $post_format = strtolower($fields['usp_format']);
			else $post_format = 'standard';
			$set_format = set_post_format($post_id, $post_format);
			
			do_action('usp_submit_post_after', $fields, $user_id, $post_id);
			
			return apply_filters('usp_post_content', $post_id);
		}
		public function post_password($post_status, $fields) {
			global $usp_admin;
			
			$password = '';
			if ($post_status == 'password') {
				$post_status = 'publish';
				$password = wp_generate_password();
				if ($usp_admin['send_mail'] !== 'no_mail') {
					$blog_name = get_bloginfo();
					$blog_url = trailingslashit(home_url());
					
					$admin_name = trim($usp_admin['admin_name']);
					$admin_from = trim($usp_admin['admin_from']);
					
					$headers  = 'X-Mailer: USP Pro'. "\n";
					$headers .= 'From: '. $admin_name .' <'. $admin_from .'>'. "\n";
					$headers .= 'Reply-To: '. $admin_name .' <'. $admin_from .'>'. "\n";
					$headers .= 'Content-Type: text/plain; charset='. get_option('blog_charset', 'UTF-8') . "\n";
					
					$post_author = (isset($fields['usp_author']) && !empty($fields['usp_author'])) ? $fields['usp_author'] : esc_html__('Guest', 'usp-pro');
					
					$email = $admin_from;
					if     (isset($_POST['usp-email'])  && !empty($_POST['usp-email']))  $email = sanitize_text_field($_POST['usp-email']);
					elseif (isset($fields['usp_email']) && !empty($fields['usp_email'])) $email = sanitize_text_field($fields['usp_email']);
					
					$subject  = esc_html__('Information about your submitted post.', 'usp-pro');
					$message  = esc_html__('Hello ', 'usp-pro') . $post_author . ', '. "\r\n\n";
					$message .= esc_html__('Here is the password for your submitted post: ', 'usp-pro') . $password . "\r\n\n";
					$message .= esc_html__('Visit your post at ', 'usp-pro') . $blog_name . ': ' .$blog_url . "\r\n";
					
					$email   = apply_filters('usp_post_password_email',   $email);
					$subject = apply_filters('usp_post_password_subject', $subject);
					$message = apply_filters('usp_post_password_message', $message);
					$headers = apply_filters('usp_post_password_headers', $headers);
					
					if ($usp_admin['send_mail'] == 'wp_mail') wp_mail($email, $subject, $message, $headers);
					else                                         mail($email, $subject, $message, $headers);
				}
			}
			$result = array('password' => $password, 'post_status' => $post_status);
			return apply_filters('usp_post_password', $result);
		}
		public function post_meta($fields, $user_id, $post_id) {
			global $usp_general, $usp_advanced;
			
			do_action('usp_post_meta_before', $fields, $user_id, $post_id);
			
			if (isset($fields['usp_custom_custom']) && !empty($fields['usp_custom_custom'])) {
				$custom_custom = $fields['usp_custom_custom'];
				foreach ($custom_custom as $key => $value) {
					if (is_array($value)) {
						foreach ($value as $k => $v) {
							if (!blank_or_zero($v)) $custom_meta = add_post_meta($post_id, $key, $v);
						}
					} else {
						if (!blank_or_zero($value)) $custom_meta = add_post_meta($post_id, $key, $value);
					}
				}
			}
			$prefix = 'null___';
			if (isset($usp_advanced['custom_prefix']) && !empty($usp_advanced['custom_prefix'])) $prefix = $usp_advanced['custom_prefix'];
			
			$post_meta = false;
			if (!empty($fields['usp_custom'])) {
				foreach ($fields['usp_custom'] as $key => $value) {
					
					do_action('usp_post_meta_foreach_before', $key, $value);
					
					$custom_field_regex = apply_filters('usp_custom_field_regex', '([0-9a-z_-]+)');
					
					if (preg_match("/^usp-custom-". $custom_field_regex ."$/i", $key, $match)) {
						if ((strpos($match[1], '-required') === false) && (strpos($match[1], '-count') === false) && (strpos($match[1], '-limit') === false)) {
							if (is_array($value)) {
								foreach ($value as $k => $v) {
									if (!blank_or_zero($v)) $post_meta = add_post_meta($post_id, 'usp-custom-'. $match[1], $v);
								}
							} else {
								if (!blank_or_zero($value)) $post_meta = add_post_meta($post_id, 'usp-custom-'. $match[1], $value);
							}
						}
					} elseif (preg_match("/^$prefix". $custom_field_regex ."?$/i", $key, $match)) {
						if ((strpos($match[1], '-required') === false) && (strpos($match[1], '-count') === false) && (strpos($match[1], '-limit') === false)) {
							if (is_array($value)) {
								foreach ($value as $k => $v) {
									if (!blank_or_zero($v)) $post_meta = add_post_meta($post_id, $prefix . $match[1], $v);
								}
							} else {
								if (!blank_or_zero($value)) $post_meta = add_post_meta($post_id, $prefix . $match[1], $value);
							}
						}
					}
					do_action('usp_post_meta_foreach_after', $post_meta, $key, $value);
				}
			}
			if (!empty($user_id)) $post_author_id = add_post_meta($post_id, 'usp-author-id', $user_id);
			
			if (!blank_or_zero($fields['usp_author']))  $post_author    = add_post_meta($post_id, 'usp-author', $fields['usp_author']);
			if (!blank_or_zero($fields['usp_subject'])) $post_subject   = add_post_meta($post_id, 'usp-subject', $fields['usp_subject']);
			
			if (!empty($fields['usp_email'])) $post_email = add_post_meta($post_id, 'usp-email', $fields['usp_email']);
			if (!empty($fields['usp_url']))   $post_url   = add_post_meta($post_id, 'usp-url', $fields['usp_url']);
			if (!empty($fields['usp_agree'])) $post_agree = add_post_meta($post_id, 'usp-agree', apply_filters('usp_custom_agree_terms', 'true'));
			
			$is_submission = add_post_meta($post_id, 'is_submission', true);
			$has_post_id   = add_post_meta($post_id, 'usp-post-id', $post_id);
			$has_post_time = add_post_meta($post_id, 'usp-post-time', apply_filters('usp_post_meta_submit_time_format', get_the_time('l, F j, Y @ h:i:s a', $post_id)));
			$usp_form_id   = add_post_meta($post_id, 'usp-form-id', sanitize_text_field($fields['usp_form_id']));
			
			if (!empty($fields['email_alerts'])) {
				foreach ($fields['email_alerts'] as $key => $value) {
					$value = htmlspecialchars(base64_decode(str_rot13($value), true), ENT_QUOTES, get_option('blog_charset', 'UTF-8'));
					$email_alerts = add_post_meta($post_id, $key, $value);
				}
			}
			
			if (isset($fields['disable_alerts']) && $fields['disable_alerts'] == 1) $disable_alerts = add_post_meta($post_id, 'usp-disable-alerts', true);
			
			if (isset($fields['post_expires']) && !empty($fields['post_expires'])) {
				$post_expires = add_post_meta($post_id, 'usp-post-expires', sanitize_text_field($fields['post_expires']));
				delete_post_meta($post_id, 'usp-custom-expires');
			}
			
			if (isset($usp_general['enable_stats']) && !empty($usp_general['enable_stats'])) {
				$stats = $this->get_user_stats();
				if (!empty($stats)) {
					foreach ($stats as $meta_key => $meta_value) {
						if (!empty($meta_value)) $post_meta = add_post_meta($post_id, $meta_key, $meta_value);
					}
				}
			}
			do_action('usp_post_meta_after', $is_submission);
			
			if ($is_submission) return true;
			else return false;
			
		}
		public function insert_attachments($fields, $post_id) {	
			global $usp_uploads;
			
			do_action('usp_insert_attachments_before', $fields, $post_id);
			
			$post_id = apply_filters('usp_attachments_postid', $post_id);
			
			$files = ''; $alt = ''; $caption = ''; $desc = ''; $mediatitle = ''; $filename = '';
			
			if (isset($fields['usp_files']))      $files      = $fields['usp_files'];
			if (isset($fields['usp_alt']))        $alt        = $fields['usp_alt'];
			if (isset($fields['usp_caption']))    $caption    = $fields['usp_caption'];
			if (isset($fields['usp_desc']))       $desc       = $fields['usp_desc'];
			if (isset($fields['usp_mediatitle'])) $mediatitle = $fields['usp_mediatitle'];
			if (isset($fields['usp_filename']))   $filename   = $fields['usp_filename'];
			
			if ($files !== '') {
				$file_data = $files;
				$loop_count = 0;
				
				if (isset($file_data['name'])) {
				
					for ($i = 0; $i < count($file_data['name']); $i++) {
						
						if (!empty($file_data['tmp_name'][$i])) {
							$file_local = file_get_contents($file_data['tmp_name'][$i]);
							$tmp_name = $file_data['tmp_name'][$i];
						} else {
							continue;
						}
						
						if (!empty($file_data['name'][$i])) $file_name = basename($file_data['name'][$i]);
						else continue;
						
						$post_title = preg_replace('/\.[^.]+$/', '', basename($file_name));
						
						if (!isset($alt[$i]))        $alt[$i]        = apply_filters('usp_alt_default',        '');
						if (!isset($caption[$i]))    $caption[$i]    = apply_filters('usp_caption_default',    '');
						if (!isset($desc[$i]))       $desc[$i]       = apply_filters('usp_desc_default',       '');
						if (!isset($mediatitle[$i])) $mediatitle[$i] = apply_filters('usp_mediatitle_default', sanitize_file_name($post_title), $file_data);
						if (!isset($filename[$i]))   $filename[$i]   = apply_filters('usp_filename_default',   sanitize_file_name($post_title), $file_data);
						
						if (isset($usp_uploads['auto-rotate']) && $usp_uploads['auto-rotate']) {
							$image_type = function_exists('exif_imagetype') ? exif_imagetype($tmp_name) : false;
							if ($image_type === 2) {
								$image_exif = function_exists('exif_read_data') ? @exif_read_data($tmp_name) : array(); // @ cuz PHP bug
								if (isset($image_exif['Orientation']) && !empty($image_exif['Orientation'])) {
									$src = imagecreatefromjpeg($tmp_name);
									if ($src) {
										switch ($image_exif['Orientation']) {
											case 3:  $image = imagerotate($src, 180, 0); break;
											case 6:  $image = imagerotate($src, -90, 0); break;
											case 8:  $image = imagerotate($src,  90, 0); break;
											default: $image = null; break;
										}
										imagedestroy($src);
										if ($image) {
											ob_start();
											imagejpeg($image, null, 100);
											$file_local = ob_get_contents();
											ob_end_clean();
											imagedestroy($image);
										}
									}
								}
							}
						}
						
						$file_path = defined('USP_UPLOAD_DIR') ? USP_UPLOAD_DIR : '/';
						
						$upload_dir  = wp_upload_dir();
						$upload_dir  = apply_filters('usp_upload_directory', $upload_dir);
						$wp_filetype = wp_check_filetype($file_name, null);
						
						if (wp_mkdir_p($upload_dir['path'])) {
							
							$file = $upload_dir['path'] . $file_path . $file_name;
							$guid = $upload_dir['url']  . $file_path . $file_name;
							
						} else {
							
							$file = $upload_dir['basedir'] . $file_path . $file_name;
							$guid = $upload_dir['baseurl'] . $file_path . $file_name;
							
						}
						
						$bytes = file_put_contents($file, $file_local);
						// @chmod($file, 0644);
						
						if (!function_exists('wp_handle_upload')) require_once(ABSPATH .'wp-admin/includes/file.php');
						
						$params = apply_filters('wp_handle_upload', array('file' => $file, 'url' => $guid, 'type' => $wp_filetype['type'])); 
						
						$file     = isset($params['file']) ? sanitize_text_field($params['file']) : $file;
						$guid     = isset($params['url'])  ? esc_url($params['url'])              : $guid;
						$filetype = isset($params['type']) ? sanitize_text_field($params['type']) : $wp_filetype['type'];
						
						$attachment = array(
							'post_mime_type' => $filetype,
							'post_name'      => $filename[$i],
							'post_title'     => $mediatitle[$i],
							'post_content'   => $desc[$i],
							'post_excerpt'   => $caption[$i],
							'post_status'    => 'inherit',
							'guid'           => $guid,
						);
						$attachment = apply_filters('usp_insert_attachment_data', $attachment);
						$attach_id = wp_insert_attachment($attachment, $file, $post_id);
						$attach_data = wp_generate_attachment_metadata($attach_id, $file);
						wp_update_attachment_metadata($attach_id, $attach_data);
						
						$featured_image = isset($usp_uploads['featured_image']) ? apply_filters('usp_featured_image', $usp_uploads['featured_image']) : 1;
						
						if ($featured_image == 1) {
							if (!current_theme_supports('post-thumbnails')) add_theme_support('post-thumbnails');
							if (isset($usp_uploads['featured_key']) && intval($usp_uploads['featured_key']) !== 0) $image_key = intval($usp_uploads['featured_key']);
							else $image_key = 1;
							if (($i + 1) == $image_key) {
								set_post_thumbnail($post_id, $attach_id);
							}
						}
						if (!is_wp_error($attach_id)) {
							
							$attach_ids[] = $attach_id;
							$l = $i + 1;
							if (isset($file_data['key'][$i])) {
								if ($file_data['key'][$i] === '0') $file_key = $l;
								else $file_key = $file_data['key'][$i];
							}
							
							$add_fields = $this->add_files_fields($attach_id, $file_data['field'][$i], $file_key, $post_id);
							
							if (!empty($alt[$i])) update_post_meta($attach_id, '_wp_attachment_image_alt', $alt[$i]);
							
							if (!empty($alt[$i]))        add_post_meta($post_id, 'usp-alt-'.        $file_key, $alt[$i]);
							if (!empty($desc[$i]))       add_post_meta($post_id, 'usp-desc-'.       $file_key, $desc[$i]);
							if (!empty($caption[$i]))    add_post_meta($post_id, 'usp-caption-'.    $file_key, $caption[$i]);
							
							if (!empty($mediatitle[$i]) && $mediatitle[$i] !== $post_title) add_post_meta($post_id, 'usp-mediatitle-'. $file_key, $mediatitle[$i]);
							if (!empty($filename[$i])   && $filename[$i]   !== $post_title) add_post_meta($post_id, 'usp-filename-'.   $file_key, $filename[$i]);
							
							do_action('usp_insert_attachments_loop', $attach_id);
							
						} else {
							
							wp_delete_attachment($attach_id);
							return false;
							
						}
						
						$loop_count++;
						
					}
					
				} else {
					
					return false;
					
				}
				
				if (isset($_FILES)) unset($_FILES);
				
			} else {
				
				return false;
				
			}
			
			return true;
			
		}
		public function add_files_fields($attach_id, $file_field, $file_key, $post_id) {
			
			global $usp_advanced;
			
			$custom = usp_merge_custom_fields();
			
			$prefix = (isset($usp_advanced['custom_prefix']) && !empty($usp_advanced['custom_prefix'])) ? trim($usp_advanced['custom_prefix']) : 'null___';
			
			$field  = (isset($file_field) && !empty($file_field)) ? sanitize_text_field($file_field) : 'usp-file';
			$key    = (isset($file_key)   && !empty($file_key))   ? sanitize_text_field($file_key)   : 'null';
			
			$value  = $field .'-'. $key;
			
			// regular files single
			if (stripos($field, 'usp-file-single') !== false) {
				
				$value = apply_filters('usp_file_name_primary_single', 'usp-file-single', $key);
				
			// regular files key
			} elseif (preg_match("/^usp-file-([0-9]+)$/i", $field)) {
				
				$value = apply_filters('usp_file_name_primary_key', $field, $key);
				
			// regular files multiple
			} elseif (stripos($field, 'usp-files') !== false) {
				
				$value = apply_filters('usp_file_name_primary_multiple', 'usp-file-'. $key, $key);
				
			// custom prefix files
			} elseif (stripos($field, $prefix) !== false) {
				
				$value = apply_filters('usp_file_name_custom_prefix', $field .'-'. $key, $key);
				
			// custom fields files
			} elseif (preg_match("/^usp_custom_file_([0-9]+)$/i", $field)) {
				
				$value = apply_filters('usp_file_name_custom_field', 'usp-file-'. $key, $key);
				
			// custom custom files
			} else {
				
				foreach ($custom as $c) {
					
					if (stripos($field, $c) !== false) {
						
						$value = apply_filters('usp_file_name_custom_custom', $field .'-'. $key, $key);
						break;
						
					}
				}
			}
			
			$file_url = apply_filters('usp_attached_file_url', wp_get_attachment_url($attach_id), $attach_id, $post_id, $value);
			
			add_post_meta($post_id, $value, $file_url);
			
		}
		public function post_categories($user_cats, $logged_cats, $default_cats, $post_id, $post_type) {
			global $usp_general;
			
			do_action('usp_post_categories_before', $user_cats, $logged_cats, $default_cats, $post_id, $post_type);
			
			$post_cats = array();
			if (isset($default_cats) && !empty($default_cats)) {
				$default_cats = trim($default_cats);
				$default_cats = explode("|", $default_cats);
				foreach ($default_cats as $default_cat) $post_cats[] = intval(trim($default_cat));
			}
			if (!empty($logged_cats) && $usp_general['use_cat']) {
				$logged_cats = trim($logged_cats);
				$logged_cats = explode("|", $logged_cats);
				foreach ($logged_cats as $logged_cat) $post_cats[] = intval(trim($logged_cat));
			}
			if (!empty($user_cats)) {
				if (is_array($user_cats)) {
					foreach ($user_cats as $user_cat) $post_cats[] = intval(trim($user_cat));
					
				} else {
					$new_cats[] = array();
					$delimiter = array(',', '|');
					$user_cats = trim($user_cats);
					$user_cats = str_replace($delimiter, ',', $user_cats);
					//
					if (strpos($user_cats, ',')) {
						$user_cats = explode(',', $user_cats);
						foreach ($user_cats as $user_cat) {
							$user_cat = trim($user_cat);
							if (is_numeric($user_cat)) {
								$post_cats[] = intval($user_cat);
							} else {
								$cat = get_term_by('name', $user_cat, 'category', ARRAY_A);
								if (!empty($cat)) {
									if (isset($cat['term_id'])) $post_cats[] = intval($cat['term_id']);
								} else {
									$new_cats[] = wp_insert_term($user_cat, 'category');
								}
							}
						}
					} else {
						if (is_numeric($user_cats)) {
							$post_cats[] = intval($user_cats);
						} else {
							$cat = get_term_by('name', $user_cats, 'category', ARRAY_A);
							if (!empty($cat)) {
								if (isset($cat['term_id'])) $post_cats[] = intval($cat['term_id']);
							} else {
								$new_cats[] = wp_insert_term($user_cats, 'category');
								
							}
						}
					}
					if (!empty($new_cats)) {
						foreach ($new_cats as $new_cat) {
							if (isset($new_cat['term_id']) && !empty($new_cat['term_id'])) {
								$post_cats[] = $new_cat['term_id'];
							}
						}
					}
				}
			}
			if ($post_type == 'post') {
				$post_categories = wp_set_post_categories($post_id, $post_cats);
			} else {
				$post_categories = wp_set_object_terms($post_id, $post_cats, 'category');	
			}
			
			do_action('usp_post_categories_after', $post_type, $post_categories, $post_id);
			
			if (is_array($post_categories) && !is_wp_error($post_categories)) return true;
			else return false;
		}
		// SUBMIT ALERTS
		public function send_email_alert($user) {
			do_action('usp_send_email_alert_before', $user);
			
			$post_id = false; 
			if (isset($user['post_id'])) $post_id = $user['post_id'];
			
			if (isset($_POST['usp-disable-alerts'])) return $post_id;
			
			$args = $this->get_email_info($post_id);
			$vars = $this->get_email_vars('submit', $args, $user);
			
			$vars = apply_filters('usp_send_email_alert_vars', $vars);
			
			$is_submit = (bool) get_post_meta($post_id, 'is_submission', true);
			if (!$is_submit) return false;
			
			if ($vars['send_user'] || $vars['send_admin']) {
				
				if ($vars['form_submit'] && $vars['send_mail'] !== 'no_mail') {
					
					$nonce = wp_verify_nonce($vars['form_submit'], 'usp_form_submit');
					if ($nonce && !wp_is_post_revision($post_id)) {
						
						do_action('usp_send_email_alert_during', $vars);
						
						if ($vars['send_mail'] == 'wp_mail') {
							if ($vars['send_user'])  wp_mail($vars['user_email'], $vars['subject_user'], $vars['message_user'], $vars['headers']);
							if ($vars['send_admin']) wp_mail($vars['admin_email'], $vars['subject_admin'], $vars['message_admin'], $vars['headers'] . $vars['bcc']);
						} else {
							if ($vars['send_user'])  mail($vars['user_email'], $vars['subject_user'], $vars['message_user'], $vars['headers']);
							if ($vars['send_admin']) mail($vars['admin_email'], $vars['subject_admin'], $vars['message_admin'], $vars['headers'] . $vars['bcc']);
						}
					}
				}
			}
			return apply_filters('usp_send_email_alert', $post_id);
		}
		// APPROVAL ALERTS
		public function send_email_approval($post) { // post object or post id
			do_action('usp_send_email_approval_before', $post);
			
			$post_id = is_object($post) ? $post->ID : $post;
			
			$disable_alert = get_post_meta($post_id, 'usp-disable-alerts', true);
			if (!empty($disable_alert)) return $post_id;
			
			$args = $this->get_email_info($post_id);
			$vars = $this->get_email_vars('approved', $args);
			
			$vars = apply_filters('usp_send_email_approval_vars', $vars);
			
			$is_submit = (bool) get_post_meta($post_id, 'is_submission', true);
			if (!$is_submit) return false;
			
			if ($vars['send_user'] || $vars['send_admin']) {
				
				if ($vars['form_submit'] && $vars['send_mail'] !== 'no_mail') {
					
					if (!wp_is_post_revision($post_id)) {
							
						do_action('usp_send_email_approval_during', $vars);
						
						if ($vars['send_mail'] == 'wp_mail') {
							if ($vars['send_user'])  wp_mail($vars['user_email'], $vars['subject_user'], $vars['message_user'], $vars['headers']);
							if ($vars['send_admin']) wp_mail($vars['admin_email'], $vars['subject_admin'], $vars['message_admin'], $vars['headers'] . $vars['bcc']);
						} else {
							if ($vars['send_user'])  mail($vars['user_email'], $vars['subject_user'], $vars['message_user'], $vars['headers']);
							if ($vars['send_admin']) mail($vars['admin_email'], $vars['subject_admin'], $vars['message_admin'], $vars['headers'] . $vars['bcc']);
						}
					}
				}
			}
			return apply_filters('usp_send_email_approval', $post_id);
		}
		// DENIED ALERTS
		public function send_email_denied($post_id) { // post ID
			do_action('usp_send_email_denied_before', $post_id);
			
			$post_id = $post_id;
			
			$disable_alert = get_post_meta($post_id, 'usp-disable-alerts', true);
			if (!empty($disable_alert)) return $post_id;
			
			$args = $this->get_email_info($post_id);
			$vars = $this->get_email_vars('denied', $args);
			
			$vars = apply_filters('usp_send_email_denied_vars', $vars);
			
			$is_submit = (bool) get_post_meta($post_id, 'is_submission', true);
			if (!$is_submit) return false;
			
			$post_type = get_post_type($post_id);
			if (did_action('trash_'. $post_type) !== 1) return false;
			
			if ($vars['send_user'] || $vars['send_admin']) {
				
				if ($vars['form_submit'] && $vars['send_mail'] !== 'no_mail') {
					
					if (!wp_is_post_revision($post_id)) {
							
						do_action('usp_send_email_denied_during', $vars);
						
						if ($vars['send_mail'] == 'wp_mail') {
							if ($vars['send_user'])  wp_mail($vars['user_email'], $vars['subject_user'], $vars['message_user'], $vars['headers']);
							if ($vars['send_admin']) wp_mail($vars['admin_email'], $vars['subject_admin'], $vars['message_admin'], $vars['headers'] . $vars['bcc']);
						} else {
							if ($vars['send_user'])  mail($vars['user_email'], $vars['subject_user'], $vars['message_user'], $vars['headers']);
							if ($vars['send_admin']) mail($vars['admin_email'], $vars['subject_admin'], $vars['message_admin'], $vars['headers'] . $vars['bcc']);
						}
					}
				}
			}
			return apply_filters('usp_send_email_denied', $post_id);
		}
		// SCHEDULED ALERTS
		public function send_email_scheduled($post) { // post object
			do_action('usp_send_email_scheduled_before', $post);
			
			$post_id = $post->ID; 
			
			$disable_alert = get_post_meta($post_id, 'usp-disable-alerts', true);
			if (!empty($disable_alert)) return $post_id;
			
			$args = $this->get_email_info($post_id);
			$vars = $this->get_email_vars('scheduled', $args);
			
			$vars = apply_filters('usp_send_email_scheduled_vars', $vars);
			
			$is_submit = (bool) get_post_meta($post_id, 'is_submission', true);
			if (!$is_submit) return false;
			
			if ($vars['send_user'] || $vars['send_admin']) {
				
				if ($vars['form_submit'] && $vars['send_mail'] !== 'no_mail') {
					
					if (!wp_is_post_revision($post_id)) {
							
						do_action('usp_send_email_scheduled_during', $vars);
						
						if ($vars['send_mail'] == 'wp_mail') {
							if ($vars['send_user'])  wp_mail($vars['user_email'], $vars['subject_user'], $vars['message_user'], $vars['headers']);
							if ($vars['send_admin']) wp_mail($vars['admin_email'], $vars['subject_admin'], $vars['message_admin'], $vars['headers'] . $vars['bcc']);
						} else {
							if ($vars['send_user'])  mail($vars['user_email'], $vars['subject_user'], $vars['message_user'], $vars['headers']);
							if ($vars['send_admin']) mail($vars['admin_email'], $vars['subject_admin'], $vars['message_admin'], $vars['headers'] . $vars['bcc']);
						}
					}
				}
			}
			return apply_filters('usp_send_email_scheduled', $post_id);
		}
		public function send_email_future($new_status, $old_status, $post) {
			$post_id = $post->ID;
			if (($old_status == 'future') && ($new_status == 'publish')) {
				$this->send_email_approval($post);
			}
			return apply_filters('usp_send_email_future', $post_id);
		}
		public function get_email_vars($action, $args, $user = false) {
			global $usp_admin;
			
			if (!empty($action) && !empty($args)) {
				
				$form_submit = false; $form_email = false; $send_mail = false;
				if (isset($_POST['usp_form_submit'])) $form_submit = sanitize_text_field($_POST['usp_form_submit']);
				if (isset($_POST['usp-email']))       $form_email  = sanitize_email($_POST['usp-email']);
				if (isset($usp_admin['send_mail']))   $send_mail   = sanitize_text_field($usp_admin['send_mail']);
				
				if (isset($args['admin_name']))  $admin_name  = sanitize_text_field($args['admin_name']);
				if (isset($args['admin_from']))  $admin_from  = sanitize_email($args['admin_from']);
				if (isset($args['admin_email'])) $admin_email = sanitize_email($args['admin_email']);
				if (isset($args['user_email']))  $user_email  = sanitize_email($args['user_email']);
				if (isset($args['post_id']))     $post_id     = sanitize_text_field($args['post_id']);
				//
				if (isset($user['usp_email']))   $user_email  = sanitize_email($user['usp_email']);
				
				$args['ip_address'] = usp_get_ip();
				
				$charset     = get_option('blog_charset', 'UTF-8');
				$mail_format = sanitize_text_field($usp_admin['mail_format']);
				
				$headers  = 'X-Mailer: USP Pro'. "\n";
				$headers .= 'From: '. $admin_name .' <'. $admin_from .'>'. "\n";
				$headers .= 'Reply-To: '. $admin_name .' <'. $admin_from .'>'. "\n";
				
				if ($mail_format == 'html') {
					$headers .= 'Content-Type: text/html; charset='. $charset . "\n";
				} else {
					$headers .= 'Content-Type: text/plain; charset='. $charset . "\n";
				}
				
				$subject_user = ''; $subject_admin = ''; $message_user = ''; $message_admin = ''; $carbon_copies = ''; $send_user = false; $send_admin = false; 
				
				if ($action == 'submit') {
					
					$subject_user_custom = get_post_meta($post_id, 'usp-alert-submit-subject', true);
					$subject_user = $subject_user_custom ? $subject_user_custom : $usp_admin['alert_subject_user'];
					$subject_user = sanitize_text_field($subject_user);
					
					$subject_admin_custom = get_post_meta($post_id, 'usp-alert-submit-subject-admin', true);
					$subject_admin = $subject_admin_custom ? $subject_admin_custom : $usp_admin['alert_subject_admin'];
					$subject_admin = sanitize_text_field($subject_admin);
					
					$message_user_custom = get_post_meta($post_id, 'usp-alert-submit-message', true);
					$message_user = $message_user_custom ? $message_user_custom : $usp_admin['post_alert_user'];
					$message_user = $this->sanitize_content(html_entity_decode(stripslashes($message_user), ENT_QUOTES, get_option('blog_charset', 'UTF-8')));
					
					$message_admin_custom = get_post_meta($post_id, 'usp-alert-submit-message-admin', true);
					$message_admin = $message_admin_custom ? $message_admin_custom : $usp_admin['post_alert_admin'];
					$message_admin = $this->sanitize_content(html_entity_decode(stripslashes($message_admin), ENT_QUOTES, get_option('blog_charset', 'UTF-8')));
					
					$carbon_copies_custom = '';
					if ($admin_email_custom = get_post_meta($post_id, 'usp-alert-submit-cc-admin', true)) {
						$admin_email_custom = array_map('trim', explode(',', $admin_email_custom));
						$i = 0; $max = apply_filters('usp_custom_alert_max', 3);
						foreach ($admin_email_custom as $value) {
							if ($i < $max) $carbon_copies_custom .= $value .',';
							$i++;
						}
					}
					$carbon_copies = ltrim($carbon_copies_custom, ', ') . trim(esc_attr($usp_admin['cc_submit']), ', ');
					$carbon_copies = trim($carbon_copies, ', ');
					
					if ($usp_admin['send_mail_user'])  $send_user  = true;
					if ($usp_admin['send_mail_admin']) $send_admin = true;
					
				} elseif ($action == 'approved') {
					
					$form_submit = true;
					
					$subject_user_custom = get_post_meta($post_id, 'usp-alert-approval-subject', true);
					$subject_user = $subject_user_custom ? $subject_user_custom : $usp_admin['approval_subject'];
					$subject_user = sanitize_text_field($subject_user);
					
					$subject_admin_custom = get_post_meta($post_id, 'usp-alert-approval-subject-admin', true);
					$subject_admin = $subject_admin_custom ? $subject_admin_custom : $usp_admin['approval_subject_admin'];
					$subject_admin = sanitize_text_field($subject_admin);
					
					$message_user_custom = get_post_meta($post_id, 'usp-alert-approval-message', true);
					$message_user = $message_user_custom ? $message_user_custom : $usp_admin['approval_message'];
					$message_user = $this->sanitize_content(html_entity_decode(stripslashes($message_user), ENT_QUOTES, get_option('blog_charset', 'UTF-8')));
					
					$message_admin_custom = get_post_meta($post_id, 'usp-alert-approval-message-admin', true);
					$message_admin = $message_admin_custom ? $message_admin_custom : $usp_admin['approval_message_admin'];
					$message_admin = $this->sanitize_content(html_entity_decode(stripslashes($message_admin), ENT_QUOTES, get_option('blog_charset', 'UTF-8')));
					
					$carbon_copies_custom = '';
					if ($admin_email_custom = get_post_meta($post_id, 'usp-alert-approval-cc-admin', true)) {
						$admin_email_custom = array_map('trim', explode(',', $admin_email_custom));
						$i = 0; $max = apply_filters('usp_custom_alert_max', 3);
						foreach ($admin_email_custom as $value) {
							if ($i < $max) $carbon_copies_custom .= $value .',';
							$i++;
						}
					}
					$carbon_copies = ltrim($carbon_copies_custom, ', ') . trim(esc_attr($usp_admin['cc_approval']), ', ');
					$carbon_copies = trim($carbon_copies, ', ');
					
					if ($usp_admin['send_approval_user'])  $send_user  = true;
					if ($usp_admin['send_approval_admin']) $send_admin = true;
					
				} elseif ($action == 'denied') {
					
					$form_submit = true;
					
					$subject_user_custom = get_post_meta($post_id, 'usp-alert-denied-subject', true);
					$subject_user = $subject_user_custom ? $subject_user_custom : $usp_admin['denied_subject'];
					$subject_user = sanitize_text_field($subject_user);
					
					$subject_admin_custom = get_post_meta($post_id, 'usp-alert-denied-subject-admin', true);
					$subject_admin = $subject_admin_custom ? $subject_admin_custom : $usp_admin['denied_subject_admin'];
					$subject_admin = sanitize_text_field($subject_admin);
					
					$message_user_custom = get_post_meta($post_id, 'usp-alert-denied-message', true);
					$message_user = $message_user_custom ? $message_user_custom : $usp_admin['denied_message'];
					$message_user = $this->sanitize_content(html_entity_decode(stripslashes($message_user), ENT_QUOTES, get_option('blog_charset', 'UTF-8')));
					
					$message_admin_custom = get_post_meta($post_id, 'usp-alert-denied-message-admin', true);
					$message_admin = $message_admin_custom ? $message_admin_custom : $usp_admin['denied_message_admin'];
					$message_admin = $this->sanitize_content(html_entity_decode(stripslashes($message_admin), ENT_QUOTES, get_option('blog_charset', 'UTF-8')));
					
					$carbon_copies_custom = '';
					if ($admin_email_custom = get_post_meta($post_id, 'usp-alert-denied-cc-admin', true)) {
						$admin_email_custom = array_map('trim', explode(',', $admin_email_custom));
						$i = 0; $max = apply_filters('usp_custom_alert_max', 3);
						foreach ($admin_email_custom as $value) {
							if ($i < $max) $carbon_copies_custom .= $value .',';
							$i++;
						}
					}
					$carbon_copies = ltrim($carbon_copies_custom, ', ') . trim(esc_attr($usp_admin['cc_denied']), ', ');
					$carbon_copies = trim($carbon_copies, ', ');
					
					if ($usp_admin['send_denied_user'])  $send_user  = true;
					if ($usp_admin['send_denied_admin']) $send_admin = true;
					
				}
				elseif ($action == 'scheduled') {
					
					$form_submit = true;
					
					$subject_user_custom = get_post_meta($post_id, 'usp-alert-scheduled-subject', true);
					$subject_user = $subject_user_custom ? $subject_user_custom : $usp_admin['scheduled_subject'];
					$subject_user = sanitize_text_field($subject_user);
					
					$subject_admin_custom = get_post_meta($post_id, 'usp-alert-scheduled-subject-admin', true);
					$subject_admin = $subject_admin_custom ? $subject_admin_custom : $usp_admin['scheduled_subject_admin'];
					$subject_admin = sanitize_text_field($subject_admin);
					
					$message_user_custom = get_post_meta($post_id, 'usp-alert-scheduled-message', true);
					$message_user = $message_user_custom ? $message_user_custom : $usp_admin['scheduled_message'];
					$message_user = $this->sanitize_content(html_entity_decode(stripslashes($message_user), ENT_QUOTES, get_option('blog_charset', 'UTF-8')));
					
					$message_admin_custom = get_post_meta($post_id, 'usp-alert-scheduled-message-admin', true);
					$message_admin = $message_admin_custom ? $message_admin_custom : $usp_admin['scheduled_message_admin'];
					$message_admin = $this->sanitize_content(html_entity_decode(stripslashes($message_admin), ENT_QUOTES, get_option('blog_charset', 'UTF-8')));
					
					$carbon_copies_custom = '';
					if ($admin_email_custom = get_post_meta($post_id, 'usp-alert-scheduled-cc-admin', true)) {
						$admin_email_custom = array_map('trim', explode(',', $admin_email_custom));
						$i = 0; $max = apply_filters('usp_custom_alert_max', 3);
						foreach ($admin_email_custom as $value) {
							if ($i < $max) $carbon_copies_custom .= $value .',';
							$i++;
						}
					}
					$carbon_copies = ltrim($carbon_copies_custom, ', ') . trim(esc_attr($usp_admin['cc_scheduled']), ', ');
					$carbon_copies = trim($carbon_copies, ', ');
					
					if ($usp_admin['send_scheduled_user'])  $send_user  = true;
					if ($usp_admin['send_scheduled_admin']) $send_admin = true;
					
				}
				
				$message_user  = $this->regex_filter($message_user, $args);
				$message_admin = $this->regex_filter($message_admin, $args);
				
				$subject_user  = $this->regex_filter($subject_user, $args);
				$subject_admin = $this->regex_filter($subject_admin, $args);
				
				$bcc = ''; $cc_emails = explode(",", $carbon_copies);
				foreach ($cc_emails as $email) if (!empty($email)) $bcc .= 'Bcc: '. sanitize_email(rtrim(trim($email))) . "\r\n";
				
				$vars = array(
					'form_submit' => $form_submit,
					'form_email'  => $form_email,
					'send_mail'   => $send_mail,
					//
					'admin_name'    => $admin_name,
					'admin_from'    => $admin_from,
					'admin_email'   => $admin_email,
					'user_email'    => $user_email,
					'post_id'       => $post_id,
					'headers'       => $headers,
					//
					'subject_user'  => $subject_user, 
					'subject_admin' => $subject_admin, 
					'message_user'  => $message_user, 
					'message_admin' => $message_admin, 
					'carbon_copies' => $carbon_copies,
					'send_user'     => $send_user,
					'send_admin'    => $send_admin,
					'bcc'           => $bcc,
				);
				return $vars;
			}
			return false;
		}
		public static function get_email_info($post_id) {
			global $usp_general, $usp_admin;
			
			$args = array();
			$args['blog_url']      = esc_html__('Blog URL', 'usp-pro');
			$args['blog_name']     = esc_html__('Blog Name', 'usp-pro');
			$args['admin_name']    = esc_html__('Admin Name', 'usp-pro');
			$args['admin_from']    = esc_html__('Admin From', 'usp-pro');
			$args['admin_email']   = esc_html__('Admin Email', 'usp-pro');
			$args['user_name']     = esc_html__('User Name', 'usp-pro');
			$args['user_email']    = esc_html__('User Email', 'usp-pro');
			$args['post_date']     = esc_html__('Post Date', 'usp-pro');
			$args['post_title']    = esc_html__('Post Title', 'usp-pro');
			$args['post_url']      = esc_html__('Post URL', 'usp-pro');
			$args['post_cats']     = esc_html__('Post Categories', 'usp-pro');
			$args['post_tags']     = esc_html__('Post Tags', 'usp-pro');
			$args['post_id']       = esc_html__('Post ID', 'usp-pro');
			$args['post_object']   = esc_html__('Post Object', 'usp-pro');
			$args['post_content']  = esc_html__('Post Content', 'usp-pro');
			$args['post_custom']   = esc_html__('Post Custom', 'usp-pro');
			$args['usp_author']    = esc_html__('Post Author', 'usp-pro');
			$args['ip_address']    = usp_get_ip();
			
			if (!empty($post_id) && is_numeric($post_id)) {
				
				$blog_url    = home_url();
				$blog_name   = get_bloginfo('name');
				$admin_name  = trim($usp_admin['admin_name']);
				$admin_from  = trim($usp_admin['admin_from']);
				$admin_email = trim($usp_admin['admin_email']);
				
				$post = get_post($post_id);
				if (is_object($post)) {
					$post_date  = $post->post_date;
					$user_id    = $post->post_author;
					$user_name  = get_the_author_meta('display_name', $user_id);
					$user_email = get_the_author_meta('user_email', $user_id);
					$usp_author = get_post_meta($post_id, 'usp-author', true);
					$meta_email = get_post_meta($post_id, 'usp-email', true);
				}
				if (!empty($meta_email)) $user_email = $meta_email;
				
				$post_title = get_the_title($post_id);
				$post_url   = get_permalink($post_id);
				$post_cats  = wp_get_post_categories($post_id, array('fields' => 'names'));
				$post_tags  = wp_get_post_tags($post_id, array('fields' => 'names'));
				
				$categories = '';
				if ($post_cats) {
					foreach ($post_cats as $cat) {
						$categories .= $cat . ', ';
					}
					$categories = rtrim(trim($categories), ',');
				}
				
				$tags = '';
				if ($post_tags) {
					foreach ($post_tags as $tag) {
						$tags .= $tag . ', ';
					}
					$tags = rtrim(trim($tags), ',');
				}
				
				if (!empty($blog_url))      $args['blog_url']      = $blog_url;
				if (!empty($blog_name))     $args['blog_name']     = $blog_name;
				if (!empty($admin_name))    $args['admin_name']    = $admin_name;
				if (!empty($admin_from))    $args['admin_from']    = $admin_from;
				if (!empty($admin_email))   $args['admin_email']   = $admin_email;
				if (!empty($user_name))     $args['user_name']     = $user_name;
				if (!empty($user_email))    $args['user_email']    = $user_email;
				if (!empty($post_date))     $args['post_date']     = $post_date;
				if (!empty($post_title))    $args['post_title']    = $post_title;
				if (!empty($post_url))      $args['post_url']      = $post_url;
				if (!empty($categories))    $args['post_cats']     = $categories;
				if (!empty($tags))          $args['post_tags']     = $tags;
				if (!empty($post_id))       $args['post_id']       = $post_id;
				if (!empty($usp_author))    $args['usp_author']    = $usp_author;
			}
			
			return apply_filters('usp_get_email_info', $args);
		}
		public static function regex_filter($string, $args) {
			
			$string = trim($string);
			
			$blog_url       = $args['blog_url'];
			$blog_name      = $args['blog_name'];
			$admin_name     = $args['admin_name'];
			$admin_email    = $args['admin_email'];
			$user_name      = $args['user_name'];
			$user_email     = $args['user_email'];
			$post_title     = $args['post_title'];
			$post_date      = $args['post_date'];
			$post_url       = $args['post_url'];
			$post_cats      = $args['post_cats'];
			$post_tags      = $args['post_tags'];
			$post_id        = $args['post_id'];
			$usp_author     = $args['usp_author'];
			$post_object    = $args['post_object'];
			$post_content   = $args['post_content'];
			$post_custom    = $args['post_custom'];
			$post_submitted = get_post_meta($post_id, 'usp-post-time') ? get_post_meta($post_id, 'usp-post-time', true) : $post_date;
			$post_schedule  = apply_filters('usp_alert_shortcut_schedule_format', get_the_time('l, F j, Y @ h:i:s a', $post_id)); 
			$post_defined   = apply_filters('usp_alert_shortcut_defined_replacement', usp_get_meta($post_id, 'usp-custom-reject')); // ;)
			$ip_address     = $args['ip_address'];
			$edit_link      = get_edit_post_link($post_id, '');
			
			if (is_numeric($post_id)) {
				$post_object = get_post($post_id);
				$post_custom = get_post_custom($post_id);
			}
			if (is_object($post_object)) {
				$post_content = $post_object->post_content;
			}
			
			$custom_fields = '';
			if (is_array($post_custom)) {
				if (isset($post_custom['usp-author']))    unset($post_custom['usp-author']);
				if (isset($post_custom['usp-author-id'])) unset($post_custom['usp-author-id']);
				if (isset($post_custom['is_submission'])) unset($post_custom['is_submission']);
				if (isset($post_custom['usp-post-id']))   unset($post_custom['usp-post-id']);
				if (isset($post_custom['usp-subject']))   unset($post_custom['usp-subject']);
				if (isset($post_custom['usp-email']))     unset($post_custom['usp-email']);
				//
				foreach ($post_custom as $key => $value) {
					
					if (is_array($value)) {
						
						foreach ($value as $k => $v) {
							
							$custom_fields .= $key .': '. $v ."\n";
							
							$string = preg_replace("/%%__" . preg_quote($key) . "%%/", $v, $string);
							
						}
						
					} else {
						
						$custom_fields .= $key .': '. $value ."\n";
						
						$string = preg_replace("/%%__" . preg_quote($key) . "%%/", $value, $string);
						
					}
					
				}
			}
			
			$attachments = get_posts(apply_filters('usp_alert_shortcut_attachment_args', array('post_parent' => $post_id, 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' => 'any')));
			$files = '';
			if ($attachments) {
				foreach($attachments as $attachment) {
					$files .= wp_get_attachment_url($attachment->ID) ."\n";
				}
			}
			
			$replace = apply_filters('usp_alert_shortcut_replace_empty', '');
			 
			$string = preg_replace("(%%__[a-z0-9-_]+%%)", $replace, $string);
			
			$patterns = array();
			$patterns[0]  = "/%%blog_url%%/";
			$patterns[1]  = "/%%blog_name%%/";
			$patterns[2]  = "/%%admin_name%%/";
			$patterns[3]  = "/%%admin_email%%/";
			$patterns[4]  = "/%%user_name%%/";
			$patterns[5]  = "/%%user_email%%/";
			$patterns[6]  = "/%%post_title%%/";
			$patterns[7]  = "/%%post_date%%/";
			$patterns[8]  = "/%%post_url%%/";
			$patterns[9]  = "/%%post_cats%%/";
			$patterns[10] = "/%%post_tags%%/";
			$patterns[11] = "/%%post_id%%/";
			$patterns[12] = "/%%post_author%%/";
			$patterns[13] = "/%%post_content%%/";
			$patterns[14] = "/%%post_custom%%/";
			$patterns[15] = "/%%post_submitted_date%%/";
			$patterns[16] = "/%%post_scheduled_date%%/";
			$patterns[17] = apply_filters('usp_alert_shortcut_defined_pattern', "/%%post_reject_reason%%/"); // ;)
			$patterns[18] = "/%%ip_address%%/";
			$patterns[19] = "/%%edit_link%%/";
			$patterns[20] = "/%%files%%/";
			
			$replacements = array();
			$replacements[0]  = $blog_url;
			$replacements[1]  = $blog_name;
			$replacements[2]  = $admin_name;
			$replacements[3]  = $admin_email;
			$replacements[4]  = $user_name;
			$replacements[5]  = $user_email;
			$replacements[6]  = $post_title;
			$replacements[7]  = $post_date;
			$replacements[8]  = $post_url;
			$replacements[9]  = $post_cats;
			$replacements[10] = $post_tags;
			$replacements[11] = $post_id;
			$replacements[12] = $usp_author;
			$replacements[13] = $post_content;
			$replacements[14] = $custom_fields;
			$replacements[15] = $post_submitted; 
			$replacements[16] = $post_schedule;
			$replacements[17] = $post_defined;
			$replacements[18] = $ip_address;
			$replacements[19] = $edit_link;
			$replacements[20] = $files;
			
			$string = html_entity_decode(preg_replace($patterns, $replacements, $string), ENT_QUOTES, get_option('blog_charset', 'UTF-8'));
			
			return apply_filters('usp_regex_filter', $string);
		}
		public function generate_password($fields, $length = 16) {
			
			$length = apply_filters('usp_generate_password_length', $length);
			
			if (!empty($fields['usp_password'])) {
				$password = $fields['usp_password'];
			} else {
				$password = wp_generate_password($length);
			}
			return apply_filters('usp_generate_password', $password);
		}
		public function challenge_question($input) {
			global $usp_general;
			$response = $usp_general['captcha_response'];
			$response = stripslashes(trim($response));
			if ($usp_general['captcha_casing'] == false) $return = (strtoupper($input) == strtoupper($response));
			else $return = ($input == $response);
			return apply_filters('usp_challenge_question', $return);
		}
		public function submission_redirect($usp_redirect, $args, $post_id) {
			
			global $usp_general, $usp_advanced;
			
			$errors_cleared = $this->get_query_vars();
			
			if (isset($args['errors_display']) && !empty($args['errors_display'])) $errors_display = $args['errors_display'];
			else $errors_display = false;
			
			if ($errors_display) {
				
				$e = array();
				
				foreach ($errors_display as $key => $value) {
					
					if (stripos($value, 'usp_error_8') !== false) {
						
						parse_str($value, $array);
						
						$e[] = $array;
						
						unset($errors_display[$key]);
						
					}
				}
				
				$new_array = isset($e[0]) ? $e[0] : array(); 
				
				$errors_display = array_merge($errors_display, $new_array);
				
			}
			
			if (isset($args['redirect_type']) && !empty($args['redirect_type'])) $redirect_type = $args['redirect_type'];
			else $redirect_type = false;
			
			if (isset($usp_advanced['redirect_failure']) && !empty($usp_advanced['redirect_failure'])) $redirect_fail = $usp_advanced['redirect_failure'];
			else $redirect_fail = false;
			
			if (isset($usp_general['redirect_post']) && !empty($usp_general['redirect_post'])) $redirect_post = $usp_general['redirect_post'];
			else $redirect_post = false;
			
			if (isset($usp_advanced['redirect_success']) && !empty($usp_advanced['redirect_success'])) $redirect_success = $usp_advanced['redirect_success'];
			else $redirect_success = false;
			
			$redirect = stripslashes($_SERVER['REQUEST_URI']);
			
			if ($redirect_type == 'redirect_failure') {
				if (isset($redirect_fail) && !empty($redirect_fail)) $redirect = $redirect_fail;
				$add_query_arg = $errors_display;
			} else {
				if ($redirect_post && !empty($post_id)) $redirect = get_permalink($post_id);
				elseif ($redirect_success) $redirect = $redirect_success;
				
				if (isset($usp_redirect) && !empty($usp_redirect)) $redirect = $usp_redirect;
				
				if ($redirect_type == 'redirect_register') {
					if (empty($post_id) || !is_numeric($post_id)) $post_id = 'register';
					$add_query_arg = array('usp_success' => '1', 'action' => $post_id);
					
				} elseif ($redirect_type == 'redirect_post') {
					$add_query_arg = array('usp_success' => '2', 'post_id' => $post_id);
					
				} elseif ($redirect_type == 'redirect_both') {
					$add_query_arg = array('usp_success' => '3', 'post_id' => $post_id);
					
				} elseif ($redirect_type == 'redirect_email') {
					if (empty($post_id) || !is_numeric($post_id)) $post_id = 'contact';
					$add_query_arg = array('usp_success' => '4', 'post_id' => $post_id);
					
				} elseif ($redirect_type == 'redirect_email_register') {
					if (empty($post_id) || !is_numeric($post_id)) $post_id = 'email_register';
					$add_query_arg = array('usp_success' => '5', 'post_id' => $post_id);
					
				} elseif ($redirect_type == 'redirect_email_post') {
					if (empty($post_id) || !is_numeric($post_id)) $post_id = 'email_post';
					$add_query_arg = array('usp_success' => '6', 'post_id' => $post_id);
					
				} elseif ($redirect_type == 'redirect_email_both') {
					if (empty($post_id) || !is_numeric($post_id)) $post_id = 'email_both';
					$add_query_arg = array('usp_success' => '7', 'post_id' => $post_id);
				}
				$this->clear_session_vars();
			}
			$return_args = array('usp_success', 'usp_error_register', 'usp_error_post', 'usp_error_mail');
			$remove_query_arg = array_merge($errors_cleared, $return_args);
			$redirect = remove_query_arg($remove_query_arg, $redirect);
			$redirect = add_query_arg($add_query_arg, $redirect);
			do_action('usp_submission_redirect_before', $redirect);
			wp_redirect(esc_url_raw($redirect));
			exit;
		}
		public static function get_query_vars() {
			$query = array();
			if (isset($_SERVER['QUERY_STRING'])) {
				if (preg_match("/usp_(.*)/i", $_SERVER['QUERY_STRING'], $match)) {
					if (isset($_POST['usp_form_submit'])) parse_str(sanitize_text_field($match[0]), $query);
				}
			}
			return apply_filters('usp_get_query_vars', $query);
		}
		public function add_query_vars($vars) {
			if (isset($_POST['usp_form_submit'])) {
				$query = $this->get_query_vars();
				if (!empty($query)) {
					foreach ($query as $key => $value) $vars[] = sanitize_text_field($key);
				}
			}
			return apply_filters('usp_add_query_vars', $vars);
		}
		public function global_allowed_file_types() {
			global $usp_uploads;
			$global_allow = array();
			$global_types = rtrim(trim($usp_uploads['files_allow']), ',');
			$global_types = explode(',', $global_types);
			foreach ($global_types as $type) $global_allow[] = strtolower(rtrim(trim($type), ','));	
			return $global_allow;
		}
		public function custom_allowed_file_types($key) {
			$custom_allow = array();
			if (isset($_POST[$key])) {
				$custom_types = rtrim(trim($_POST[$key]), ',');
				$custom_types = explode(',', $custom_types);
				foreach ($custom_types as $type) $custom_allow[] = strtolower(rtrim(trim($type), ','));
			}
			return $custom_allow;
		}
		public function get_min_max_files($min_or_max) {
			$min_files = array();
			$min_array = array();
			$min_unique = array_unique($min_or_max);
			foreach ($min_unique as $key => $value) {
				$min_array[] = explode('|', $value);
			}
			foreach ($min_array as $key => $value) {
				foreach ($value as $k => $v) {
					if (is_numeric($v)) $min_files[] = intval($v);
				}
			}
			return array_sum($min_files);
		}
		//
		public function get_file_key_data($usp_files, $files_key, $files_value) {
			global $usp_uploads;
			
			if (preg_match("/^usp-file-([0-9a-z_-]+)$/i", $files_key, $match)) {
				
				foreach ($files_value as $key => $value) {
					if (is_array($value)) {
						foreach ($value as $k => $v) $usp_files[$key][] = $v;
					} else {
						$usp_files[$key][] = $value;
					}
				}
				if (!empty($_POST['usp-file-required-single'])) {
					$usp_files['req'][] = 'required';
					$usp_files['min'][] = $files_key .'|1';
					
				} elseif (!empty($_POST['usp-file-required-'.$match[1]])) {
					$usp_files['req'][] = 'required';
					$usp_files['min'][] = $files_key .'|'. $_POST['usp-file-required-'.$match[1]];
					
				} else {
					$usp_files['req'][] = 'optional';
					$usp_files['min'][] = $files_key .'|'. $usp_uploads['min_files'];
				}
				if (!empty($_POST['usp-file-limit-'.$match[1]])) {
					$usp_files['max'][] = $files_key .'|'. $_POST['usp-file-limit-'.$match[1]];
				} else {
					$usp_files['max'][] = $files_key .'|'. $usp_uploads['max_files'];
				}
				$usp_files['key'][] = $match[1];
				$usp_files['types'][] = $this->custom_allowed_file_types('usp-file-types');
				$usp_files['field'][] = $files_key;	
			}	
			return $usp_files;
		}
		public function get_usp_files_data($usp_files, $files_key, $files_value) {
			global $usp_uploads;
			
			foreach ($files_value as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $k => $v) $usp_files[$key][] = $v;
				} else {
					$usp_files[$key][] = $value;
				}
			}
			for ($i = 0; $i < count($files_value['name']); $i++) {
				
				if (isset($_POST['usp-files-required'])) {
					$usp_files['req'][] = 'required';
					$usp_files['min'][] = $files_key .'|'. $_POST['usp-files-required'];
				} else { 
					$usp_files['req'][] = 'optional';
					$usp_files['min'][] = $files_key .'|'. $usp_uploads['min_files'];
				}
				if (!empty($_POST['usp-file-limit'])) {
					$usp_files['max'][] = $files_key .'|'. $_POST['usp-file-limit'];
				} else {
					$usp_files['max'][] = $files_key .'|'. $usp_uploads['max_files'];
				}
				$usp_files['key'][] = '0';
				$usp_files['types'][] = $this->custom_allowed_file_types('usp-file-types');
				$usp_files['field'][] = $files_key;
			}
			return $usp_files;
		}
		public function get_custom_files_data($usp_files, $files_key, $files_value, $match, $prefix) {
			global $usp_uploads;
			
			if (strpos($match[0], 'usp_custom_file_') !== false) $prefix = 'usp_custom_file_';
			
			foreach ($files_value as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $k => $v) $usp_files[$key][] = $v;
				} else {
					$usp_files[$key][] = $value;
				}
			}
			for ($i = 0; $i < count($files_value['name']); $i++) {
				
				if (isset($_POST[$prefix . $match[1] .'-required'])) {
					$usp_files['req'][] = 'required';
					$usp_files['min'][] = $files_key .'|'. $_POST[$prefix . $match[1] .'-required'];
				} else {
					$usp_files['req'][] = 'optional';
					$usp_files['min'][] = $files_key .'|'. $usp_uploads['min_files'];
				}
				if (!empty($_POST[$prefix . $match[1] .'-limit'])) {
					$usp_files['max'][] = $files_key .'|'. $_POST[$prefix . $match[1] .'-limit'];
				} else {
					$usp_files['max'][] = $files_key .'|'. $usp_uploads['max_files'];
				}
				$usp_files['key'][] = '0';
				$usp_files['types'][] = $this->custom_allowed_file_types($match[0] .'-types');
				$usp_files['field'][] = $files_key;
			}
			return $usp_files;
		}
		public function get_custom_custom_files_data($usp_files, $files_key, $files_value) {
			global $usp_uploads;
			
			foreach ($files_value as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $k => $v) $usp_files[$key][] = $v;
				} else {
					$usp_files[$key][] = $value;
				}
			}	
			for ($i = 0; $i < count($files_value['name']); $i++) {
				
				if (isset($_POST[$files_key .'-required'])) {
					$usp_files['req'][] = 'required';
					$usp_files['min'][] = $files_key .'|'. $_POST[$files_key .'-required'];
				} else { 
					$usp_files['req'][] = 'optional';
					$usp_files['min'][] = $files_key .'|'. $usp_uploads['min_files'];
				}
				if (!empty($_POST[$files_key .'-limit'])) {
					$usp_files['max'][] = $files_key .'|'. $_POST[$files_key .'-limit'];
				} else {
					$usp_files['max'][] = $files_key .'|'. $usp_uploads['max_files'];
				}
				$usp_files['key'][] = '0';
				$usp_files['types'][] = $this->custom_allowed_file_types($files_key .'-types');
				$usp_files['field'][] = $files_key;
			}
			return $usp_files;
		}
		//
		public function process_files() {
			
			global $usp_uploads, $usp_advanced;
			
			$usp_files = array(); 
			$error_8   = array();
			
			$custom_custom = usp_merge_custom_fields();
			
			$prefix = 'null___';
			if (isset($usp_advanced['custom_prefix']) && !empty($usp_advanced['custom_prefix'])) $prefix = $usp_advanced['custom_prefix'];
			
			if (isset($_FILES) && !empty($_FILES)) {
				
				foreach ($_FILES as $files_key => $files_value) {
					
					if ($files_key === 'usp-files') {
						
						$usp_files = $this->get_usp_files_data($usp_files, $files_key, $files_value);
						
					} elseif (preg_match("/^usp-file-(single|[0-9]+)$/i", $files_key, $match)) {
						
						$usp_files = $this->get_file_key_data($usp_files, $files_key, $files_value);
						
					} elseif (in_array($files_key, $custom_custom)) {
						
						$usp_files = $this->get_custom_custom_files_data($usp_files, $files_key, $files_value);
						
					} elseif (
						preg_match("/^usp_custom_file_([0-9a-z_-]+)$/i", $files_key, $match) || 
						preg_match("/^". preg_quote($prefix) ."([0-9a-z_-]+)$/i", $files_key, $match)
					) {
						
						$usp_files = $this->get_custom_files_data($usp_files, $files_key, $files_value, $match, $prefix);
						
					}
					
					foreach ($usp_files as $key => $value) {
						
						if (isset($key)) ${$key} = $value;
					}
					
				}
				
				$usp_files = apply_filters('usp_files_combined_array', $usp_files);
				
				$global_allow = $this->global_allowed_file_types();
				
				//
				$loop = 0;
				for ($i = 0; $i < count($tmp_name); $i++) {
					
					$suffix = '--'. $field[$i] .'--'. $i;
					
					if (empty($tmp_name[$i]) || !is_uploaded_file($tmp_name[$i])) {
						
						// check required
						if ($req[$i] == 'required') {
							$error_8[] = 'usp_error_8'. $suffix;
						}
						
					} else {
						
						// check type
						$filetype = wp_check_filetype($name[$i]);
						$extension = $filetype['ext'];
						
						if (empty($extension) || (stripos($name[$i],'.php') !== false)) {
							$error_8[] = 'usp_error_8a'. $suffix;
						}
						
						if (empty($types[$i])) {
							
							if (!in_array(strtolower($extension), $global_allow)) {
								$error_8[] = 'usp_error_8a'. $suffix;
							}
							
						} else {
							
							if (!in_array(strtolower($extension), $types[$i])) {
								$error_8[] = 'usp_error_8a'. $suffix;
							}
							
						}
						
						// check dimensions
						if (is_uploaded_file($tmp_name[$i]) && @exif_imagetype($tmp_name[$i]) !== false) {
							$image = getimagesize($tmp_name[$i]);
							if ($image === false || !$this->check_dimensions($image[0], $image[1])) {
								$error_8[] = 'usp_error_8b'. $suffix;
							}
						}
						
						// check max size
						if (!empty($size[$i])) {
							if (intval($size[$i]) > $usp_uploads['max_size']) {
								$error_8[] = 'usp_error_8c'. $suffix;
							}
						}
						
						// check min size
						if (!empty($size[$i])) {
							if (intval($size[$i]) < $usp_uploads['min_size']) {
								$error_8[] = 'usp_error_8d'. $suffix;
							}
						}
						
						// check errors
						if (!empty($error[$i])) {
							if ($req[$i] == 'required') {
								$error_8[] = 'usp_error_8e'. $suffix;
							}
						}
						
						// check file name
						if (!empty($name[$i])) {
							$usp_files['name'][$i] = preg_replace("/[^0-9a-z\.\_\-]/i", "", $usp_files['name'][$i]);
							$usp_files['name'][$i] = preg_replace("/\.+/", ".", $usp_files['name'][$i]);
							$usp_files['name'][$i] = preg_replace("/\_+/", "_", $usp_files['name'][$i]);
							$usp_files['name'][$i] = preg_replace("/\-+/", "-", $usp_files['name'][$i]);
							
							if ($usp_uploads['unique_filename']) {
								$usp_files['name'][$i] = date('Y-m-d') . '_' . uniqid() . '_' . $usp_files['name'][$i];
							}
							if (strlen($usp_files['name'][$i]) > 250) {
								$error_8[] = 'usp_error_8f'. $suffix;
							}
						}
						
						//check square image
						if (
							isset($usp_uploads['square_image']) && $usp_uploads['square_image'] && 
							is_uploaded_file($tmp_name[$i]) && exif_imagetype($tmp_name[$i]) !== false
						) {
							$image = getimagesize($tmp_name[$i]);
							if ($image === false || !($this->check_square($image[0], $image[1]))) {
								$error_8[] = 'usp_error_8i'. $suffix;
							}
						}
						
					}
					
					$loop++;
					
				}
				//
				
				if (empty($error_8)) {
					
					$min_files = $this->get_min_max_files(array_unique($usp_files['min']));
					$max_files = $this->get_min_max_files(array_unique($usp_files['max']));
					
					if ($min_files > 0) {
						if ($loop < $min_files) $error_8[] = 'usp_error_8g';
					}
					if ($max_files > 0) {
						if ($loop > $max_files) $error_8[] = 'usp_error_8h';
					}
				}
				
			} else { // no files
				
				foreach ($_POST as $key => $value) {
					if ($key == 'usp-files-required') {
						$error_8[] = 'usp_error_8';
						
					} elseif (preg_match("/^usp-file-required-([0-9]+)$/i", $key, $match)) {
						$error_8[] = 'usp_error_8-'. $match[1];
						
					} elseif (preg_match("/^usp_custom_file_([0-9a-z_-]+)-required$/i", $key, $match)) {
						$error_8[] = 'usp_error_8-'. $match[1];
						
					}
				}
			}
			
			$process_files = array('files' => $usp_files, 'error' => $error_8);
			
			return apply_filters('usp_process_files', $process_files);
			
		}
		public function check_square($width, $height) {
			global $usp_uploads;
			$square = ($width === $height);
			return $square;
		}
		public function check_dimensions($width, $height) {
			global $usp_uploads;
			$width_fits  = ($width  <= intval($usp_uploads['max_width']))  && ($width  >= intval($usp_uploads['min_width']));
			$height_fits = ($height <= intval($usp_uploads['max_height'])) && ($height >= intval($usp_uploads['min_height']));
			return $width_fits && $height_fits;
		}
		public function sanitize_content($string) {
			global $usp_advanced;
			$string = trim($string);
			if ($usp_advanced['html_content'] !== '') {
				$allowed_tags = trim($usp_advanced['html_content']);
				$allowed_tags = explode(",", $allowed_tags);
				$allowed_atts = array(
					'align'      => array(),
					'class'      => array(),
					'type'       => array(),
					'id'         => array(),
					'dir'        => array(),
					'lang'       => array(),
					'style'      => array(),
					'xml:lang'   => array(),
					'src'        => array(),
					'alt'        => array(),
					'href'       => array(),
					'rel'        => array(),
					'rev'        => array(),
					'novalidate' => array(),
					'type'       => array(),
					'value'      => array(),
					'name'       => array(),
					'tabindex'   => array(),
					'action'     => array(),
					'method'     => array(),
					'for'        => array(),
					'width'      => array(),
					'height'     => array(),
					'data'       => array(),
					'title'      => array(),
				);
				$allowed_atts = apply_filters('usp_sanitize_content_atts', $allowed_atts);
				
				$allowedposttags = array();
				foreach ($allowed_tags as $allowed_tag) {
					$allowedposttags[trim($allowed_tag)] = $allowed_atts;
				}
				$enable_kses = apply_filters('usp_sanitize_content_kses', true); // e.g., <?php tags
				$string = $enable_kses ? wp_kses($string, $allowedposttags) : $string;
			} else {
				$string = sanitize_text_field($string);
			}
			return apply_filters('usp_sanitize_content', $string);
		}
		public function set_session_vars() {
			global $usp_general, $usp_advanced;
			$custom_prefix = $usp_advanced['custom_prefix'];
			$custom_merged = usp_merge_custom_fields();
			//
			if (usp_is_session_started() === false) session_start();
			if (isset($_POST)) $_POST = stripslashes_deep($_POST);
			//
			foreach ($_POST as $key => $value) {
				if (preg_match("/^usp-(.*)$/i", $key, $match) || preg_match("/^$custom_prefix(.*)$/i", $key, $match) || in_array($key, $custom_merged)) {
					if ($key !== 'usp-remember') {
						unset($_SESSION['usp_form_session'][$key]);
						if (is_array($value)) {
							foreach ($value as $k => $v) {
								if (isset($usp_general['sessions_on']) && (isset($_COOKIE['remember']) || isset($_POST['usp-remember']))) {
									if ($key == 'usp-content') $_SESSION['usp_form_session'][$key][$k] = $this->sanitize_content($v);
									                      else $_SESSION['usp_form_session'][$key][$k] = htmlspecialchars($v, ENT_QUOTES, get_option('blog_charset', 'UTF-8'));
								} else {
									if (isset($_SESSION['usp_form_session'])) unset($_SESSION['usp_form_session']);
								}
							}
						} else {
							if (isset($usp_general['sessions_on']) && (isset($_COOKIE['remember']) || isset($_POST['usp-remember']))) {
								if ($key == 'usp-content') $_SESSION['usp_form_session'][$key] = $this->sanitize_content($value);
								                      else $_SESSION['usp_form_session'][$key] = htmlspecialchars($value, ENT_QUOTES, get_option('blog_charset', 'UTF-8'));
							} else {
								if (isset($_SESSION['usp_form_session'])) unset($_SESSION['usp_form_session']);
							}
						}
					}
				}
			}
			if (isset($usp_general['sessions_on'])) {
				if (isset($usp_general['sessions_default']) && !empty($usp_general['sessions_default'])) setcookie('remember', 'remember', time() + 86400*30);
				if (isset($_POST['usp-remember'])) {
					setcookie('forget', '', 1);
					setcookie('remember', 'remember', time() + 86400*30);
				} else {
					setcookie('remember', '', 1);
					setcookie('forget', 'forget', time() + 86400*30);
				}
			}
		}
		public function check_session() {
			if (isset($_POST['PHPSESSID']) && $_POST['PHPSESSID'] == session_id()) {
				return true;
			} else {
				session_unset();
				die(esc_html__('Please do not load this page directly. Thanks!', 'usp-pro'));
			}
		}
		public function clear_session_vars() {
			global $usp_general;
			if (usp_is_session_started() === false) session_start();
			if (isset($usp_general['sessions_scope']) && empty($usp_general['sessions_scope'])) {
				$this->unset_session();
			}
			// session_start(); 
			// session_destroy();
		}
		public function unset_session() {
			if (isset($_SESSION['usp_form_session'])) unset($_SESSION['usp_form_session']);
			setcookie('forget', '' , 1);
			setcookie('remember', '' , 1);
		}
		public static function get_user_stats() {
			$time = current_time('mysql');
			
			$protocol = is_ssl() ? 'https://' : 'http://';
			if (isset($_SERVER['REQUEST_URI']) && isset($_SERVER['HTTP_HOST'])) $request = esc_url($protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			else $request = esc_html__('undefined', 'usp-pro');
			
			if (isset($_SERVER['HTTP_REFERER'])) $referer = esc_url($_SERVER['HTTP_REFERER']);
			else $referer = esc_html__('undefined', 'usp-pro');

			$address = usp_get_ip();

			if (isset($_SERVER['HTTP_USER_AGENT'])) $agent = sanitize_text_field($_SERVER['HTTP_USER_AGENT']);
			else $agent = esc_html__('undefined', 'usp-pro');

			$stats = array('usp-time' => $time, 'usp-request' => $request, 'usp-referer' => $referer, 'usp-address' => $address, 'usp-agent' => $agent);
			return apply_filters('usp_get_user_stats', $stats);
		}
		public function reset_form() {
			
			if (isset($_GET['usp_reset_form'])) {
				
				$protocol = is_ssl() ? 'https://' : 'http://';
				$request  = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				
				$redirect = str_replace('?usp_reset_form=true', '', $request);
				$redirect = str_replace('&usp_reset_form=true', '', $redirect);
				
				wp_redirect(esc_url_raw($redirect));
				exit;
				
			}
			
		}
		public function get_field_val() {
			
			global $usp_general, $usp_advanced, $usp_admin;
			
			do_action('usp_get_field_val_before', $_POST);
			
			if (isset($_POST['usp_form_submit']) && empty($_POST['usp-verify']) && wp_verify_nonce($_POST['usp_form_submit'], 'usp_form_submit')) {
				
				// AUTHOR NAME
				$error_1 = '';
				if (isset($_POST['usp-name']) && !blank_or_zero($_POST['usp-name'])) {
					$usp_author = sanitize_text_field($_POST['usp-name']);
					if (usp_check_malicious($usp_author)) $error_1 = 'usp_error_1a';
				} else {
					if (isset($_POST['usp-name-required'])) $error_1 = 'usp_error_1';
					$usp_author = '';
				}
				
				// POST URL
				$error_2 = '';
				if (isset($_POST['usp-url']) && !empty($_POST['usp-url'])) {
					$usp_url = esc_url($_POST['usp-url']);
				} else {
					if (isset($_POST['usp-url-required'])) $error_2 = 'usp_error_2';
					$usp_url = '';
				}
				
				// POST TITLE
				$error_3 = '';
				if (isset($_POST['usp-title']) && !blank_or_zero($_POST['usp-title'])) {
					$usp_title = sanitize_text_field($_POST['usp-title']);
				} else {
					if (isset($_POST['usp-title-required'])) $error_3 = 'usp_error_3';
					$usp_title = '';
				}
				
				// POST TAGS
				$error_4 = '';
				if (isset($_POST['usp-tags']) && !blank_or_zero($_POST['usp-tags'])) {
					if (is_array($_POST['usp-tags'])) {
						$usp_tags = array();
						foreach ($_POST['usp-tags'] as $tag_id) $usp_tags[] = sanitize_text_field($tag_id);
					} else {
						$usp_tags = sanitize_text_field($_POST['usp-tags']);
					}
				} else {
					if (isset($_POST['usp-tags-required'])) $error_4 = 'usp_error_4';
					$usp_tags = '';
				}
				
				// POST CAPTCHA
				$error_5 = '';
				$usp_captcha = '';
				
				if (isset($_POST['usp-captcha']) && !blank_or_zero($_POST['usp-captcha'])) {
					$usp_captcha = sanitize_text_field($_POST['usp-captcha']);
					$pass = $this->challenge_question($_POST['usp-captcha']);
					if (!$pass) $error_5 = 'usp_error_5a';
					
				} elseif (isset($_POST['recaptcha_response_field']) || isset($_POST['g-recaptcha-response'])) {
					$recaptcha_secret = isset($usp_general['recaptcha_private']) ? $usp_general['recaptcha_private'] : null;
					$recaptcha_response = null; $pass = false;
					if (isset($usp_general['recaptcha_version']) && $usp_general['recaptcha_version'] === 'v2') {
						if (version_compare(phpversion(), '5.3.0', '>=')) {
							$pass = require_once(USP_PRO_PATH .'/lib/recaptcha/connect-current.php');
						} else {
							$pass = require_once(USP_PRO_PATH .'/lib/recaptcha/connect-2014.php');
						}
					} else {
						$pass = require_once(USP_PRO_PATH .'/lib/recaptcha/connect-2007.php');
					}
					if (!$pass) $error_5 = 'usp_error_5a';
				} else {
					if (isset($_POST['usp-captcha-required'])) $error_5 = 'usp_error_5';
				}
				
				// POST CATS
				$error_6 = '';
				if (isset($_POST['usp-category']) && !blank_or_zero($_POST['usp-category'])) {
					if (is_array($_POST['usp-category'])) {
						$usp_category = array();
						foreach ($_POST['usp-category'] as $cat_id) $usp_category[] = sanitize_text_field($cat_id);
					} else {
						$usp_category = sanitize_text_field($_POST['usp-category']);
					}
				} else {
					if (isset($_POST['usp-category-required'])) $error_6 = 'usp_error_6';
					$usp_category = '';
				}
				
				// CAT COMBOS
				$usp_cat_combos = array();
				if (isset($_POST['usp-cat-combo-1']) && !blank_or_zero($_POST['usp-cat-combo-1'])) {
					if (is_array($_POST['usp-cat-combo-1'])) foreach ($_POST['usp-cat-combo-1'] as $cat) $usp_cat_combos[] = sanitize_text_field($cat);
					else $usp_cat_combos[] = sanitize_text_field($_POST['usp-cat-combo-1']);
				} else {
					if (isset($_POST['usp-cat-combo-1-required'])) $error_6 = 'usp_error_6';
				}
				if (isset($_POST['usp-cat-combo-2']) && !blank_or_zero($_POST['usp-cat-combo-2'])) {
					if (is_array($_POST['usp-cat-combo-2'])) foreach ($_POST['usp-cat-combo-2'] as $cat) $usp_cat_combos[] = sanitize_text_field($cat);
					else $usp_cat_combos[] = sanitize_text_field($_POST['usp-cat-combo-2']);
				} else {
					if (isset($_POST['usp-cat-combo-2-required'])) $error_6 = 'usp_error_6';
				}
				if (isset($_POST['usp-cat-combo-3']) && !blank_or_zero($_POST['usp-cat-combo-3'])) {
					if (is_array($_POST['usp-cat-combo-3'])) foreach ($_POST['usp-cat-combo-3'] as $cat) $usp_cat_combos[] = sanitize_text_field($cat);
					else $usp_cat_combos[] = sanitize_text_field($_POST['usp-cat-combo-3']);
				} else {
					if (isset($_POST['usp-cat-combo-3-required'])) $error_6 = 'usp_error_6';
				}
				if (isset($_POST['usp-cat-combo-4']) && !blank_or_zero($_POST['usp-cat-combo-4'])) {
					if (is_array($_POST['usp-cat-combo-4'])) foreach ($_POST['usp-cat-combo-4'] as $cat) $usp_cat_combos[] = sanitize_text_field($cat);
					else $usp_cat_combos[] = sanitize_text_field($_POST['usp-cat-combo-4']);
				} else {
					if (isset($_POST['usp-cat-combo-4-required'])) $error_6 = 'usp_error_6';
				}
				if (isset($_POST['usp-cat-combo-5']) && !blank_or_zero($_POST['usp-cat-combo-5'])) {
					if (is_array($_POST['usp-cat-combo-5'])) foreach ($_POST['usp-cat-combo-5'] as $cat) $usp_cat_combos[] = sanitize_text_field($cat);
					else $usp_cat_combos[] = sanitize_text_field($_POST['usp-cat-combo-5']);
				} else {
					if (isset($_POST['usp-cat-combo-5-required'])) $error_6 = 'usp_error_6';
				}
				if (!empty($usp_cat_combos)) {
					if (!empty($usp_category)) {
						$all_cats = array();
						if (is_array($usp_category)) $all_cats = array_merge($usp_cat_combos, $usp_category);
						else $all_cats = array_push($usp_cat_combos, $usp_category);
						$usp_category = array_unique($all_cats);
					} else {
						$usp_category = $usp_cat_combos;
					}
				}
				
				// POST TAX
				$error_14 = array();
				$usp_taxonomy = array();
				$usp_taxonomy_required = array();
				if (isset($_POST) && !empty($_POST)) {
					foreach ($_POST as $key => $value) {
						if (preg_match("/^usp-taxonomy-([0-9a-z_-]+)$/i", $key, $match)) {
							if (strpos($match[0], '-required') === false) {
								if (is_array($value)) {
									foreach ($value as $val) {
										if (!blank_or_zero($val)) $usp_taxonomy[$match[1]][] = sanitize_text_field($val);
									}
								} else {
									if (!blank_or_zero($value)) $usp_taxonomy[$match[1]] = sanitize_text_field($value);
								}
							} else {
								$required = $match[1];
								$required = substr_replace($required, '', -9);
								$usp_taxonomy_required['usp_taxonomy_required_'. $required] = $required;
							}
						}
					}
					foreach ($usp_taxonomy_required as $key => $value) {
						if (blank_or_zero($usp_taxonomy[$value])) {
							$error_14['usp_error_14_'. $value] = 'usp_error_14_'. $value;
						}
					}
				}
				
				// POST CONTENT
				$error_7 = '';
				$content_filter = '';
				$blocked_terms = $usp_advanced['blacklist_terms'];
				if (isset($_POST['usp-content']) && !blank_or_zero($_POST['usp-content'])) {
					$usp_content = $this->sanitize_content($_POST['usp-content']);
					if (!empty($blocked_terms)) {
						$blocked_terms = trim($blocked_terms);
						$blocked_terms = explode("\n", $blocked_terms);
						foreach ($blocked_terms as $term) {
							$term = trim($term);
							if (preg_match("/$term/i", $usp_content)) $content_filter = 'usp_content_filter';
						}
					}
					if (isset($usp_general['character_min']) && !empty($usp_general['character_min'])) {
						if (strlen($usp_content) < (int) $usp_general['character_min']) $error_7 = 'usp_error_7a';
					}
					if (isset($usp_general['character_max']) && !empty($usp_general['character_max'])) {
						if (strlen($usp_content) > (int) $usp_general['character_max']) $error_7 = 'usp_error_7b';
					}
				} else {
					if (isset($_POST['usp-content-required'])) $error_7 = 'usp_error_7';
					$usp_content = '';
				}
				
				// POST EXCERPT
				$error_19 = '';
				$excerpt_filter = '';
				if (isset($_POST['usp-excerpt']) && !blank_or_zero($_POST['usp-excerpt'])) {
					$usp_excerpt = $this->sanitize_content($_POST['usp-excerpt']);
					if (!empty($blocked_terms)) {
						$blocked_terms = trim($blocked_terms);
						$blocked_terms = explode("\n", $blocked_terms);
						foreach ($blocked_terms as $term) {
							$term = trim($term);
							if (preg_match("/$term/i", $usp_excerpt)) $excerpt_filter = 'usp_excerpt_filter';
						}
					}
					if (isset($usp_general['character_min']) && !empty($usp_general['character_min'])) {
						if (strlen($usp_excerpt) < (int) $usp_general['character_min']) $error_19 = 'usp_error_19a';
					}
					if (isset($usp_general['character_max']) && !empty($usp_general['character_max'])) {
						if (strlen($usp_excerpt) > (int) $usp_general['character_max']) $error_19 = 'usp_error_19b';
					}
				} else {
					if (isset($_POST['usp-excerpt-required'])) $error_19 = 'usp_error_19';
					$usp_excerpt = '';
				}
				
				// POST FILES
				$process_files = $this->process_files();
				
				if (isset($process_files['files'])) $usp_files = $process_files['files'];
				
				if (isset($process_files['error'])) {
					
					$error_8 = '';
					
					foreach ($process_files['error'] as $files_error) {
						
						$error_8 .= $files_error .'='. $files_error .'&';
						
					}
					
					$error_8 = rtrim($error_8, '&');
					
				}
				
				// POST EMAIL
				$error_9 = '';
				if (isset($_POST['usp-email']) && !empty($_POST['usp-email'])) {
					$usp_email = sanitize_email($_POST['usp-email']);
					if (usp_check_malicious($usp_email) || !is_email($usp_email)) $error_9 = 'usp_error_9a';
				} else {
					if (isset($_POST['usp-email-required'])) $error_9 = 'usp_error_9';
					$usp_email = '';
				}
				
				// POST SUBJECT
				$error_10 = '';
				if (isset($_POST['usp-subject']) && !blank_or_zero($_POST['usp-subject'])) {
					$usp_subject = sanitize_text_field($_POST['usp-subject']);
					if (usp_check_malicious($usp_subject)) $error_10 = 'usp_error_10a';
				} else {
					if (isset($_POST['usp-subject-required'])) $error_10 = 'usp_error_10';
					$usp_subject = '';
				}
				
				// EMAIL ALERTS
				$email_alert = array(
					'usp-alert-submit-subject',    'usp-alert-submit-message',    'usp-alert-submit-cc-admin',    'usp-alert-submit-subject-admin',    'usp-alert-submit-message-admin', 
					'usp-alert-approval-subject',  'usp-alert-approval-message',  'usp-alert-approval-cc-admin',  'usp-alert-approval-subject-admin',  'usp-alert-approval-message-admin', 
					'usp-alert-denied-subject',    'usp-alert-denied-message',    'usp-alert-denied-cc-admin',    'usp-alert-denied-subject-admin',    'usp-alert-denied-message-admin', 
					'usp-alert-scheduled-subject', 'usp-alert-scheduled-message', 'usp-alert-scheduled-cc-admin', 'usp-alert-scheduled-subject-admin', 'usp-alert-scheduled-message-admin',
				);
				$email_alerts = array();
				foreach ($email_alert as $alert) {
					if (isset($_POST[$alert]) && !empty($_POST[$alert])) {
						$email_alerts[$alert] = $_POST[$alert];
					}
				}
				
				// POST FORMAT
				$error_15 = '';
				if (isset($_POST['usp-custom-format']) && !blank_or_zero($_POST['usp-custom-format'])) {
					$usp_format = sanitize_text_field($_POST['usp-custom-format']);
				} else {
					if (isset($_POST['usp-custom-format-required'])) $error_15 = 'usp_error_15';
					$usp_format = '';
				}
				
				// ALT CAPTION DESC TITLE NAME
				$error_11 = ''; $usp_alt        = array();
				$error_12 = ''; $usp_caption    = array();
				$error_13 = ''; $usp_desc       = array();
				$error_16 = ''; $usp_mediatitle = array();
				$error_17 = ''; $usp_filename   = array();
				if (isset($_POST) && !empty($_POST)) {
					foreach ($_POST as $key => $value) {
						if (preg_match("/^usp-custom-alt-([0-9]+)$/i", $key, $match)) {
							if (!blank_or_zero($value)) {
								${'usp_alt_'. $match[1]} = sanitize_text_field($value);
							} else {
								if (isset($_POST['usp-custom-alt-'. $match[1] .'-required'])) {
									${'usp_alt_'. $match[1]} = '';
									$error_11 = 'usp_error_11';
								} else {
									${'usp_alt_'. $match[1]} = '';
								}
							}
							$usp_alt[] = ${'usp_alt_'. $match[1]};
						}
						if (preg_match("/^usp-custom-caption-([0-9]+)$/i", $key, $match)) {
							if (!blank_or_zero($value)) {
								${'usp_caption_'. $match[1]} = sanitize_text_field($value);
							} else {
								if (isset($_POST['usp-custom-caption-'. $match[1] .'-required'])) {
									${'usp_caption_'. $match[1]} = '';
									$error_12 = 'usp_error_12';
								} else {
									${'usp_caption_'. $match[1]} = '';
								}
							}
							$usp_caption[] = ${'usp_caption_'. $match[1]};
						}
						if (preg_match("/^usp-custom-desc-([0-9]+)$/i", $key, $match)) {
							if (!blank_or_zero($value)) {
								${'usp_desc_'. $match[1]} = sanitize_text_field($value);
							} else {
								if (isset($_POST['usp-custom-desc-'. $match[1] .'-required'])) {
									${'usp_desc_'. $match[1]} = '';
									$error_13 = 'usp_error_13';
								} else {
									${'usp_desc_'. $match[1]} = '';
								}
							}
							$usp_desc[] = ${'usp_desc_'. $match[1]};
						}
						if (preg_match("/^usp-custom-mediatitle-([0-9]+)$/i", $key, $match)) {
							if (!blank_or_zero($value)) {
								${'usp_mediatitle_'. $match[1]} = sanitize_text_field($value);
							} else {
								if (isset($_POST['usp-custom-mediatitle-'. $match[1] .'-required'])) {
									${'usp_mediatitle_'. $match[1]} = '';
									$error_16 = 'usp_error_16';
								} else {
									${'usp_mediatitle_'. $match[1]} = '';
								}
							}
							$usp_mediatitle[] = ${'usp_mediatitle_'. $match[1]};
						}
						if (preg_match("/^usp-custom-filename-([0-9]+)$/i", $key, $match)) {
							if (!blank_or_zero($value)) {
								${'usp_filename_'. $match[1]} = sanitize_text_field($value);
							} else {
								if (isset($_POST['usp-custom-filename-'. $match[1] .'-required'])) {
									${'usp_filename_'. $match[1]} = '';
									$error_17 = 'usp_error_17';
								} else {
									${'usp_filename_'. $match[1]} = '';
								}
							}
							$usp_filename[] = ${'usp_filename_'. $match[1]};
						}
					}
				}
				
				// AGREE TO TERMS
				$error_18 = '';
				if (isset($_POST['usp-agree']) && !empty($_POST['usp-agree'])) {
					$usp_agree = sanitize_text_field($_POST['usp-agree']);
				} else {
					if (isset($_POST['usp-agree-required'])) $error_18 = 'usp_error_18';
					$usp_agree = '';
				}
				
				// CUSTOM FIELDS
				$usp_custom = array();
				$usp_required = array();
				$usp_error_custom = array();
				$prefix = 'null___';
				if (isset($usp_advanced['custom_prefix']) && !empty($usp_advanced['custom_prefix'])) $prefix = $usp_advanced['custom_prefix'];
				//
				if (isset($_POST) && !empty($_POST)) {
					
					foreach ($_POST as $key => $value) {
						
						$custom_field_regex = apply_filters('usp_custom_field_regex', '([0-9a-z_-]+)');
						
						if ((preg_match("/^usp-custom-". $custom_field_regex ."$/i", $key, $match)) || (preg_match("/^$prefix". $custom_field_regex ."?$/i", $key, $match))) {
							
							if (strpos($match[0], 'usp-custom-') !== false) $field = 'usp-custom-';
							else $field = $prefix;
							
							$excludes = array(
								'-nicename', '-displayname', '-nickname', '-firstname', '-lastname', '-description', 
								'-password', '-format', '-type', '-caption', '-desc', '-alt', '-mediatitle', '-filename'
							);
							
							foreach ($excludes as $exclude) {
								
								if (strpos($match[0], $exclude) !== false) continue 2;
								
							}
							
							if (strpos($match[1], '-required') === false) {
								
								if (is_array($value)) {
									foreach ($value as $val) {
										if (!blank_or_zero($val)) {
											$usp_custom[$field . $match[1]][] = apply_filters('usp_custom_value', htmlspecialchars($val, ENT_QUOTES, get_option('blog_charset', 'UTF-8')), $field . $match[1]);
										}
									}
								} else {
									if (!blank_or_zero($value)) {
										$usp_custom[$field . $match[1]] = apply_filters('usp_custom_value', htmlspecialchars($value, ENT_QUOTES, get_option('blog_charset', 'UTF-8')), $field . $match[1]);
									}
								}
								
							} else {
								
								$required = substr_replace($match[1], '', -9);
								$usp_required['usp_required_'. $key] = $required;
								
							}
						}
					}
					
					foreach ($usp_required as $key => $value) {
						
						if (isset($usp_files['field'])) {
							foreach ($usp_files['field'] as $field) {
								
								if (strpos($key, $prefix) !== false) { 
									
									if (strpos($key, $field) !== false) continue 2;
									
								}
							}
						}
						
						if (strpos($key, $prefix) !== false) { 
							
							if (strpos($error_8, $value) !== false) continue;
							
						}
						
						if (strpos($key, 'usp-custom-') !== false) {
							
							$error_prefix = 'usp_error_custom_';
							$field = 'usp-custom-';
							
						} else {
							
							$error_prefix = 'usp_error_'. $prefix;
							$field = $prefix;
							
						}
						
						if (!isset($usp_custom[$field . $value]) || blank_or_zero($usp_custom[$field . $value])) {
							
							$usp_error_custom[$error_prefix . $value] = $error_prefix . $value;
							
						}
					}
				}
				
				// CUSTOM CUSTOM
				$usp_custom_custom   = array();
				$usp_custom_required = array();
				$usp_ccf_error       = array();
				
				$custom_merged   = usp_merge_custom_fields();
				$custom_required = usp_required_custom_fields();
				$custom_custom   = array_merge($custom_merged, $custom_required);
				
				if (isset($_POST) && !empty($_POST)) {
					foreach ($_POST as $key => $value) {
						if (preg_match("/^usp([_-])/i", $key)) continue;
						//
						if (in_array($key, $custom_custom)) {
							if (preg_match("/^([0-9a-z_-]+)-required$/i", $key, $match)) {
								//
								if (isset($usp_files['field'])) { 
									foreach ($usp_files['field'] as $field) { 
										if (strpos($field, $match[1]) !== false) continue 2;
									}
								}
								if (strpos($error_8, $match[1]) !== false) continue;
								//
								$usp_custom_required[$match[1]] = $match[1];
							} else {
								if (is_array($value)) {
									foreach ($value as $val) {
										if (!blank_or_zero($val)) {
											$usp_custom_custom[$key][] = apply_filters('usp_custom_custom_value', htmlspecialchars($val, ENT_QUOTES, get_option('blog_charset', 'UTF-8')), $key);
										}
									}
								} else {
									if (!blank_or_zero($value)) {
										$usp_custom_custom[$key] = apply_filters('usp_custom_custom_value', htmlspecialchars($value, ENT_QUOTES, get_option('blog_charset', 'UTF-8')), $key);
									}
								}
							}
						}
					}
					foreach ($usp_custom_required as $key => $value) {
						if (!isset($usp_custom_custom[$key]) || blank_or_zero($usp_custom_custom[$key])) $usp_ccf_error['usp_ccf_error_'. $key] = 'usp_ccf_error_'. $key;
					}
				}
				
				// CUSTOM USER
				$error_a = '';
				if (isset($_POST['usp-custom-nicename']) && !blank_or_zero($_POST['usp-custom-nicename'])) {
					$usp_nicename = sanitize_text_field($_POST['usp-custom-nicename']);
				} else {
					if (isset($_POST['usp-custom-nicename-required'])) $error_a = 'usp_error_a';
					$usp_nicename = '';
				}
				$error_b = '';
				if (isset($_POST['usp-custom-displayname']) && !blank_or_zero($_POST['usp-custom-displayname'])) {
					$usp_displayname = sanitize_text_field($_POST['usp-custom-displayname']);
				} else {
					if (isset($_POST['usp-custom-displayname-required'])) $error_b = 'usp_error_b';
					$usp_displayname = '';
				}
				$error_c = '';
				if (isset($_POST['usp-custom-nickname']) && !blank_or_zero($_POST['usp-custom-nickname'])) {
					$usp_nickname = sanitize_text_field($_POST['usp-custom-nickname']);
				} else {
					if (isset($_POST['usp-custom-nickname-required'])) $error_c = 'usp_error_c';
					$usp_nickname = '';
				}
				$error_d = '';
				if (isset($_POST['usp-custom-firstname']) && !blank_or_zero($_POST['usp-custom-firstname'])) {
					$usp_firstname = sanitize_text_field($_POST['usp-custom-firstname']);
				} else {
					if (isset($_POST['usp-custom-firstname-required'])) $error_d = 'usp_error_d';
					$usp_firstname = '';
				}
				$error_e = '';
				if (isset($_POST['usp-custom-lastname']) && !blank_or_zero($_POST['usp-custom-lastname'])) {
					$usp_lastname = sanitize_text_field($_POST['usp-custom-lastname']);
				} else {
					if (isset($_POST['usp-custom-lastname-required'])) $error_e = 'usp_error_e';
					$usp_lastname = '';
				}
				$error_f = '';
				if (isset($_POST['usp-custom-description']) && !blank_or_zero($_POST['usp-custom-description'])) {
					$usp_description = sanitize_text_field($_POST['usp-custom-description']);
				} else {
					if (isset($_POST['usp-custom-description-required'])) $error_f = 'usp_error_f';
					$usp_description = '';
				}
				$error_g = '';
				if (isset($_POST['usp-custom-password']) && !blank_or_zero($_POST['usp-custom-password'])) {
					$usp_password = sanitize_text_field($_POST['usp-custom-password']);
				} else {
					if (isset($_POST['usp-custom-password-required'])) $error_g = 'usp_error_g';
					$usp_password = '';
				}
				
				// OTHERS
				$form_error = '';
				$form_id = '';
				if (isset($_POST['usp-form-id'])) $form_id = intval($_POST['usp-form-id']);
				
				$post_submit = true;
				if (isset($_POST['usp-is-register']) && !isset($_POST['usp-is-post-submit'])) $post_submit = false;
				if (isset($_POST['usp-is-contact']) && !isset($_POST['usp-is-post-submit'])) $post_submit = false;
				if (isset($_POST['usp-send-mail']) && !isset($_POST['usp-is-post-submit'])) $post_submit = false; // dep.
				
				if (isset($_POST['usp-is-post-submit']) || (!isset($_POST['usp-is-register']) && !isset($_POST['usp-is-contact']) && !isset($_POST['usp-send-mail']))) {
					if (isset($_POST['usp-is-post-submit'])) $post_submit = (bool) sanitize_text_field($_POST['usp-is-post-submit']);
					if ($usp_general['enable_form_lock']) {
						if (!usp_check_form_type($form_id, 'submit')) {
							$post_submit = false;
							$form_error = 'usp_error_form';
						}
					}
				}
				$register = false;
				if (isset($_POST['usp-is-register'])) {
					$register = (bool) sanitize_text_field($_POST['usp-is-register']);
					if ($usp_general['enable_form_lock']) {
						if (!usp_check_form_type($form_id, 'register')) {
							$register = false;
							$form_error = 'usp_error_form';
						}
					}
				}
				$contact = false;
				if (isset($_POST['usp-is-contact']) || isset($_POST['usp-send-mail'])) {
					if (!empty($_POST['usp-send-mail']))  $contact = (bool) sanitize_text_field($_POST['usp-send-mail']); // dep.
					if (!empty($_POST['usp-is-contact'])) $contact = (bool) sanitize_text_field($_POST['usp-is-contact']);
					if ($usp_general['enable_form_lock']) {
						if (!usp_check_form_type($form_id, 'contact')) {
							$contact = false;
							$form_error = 'usp_error_form';
						}
					}
				}
				if (isset($_POST['usp-logged-id'])) $logged_id = sanitize_text_field($_POST['usp-logged-id']);
				else $logged_id = '';
	
				if (isset($_POST['usp-logged-cat'])) $logged_cats = sanitize_text_field($_POST['usp-logged-cat']); 
				else $logged_cats = ''; // General Settings > Required Category IDs
				
				if (isset($_POST['usp-tags-default'])) $default_tags = sanitize_text_field($_POST['usp-tags-default']);
				else $default_tags = '';
				
				if (isset($_POST['usp-cats-default'])) $default_cats = sanitize_text_field($_POST['usp-cats-default']);
				else $default_cats = ''; // Assign Category via hidden field
				
				if (isset($_POST['usp-redirect'])) $usp_redirect = esc_url_raw($_POST['usp-redirect']);
				else $usp_redirect = '';
				
				if (isset($_POST['usp-custom-type'])) $custom_type = sanitize_text_field($_POST['usp-custom-type']);
				else $custom_type = '';
				
				if (isset($_POST['usp-contact-ids'])) $contact_ids = sanitize_text_field($_POST['usp-contact-ids']);
				else $contact_ids = '';
				
				if (isset($_POST['usp-post-parent'])) $post_parent = sanitize_text_field($_POST['usp-post-parent']);
				else $post_parent = '';
				
				if (isset($_POST['usp-disable-alerts'])) $disable_alerts = sanitize_text_field($_POST['usp-disable-alerts']);
				else $disable_alerts = '';
				
				if (isset($_POST['usp-post-expires'])) $post_expires = sanitize_text_field($_POST['usp-post-expires']);
				else $post_expires = '';
				
				if (isset($_POST['usp-limit-posts'])) $post_limit = sanitize_text_field($_POST['usp-limit-posts']);
				else $post_limit = '';
				
				// PROCESS
				$fields = array(
					'usp_author'   => $usp_author, 
					'usp_url'      => $usp_url, 
					'usp_title'    => $usp_title, 
					'usp_tags'     => $usp_tags, 
					'usp_captcha'  => $usp_captcha, 
					'usp_category' => $usp_category, 
					'usp_taxonomy' => $usp_taxonomy,
					'usp_content'  => $usp_content, 
					'usp_excerpt'  => $usp_excerpt,
					'usp_files'    => $usp_files, 
					'usp_email'    => $usp_email, 
					'usp_subject'  => $usp_subject, 
					'usp_format'   => $usp_format,
					'usp_agree'    => $usp_agree,
					
					'usp_alt'        => $usp_alt, 
					'usp_caption'    => $usp_caption, 
					'usp_desc'       => $usp_desc, 
					'usp_mediatitle' => $usp_mediatitle,
					'usp_filename'   => $usp_filename,
					
					'usp_custom'        => $usp_custom,
					'usp_custom_custom' => $usp_custom_custom,
					
					'usp_nicename'    => $usp_nicename, 
					'usp_displayname' => $usp_displayname, 
					'usp_nickname'    => $usp_nickname, 
					'usp_firstname'   => $usp_firstname, 
					'usp_lastname'    => $usp_lastname, 
					'usp_description' => $usp_description,
					'usp_password'    => $usp_password,
					
					'usp_form_id'     => $form_id,
					'email_alerts'    => $email_alerts,
					'post_parent'     => $post_parent,
					'disable_alerts'  => $disable_alerts,
					'post_expires'    => $post_expires,
					'post_limit'      => $post_limit,
				);
				
				$errors = array(
					$error_1, $error_2, $error_3, $error_4, $error_5, $error_6, $error_7, $error_8, $error_9, $error_10, 
					$error_11, $error_12, $error_13, $error_14, $error_15, $error_16, $error_17, $error_18, $error_19, 
					$error_a, $error_b, $error_c, $error_d, $error_e, $error_f, $error_g,
					$usp_error_custom, $usp_ccf_error, $form_error, $content_filter
				);
				
				$args = array(
					'fields'          => $fields, 
					'errors'          => $errors, 
					'contact'         => $contact,
					'register'        => $register, 
					'post_submit'     => $post_submit,
					'logged_id'       => $logged_id, 
					'logged_cats'     => $logged_cats, 
					'default_tags'    => $default_tags, 
					'default_cats'    => $default_cats, 
					'usp_redirect'    => $usp_redirect,
					'usp_custom_type' => $custom_type,
					'contact_ids'     => $contact_ids,
				);
				
				return apply_filters('usp_get_field_val', $args);
				
			}
		}
	}
}
