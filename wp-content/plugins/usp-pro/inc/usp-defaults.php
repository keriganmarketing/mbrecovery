<?php // USP Pro - Default Options

if (!defined('ABSPATH')) die();

function usp_default_options_admin() {
	
	$user = USP_Pro::get_user_infos();
	
	if (empty($user)) return;
	
	return array(
		
		'admin_email'             => $user['admin_email'],
		'admin_from'              => $user['admin_email'],
		'admin_name'              => $user['admin_name'],
		'cc_submit'               => '',
		'cc_approval'             => '',
		'cc_denied'               => '',
		'cc_scheduled'            => '',
		'send_mail'               => 'wp_mail',
		'mail_format'             => 'text',
		'send_mail_admin'         => 1,
		'send_mail_user'          => 1,
		'send_approval_admin'     => 1,
		'send_approval_user'      => 1,
		'send_denied_admin'       => 1,
		'send_denied_user'        => 1, 
		'send_scheduled_admin'    => 1,
		'send_scheduled_user'     => 1,
		'alert_subject_admin'     => esc_html__('New User Submitted Post!', 'usp-pro'),
		'post_alert_admin'        => esc_html__('New user-submitted post at ', 'usp-pro') . $user['admin_name'] . esc_html__('! URL: ', 'usp-pro') . $user['admin_url'],
		'alert_subject_user'      => esc_html__('Thank you for your submitted post!', 'usp-pro'),
		'post_alert_user'         => esc_html__('Thank you for your submission at ', 'usp-pro') . $user['admin_name'] . esc_html__('! If submissions require approval, you\'ll receive an email once it\'s approved.', 'usp-pro'),
		'approval_subject_admin'  => esc_html__('Submitted Post Approved!', 'usp-pro'),
		'approval_message_admin'  => esc_html__('Congratulations, a submitted post has been approved at '. $user['admin_name'] .'!', 'usp-pro'),
		'approval_subject'        => esc_html__('Submitted Post Approved!', 'usp-pro'),
		'approval_message'        => esc_html__('Congratulations, your submitted post has been approved at '. $user['admin_name'] .'!', 'usp-pro'),
		'denied_subject_admin'    => esc_html__('Submitted Post Denied', 'usp-pro'),
		'denied_message_admin'    => esc_html__('A submitted post has been denied at '. $user['admin_name'], 'usp-pro'),
		'denied_subject'          => esc_html__('Submitted Post Denied', 'usp-pro'),
		'denied_message'          => esc_html__('Sorry, but your submission has been denied.', 'usp-pro'),
		'scheduled_subject_admin' => esc_html__('Submitted Post Scheduled', 'usp-pro'),
		'scheduled_message_admin' => esc_html__('A submitted post has been scheduled for publishing at '. $user['admin_name'] .'.', 'usp-pro'),
		'scheduled_subject'       => esc_html__('Submitted Post Scheduled', 'usp-pro'),
		'scheduled_message'       => esc_html__('Your submitted post has been scheduled for publishing at '. $user['admin_name'] .'.', 'usp-pro'),
		'contact_sub_prefix'      => esc_html__('Message sent from ', 'usp-pro') . $user['admin_name'] . ': ',
		'contact_subject'         => esc_html__('Email Subject', 'usp-pro'),
		'contact_cc'              => $user['admin_email'],
		'contact_cc_user'         => 0,
		'contact_cc_note'         => esc_html__('A copy of this message will be sent to the specified email address.', 'usp-pro'),
		'contact_stats'           => 0,
		'contact_from'            => $user['admin_email'],
		'contact_custom'          => 1,
		'custom_content'          => '',
		'custom_contact_1'        => '',
		'custom_contact_2'        => '',
		'custom_contact_3'        => '',
		'custom_contact_4'        => '',
		'custom_contact_5'        => '',
		
	);
	
}


function usp_default_options_advanced() {
	
	return array(
		
		'enable_autop'       => 0,
		'submit_button'      => 1,
		'disable_ip'         => 0,
		'submit_text'        => esc_html__('Submit Post', 'usp-pro'),
		'html_content'       => '',
		'fieldsets'          => 1,
		'form_demos'         => 1,
		'post_demos'         => 1,
		'post_type'          => 'post',
		'post_type_role'     => array('administrator'), 
		'form_type_role'     => array('administrator'), 
		'other_type'         => '',
		'post_type_slug'     => 'usp_post',
		'form_atts'          => 'data-parsley-validate data-persist="garlic"',
		'redirect_success'   => '',
		'redirect_failure'   => '',
		'blacklist_terms'    => '',
		'default_title'      => 'Post Title',
		'default_content'    => 'Post Content',
		
		'custom_before'      => '<div class="usp-pro-form">',
		'custom_after'       => '</div>',
		'success_post'       => esc_html__('Success! You have successfully submitted a post.', 'usp-pro'),
		'success_reg'        => esc_html__('Congratulations, you have been registered with the site.', 'usp-pro'),
		'success_both'       => esc_html__('Registration successful! Post Submission successful! You&rsquo;re golden.', 'usp-pro'),
		'success_contact'    => esc_html__('Email sent! We&rsquo;ll get back to you as soon as possible.', 'usp-pro'),
		'success_email_reg'  => esc_html__('Registration successful! Email sent! We&rsquo;ll get back to you as soon as possible.', 'usp-pro'),
		'success_email_post' => esc_html__('Post Submitted! Email sent! We&rsquo;ll get back to you as soon as possible.', 'usp-pro'),
		'success_email_both' => esc_html__('Post Submitted! Registration successful! Email sent! We&rsquo;ll get back to you as soon as possible.', 'usp-pro'),
		'success_before'     => '<div class="usp-success">',
		'success_after'      => '</div>',
		'success_form'       => 0,
		
		'custom_fields'      => 3,
		'custom_names'       => '', // no default for usp_label_c{n}
		'custom_prefix'      => 'prefix_',
		'custom_optional'    => '',
		'custom_required'    => '',
		'custom_field_names' => '', // no default for usp_custom_field_{n}
		
		'usp_error_1'        => esc_html__('Your Name', 'usp-pro'),
		'usp_error_2' 	     => esc_html__('Post URL', 'usp-pro'),
		'usp_error_3' 	     => esc_html__('Post Title', 'usp-pro'),
		'usp_error_4' 	     => esc_html__('Post Tags', 'usp-pro'),
		'usp_error_5' 	     => esc_html__('Challenge Question', 'usp-pro'),
		'usp_error_6' 	     => esc_html__('Post Category', 'usp-pro'),
		'usp_error_7' 	     => esc_html__('Post Content', 'usp-pro'),
		'usp_error_8' 	     => esc_html__('File(s)', 'usp-pro'),
		'usp_error_9' 	     => esc_html__('Email Address', 'usp-pro'),
		'usp_error_10'       => esc_html__('Email Subject', 'usp-pro'),
		'usp_error_11'       => esc_html__('Alt text', 'usp-pro'), 
		'usp_error_12'       => esc_html__('Caption', 'usp-pro'), 
		'usp_error_13'       => esc_html__('Description', 'usp-pro'), 
		'usp_error_14'       => esc_html__('Taxonomy', 'usp-pro'),
		'usp_error_15'       => esc_html__('Post Format', 'usp-pro'),
		'usp_error_16'       => esc_html__('Media Title', 'usp-pro'),
		'usp_error_17'       => esc_html__('File Name', 'usp-pro'),
		'usp_error_18'       => esc_html__('I agree to the terms', 'usp-pro'),
		'usp_error_19'       => esc_html__('Post Excerpt', 'usp-pro'),
		
		// not used
		'usp_error_a'        => esc_html__('User Nicename', 'usp-pro'),
		'usp_error_b'        => esc_html__('User Display Name', 'usp-pro'),
		'usp_error_c'        => esc_html__('User Nickname', 'usp-pro'),
		'usp_error_d'        => esc_html__('User First Name', 'usp-pro'),
		'usp_error_e'        => esc_html__('User Last Name', 'usp-pro'),
		'usp_error_f'        => esc_html__('User Description', 'usp-pro'),
		'usp_error_g'        => esc_html__('User Password', 'usp-pro'),
		
	);
	
}

function usp_default_options_more() {
	
	return array(
		
		'tax_before'          => '<div class="usp-error usp-error-taxonomy">' . esc_html__('Required field: ', 'usp-pro'),
		'tax_after'           => '</div>',
		'custom_field_before' => '<div class="usp-error usp-error-custom">' . esc_html__('Required field: ', 'usp-pro'),
		'custom_field_after'  => '</div>',
		'error_sep'           => '',
		'error_before'       => '<div class="usp-errors"><div class="usp-errors-heading"><strong>'. esc_html__('Important!', 'usp-pro') .'</strong> '. esc_html__('Please fix the following issues:', 'usp-pro') .'</div>',
		'error_after'        => '</div>',
		
		'usp_error_1_desc'    => '<div class="usp-error">' . esc_html__('Required field: Your Name', 'usp-pro') . '</div>',
		'usp_error_2_desc'    => '<div class="usp-error">' . esc_html__('Required field: Post URL', 'usp-pro') . '</div>',
		'usp_error_3_desc'    => '<div class="usp-error">' . esc_html__('Required field: Post Title', 'usp-pro') . '</div>',
		'usp_error_4_desc'    => '<div class="usp-error">' . esc_html__('Required field: Post Tags', 'usp-pro') . '</div>',
		'usp_error_5_desc'    => '<div class="usp-error">' . esc_html__('Required field: Challenge Question', 'usp-pro') . '</div>',
		'usp_error_6_desc'    => '<div class="usp-error">' . esc_html__('Required field: Post Category', 'usp-pro') . '</div>',
		'usp_error_7_desc'    => '<div class="usp-error">' . esc_html__('Required field: Post Content', 'usp-pro') . '</div>',
		'usp_error_8_desc'    => '<div class="usp-error">' . esc_html__('Required field: File(s)', 'usp-pro') . '</div>',
		'usp_error_9_desc'    => '<div class="usp-error">' . esc_html__('Required field: Email Address', 'usp-pro') . '</div>',
		'usp_error_10_desc'   => '<div class="usp-error">' . esc_html__('Required field: Email Subject', 'usp-pro') . '</div>',
		'usp_error_11_desc'   => '<div class="usp-error">' . esc_html__('Required field: Alt text', 'usp-pro') . '</div>', 
		'usp_error_12_desc'   => '<div class="usp-error">' . esc_html__('Required field: Caption', 'usp-pro') . '</div>', 
		'usp_error_13_desc'   => '<div class="usp-error">' . esc_html__('Required field: Description', 'usp-pro') . '</div>', 
		'usp_error_14_desc'   => '<div class="usp-error">' . esc_html__('Required field: Taxonomy', 'usp-pro') . '</div>',
		'usp_error_15_desc'   => '<div class="usp-error">' . esc_html__('Required field: Post Format', 'usp-pro') . '</div>',
		'usp_error_16_desc'   => '<div class="usp-error">' . esc_html__('Required field: Media Title', 'usp-pro') . '</div>',
		'usp_error_17_desc'   => '<div class="usp-error">' . esc_html__('Required field: File Name', 'usp-pro') . '</div>',
		'usp_error_18_desc'   => '<div class="usp-error">' . esc_html__('Required field: Agree to Terms', 'usp-pro') . '</div>',
		'usp_error_19_desc'   => '<div class="usp-error">' . esc_html__('Required field: Post Excerpt', 'usp-pro') . '</div>',
		
		'usp_error_a_desc'    => '<div class="usp-error">' . esc_html__('Required field: User Nicename', 'usp-pro') . '</div>',
		'usp_error_b_desc'    => '<div class="usp-error">' . esc_html__('Required field: User Display Name', 'usp-pro') . '</div>',
		'usp_error_c_desc'    => '<div class="usp-error">' . esc_html__('Required field: User Nickname', 'usp-pro') . '</div>',
		'usp_error_d_desc'    => '<div class="usp-error">' . esc_html__('Required field: User First Name', 'usp-pro') . '</div>',
		'usp_error_e_desc'    => '<div class="usp-error">' . esc_html__('Required field: User Last Name', 'usp-pro') . '</div>',
		'usp_error_f_desc'    => '<div class="usp-error">' . esc_html__('Required field: User Description', 'usp-pro') . '</div>',
		'usp_error_g_desc'    => '<div class="usp-error">' . esc_html__('Required field: User Password', 'usp-pro') . '</div>',
		
		'error_username'      => '<div class="usp-error">' . esc_html__('Username already registered. If that is your username, please log in to submit posts. Otherwise, please choose a different username.', 'usp-pro') . '</div>',
		'error_email'         => '<div class="usp-error">' . esc_html__('Email already registered. If that is your address, please log in to submit content. Otherwise, please choose a different email address.', 'usp-pro') . '</div>',
		'error_register'      => '<div class="usp-error">' . esc_html__('User-registration is currently disabled. Please contact the admin if you need help.', 'usp-pro') . '</div>',
		'user_exists'         => '<div class="usp-error">' . esc_html__('You are already registered with this site. Please contact the admin if you need help.', 'usp-pro') . '</div>',
		'post_required'       => '<div class="usp-error">' . esc_html__('Post-submission required. Please try again or contact the admin if you need help.', 'usp-pro') . '</div>',
		'post_duplicate'      => '<div class="usp-error">' . esc_html__('Duplicate post detected. Please enter a unique post title and unique post content.', 'usp-pro') . '</div>',
		'form_allowed'        => '<div class="usp-error">' . esc_html__('Incorrect form type. Please notify the administrator.', 'usp-pro') . '</div>',
		
		'name_restrict'       => '<div class="usp-error">' . esc_html__('Restricted characters found in Name field. Please try again.', 'usp-pro') . '</div>',
		'spam_response'       => '<div class="usp-error">' . esc_html__('Incorrect response for the spam check. Please try again.', 'usp-pro') . '</div>',
		'content_min'         => '<div class="usp-error">' . esc_html__('Minimum number of characters not met in content field. Please try again.', 'usp-pro') . '</div>',
		'content_max'         => '<div class="usp-error">' . esc_html__('Number of characters in content field exceeds maximum allowed. Please try again.', 'usp-pro') . '</div>',
		'excerpt_min'         => '<div class="usp-error">' . esc_html__('Minimum number of characters not met in excerpt field. Please try again.', 'usp-pro') . '</div>',
		'excerpt_max'         => '<div class="usp-error">' . esc_html__('Number of characters in excerpt field exceeds maximum allowed. Please try again.', 'usp-pro') . '</div>',
		'email_restrict'      => '<div class="usp-error">' . esc_html__('Please enter a valid email address.', 'usp-pro') . '</div>',
		'subject_restrict'    => '<div class="usp-error">' . esc_html__('Restricted characters found in Subject field. Please try again.', 'usp-pro') . '</div>',
		'content_filter'      => '<div class="usp-error">' . esc_html__('Restricted terms found in Content field. Please try again.', 'usp-pro') . '</div>',
		
		'files_required'      => '<div class="usp-error">' . esc_html__('File(s) required. Please check any required file(s) and try again.', 'usp-pro') . '</div>',
		'file_type_not'       => '<div class="usp-error">' . esc_html__('File type not allowed. Please check the allowed file types and try again.', 'usp-pro') . '</div>',
		'file_dimensions'     => '<div class="usp-error">' . esc_html__('Image dimensions (width/height) exceed set limits. Please check the requirements and try again.', 'usp-pro') . '</div>',
		'file_max_size'       => '<div class="usp-error">' . esc_html__('Maximum file-size limit exceeded. Please check the file requirements and try again.', 'usp-pro') . '</div>',
		'file_min_size'       => '<div class="usp-error">' . esc_html__('Minimum file-size not met. Please check the file requirements and try again.', 'usp-pro') . '</div>',
		'file_required'       => '<div class="usp-error">' . esc_html__('File(s) required. Please check any required file(s) and try again.', 'usp-pro') . '</div>',
		'file_name'           => '<div class="usp-error">' . esc_html__('Length of filename exceeds allowed limit. Please check the requirements and try again.', 'usp-pro') . '</div>',
		'min_req_files'       => '<div class="usp-error">' . esc_html__('Please ensure that you have met the minimum number of required files, and that any specific requirements have been met (e.g., size, dimensions).', 'usp-pro') . '</div>',
		'max_req_files'       => '<div class="usp-error">' . esc_html__('Please ensure that you have not exceeded the maximum number of files, and that any specific requirements have been met (e.g., size, dimensions).', 'usp-pro') . '</div>',
		'file_square'         => '<div class="usp-error">' . esc_html__('A square image is required. Please check the requirements and try again.', 'usp-pro') . '</div>',
		
	);
	
}

function usp_default_options_general() {
	
	$user = USP_Pro::get_user_infos();
	
	if (empty($user)) return;
	
	return array(
		
		'number_approved'    => -1,
		'custom_status'      => 'Custom',
		'categories'         => array(get_option('default_category')),
		'hidden_cats'        => 0,
		'cats_menu'          => 'dropdown',
		'cats_multiple'      => 0,
		'cats_nested'        => 1,
		'tags'               => array(),
		'hidden_tags'        => 0,
		'tags_order'         => 'name_asc',
		'tags_number'        => '-1',
		'tags_empty'         => 0,
		'tags_menu'          => 'dropdown',
		'tags_multiple'      => 0,
		'redirect_post'      => 0,
		'enable_stats'       => 0,
		'character_max'      => 0,
		'character_min'      => 0,
		'titles_unique'      => 1,
		'content_unique'     => 1,
		'sessions_on'        => 1,
		'sessions_scope'     => 0,
		'sessions_default'   => 1,
		'captcha_question'   => '1 + 1 =',
		'captcha_response'   => '2',
		'captcha_casing'     => 0,
		'recaptcha_public'   => '',
		'recaptcha_private'  => '',
		'recaptcha_version'  => 'v1',
		'assign_role'        => 'subscriber',
		'assign_author'      => $user['admin_id'],
		'use_author'         => 0,
		'replace_author'     => 0,
		'use_cat'            => 0,
		'use_cat_id'         => '',
		'submit_form_ids'    => 'classic, preview, submit, starter',
		'register_form_ids'  => 'register',
		'contact_form'       => 'contact',
		'enable_form_lock'   => 0,
		
	);
	
}

function usp_default_options_uploads() {
	
	return array(
		
		'post_images'      => 'before',
		'min_files'        => 0,
		'max_files'        => 3,
		'max_height'       => 1500,
		'min_height'       => 0,
		'max_width'        => 1500,
		'min_width'        => 0,
		'max_size'         => 5242880, // bytes = 5 MB
		'min_size'         => 5, // = 5 bytes
		'files_allow'      => 'bmp, gif, ico, jpe, jpeg, jpg, png, tif, tiff',
		'featured_image'   => 1,
		'featured_key'     => '1',
		'unique_filename'  => 1,
		'user_shortcodes'  => 0,
		'display_size'     => 'thumbnail',
		'enable_media'     => false,
		'square_image'     => 0,
		
	);
	
}

function usp_default_options_tools() {
	
	return array(
		
		'default_options' => 0,
		
	);
	
}

function usp_default_options_style() {
	
	return array(
		
		'style_custom'    => '',
		'include_css'     => 0,
		'include_js'      => 1,
		'include_parsley' => 0,
		'script_custom'   => '',
		'include_url'     => '',
		'form_style'    => 'simple',
		
		'style_simple'  => '.usp-pro .usp-form { padding: 5px; }
.usp-pro .usp-fieldset, .usp-pro fieldset { border: 0; margin: 10px 0; padding: 0; }
.usp-pro .usp-label, .usp-pro .usp-input, .usp-pro .usp-textarea, .usp-pro textarea, .usp-pro .usp-select, .usp-pro .usp-input-files, .usp-pro .usp-checkbox, .usp-pro .usp-checkboxes label, .usp-pro .usp-radio label, .usp-pro .usp-preview, .usp-pro .usp-contact-cc { float: none; clear: both; display: block; width: 99%; box-sizing: border-box; }
.usp-pro .usp-checkbox .usp-input, .usp-pro .usp-checkboxes input[type="checkbox"], .usp-pro .usp-input-agree, .usp-pro .usp-remember, .usp-pro .usp-label-agree, .usp-pro .usp-label-remember { float: none; clear: none; display: inline-block; width: auto; box-sizing: border-box; vertical-align: middle; }
.usp-pro .usp-files, .usp-pro .usp-agree { margin: 5px 0; }
.usp-pro .usp-contact-cc, .usp-pro .usp-submit { margin: 10px 0; }
.usp-pro .usp-agree-toggle, .usp-pro .usp-add-another { cursor: pointer; }
.usp-pro .usp-agree-toggle:hover { text-decoration: underline; }
.usp-pro .usp-agree-terms { padding: 5px 0; font-size: 90%; }
.usp-pro .usp-preview { overflow: hidden; }
.usp-pro .usp-preview div { float: left; width: 150px; height: 75px; overflow: hidden; margin: 5px 10px 5px 0; }
.usp-pro .usp-preview div a { display: block; width: 100%; height: 100%; }
.usp-pro .usp-input-files { margin: 5px 0; line-height: 1; }
.usp-pro .usp-form-errors { margin: 0 0 20px 0; }
.usp-pro .usp-error { color: #cc6666; }
.usp-pro .usp-error-field { border-color: #cc6666; background-color: #fef9f9; }
.usp-pro .usp-error-file { border: 1px solid #cc6666; }
.usp-hidden { display: none; }
.wp-editor-wrap { width: 99%; }
.wp-editor-container { border: 1px solid #e5e5e5; }
.wp-editor-area.usp-error-field { border: 1px solid #cc6666; }',
		
		'style_min'     => '.usp-pro .usp-form { padding: 5px; }
.usp-pro .usp-fieldset, .usp-pro fieldset { border: 0; margin: 10px 0; padding: 0; }
.usp-pro .usp-label, .usp-pro .usp-input, .usp-pro .usp-textarea, .usp-pro textarea, .usp-pro .usp-select, .usp-pro .usp-input-files, .usp-pro .usp-checkbox, .usp-pro .usp-checkboxes label, .usp-pro .usp-radio label, .usp-pro .usp-preview, .usp-pro .usp-contact-cc { float: none; clear: both; display: block; width: 99%; box-sizing: border-box; font-size: 14px; }
.usp-pro .usp-checkbox .usp-input, .usp-pro .usp-checkboxes input[type="checkbox"], .usp-pro .usp-input-agree, .usp-pro .usp-remember, .usp-pro .usp-label-agree, .usp-pro .usp-label-remember { float: none; clear: none; display: inline-block; width: auto; box-sizing: border-box; vertical-align: middle; font-size: 14px; }
.usp-pro .usp-files, .usp-pro .usp-agree { margin: 5px 0; font-size: 14px; }
.usp-pro .usp-contact-cc, .usp-pro .usp-submit { margin: 10px 0; font-size: 14px; }
.usp-pro .usp-agree-toggle, .usp-pro .usp-add-another { cursor: pointer; font-size: 13px; }
.usp-pro .usp-agree-toggle:hover { text-decoration: underline; }
.usp-pro .usp-agree-terms { padding: 5px 0; font-size: 12px; }
.usp-pro .usp-preview { overflow: hidden; }
.usp-pro .usp-preview div { float: left; width: 150px; height: 75px; overflow: hidden; margin: 5px 10px 5px 0; }
.usp-pro .usp-preview div a { display: block; width: 100%; height: 100%; }
.usp-pro .usp-input-files { margin: 5px 0; line-height: 1; font-size: 13px; }
.usp-pro .usp-form-errors { margin: 0 0 20px 0; font-size: 14px; }
.usp-pro .usp-error { color: #cc6666; }
.usp-pro .usp-error-field { border-color: #cc6666; background-color: #fef9f9; }
.usp-pro .usp-error-file { border: 1px solid #cc6666; }
.usp-pro, .usp-pro ul, .usp-pro p, .usp-pro code { font-size: 14px; }
.usp-pro .usp-contact-cc { font-size: 13px; }
.usp-hidden { display: none; }
.wp-editor-wrap { width: 99%; }
.wp-editor-container { border: 1px solid #e5e5e5; }
.wp-editor-area.usp-error-field { border: 1px solid #cc6666; }',
		
		'style_small'   => '.usp-pro .usp-form { padding: 5px; }
.usp-pro .usp-fieldset, .usp-pro fieldset { border: 0; margin: 5px 0; padding: 0; }
.usp-pro .usp-label, .usp-pro .usp-input, .usp-pro .usp-textarea, .usp-pro textarea, .usp-pro .usp-select, .usp-pro .usp-input-files, .usp-pro .usp-checkbox, .usp-pro .usp-checkboxes label, .usp-pro .usp-radio label, .usp-pro .usp-preview, .usp-pro .usp-contact-cc { float: none; clear: both; display: block; width: 70%; box-sizing: border-box; font-size: 12px; }
.usp-pro .usp-checkbox .usp-input, .usp-pro .usp-checkboxes input[type="checkbox"], .usp-pro .usp-input-agree, .usp-pro .usp-remember, .usp-pro .usp-label-agree, .usp-pro .usp-label-remember { float: none; clear: none; display: inline-block; width: auto; box-sizing: border-box; vertical-align: middle; font-size: 12px; }
.usp-pro .usp-files, .usp-pro .usp-agree { margin: 5px 0; font-size: 12px; }
.usp-pro .usp-contact-cc, .usp-pro .usp-submit { margin: 10px 0; font-size: 12px; }
.usp-pro .usp-agree-toggle, .usp-pro .usp-add-another { cursor: pointer; font-size: 11px; }
.usp-pro .usp-agree-toggle:hover { text-decoration: underline; }
.usp-pro .usp-agree-terms { padding: 5px 0; font-size: 10px; }
.usp-pro .usp-preview { overflow: hidden; }
.usp-pro .usp-preview div { float: left; width: 100px; height: 50px; overflow: hidden; margin: 5px 5px 0 0; }
.usp-pro .usp-preview div a { display: block; width: 100%; height: 100%; }
.usp-pro .usp-input-files { margin: 3px 0; line-height: 1; font-size: 12px; }
.usp-pro .usp-form-errors { margin: 10px 0; font-size: 12px; }
.usp-pro .usp-error { color: #cc6666; }
.usp-pro .usp-error-field { border-color: #cc6666; background-color: #fef9f9; }
.usp-pro .usp-error-file { border: 1px solid #cc6666; }
.usp-pro, .usp-pro ul, .usp-pro p, .usp-pro code { font-size: 12px; }
.usp-pro .usp-contact-cc { font-size: 11px; }
.usp-hidden { display: none; }
.wp-editor-wrap { width: 99%; }
.wp-editor-container { border: 1px solid #e5e5e5; }
.wp-editor-area.usp-error-field { border: 1px solid #cc6666; }',
		
		'style_large'   => '.usp-pro .usp-form { padding: 5px; }
.usp-pro .usp-fieldset, .usp-pro fieldset { border: 0; margin: 20px 0; padding: 0; }
.usp-pro .usp-label, .usp-pro .usp-input, .usp-pro .usp-textarea, .usp-pro textarea, .usp-pro .usp-select, .usp-pro .usp-input-files, .usp-pro .usp-checkbox, .usp-pro .usp-checkboxes label, .usp-pro .usp-radio label, .usp-pro .usp-preview, .usp-pro .usp-contact-cc { float: none; clear: both; display: block; width: 99%; box-sizing: border-box; font-size: 16px; }
.usp-pro .usp-checkbox .usp-input, .usp-pro .usp-checkboxes input[type="checkbox"], .usp-pro .usp-input-agree, .usp-pro .usp-remember, .usp-pro .usp-label-agree, .usp-pro .usp-label-remember { float: none; clear: none; display: inline-block; width: auto; box-sizing: border-box; vertical-align: middle; font-size: 16px; }
.usp-pro .usp-contact-cc { margin: 20px 0; font-size: 16px; }
.usp-pro .usp-submit { margin: 10px 0; font-size: 16px; }
.usp-pro .usp-agree-toggle, .usp-pro .usp-add-another { margin: 5px 0 0 0; cursor: pointer; font-size: 14px; }
.usp-pro .usp-agree-toggle:hover { text-decoration: underline; }
.usp-pro .usp-agree-terms { padding: 10px 0; font-size: 13px; }
.usp-pro .usp-preview { overflow: hidden; }
.usp-pro .usp-preview div { float: left; width: 200px; height: 100px; overflow: hidden; margin: 10px 10px 0 0; }
.usp-pro .usp-preview div a { display: block; width: 100%; height: 100%; }
.usp-pro .usp-input-files { margin: 5px 0; line-height: 1; font-size: 14px; }
.usp-pro .usp-form-errors { margin: 20px 0; font-size: 16px; }
.usp-pro .usp-error { color: #cc6666; }
.usp-pro .usp-error-field { border-color: #cc6666; background-color: #fef9f9; }
.usp-pro .usp-error-file { border: 1px solid #cc6666; }
.usp-pro, .usp-pro ul, .usp-pro p, .usp-pro code { font-size: 16px; }
.usp-pro .usp-contact-cc { font-size: 14px; }
.usp-hidden { display: none; }
.wp-editor-wrap { width: 99%; }
.wp-editor-container { border: 1px solid #e5e5e5; }
.wp-editor-area.usp-error-field { border: 1px solid #cc6666; }',
	
	);
	
}
