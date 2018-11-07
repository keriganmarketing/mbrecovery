<?php // USP Pro - Settings Callbacks

if (!defined('ABSPATH')) die();

function usp_callback_input_text_label($id) {
	
	$label = esc_html__('Undefined', 'usp-pro');
	
	if     ($id == 'submit_text')             $label = esc_html__('Text for submit button when &ldquo;Auto-Include&rdquo; setting is enabled', 'usp-pro');
	elseif ($id == 'html_content')            $label = esc_html__('HTML tags that should be allowed in submitted post content and/or post excerpt.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-enable-post-formatting/">'. esc_html__('Learn more &raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'usp_error_1')             $label = esc_html__('Name for the &ldquo;Your Name&rdquo; field.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-shortcodes/#usp-name">'. esc_html__('Learn more &raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'usp_error_2')             $label = esc_html__('Name for the &ldquo;Post URL&rdquo; field.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-shortcodes/#usp-url">'. esc_html__('Learn more &raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'usp_error_3')             $label = esc_html__('Name for the &ldquo;Post Title&rdquo; field.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-shortcodes/#usp-title">'. esc_html__('Learn more &raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'usp_error_4')             $label = esc_html__('Name for the &ldquo;Post Tags&rdquo; field.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-shortcodes/#usp-tags">'. esc_html__('Learn more &raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'usp_error_5')             $label = esc_html__('Name for the &ldquo;Challenge Question&rdquo; and &ldquo;reCAPTCHA&rdquo; fields.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-shortcodes/#usp-captcha">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'usp_error_6')             $label = esc_html__('Name for the &ldquo;Post Category&rdquo; field.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-shortcodes/#usp-category">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'usp_error_7')             $label = esc_html__('Name for the &ldquo;Post Content&rdquo; field.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-shortcodes/#usp-content">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'usp_error_8')             $label = esc_html__('Name for the &ldquo;File(s)&rdquo; field.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-shortcodes/#usp-files">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'usp_error_9')             $label = esc_html__('Name for the &ldquo;Email Address&rdquo; field.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-shortcodes/#usp-email">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'usp_error_10')            $label = esc_html__('Name for the &ldquo;Email Subject&rdquo; field.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-shortcodes/#usp-subject">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'usp_error_11')            $label = esc_html__('Name for the &ldquo;Alt Text&rdquo; field.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-custom-metadata-for-submitted-files/">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'usp_error_12')            $label = esc_html__('Name for the &ldquo;Caption&rdquo; field.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-custom-metadata-for-submitted-files/">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'usp_error_13')            $label = esc_html__('Name for the &ldquo;Description&rdquo; field.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-custom-metadata-for-submitted-files/">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'usp_error_14')            $label = esc_html__('Name for the &ldquo;Taxonomy&rdquo; field.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-shortcodes/#custom-taxonomy">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'usp_error_15')            $label = esc_html__('Name for the &ldquo;Post Format&rdquo; field.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-shortcodes/#post-format">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'usp_error_16')            $label = esc_html__('Name for the &ldquo;Media Title&rdquo; field.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-custom-metadata-for-submitted-files/">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'usp_error_17')            $label = esc_html__('Name for the &ldquo;File Name&rdquo; field.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-custom-metadata-for-submitted-files/">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'usp_error_18')            $label = esc_html__('Name for the &ldquo;Agree to Terms&rdquo; field.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-shortcodes/#agree-to-terms">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'usp_error_19')            $label = esc_html__('Name for the &ldquo;Post Excerpt&rdquo; field.', 'usp-pro');
	elseif ($id == 'usp_error_a')             $label = esc_html__('Name for the &ldquo;User Nicename&rdquo; field', 'usp-pro');
	elseif ($id == 'usp_error_b')             $label = esc_html__('Name for the &ldquo;User Display Name&rdquo; field', 'usp-pro');
	elseif ($id == 'usp_error_c')             $label = esc_html__('Name for the &ldquo;User Nickname&rdquo; field', 'usp-pro');
	elseif ($id == 'usp_error_d')             $label = esc_html__('Name for the &ldquo;User First Name&rdquo; field', 'usp-pro');
	elseif ($id == 'usp_error_e')             $label = esc_html__('Name for the &ldquo;User Last Name&rdquo; field', 'usp-pro');
	elseif ($id == 'usp_error_f')             $label = esc_html__('Name for the &ldquo;User Description&rdquo; field', 'usp-pro');
	elseif ($id == 'usp_error_g')             $label = esc_html__('Name for the &ldquo;User Password&rdquo; field (deprecated)', 'usp-pro');
	elseif ($id == 'contact_sub_prefix')      $label = esc_html__('Custom text to prepend to the Subject line', 'usp-pro');
	elseif ($id == 'contact_subject')         $label = esc_html__('Default Subject line (when not using', 'usp-pro') .' <code>[usp_subject]</code> '. esc_html__('shortcode)', 'usp-pro');
	elseif ($id == 'contact_from')            $label = esc_html__('Default &ldquo;From&rdquo; address (when not using', 'usp-pro') .' <code>[usp_email]</code> '. esc_html__('shortcode)', 'usp-pro');
	elseif ($id == 'contact_cc')              $label = esc_html__('Email addresses that should be carbon copied (comma-separated)', 'usp-pro');
	elseif ($id == 'redirect_success')        $label = esc_html__('Where should visitors go after successful form submission? Enter any complete URL (e.g.,', 'usp-pro') .' <code>http://example.com</code>'. esc_html__(') or leave blank to redirect to the current page. Note that you can', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-custom-redirects/">'. esc_html__('override this setting on any form', 'usp-pro') .'</a>. '. esc_html__('Important: this option is for advanced users; recommended to leave blank. See', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-error-messages-custom-redirects/">'. esc_html__('this post', 'usp-pro') .'</a> '. esc_html__('for more info.', 'usp-pro');
	elseif ($id == 'redirect_failure')        $label = esc_html__('Where should visitors go after failed form submission? Enter any complete URL (e.g.,', 'usp-pro') .' <code>http://example.com</code>'. esc_html__(') or leave blank to redirect to the current page. Important: this option is for advanced users; recommended to leave blank. See', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-error-messages-custom-redirects/">'. esc_html__('this post', 'usp-pro') .'</a> '. esc_html__('for more info.', 'usp-pro');
	elseif ($id == 'captcha_question')        $label = esc_html__('This question is displayed when', 'usp-pro') .' <code>[usp_captcha]</code> '. esc_html__('is added to any form', 'usp-pro');
	elseif ($id == 'captcha_response')        $label = esc_html__('Enter the <em>only</em> correct answer to the previous &ldquo;Challenge Question&rdquo;', 'usp-pro');
	elseif ($id == 'recaptcha_public')        $label = esc_html__('To use Google reCAPTCHA instead of the Challenge Question, enter your Public &amp; Private Keys', 'usp-pro');
	elseif ($id == 'recaptcha_private')       $label = esc_html__('To use Google reCAPTCHA instead of the Challenge Question, enter your Public &amp; Private Keys', 'usp-pro');
	elseif ($id == 'use_cat_id')              $label = esc_html__('Automatically include these category IDs for all submitted posts (comma-separated)', 'usp-pro');
	elseif ($id == 'admin_email')             $label = esc_html__('Messages from contact forms and email alerts will be sent to this address', 'usp-pro');
	elseif ($id == 'admin_name')              $label = esc_html__('Email alerts will be addressed to this name', 'usp-pro');
	elseif ($id == 'admin_from')              $label = esc_html__('Email alerts will use this address as the &ldquo;From&rdquo; header', 'usp-pro');
	elseif ($id == 'alert_subject_admin')     $label = esc_html__('Subject line for submission alerts sent to the admin', 'usp-pro');
	elseif ($id == 'approval_subject_admin')  $label = esc_html__('Subject line for approval alerts sent to the admin', 'usp-pro');
	elseif ($id == 'denied_subject_admin')    $label = esc_html__('Subject line for denied alerts sent to the admin', 'usp-pro');
	elseif ($id == 'alert_subject_user')      $label = esc_html__('Subject line for submission alerts sent to the user', 'usp-pro');
	elseif ($id == 'approval_subject')        $label = esc_html__('Subject line for approval alerts sent to the user', 'usp-pro');
	elseif ($id == 'denied_subject')          $label = esc_html__('Subject line for denied alerts sent to the user', 'usp-pro');
	elseif ($id == 'scheduled_subject_admin') $label = esc_html__('Subject line for scheduled alerts sent to the admin', 'usp-pro');
	elseif ($id == 'scheduled_subject')       $label = esc_html__('Subject line for scheduled alerts sent to the user', 'usp-pro');
	elseif ($id == 'cc_submit')               $label = esc_html__('Additional addresses for submission alerts (comma-separated)', 'usp-pro');
	elseif ($id == 'cc_approval')             $label = esc_html__('Additional addresses for approval alerts (comma-separated)', 'usp-pro');
	elseif ($id == 'cc_denied')               $label = esc_html__('Additional addresses for denied alerts (comma-separated)', 'usp-pro');
	elseif ($id == 'cc_scheduled')            $label = esc_html__('Additional addresses for scheduled alerts (comma-separated)', 'usp-pro');
	elseif ($id == 'character_min')           $label = esc_html__('Minimum number of characters required for post content and excerpt fields (0 = no minimum)', 'usp-pro');
	elseif ($id == 'character_max')           $label = esc_html__('Maximum number of characters allowed for post content and excerpt fields (0 = no maximum)', 'usp-pro');
	elseif ($id == 'post_type_slug')          $label = esc_html__('Slug to use when &ldquo;USP Posts&rdquo; is selected for the setting, &ldquo;Submitted Post Type&rdquo;. Note: this setting is for advanced users. Recommended to use the default value,', 'usp-pro') .' <code>usp_post</code>.';
	elseif ($id == 'other_type')              $label = esc_html__('Slug to use when &ldquo;Existing Post Type&rdquo; is selected for the setting, &ldquo;Submitted Post Type&rdquo;. Note: the Custom Post Type specified here must be provided by your theme.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-unlimited-custom-post-types/">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'tags_number')             $label = esc_html__('Number of tags that should be displayed for the &ldquo;Post Tags&rdquo; setting (use', 'usp-pro') .' <code>-1</code> '. esc_html__('to display all tags)', 'usp-pro');
	elseif ($id == 'min_size')                $label = esc_html__('Min size (in bytes) for uploaded files (applies to all file types). Default:', 'usp-pro') .' <code>25600</code> (25 KB)';
	elseif ($id == 'max_size')                $label = esc_html__('Max size (in bytes) for uploaded files (applies to all file types). Default:', 'usp-pro') .' <code>5242880</code> (5 MB)';
	elseif ($id == 'min_width')               $label = esc_html__('Minimum width (in pixels) for uploaded images', 'usp-pro');
	elseif ($id == 'max_width')               $label = esc_html__('Maximum width (in pixels) for uploaded images', 'usp-pro');
	elseif ($id == 'min_height')              $label = esc_html__('Minimum height (in pixels) for uploaded images', 'usp-pro');
	elseif ($id == 'max_height')              $label = esc_html__('Maximum height (in pixels) for uploaded images', 'usp-pro');
	elseif ($id == 'files_allow')             $label = esc_html__('Allowed file types (comma-separated) for any USP Form.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-allowed-file-types/">Learn more&nbsp;&raquo;</a>';
	elseif ($id == 'contact_cc_note')         $label = esc_html__('Message displayed on the contact form (when the setting &ldquo;CC User&rdquo; is enabled)', 'usp-pro');
	elseif ($id == 'featured_key')            $label = esc_html__('Image to use as the Featured Image (when &ldquo;Featured Images&rdquo; setting is enabled).', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-featured-image-key/">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'include_url')             $label = esc_html__('Comma-separated list of URLs (leave blank to load CSS/JS on all pages)', 'usp-pro');
	elseif ($id == 'custom_status')           $label = esc_html__('Applies when &ldquo;Default Post Status&rdquo; is set to &ldquo;Always moderate via Custom Status&rdquo;', 'usp-pro');
	elseif ($id == 'custom_contact_1')        $label = esc_html__('Email address for Custom Recipient 1', 'usp-pro');
	elseif ($id == 'custom_contact_2')        $label = esc_html__('Email address for Custom Recipient 2', 'usp-pro');
	elseif ($id == 'custom_contact_3')        $label = esc_html__('Email address for Custom Recipient 3', 'usp-pro');
	elseif ($id == 'custom_contact_4')        $label = esc_html__('Email address for Custom Recipient 4', 'usp-pro');
	elseif ($id == 'custom_contact_5')        $label = esc_html__('Email address for Custom Recipient 5', 'usp-pro');
	elseif ($id == 'custom_prefix')           $label = esc_html__('Unique prefix for Custom Fields (leave blank to disable)', 'usp-pro');
	elseif ($id == 'custom_optional')         $label = esc_html__('Optional Custom Fields (leave blank to disable)', 'usp-pro');
	elseif ($id == 'custom_required')         $label = esc_html__('Required Custom Fields (leave blank to disable)', 'usp-pro');
	elseif ($id == 'default_title')           $label = esc_html__('Default Post Title for submitted posts', 'usp-pro');
	elseif ($id == 'form_atts')               $label = esc_html__('Custom attributes that should be included in the', 'usp-pro') .' <code>&lt;form&gt;</code> '. esc_html__('tag', 'usp-pro');
	elseif ($id == 'submit_form_ids')         $label = esc_html__('Form IDs of any post-submission forms', 'usp-pro');
	elseif ($id == 'register_form_ids')       $label = esc_html__('Form IDs of any user-registration forms', 'usp-pro');
	elseif ($id == 'contact_form')            $label = esc_html__('Form IDs of any contact forms', 'usp-pro');
	
	return  $label;
}

function usp_callback_textarea_label($id) {
	
	$label = esc_html__('Undefined', 'usp-pro');
	
	if     ($id == 'custom_before')           $label = esc_html__('Text/markup to be included before all USP Forms', 'usp-pro');
	elseif ($id == 'custom_after')            $label = esc_html__('Text/markup to be included after all USP Forms', 'usp-pro');
	elseif ($id == 'post_alert_admin')        $label = esc_html__('Message for submission alerts sent to the admin', 'usp-pro');
	elseif ($id == 'post_alert_user')         $label = esc_html__('Message for submission alerts sent to the user', 'usp-pro');
	elseif ($id == 'approval_message_admin')  $label = esc_html__('Message for approval alerts sent to the admin', 'usp-pro');
	elseif ($id == 'approval_message')        $label = esc_html__('Message for approval alerts sent to the user', 'usp-pro');
	elseif ($id == 'denied_message_admin')    $label = esc_html__('Message for denied alerts sent to the admin', 'usp-pro');
	elseif ($id == 'denied_message')          $label = esc_html__('Message for denied alerts sent to the user', 'usp-pro');
	elseif ($id == 'scheduled_message_admin') $label = esc_html__('Message for scheduled alerts sent to the admin', 'usp-pro');
	elseif ($id == 'scheduled_message')       $label = esc_html__('Message for scheduled alerts sent to the user', 'usp-pro');
	elseif ($id == 'custom_content')          $label = esc_html__('Custom content that should be appended to messages sent via contact form', 'usp-pro');
	elseif ($id == 'success_reg')             $label = esc_html__('Message displayed when a user is registered successfully', 'usp-pro');
	elseif ($id == 'success_post')            $label = esc_html__('Message displayed when a post is submitted successfully', 'usp-pro');
	elseif ($id == 'success_both')            $label = esc_html__('Message displayed when user is registered and post is submitted', 'usp-pro');
	elseif ($id == 'success_contact')         $label = esc_html__('Message displayed when email is sent via contact form', 'usp-pro');
	elseif ($id == 'success_email_reg')       $label = esc_html__('Message displayed when email is sent and user is registered', 'usp-pro');
	elseif ($id == 'success_email_post')      $label = esc_html__('Message displayed when email is sent and post is submitted', 'usp-pro');
	elseif ($id == 'success_email_both')      $label = esc_html__('Message displayed when email is sent, user is registered, and post is submitted', 'usp-pro');
	elseif ($id == 'error_before')            $label = esc_html__('Custom text/markup to appear before the listed errors', 'usp-pro');
	elseif ($id == 'error_after')             $label = esc_html__('Custom text/markup to appear after the listed errors', 'usp-pro');
	elseif ($id == 'success_before')          $label = esc_html__('Custom text/markup to appear before the success message', 'usp-pro');
	elseif ($id == 'success_after')           $label = esc_html__('Custom text/markup to appear after the success message', 'usp-pro');
	elseif ($id == 'style_simple')            $label = esc_html__('CSS for Simple form style (edit as needed to fit your theme)', 'usp-pro');
	elseif ($id == 'style_min')               $label = esc_html__('CSS for Minimal form style (edit as needed to fit your theme)', 'usp-pro');
	elseif ($id == 'style_small')             $label = esc_html__('CSS for Small form style (edit as needed to fit your theme)', 'usp-pro');
	elseif ($id == 'style_large')             $label = esc_html__('CSS for Large form style (edit as needed to fit your theme)', 'usp-pro');
	elseif ($id == 'style_custom')            $label = esc_html__('CSS for Custom form style (edit as needed to fit your theme)', 'usp-pro');
	elseif ($id == 'script_custom')           $label = esc_html__('Custom JavaScript, included inline via', 'usp-pro') .' <code>&lt;script&gt;</code> '. esc_html__('tag', 'usp-pro');
	elseif ($id == 'default_content')         $label = esc_html__('Default Post Content for submitted posts (basic HTML allowed)', 'usp-pro');
	elseif ($id == 'blacklist_terms')         $label = esc_html__('Words that are not allowed in any submitted post content and/or post excerpt (', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="http://delim.co/">'. esc_html__('put each word on its own line', 'usp-pro') .'</a> )';
	
	elseif ($id == 'usp_error_1_desc')        $label = esc_html__('Name errors &ndash; when using', 'usp-pro') .' <code>[usp_name]</code> '. esc_html__('shortcode', 'usp-pro');
	elseif ($id == 'usp_error_2_desc')        $label = esc_html__('URL errors &ndash; when using', 'usp-pro') .' <code>[usp_url]</code> '. esc_html__('shortcode', 'usp-pro');
	elseif ($id == 'usp_error_3_desc')        $label = esc_html__('Title errors &ndash; when using', 'usp-pro') .' <code>[usp_title]</code> '. esc_html__('shortcode', 'usp-pro');
	elseif ($id == 'usp_error_4_desc')        $label = esc_html__('Tag errors &ndash; when using', 'usp-pro') .' <code>[usp_tags]</code> '. esc_html__('shortcode', 'usp-pro');
	elseif ($id == 'usp_error_5_desc')        $label = esc_html__('Captcha errors &ndash; when using', 'usp-pro') .' <code>[usp_captcha]</code> '. esc_html__('shortcode', 'usp-pro');
	elseif ($id == 'usp_error_6_desc')        $label = esc_html__('Category errors &ndash; when using', 'usp-pro') .' <code>[usp_category]</code> '. esc_html__('shortcode', 'usp-pro');
	elseif ($id == 'usp_error_7_desc')        $label = esc_html__('Content errors &ndash; when using', 'usp-pro') .' <code>[usp_content]</code> '. esc_html__('shortcode', 'usp-pro');
	elseif ($id == 'usp_error_8_desc')        $label = esc_html__('Files errors &ndash; when using', 'usp-pro') .' <code>[usp_files]</code> '. esc_html__('shortcode', 'usp-pro');
	elseif ($id == 'usp_error_9_desc')        $label = esc_html__('Email errors &ndash; when using', 'usp-pro') .' <code>[usp_email]</code> '. esc_html__('shortcode', 'usp-pro');
	elseif ($id == 'usp_error_10_desc')       $label = esc_html__('Subject errors &ndash; when using', 'usp-pro') .' <code>[usp_subject]</code> '. esc_html__('shortcode', 'usp-pro');
	elseif ($id == 'usp_error_11_desc')       $label = esc_html__('Alt Text errors &ndash; when using', 'usp-pro') .' <code>[usp_custom_field]</code> '. esc_html__('with', 'usp-pro') .' <code>name#alt-{id}</code>';
	elseif ($id == 'usp_error_12_desc')       $label = esc_html__('Caption errors &ndash; when using', 'usp-pro') .' <code>[usp_custom_field]</code> '. esc_html__('with', 'usp-pro') .' <code>name#caption-{id}</code>';
	elseif ($id == 'usp_error_13_desc')       $label = esc_html__('Description errors &ndash; when using', 'usp-pro') .' <code>[usp_custom_field]</code> '. esc_html__('with', 'usp-pro') .' <code>name#desc-{id}</code>';
	elseif ($id == 'usp_error_14_desc')       $label = esc_html__('Taxonomy errors &ndash; when using', 'usp-pro') .' <code>[usp_taxonomy]</code> '. esc_html__('shortcode', 'usp-pro');
	elseif ($id == 'usp_error_15_desc')       $label = esc_html__('Post Format errors &ndash; when using', 'usp-pro') .' <code>[usp_custom_field]</code>. <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-shortcodes/#post-format">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'usp_error_16_desc')       $label = esc_html__('Media Title errors &ndash; when using', 'usp-pro') .' <code>[usp_custom_field]</code> '. esc_html__('with', 'usp-pro') .' <code>name#mediatitle-{id}</code>';
	elseif ($id == 'usp_error_17_desc')       $label = esc_html__('File Name errors &ndash; when using', 'usp-pro') .' <code>[usp_custom_field]</code> '. esc_html__('with', 'usp-pro') .' <code>name#filename-{id}</code>';
	elseif ($id == 'usp_error_18_desc')       $label = esc_html__('"Agree to Terms" errors &ndash; when using', 'usp-pro') .' <code>[usp_agree]</code> '. esc_html__('shortcode', 'usp-pro');
	elseif ($id == 'usp_error_19_desc')       $label = esc_html__('Post Excerpt errors &ndash; when using', 'usp-pro') .' <code>[usp_excerpt]</code> '. esc_html__('shortcode', 'usp-pro');
	
	elseif ($id == 'usp_error_a_desc')        $label = esc_html__('Errors for the &ldquo;User Nicename&rdquo; field', 'usp-pro');
	elseif ($id == 'usp_error_b_desc')        $label = esc_html__('Errors for the &ldquo;User Display Name&rdquo; field', 'usp-pro');
	elseif ($id == 'usp_error_c_desc')        $label = esc_html__('Errors for the &ldquo;User Nickname&rdquo; field', 'usp-pro');
	elseif ($id == 'usp_error_d_desc')        $label = esc_html__('Errors for the &ldquo;User First Name&rdquo; field', 'usp-pro');
	elseif ($id == 'usp_error_e_desc')        $label = esc_html__('Errors for the &ldquo;User Last Name&rdquo; field', 'usp-pro');
	elseif ($id == 'usp_error_f_desc')        $label = esc_html__('Errors for the &ldquo;User Description&rdquo; field', 'usp-pro');
	elseif ($id == 'usp_error_g_desc')        $label = esc_html__('Errors for the &ldquo;User Password&rdquo; field (deprecated)', 'usp-pro');
	
	elseif ($id == 'error_username')          $label = esc_html__('User Name errors (when using a form that registers users)', 'usp-pro');
	elseif ($id == 'error_email')             $label = esc_html__('User Email errors (when using a form that registers users)', 'usp-pro');
	elseif ($id == 'error_register')          $label = esc_html__('Registration Disabled errors (when using a form that registers users)', 'usp-pro');
	elseif ($id == 'user_exists')             $label = esc_html__('User Exists errors (when using a form that registers users)', 'usp-pro');
	elseif ($id == 'post_required')           $label = esc_html__('Post Required errors (when using a form that submits posts)', 'usp-pro');
	elseif ($id == 'post_duplicate')          $label = esc_html__('Duplicate Post errors (when using a form that submits posts)', 'usp-pro');
	elseif ($id == 'name_restrict')           $label = esc_html__('Illegal characters in the Name field', 'usp-pro');
	elseif ($id == 'spam_response')           $label = esc_html__('Incorrect response for the anti-spam captcha/challenge question', 'usp-pro');
	elseif ($id == 'content_min')             $label = esc_html__('Minimum number of characters not met in Post Content field', 'usp-pro');
	elseif ($id == 'content_max')             $label = esc_html__('Maximum number of characters not met in Post Content field', 'usp-pro');
	elseif ($id == 'excerpt_min')             $label = esc_html__('Minimum number of characters not met in Post Excerpt field', 'usp-pro');
	elseif ($id == 'excerpt_max')             $label = esc_html__('Maximum number of characters not met in Post Excerpt field', 'usp-pro');
	elseif ($id == 'email_restrict')          $label = esc_html__('Email address is incorrect, incomplete, or contains restricted characters', 'usp-pro');
	elseif ($id == 'subject_restrict')        $label = esc_html__('Illegal characters in the Email Subject field', 'usp-pro');
	elseif ($id == 'form_allowed')            $label = esc_html__('Incorrect form type (when &ldquo;Extra Form Security&rdquo; is enabled in General settings)', 'usp-pro');
	elseif ($id == 'content_filter')          $label = esc_html__('Forbidden words in Post Content (when &ldquo;Content Filter&rdquo; is enabled in Advanced settings)', 'usp-pro');
	
	elseif ($id == 'files_required')          $label = esc_html__('Files required (for multiple-select files)', 'usp-pro');
	elseif ($id == 'file_required')           $label = esc_html__('File required (for single-select files)', 'usp-pro');
	elseif ($id == 'file_type_not')           $label = esc_html__('File type not allowed', 'usp-pro');
	elseif ($id == 'file_dimensions')         $label = esc_html__('File width and height exceed limits', 'usp-pro');
	elseif ($id == 'file_max_size')           $label = esc_html__('Maximum file size exceeded', 'usp-pro');
	elseif ($id == 'file_min_size')           $label = esc_html__('Minimum file size not met', 'usp-pro');
	elseif ($id == 'file_name')               $label = esc_html__('Length of file name exceeds limit', 'usp-pro');
	elseif ($id == 'min_req_files')           $label = esc_html__('Minimum number of files not met', 'usp-pro');
	elseif ($id == 'max_req_files')           $label = esc_html__('Maximum number of files exceeded', 'usp-pro');
	elseif ($id == 'file_square')             $label = esc_html__('Image is not square (width does not equal height)', 'usp-pro');
	
	elseif ($id == 'tax_before')              $label = esc_html__('Text/markup displayed before each Taxonomy error', 'usp-pro');
	elseif ($id == 'tax_after')               $label = esc_html__('Text/markup displayed after each Taxonomy error', 'usp-pro');
	elseif ($id == 'custom_field_before')     $label = esc_html__('Text/markup displayed before each Custom Field error', 'usp-pro');
	elseif ($id == 'custom_field_after')      $label = esc_html__('Text/markup displayed after each Custom Field error', 'usp-pro');
	elseif ($id == 'error_sep')               $label = esc_html__('Text/markup displayed between each error (e.g.,', 'usp-pro') .' <code>,</code> '. esc_html__('or', 'usp-pro') .' <code>&lt;span&gt;, &lt;/span&gt;</code>)';
	
	return $label;
}

function usp_callback_select_label($id) {
	
	$label = esc_html__('Undefined', 'usp-pro');
	
	if     ($id == 'min_files')          $label = esc_html__('This default value can be overridden via the', 'usp-pro') .' <code>files_min</code> '. esc_html__('shortcode attribute', 'usp-pro');
	elseif ($id == 'max_files')          $label = esc_html__('This default value can be overridden via the', 'usp-pro') .' <code>files_max</code> '. esc_html__('shortcode attribute', 'usp-pro');
	elseif ($id == 'display_size')       $label = esc_html__('Size of auto-displayed images', 'usp-pro');                        
	elseif ($id == 'mail_format')        $label = esc_html__('Format for all email (contact form and email alerts).', 'usp-pro') .'<br /><strong>'. esc_html__('Note:', 'usp-pro') .'</strong> '. esc_html__('to allow HTML in contact-form messages, the option &ldquo;Post Formatting&rdquo; must be enabled in Advanced settings.', 'usp-pro');
	elseif ($id == 'recaptcha_version')  $label = esc_html__('reCAPTCHA version to display via', 'usp-pro') .' <code>[usp_captcha]</code>';
	
	return $label;
}

function usp_callback_checkbox_label($id) {
	
	$label = esc_html__('Undefined', 'usp-pro');
	
	if     ($id == 'send_mail_user')       $label = esc_html__('Send post-submission alert to the user', 'usp-pro');
	elseif ($id == 'send_mail_admin')      $label = esc_html__('Send post-submission alert to the admin', 'usp-pro');
	elseif ($id == 'send_approval_user')   $label = esc_html__('Send post-approval alert to the user (published post)', 'usp-pro');
	elseif ($id == 'send_approval_admin')  $label = esc_html__('Send post-approval alert to the admin (published post)', 'usp-pro');
	elseif ($id == 'send_denied_user')     $label = esc_html__('Send post-denied alert to the user (trashed post)', 'usp-pro');
	elseif ($id == 'send_denied_admin')    $label = esc_html__('Send post-denied alert to the admin (trashed post)', 'usp-pro');
	elseif ($id == 'send_scheduled_admin') $label = esc_html__('Send alert to the admin when a submitted post is scheduled', 'usp-pro');
	elseif ($id == 'send_scheduled_user')  $label = esc_html__('Send alert to the user when a submitted post is scheduled', 'usp-pro');
	elseif ($id == 'contact_cc_user')      $label = esc_html__('Send a copy of the message to the sender (via CC)', 'usp-pro');
	elseif ($id == 'contact_stats')        $label = esc_html__('Append user data to messages (e.g., IP address, referrer, request, et al)', 'usp-pro');
	elseif ($id == 'contact_custom')       $label = esc_html__('Append any Custom Field data to messages.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-displaying-custom-fields/">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'include_js')           $label = esc_html__('Include external USP JavaScript file (required for multiple file uploads and thumbnail previews). Note: this file will be overwritten with each plugin upgrade, so if you need to customize or add any JavaScript, use the previous option or some other method.', 'usp-pro');
	elseif ($id == 'include_parsley')      $label = esc_html__('Include Parsley.js (required for front-end form validation). Note: this file will be overwritten with each plugin upgrade, so if you need to customize or add any JavaScript, use the previous option or some other method.', 'usp-pro');
	elseif ($id == 'include_css')          $label = esc_html__('Include external CSS file (optional). Includes Parsley.js v2.7.0. Note: this file will be overwritten with each plugin upgrade; so if you need to customize or add any CSS, use the previous option or some other method.', 'usp-pro');
	elseif ($id == 'success_form')         $label = esc_html__('Display the submission form with the success message', 'usp-pro');
	elseif ($id == 'enable_autop')         $label = esc_html__('Apply WP&rsquo;s auto-formatting to form content', 'usp-pro');
	elseif ($id == 'fieldsets')            $label = esc_html__('Automatically wrap form inputs with', 'usp-pro') .' <code>&lt;fieldset&gt;</code> '. esc_html__('tags', 'usp-pro');
	elseif ($id == 'form_demos')           $label = esc_html__('Automatically regenerate the USP Form Demos (for any form that is permanently deleted)', 'usp-pro');
	elseif ($id == 'post_demos')           $label = esc_html__('Automatically regenerate the USP Post Demos', 'usp-pro');
	elseif ($id == 'submit_button')        $label = esc_html__('Automatically include a submit button on all USP Forms', 'usp-pro');
	elseif ($id == 'use_author')           $label = esc_html__('Use the registered username as the Post Author.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-use-registered-author/">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'replace_author')       $label = esc_html__('Use submitted name as Post Author, and submitted URL as Author URL.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-replace-author-name-url/">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'redirect_post')        $label = esc_html__('Redirect users to their submitted post, applies when &ldquo;Default Post Status&rdquo; is set to &ldquo;Always publish immediately&rdquo;', 'usp-pro');
	elseif ($id == 'enable_stats')         $label = esc_html__('Attach user data (e.g., IP Address, Referrer, Request, et al) to submitted posts as Custom Fields', 'usp-pro');
	elseif ($id == 'captcha_casing')       $label = esc_html__('Make the &ldquo;Challenge Response&rdquo; case-sensitive', 'usp-pro');
	elseif ($id == 'cats_nested')          $label = esc_html__('Enable nested/hierarchical display of subcategories', 'usp-pro');
	elseif ($id == 'use_cat')              $label = esc_html__('Enable required categories for all forms (see next option)', 'usp-pro');
	elseif ($id == 'hidden_cats')          $label = esc_html__('Hide Category field when using', 'usp-pro') .' <code>[usp_category]</code>';
	elseif ($id == 'cats_multiple')        $label = esc_html__('Allow users to select multiple categories when using the dropdown menu', 'usp-pro');
	elseif ($id == 'tags_empty')           $label = esc_html__('Do not display empty tags for the &ldquo;Post Tags&rdquo; setting', 'usp-pro');
	elseif ($id == 'hidden_tags')          $label = esc_html__('Hide Tags field when using', 'usp-pro') .' <code>[usp_tags]</code>';
	elseif ($id == 'tags_multiple')        $label = esc_html__('Allow users to select multiple tags when using the dropdown menu', 'usp-pro');
	elseif ($id == 'sessions_on')          $label = esc_html__('Enable &ldquo;remembering&rdquo; of form data', 'usp-pro');
	elseif ($id == 'sessions_scope')       $label = esc_html__('Super strength: remember form values forever, even after successful form submission', 'usp-pro');
	elseif ($id == 'sessions_default')     $label = esc_html__('Default state of the &ldquo;remember me&rdquo; checkbox field (checked or unchecked)', 'usp-pro');
	elseif ($id == 'titles_unique')        $label = esc_html__('Require Post Titles to be unique', 'usp-pro');
	elseif ($id == 'content_unique')       $label = esc_html__('Require Post Content to be unique', 'usp-pro');
	elseif ($id == 'enable_form_lock')     $label = esc_html__('Check this box to enable the following three options', 'usp-pro');
	elseif ($id == 'featured_image')       $label = esc_html__('Auto-display submitted images as Featured Images (theme support required)', 'usp-pro');
	elseif ($id == 'unique_filename')      $label = esc_html__('Make submitted file names unique by prepending a date-based/random string', 'usp-pro');
	elseif ($id == 'user_shortcodes')      $label = esc_html__('Check this box to enable User Shortcodes in submitted post content.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-user-shortcodes/">'. esc_html__('Learn more &raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'enable_media')         $label = esc_html__('Enable non-admin users to upload media via the &ldquo;Add Media&rdquo; button.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-enable-non-admin-users-upload-media/">'. esc_html__('Learn more &raquo;', 'usp-pro') .'</a>';
	elseif ($id == 'default_options')      $label = esc_html__('Restore plugin settings upon plugin deactivation/reactivation', 'usp-pro');
	elseif ($id == 'square_image')         $label = esc_html__('Require each submitted image to have an equal width and height', 'usp-pro');
	elseif ($id == 'auto-rotate')          $label = esc_html__('Auto-rotate JPG images if necessary (requires PHP memory_limit &gt; 128M)', 'usp-pro');
	elseif ($id == 'disable_ip')           $label = esc_html__('Disable collection of user IP address information (e.g., for GDPR)', 'usp-pro');
	
	return $label;
}

function usp_callback_number_label($id) {
	
	$label = esc_html__('Undefined', 'usp-pro');
	
	if ($id == 'custom_fields') $label = esc_html__('Number of Custom Fields to auto-generate for each USP Form', 'usp-pro');
	
	return $label;
}

function usp_callback_dropdown_label($id) {
	
	$label = esc_html__('Undefined', 'usp-pro');
	
	if     ($id == 'assign_author')   $label = esc_html__('Default author for user-submitted posts', 'usp-pro');
	elseif ($id == 'assign_role')     $label = esc_html__('Role for users registering via USP Form (default: subscriber)', 'usp-pro');
	elseif ($id == 'number_approved') $label = esc_html__('Note: this setting', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-custom-post-status/">'. esc_html__('can be overridden per form', 'usp-pro') .'</a>';
	
	return $label;
}

function usp_callback_radio_label($id) {
	
	$label = esc_html__('Undefined', 'usp-pro');
	
	if     ($id == 'send_mail')   $label = esc_html__('Send email alerts using: ', 'usp-pro');
	elseif ($id == 'post_type')   $label = esc_html__('Submitted content should be posted as: ', 'usp-pro');
	elseif ($id == 'cats_menu')   $label = esc_html__('On the frontend, categories should be displayed as: ', 'usp-pro');
	elseif ($id == 'tags_order')  $label = esc_html__('On the frontend, display tags ordered by: ', 'usp-pro');
	elseif ($id == 'tags_menu')   $label = esc_html__('On the frontend, tags should be displayed as: ', 'usp-pro');
	elseif ($id == 'form_style')  $label = esc_html__('Include the following styles with all USP Forms (included via inline CSS): ', 'usp-pro');
	elseif ($id == 'post_images') $label = esc_html__('Automatically display images in submitted posts: ', 'usp-pro');
	
	return $label;
}

function usp_callback_author_menu() {
	
	global $usp_general, $wpdb;
	
	$user_count = count_users();
	
	$user_total = isset($user_count['total_users']) ? intval($user_count['total_users']) : 1;
	
	$user_max = apply_filters('usp_max_users', 200);
	
	$limit = ($user_total > $user_max) ? $user_max : $user_total;
	
	if (is_multisite()) {
			
		$query = "SELECT {$wpdb->users}.ID, {$wpdb->users}.display_name FROM {$wpdb->users}, {$wpdb->usermeta} WHERE {$wpdb->users}.ID = {$wpdb->usermeta}.user_id AND {$wpdb->usermeta}.meta_key=\"wp_{$wpdb->blogid}_user_level\" LIMIT %d";
		
	} else {
		
		$query = "SELECT ID, display_name FROM {$wpdb->users} LIMIT %d";
		
	}
	
	$users = $wpdb->get_results($wpdb->prepare($query, $limit));
	
	return $users;
	
}
