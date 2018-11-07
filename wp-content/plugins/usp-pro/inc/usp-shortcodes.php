<?php // USP Pro - Shortcodes

if (!defined('ABSPATH')) die();

if (usp_is_session_started() === false) session_start();

require_once(dirname(dirname(__FILE__)) .'/shortcodes/usp-cats.php');
require_once(dirname(dirname(__FILE__)) .'/shortcodes/usp-tags.php');
require_once(dirname(dirname(__FILE__)) .'/shortcodes/usp-tax.php');

/*
	Shortcode: Fieldset
		Displays opening/closing fieldset brackets
		Syntax: [usp_fieldset class="aaa,bbb,ccc"][...][#usp_fieldset]
		Attributes:
			class = classes comma-sep list (displayed as class="aaa bbb ccc") 
*/
if (!function_exists('usp_fieldset_open')) : 
function usp_fieldset_open($args) {
	
	$class = isset($args['class']) ? 'usp-fieldset,'. $args['class'] : 'usp-fieldset';
	
	$classes = usp_classes($class, 'fieldset');
	
	return '<fieldset class="'. $classes .'">'. "\n";
	
}
add_shortcode('usp_fieldset', 'usp_fieldset_open');
function usp_fieldset_close() { return '</fieldset>'. "\n"; }
add_shortcode('#usp_fieldset', 'usp_fieldset_close');
endif;

/*
	Shortcode: Name
	Displays name input field
	Syntax: [usp_name class="aaa,bbb,ccc" placeholder="Your Name" label="Your Name" required="yes" max="99" fieldset="true"]
	Attributes:
		class       = comma-sep list of classes
		placeholder = text for input placeholder
		label       = text for input label
		required    = specifies if input is required (data-required attribute)
		max         = sets maximum number of allowed characters (maxlength attribute)
		custom      = any attributes or custom code
		fieldset    = enable auto-fieldset: true, false, or custom class name for fieldset (default true)
*/
if (!function_exists('usp_input_name')) : 
function usp_input_name($args) {
	
	$current_user = wp_get_current_user();
	
	if ($current_user->ID) $value = $current_user->user_login;
	elseif (isset($_SESSION['usp_form_session']['usp-name']) && isset($_COOKIE['remember'])) $value = $_SESSION['usp_form_session']['usp-name'];
	else $value = '';
	
	$value = apply_filters('usp_input_name_value', $value);
	
	if (isset($args['custom'])) $custom = sanitize_text_field($args['custom']) .' ';
	else $custom = '';
	
	$field = 'usp_error_1';
	
	$placeholder = usp_placeholder($args, $field);
	$label = usp_label($args, $field);
	
	if (isset($args['class'])) $class = 'usp-input,usp-input-name,' . $args['class'];
	else $class = 'usp-input,usp-input-name';
	$classes = usp_classes($class, $field);
	
	if (isset($args['fieldset'])) $fieldset_custom = sanitize_text_field($args['fieldset']);
	else $fieldset_custom = '';
	
	$fieldset = usp_fieldset($fieldset_custom);
	$fieldset_before = $fieldset['fieldset_before'];
	$fieldset_after = $fieldset['fieldset_after'];
	
	$required = usp_required($args);
	if ($required == 'true') $parsley = 'required="required" ';
	else $parsley = '';
	
	$max = usp_max_att($args, '999999');

	if (empty($label)) $content = '';
	else $content = '<label for="usp-name" class="usp-label usp-label-name">'. $label .'</label>'. "\n";
	
	$content .= '<input name="usp-name" id="usp-name" type="text" value="'. esc_attr($value) .'" data-required="'. $required .'" '. $parsley . $max .'placeholder="'. $placeholder .'" class="'. $classes .'" '. $custom .'/>'. "\n";
	if ($required == 'true') $content .= '<input type="hidden" name="usp-name-required" value="1" />'. "\n";
	return $fieldset_before . $content . $fieldset_after;
}
add_shortcode('usp_name', 'usp_input_name');
endif;

/*
	Shortcode: URL
	Displays URL input field
	Syntax: [usp_url class="aaa,bbb,ccc" placeholder="Your URL" label="Your URL" required="yes" max="99"]
	Attributes:
		class       = comma-sep list of classes
		placeholder = text for input placeholder
		label       = text for input label
		required    = specifies if input is required (data-required attribute)
		max         = sets maximum number of allowed characters (maxlength attribute)
		custom      = any attributes or custom code
		fieldset    = enable auto-fieldset: true, false, or custom class name for fieldset (default true)
*/
if (!function_exists('usp_input_url')) : 
function usp_input_url($args) {
	if (isset($_SESSION['usp_form_session']['usp-url']) && isset($_COOKIE['remember'])) $value = $_SESSION['usp_form_session']['usp-url'];
	else $value = '';
	
	if (isset($args['custom'])) $custom = sanitize_text_field($args['custom']) .' ';
	else $custom = '';
	
	$field = 'usp_error_2';
	
	$placeholder = usp_placeholder($args, $field);
	$label = usp_label($args, $field);
	
	if (isset($args['class'])) $class = 'usp-input,usp-input-url,' . $args['class'];
	else $class = 'usp-input,usp-input-url';
	$classes = usp_classes($class, $field);
	
	if (isset($args['fieldset'])) $fieldset_custom = sanitize_text_field($args['fieldset']);
	else $fieldset_custom = '';
	
	$fieldset = usp_fieldset($fieldset_custom);
	$fieldset_before = $fieldset['fieldset_before'];
	$fieldset_after = $fieldset['fieldset_after'];
	
	$required = usp_required($args);
	if ($required == 'true') $parsley = 'required="required" ';
	else $parsley = '';
	$max = usp_max_att($args, '999999');

	if (empty($label)) $content = '';
	else $content  = '<label for="usp-url" class="usp-label usp-label-url">'. $label .'</label>'. "\n";
	$content .= '<input name="usp-url" id="usp-url" type="text" value="'. esc_url($value) .'" data-required="'. $required .'" '. $parsley . $max .'placeholder="'. $placeholder .'" class="'. $classes .'" '. $custom .'/>'. "\n";
	if ($required == 'true') $content .= '<input type="hidden" name="usp-url-required" value="1" />'. "\n";
	return $fieldset_before . $content . $fieldset_after;
}
add_shortcode('usp_url', 'usp_input_url');
endif;

/*
	Shortcode: Title
	Displays title input field
	Syntax: [usp_title class="aaa,bbb,ccc" placeholder="Post Title" label="Post Title" required="yes" max="99"]
	Attributes:
		class       = comma-sep list of classes
		placeholder = text for input placeholder
		label       = text for input label
		required    = specifies if input is required (data-required attribute)
		max         = sets maximum number of allowed characters (maxlength attribute)
		custom      = any attributes or custom code
		fieldset    = enable auto-fieldset: true, false, or custom class name for fieldset (default true)
*/
if (!function_exists('usp_input_title')) : 
function usp_input_title($args) {
	if (isset($_SESSION['usp_form_session']['usp-title']) && isset($_COOKIE['remember'])) $value = $_SESSION['usp_form_session']['usp-title'];
	else $value = '';
	
	if (isset($args['custom'])) $custom = sanitize_text_field($args['custom']) .' ';
	else $custom = '';
	
	$field = 'usp_error_3';
	
	$placeholder = usp_placeholder($args, $field);
	$label = usp_label($args, $field);
	
	if (isset($args['class'])) $class = 'usp-input,usp-input-title,' . $args['class'];
	else $class = 'usp-input,usp-input-title';
	$classes = usp_classes($class, $field);
	
	if (isset($args['fieldset'])) $fieldset_custom = sanitize_text_field($args['fieldset']);
	else $fieldset_custom = '';
	
	$fieldset = usp_fieldset($fieldset_custom);
	$fieldset_before = $fieldset['fieldset_before'];
	$fieldset_after = $fieldset['fieldset_after'];
	
	$required = usp_required($args);
	if ($required == 'true') $parsley = 'required="required" ';
	else $parsley = '';
	$max = usp_max_att($args, '999999');

	if (empty($label)) $content = '';
	else $content  = '<label for="usp-title" class="usp-label usp-label-title">'. $label .'</label>'. "\n";
	$content .= '<input name="usp-title" id="usp-title" type="text" value="'. esc_attr($value) .'" data-required="'. $required .'" '. $parsley . $max .'placeholder="'. $placeholder .'" class="'. $classes .'" '. $custom .'/>'. "\n";
	if ($required == 'true') $content .= '<input type="hidden" name="usp-title-required" value="1" />'. "\n";
	return $fieldset_before . $content . $fieldset_after;
}
add_shortcode('usp_title', 'usp_input_title');
endif;

/*
	Shortcode: Captcha
	Displays captcha input field
	Syntax: [usp_captcha class="aaa,bbb,ccc" placeholder="Antispam Question" label="Antispam Question" max="99"]
	Attributes:
		class       = comma-sep list of classes
		placeholder = text for input placeholder
		label       = text for input label
		max         = sets maximum number of allowed characters (maxlength attribute)
		custom      = any attributes or custom code
		fieldset    = enable auto-fieldset: true, false, or custom class name for fieldset (default true)
*/
if (!function_exists('usp_input_captcha')) : 
function usp_input_captcha($args) {
	global $usp_general;
	$required = 'true'; // always required when included in form
	if (isset($_SESSION['usp_form_session']['usp-captcha']) && isset($_COOKIE['remember'])) $value = $_SESSION['usp_form_session']['usp-captcha'];
	else $value = '';
	
	if (isset($args['custom'])) $custom = sanitize_text_field($args['custom']) .' ';
	else $custom = '';
	
	$field = 'usp_error_5';
	
	$placeholder = usp_placeholder($args, $field);
	$label = usp_label($args, $field);
	
	if (isset($args['class'])) $class = 'usp-input,usp-input-captcha,' . $args['class'];
	else $class = 'usp-input,usp-input-captcha';
	$classes = usp_classes($class, $field);

	if (isset($args['fieldset'])) $fieldset_custom = sanitize_text_field($args['fieldset']);
	else $fieldset_custom = '';
	
	$fieldset = usp_fieldset($fieldset_custom);
	$fieldset_before = $fieldset['fieldset_before'];
	$fieldset_after = $fieldset['fieldset_after'];
	
	$max = usp_max_att($args, '999999');
	
	$recaptcha_public  = isset($usp_general['recaptcha_public'])  ? $usp_general['recaptcha_public']  : '';
	$recaptcha_private = isset($usp_general['recaptcha_private']) ? $usp_general['recaptcha_private'] : '';
	$recaptcha_version = isset($usp_general['recaptcha_version']) ? $usp_general['recaptcha_version'] : 'v1';
	
	if (!empty($recaptcha_public) && !empty($recaptcha_private)) {
		
		if ($recaptcha_version === 'v1') {
			
			$id = 'recaptcha_response_field';
			$captcha = '<script type="text/javascript" src="https://www.google.com/recaptcha/api/challenge?k='. $recaptcha_public .'"></script>
			<noscript>
				<iframe src="https://www.google.com/recaptcha/api/noscript?k='. $recaptcha_public .'" height="300" width="500" frameborder="0"></iframe><br>
				<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
				<input type="hidden" name="recaptcha_response_field" id="'. $id .'" value="manual_challenge">
			</noscript>'. "\n";
			
		} elseif ($recaptcha_version === 'v2') {
			
			$captcha_params = apply_filters('usp_captcha_params', '');
			$captcha_atts   = apply_filters('usp_captcha_atts',   '');
			
			$id = 'g-recaptcha-response';
			$captcha = '<script src="https://www.google.com/recaptcha/api.js'. $captcha_params .'" async defer></script>
			<div class="g-recaptcha" '. $captcha_atts .' data-sitekey="'. $recaptcha_public .'"></div>'. "\n";
		}
	} else {
		
		$id = 'usp-captcha';
		$captcha = '<input name="usp-captcha" id="'. $id .'" type="text" value="'. esc_attr($value) .'" data-required="true" required="required" '. $max .'placeholder="'. $placeholder .'" class="'. $classes .'" '. $custom .'/>'. "\n";
	}
	
	$captcha = apply_filters('usp_captcha_output', $captcha);
	
	if (empty($label)) $content = '';
	else $content  = '<label for="'. $id .'" class="usp-label usp-label-captcha">'. $label .'</label>'. "\n";
	
	if ($required == 'true') $required = '<input type="hidden" name="usp-captcha-required" value="1" />'. "\n";
	
	return $fieldset_before . $content . $captcha . $required . $fieldset_after;
}
add_shortcode('usp_captcha', 'usp_input_captcha');
endif;

/*
	Shortcode: Content
	Displays content textarea
	Syntax: [usp_content class="aaa,bbb,ccc" placeholder="Post Content" label="Post Content" required="yes" max="999" cols="3" rows="30" richtext="off"]
	Attributes:
		class       = comma-sep list of classes
		placeholder = text for input placeholder
		label       = text for input label
		required    = specifies if input is required (data-required attribute)
		max         = sets maximum number of allowed characters (maxlength attribute)
		cols        = sets the number of columns for the textarea
		rows        = sets the number of rows for the textarea
		richtext    = specifies whether or not to use WP rich-text editor
		custom      = any attributes or custom code
		fieldset    = enable auto-fieldset: true, false, or custom class name for fieldset (default true)
*/
if (!function_exists('usp_input_content')) : 
function usp_input_content($args) {
	if (isset($_SESSION['usp_form_session']['usp-content']) && isset($_COOKIE['remember'])) $value = $_SESSION['usp_form_session']['usp-content'];
	else $value = '';
	
	if (isset($args['custom'])) $custom = ' '. sanitize_text_field($args['custom']);
	else $custom = '';
	
	$field = 'usp_error_7';
	
	$placeholder = usp_placeholder($args, $field);
	$label = usp_label($args, $field);
	
	if (isset($args['class'])) $class = 'usp-input,usp-textarea,usp-input-content,' . $args['class'];
	else $class = 'usp-input,usp-textarea,usp-input-content';
	$classes = usp_classes($class, $field);
	
	if (isset($args['fieldset'])) $fieldset_custom = sanitize_text_field($args['fieldset']);
	else $fieldset_custom = '';
	
	$fieldset = usp_fieldset($fieldset_custom);
	$fieldset_before = $fieldset['fieldset_before'];
	$fieldset_after = $fieldset['fieldset_after'];
	
	$required = usp_required($args);
	if ($required == 'true') $parsley = 'required="required" ';
	else $parsley = '';
	$max = usp_max_att($args, '999999');
	
	if (isset($args['cols']) && !empty($args['cols'])) $cols = trim(intval($args['cols']));
	else $cols = '30';
	
	if (isset($args['rows']) && !empty($args['rows'])) $rows = trim(intval($args['rows']));
	else $rows = '5';
	
	if (isset($args['richtext']) && !empty($args['richtext']) && ($args['richtext'] == 'on' || $args['richtext'] == 'yes' || $args['richtext'] == 'true')) $richtext = 'on';
	else $richtext = 'off';
	
	if (empty($label)) $content = '';
	else $content = '<label for="usp-content" class="usp-label usp-label-content">'. $label .'</label>'. "\n";
	if ($richtext == 'on') {
		$settings = array(
			'wpautop'          => true,          // enable rich text editor
			'media_buttons'    => true,          // enable add media button
			'textarea_name'    => 'usp-content', // name
			'textarea_rows'    => $rows,         // number of textarea rows
			'tabindex'         => '',            // tabindex
			'editor_css'       => '',            // extra CSS
			'editor_class'     => $classes,      // class
			'editor_height'    => '',            // editor height
			'teeny'            => false,         // output minimal editor config
			'dfw'              => false,         // replace fullscreen with DFW
			'tinymce'          => true,          // enable TinyMCE
			'quicktags'        => true,          // enable quicktags
			'drag_drop_upload' => true,          // drag-n-drop uploads
		);
		$settings = apply_filters('usp_wp_editor_settings', $settings);
		$value = apply_filters('usp_wp_editor_value', $value);
		ob_start(); // until get_wp_editor() is available..
		wp_editor($value, 'uspcontent', $settings);
		$get_wp_editor = ob_get_clean();
		$content .= $get_wp_editor;
	} else {
		$content .= '<textarea name="usp-content" id="usp-content" rows="'. $rows .'" cols="'. $cols .'" '. $max .'data-required="'. $required .'" '. $parsley .'placeholder="'. $placeholder .'" class="'. $classes .'"'. $custom .'>'. esc_html($value) .'</textarea>'. "\n";
	}
	if ($required == 'true') $content .= '<input type="hidden" name="usp-content-required" value="1" />'. "\n";
	return $fieldset_before . $content . $fieldset_after;
}
add_shortcode('usp_content', 'usp_input_content');
endif;

/*
	Shortcode: Excerpt
	Displays excerpt textarea
	Syntax: [usp_excerpt class="aaa,bbb,ccc" placeholder="Post Excerpt" label="Post Excerpt" required="yes" max="999" cols="3" rows="30" richtext="off"]
	Attributes:
		class       = comma-sep list of classes
		placeholder = text for input placeholder
		label       = text for input label
		required    = specifies if input is required (data-required attribute)
		max         = sets maximum number of allowed characters (maxlength attribute)
		cols        = sets the number of columns for the textarea
		rows        = sets the number of rows for the textarea
		richtext    = specifies whether or not to use WP rich-text editor
		custom      = any attributes or custom code
		fieldset    = enable auto-fieldset: true, false, or custom class name for fieldset (default true)
*/
if (!function_exists('usp_input_excerpt')) : 
function usp_input_excerpt($args) {
	if (isset($_SESSION['usp_form_session']['usp-excerpt']) && isset($_COOKIE['remember'])) $value = $_SESSION['usp_form_session']['usp-excerpt'];
	else $value = '';
	
	if (isset($args['custom'])) $custom = ' '. sanitize_text_field($args['custom']);
	else $custom = '';
	
	$field = 'usp_error_19';
	
	$placeholder = usp_placeholder($args, $field);
	$label = usp_label($args, $field);
	
	if (isset($args['class'])) $class = 'usp-input,usp-textarea,usp-input-excerpt,' . $args['class'];
	else $class = 'usp-input,usp-textarea,usp-input-excerpt';
	$classes = usp_classes($class, $field);
	
	if (isset($args['fieldset'])) $fieldset_custom = sanitize_text_field($args['fieldset']);
	else $fieldset_custom = '';
	
	$fieldset = usp_fieldset($fieldset_custom);
	$fieldset_before = $fieldset['fieldset_before'];
	$fieldset_after = $fieldset['fieldset_after'];
	
	$required = usp_required($args);
	if ($required == 'true') $parsley = 'required="required" ';
	else $parsley = '';
	$max = usp_max_att($args, '999999');
	
	if (isset($args['cols']) && !empty($args['cols'])) $cols = trim(intval($args['cols']));
	else $cols = '30';
	
	if (isset($args['rows']) && !empty($args['rows'])) $rows = trim(intval($args['rows']));
	else $rows = '5';
	
	if (isset($args['richtext']) && !empty($args['richtext']) && ($args['richtext'] == 'on' || $args['richtext'] == 'yes' || $args['richtext'] == 'true')) $richtext = 'on';
	else $richtext = 'off';
	
	if (empty($label)) $content = '';
	else $content = '<label for="usp-excerpt" class="usp-label usp-label-excerpt">'. $label .'</label>'. "\n";
	if ($richtext == 'on') {
		$settings = array(
			'wpautop'          => true,          // enable rich text editor
			'media_buttons'    => true,          // enable add media button
			'textarea_name'    => 'usp-excerpt', // name
			'textarea_rows'    => $rows,         // number of textarea rows
			'tabindex'         => '',            // tabindex
			'editor_css'       => '',            // extra CSS
			'editor_class'     => $classes,      // class
			'editor_height'    => '',            // editor height
			'teeny'            => false,         // output minimal editor config
			'dfw'              => false,         // replace fullscreen with DFW
			'tinymce'          => true,          // enable TinyMCE
			'quicktags'        => true,          // enable quicktags
			'drag_drop_upload' => true,          // drag-n-drop uploads
		);
		$settings = apply_filters('usp_excerpt_editor_settings', $settings);
		$value = apply_filters('usp_excerpt_editor_value', $value);
		ob_start(); // until get_wp_editor() is available..
		wp_editor($value, 'uspexcerpt', $settings);
		$get_wp_editor = ob_get_clean();
		$content .= $get_wp_editor;
	} else {
		$content .= '<textarea name="usp-excerpt" id="usp-excerpt" rows="'. $rows .'" cols="'. $cols .'" '. $max .'data-required="'. $required .'" '. $parsley .'placeholder="'. $placeholder .'" class="'. $classes .'"'. $custom .'>'. esc_html($value) .'</textarea>'. "\n";
	}
	if ($required == 'true') $content .= '<input type="hidden" name="usp-excerpt-required" value="1" />'. "\n";
	return $fieldset_before . $content . $fieldset_after;
}
add_shortcode('usp_excerpt', 'usp_input_excerpt');
endif;

/*
	Shortcode: Files
	Displays file-upload input field
	Syntax: [usp_files class="aaa,bbb,ccc" placeholder="Upload File" label="Upload File" required="yes" max="99" link="Add another file" multiple="yes" key="single"]
	Attributes:
		class       = comma-sep list of classes
		placeholder = text for input placeholder
		label       = text for input label
		required    = specifies if input is required (data-required attribute)
		max         = sets maximum number of allowed characters (maxlength attribute)
		link        = specifies text for the add-another input link (when displayed)
		multiple    = specifies whether to display single or multiple file input fields
		key         = key to use for custom field for this image
		types       = allowed file types (overrides global defaults)
		custom      = any attributes or custom code
		fieldset    = enable auto-fieldset: true, false, or custom class name for fieldset (default = true)
		preview_off = disable file preview: true
		files_min   = specifies minimum number of required files (overrides global defaults)
		files_max   = specifies maximum number of allowed files (overrides global defaults)
*/
if (!function_exists('usp_input_files')) : 
function usp_input_files($args) {
	global $usp_uploads;
	
	if (isset($args['custom'])) $custom = sanitize_text_field($args['custom']) .' ';
	else $custom = '';
	
	if (isset($args['files_max']) && is_numeric($args['files_max'])) $files_max = $args['files_max'];
	else $files_max = $usp_uploads['max_files'];
	
	if (isset($args['files_min']) && is_numeric($args['files_min'])) $files_min = $args['files_min'];
	else $files_min = $usp_uploads['min_files'];
	
	$files_max = (intval($files_max) < 0) ? 9999999 : $files_max;
	
	$files_min = (intval($files_min) > intval($files_max)) ? $files_max : $files_min;
	
	$files_min = (intval($files_min) < 1) ? 1 : $files_min;
	
	$field = 'usp_error_8';
	
	$placeholder = usp_placeholder($args, $field);
	$label = usp_label($args, $field);
	
	if (isset($args['class'])) $class = 'usp-input,usp-input-files,' . $args['class'];
	else $class = 'usp-input,usp-input-files';
	$classes = usp_classes($class, $field);
	
	if (isset($args['fieldset'])) $fieldset_custom = sanitize_text_field($args['fieldset']);
	else $fieldset_custom = '';
	
	if (isset($args['preview_off'])) $preview = '<input type="hidden" name="usp-file-preview" value="1" />'. "\n";
	else $preview = '';
	
	$fieldset = usp_fieldset($fieldset_custom);
	$fieldset_before = $fieldset['fieldset_before'];
	$fieldset_after = $fieldset['fieldset_after'];

	if (isset($args['types'])) {
		$allow_types = trim($args['types']);
		$types = explode(",", $allow_types);
		$file_types = '';
		foreach ($types as $type) $file_types .= trim($type) . ',';
		$file_types = rtrim(trim($file_types), ',');
	} else {
		$file_types = '';
	}
	
	$required = usp_required($args);
	$max = usp_max_att($args, '999999');
	
	if ($required === 'true' && empty($files_min)) $files_min = '1';
	
	$key = 'single';
	if (isset($args['key']) && is_numeric($args['key'])) $key = $args['key'];
	
	$multiple = true;
	if (isset($args['multiple'])) {
		if ($args['multiple'] == 'no' || $args['multiple'] == 'false' || $args['multiple'] == 'off') $multiple = false;
	}
	$method = '';
	if (isset($args['method'])) {
		if ($args['method'] == 'yes' || $args['method'] == 'true' || $args['method'] == 'on' || $args['method'] == 'select') $method = ' multiple="multiple"';
	}
	if (isset($args['link']) && !empty($args['link'])) $link = trim($args['link']);
	else $link = 'Add another file';
	
	if (intval($files_max) !== 0) {
		
		$content = '<div id="usp-files-wrap" class="usp-files">'. "\n";
		
		if ($multiple) {
			
			$content .= empty($label) ? '' : '<label for="usp-files" class="usp-label usp-label-files">'. $label .'</label>'. "\n";
			
			$content .= '<div class="usp-input-wrap">'. "\n";
			
			if (empty($method)) {
				
				for ($i = 1; $i <= $files_min; $i++) {
					
					$content .= '<input name="usp-files[]" id="usp-files-'. $i .'" type="file" '. $max .'data-required="'. $required .'" data-file="'. $i .'" placeholder="'. $placeholder .'" class="'. $classes .' add-another multiple" '. $custom .'/>'. "\n";
				
				}
				
				if ((intval($files_min) < intval($files_max)) || (intval($files_min) === 1 && intval($files_max) < 0)) {
					
					$content .= '<div class="usp-add-another"><a href="#">'. $link .'</a></div>'. "\n";
					
				}
				
			} else {
				
				$content .= '<input name="usp-files[]" id="usp-files" type="file" '. $max .'data-required="'. $required .'" placeholder="'. $placeholder .'" class="'. $classes .' select-file multiple"'. $method .' '. $custom .'/>'. "\n";
				
			}
			
			$content .= '</div>'. "\n";
			$content .= '<input type="hidden" name="usp-file-limit" class="usp-file-limit" value="'. $files_max .'" />'. "\n";
			$content .= '<input type="hidden" name="usp-file-count" class="usp-file-count" value="'. $files_min .'" />'. "\n";
			
			if (!empty($file_types)) $content .= '<input type="hidden" name="usp-file-types" value="'. $file_types .'" />'. "\n";
			if ($required == 'true') $content .= '<input type="hidden" name="usp-files-required" value="'. $files_min .'" />'. "\n";
			
		} else {
			
			$content .= empty($label) ? '' : '<label for="usp-file-'. $key .'" class="usp-label usp-label-files usp-label-file usp-label-file-'. $key .'">'. $label .'</label>'. "\n";
			
			$method_class = empty($method) ? ' add-another single-file' : ' select-file single-file';
			
			$content .= '<input name="usp-file-'. $key .'" id="usp-file-'. $key .'" type="file" '. $max .'data-required="'. $required .'" placeholder="'. $placeholder .'" class="'. $classes .' usp-input-file usp-input-file-'. $key . $method_class .'" '. $custom .'/>'. "\n";
			
			$content .= '<input type="hidden" name="usp-file-key" value="'. $key .'" />'. "\n";
			
			if (!empty($file_types)) $content .= '<input type="hidden" name="usp-file-types" value="'. $file_types .'" />'. "\n";
			if ($required == 'true') $content .= '<input type="hidden" name="usp-file-required-'. $key .'" value="'. $files_min .'" />'. "\n";
			
		}
		
		$content .= $preview .'</div>'. "\n" .'<div class="usp-preview"></div>'. "\n";
		
	} else {
		
		return esc_html__('File uploads not allowed. Please check your settings or contact the site administrator.', 'usp-pro');
		
	}
	
	return $fieldset_before . $content . $fieldset_after;
	
}
add_shortcode('usp_files', 'usp_input_files');
endif;

/*
	Shortcode: Remember
	Displays "remember me" button
	Syntax: [usp_remember class="aaa,bbb,ccc" label="Remember me"]
	Attributes:
		class       = comma-sep list of classes
		label       = text for input label (set checked/unchecked in USP Settings)
		custom      = any attributes or custom code
		fieldset    = enable auto-fieldset: true, false, or custom class name for fieldset (default true)
*/
if (!function_exists('usp_remember')) : 
function usp_remember($args) {
	global $usp_general;
	if ($usp_general['sessions_default']) $checked = ' checked';
	else $checked = '';
	
	if (isset($_COOKIE['remember'])) $checked = ' checked';
	elseif (isset($_COOKIE['forget'])) $checked = '';
	
	if (isset($args['custom'])) $custom = sanitize_text_field($args['custom']) .' ';
	else $custom = '';
	
	if (isset($args['class'])) $class = 'usp-remember,usp-input,usp-input-remember,' . $args['class'];
	else $class = 'usp-remember,usp-input,usp-input-remember';
	$classes = usp_classes($class, 'remember');
	
	if (isset($args['fieldset'])) $fieldset_custom = sanitize_text_field($args['fieldset']);
	else $fieldset_custom = '';
	
	$fieldset = usp_fieldset($fieldset_custom);
	$fieldset_before = $fieldset['fieldset_before'];
	$fieldset_after = $fieldset['fieldset_after'];

	if (isset($args['label']) && !empty($args['label'])) $label_text = trim($args['label']);
	else $label_text = esc_html__('Remember me', 'usp-pro');
	$label = '<label for="usp-remember" class="usp-label usp-label-remember">'. $label_text .'</label>'. "\n";
	
	$content = '';
	$content .= '<input name="usp-remember" id="usp-remember" type="checkbox" data-required="true" class="'. $classes .'" value="1"'. $checked .' '. $custom .'/> '. $label;
	
	return $fieldset_before . $content . $fieldset_after;
}
add_shortcode('usp_remember', 'usp_remember');
endif;

/*
	Shortcode: Submit
	Displays submit button
	Syntax: [usp_submit class="aaa,bbb,ccc" value="Submit Post"]
	Attributes:
		class       = comma-sep list of classes
		value       = text to display on submit button
		custom      = any attributes or custom code
		fieldset    = enable auto-fieldset: true, false, or custom class name for fieldset (default true)
*/
if (!function_exists('usp_submit_button')) : 
function usp_submit_button($args) {
	if (isset($args['class'])) $class = 'usp-submit,' . $args['class'];
	else $class = 'usp-submit';
	$classes = usp_classes($class, 'submit');
	
	if (isset($args['custom'])) $custom = sanitize_text_field($args['custom']) .' ';
	else $custom = '';
	
	if (isset($args['fieldset'])) $fieldset_custom = sanitize_text_field($args['fieldset']);
	else $fieldset_custom = '';
	
	$fieldset = usp_fieldset($fieldset_custom);
	$fieldset_before = $fieldset['fieldset_before'];
	$fieldset_after = $fieldset['fieldset_after'];

	if (isset($args['value']) && !empty($args['value'])) $value = trim($args['value']);
	else $value = esc_html__('Submit Post', 'usp-pro');

	return $fieldset_before . '<input type="submit" class="'. $classes .'" value="'. $value .'" '. $custom .'/>'. "\n" . $fieldset_after;
}
add_shortcode('usp_submit', 'usp_submit_button');
endif;

/*
	Shortcode: Email
	Displays email input field
	Syntax: [usp_email class="aaa,bbb,ccc" placeholder="Your Email" label="Your Email" required="yes" max="99"]
	Attributes:
		class       = comma-sep list of classes
		placeholder = text for input placeholder
		label       = text for input label
		required    = specifies if input is required (data-required attribute)
		max         = sets maximum number of allowed characters (maxlength attribute)
		custom      = any attributes or custom code
		fieldset    = enable auto-fieldset: true, false, or custom class name for fieldset (default true)
*/
if (!function_exists('usp_input_email')) : 
function usp_input_email($args) {
	
	$current_user = wp_get_current_user();
	
	if ($current_user->user_email) $value = $current_user->user_email;
	elseif (isset($_SESSION['usp_form_session']['usp-email']) && isset($_COOKIE['remember'])) $value = $_SESSION['usp_form_session']['usp-email'];
	else $value = '';
	
	$value = apply_filters('usp_input_email_value', $value);
	
	if (isset($args['custom'])) $custom = sanitize_text_field($args['custom']) .' ';
	else $custom = '';
	
	$field = 'usp_error_9';
	
	$placeholder = usp_placeholder($args, $field);
	$label = usp_label($args, $field);
	
	if (isset($args['class'])) $class = 'usp-input,usp-input-email,' . $args['class'];
	else $class = 'usp-input,usp-input-email';
	$classes = usp_classes($class, $field);
	
	if (isset($args['fieldset'])) $fieldset_custom = sanitize_text_field($args['fieldset']);
	else $fieldset_custom = '';
	
	$fieldset = usp_fieldset($fieldset_custom);
	$fieldset_before = $fieldset['fieldset_before'];
	$fieldset_after = $fieldset['fieldset_after'];
	
	$required = usp_required($args);
	if ($required == 'true') $parsley = 'required="required" ';
	else $parsley = '';
	$max = usp_max_att($args, '999999');

	if (empty($label)) $content = '';
	else $content  = '<label for="usp-email" class="usp-label usp-label-email">'. $label .'</label>'. "\n";
	$content .= '<input name="usp-email" id="usp-email" type="email" value="'. esc_attr($value) .'" data-required="'. $required .'" '. $parsley . $max .'placeholder="'. $placeholder .'" class="'. $classes .'" '. $custom .'/>'. "\n";
	if ($required == 'true') $content .= '<input type="hidden" name="usp-email-required" value="1" />'. "\n";
	return $fieldset_before . $content . $fieldset_after;
}
add_shortcode('usp_email', 'usp_input_email');
endif;

/*
	Shortcode: Email Subject
	Displays email subject input field
	Syntax: [usp_subject class="aaa,bbb,ccc" placeholder="Email Subject" label="Email Subject" required="yes" max="99"]
	Attributes:
		class       = comma-sep list of classes
		placeholder = text for input placeholder
		label       = text for input label
		required    = specifies if input is required (data-required attribute)
		max         = sets maximum number of allowed characters (maxlength attribute)
		custom      = any attributes or custom code
		fieldset    = enable auto-fieldset: true, false, or custom class name for fieldset (default true)
*/
if (!function_exists('usp_input_subject')) : 
function usp_input_subject($args) {
	if (isset($_SESSION['usp_form_session']['usp-subject']) && isset($_COOKIE['remember'])) $value = $_SESSION['usp_form_session']['usp-subject'];
	else $value = '';
	
	if (isset($args['custom'])) $custom = sanitize_text_field($args['custom']) .' ';
	else $custom = '';
	
	$field = 'usp_error_10';
	
	$placeholder = usp_placeholder($args, $field);
	$label = usp_label($args, $field);
	
	if (isset($args['class'])) $class = 'usp-input,usp-input-subject,'. $args['class'];
	else $class = 'usp-input,usp-input-subject';
	$classes = usp_classes($class, $field);
	
	if (isset($args['fieldset'])) $fieldset_custom = sanitize_text_field($args['fieldset']);
	else $fieldset_custom = '';
	
	$fieldset = usp_fieldset($fieldset_custom);
	$fieldset_before = $fieldset['fieldset_before'];
	$fieldset_after = $fieldset['fieldset_after'];
	
	$required = usp_required($args);
	if ($required == 'true') $parsley = 'required="required" ';
	else $parsley = '';
	$max = usp_max_att($args, '999999');

	if (empty($label)) $content = '';
	else $content  = '<label for="usp-subject" class="usp-label usp-label-subject">'. $label .'</label>'. "\n";
	$content .= '<input name="usp-subject" id="usp-subject" type="text" value="'. esc_attr($value) .'" data-required="'. $required .'" '. $parsley . $max .'placeholder="'. $placeholder .'" class="'. $classes .'" '. $custom .'/>'. "\n";
	if ($required == 'true') $content .= '<input type="hidden" name="usp-subject-required" value="1" />'. "\n";
	return $fieldset_before . $content . $fieldset_after;
}
add_shortcode('usp_subject', 'usp_input_subject');
endif;

/*
	Shortcode: Reset form button
	Returns the markup for a reset-form button
	Syntax: [usp_reset_button class="aaa,bbb,ccc" value="Reset form" url="http://example.com/usp-pro/submit/"]
	Attributes:
		class  = comma-sep list of classes
		value  = text for input placeholder
		url    = full URL that the form is displayed on (not the form URL, unless you want to redirect there)
		custom = any attributes or custom code
*/
if (!function_exists('usp_reset_button')) : 
function usp_reset_button($args) {
	if (isset($args['class'])) $class = 'usp-reset-button,' . $args['class'];
	else $class = 'usp-reset-button';
	$classes = usp_classes($class, 'reset');
	
	if (isset($args['custom'])) $custom = ' '. sanitize_text_field($args['custom']);
	else $custom = '';
	
	if (isset($args['value']) && !empty($args['value'])) $value = trim($args['value']);
	else $value = 'Reset form';
	
	if (isset($args['url']) && !empty($args['url'])) $url = trim($args['url']);
	else $url = '#please-check-shortcode';
	
	$href = get_option('permalink_structure') ? $url .'?usp_reset_form=true"' : $url .'&usp_reset_form=true';
	
	$content = '<div class="'. $classes .'"><a href="'. esc_url($href) .'"'. $custom .'>'. $value .'</a></div>'. "\n";
	return $content;
}
add_shortcode('usp_reset_button', 'usp_reset_button');
endif;

/*
	Shortcode: CC Message
	Returns the CC message
	Syntax: [usp_cc class="aaa,bbb,ccc" text=""]
	Attributes:
		class  = comma-sep list of classes
		text   = custom cc message (overrides default)
		custom = any attributes or custom code
*/
if (!function_exists('usp_cc')) : 
function usp_cc($args) {
	global $usp_admin;
	if (isset($args['class'])) $class = 'usp-contact-cc,' . $args['class'];
	else $class = 'usp-contact-cc';
	$classes = usp_classes($class, 'carbon');
	
	if (isset($args['custom'])) $custom = ' '. sanitize_text_field($args['custom']);
	else $custom = '';
	
	if (isset($usp_admin['contact_cc_note'])) $default = $usp_admin['contact_cc_note'];
	if (isset($args['text']) && !empty($args['text'])) $text = trim($args['text']);
	else $text = $default;
	
	$content = '<div class="'. $classes .'"'. $custom .'>'. $text .'</div>'. "\n";
	return $content;
}
add_shortcode('usp_cc', 'usp_cc');
endif;

/*
	Shortcode: Custom Redirect
	Redirects to specified URL on successful form submission
	Syntax: [usp_redirect url="http://example.com/" custom="class='example classes' data-role='custom'"]
	Attributes:
		url    = any complete/full URL
		custom = any custom attribute(s) using single quotes
*/
if (!function_exists('usp_redirect')) : 
function usp_redirect($args) {
	if (isset($args['custom']) && !empty($args['custom'])) $custom = ' '. stripslashes(trim($args['custom']));
	else $custom = '';

	if (isset($args['url']) && !empty($args['url'])) $url = esc_url(trim($args['url']));
	else $url = '';
	
	if (!empty($url)) return '<input type="hidden" name="usp-redirect" value="'. $url .'"'. $custom .' />'. "\n";
	else return '<!-- please check URL shortcode attribute -->'. "\n";
}
add_shortcode('usp_redirect', 'usp_redirect');
endif;

/*
	Shortcode: Agree to Terms Checkbox
	Returns HTML/CSS/JS for a required checkbox
	Syntax: [usp_agree label="" toggle="" terms="" custom="" style="" script="" alert="" class="" fieldset="" required=""]
	Attributes:
		label    = (optional) label text for the required checkbox (default: I agree to the terms)
		toggle   = (optional) text used for the toggle-terms link (default: Show/hide terms)
		terms    = (optional) text used for the terms information (default: Put terms here.)
		custom   = (optional) custom attributes for the required checkbox (default: none)
		style    = (optional) custom CSS for the required checkbox (default: none)
		script   = (optional) custom JavaScript for the required checkbox (default: none)
		alert    = (optional) enable the JavaScript alert: enter text to enable or leave blank to disable (default: blank)
		class    = (optional) custom classes for the required checkbox (default: none)
		fieldset = (optional) enable auto-fieldset: true, false, or custom class name (default true)
		required = (optional) whether the field should be required: true or false (default true)
*/
if (!function_exists('usp_required')) : 
function usp_agree($args) {
	
	$field = 'usp_error_18';
	
	$label = usp_label($args, $field);
	
	$value =(isset($_SESSION['usp_form_session']['usp-agree']) && isset($_COOKIE['remember'])) ? $_SESSION['usp_form_session']['usp-agree'] : '';
	
	$checked = ($value === 'on') ? 'checked' : '';
	
	$toggle = (isset($args['toggle']) && !empty($args['toggle'])) ? sanitize_text_field($args['toggle']) : esc_html__('Show/hide terms', 'usp-pro');
	$terms  = (isset($args['terms'])  && !empty($args['terms']))  ? sanitize_text_field($args['terms'])  : esc_html__('Put terms here.', 'usp-pro');
	$terms  = str_replace('{', '<', $terms);
	$terms  = str_replace('}', '>', $terms);
	
	$custom = isset($args['custom']) ? sanitize_text_field($args['custom']) : '';
	$style  = isset($args['style'])  ? sanitize_text_field($args['style'])  : '';
	$script = isset($args['script']) ? sanitize_text_field($args['script']) : '';
	$alert  = isset($args['alert'])  ? sanitize_text_field($args['alert'])  : '';
	
	$alert_script = (!empty($alert)) ? '$(".usp-submit").click(function(){ if (!$(".usp-input-agree").prop("checked")) { alert("'. $alert .'"); return false; } });' : '';
	
	$class = isset($args['class']) ? 'usp-agree,'. sanitize_text_field($args['class']) : 'usp-agree';
	$classes = usp_classes($class, $field);
	
	$fieldset_custom = isset($args['fieldset']) ? sanitize_text_field($args['fieldset']) : ''; 
	$fieldset = usp_fieldset($fieldset_custom);
	$fieldset_before = $fieldset['fieldset_before'];
	$fieldset_after  = $fieldset['fieldset_after'];
	
	$required = ((!isset($args['required'])) || (isset($args['required']) && $args['required'] === 'true')) ? 1 : 0;
	$required_atts = $required ? ' required="required" data-required="true"' : '';
	
	$content  = (!empty($style)) ? '<style>'. $style .'</style>'. "\n" : '';
	$content .= '<script>jQuery(document).ready(function($){ $(".usp-agree-terms").hide();'. $alert_script . $script .' });</script>'. "\n";
	$content .= '<div class="'. $classes .'">'. "\n";
	$content .= '<input name="usp-agree" id="usp-agree" type="checkbox"'. $required_atts .' class="usp-input usp-input-agree" '. $custom .' '. $checked .' /> ';
	$content .= '<label for="usp-agree" class="usp-label usp-label-agree">'. $label .'</label>'. "\n";
	$content .= $required ? '<input type="hidden" name="usp-agree-required" value="1" />'. "\n" : '';
	$content .= '<div class="usp-agree-toggle">'. $toggle .'</div>'. "\n";
	$content .= '<div class="usp-agree-terms">'. $terms .'</div>'. "\n";
	$content .= '</div>'. "\n";
	
	return $fieldset_before . $content . $fieldset_after;
	
}
add_shortcode('usp_agree', 'usp_agree');
endif;

/*
	Shortcodes: Custom Email Alerts
	Returns hidden input fields with encoded values
	Syntax: [usp_email_alert]..email message for user..[/usp_email_alert]
	Attributes:
		user    = (optional)  email alert for user or admin [default: user]
		type    = (optional)  type of alert (submit, approval, denied, scheduled) [default: submit]
		subject = (optional)  subject line [default: Email Alert from Site Name]
		cc      = (optional)  additional recipient address for admin alerts only (comma separate multiple values) [default: empty/none]
	Notes: 
		Can use shortcut %%vars%% in message
		Can use basic markup in message
*/
if (!function_exists('usp_email_alert')) :
function usp_email_alert($attr, $message = null) {
	extract(shortcode_atts(array(
		'user'    => '',
		'type'    => 'submit',
		'subject' => __('Email Alert from Site Name', 'usp-pro'),
		'cc'      => '',
	), $attr));
	
	$user    = ($user === 'admin') ? '-admin' : '';
	$types   = array_map('trim', explode(',', $type));
	$message = str_rot13(base64_encode($message));
	$subject = str_rot13(base64_encode($subject));
	$cc      = str_rot13(base64_encode($cc));
	
	unset($type);
	
	$output = '';
	
	foreach ($types as $type) {
		
		$output .= '<input type="hidden" name="usp-alert-'. $type .'-message'. $user .'" value="'. $message .'">';
		$output .= '<input type="hidden" name="usp-alert-'. $type .'-subject'. $user .'" value="'. $subject .'">';
		
		if (!empty($cc) && $user === '-admin') {
			
			$output .= '<input type="hidden" name="usp-alert-'. $type .'-cc-admin" value="'. $cc .'">';
			
		}
		
	}
	
	return $output;
	
}
add_shortcode('usp_email_alert', 'usp_email_alert');
endif;

/*
	Template Tag: Custom Fields
	Displays custom input and textarea fields
	Syntax: [usp_custom_field id=x]
	Template tag: usp_custom_field(array('id'=>'x', 'form'=>'y'));
	Attributes:
		id   = id of custom field (1-9)
		form = id of custom post type (usp_form)
	Notes:
		shortcode must be used within USP custom post type
		template tag may be used anywhere in the theme template
*/
require_once('usp-custom.php');
if (!function_exists('usp_custom_field')) : 
function usp_custom_field($args) {
	$USP_Custom_Fields = new USP_Custom_Fields();
	echo $USP_Custom_Fields->usp_custom_field($args);
}
endif;


/*
	Shortcode: USP Form
		Displays the specified USP Form by id attribute
		Syntax: [usp_form id="1" class="aaa,bbb,ccc"]
		Attributes:
			id    = id of form (post id or slug)
			class = classes comma-sep list (displayed as class="aaa bbb ccc") 
*/
if (!function_exists('usp_form')) : 
function usp_form($args) {
	
	global $usp_advanced;
	
	if (isset($args['id']) && !empty($args['id'])) {
		$id = usp_get_form_id($args['id']);
	} else {
		return esc_html__('error:usp_form:1:', 'usp-pro') . $args['id'];
	}
	
	$title = '';
	$widget_before = '';
	$widget_after = '';
	
	if (isset($args['widget']) && $args['widget']) {
		
		if (isset($args['title'])) $title = '<h2 class="widget-title">'. sanitize_text_field($args['title']) .'</h2>';
		
		$widget_before = '<section id="usp-pro-widget-'. $id .'" class="widget widget_usp_pro">';
		$widget_after  = '</section>';
		
	}
	
	$class = (isset($args['class']) && !empty($args['class'])) ? 'usp-pro,usp-form-'. $id .','. $args['class'] : 'usp-pro,usp-form-'. $id;
	$classes = usp_classes($class, 'form');
	
	$content   = get_post($id, ARRAY_A);
	$args      = array('classes' => $classes, 'id' => $id);
	$success   = isset($_GET['usp_success']) ? true : false;
	$form_wrap = usp_form_wrap($args, $success);
	
	if (get_post_type() !== 'usp_form') {
		
		if ($success && !$usp_advanced['success_form']) {
			return $widget_before . $title . $form_wrap['form_before'] . $form_wrap['form_after'] . $widget_after;
		} else {
			return $widget_before . $title . $form_wrap['form_before'] . do_shortcode($content['post_content']) . $form_wrap['form_after'] . $widget_after;
		}
		
	} else {
		
		return;
		
	}
}
add_shortcode('usp_form', 'usp_form');
endif;
