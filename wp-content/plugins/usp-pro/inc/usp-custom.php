<?php // USP Pro - Custom Fields

if (!defined('ABSPATH')) die();

/*
	Shortcode: Custom Fields
	Displays custom input and textarea fields
	Syntax: [usp_custom_field form="x" id="y"]
	Template tag: usp_custom_field(array('form'=>'y', 'id'=>'x'));
	Attributes:
		id   = id of custom field (1-9)
		form = id of custom post type (usp_form)
	Notes:
		shortcode must be used within USP custom post type
		template tag may be used anywhere in the theme template
*/
if (!class_exists('USP_Custom_Fields')) {
	class USP_Custom_Fields {
		function __construct() { 
			add_shortcode('usp_custom_field', array(&$this, 'usp_custom_field')); 
		}
		function usp_custom_field($args) {
			global $usp_advanced;
			
			if (isset($args['id']) && !empty($args['id'])) $id = $args['id'];
			else return esc_html__('error:usp_custom_field:1:', 'usp-pro') . $args['id'];
			
			if (isset($args['form']) && !empty($args['form'])) $form = usp_get_form_id($args['form']);
			else return esc_html__('error:usp_custom_field:2:', 'usp-pro') . $args['form'];
			
			$custom_fields = get_post_custom($form);
			if (is_null($custom_fields) || empty($custom_fields)) return esc_html__('error:usp_custom_field:3:', 'usp-pro') . $custom_fields;
			
			$custom_merged = usp_merge_custom_fields();
			$custom_arrays = usp_custom_field_string_to_array();
			$custom_prefix = $usp_advanced['custom_prefix'];
			
			if (empty($custom_prefix)) $custom_prefix = 'prefix_';
			
			foreach ($custom_fields as $key => $value) {
				
				$key = trim($key);
				if ('_' == $key{0}) continue;
				if ($key !== '[usp_custom_field form="'. $args['form'] .'" id="'. $id .'"]') continue;
				
				if (preg_match("/usp_custom_field/i", $key)) {
					
					$atts = explode("|", $value[0]);
					
					$get_value    = $this->usp_custom_field_cookies($id, $value);
					$default_atts = $this->usp_custom_field_defaults($id, $get_value);
					$field_atts   = $this->usp_custom_field_atts($atts, $default_atts);
					
					if (empty($field_atts)) return esc_html__('error:usp_custom_field:4:', 'usp-pro') . $field_atts;
						
					$fieldset = usp_fieldset_custom($field_atts['fieldset'], $field_atts['field_class']);
					
					if ((in_array($field_atts['name'], $custom_merged)) || (stripos($field_atts['name'], $custom_prefix) !== false)) $prefix = '';
					else $prefix = 'usp-custom-';
					
					$checked = ''; $selected = ''; $class = ''; $class_label = ''; $label_custom = '';
					
					if (!empty($field_atts['checked']))      $checked      = ' checked="checked"';
					if (!empty($field_atts['selected']))     $selected     = ' selected="selected"';
					if (!empty($field_atts['class']))        $class        = $field_atts['class'] .' ';
					if (!empty($field_atts['label_class']))  $class_label  = $field_atts['label_class'] .' ';
					if (!empty($field_atts['label_custom'])) $label_custom = ' '. $field_atts['label_custom'];
					
					$multiple = ''; $select_array = ''; $multiple_enable = array('multiple','true','yes','on');
					
					if (in_array($field_atts['multiple'], $multiple_enable)) {
						$multiple = ' multiple="multiple"';
						$select_array = '[]';
					}
					
					if     (in_array($field_atts['name'], $custom_arrays['required'])) $field_atts['data-required'] = 'true';
					elseif (in_array($field_atts['name'], $custom_arrays['optional'])) $field_atts['data-required'] = 'false';
					
					$field_hidden = ''; $parsley = '';
					
					if ($field_atts['data-required'] == 'true') {
						if (!empty($field_atts['checkboxes']) && empty($multiple)) {
							unset($field_atts['data-required']);
						} else {
							if ($field_atts['field'] !== 'input_file') {
								$field_hidden = '<input type="hidden" name="'. $prefix . $field_atts['name'] .'-required" value="1" />'. "\n";
							}
						}
						$parsley = ' required="required"'; 
					} else {
						if ($field_atts['data-required'] == 'null') {
							unset($field_atts['data-required']);
						}
					}
					
					
					
					$label_for = !is_numeric($field_atts['for']) ? $prefix . $field_atts['for'] : $prefix . $field_atts['name'];
					
					$max_att   = (isset($field_atts['max']) && !empty($field_atts['max'])) ? ' max="'. $field_atts['max'] .'"' : '';
					
					$error      = $this->usp_custom_field_errors($id, $field_atts, $custom_prefix);
					$checkboxes = $this->usp_custom_fields_checkboxes($id, $field_atts, $prefix, $select_array);
					$radio      = $this->usp_custom_fields_radio($field_atts, $prefix);
					$options    = $this->usp_custom_fields_select($field_atts);
					$files      = $this->usp_custom_fields_files($field_atts, $prefix, $class_label, $label_custom, $error);
					
					
					
					//
					switch ($field_atts['field']) {
						case 'input':
							$field_start = '<input name="'. $prefix . $field_atts['name'] .'" id="'. $label_for .'"'. $max_att .' ';
							$field_end   = 'class="'. $error . $class .'usp-input usp-input-custom usp-form-'. $form .'"'. $checked . $selected . $parsley .' />';
							$label_class = 'class="'. $class_label .'usp-label usp-label-input usp-label-custom usp-form-'. $form;
						break;
						case 'textarea':
							
							$get_wp_editor = $this->usp_custom_field_wp_editor($field_atts, $error, $form, $prefix);
							
							if (!empty($get_wp_editor)) {
								
								$field_start = $get_wp_editor;
								$field_end   = '';
								
								unset($field_atts['type'], $field_atts['value'], $field_atts['rte_id'], $field_atts['placeholder'], $field_atts['data-required'], $field_atts['data-richtext']);
								
							} else {
									
								$field_start = '<textarea name="'. $prefix . $field_atts['name'] .'" id="'. $label_for .'"'. $max_att .' ';
								$field_end   = 'class="'. $error . $class .'usp-input usp-textarea usp-form-'. $form .'" rows="'. $field_atts['rows'] .'" cols="'. $field_atts['cols'] .'"'. $parsley .'>'. $field_atts['value'] .'</textarea>';
								
								unset($field_atts['type'], $field_atts['value']);
								
							}
							
							$label_class = 'class="'. $class_label .'usp-label usp-label-textarea usp-label-custom usp-form-'. $form;
							
						break;
						case 'select':
							$field_start = '<select name="'. $prefix . $field_atts['name'] . $select_array .'" id="'. $label_for .'" ';
							$field_end   = 'class="'. $error . $class .'usp-input usp-select usp-form-'. $form .'"'. $parsley . $multiple .'>'. $options .'</select>';
							$label_class = 'class="'. $class_label .'usp-label usp-label-select usp-label-custom usp-form-'. $form;
							unset($field_atts['type'], $field_atts['value'], $field_atts['multiple'], $field_atts['placeholder']);
						break;
						case 'input_checkbox':
							$field_start = '<div class="'. $error . $class .'usp-input usp-checkboxes usp-form-'. $form .'">';
							$field_end   = $checkboxes .'</div>';
							$label_class = '';
							unset($field_atts['type'], $field_atts['value'], $field_atts['multiple'], $field_atts['placeholder'], $field_atts['data-required']);
						break;
						case 'input_radio':
							$field_start = '<div class="'. $error . $class .'usp-input usp-radio usp-form-'. $form .'">';
							$field_end   = $radio .'</div>';
							$label_class = '';
							unset($field_atts['type'], $field_atts['value'], $field_atts['placeholder'], $field_atts['data-required']);
						break;
						case 'input_file':
							$field_start = '<div id="'. $prefix . $field_atts['name'] .'-files" class="'. $class .'usp-files">'. $files['start'];
							$field_end   = $files['end'] .'</div>'. "\n". '<div class="usp-preview"></div>';
							$label_class = '';
							unset($field_atts['type'], $field_atts['value']);
						break;
						default:
							return esc_html__('error:usp_custom_field:5:', 'usp-pro') . $field_atts['field'];
						break;
					}
					//
					
					
					
					if ($field_atts['field'] == 'input_checkbox' || $field_atts['field'] == 'input_radio' || $field_atts['field'] == 'input_file') $label = '';
					else $label = '<label for="'. $label_for .'" '. $label_class .'"'. $label_custom . '>'. $field_atts['label'] .'</label>' . "\n";
					
					if (isset($field_atts['label']) && $field_atts['label'] == 'null') $label = '';
					if (isset($field_atts['placeholder']) && $field_atts['placeholder'] == 'null') unset($field_atts['placeholder']);
					
					$field_atts = $this->usp_custom_field_unset($field_atts);
					
					$attributes = '';
					foreach ($field_atts as $att_name => $att_value) $attributes .= $att_name .'="'. $att_value .'" ';
					
					$content = $label . $field_start . $attributes . $field_end . "\n" . $field_hidden;
					
					$return = $fieldset['fieldset_before'] . $content . $fieldset['fieldset_after'];
					return apply_filters('usp_custom_field_data', $return);
				}
			}
		}
		function usp_custom_field_cookies($id, $value) {
			$get_value = '';
			if (isset($_COOKIE['remember'])) {
				preg_match("/name#([0-9a-z_-]+)/i",         $value[0], $name);
				preg_match("/checkboxes#([0-9a-z: _-]+)/i", $value[0], $checkbox);
				
				if (!empty($name[1])) {
					if (isset($_SESSION['usp_form_session']['usp-custom-'. $name[1]])) {
						$get_value = $_SESSION['usp_form_session']['usp-custom-'. $name[1]];
					
					} elseif (isset($_SESSION['usp_form_session'][$name[1]])) {
						$get_value = $_SESSION['usp_form_session'][$name[1]];
					}
				} else {
					if (isset($_SESSION['usp_form_session']['usp-custom-'. $id])) {
						$get_value = $_SESSION['usp_form_session']['usp-custom-'. $id];
					
					} elseif (!empty($checkbox[1])) {
						$get_value = array();
						$checkbox_array = explode(":", $checkbox[1]);
						
						foreach ($checkbox_array as $key => $value) {
							$value = trim($value);
							$checkbox_value = strtolower(trim(str_replace(' ', '_', $value)));
							
							if (isset($_SESSION['usp_form_session']['usp-custom-'. $checkbox_value])) {
								$get_value[] = $_SESSION['usp_form_session']['usp-custom-'. $checkbox_value];
							}
						}
					}
				}
				if (stripos($value[0], 'data-richtext#true') !== false) {
					if (!is_array($get_value)) $get_value = html_entity_decode($get_value, ENT_QUOTES, get_option('blog_charset', 'UTF-8'));
				}
			}
			return apply_filters('usp_custom_field_cookies', $get_value);
		}
		function usp_custom_field_defaults($id, $get_value) {
			global $usp_uploads;
			/*
				Notes:
					form fields:   input, textarea, select, input_checkbox, input_radio, input_file
					form atts:     autocomplete, novalidate, 
					
					input types:   text, checkbox, password, radio, file, url, search, email, tel, month, week, time, datetime, datetime-local, color, date, range, number
					
					input atts:    autocomplete, autofocus, form, formaction, formenctype, formmethod, formnovalidate, formtarget, height, width, list, min, max, multiple, 
					               pattern, placeholder, required, step, value, type, src, size, readonly, name, maxlength, disabled, checked, alt, align, accept
					
					textarea atts: autofocus, cols, disabled, form, name, placeholder, readonly, required, rows, wrap
					select atts:   autofocus, disabled, form, multiple, name, required, size
					option atts:   value, label, selected, disabled
					checkbox atts: name, disabled, form, type, checked, value, autofocus, required
					radio atts:    name, disabled, form, type, checked, value, required 
					
					mime types:    audio/*, video/*, image/*
					
				Infos:
					https://plugin-planet.com/usp-pro-shortcodes/#custom-fields
					http://iana.org/assignments/media-types
			*/
			$default_atts = array(
				// general atts
				'field'              => 'input',                           // type of field
				'type'               => 'text',                            // type of input, valid when field = input
				'name'               => $id,                               // field name, should equal for attribute
				'value'              => $get_value,                        // field value
				'data-required'      => 'true',                            // required + data-required atts
				'placeholder'        => esc_html__('Example Input ', 'usp-pro') . $id, // placeholder
				'class'              => '',                                // field class
				'checked'            => '',                                // checked attribute
				'multiple'           => '',                                // enable multiple selections
				
				// fieldset atts
				'field_class'        => '', // custom field class
				'fieldset'           => '', // use null to disable fieldset
				
				// label atts
				'label'              => esc_html__('Example Label ', 'usp-pro') . $id, // label text
				'for'                => $id, // for att should equal name att
				'label_class'        => '',  // custom label class
				'label_custom'       => '',  // custom attribute(s)
				
				// custom atts
				'custom_1'           => '', // custom_1#attribute:value
				'custom_2'           => '', // custom_2#attribute:value
				'custom_3'           => '', // custom_3#attribute:value
				'custom_4'           => '', // custom_4#attribute:value
				'custom_5'           => '', // custom_5#attribute:value
				
				// textarea atts
				'rows'               => '3',  // number of rows
				'cols'               => '30', // number of columns
				
				// select atts
				'options'            => '', // list of select options
				'option_select'      => '', // list of selected options
				'option_default'     => esc_html__('Please select', 'usp-pro'), // default option
				'selected'           => '', // general selected attribute
				
				// checkbox atts
				'checkboxes'         => '', // list of checkbox values
				'checkboxes_checked' => '', // list of selected checkboxes
				'checkboxes_req'     => '', // list of required checkboxes
				
				// radio atts
				'radio_inputs'       => '', // list of radio inputs
				'radio_checked'      => '', // the selected input
				
				// files atts
				'accept'             => '', // mime types
				'types'              => '', // accepted file types
				'method'             => '', // blank = "Add another", select = dropdown menu
				'link'               =>  esc_html__('Add another file', 'usp-pro'), // link text
				'files_min'          => $usp_uploads['min_files'],      // min number of files
				'files_max'          => $usp_uploads['max_files'],      // max number of files
				'preview_off'        => '',
				'max'                => '', // max length of files value
			);
			return apply_filters('usp_custom_field_atts_default', $default_atts);
		}
		function usp_custom_field_atts($atts, $default_atts) {
			foreach ($atts as $att) {
				$a = explode("#", $att); // eg: $a[0] = field, $a[1] = input
				if ($a[0] == 'atts' && $a[1] == 'defaults') continue; // use defaults
				if (isset($a[0])) $user_att_names[]  = $a[0];
				if (isset($a[1])) $user_att_values[] = $a[1];
			}
			if (!empty($user_att_names) && !empty($user_att_values)) $user_atts = array_combine($user_att_names, $user_att_values);
			else $user_atts = $default_atts;
			
			$field_atts = wp_parse_args($user_atts, $default_atts);
			
			if (isset($user_att_names)) unset($user_att_names);
			if (isset($user_att_values)) unset($user_att_values);
			
			$custom_att_names = array();
			$custom_att_values = array();
			
			foreach ($field_atts as $key => $value) {
				
				if (preg_match("/^custom_/i", $key)) {
					$b = explode(":", $value);
					if (isset($b[0])) $custom_att_names[]  = $b[0];
					if (isset($b[1])) $custom_att_values[] = $b[1];
					if (isset($field_atts[$key])) unset($field_atts[$key]);
				}
			}
			
			foreach ($custom_att_names as $key => $value) {
				if (is_null($value) || empty($value)) unset($custom_att_names[$key]);
			}
			foreach ($custom_att_values as $key => $value) {
				if (is_null($value) || empty($value)) unset($custom_att_values[$key]);
			}
			
			if (!empty($custom_att_names) && !empty($custom_att_values)) $custom_atts = array_combine($custom_att_names, $custom_att_values);
			else $custom_atts = array();
			
			$field_atts = wp_parse_args($custom_atts, $field_atts);
			if (isset($custom_att_names)) unset($custom_att_names);
			if (isset($custom_att_values)) unset($custom_att_values);
			
			return apply_filters('usp_custom_field_atts', $field_atts);
		}
		function usp_custom_field_wp_editor($field_atts, $error, $form, $prefix) {
			$get_wp_editor = '';
			
			$class = $error . $field_atts['class'] .'usp-input usp-textarea usp-form-'. $form;
			
			$rte_id = (isset($field_atts['rte_id'])) ? 'uspcustom'. $field_atts['rte_id'] : 'uspcustom';
			
			if (isset($field_atts['data-richtext']) && $field_atts['data-richtext'] == 'true') {
				$settings = array(
					'wpautop'          => true,                              // enable rich text editor
					'media_buttons'    => true,                              // enable add media button
					'textarea_name'    => $prefix . $field_atts['name'],     // name
					'textarea_rows'    => $field_atts['rows'],               // number of textarea rows
					'tabindex'         => '',                                // tabindex
					'editor_css'       => '',                                // extra CSS
					'editor_class'     => $class,                            // class
					'editor_height'    => '',                                // editor height
					'teeny'            => false,                             // output minimal editor config
					'dfw'              => false,                             // replace fullscreen with DFW
					'tinymce'          => true,                              // enable TinyMCE
					'quicktags'        => true,                              // enable quicktags
					'drag_drop_upload' => true,                              // drag-n-drop uploads
				);
				$settings = apply_filters('usp_custom_editor_settings', $settings);
				$value = apply_filters('usp_custom_editor_value', $field_atts['value']);
				ob_start(); // until get_wp_editor() is available..
				wp_editor($value, $rte_id, $settings);
				$get_wp_editor = ob_get_clean();
				return apply_filters('usp_custom_field_wp_editor', $get_wp_editor);
			}
		}
		function usp_custom_field_errors($id, $field_atts, $custom_prefix) {
			
			$error = '';
			
			$field = isset($field_atts['name']) ? $field_atts['name'] : 'undefined';
			
			wp_parse_str(wp_strip_all_tags($_SERVER['QUERY_STRING']), $vars);
			
			if ($vars) {
				
				foreach ($vars as $key => $value) {
					
					// CUSTOM PREFIX
					
					if (preg_match("/^usp_error_". preg_quote($custom_prefix) ."([0-9a-z_-]+)?$/i", $key, $match)) {
						
						if ($custom_prefix . $match[1] === $field) {
							
							$error = 'usp-error-field usp-error-custom-prefix ';
							
						}
						
					} elseif (preg_match("/^usp_error_8([a-z])?--". preg_quote($custom_prefix) ."([0-9a-z_-]+)--([0-9]+)$/i", $key, $match)) {
						
						if ($custom_prefix . $match[2] === $field) {
							
							$error = 'usp-error-field usp-error-custom-prefix usp-error-file ';
							
						}
						
					// CUSTOM CUSTOM
					
					} elseif (preg_match("/^usp_ccf_error_". preg_quote($field) ."$/i", $key)) {
						
						$error = 'usp-error-field usp-error-custom-custom ';
						
						
					} elseif (preg_match("/^usp_error_8([a-z])?--". preg_quote($field) ."--([0-9]+)$/i", $key)) {
						
						$error = 'usp-error-field usp-error-custom-custom usp-error-file ';
						
						
					// CUSTOM FIELDS
					
					} elseif (preg_match("/^usp_error_custom_([0-9a-z_-]+)$/i", $key, $match)) {
						
						if (($match[1] === $id) || ($match[1] === $field)) {
							
							$error = 'usp-error-field usp-error-custom ';
							
						}
						
					} elseif (preg_match("/^usp_error_8([a-z])?--usp_custom_file_([0-9]+)--([0-9]+)$/i", $key, $match)) {
						
						if (($match[2] === $id) || ($match[2] === $field)) {
							
							$error = 'usp-error-field usp-error-custom usp-error-file ';
							
						}
						
					// USER REGISTER
					
					} elseif (preg_match("/^usp_error_([a-g]+)$/i", $key, $match)) {
						 
						$user_fields = array('a'=>'nicename', 'b'=>'displayname', 'c'=>'nickname', 'd'=>'firstname', 'e'=>'lastname', 'f'=>'description', 'g'=>'password');
						
						foreach ($user_fields as $k => $v) {
							
							if ($v === $field && $k === $match[1]) {
								
								$error = 'usp-error-field usp-error-register ';
								
							}
							
						}
						
					// META & MISC.
					
					} elseif (preg_match("/^usp_error_(11|12|13|15|16|17)$/i", $key, $match)) {
						
						$user_fields = array('11'=>'alt', '12'=>'caption', '13'=>'desc', '15'=>'format', '16'=>'mediatitle', '17'=>'filename');
						
						foreach ($user_fields as $k => $v) {
							
							if ((strpos($field, $v) !== false) && (strpos($k, $match[1]) !== false)) {
								
								$error = 'usp-error-field usp-error-meta ';
								
							}
							
						}
						
					} 
					
				}
				
			}
			
			return apply_filters('usp_custom_field_errors', $error);
			
		}
		function usp_custom_field_unset($field_atts) {
			if (!empty($field_atts)) {
				unset(
					$field_atts['field'], 
					$field_atts['accept'], 
					$field_atts['name'], 
					$field_atts['checked'], 
					$field_atts['selected'], 
					$field_atts['class'], 
					$field_atts['label_class'], 
					$field_atts['rows'], 
					$field_atts['cols'],
					$field_atts['for'], 
					$field_atts['label_custom'], 
					$field_atts['label'], 
					$field_atts['field_class'],
					$field_atts['options'],
					$field_atts['option_default'], 
					$field_atts['option_select'],
					$field_atts['checkboxes'],
					$field_atts['checkboxes_checked'],
					$field_atts['checkboxes_req'],
					$field_atts['radio_inputs'],
					$field_atts['radio_checked'],
					$field_atts['fieldset'],
					$field_atts['types'],
					$field_atts['method'],
					$field_atts['link'],
					$field_atts['files_min'],
					$field_atts['files_max'],
					$field_atts['multiple'],
					$field_atts['preview_off'],
					$field_atts['max'],
					$field_atts['desc']
				);
			}
			return apply_filters('usp_custom_field_unset', $field_atts);
		}
		function usp_custom_fields_select($field_atts) {
			$options = '';
			if (!empty($field_atts['options']) && $field_atts['field'] == 'select') {
				$options_array = explode(":", $field_atts['options']);
				foreach ($options_array as $option) {
					$option = trim($option);
					$option_value = strtolower(trim(str_replace(' ', '_', $option)));
					$option_value = apply_filters('usp_custom_fields_select_value', $option_value);
					
					$selected = false;
					$option_selected = '';
					if (isset($field_atts['option_select']) && strtolower($option) == strtolower($field_atts['option_select'])) $selected = true;
					if (isset($field_atts['value'])) {
						$value = $field_atts['value'];
						if (is_array($value)) {
							foreach ($value as $att) {
								if ($att == $option_value) $selected = true;
							}
						} else {
							if ($value == $option_value) $selected = true;
						}
					}
					if ($selected) $option_selected = ' selected="selected"';
					if ($option == 'null' && isset($field_atts['option_default'])) {
						$option = $field_atts['option_default'];
						$option_value = '';
					}
					$options .= '<option value="'. $option_value .'"'. $option_selected .'>'. $option .'</option>' . "\n";
				}
				$options = "\n" . $options;
			}
			return apply_filters('usp_custom_fields_select', $options);
		}
		function usp_custom_fields_checkboxes($id, $field_atts, $prefix, $select_array) {
			
			$checkboxes = ''; 
			
			if ($field_atts['field'] !== 'input_checkbox') return $checkboxes;
			
			$checkboxes_array = !empty($field_atts['checkboxes'])         ? explode(":", $field_atts['checkboxes'])         : array();
			$required_single  = !empty($field_atts['checkboxes_req'])     ? explode(":", $field_atts['checkboxes_req'])     : array();
			$checked_array    = !empty($field_atts['checkboxes_checked']) ? explode(":", $field_atts['checkboxes_checked']) : array();
			
			$desc = (isset($field_atts['desc']) && !empty($field_atts['desc'])) ? "\n" .'<div class="usp-label">'. $field_atts['desc'] .'</div>' : '';
			
			$required = array();
			
			foreach ($checkboxes_array as $checkbox) {
				
				$checkbox_value = strtolower(trim(str_replace(' ', '_', trim($checkbox))));
				$checkbox_value = apply_filters('usp_custom_fields_checkbox_value', $checkbox_value);
				
				if (!empty($select_array)) {
					
					$name = (!empty($field_atts['name'])) ? esc_attr($field_atts['name']) : esc_html__('undefined', 'usp-pro');
					$suffix = '['. $checkbox_value .']';
					
				} else {
					
					$name = $checkbox_value;
					$suffix = '';
					
				}
				
				$check = false; 
				
				if (!empty($checked_array)) {
					
					$checked_array = array_map('strtolower', $checked_array);
					if (in_array(strtolower($checkbox), $checked_array)) $check = true;
					
				}
				
				$checked = ($check) ? ' checked="checked"' : '';
				
				$req_att = '';
				
				if (!empty($required_single)) {
					
					$required_single = array_map('strtolower', $required_single);
					
					if (in_array(strtolower($checkbox), $required_single)) {
						
						$required[] = '<input type="hidden" name="'. $prefix . $name .'-required" value="1" />' . "\n";
						$req_att = ' required="required"';
					}
				}
				
				$checkboxes .= '<label for="usp-checkbox-'. $checkbox_value .'-'. $id .'">';
				$checkboxes .= '<input name="'. $prefix . $name . $suffix .'" ';
				$checkboxes .= 'id="usp-checkbox-'. $checkbox_value .'-'. $id .'" type="checkbox" ';
				$checkboxes .= 'value="'. $checkbox_value .'"'. $req_att . $checked .' /> '. $checkbox;
				$checkboxes .= '</label>'. "\n";
				
			}
			
			$checkboxes = $desc . "\n" . $checkboxes;
			foreach ($required as $require) $checkboxes .= $require;
			
			return apply_filters('usp_custom_fields_checkbox', $checkboxes);
			
		}
		function usp_custom_fields_radio($field_atts, $prefix) {
			$radios = '';
			if ($field_atts['field'] == 'input_radio') {
				$checked = array();
				$radio_array = array();
				if (isset($field_atts['radio_checked']) && !empty($field_atts['radio_checked'])) $radio_checked = strtolower(trim(str_replace(' ', '_', $field_atts['radio_checked'])));
				if (isset($field_atts['radio_inputs'])  && !empty($field_atts['radio_inputs']))  $radio_array   = explode(":", $field_atts['radio_inputs']);
				
				$desc = (isset($field_atts['desc']) && !empty($field_atts['desc'])) ? "\n" .'<div class="usp-label">'. $field_atts['desc'] .'</div>' : '';
				
				foreach ($radio_array as $radio) {
					$radio = trim($radio);
					
					$radio_value = strtolower(trim(str_replace(' ', '_', $radio)));
					$radio_value = apply_filters('usp_custom_fields_radio_value', $radio_value);
					
					if (!empty($field_atts['name'])) $name = $field_atts['name'];
					else $name = esc_html__('undefined', 'usp-pro');
					
					$checked = '';
					if (!empty($field_atts['value'])) {
						if ($radio_value == strtolower($field_atts['value'])) $checked = ' checked="checked"';
						
					} elseif (!empty($radio_checked)) {
						if ($radio_value == $radio_checked) $checked = ' checked="checked"';
					}
					$radios .= '<label for="usp-radio-'. $radio_value .'">';
					$radios .= '<input name="'. $prefix . $name .'" id="usp-radio-'. $radio_value .'" type="radio" value="'. $radio_value .'"'. $checked .' /> '. $radio;
					$radios .= '</label>' . "\n";
				}
				$radios = $desc . "\n" . $radios;
			}
			return apply_filters('usp_custom_fields_radio', $radios);
		}
		function usp_custom_fields_files($field_atts, $prefix, $class_label, $label_custom, $error) {
			$files = array();
			if ($field_atts['field'] == 'input_file') {
				
				$name     = $field_atts['name'];
				$link     = $field_atts['link'];
				$label    = $field_atts['label'];
				$method   = $field_atts['method'];
				$multiple = $field_atts['multiple'];
				
				if ($prefix == 'usp-custom-') $prefix = 'usp_custom_file_';
				
				if (!empty($field_atts['class'])) $class = $field_atts['class'] .' ';
				else $class = '';
				
				if (!empty($field_atts['max'])) $max = ' maxlength="'. $field_atts['max'] .'"';
				else $max = '';
				
				if (!empty($field_atts['accept'])) $accept = ' accept="'. $field_atts['accept'] .'"';
				else $accept = '';
				
				if (!empty($field_atts['files_max'])) $files_max = "\n" .'<input type="hidden" name="'. $prefix . $name .'-limit" class="usp-file-limit" value="'. $field_atts['files_max'] .'" />';
				else $files_max = '';
				
				if (!empty($field_atts['types'])) $files_type = "\n" .'<input type="hidden" name="'. $prefix . $name .'-types" value="'. $field_atts['types'] .'" />';
				else $files_type = '';
				
				if (!empty($field_atts['preview_off'])) $preview = "\n" .'<input type="hidden" name="'. $prefix . $name .'-preview" value="1" />';
				else $preview = '';
				
				$for = !is_numeric($field_atts['for']) ? $prefix . $field_atts['for'] : $prefix . $field_atts['name'];
				
				$files_min = '';
				if ($field_atts['data-required'] == 'true') {
					if (empty($field_atts['files_min']) && intval($field_atts['files_min']) < 1) $files_min = '1';
					else $files_min = $field_atts['files_min'];
					$files_min = "\n" .'<input type="hidden" name="'. $prefix . $name .'-required" value="'. $files_min .'" />';
				}
				
				$files_count = "\n" .'<input type="hidden" name="'. $prefix . $name .'-count" class="usp-file-count" value="1" />';
				
				if (empty($method)) {
					$input_id = '';
					$data_file = ' data-file="1"';
					$class_method = ' add-another';
					$add_another = "\n" .'<div class="usp-add-another"><a href="#">'. $link .'</a></div>';
					$is_multiple = false;
				} else {
					$input_id = ' id="'. $prefix . $name .'-multiple-files"';
					$data_file = '';
					$class_method = ' select-file';
					$add_another = '';
					$is_multiple = true;
				}
				
				$multiple_enable = array('multiple', 'true', 'yes', 'on');
				if (empty($multiple) || in_array($multiple, $multiple_enable)) {
					
					$class_label = ' class="'. $class_label    .'usp-label usp-label-files usp-label-custom"';
					$class_input = ' class="'. $class . $error .'usp-input usp-input-files usp-input-custom'. $class_method .' multiple"';
					
					$input_wrap_open = "\n" .'<div class="usp-input-wrap">';
					$input_wrap_close = "\n" .'</div>';
					$select = '[]';
					
					if ($is_multiple) $multiple_att = ' multiple="multiple"';
					else              $multiple_att = '';
				} else {
					$class_label = ' class="'. $class_label    .'usp-label usp-label-files usp-label-custom usp-label-file usp-label-file-single"';
					$class_input = ' class="'. $class . $error .'usp-input usp-input-files usp-input-custom usp-input-file usp-input-file-single'. $class_method .' single-file"';
					
					$input_wrap_open = '';
					$input_wrap_close = '';
					$select = '';
					
					$multiple_att = '';
					if (!$is_multiple) {
						$add_another = '';
						$data_file = '';
					}
					$input_id = '';
					$files_max = '';
					$files_count = '';	
				}
				
				$files['start']  = "\n" .'<label for="'. $for .'"'. $class_label . $label_custom .'>'. $label .'</label>';
				$files['start'] .= $input_wrap_open . "\n" .'<input name="'. $prefix . $name . $select .'" id="'. $prefix . $name .'" ';
				$files['start'] .= 'type="file"'. $class_input . $input_id . $multiple_att . $accept . $max . $data_file .' ';
				
				$files['end'] = '/>'. $add_another . $input_wrap_close . $files_max . $files_count . $files_type . $files_min . $preview . "\n";
				
				$files['id'] = $prefix . $name;
			}
			return apply_filters('usp_custom_fields_files', $files);
		}
	}
}
