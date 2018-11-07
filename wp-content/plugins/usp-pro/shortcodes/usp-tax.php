<?php // USP Pro - Taxonomy Shortcode

if (!defined('ABSPATH')) die();

/*
	Shortcode: Taxonomy
	Displays taxonomy input field
	Syntax: [usp_taxonomy class="aaa,bbb,ccc" label="Post Taxonomy" required="yes" tax="" size="3" multiple="no" terms="123,456,789" type="checkbox"]
	Attributes:
		type        = specifies the type of field to display (checkbox or dropdown)
		class       = comma-sep list of classes
		placeholder = text for input placeholder
		label       = text for input label
		default     = specifies the text used for the "Please select.." default option, may by any string or "null" to exclude
		required    = specifies if input is required (data-required attribute)
		tax         = specifies the taxonomy
		size        = specifies value for the size attribute of the select tag when using the select menu
		multiple    = specifies whether users should be allowed to select multiple terms
		terms       = specifies which tax terms to include (comma-separated list of term IDs), 
					  OR specifies to include all non-empty terms via "all", OR all terms including empty via "all_include_empty"
		exclude     = specifies any cats that should be excluded from the form (comma separated)
		custom      = any attributes or custom code
		fieldset    = enable auto-fieldset: true, false, or custom class name for fieldset (default true)
*/

if (!function_exists('usp_input_taxonomy')) : 

function usp_input_taxonomy($args) {

	// taxonomy
	$taxonomy = (isset($args['tax'])) ? $args['tax'] : 'undefined';
	
	// cookie value
	$value = '';
	if (isset($_SESSION['usp_form_session']) && isset($_COOKIE['remember'])) {
		foreach($_SESSION['usp_form_session'] as $session_key => $session_value) {
			if (preg_match("/^usp-taxonomy-$taxonomy$/i", $session_key, $match)) {
				$value = $session_value;
			}
		}
	}
	
	// display type
	$type = (isset($args['type'])) ? $args['type'] : 'dropdown';
	
	// custom atts
	$custom = '';
	if (isset($args['custom'])) {
		$custom = ($type == 'checkbox') ? sanitize_text_field($args['custom']) .' ' : ' '. sanitize_text_field($args['custom']);
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
	}
	
	// attributes
	$field       = $taxonomy;
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
	$class = isset($args['class']) ? 'usp-input,usp-input-taxonomy,'. $args['class'] : 'usp-input,usp-input-taxonomy';
	$classes = usp_classes($class, 'usp_error_14');
	
	// local tax
	$tax_terms = array();
	if (isset($args['terms'])) {
		
		if     ($args['terms'] === 'all')               $tax_args = array('taxonomy' => $taxonomy);
		elseif ($args['terms'] === 'all_include_empty') $tax_args = array('taxonomy' => $taxonomy, 'hide_empty' => 0);
		else                                            $tax_args = array('taxonomy' => $taxonomy, 'hide_empty' => 0, 'include' => trim($args['terms']));
		
		// WP >= 4.6 replace with new WP_Term_Query()
		$tax_terms = usp_get_taxonomy(get_terms($tax_args));
		
	}
	
	// exclude tax
	if (isset($args['exclude']) && !empty($args['exclude'])) {
		$exclude = trim($args['exclude']);
		$excluded = explode(",", $exclude);
		foreach($excluded as $exclude) {
			$excluded_tax[] = trim($exclude);
		}
		foreach($tax_terms as $key => $val) {
			if (isset($val['id']) && in_array($val['id'], $excluded_tax)) unset($tax_terms[$key]);
			if ($args['terms'] === 'all' && empty($val['count'])) unset($tax_terms[$key]);
		}
	}
	
	$tax_terms = apply_filters('usp_tax_terms', $tax_terms);
	
	// display tax
	if (!empty($tax_terms)) {
		
		if ($type == 'checkbox') {
			
			$style  = '<style type="text/css">';
			$style .= '.usp-tax { display: block; } .usp-tax-0 { margin-left: 0; } .usp-tax-1 { margin-left: 20px; } ';
			$style .= '.usp-tax-2 { margin-left: 40px; } .usp-tax-3 { margin-left: 60px; } .usp-tax-4 { margin-left: 80px; }';
			$style .= '</style>'. "\n";
			
			$content  = apply_filters('usp_tax_hierarchy_style', $style);
			$content .= (!empty($label)) ? '<div class="usp-label usp-label-taxonomy">'. ucfirst(esc_html($label)) .'</div>'. "\n" : '';
			
			foreach ($tax_terms as $tax) {
				
				$checked = '';
				if (is_array($value)) {
					if (in_array($tax['id'], $value)) $checked = ' checked';
				} else {
					if (intval($tax['id']) === intval($value)) $checked = ' checked';
				}
				$level = 'na';
				$tax_level = $tax['level'];
				
				if     ($tax_level == 'parent')                 $level = '0';
				elseif ($tax_level == 'child')                  $level = '1';
				elseif ($tax_level == 'grandchild')             $level = '2';
				elseif ($tax_level == 'great_grandchild')       $level = '3';
				elseif ($tax_level == 'great_great_grandchild') $level = '4';
				
				$content .= '<label for="usp-taxonomy-'. esc_attr($taxonomy) .'-'. esc_attr($tax['id']) .'" class="usp-checkbox usp-tax usp-tax-'. esc_attr($level) .'">';
				$content .= '<input type="checkbox" name="usp-taxonomy-'. esc_attr($taxonomy) .'[]" id="usp-taxonomy-'. esc_attr($taxonomy) .'-'. esc_attr($tax['id']) .'" ';
				$content .= 'value="'. esc_attr($tax['id']) .'" data-required="'. esc_attr($required) .'" class="'. esc_attr($classes) .'" ';
				$content .= $checked .' '. $custom .'/> '. esc_html($tax['name']) .'</label>' . "\n";
				
			}
			
		} else {
			
			$content = (!empty($label)) ? '<label for="usp-taxonomy-'. esc_attr($taxonomy) .'" class="usp-label usp-label-taxonomy">'. esc_html($label) .'</label>'. "\n" : '';
			
			$content .= '<select name="usp-taxonomy-'. esc_attr($taxonomy) . $brackets .'" id="usp-taxonomy-'. esc_attr($taxonomy) .'" ';
			$content .= $parsley .'data-required="'. esc_attr($required) .'"'. $size . $multiple .' class="'. esc_attr($classes) .' usp-select"';
			$content .= $custom .'>'. "\n" . $default_html;
			
			foreach ($tax_terms as $tax) {
				
				$selected = '';
				if (is_array($value)) {
					if (in_array($tax['id'], $value)) $selected = ' selected';
				} else {
					if (intval($tax['id']) === intval($value)) $selected = ' selected';
				}
				$indent = '';
				$tax_level = $tax['level'];
				
				if     ($tax_level == 'parent')                 $indent = '';
				elseif ($tax_level == 'child')                  $indent = '&emsp;';
				elseif ($tax_level == 'grandchild')             $indent = '&emsp;&emsp;';
				elseif ($tax_level == 'great_grandchild')       $indent = '&emsp;&emsp;&emsp;';
				elseif ($tax_level == 'great_great_grandchild') $indent = '&emsp;&emsp;&emsp;&emsp;';
				
				$content .= '<option value="'. esc_attr($tax['id']) .'"'. esc_attr($selected) .'>'. $indent . esc_html($tax['name']) .'</option>'. "\n";
				
			}
			
			$content .= '</select>'. "\n";
			
		}
		
		if ($required == 'true') $content .= '<input type="hidden" name="usp-taxonomy-'. esc_attr($taxonomy) .'-required" value="1" />'. "\n";
		
	} else {
		
		$content = esc_html__('No terms found for ', 'usp-pro') . esc_html($taxonomy);
	}
	
	return $fieldset_before . $content . $fieldset_after;
}

add_shortcode('usp_taxonomy', 'usp_input_taxonomy');

endif;
