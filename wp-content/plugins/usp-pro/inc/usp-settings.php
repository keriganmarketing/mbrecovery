<?php // USP Pro - Settings Section Descriptions

if (!defined('ABSPATH')) die();

// GENERAL TAB

function section_general_0_desc() {
	echo '<p class="intro">'. esc_html__('Welcome to USP Pro! Before diving in,', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-quick-start/">'. esc_html__('check out the Quick Start guide &raquo;', 'usp-pro') .'</a></p>'; 
}
function section_general_1_desc() { 
	echo '<p>'. esc_html__('These settings control basic form functionality.', 'usp-pro') .'</p>'; 
}
function section_general_2_desc() { 
	echo '<p>'. esc_html__('These settings control how forms behave if returned with an error.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-add-remember-me-checkbox/">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a></p>'; 
}
function section_general_3_desc() { 
	echo '<p>'. esc_html__('These settings determine how users and handled when submitting form content.', 'usp-pro') .'</p>'; 
}
function section_general_4_desc() { 
	echo '<p>'. esc_html__('Here you may configure the Google reCAPTCHA and antispam/challenge question.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-add-google-recaptcha/">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a></p>'; 
}
function section_general_5_desc() { 
	echo '<p>'. esc_html__('These settings determine how categories are handled with submitted content.', 'usp-pro') .'</p>'; 
}
function section_general_6_desc() { 
	echo '<p>'. esc_html__('These settings determine how tags are handled with submitted content.', 'usp-pro') .'</p>'; 
}
function section_general_7_desc() { 
	echo '<p>'. esc_html__('Optional security measure. After publishing each form, enter its', 'usp-pro') .' <a href="'. plugins_url('img/usp-settings-form-id.jpg', dirname(__FILE__)) .'">'. esc_html__('Form ID', 'usp-pro') .'</a> ';
	echo esc_html__('in the appropriate field(s) below. See', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-enable-extra-form-security/">'. esc_html__('this post', 'usp-pro') .'</a> '. esc_html__('for more information.', 'usp-pro') .'</p>'; 
}

// STYLE TAB

function section_style_0_desc() { 
	echo '<p class="intro">'. esc_html__('Customize the appearance (CSS) and behavior (JavaScript) of USP Forms.', 'usp-pro'). ' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-adding-css-javascript/">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a></p>';
}
function section_style_1_desc() { 
	echo '<p>'. esc_html__('Here you may customize CSS/styles for your USP Forms.', 'usp-pro') . '</p>';
}
function section_style_2_desc() { 
	echo '<p>'. esc_html__('Here you may customize JavaScript for your USP Forms.', 'usp-pro') . '</p>';
}
function section_style_3_desc() { 
	echo '<p>'. esc_html__('By default, external CSS &amp; JavaScript files are loaded on every page. Here you can optimize performance by loading resources only on specific URLs.', 'usp-pro') . '</p>';
}

// UPLOADS TAB

function section_uploads_0_desc() { 
	echo '<p class="intro">'. esc_html__('Configure file uploads. Advanced configuration is possible via the', 'usp-pro') .' <code>[usp_files]</code> '. esc_html__('shortcode and Custom Fields. ', 'usp-pro');
	echo '<a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-multiple-file-upload-fields/">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a></p>';
}
function section_uploads_1_desc() { 
	echo '<p>'. esc_html__('Here are the main settings for file uploads. If in doubt on anything, go with the default option.', 'usp-pro') .'</p>'; 
}

// ADMIN TAB

function section_admin_0_desc() { 
	echo '<p class="intro">'. esc_html__('Customize email alerts and contact forms.', 'usp-pro') .'</p>'; 
}
function section_admin_1_desc() { 
	echo '<p>'. esc_html__('Here are you may specify your email settings, which are used for email alerts and contact forms.', 'usp-pro') .'</p>'; 
}
function section_admin_2_desc() { 
	echo '<p>'. esc_html__('Here are you may customize email alerts. Note: user-registration email is sent automatically by WordPress and can&rsquo;t be disabled via USP Pro.', 'usp-pro') .'</p>';  
}
function section_admin_3_desc() { 
	echo '<p>'. esc_html__('Here are you may customize email alerts that are sent to the admin. You may use ', 'usp-pro');
	echo '<a id="usp-toggle-regex-1" class="usp-toggle-regex-1" href="#usp-toggle-regex-1" title="'. esc_attr__('Show/Hide Variables', 'usp-pro') .'">'. esc_html__('shortcut variables', 'usp-pro') .'</a> ';
	echo esc_html__('in your alert messages to display dynamic bits of information.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-email-shortcodes/">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a></p>';
	echo '<pre class="usp-regex-1 usp-toggle default-hidden">blog url              = %%blog_url%%	
blog name             = %%blog_name%%
admin name            = %%admin_name%%
admin email           = %%admin_email%%
user name             = %%user_name%%
user email            = %%user_email%%
post title            = %%post_title%%
post date             = %%post_date%%
post url              = %%post_url%%
post categories       = %%post_cats%%
post tags             = %%post_tags%%
post id               = %%post_id%%
post author           = %%post_author%%
post content          = %%post_content%%
custom fields         = %%post_custom%%
specific custom field = %%__custom-field-key%%
post submitted date   = %%post_submitted_date%%
post scheduled date   = %%post_scheduled_date%%
ip address            = %%ip_address%%
post edit link        = %%edit_link%%
attached file urls    = %%files%%</pre>';
}
function section_admin_4_desc() { 
	echo '<p>'. esc_html__('Here are you may customize email alerts that are sent to the user. You may use ', 'usp-pro');
	echo '<a id="usp-toggle-regex-2" class="usp-toggle-regex-2" href="#usp-toggle-regex-2" title="'. esc_attr__('Show/Hide Variables', 'usp-pro') .'">'. esc_html__('shortcut variables', 'usp-pro') .'</a> ';
	echo esc_html__('in your alert messages to display dynamic bits of information. ', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-email-shortcodes/">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a></p>';
	echo '<pre class="usp-regex-2 usp-toggle default-hidden">blog url              = %%blog_url%%	
blog name             = %%blog_name%%
admin name            = %%admin_name%%
admin email           = %%admin_email%%
user name             = %%user_name%%
user email            = %%user_email%%
post title            = %%post_title%%
post date             = %%post_date%%
post url              = %%post_url%%
post categories       = %%post_cats%%
post tags             = %%post_tags%%
post id               = %%post_id%%
post author           = %%post_author%%
post content          = %%post_content%%
custom fields         = %%post_custom%%
specific custom field = %%__custom-field-key%%
post submitted date   = %%post_submitted_date%%
post scheduled date   = %%post_scheduled_date%%
ip address            = %%ip_address%%
post edit link        = %%edit_link%%
attached file urls    = %%files%%</pre>';
}
function section_admin_5_desc() { 
	echo '<p>'. esc_html__('Here you may customize default', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-make-contact-form/">'. esc_html__('contact form', 'usp-pro') .'</a> '. esc_html__('functionality. For ', 'usp-pro');
	echo '<a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-submit-content-and-send-email/">'. esc_html__('Contact/Post Combo Forms', 'usp-pro') .'</a>, '. esc_html__('you can use any of the ', 'usp-pro');
	echo '<a id="usp-toggle-regex-3" class="usp-toggle-regex-3" href="#usp-toggle-regex-3" title="'. esc_attr__('Show/Hide Variables &raquo;', 'usp-pro') .'">'. esc_html__('shortcut variables', 'usp-pro') .'</a> '. esc_html__('for the &ldquo;Custom Content&rdquo; setting. Note that ', 'usp-pro');
	echo '<a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-enable-post-formatting/">'. esc_html__('post-formatting', 'usp-pro') .'</a> '. esc_html__('must be enabled to send HTML-formatted email.', 'usp-pro') .'</p>';
	echo '<pre class="usp-regex-3 usp-toggle default-hidden">blog url              = %%blog_url%%	
blog name             = %%blog_name%%
admin name            = %%admin_name%%
admin email           = %%admin_email%%
user name             = %%user_name%%
user email            = %%user_email%%
post title            = %%post_title%%
post date             = %%post_date%%
post url              = %%post_url%%
post categories       = %%post_cats%%
post tags             = %%post_tags%%
post id               = %%post_id%%
post author           = %%post_author%%
post content          = %%post_content%%
custom fields         = %%post_custom%%
specific custom field = %%__custom-field-key%%
post submitted date   = %%post_submitted_date%%
post scheduled date   = %%post_scheduled_date%%
ip address            = %%ip_address%%
post edit link        = %%edit_link%%
attached file urls    = %%files%%</pre>';
}
function section_admin_6_desc() {
	echo '<p>'. esc_html__('Here you may specify custom recipients for any contact form.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-custom-recipients-contact-forms/">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a></p>';
}

// ADVANCED TAB

function section_advanced_0_desc() {
	echo '<p class="intro">'. esc_html__('Customize form configuration, post types, custom fields, and more.', 'usp-pro') .'</p>'; 
}
function section_advanced_1_desc() { 
	echo '<p>'. esc_html__('Here you may customize formatting, form demos, custom redirects, content filter, and other automatic functionality.', 'usp-pro') .'</p>'; 
}
function section_advanced_2_desc() { 
	echo '<p>'. esc_html__('Here you may customize the post types used by USP Pro. The option &ldquo;USP Posts&rdquo; uses a Custom Post Type provided by USP Pro. The option &ldquo;Existing Post Type&rdquo; uses a Custom Post Type that is provided by your theme. If in doubt, use the default option, &ldquo;Regular WP Posts&rdquo;.', 'usp-pro') .'</p>';
}
function section_advanced_3_desc() { 
	echo '<p>'. esc_html__('Here you may specify default values for submitted Post Title and Post Content. This enables you to exclude title and content fields on forms and use these values instead. ', 'usp-pro');
	echo esc_html__('Note that default fields also may be specified on a per-form basis by', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-set-values-with-hidden-fields/">'. esc_html__('setting values with hidden fields', 'usp-pro') .'</a>.</p>'; 
}
function section_advanced_4_desc() { 
	echo '<p>'. esc_html__('Here you may specify any custom text and/or markup to appear before and after all USP Forms.', 'usp-pro') .'</p>'; 
}
function section_advanced_5_desc() { 
	echo '<p>'. esc_html__('Here you may customize the various success messages. Basic markup allowed.', 'usp-pro') .'</p>'; 
}
function section_advanced_6_desc() {
	echo '<p>'. esc_html__('Here you may customize default names/labels for primary fields. These values may be customized on a per-form basis via the', 'usp-pro') .' <code>label</code> '. esc_html__('attribute.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-change-label-and-placeholder-text/">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a></p>';
}
function section_advanced_7_desc() { 
	echo '<p>'. esc_html__('Here you may customize default names/labels for', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-shortcodes/#user-registration-attributes">'. esc_html__('user-registration fields', 'usp-pro') .'</a>. '. esc_html__('These values may be customized on a per-form basis via the', 'usp-pro') .' <code>label</code> '. esc_html__('attribute.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-make-registration-form/">'. esc_html__('Learn more&nbsp;&raquo;', 'usp-pro') .'</a></p>';
}
function section_advanced_8_desc() { 
	echo '<p>'. esc_html__('Number of Custom Fields to auto-generate for each USP Form.', 'usp-pro') .' <a id="usp-toggle-a1" class="usp-toggle-a1" href="#usp-toggle-a1" title="'. esc_attr__('Show/Hide Description', 'usp-pro') .'">'. esc_html__('Toggle more info', 'usp-pro') .'</a></p>';
	echo '<p class="usp-a1 usp-toggle default-hidden">';
	echo esc_html__('The number specified below is used for two things: 1) it determines how many Custom Fields are added to newly created forms, and 2) it determines how many options to generate for the next ', 'usp-pro');
	echo esc_html__('group of settings, &ldquo;Custom Field Names&rdquo;. So for example, if', 'usp-pro') .' <code>3</code> '. esc_html__('is selected for this setting, all new USP Forms will include three Custom Fields, ', 'usp-pro');
	echo esc_html__('each with its own option in the following setting, &ldquo;Custom Field Names&rdquo;. Note that unused Custom Fields are fine; the idea is to have them readily available for each form.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-custom-fields/">'. esc_html__('Learn more about Custom Fields&nbsp;&raquo;', 'usp-pro') .'</a></p>'; 
}
function section_advanced_9_desc() {
	echo '<p>'. esc_html__('Here you may define names for Custom Fields (see previous setting).', 'usp-pro') .' <a id="usp-toggle-a2" class="usp-toggle-a2" href="#usp-toggle-a2" title="'. esc_attr__('Show/Hide Description', 'usp-pro') .'">'. esc_html__('Toggle more info', 'usp-pro') .'</a></p>';
	echo '<p class="usp-a2 usp-toggle default-hidden">';
	echo esc_html__('This setting defines &ldquo;human readable&rdquo; names for any Custom Custom Fields. They are displayed in error messages, contact-form messages, and elsewhere. They apply only to default Custom Fields.', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-custom-fields/">'. esc_html__('Learn more about Custom Fields&nbsp;&raquo;', 'usp-pro') .'</a></p>'; 
}
function section_advanced_10_desc() {
	echo '<p>'. esc_html__('Here you may specify a unique prefix to use for Custom Field names.', 'usp-pro') .' <a id="usp-toggle-a3" class="usp-toggle-a3" href="#usp-toggle-a3" title="'. esc_attr__('Show/Hide Description', 'usp-pro') .'">'. esc_html__('Toggle more info', 'usp-pro') .'</a></p>';
	echo '<p class="usp-a3 usp-toggle default-hidden">';
	echo esc_html__('For example, if you specify', 'usp-pro') .' <code>foo_</code> '. esc_html__('for this setting, you can create unique Custom Fields by including the parameter', 'usp-pro') .' <code>name#foo_whatever</code> '. esc_html__('in your custom-field definition. ', 'usp-pro');
	echo esc_html__('Note that the custom prefix may contain lowercase/uppercase alphanumeric characters, plus underscores and dashes. ', 'usp-pro');
	echo '<strong>'. esc_html__('Important:', 'usp-pro') .'</strong> '. esc_html__('do not use', 'usp-pro') .' <code>usp-</code> '. esc_html__('or', 'usp-pro') .' <code>usp_</code> '. esc_html__('for the custom prefix (these are reserved for default Custom Fields).', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-custom-field-prefix/">'. esc_html__('Learn more about Prefix Custom Fields&nbsp;&raquo;', 'usp-pro') .'</a></p>';
}
function section_advanced_11_desc() { 
	echo '<p>'. esc_html__('Here you may specify any of your own', 'usp-pro') .' <em>'. esc_html__('Custom', 'usp-pro') .'</em> '. esc_html__('Custom Field names (separated by commas).', 'usp-pro') .' <a id="usp-toggle-a4" class="usp-toggle-a4" href="#usp-toggle-a4" title="'. esc_attr__('Show/Hide Description', 'usp-pro') .'">'. esc_html__('Toggle more info', 'usp-pro') .'</a></p>';
	echo '<p class="usp-a4 usp-toggle default-hidden">';
	echo esc_html__('For this setting, there are two types of fields, Optional and Required. Required fields will trigger an error if left empty when the form is submitted; whereas Optional fields will not trigger an error if left empty. ', 'usp-pro');
	echo esc_html__('Note that Custom Field names may contain lowercase/uppercase alphanumeric characters, plus underscores and dashes. ', 'usp-pro') .'<strong>' . esc_html__('Important:', 'usp-pro') . '</strong> ';
	echo esc_html__('your Custom Field names must NOT begin with', 'usp-pro') .' <code>usp-</code> '. esc_html__('or', 'usp-pro') .' <code>usp_</code> '. esc_html__('(these are reserved for default Custom Fields).', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-custom-custom-fields/">'. esc_html__('Learn more about Custom Custom Fields&nbsp;&raquo;', 'usp-pro') .'</a></p>';
}
function section_advanced_12_desc() { 
	echo '<p>'. esc_html__('Here you may define names for any', 'usp-pro') .' <em>'. esc_html__('Custom', 'usp-pro') .'</em> '. esc_html__('Custom Fields (see previous setting).', 'usp-pro') .' <a id="usp-toggle-a5" class="usp-toggle-a5" href="#usp-toggle-a5" title="'. esc_attr__('Show/Hide Description', 'usp-pro') .'">'. esc_html__('Toggle more info', 'usp-pro') .'</a></p>';
	echo '<p class="usp-a5 usp-toggle default-hidden">';
	echo esc_html__('This setting defines &ldquo;human readable&rdquo; names for any Custom Custom Fields. They are displayed in error messages, contact-form messages, and elsewhere. ', 'usp-pro');
	echo esc_html__('Note: in order to define any names, you first must specify some Custom Custom Fields in the previous setting, then click &ldquo;Save Changes&rdquo; and return to this location.', 'usp-pro') .'</p>';
}

// MORE TAB

function section_more_0_desc() {
	echo '<p class="intro">'. esc_html__('Here you may customize error messages for USP Pro.', 'usp-pro') .'</p>'; 
}
function section_more_1_desc() { 
	echo '<p>'. esc_html__('Here you may customize the default error message. You may use text and/or basic markup.', 'usp-pro') .'</p>';
}
function section_more_2_desc() { 
	echo '<p>'. esc_html__('Here you may customize primary field errors. You may use text and/or basic markup.', 'usp-pro') .'</p>'; 
}
function section_more_3_desc() { 
	echo '<p>'. esc_html__('Here you may customize form-submission errors. You may use text and/or basic markup.', 'usp-pro') .'</p>'; 
}
function section_more_4_desc() { 
	echo '<p>'. esc_html__('Here you may customize file-submission errors. You may use text and/or basic markup.', 'usp-pro') .'</p>'; 
}
function section_more_5_desc() { 
	echo '<p>'. esc_html__('Here you may customize user-registration errors. You may use text and/or basic markup.', 'usp-pro') .'</p>'; 
}
function section_more_6_desc() { 
	echo '<p>'. esc_html__('Here you may customize various aspects of miscellaneous errors. You may use text and/or basic markup.', 'usp-pro') .'</p>'; 
}

// TOOLS TAB

function section_tools_0_desc() {
	echo '<p class="intro">'. esc_html__('Here you will find a tools for resetting, exporting, and importing options, as well as information on shortcodes, template tags, and other useful resources.', 'usp-pro') .'</p>'; 
}
function section_tools_1_desc() {
	echo usp_tools_reset();
}

// ABOUT TAB

function section_about_desc() {
	echo '<p class="intro">'. esc_html__('About USP Pro, WordPress, the server and current user.', 'usp-pro') .'</p>';
	
	echo '<div class="usp-pro-about-display">';
	
	echo '<h3><a id="usp-toggle-s1" class="usp-toggle-s1" href="#usp-toggle-s1" title="'. esc_attr__('Show/Hide Plugin Info', 'usp-pro') .'">' . esc_html__('Plugin Information', 'usp-pro') . '</a></h3>';
	echo '<div class="usp-s1 usp-toggle">' . usp_about_plugin() . '</div>';
	
	echo '<h3><a id="usp-toggle-s2" class="usp-toggle-s2" href="#usp-toggle-s2" title="'. esc_attr__('Show/Hide WordPress Info', 'usp-pro') .'">' . esc_html__('WordPress Information', 'usp-pro') . '</a></h3>';
	echo '<div class="usp-s2 usp-toggle default-hidden">' . usp_about_wp() . '</div>';
	
	echo '<h3><a id="usp-toggle-s3" class="usp-toggle-s3" href="#usp-toggle-s3" title="'. esc_attr__('Show/Hide WP Contants Info', 'usp-pro') .'">' . esc_html__('WordPress Contants', 'usp-pro') . '</a></h3>';
	echo '<div class="usp-s3 usp-toggle default-hidden">' . usp_about_constants() . '</div>';
	
	echo '<h3><a id="usp-toggle-s4" class="usp-toggle-s4" href="#usp-toggle-s4" title="'. esc_attr__('Show/Hide Server Info', 'usp-pro') .'">' . esc_html__('Server Information', 'usp-pro') . '</a></h3>';
	echo '<div class="usp-s4 usp-toggle default-hidden">' . usp_about_server() . '</div>';
	
	echo '<h3><a id="usp-toggle-s5" class="usp-toggle-s5" href="#usp-toggle-s5" title="'. esc_attr__('Show/Hide User Info', 'usp-pro') .'">' . esc_html__('User Information', 'usp-pro') . '</a></h3>';
	echo '<div class="usp-s5 usp-toggle default-hidden">' . usp_about_user() . '</div>';
	
	echo '</div>';
}

// LICENSE TAB

function section_license_desc() {
	
	$license = get_option('usp_license_key');
	$status  = get_option('usp_license_status');
	
	echo '<p class="intro"><a href="'. get_admin_url() .'plugins.php?page=usp-pro-license">'. esc_html__('Activate your license', 'usp-pro') .'</a> '. esc_html__('to unlock USP Pro and enable free automatic updates. ', 'usp-pro');
	echo '<a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/get-license-key/">'. esc_html__('Get your License Key&nbsp;&raquo;', 'usp-pro') .'</a></p>';
	echo '<h3>'. esc_html__('License Status', 'usp-pro') .'</h3>';
	
	if ($status === 'valid' || USP_PRO_CODE) {
		
		echo '<p><strong>'. esc_html__('License Status:', 'usp-pro') .'</strong> <span style="color:green;">'. esc_html__('Your USP Pro License is currently active.', 'usp-pro') .'</span></p>';
		echo '<p><strong>'. esc_html__('License Key:', 'usp-pro') .'</strong> <code style="padding:3px 5px;text-shadow:1px 1px 1px #fff;">'. $license .'</code></p>';
		echo '<p><strong>'. esc_html__('License Domain:', 'usp-pro') .'</strong> <code style="padding:3px 5px;text-shadow:1px 1px 1px #fff;">'. sanitize_text_field($_SERVER['SERVER_NAME']) .'</code></p>';
		echo '<p><strong>'. esc_html__('License Admin:', 'usp-pro') .'</strong> <code style="padding:3px 5px;text-shadow:1px 1px 1px #fff;">'. get_bloginfo('admin_email') .'</code></p>';
		echo '<p><a href="'. get_admin_url() .'plugins.php?page=usp-pro-license">Deactivate License &raquo;</a></p>';
		
	} else {
		
		echo '<p><strong>'. esc_html__('License Status:', 'usp-pro') .'</strong> <span style="color:red;">'. esc_html__('Your USP Pro License is currently inactive.', 'usp-pro') .'</span></p>';
		echo '<p><a href="'. get_admin_url() .'plugins.php?page=usp-pro-license">Activate License &raquo;</a></p>';
	}
	
	echo '<br /><h3>'. esc_html__('Resources', 'usp-pro') .'</h3>';
	echo '<ul class="list-margin">';
	echo '<li><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/get-license-key/">'. esc_html__('Get Your License Key', 'usp-pro') .'</a></li>';
	echo '<li><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/manage-license/">'. esc_html__('Manage Licensed Domains', 'usp-pro') .'</a></li>';
	echo '<li><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/download-purchased-plugin/">'. esc_html__('Download Current Version', 'usp-pro') .'</a></li>';
	echo '<li><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/download-purchase-receipt/">'. esc_html__('Download Purchase Receipt', 'usp-pro') .'</a></li>';
	echo '<li><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/troubleshooting-license-activation/">'. esc_html__('Troubleshooting License Activation', 'usp-pro') .'</a></li>';
	echo '</ul>';
}


