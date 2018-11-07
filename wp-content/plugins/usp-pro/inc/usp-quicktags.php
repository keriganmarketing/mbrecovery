<?php
/*
	USP Pro - Quicktags
	QTags.addButton(id, display, arg1, arg2, access_key, title, priority, instance);
	@ http://codex.wordpress.org/Quicktags_API
*/

if (!defined('ABSPATH')) die();

global $post, $usp_general, $usp_uploads; 

$usp_pro_tags = '';
if (isset($usp_general['tags'])) foreach ($usp_general['tags'] as $tag) { $usp_pro_tags .= $tag .', '; }
$usp_pro_tags = rtrim(trim($usp_pro_tags), ',');

$usp_pro_captcha = isset($usp_general['captcha_question']) ? $usp_general['captcha_question'] : '';

$usp_pro_cats = '';
if (isset($usp_general['categories'])) foreach ($usp_general['categories'] as $cat) { $usp_pro_cats .= $cat .', '; }
$usp_pro_cats = rtrim(trim($usp_pro_cats), ',');

$usp_pro_types = isset($usp_uploads['files_allow']) ? $usp_uploads['files_allow'] : '';

?>
<script type="text/javascript">
	vex.defaultOptions.className = 'vex-theme-os';
	
	// fieldset
	QTags.addButton('usp_fieldset', 'USP:Fieldset', usp_fieldset_prompt, '', '', '', 1);
	function usp_fieldset_prompt(e, c, ed) {
		var t = this;
		if (ed.canvas.selectionStart !== ed.canvas.selectionEnd) {
			t.tagStart = '[usp_fieldset]';
			t.tagEnd   = '[#usp_fieldset]';
			
		} else if (ed.openTags) {
			var ret = false, i = 0, t = this;
			while (i < ed.openTags.length) {
				ret = ed.openTags[i] == t.id ? i : false;
				i ++;
			}
			if (ret === false) {
				t.tagStart = '[usp_fieldset]';
				t.tagEnd = false;
				if (!ed.openTags) ed.openTags = [];
				ed.openTags.push(t.id);
				e.value = '#'+ e.value;
			} else {
				ed.openTags.splice(ret, 1);
				t.tagStart = '[#usp_fieldset]';
				e.value = t.display;
			}
		} else {
			t.tagStart = '[usp_fieldset]';
			t.tagEnd = false;
			if (!ed.openTags) ed.openTags = [];
			ed.openTags.push(t.id);
			e.value = '#'+ e.value;
		}
		QTags.TagButton.prototype.callback.call(t, e, c, ed);
	}
	
	// name input
	QTags.addButton('usp_name', 'USP:Name', usp_name_prompt, '', '', '', 2);
	function usp_name_prompt(e, c, ed) {
		t = this;
		vex.dialog.open({
			message:'<h3>Attributes for the Name field</h3><p>All attributes optional, leave blank to use the default value.</p>',
			input:	'<p class="inline-blocks"><label for="label">Label</label><input type="text" value="" name="label" id="label" placeholder="Your Name"></p>' + 
					'<p class="inline-blocks"><label for="place">Placeholder</label><input type="text" value="" name="place" id="place" placeholder="Your Name"></p>' + 
					'<p class="inline-blocks"><label for="class">CSS classes <span>(comma-separated)</span></label><input type="text" value="" name="class" id="class" placeholder="example-class"></p>' + 
					'<p class="inline-blocks"><label for="maximum">Maximum number of characters</label><input type="number" min="0" step="1" value="" name="maximum" id="maximum" placeholder="Unlimited"></p>' + 
					
					'<p class="radio-heading">Require this field?</p>' + 
					'<div class="radio-blocks"><div><input type="radio" value="true" name="required" id="req-1" checked="checked"><label for="req-1">Yes</label></div>' + 
					'<div><input type="radio" value="false" name="required" id="req-2"><label for="req-2">No</label></div></div>',
					//
			callback: function(data) {
				if (data === false) {
					return false;
				} else {
					label_att = ''; place_att = ''; class_att = ''; maximum_att = ''; required_att = '';
					
					if (data.label)    label_att    = ' label="'      + data.label    +'"';
					if (data.place)    place_att    = ' placeholder="'+ data.place    +'"';
					if (data.class)    class_att    = ' class="'      + data.class    +'"';
					if (data.maximum)  maximum_att  = ' max="'        + data.maximum  +'"';
					if (data.required) required_att = ' required="'   + data.required +'"';
					
					t.tagEnd = false;
					t.tagStart = '[usp_name'+ label_att + place_att + class_att + maximum_att + required_att +']';
					QTags.TagButton.prototype.callback.call(t, e, c, ed);
				}
			}
		});
	}
	
	// url input
	QTags.addButton('usp_url', 'USP:URL', usp_url_prompt, '', '', '', 3);
	function usp_url_prompt(e, c, ed) {
		t = this;
		vex.dialog.open({
			message:'<h3>Attributes for the URL field</h3><p>All attributes optional, leave blank to use the default value.</p>',
			input:	'<p class="inline-blocks"><label for="label">Label</label><input type="text" value="" name="label" id="label" placeholder="Post URL"></p>' + 
					'<p class="inline-blocks"><label for="place">Placeholder</label><input type="text" value="" name="place" id="place" placeholder="Post URL"></p>' + 
					'<p class="inline-blocks"><label for="class">CSS classes <span>(comma-separated)</span></label><input type="text" value="" name="class" id="class" placeholder="example-class"></p>' + 
					'<p class="inline-blocks"><label for="maximum">Maximum number of characters</label><input type="number" min="0" step="1" value="" name="maximum" id="maximum" placeholder="Unlimited"></p>' + 
					
					'<p class="radio-heading">Require this field?</p>' + 
					'<div class="radio-blocks"><div><input type="radio" value="true" name="required" id="req-1" checked="checked"><label for="req-1">Yes</label></div>' + 
					'<div><input type="radio" value="false" name="required" id="req-2"><label for="req-2">No</label></div></div>',
					//
			callback: function(data) {
				if (data === false) {
					return false;
				} else {
					label_att = ''; place_att = ''; class_att = ''; maximum_att = ''; required_att = '';
					
					if (data.label)    label_att    = ' label="'      + data.label    +'"';
					if (data.place)    place_att    = ' placeholder="'+ data.place    +'"';
					if (data.class)    class_att    = ' class="'      + data.class    +'"';
					if (data.maximum)  maximum_att  = ' max="'        + data.maximum  +'"';
					if (data.required) required_att = ' required="'   + data.required +'"';
					
					t.tagEnd = false;
					t.tagStart = '[usp_url'+ label_att + place_att + class_att + maximum_att + required_att +']';
					QTags.TagButton.prototype.callback.call(t, e, c, ed);
				}
			}
		});
	}
	
	// title input
	QTags.addButton('usp_title', 'USP:Title', usp_title_prompt, '', '', '', 4);
	function usp_title_prompt(e, c, ed) {
		t = this;
		vex.dialog.open({
			message:'<h3>Attributes for the Title field</h3><p>All attributes optional, leave blank to use the default value.</p>',
			input:	'<p class="inline-blocks"><label for="label">Label</label><input type="text" value="" name="label" id="label" placeholder="Post Title"></p>' + 
					'<p class="inline-blocks"><label for="place">Placeholder</label><input type="text" value="" name="place" id="place" placeholder="Post Title"></p>' + 
					'<p class="inline-blocks"><label for="class">CSS classes <span>(comma-separated)</span></label><input type="text" value="" name="class" id="class" placeholder="example-class"></p>' + 
					'<p class="inline-blocks"><label for="maximum">Maximum number of characters</label><input type="number" min="0" step="1" value="" name="maximum" id="maximum" placeholder="Unlimited"></p>' + 
					
					'<p class="radio-heading">Require this field?</p>' + 
					'<div class="radio-blocks"><div><input type="radio" value="true" name="required" id="req-1" checked="checked"><label for="req-1">Yes</label></div>' + 
					'<div><input type="radio" value="false" name="required" id="req-2"><label for="req-2">No</label></div></div>',
					//
			callback: function(data) {
				if (data === false) {
					return false;
				} else {
					label_att = ''; place_att = ''; class_att = ''; maximum_att = ''; required_att = '';
					
					if (data.label)    label_att    = ' label="'      + data.label    +'"';
					if (data.place)    place_att    = ' placeholder="'+ data.place    +'"';
					if (data.class)    class_att    = ' class="'      + data.class    +'"';
					if (data.maximum)  maximum_att  = ' max="'        + data.maximum  +'"';
					if (data.required) required_att = ' required="'   + data.required +'"';
					
					t.tagEnd = false;
					t.tagStart = '[usp_title'+ label_att + place_att + class_att + maximum_att + required_att +']';
					QTags.TagButton.prototype.callback.call(t, e, c, ed);
				}
			}
		});
	}
	
	// tags input
	QTags.addButton('usp_tags', 'USP:Tags', usp_tags_prompt, '', '', '', 5);
	function usp_tags_prompt(e, c, ed) {
		t = this;
		vex.dialog.open({
			message:'<h3>Attributes for the Tags field</h3><p>All attributes optional, leave blank to use the default value.</p>',
			input:	'<p class="inline-blocks"><label for="label">Label</label><input type="text" value="" name="label" id="label" placeholder="Post Tags"></p>' + 
					'<p class="inline-blocks"><label for="class">CSS classes <span>(comma-separated)</span></label><input type="text" value="" name="class" id="class" placeholder="Post Tags"></p>' + 
					'<p class="inline-blocks"><label for="include">Tag IDs to include <span>(comma-separated, or use "all" for all tags)</span></label><input type="text" value="" name="include" id="include" placeholder="<?php echo $usp_pro_tags; ?>"></p>' + 
					'<p class="inline-blocks"><label for="exclude">Tag IDs to exclude <span>(comma-separated, when using "all" for previous setting)</span></label><input type="text" value="" name="exclude" id="exclude" placeholder=""></p>' + 
					
					'<p class="radio-heading">Type of field?</p>' + 
					'<div class="radio-blocks"><div><input type="radio" value="dropdown" name="type" id="type-1" checked="checked"><label for="type-1">Dropdown</label></div>' + 
					'<div><input type="radio" value="checkbox" name="type" id="type-2"><label for="type-2">Checkboxes</label></div>' + 
					'<div><input type="radio" value="input" name="type" id="type-3"><label for="type-3">Text input</label></div></div>' + 
					
					'<p class="radio-heading">Allow users to select multiple options?</p>' + 
					'<div class="radio-blocks"><div><input type="radio" value="true" name="multiple" id="multiple-1" checked="checked"><label for="multiple-1">Yes</label></div>' + 
					'<div><input type="radio" value="false" name="multiple" id="multiple-2"><label for="multiple-2">No</label></div></div>' + 
					
					'<p class="radio-heading">Require this field?</p>' + 
					'<div class="radio-blocks"><div><input type="radio" value="true" name="required" id="req-1" checked="checked"><label for="req-1">Yes</label></div>' + 
					'<div><input type="radio" value="false" name="required" id="req-2"><label for="req-2">No</label></div></div>',
					//
			callback: function(data) {
				if (data === false) {
					return false;
				} else {
					label_att = ''; class_att = ''; include_att = ''; exclude_att = ''; type_att = ''; multiple_att =''; required_att = '';
					
					if (data.label)    label_att    = ' label="'      + data.label    +'"';
					if (data.class)    class_att    = ' class="'      + data.class    +'"';
					if (data.include)  include_att  = ' include="'    + data.include  +'"';
					if (data.exclude)  exclude_att  = ' exclude="'    + data.exclude  +'"';
					if (data.type)     type_att     = ' type="'       + data.type     +'"';
					if (data.multiple) multiple_att = ' multiple="'   + data.multiple +'"';
					if (data.required) required_att = ' required="'   + data.required +'"';
					
					t.tagEnd = false;
					t.tagStart = '[usp_tags'+ label_att + class_att + include_att + exclude_att + type_att + multiple_att + required_att +']';
					QTags.TagButton.prototype.callback.call(t, e, c, ed);
				}
			}
		});
	}
	
	// captcha input
	QTags.addButton('usp_captcha', 'USP:Captcha', usp_captcha_prompt, '', '', '', 6);
	function usp_captcha_prompt(e, c, ed) {
		t = this;
		vex.dialog.open({
			message:'<h3>Attributes for the Captcha field</h3><p>All attributes optional, leave blank to use the default value.</p>',
			input:	'<p class="inline-blocks"><label for="label">Label</label><input type="text" value="" name="label" id="label" placeholder="<?php echo $usp_pro_captcha; ?>"></p>' + 
					'<p class="inline-blocks"><label for="place">Placeholder</label><input type="text" value="" name="place" id="place" placeholder="<?php echo $usp_pro_captcha; ?>"></p>' + 
					'<p class="inline-blocks"><label for="class">CSS classes <span>(comma-separated)</span></label><input type="text" value="" name="class" id="class" placeholder="example-class"></p>' + 
					'<p class="inline-blocks"><label for="maximum">Maximum number of characters</label><input type="number" min="0" step="1" value="" name="maximum" id="maximum" placeholder="Unlimited"></p>',
					//
			callback: function(data) {
				if (data === false) {
					return false;
				} else {
					label_att = ''; place_att = ''; class_att = ''; maximum_att = '';
					
					if (data.label)    label_att    = ' label="'      + data.label    +'"';
					if (data.place)    place_att    = ' placeholder="'+ data.place    +'"';
					if (data.class)    class_att    = ' class="'      + data.class    +'"';
					if (data.maximum)  maximum_att  = ' max="'        + data.maximum  +'"';
					
					t.tagEnd = false;
					t.tagStart = '[usp_captcha'+ label_att + place_att + class_att + maximum_att +']';
					QTags.TagButton.prototype.callback.call(t, e, c, ed);
				}
			}
		});
	}
	
	// category input
	QTags.addButton('usp_category', 'USP:Category', usp_category_prompt, '', '', '', 7);
	function usp_category_prompt(e, c, ed) {
		t = this;
		vex.dialog.open({
			message:'<h3>Attributes for the Category field</h3><p>All attributes optional, leave blank to use the default value.</p>',
			input:	'<p class="inline-blocks"><label for="label">Label</label><input type="text" value="" name="label" id="label" placeholder="Post Category"></p>' + 
					'<p class="inline-blocks"><label for="class">CSS classes <span>(comma-separated)</span></label><input type="text" value="" name="class" id="class" placeholder="Post Category"></p>' + 
					'<p class="inline-blocks"><label for="include">Category IDs to include <span>(comma-separated, or use "all" for all categories)</span></label><input type="text" value="" name="include" id="include" placeholder="<?php echo $usp_pro_cats; ?>"></p>' + 
					'<p class="inline-blocks"><label for="exclude">Category IDs to exclude <span>(comma-separated, when using "all" for previous setting)</span></label><input type="text" value="" name="exclude" id="exclude" placeholder=""></p>' + 
					'<p class="inline-blocks"><label for="combo">Combo ID for chained categories <span>(must be either 1, 2, or 3)</span>. Leave blank to use a single category field.</label>' + 
					'<input type="number" min="1" max="3" step="1" value="" name="combo" id="combo" placeholder=""></p>' + 
					
					'<p class="radio-heading">Type of field?</p>' + 
					'<div class="radio-blocks"><div><input type="radio" value="dropdown" name="type" id="type-1" checked="checked"><label for="type-1">Dropdown/select</label></div>' + 
					'<div><input type="radio" value="checkbox" name="type" id="type-2"><label for="type-2">Checkboxes</label></div></div>' + 
					
					'<p class="radio-heading">Allow users to select multiple options?</p>' + 
					'<div class="radio-blocks"><div><input type="radio" value="true" name="multiple" id="multiple-1" checked="checked"><label for="multiple-1">Yes</label></div>' + 
					'<div><input type="radio" value="false" name="multiple" id="multiple-2"><label for="multiple-2">No</label></div></div>' + 
					
					'<p class="radio-heading">Require this field?</p>' + 
					'<div class="radio-blocks"><div><input type="radio" value="true" name="required" id="req-1" checked="checked"><label for="req-1">Yes</label></div>' + 
					'<div><input type="radio" value="false" name="required" id="req-2"><label for="req-2">No</label></div></div>',
					//
			callback: function(data) {
				if (data === false) {
					return false;
				} else {
					label_att = ''; class_att = ''; include_att = ''; exclude_att = ''; combo_att = ''; type_att = ''; multiple_att =''; required_att = '';
					
					if (data.label)    label_att    = ' label="'      + data.label    +'"';
					if (data.class)    class_att    = ' class="'      + data.class    +'"';
					if (data.include)  include_att  = ' include="'    + data.include  +'"';
					if (data.exclude)  exclude_att  = ' exclude="'    + data.exclude  +'"';
					if (data.combo)    combo_att    = ' combo="'      + data.combo    +'"';
					if (data.type)     type_att     = ' type="'       + data.type     +'"';
					if (data.multiple) multiple_att = ' multiple="'   + data.multiple +'"';
					if (data.required) required_att = ' required="'   + data.required +'"';
					
					t.tagEnd = false;
					t.tagStart = '[usp_category'+ label_att + class_att + include_att + exclude_att + combo_att + type_att + multiple_att + required_att +']';
					QTags.TagButton.prototype.callback.call(t, e, c, ed);
				}
			}
		});
	}
	
	// content input
	QTags.addButton('usp_content', 'USP:Content', usp_content_prompt, '', '', '', 8);
	function usp_content_prompt(e, c, ed) {
		t = this;
		vex.dialog.open({
			message:'<h3>Attributes for the Content field</h3><p>All attributes optional, leave blank to use the default value.</p>',
			input:	'<p class="inline-blocks"><label for="label">Label</label><input type="text" value="" name="label" id="label" placeholder="Post Content"></p>' + 
					'<p class="inline-blocks"><label for="place">Placeholder</label><input type="text" value="" name="place" id="place" placeholder="Post Content"></p>' + 
					'<p class="inline-blocks"><label for="class">CSS classes <span>(comma-separated)</span></label><input type="text" value="" name="class" id="class" placeholder="example-class"></p>' + 
					'<p class="inline-blocks"><label for="maximum">Maximum number of characters</label><input type="number" min="0" step="1" value="" name="maximum" id="maximum" placeholder="Unlimited"></p>' + 
					
					'<p class="inline-blocks"><label for="cols">Number of columns</label><input type="number" min="1" step="1" value="" name="cols" id="cols" placeholder="30"></p>' + 
					'<p class="inline-blocks"><label for="rows">Number of rows</label><input type="number" min="1" step="1" value="" name="rows" id="rows" placeholder="3"></p>' + 
					
					'<p class="radio-heading">Enable the WP visual/richtext editor?</p>' + 
					'<div class="radio-blocks"><div><input type="radio" value="true" name="richtext" id="richtext-1"><label for="richtext-1">Yes</label></div>' + 
					'<div><input type="radio" value="false" name="richtext" id="richtext-2" checked="checked"><label for="richtext-2">No</label></div></div>' + 
					
					'<p class="radio-heading">Require this field?</p>' + 
					'<div class="radio-blocks"><div><input type="radio" value="true" name="required" id="req-1" checked="checked"><label for="req-1">Yes</label></div>' + 
					'<div><input type="radio" value="false" name="required" id="req-2"><label for="req-2">No</label></div></div>',
					//
			callback: function(data) {
				if (data === false) {
					return false;
				} else {
					label_att = ''; place_att = ''; class_att = ''; maximum_att = ''; cols_att = ''; rows_att = ''; richtext_att = ''; required_att = '';
					
					if (data.label)    label_att    = ' label="'      + data.label    +'"';
					if (data.place)    place_att    = ' placeholder="'+ data.place    +'"';
					if (data.class)    class_att    = ' class="'      + data.class    +'"';
					if (data.maximum)  maximum_att  = ' max="'        + data.maximum  +'"';
					if (data.cols)     cols_att     = ' cols="'       + data.cols     +'"';
					if (data.rows)     rows_att     = ' rows="'       + data.rows     +'"';
					if (data.richtext) richtext_att = ' richtext="'   + data.richtext +'"';
					if (data.required) required_att = ' required="'   + data.required +'"';
					
					t.tagEnd = false;
					t.tagStart = '[usp_content'+ label_att + place_att + class_att + maximum_att + cols_att + rows_att + richtext_att + required_att +']';
					QTags.TagButton.prototype.callback.call(t, e, c, ed);
				}
			}
		});
	}
	
	// excerpt input
	QTags.addButton('usp_excerpt', 'USP:Excerpt', usp_excerpt_prompt, '', '', '', 8);
	function usp_excerpt_prompt(e, c, ed) {
		t = this;
		vex.dialog.open({
			message:'<h3>Attributes for the Excerpt field</h3><p>All attributes optional, leave blank to use the default value.</p>',
			input:	'<p class="inline-blocks"><label for="label">Label</label><input type="text" value="" name="label" id="label" placeholder="Post Excerpt"></p>' + 
					'<p class="inline-blocks"><label for="place">Placeholder</label><input type="text" value="" name="place" id="place" placeholder="Post Excerpt"></p>' + 
					'<p class="inline-blocks"><label for="class">CSS classes <span>(comma-separated)</span></label><input type="text" value="" name="class" id="class" placeholder="example-class"></p>' + 
					'<p class="inline-blocks"><label for="maximum">Maximum number of characters</label><input type="number" min="0" step="1" value="" name="maximum" id="maximum" placeholder="Unlimited"></p>' + 
					
					'<p class="inline-blocks"><label for="cols">Number of columns</label><input type="number" min="1" step="1" value="" name="cols" id="cols" placeholder="30"></p>' + 
					'<p class="inline-blocks"><label for="rows">Number of rows</label><input type="number" min="1" step="1" value="" name="rows" id="rows" placeholder="3"></p>' + 
					
					'<p class="radio-heading">Enable the WP visual/richtext editor?</p>' + 
					'<div class="radio-blocks"><div><input type="radio" value="true" name="richtext" id="richtext-1"><label for="richtext-1">Yes</label></div>' + 
					'<div><input type="radio" value="false" name="richtext" id="richtext-2" checked="checked"><label for="richtext-2">No</label></div></div>' + 
					
					'<p class="radio-heading">Require this field?</p>' + 
					'<div class="radio-blocks"><div><input type="radio" value="true" name="required" id="req-1" checked="checked"><label for="req-1">Yes</label></div>' + 
					'<div><input type="radio" value="false" name="required" id="req-2"><label for="req-2">No</label></div></div>',
					//
			callback: function(data) {
				if (data === false) {
					return false;
				} else {
					label_att = ''; place_att = ''; class_att = ''; maximum_att = ''; cols_att = ''; rows_att = ''; richtext_att = ''; required_att = '';
					
					if (data.label)    label_att    = ' label="'      + data.label    +'"';
					if (data.place)    place_att    = ' placeholder="'+ data.place    +'"';
					if (data.class)    class_att    = ' class="'      + data.class    +'"';
					if (data.maximum)  maximum_att  = ' max="'        + data.maximum  +'"';
					if (data.cols)     cols_att     = ' cols="'       + data.cols     +'"';
					if (data.rows)     rows_att     = ' rows="'       + data.rows     +'"';
					if (data.richtext) richtext_att = ' richtext="'   + data.richtext +'"';
					if (data.required) required_att = ' required="'   + data.required +'"';
					
					t.tagEnd = false;
					t.tagStart = '[usp_excerpt'+ label_att + place_att + class_att + maximum_att + cols_att + rows_att + richtext_att + required_att +']';
					QTags.TagButton.prototype.callback.call(t, e, c, ed);
				}
			}
		});
	}
	
	// file input
	QTags.addButton('usp_files', 'USP:Files', usp_files_prompt, '', '', '', 9);
	function usp_files_prompt(e, c, ed) {
		t = this;
		vex.dialog.open({
			message:'<h3>Attributes for the File(s) field</h3><p>All attributes optional, leave blank to use the default value.</p>',
			input:	'<p class="inline-blocks"><label for="label">Label</label><input type="text" value="" name="label" id="label" placeholder="File(s)"></p>' + 
					'<p class="inline-blocks"><label for="place">Placeholder</label><input type="text" value="" name="place" id="place" placeholder="File(s)"></p>' + 
					'<p class="inline-blocks"><label for="class">CSS classes <span>(comma-separated)</span></label><input type="text" value="" name="class" id="class" placeholder="example-class"></p>' + 
					'<p class="inline-blocks"><label for="maximum">Maximum number of characters</label><input type="number" min="0" step="1" value="" name="maximum" id="maximum" placeholder="Unlimited"></p>' + 
					
					'<p class="inline-blocks"><label for="types">Allowed file types. Leave blank to use types specified in Uploads settings.</label>' + 
					'<input type="text" value="" name="types" id="types" placeholder="<?php echo $usp_pro_types; ?>"></p>' + 
					
					'<p class="radio-heading">Enable multiple file uploads for this field?</p>' + 
					'<div class="radio-blocks"><div><input type="radio" value="true" name="multiple" id="multiple-1" checked="checked"><label for="multiple-1">Yes</label></div>' + 
					'<div><input type="radio" value="false" name="multiple" id="multiple-2"><label for="multiple-2">No</label></div></div>' + 
					
					'<p class="radio-heading">How should multiple files be selected?</p>' + 
					'<div class="radio-blocks"><div><input type="radio" value="" name="method" id="method-1" checked="checked"><label for="method-1">via "Add another file" link</label></div>' + 
					'<div><input type="radio" value="select" name="method" id="method-2"><label for="method-2">via the "Choose file(s)" prompt</label></div></div>' + 
					
					'<p class="radio-heading">Require this field?</p>' + 
					'<div class="radio-blocks"><div><input type="radio" value="true" name="required" id="req-1" checked="checked"><label for="req-1">Yes</label></div>' + 
					'<div><input type="radio" value="false" name="required" id="req-2"><label for="req-2">No</label></div></div>',
					//
			callback: function(data) {
				if (data === false) {
					return false;
				} else {
					label_att = ''; place_att = ''; class_att = ''; maximum_att = ''; types_att = ''; multiple_att = ''; method_att = ''; required_att = '';
					
					if (data.label)    label_att    = ' label="'      + data.label    +'"';
					if (data.place)    place_att    = ' placeholder="'+ data.place    +'"';
					if (data.class)    class_att    = ' class="'      + data.class    +'"';
					if (data.maximum)  maximum_att  = ' max="'        + data.maximum  +'"';
					if (data.types)    types_att    = ' types="'      + data.types    +'"';
					
					if (data.multiple) multiple_att = ' multiple="'   + data.multiple +'"';
					if (data.method)   method_att   = ' method="'     + data.method   +'"';
					if (data.required) required_att = ' required="'   + data.required +'"';
					
					t.tagEnd = false;
					t.tagStart = '[usp_files'+ label_att + place_att + class_att + maximum_att + types_att + multiple_att + method_att + required_att +']';
					QTags.TagButton.prototype.callback.call(t, e, c, ed);
				}
			}
		});
	}
	
	// custom input
	QTags.addButton('usp_custom', 'USP:Custom', usp_custom_prompt, '', '', '', 10);
	function usp_custom_prompt(e, c, ed) {
		t = this;
		vex.dialog.open({
			message:'<h3>Attributes for Custom Field</h3>' + 
					'<p>This shortcode adds a <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-custom-fields/">Custom Field</a> to the form. ' + 
					'Enter the Custom Field ID <span>(e.g., 1)</span> from the Name field of the Custom Fields meta box. Then customize as desired by adding attributes to the Value field.' + 
					'<div><img src="<?php echo plugins_url(); ?>/usp-pro/img/usp-custom-fields.jpg" alt=""></div>',
			input:	'<p class="inline-blocks"><label for="id">Custom Field ID</label><input type="number" min="1" step="1" value="" name="id" id="id" placeholder="1"></p>',
					//
			callback: function(data) {
				if (data === false) {
					return false;
				} else {
					id_att = ' id="1"'; 
					if (data.id) id_att = ' id="'+ data.id +'"';
					
					t.tagEnd = false;
					t.tagStart = '[usp_custom_field form="<?php echo $post->ID; ?>"'+ id_att +']';
					QTags.TagButton.prototype.callback.call(t, e, c, ed);
				}
			}
		});
	}
	
	// email input
	QTags.addButton('usp_email', 'USP:Email', usp_email_prompt, '', '', '', 11);
	function usp_email_prompt(e, c, ed) {
		t = this;
		vex.dialog.open({
			message:'<h3>Attributes for the Email Address field</h3><p>All attributes optional, leave blank to use the default value.</p>',
			input:	'<p class="inline-blocks"><label for="label">Label</label><input type="text" value="" name="label" id="label" placeholder="Email Address"></p>' + 
					'<p class="inline-blocks"><label for="place">Placeholder</label><input type="text" value="" name="place" id="place" placeholder="Email Address"></p>' + 
					'<p class="inline-blocks"><label for="class">CSS classes <span>(comma-separated)</span></label><input type="text" value="" name="class" id="class" placeholder="example-class"></p>' + 
					'<p class="inline-blocks"><label for="maximum">Maximum number of characters</label><input type="number" min="0" step="1" value="" name="maximum" id="maximum" placeholder="Unlimited"></p>' + 
					
					'<p class="radio-heading">Require this field?</p>' + 
					'<div class="radio-blocks"><div><input type="radio" value="true" name="required" id="req-1" checked="checked"><label for="req-1">Yes</label></div>' + 
					'<div><input type="radio" value="false" name="required" id="req-2"><label for="req-2">No</label></div></div>',
					//
			callback: function(data) {
				if (data === false) {
					return false;
				} else {
					label_att = ''; place_att = ''; class_att = ''; maximum_att = ''; required_att = '';
					
					if (data.label)    label_att    = ' label="'      + data.label    +'"';
					if (data.place)    place_att    = ' placeholder="'+ data.place    +'"';
					if (data.class)    class_att    = ' class="'      + data.class    +'"';
					if (data.maximum)  maximum_att  = ' max="'        + data.maximum  +'"';
					if (data.required) required_att = ' required="'   + data.required +'"';
					
					t.tagEnd = false;
					t.tagStart = '[usp_email'+ label_att + place_att + class_att + maximum_att + required_att +']';
					QTags.TagButton.prototype.callback.call(t, e, c, ed);
				}
			}
		});
	}
	
	// email subject input
	QTags.addButton('usp_subject', 'USP:Subject', usp_subject_prompt, '', '', '', 12);
	function usp_subject_prompt(e, c, ed) {
		t = this;
		vex.dialog.open({
			message:'<h3>Attributes for the Email Subject field</h3><p>All attributes optional, leave blank to use the default value.</p>',
			input:	'<p class="inline-blocks"><label for="label">Label</label><input type="text" value="" name="label" id="label" placeholder="Email Subject"></p>' + 
					'<p class="inline-blocks"><label for="place">Placeholder</label><input type="text" value="" name="place" id="place" placeholder="Email Subject"></p>' + 
					'<p class="inline-blocks"><label for="class">CSS classes <span>(comma-separated)</span></label><input type="text" value="" name="class" id="class" placeholder="example-class"></p>' + 
					'<p class="inline-blocks"><label for="maximum">Maximum number of characters</label><input type="number" min="0" step="1" value="" name="maximum" id="maximum" placeholder="Unlimited"></p>' + 
					
					'<p class="radio-heading">Require this field?</p>' + 
					'<div class="radio-blocks"><div><input type="radio" value="true" name="required" id="req-1" checked="checked"><label for="req-1">Yes</label></div>' + 
					'<div><input type="radio" value="false" name="required" id="req-2"><label for="req-2">No</label></div></div>',
					//
			callback: function(data) {
				if (data === false) {
					return false;
				} else {
					label_att = ''; place_att = ''; class_att = ''; maximum_att = ''; required_att = '';
					
					if (data.label)    label_att    = ' label="'      + data.label    +'"';
					if (data.place)    place_att    = ' placeholder="'+ data.place    +'"';
					if (data.class)    class_att    = ' class="'      + data.class    +'"';
					if (data.maximum)  maximum_att  = ' max="'        + data.maximum  +'"';
					if (data.required) required_att = ' required="'   + data.required +'"';
					
					t.tagEnd = false;
					t.tagStart = '[usp_subject'+ label_att + place_att + class_att + maximum_att + required_att +']';
					QTags.TagButton.prototype.callback.call(t, e, c, ed);
				}
			}
		});
	}
	
	// submit button
	QTags.addButton('usp_submit', 'USP:Submit', user_submit_prompt, '', '', '', 13);
	function user_submit_prompt(e, c, ed) {
		t = this;
		vex.dialog.open({
			message:'<h3>Attributes for the Submit button</h3><p>All attributes optional, leave blank to use the default value.</p>',
			input:	'<p class="inline-blocks"><label for="value">Button text</label><input type="text" value="" name="value" id="value" placeholder="Submit Post"></p>' + 
					'<p class="inline-blocks"><label for="class">CSS classes <span>(comma-separated)</span></label><input type="text" value="" name="class" id="class" placeholder="example-class"></p>',
					//
			callback: function(data) {
				if (data === false) {
					return false;
				} else {
					class_att = ''; value_att = '';
					
					if (data.class) class_att = ' class="'+ data.class +'"';
					if (data.value) value_att = ' value="'+ data.value +'"';
					
					t.tagEnd = false;
					t.tagStart = '[usp_submit'+ class_att + value_att +']';
					QTags.TagButton.prototype.callback.call(t, e, c, ed);
				}
			}
		});
	}
	
	// reset button
	QTags.addButton('usp_reset_button', 'USP:Reset', user_reset_prompt, '', '', '', 14);
	function user_reset_prompt(e, c, ed) {
		t = this;
		vex.dialog.open({
			message:'<h3>Attributes for the Reset-form link</h3><p>All attributes optional, leave blank to use the default value.</p>',
			input:	'<p class="inline-blocks"><label for="value">Link text</label><input type="text" value="" name="value" id="value" placeholder="Reset form"></p>' + 
					'<p class="inline-blocks"><label for="class">CSS classes <span>(comma-separated)</span></label><input type="text" value="" name="class" id="class" placeholder="example-class"></p>' + 
					'<p class="inline-blocks"><label for="url">Form <abbr title="Uniform Resource Locator">URL</abbr> <span>(required)</span>. This should be the page on which the USP Form is displayed.</label>' + 
					'<input type="text" value="http://example.com/submit/" name="url" id="url" placeholder="http://example.com/submit/"></p>',
					//
			callback: function(data) {
				if (data === false) {
					return false;
				} else {
					class_att = ''; value_att = ''; url_att = ' url="FORM URL (required)"';
					
					if (data.class) class_att = ' class="'+ data.class +'"';
					if (data.value) value_att = ' value="'+ data.value +'"';
					if (data.url)   url_att   = ' url="'  + data.url   +'"';
					
					t.tagEnd = false;
					t.tagStart = '[usp_reset_button'+ class_att + value_att + url_att +']';
					QTags.TagButton.prototype.callback.call(t, e, c, ed);
				}
			}
		});
	}
	
	// remember button
	QTags.addButton('usp_remember', 'USP:Remember', user_remember_prompt, '', '', '', 15);
	function user_remember_prompt(e, c, ed) {
		t = this;
		vex.dialog.open({
			message:'<h3>Attributes for the Remember checkbox</h3><p>All attributes optional, leave blank to use the default value. ' + 
					'<a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-add-remember-me-checkbox/">Learn more</a></p>',
			input:	'<p class="inline-blocks"><label for="label">Label text</label><input type="text" value="" name="label" id="label" placeholder="Remember me"></p>' + 
					'<p class="inline-blocks"><label for="class">CSS classes <span>(comma-separated)</span></label><input type="text" value="" name="class" id="class" placeholder="example-class"></p>',
					//
			callback: function(data) {
				if (data === false) {
					return false;
				} else {
					class_att = ''; label_att = '';
					
					if (data.class) class_att = ' class="'+ data.class +'"';
					if (data.label) label_att = ' label="'+ data.label +'"';
					
					t.tagEnd = false;
					t.tagStart = '[usp_remember'+ class_att + label_att +']';
					QTags.TagButton.prototype.callback.call(t, e, c, ed);
				}
			}
		});
	}
	
	// cc message
	QTags.addButton('usp_cc', 'USP:CC', user_cc_prompt, '', '', '', 16);
	function user_cc_prompt(e, c, ed) {
		t = this;
		vex.dialog.open({
			message:'<h3>Attributes for <abbr title="Carbon Copy">CC</abbr> message</h3><p>All attributes optional, leave blank to use the default value.</p>',
			input:	'<p class="inline-blocks"><label for="text">CC message</label><input type="text" value="A copy of this message will be sent to the specified email address." name="text" id="text" placeholder="A copy of this message will be sent to the specified email address."></p>' + 
					'<p class="inline-blocks"><label for="class">CSS classes <span>(comma-separated)</span></label><input type="text" value="" name="class" id="class" placeholder="example-class"></p>',
					//
			callback: function(data) {
				if (data === false) {
					return false;
				} else {
					class_att = ''; text_att = '';
					
					if (data.class) class_att = ' class="'+ data.class +'"';
					if (data.text)  text_att  = ' text="' + data.text  +'"';
					
					t.tagEnd = false;
					t.tagStart = '[usp_cc'+ class_att + text_att +']';
					QTags.TagButton.prototype.callback.call(t, e, c, ed);
				}
			}
		});
	}
	
	// custom redirect
	QTags.addButton('usp_redirect', 'USP:Redirect', user_redirect_prompt, '', '', '', 17);
	function user_redirect_prompt(e, c, ed) {
		t = this;
		vex.dialog.open({
			message:'<h3>Attributes for Redirect URL</h3><p>Redirect <abbr title="Uniform Resource Locator">URL</abbr> required. CSS attributes optional, leave blank to use the default value.</p>',
			input:	'<p class="inline-blocks"><label for="class">CSS classes <span>(comma-separated)</span></label><input type="text" value="" name="class" id="class" placeholder="example-class"></p>' + 
					'<p class="inline-blocks"><label for="url">Redirect <abbr title="Uniform Resource Locator">URL</abbr> <span>(required)</span>. ' + 
					'This should be the page to which the user is redirected after submitting the form.</label>' + 
					'<input type="text" value="http://example.com/thank-you/" name="url" id="url" placeholder="http://example.com/thank-you/"></p>',
					//
			callback: function(data) {
				if (data === false) {
					return false;
				} else {
					class_att = ''; url_att = ' url="REDIRECT URL (required)"';
					
					if (data.class) class_att = ' class="'+ data.class +'"';
					if (data.url)   url_att   = ' url="'  + data.url   +'"';
					
					t.tagEnd = false;
					t.tagStart = '[usp_redirect'+ class_att + url_att +']';
					QTags.TagButton.prototype.callback.call(t, e, c, ed);
				}
			}
		});
	}
	
	// taxonomy input
	QTags.addButton('usp_taxonomy', 'USP:Taxonomy', usp_taxonomy_prompt, '', '', '', 18);
	function usp_taxonomy_prompt(e, c, ed) {
		t = this;
		vex.dialog.open({
			message:'<h3>Attributes for the Taxonomy field</h3><p>Taxonomy and Terms fields are required. All other attributes optional, leave blank to use the default value.</p>',
			input:	'<p class="inline-blocks"><label for="label">Label</label><input type="text" value="" name="label" id="label" placeholder="E.g., People"></p>' + 
					'<p class="inline-blocks"><label for="tax">Taxonomy <span>(required)</span></label><input type="text" value="" name="tax" id="tax" placeholder="people"></p>' + 
					'<p class="inline-blocks"><label for="terms">Term IDs <span>(comma-separated term ids, or use all_include_empty)</span> <span>(required)</span></label><input type="text" value="all_include_empty" name="terms" id="terms" placeholder="all_include_empty"></p>' + 
					'<p class="inline-blocks"><label for="class">CSS classes <span>(comma-separated)</span></label><input type="text" value="" name="class" id="class" placeholder="example-class"></p>' + 
					
					'<p class="radio-heading">How should this field be displayed?</p>' + 
					'<div class="radio-blocks"><div><input type="radio" value="dropdown" name="type" id="type-1" checked="checked"><label for="type-1">Dropdown/select menu</label></div>' + 
					'<div><input type="radio" value="checkbox" name="type" id="type-2"><label for="type-2">Checkbox fields</label></div></div>' + 
					
					'<p class="radio-heading">Allow users to select multiple options?</p>' + 
					'<div class="radio-blocks"><div><input type="radio" value="true" name="multiple" id="multiple-1" checked="checked"><label for="multiple-1">Yes</label></div>' + 
					'<div><input type="radio" value="false" name="multiple" id="multiple-2"><label for="multiple-2">No</label></div></div>' + 
					
					'<p class="radio-heading">Require this field?</p>' + 
					'<div class="radio-blocks"><div><input type="radio" value="true" name="required" id="req-1" checked="checked"><label for="req-1">Yes</label></div>' + 
					'<div><input type="radio" value="false" name="required" id="req-2"><label for="req-2">No</label></div></div>',
					//
			callback: function(data) {
				if (data === false) {
					return false;
				} else {
					class_att = ''; label_att = ''; tax_att = ' tax="TAXONOMY NAME (required)"'; terms_att = ' terms="TAX TERMS (required)"'; type_att = ''; multiple_att = ''; required_att = '';
					
					if (data.class)    class_att    = ' class="'    + data.class    +'"';
					if (data.label)    label_att    = ' label="'    + data.label    +'"';
					if (data.tax)      tax_att      = ' tax="'      + data.tax      +'"';
					if (data.terms)    terms_att    = ' terms="'    + data.terms    +'"';
					if (data.type)     type_att     = ' type="'     + data.type     +'"';
					if (data.multiple) multiple_att = ' multiple="' + data.multiple +'"';
					if (data.required) required_att = ' required="' + data.required +'"';
					
					t.tagEnd = false;
					t.tagStart = '[usp_taxonomy'+ class_att + label_att + tax_att + terms_att + type_att + multiple_att + required_att +']';
					QTags.TagButton.prototype.callback.call(t, e, c, ed);
				}
			}
		});
	}
	
	// agree to terms
	QTags.addButton('usp_agree', 'USP:Agree', usp_agree_prompt, '', '', '', 19);
	function usp_agree_prompt(e, c, ed) {
		t = this;
		vex.dialog.open({
			message:'<h3>Attributes for the Agree to Terms box</h3><p>All attributes optional, leave any option blank to use its default value.</p>',
			input:	'<p class="inline-blocks"><label for="label">Label for the checkbox</label><input type="text" value="" name="label" id="label" placeholder="I agree to the terms"></p>' + 
					'<p class="inline-blocks"><label for="toggle">Text for the toggle link</label><input type="text" value="" name="toggle" id="toggle" placeholder="Show/hide terms"></p>' + 
					'<p class="inline-blocks"><label for="terms">Text for the toggling terms box</label><input type="text" value="" name="terms" id="terms" placeholder="Put terms here."></p>' + 
					'<p class="inline-blocks"><label for="alert">Text for the popup alert (or leave blank to disable)</label><input type="text" value="" name="alert" id="alert" placeholder=""></p>',
					//
			callback: function(data) {
				if (data === false) {
					return false;
				} else {
					label_att = ''; toggle_att = ''; terms_att = ''; alert_att = '';
					
					if (data.label)  label_att  = ' label="'  + data.label  +'"';
					if (data.toggle) toggle_att = ' toggle="' + data.toggle +'"';
					if (data.terms)  terms_att  = ' terms="'  + data.terms  +'"';
					if (data.alert)  alert_att  = ' alert="'  + data.alert  +'"';
					
					t.tagEnd = false;
					t.tagStart = '[usp_agree'+ label_att + toggle_att + terms_att + alert_att +']';
					QTags.TagButton.prototype.callback.call(t, e, c, ed);
				}
			}
		});
	}
</script>
