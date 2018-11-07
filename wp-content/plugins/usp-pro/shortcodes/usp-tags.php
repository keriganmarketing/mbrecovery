<?php // USP Pro - Tag Shortcode

if (!defined('ABSPATH')) die();

/*
	Shortcode: Tags
	Displays tags input field
	Syntax: [usp_tags class="aaa,bbb,ccc" placeholder="Post Tags" label="Post Tags" required="yes" max="99" tags="" size="3" multiple="no"]
	Attributes:
		type        = specifies the type of field to use for displaying the tags (dropdown, checkbox, input) (default = General > Tag settings)
		class       = comma-sep list of classes
		placeholder = text for input placeholder
		label       = text for input label
		default     = specifies the text used for the "Please select.." default option, may by any string or "null" to exclude
		required    = specifies if input is required (data-required attribute)
		max         = sets maximum number of allowed characters (maxlength attribute)
		tags        = specifies any default tags that should always be include with the form
		size        = specifies value for the size attribute of the select tag when using the select menu
		multiple    = specifies whether users should be allowed to select multiple tags
		include     = overrides global tags to specify local tags (comma-separated list of tag IDs), 
					  OR specifies to include all non-empty tags via "all", OR all tags including empty via "all_include_empty"
		exclude     = specifies any tags that should be excluded from the form (comma separated)
		custom      = any attributes or custom code
		fieldset    = enable auto-fieldset: true, false, or custom class name for fieldset (default true)
*/

if (!function_exists('usp_input_tags')) :
 
function usp_input_tags($args) {
	global $usp_general;
	
	// cookie value
	$value = (isset($_SESSION['usp_form_session']['usp-tags']) && isset($_COOKIE['remember'])) ? $_SESSION['usp_form_session']['usp-tags'] : '';
	
	// display type
	$display_tags = $usp_general['tags_menu'];
	if     (isset($args['type']) && $args['type'] === 'checkbox') $display_tags = 'checkbox';
	elseif (isset($args['type']) && $args['type'] === 'dropdown') $display_tags = 'dropdown';
	elseif (isset($args['type']) && $args['type'] === 'input')    $display_tags = 'input';
	
	// custom atts
	$custom = '';
	if (isset($args['custom']) && !empty($args['custom'])) {
		$custom = ($display_tags == 'checkbox' || $display_tags == 'input') ? sanitize_text_field($args['custom']) .' ' : ' '. sanitize_text_field($args['custom']);
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
		if ($usp_general['tags_multiple']) {
			$multiple = ' multiple="multiple"';
			$brackets = '[]';
		}
	}
	
	// attributes
	$field       = 'usp_error_4';
	$placeholder = usp_placeholder($args, $field);
	$required    = usp_required($args);
	$label       = usp_label($args, $field);
	$max         = usp_max_att($args, '999999');
	
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
	$class = isset($args['class']) ? 'usp-input,usp-input-tags,'. $args['class'] : 'usp-input,usp-input-tags';
	$classes = usp_classes($class, $field);
	
	// hidden tags
	$tags = isset($args['tags']) ? usp_tags($args['tags']) : '';
	
	// global tags
	$tag_array = isset($usp_general['tags']) ? $usp_general['tags'] : array();
	
	$tag_objects = array();
	
	foreach ($tag_array as $tag_arr) {
		
		$tag_objects[] = get_tag(intval($tag_arr));
		
	}
	
	// local tags
	if (isset($args['include']) && !empty($args['include'])) {
		
		if ($args['include'] === 'all') {
			
			$include_tags = get_tags();
			
			$tag_objects = array_merge($tag_objects, $include_tags);
			
		} elseif ($args['include'] === 'all_include_empty') {
			
			$include_tags = get_tags(array('hide_empty' => false));
			
			$tag_objects = array_merge($tag_objects, $include_tags);
			
		} else {
			
			$include_tags = trim($args['include']);
			$include_tags = explode(',', $include_tags);
			
			foreach ($include_tags as $include_tag) {
				
				$include_tag = intval(trim($include_tag));
				$tag_exists  = term_exists($include_tag, 'post_tag');
				
				if (!empty($tag_exists)) $tag_objects[] = get_tag($include_tag);
				
			}
		}
	}
	
	// exclude tags
	$excluded_tags = array();
	if (isset($args['exclude']) && !empty($args['exclude'])) {
		
		$exclude_tags = trim($args['exclude']);
		$exclude_tags = explode(',', $exclude_tags);
		
		foreach($exclude_tags as $exclude_tag) {
			
			$exclude_tag = trim($exclude_tag);
			$excluded_tags[] = get_tag($exclude_tag);
			
		}
		
		foreach($tag_objects as $tag_key => $tag_value) {
			
			$term_id = $tag_value->term_id;
			
			foreach ($excluded_tags as $excluded_tag) {
				
				$excluded_id = $excluded_tag->term_id;
				
				if ($term_id === $excluded_id) unset($tag_objects[$tag_key]);
				
			}
		}
	}
	
	// remove duplicates
	$tag_objects = array_filter($tag_objects, 'usp_pro_unique_tag_objects');
	
	// tag order
	$order = isset($usp_general['tags_order']) ? $usp_general['tags_order'] : 'name_asc';
	
	if ($order === 'name_asc') {
		
		usort($tag_objects, 'usp_pro_compare_tag_names');
		
	} elseif ($order === 'name_desc') {
		
		usort($tag_objects, 'usp_pro_compare_tag_names');
		$tag_objects = array_reverse($tag_objects);
	
	
	} elseif ($order === 'id_asc') {
		
		usort($tag_objects, 'usp_pro_compare_tag_ids');
		
	} elseif ($order === 'id_desc') {
		
		usort($tag_objects, 'usp_pro_compare_tag_ids');
		$tag_objects = array_reverse($tag_objects);
		
	} elseif ($order === 'count_asc') {
		
		usort($tag_objects, 'usp_pro_compare_tag_counts');
		
	} elseif ($order === 'count_desc') {
		
		usort($tag_objects, 'usp_pro_compare_tag_counts');
		$tag_objects = array_reverse($tag_objects);
		
	}
	
	// display tags
	if (isset($usp_general['hidden_tags']) && !empty($usp_general['hidden_tags'])) {
		
		return (!empty($tags)) ? '<input type="hidden" name="usp-tags-default" value="'. esc_attr($tags) .'" />'. "\n" : '';
		
	} else {
		
		if ($display_tags == 'checkbox') {
			
			$content = (!empty($label)) ? '<div class="usp-label usp-label-tags">'. esc_html($label) .'</div>'. "\n" : '';
			
			foreach ((array) $tag_objects as $tag_object) {
				
				$tag   = esc_attr($tag_object->term_id);
				$count = esc_attr($tag_object->count);
				$name  = esc_html($tag_object->name);
				
				$the_tag = get_term_by('id', $tag, 'post_tag');
				
				if (!$the_tag) continue;
				
				$checked = '';
				
				if (is_array($value)) { 
					
					if (in_array($tag, $value)) $checked = ' checked';
					
				}
				
				$content .= '<label for="usp-tag-'. $tag .'" class="usp-checkbox usp-tag" data-count="'. $count .'" data-id="'. $tag .'">';
				$content .= '<input type="checkbox" name="usp-tags[]" id="usp-tag-'. $tag .'" value="'. $tag .'" data-required="'. esc_attr($required);
				$content .= '" class="'. esc_attr($classes) .'"'. $checked .' '. $custom .'/> '. $name .'</label>'. "\n";
				
			}
			
		} elseif ($display_tags == 'input') {
			
			$content  = (!empty($label)) ? '<label for="usp-tags" class="usp-label usp-label-tags">'. esc_html($label) .'</label>'. "\n" : '';
			$content .= '<input name="usp-tags" id="usp-tags" type="text" value="'. esc_attr($value) .'" data-required="'. esc_attr($required);
			$content .= '" '. $parsley .' '. $max .' placeholder="'. esc_attr($placeholder);
			$content .= '" class="'. esc_attr($classes) .'" '. $custom .'/>'. "\n";
			
		} else {
			
			$content  = (!empty($label)) ? '<label for="usp-tags" class="usp-label usp-label-tags">'. esc_html($label) .'</label>'. "\n" : '';
			$content .= '<select name="usp-tags'. $brackets .'" id="usp-tags" '. $parsley .'data-required="'. esc_attr($required);
			$content .= '"'. $size . $multiple .' class="'. esc_attr($classes) .' usp-select"'. $custom .'>'. "\n" . $default_html;
			
			foreach ((array) $tag_objects as $tag_object) {
				
				$tag   = esc_attr($tag_object->term_id);
				$count = esc_attr($tag_object->count);
				$name  = esc_html($tag_object->name);
				
				$the_tag = get_term_by('id', $tag, 'post_tag');
				
				if (!$the_tag) continue;
				
				$selected = '';
				
				if (is_array($value)) {
					
					foreach ($value as $val) {
						
						if (intval($tag) === intval($val)) $selected = ' selected';
						
					}
					
				} else {
					
					if (intval($tag) === intval($value)) $selected = ' selected';
					
				}
				
				$content .= '<option value="'. $tag .'"'. $selected .' data-count="'. $count .'" data-id="'. $tag .'">'. esc_html($name) .'</option>'. "\n";
			}
			
			$content .= '</select>'. "\n";
			
		}
		
		// hidden fields
		if ($required == 'true') $content .= '<input type="hidden" name="usp-tags-required" value="1" />'. "\n";
		if (!empty($tags))       $content .= '<input type="hidden" name="usp-tags-default" value="'. $tags .'" />'. "\n";
		
		return $fieldset_before . $content . $fieldset_after;
		
	}
}

add_shortcode('usp_tags', 'usp_input_tags');

endif;



if (!function_exists('usp_pro_unique_tag_objects')) :

function usp_pro_unique_tag_objects($object) {
	
	static $id_list = array();
	
	if (in_array($object->term_id, $id_list)) return false;
	
	$id_list[] = $object->term_id;
	
	return true;
	
}

endif;



if (!function_exists('usp_pro_compare_tag_names')) :

function usp_pro_compare_tag_names($a, $b) {
	
	return strcmp($a->name, $b->name);
	
}

endif;



if (!function_exists('usp_pro_compare_tag_ids')) :

function usp_pro_compare_tag_ids($a, $b) {
	
	return strcmp($a->term_id, $b->term_id);
	
}

endif;



if (!function_exists('usp_pro_compare_tag_counts')) :

function usp_pro_compare_tag_counts($a, $b) {
	
	return strcmp($a->count, $b->count);
	
}

endif;


