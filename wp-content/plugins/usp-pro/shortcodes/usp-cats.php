<?php // USP Pro - Category Shortcode

if (!defined('ABSPATH')) die();

/*
	Shortcode: Category
	Displays category input field
	Syntax: [usp_combo id="" class="aaa,bbb,ccc" label="Post Category" required="yes" cats="" size="3" multiple="no" exclude="" custom=""]
	Attributes:
		type        = specifies the type of field to use for displaying the categories (dropdown or checkbox) (default = General > Category settings)
		class       = comma-sep list of classes
		placeholder = text for input placeholder
		label       = text for input label
		default     = specifies the text used for the "Please select.." default option, may by any string or "null" to exclude
		required    = specifies if input is required (data-required attribute)
		cats        = specifies any default cats that should always be include with the form (comma separated)
		size        = specifies value for the size attribute of the select tag when using the select menu
		multiple    = specifies whether users should be allowed to select multiple categories
		include     = overrides global cats to specify local cats (comma-separated list of cat IDs), 
					  OR specifies to include all non-empty cats via "all", OR all cats including empty via "all_include_empty"
		exclude     = specifies any cats that should be excluded from the form (comma separated)
		custom      = any attributes or custom code
		fieldset    = enable auto-fieldset: true, false, or custom class name for fieldset (default true)
		combo       = a unique id if using chained/combo fields (must be either 1, 2, or 3)
*/

if (!function_exists('usp_input_category')) : 

function usp_input_category($args) {
	global $usp_general;
	
	// cookie value
	$id = ''; 
	$value = ''; 
	$name = 'usp-category';
	if (isset($args['combo']) && is_numeric($args['combo'])) {
		$id = $args['combo'];
		if (!empty($id)) {
			$name = 'usp-cat-combo-'. $id;
			if (isset($_COOKIE['remember'])) {
				if (isset($_SESSION['usp_form_session']['usp-cat-combo-'. $id])) $value = $_SESSION['usp_form_session']['usp-cat-combo-'. $id];
			}
		}
	} else {
		if (isset($_COOKIE['remember'])) {
			if (isset($_SESSION['usp_form_session']['usp-category'])) $value = $_SESSION['usp_form_session']['usp-category'];
		}
	}
	
	// display type
	$display_cats = $usp_general['cats_menu'];
	if     (isset($args['type']) && $args['type'] === 'checkbox') $display_cats = 'checkbox';
	elseif (isset($args['type']) && $args['type'] === 'dropdown') $display_cats = 'dropdown';
	
	// custom atts
	$custom = '';
	if (isset($args['custom'])) {
		$custom = ($display_cats === 'checkbox') ? sanitize_text_field($args['custom']) .' ' : ' '. sanitize_text_field($args['custom']);
	}
	
	// default options
	$default = esc_html__('Please select..', 'usp-pro');
	if     (isset($args['default']) && !empty($args['default']) && $args['default'] !== 'null') $default = sanitize_text_field($args['default']);
	elseif (isset($args['default']) && !empty($args['default']) && $args['default'] === 'null') $default = false;
	$default_html = ($default) ? '<option value="">'. $default .'</option>'. "\n" : "\n";
	
	// multiple select
	$multiple = '';
	$brackets = '';
	if (isset($args['multiple']) && !empty($args['multiple'])) {
		if ($args['multiple'] == 'yes' || $args['multiple'] == 'true' || $args['multiple'] == 'on') {
			$multiple = ' multiple="multiple"';
			$brackets = '[]';
		}
	} else {
		if ($usp_general['cats_multiple']) {
			$multiple = ' multiple="multiple"';
			$brackets = '[]';
		}
	}
	
	// attributes
	$field       = 'usp_error_6';
	$placeholder = usp_placeholder($args, $field);
	$required    = usp_required($args);
	$label       = usp_label($args, $field);
	
	// fieldset
	$fieldset_custom = (isset($args['fieldset'])) ? sanitize_text_field($args['fieldset']) : '';
	$fieldset = usp_fieldset($fieldset_custom);
	$fieldset_before = $fieldset['fieldset_before'];
	$fieldset_after = $fieldset['fieldset_after'];
	
	// parsley
	$parsley = ($required == 'true') ? 'required="required" ' : '';
	
	// select size
	$size = (isset($args['size']) && !empty($args['size']) && $multiple == ' multiple="multiple"') ? ' size="'. $args['size'] .'"' : '';
	
	// input class
	$class = isset($args['class']) ? 'usp-input,usp-input-category,'. $args['class'] : 'usp-input,usp-input-category';
	$classes = usp_classes($class, $field);
	
	// hidden cats
	$cats = isset($args['cats']) ? usp_cats($args['cats']) : '';
	
	// global cats
	$global_cats = usp_get_cats();
	
	// local cats
	$local_cats = array();
	if (isset($args['include']) && !empty($args['include'])) {
		if ($args['include'] === 'all') {
			$include_cats = get_categories();
			foreach ($include_cats as $include_cat) {
				$local_cats[] = $include_cat->term_id;
			}
		} elseif ($args['include'] === 'all_include_empty') {
			$include_cats = get_categories(array('hide_empty' => false));
			foreach ($include_cats as $include_cat) {
				$local_cats[] = $include_cat->term_id;
			}
		} else {
			$include_cats = trim($args['include']);
			$include_cats = explode(',', $include_cats);
			foreach ($include_cats as $include_cat) {
				$include_cat = intval(trim($include_cat));
				$cat_exists = term_exists($include_cat, 'category');
				if (!empty($cat_exists)) $local_cats[] = $include_cat;
				else unset($include_cats[$include_cat]);
			}
		}
		$local_cats = usp_get_cats($local_cats);
	}
	
	// combine cats
	$global_cats = usp_remove_duplicate_cats($global_cats, $local_cats);
	$cat_array = array_merge($global_cats, $local_cats);
	// usort($cat_array, 'usp_sort_cat_array');
     
	// exclude cats
	if (isset($args['exclude']) && !empty($args['exclude'])) {
		$exclude = trim($args['exclude']);
		$excluded = explode(',', $exclude);
		foreach($excluded as $exclude) {
			$excluded_cats[] = trim($exclude);
		}
		foreach($cat_array as $key => $val) {
			foreach($val as $k => $v) {
				if (in_array($v, $excluded_cats)) unset($cat_array[$key]);
			}
		}
	}
	
	// display cats
	if (isset($usp_general['hidden_cats']) && !empty($usp_general['hidden_cats'])) {
		
		return (!empty($cats)) ? '<input type="hidden" name="usp-cats-default" value="'. esc_attr($cats) .'" />'. "\n" : '';
		
	} else {
		
		if ($display_cats == 'checkbox') {
			
			$content = (!empty($label)) ? '<div class="usp-label usp-label-category">'. esc_html($label) .'</div>'. "\n" : '';

			if (isset($usp_general['cats_nested']) && !empty($usp_general['cats_nested'])) {
				
				$content .= '<style type="text/css">';
				$content .= '.usp-cat { display: block; } .usp-cat-0 { margin-left: 0; } .usp-cat-1 { margin-left: 20px; } ';
				$content .= '.usp-cat-2 { margin-left: 40px; } .usp-cat-3 { margin-left: 60px; } .usp-cat-4 { margin-left: 80px; }';
				$content .= '</style>'. "\n";
			}
			foreach ($cat_array as $cat) {
				
				$category = get_category($cat['id']);
				if (!$category) continue;
				
				$checked = '';
				if (is_array($value)) {
					if (in_array($cat['id'], $value)) $checked = ' checked';
				}
				
				$level = 'na';
				$cat_level = $cat['level'];
				
				if ($cat_level == 'parent') $level = '0';
				elseif ($cat_level == 'child') $level = '1';
				elseif ($cat_level == 'grandchild') $level = '2';
				elseif ($cat_level == 'great_grandchild') $level = '3';
				elseif ($cat_level == 'great_great_grandchild') $level = '4';
				
				$content .= '<label for="'. esc_attr($name) .'-'. esc_attr($cat['id']) .'" class="usp-checkbox usp-cat usp-cat-'. esc_attr($level) .'">';
				$content .= '<input type="checkbox" name="'. esc_attr($name) .'[]" id="'. esc_attr($name) .'-'. esc_attr($cat['id']) .'" value="'. esc_attr($cat['id']) .'" ';
				$content .= 'data-required="'. esc_attr($required) .'" class="'. esc_attr($classes) .'"'. $checked .' '. $custom .'/> ';
				$content .= esc_html(get_cat_name($cat['id'])) .'</label>'. "\n";
				
			}
			
		} else {
			
			$content  = (!empty($label)) ? '<label for="'. esc_attr($name) .'" class="usp-label usp-label-category">'. esc_html($label) .'</label>'. "\n" : '';
			
			$content .= '<select name="'. esc_attr($name) . $brackets .'" id="'. esc_attr($name) .'" '. $parsley .'data-required="'. esc_attr($required) .'"';
			$content .= $size . $multiple .' class="'. esc_attr($classes) .' usp-select"'. $custom .'>'. "\n" . $default_html;
			
			foreach ($cat_array as $cat) {
				
				$category = get_category($cat['id']);
				if (!$category) continue;
				
				$selected = '';
				if (is_array($value)) {
					foreach ($value as $val) {
						if (intval($cat['id']) === intval($val)) $selected = ' selected';
					}
				} else {
					if (intval($cat['id']) === intval($value)) $selected = ' selected';
				}
				
				$indent = '';
				$cat_level = $cat['level'];
				
				if (isset($usp_general['cats_nested']) && !empty($usp_general['cats_nested'])) {
					
					if ($cat_level == 'parent') $indent = '';
					elseif ($cat_level == 'child') $indent = '&emsp;';
					elseif ($cat_level == 'grandchild') $indent = '&emsp;&emsp;';
					elseif ($cat_level == 'great_grandchild') $indent = '&emsp;&emsp;&emsp;';
					elseif ($cat_level == 'great_great_grandchild') $indent = '&emsp;&emsp;&emsp;&emsp;';
				}
				
				$content .= '<option value="'. esc_attr($cat['id']) .'"'. $selected .'>'. $indent . esc_html(get_cat_name($cat['id'])) .'</option>'. "\n";
			}
			
			$content .= '</select>'. "\n";
		}
		
		// hidden fields
		if ($required == 'true') $content .= '<input type="hidden" name="'. esc_attr($name) .'-required" value="1" />'. "\n";
		if (!empty($cats))       $content .= '<input type="hidden" name="usp-cats-default" value="'. esc_attr($cats) .'" />'. "\n";
		
		return $fieldset_before . $content . $fieldset_after;
	}
}

add_shortcode('usp_category', 'usp_input_category');

endif;
