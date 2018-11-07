<?php // USP Pro - Form Demos

if (!defined('ABSPATH')) die();

global $usp_advanced;

$usp_submit = (!$usp_advanced['submit_button']) ? '[usp_submit]'. "\n\n" : '';

$usp_intro_form     = 'This USP Form Demo enables visitors to submit content.';
$usp_intro_contact  = 'This Contact Form Demo enables visitors to contact you via email.';
$usp_intro_register = 'This Registration Form Demo enables visitors to register without submitting content.';
$usp_intro_images   = 'This Image-Preview Form Demo demonstrates how to display image previews with file uploads.';
$usp_intro_classic  = 'This Classic Form Demo is a replica of the form that is included with the free version of the USP plugin.';
$usp_intro_starter  = 'This Starter Form Demo includes extra information to help beginners get started with creating and customizing their own forms.';

$usp_intro_more     = ' For a more detailed demo, check out the "Starter Form".';
$usp_intro_note     = ' Note: in order to display this form via its shortcode, the form must be published.';
$usp_intro_info     = ' To learn more about the shortcodes used in this form, <a href="https://plugin-planet.com/usp-pro-shortcodes/">check out the USP Shortcode Reference</a>.';



$usp_form = '<p>'. $usp_intro_form  . $usp_intro_more . $usp_intro_info . $usp_intro_note .'</p>'. "\n\n" .
'[usp_name]
[usp_email]
[usp_url]
[usp_title]
[usp_captcha]
[usp_category]
[usp_content]
[usp_files]
[usp_remember label="Remember Form Info"]'. "\n\n" . $usp_submit;



$contact_form = '<p>'. $usp_intro_contact . $usp_intro_more . $usp_intro_info . $usp_intro_note .'</p>'. "\n\n" . 
'[usp_name]
[usp_email]
[usp_url]
[usp_subject]
[usp_content]
[usp_remember label="Remember Form Info"]'. "\n\n" . $usp_submit . '<input type="hidden" name="usp-is-contact" value="1" />';



$register_form = '<p>'. $usp_intro_register . $usp_intro_more . $usp_intro_info . $usp_intro_note .'</p>'. "\n\n" . 
'[usp_name]
[usp_url]
[usp_captcha]
[usp_email]'. "\n\n" .
'[usp_custom_field form="register" id="1"]
[usp_custom_field form="register" id="2"]
[usp_custom_field form="register" id="3"]
[usp_custom_field form="register" id="4"]
[usp_custom_field form="register" id="5"]
[usp_custom_field form="register" id="6"]
[usp_remember label="Remember Form Info"]'. "\n\n" . $usp_submit . '<input type="hidden" name="usp-is-register" value="1" />';



$image_form = '<p>'. $usp_intro_images . $usp_intro_more . $usp_intro_info . $usp_intro_note .'</p>'. "\n\n" .
'[usp_title]
[usp_content]
[usp_files multiple="yes" method="select"]
[usp_remember label="Remember Form Info"]'. "\n\n" . $usp_submit;



$classic_form = '<p>'. $usp_intro_classic . $usp_intro_more . $usp_intro_info . $usp_intro_note .'</p>'. "\n\n" .
'[usp_name]
[usp_url]
[usp_email]
[usp_title]
[usp_tags required="false"]
[usp_captcha]
[usp_category]
[usp_content]
[usp_files]
[usp_remember label="Remember Form Info"]'. "\n\n" . $usp_submit . "\n\n";



$starter_form = '<p>'. $usp_intro_starter .'</p>'. "\n\n" .
'<h2>Primary Form Fields</h2>

<p>The shortcodes used to display fields in this section are referred to as "primary fields". Most WP themes include primary fields when they display posts on the front-end. Also, in the Admin Area, primary fields each have their own special location on the Edit Post screen. It is important to understand that primary shortcodes/fields may be used only once per form. For additional fields, like multiple content textareas, you can use Custom Fields, which are explained further along in this demo.</p>



<h3>Name Field</h3>

<p>The following shortcode displays the "Name" field when this form is viewed on the front-end of your site. You can click the Preview button to see what it looks like. Notice that we are using several attributes to customize the Name field:</p>

[usp_name label="Name" placeholder="Name" max="99" required="false"]

<p>The Name shortcode may be customized with various attributes, including:</p>

<ul>
<li>label = the field label</li>
<li>placeholder = the field placeholder</li>
<li>max = the maximum number of characters allowed for the field</li>
<li>required = whether or not the field is required</li>
</ul>

<p><a href="https://plugin-planet.com/usp-pro-shortcodes/#usp-name">Documentation for the Name shortcode and its attributes</a>.</p>

<p>Note that the Name shortcode may be added to any form using the "USP:Name" Quicktag.</p>



<h3>Title Field</h3>

<p>Next up, here is the shortcode used to display the Title field:</p>

[usp_title label="Title" placeholder="Title" max="99" required="true"]

<p>Just like with the Name shortcode, the Title shortcode may be customized with various attributes:</p>

<ul>
<li>label = the field label</li>
<li>placeholder = the field placeholder</li>
<li>max = the maximum number of characters allowed for the field</li>
<li>required = whether or not the field is required</li>
</ul>

<p><a href="https://plugin-planet.com/usp-pro-shortcodes/#usp-title">Documentation for the Title shortcode and its attributes</a>.</p>

<p>Note that the Title shortcode may be added to any form using the "USP:Title" Quicktag.</p>



<h3>Tags Field</h3>

<p>Here is the shortcode used to display the Tags field:</p>

[usp_tags label="Tags" type="dropdown" multiple="false" required="false" include="all_include_empty"]

<p>Here are the attributes we are using to customize the Tags field:</p>

<ul>
<li>label = the field label</li>
<li>type = the type of field is specified as a dropdown menu</li>
<li>multiple = do not allow the user to select multiple tags</li>
<li>required = whether or not the field is required</li>
<li>include = which tags to include (overrides general setting)</li>
</ul>

<p>Note that if no tags are displayed in the Tags field, you may need to create some tags via Posts &gt; Tags. Once your site has some tags to display, you can enable them via the USP General Settings &gt; Post Tags. You can also use the "include" attribute to specify which tags should be included for each form.</p> 

<p><a href="https://plugin-planet.com/usp-pro-shortcodes/#usp-tags">Documentation for the Tags shortcode and its attributes</a>.</p>

<p>Note that the Tags shortcode may be added to any form using the "USP:Tags" Quicktag.</p>



<h3>Category Field</h3>

<p>Here is the shortcode used to display the Category field:</p>

[usp_category label="Categories" type="dropdown" multiple="false" required="false" include="all_include_empty"]

<p>Here are the attributes we are using to customize the Category field:</p>

<ul>
<li>label = the field label</li>
<li>type = the type of field is specified as a dropdown menu</li>
<li>multiple = do not allow the user to select multiple categories</li>
<li>required = whether or not the field is required</li>
<li>include = which cats to include (overrides general setting)</li>
</ul>

<p>Note that if no categories are displayed in the Category field, you may need to create some categories via Posts &gt; Categories. Once your site has some categories to display, you can enable them via the USP General Settings &gt; Post Categories. You can also use the "include" attribute to specify which categories should be included for each form.</p> 

<p><a href="https://plugin-planet.com/usp-pro-shortcodes/#usp-category">Documentation for the Category shortcode and its attributes</a>.</p>

<p>Note that the Category shortcode may be added to any form using the "USP:Category" Quicktag.</p>



<h3>Content Field</h3>

<p>Here is the shortcode used to display the Content field:</p>

[usp_content label="Content" placeholder="Content" max="99" richtext="false" required="true"]

<p>Here are the attributes we are using to customize the Content field:</p>

<ul>
<li>label = the field label</li>
<li>placeholder = the field placeholder</li>
<li>max = the maximum number of characters allowed for the field</li>
<li>richtext = indicates that the field should use the WP Rich Text Editor</li>
<li>required = whether or not the field is required</li>
</ul>

<p>Remember, only one primary content field may be used per form. So if you want to add more content fields, use a Custom Field, as explained further along in the demo.</p>

<p><a href="https://plugin-planet.com/usp-pro-shortcodes/#usp-content">Documentation for the Content shortcode and its attributes</a>.</p>

<p>Note that the Content shortcode may be added to any form using the "USP:Content" Quicktag.</p>



<h3>Excerpt Field</h3>

<p>Here is the shortcode used to display the Excerpt field:</p>

[usp_excerpt label="Excerpt" placeholder="Excerpt" max="99" richtext="false" required="true"]

<p>Here are the attributes we are using to customize the Excerpt field:</p>

<ul>
<li>label = the field label</li>
<li>placeholder = the field placeholder</li>
<li>max = the maximum number of characters allowed for the field</li>
<li>richtext = indicates that the field should use the WP Rich Text Editor</li>
<li>required = whether or not the field is required</li>
</ul>

<p>Remember, only one primary excerpt field may be used per form. So if you want to add more excerpt fields, use a Custom Field, as explained further along in the demo.</p>

<p><a href="https://plugin-planet.com/usp-pro-shortcodes/#usp-excerpt">Documentation for the Excerpt shortcode and its attributes</a>.</p>

<p>Note that the Excerpt shortcode may be added to any form using the "USP:Excerpt" Quicktag.</p>



<h2>Secondary Fields</h2>

<p>The shortcodes provided in this section display secondary fields. Secondary fields are used to collect data such as URL, Email, Files, Taxonomy, and more. Secondary fields actually are Custom Fields that have their own Quicktags to make them easier to add to forms. If your form includes any secondary fields, they may be viewed in the Custom Fields panel located on the "Edit Post" screen for each submitted post. As with primary fields, secondary fields may be included only once per form.</p>



<h3>URL Field</h3>

<p>Here is the shortcode used to display the URL field:</p>

[usp_url label="URL" placeholder="URL" required="false"]

<p>Here are the attributes we are using to customize the URL field:</p>

<ul>
<li>label = the field label</li>
<li>placeholder = the field placeholder</li>
<li>required = whether or not the field is required</li>
</ul>

<p><a href="https://plugin-planet.com/usp-pro-shortcodes/#usp-url">Documentation for the URL shortcode and its attributes</a>.</p>

<p>Note that the URL shortcode may be added to any form using the "USP:URL" Quicktag.</p>



<h3>Email Field</h3>

<p>Here is the shortcode used to display the Email field:</p>

[usp_email label="Email" placeholder="Email" max="99" required="false"]

<p>Here are the attributes we are using to customize the Email field:</p>

<ul>
<li>label = the field label</li>
<li>placeholder = the field placeholder</li>
<li>max = the maximum number of characters allowed for the field</li>
<li>required = whether or not the field is required</li>
</ul>

<p><a href="https://plugin-planet.com/usp-pro-shortcodes/#usp-email">Documentation for the Email shortcode and its attributes</a>.</p>

<p>Note that the Email shortcode may be added to any form using the "USP:Email" Quicktag.</p>



<h3>Subject Field</h3>

<p>When included in a Contact Form, the Subject field is used as the email subject. Here is the shortcode:</p>

[usp_subject label="Subject" placeholder="Subject" max="99" required="false"]

<p>Here are the attributes we are using to customize the Subject field:</p>

<ul>
<li>label = the field label</li>
<li>placeholder = the field placeholder</li>
<li>max = the maximum number of characters allowed for the field</li>
<li>required = whether or not the field is required</li>
</ul>

<p><a href="https://plugin-planet.com/usp-pro-shortcodes/#usp-subject">Documentation for the Subject shortcode and its attributes</a>.</p>

<p>Note that the Subject shortcode may be added to any form using the "USP:Subject" Quicktag.</p>



<h3>Taxonomy Field</h3>

<p>If your theme supports any Custom Taxonomies, you can use this shortcode to include a Taxonomy field in your form:</p>

[usp_taxonomy label="People" tax="people" terms="all_include_empty" type="dropdown" multiple="true" required="false"]

<p>Here are the attributes we are using to customize the Taxonomy field:</p>

<ul>
<li>label = the field label</li>
<li>tax = the name of the taxonomy</li>
<li>terms = the term IDs to be included in the field</li>
<li>type = the type of field is specified as a dropdown menu</li>
<li>multiple = allow the user to select multiple categories</li>
<li>required = whether or not the field is required</li>
</ul>

<p>The trick for this tag is making sure to include the taxonomy name for the "tax" attribute, and the taxonomy term IDs in the "terms" attribute. So, if the previous shortcode/field is displaying "No terms found for people", it means that the "people" taxonomy is not supported by your theme. Tip: to include all tax terms automatically, use "all" as the value for "terms" (e.g., terms="all"). Check out the Taxonomy Shortcode reference for more useful attributes and tricks.</p>

<p><a href="https://plugin-planet.com/usp-pro-shortcodes/#custom-taxonomy">Documentation for the Taxonomy shortcode and its attributes</a>.</p>

<p>Note that the Taxonomy shortcode may be added to any form using the "USP:Taxonomy" Quicktag.</p>



<h3>Files Field</h3>

<p>The Files shortcode displays a Files field that enables the user to upload files. Here is the shortcode:</p>

[usp_files label="Files" placeholder="Files" types="gif,jpg,png" multiple="true" method="select" required="false"]

<p>Here are the attributes we are using to customize the Files field:</p>

<ul>
<li>label = the field label</li>
<li>placeholder = the field placeholder</li>
<li>types = allow only gif, jpg, and png file types</li>
<li>multiple = whether to allow user to select multiple files</li>
<li>method = method of selecting multiple files</li>
<li>required = whether or not the field is required</li>
</ul>

<p>For the allowed file types, you can <a href="https://plugin-planet.com/usp-pro-allowed-file-types/">specify global and local types</a>. To customize other global options, you can visit the USP Uploads settings. Then to override the global settings for each form , you can use attributes such as "files_min", "files_max", and many others. Also, each form may include only one Files shortcode, but you can <a href="https://plugin-planet.com/usp-pro-multiple-file-upload-fields/">use Custom Fields to add multiple Files/upload fields</a>.</p>

<p><a href="https://plugin-planet.com/usp-pro-shortcodes/#usp-files">Documentation for the Files shortcode and its attributes</a>.</p>

<p>Note that the Files shortcode may be added to any form using the "USP:Files" Quicktag.</p>



<h3>Captcha</h3>

<p>To protect the form from spammers, we add a captcha field. Here is the shortcode:</p>

[usp_captcha]

<p>Note that the label and placeholder are determined automatically, based on the value of the plugin option, "Antispam/Captcha", which is located under the General settings tab.</p>

<p>Instead of using the default challenge question/answer for the captcha, we can <a href="https://plugin-planet.com/usp-pro-add-google-recaptcha/">enable Google reCaptcha</a>.</p>

<p><a href="https://plugin-planet.com/usp-pro-shortcodes/#usp-captcha">Documentation for the Captcha shortcode and its attributes</a>.</p>

<p>Note that the Agree to Terms shortcode may be added to any form using the "USP:Captcha" Quicktag.</p>



<h3>Agree to Terms</h3>

<p>The "Agree to Terms" field actually is a checkbox field that is associated with a toggling "terms" box. To include it, add the following shortcode:</p>

[usp_agree label="Agree to Terms" toggle="Toggle Terms" terms="Put your terms here.." alert=""]

<p>Here are the attributes we are using to customize the Agree field:</p>

<ul>
<li>label = the field label</li>
<li>toggle = the text used for the toggle link</li>
<li>terms = the terms displayed in the toggle box</li>
<li>alert = the text displayed in the popup alert (leave blank to disable)</li>
</ul>

<p>For more attributes, <a href="https://plugin-planet.com/usp-pro-agree-to-terms-box/">check out the "Add an Agree to Terms Box" tutorial</a> at Plugin Planet. Note that the USP Pro JavaScript must be enabled for this shortcode to work.</p>

<p><a href="https://plugin-planet.com/usp-pro-shortcodes/#agree-terms">Documentation for the Agree to Terms shortcode and its attributes</a>.</p>

<p>Note that the Agree to Terms shortcode may be added to any form using the "USP:Agree" Quicktag.</p>



<h2>Custom Fields</h2>

<p>In USP Pro, <a href="https://plugin-planet.com/usp-pro-custom-fields/">Custom Fields</a> are used to add virtually any type of fields to USP Forms. We have seen several Custom Fields already in this Starter Form, including the URL, Email, and Subject fields. Custom Fields may be used to add other types of fields, such as textareas, select fields, radio fields, checkboxes, and text fields. Here is a <a href="https://plugin-planet.com/usp-pro-shortcodes/#custom-fields">list of all attributes for the Custom Field shortcode</a>.</p>



<h3>Checkboxes</h3>

<p>To add a custom checkbox field, we define a Custom Field on the "Edit Form" screen in the "Custom Fields" meta box. For this Starter Form, a checkboxes field already is defined and ready for use. If you examine the Custom Fields meta box, you will notice a custom-field definition that looks like this:</p>

<p><code>field#input_checkbox|checkboxes#Option 1:Option 2:Option 3|checkboxes_checked#Option 1|data-required#false</code></p>

<p>To the left of that custom-field definition is its shortcode, which for this form looks like this:</p>

[usp_custom_field form="starter" id="1"]

<p>Including that shortcode in the form results in the display of a checkbox field, as defined by its attributes:</p>

<ul>
<li>field = the type of field</li>
<li>desc = description/label for the group of checkboxes</li>
<li>checkboxes = the options for the checkbox, separated by a colon :</li>
<li>checkboxes_checked = the option that should be checked by default</li>
<li>data-required = whether or not the field is required</li>
</ul>

<p><a href="https://plugin-planet.com/usp-pro-custom-checkbox-fields/">Learn more about custom checkbox fields</a>.</p>



<h3>Radio Field</h3>

<p>To add a custom radio field, we define a Custom Field on the "Edit Form" screen in the "Custom Fields" meta box. For this Starter Form, a radio field already is defined and ready for use. If you examine the Custom Fields meta box, you will notice a custom-field definition that looks like this:</p>

<p><code>field#input_radio|radio_inputs#Option 1:Option 2:Option 3|radio_checked#Option 1|data-required#false</code></p>

<p>To the left of that custom-field definition is its shortcode, which for this form looks like this:</p>

[usp_custom_field form="starter" id="2"]

<p>Including that shortcode in the form results in the display of a radio field, as defined by its attributes:</p>

<ul>
<li>field = the type of field</li>
<li>desc = description/label for the group of radio inputs</li>
<li>radio_inputs = the radio options, separated by a colon :</li>
<li>radio_checked = the option that should be selected by default</li>
<li>data-required = whether or not the field is required</li>
</ul>

<p><a href="https://plugin-planet.com/usp-pro-custom-radio-fields/">Learn more about custom radio fields</a>.</p>



<h3>Select Field</h3>

<p>To add a custom select field, we define a Custom Field on the "Edit Form" screen in the "Custom Fields" meta box. For this Starter Form, a select field already is defined and ready for use. If you examine the Custom Fields meta box, you will notice a custom-field definition that looks like this:</p>

<p><code>field#select|options#null:Option 1:Option 2:Option 3|option_default#Please Select..|option_select#null|label#Options|data-required#false</code></p>

<p>To the left of that custom-field definition is its shortcode, which for this form looks like this:</p>

[usp_custom_field form="starter" id="3"]

<p>Including that shortcode in the form results in the display of a select field, as defined by its attributes:</p>

<ul>
<li>field = the type of field</li>
<li>options = list of options (include null for an empty option)</li>
<li>option_default = text to use for the null/empty field (default: "Please select...")</li>
<li>option_select = the option that should be selected by default</li>
<li>label = the field label</li>
<li>data-required = whether or not the field is required</li>
</ul>

<p><a href="https://plugin-planet.com/usp-pro-custom-select-fields/">Learn more about custom select fields</a>.</p>



<h3>Text Field</h3>

<p>To add a custom text field, we define a Custom Field on the "Edit Form" screen in the "Custom Fields" meta box. For this Starter Form, a text field already is defined and ready for use. If you examine the Custom Fields meta box, you will notice a custom-field definition that looks like this:</p>

<p><code>label#Text Field|placeholder#Text Field|data-required#false</code></p>

<p>To the left of that custom-field definition is its shortcode, which for this form looks like this:</p>

[usp_custom_field form="starter" id="4"]

<p>Including that shortcode in the form results in the display of a text field, as defined by its attributes:</p>

<ul>
<li>label = the field label</li>
<li>placeholder = the field placeholder</li>
<li>data-required = whether or not the field is required</li>
</ul>

<p><a href="https://plugin-planet.com/usp-pro-custom-text-field/">Learn more about custom text fields</a>.</p>



<h3>Textarea</h3>

<p>To add a custom textarea, we define a Custom Field on the "Edit Form" screen in the "Custom Fields" meta box. For this Starter Form, a textarea already is defined and ready for use. If you examine the Custom Fields meta box, you will notice a custom-field definition that looks like this:</p>

<p><code>field#textarea|label#Textarea|placeholder#Textarea|data-required#false</code></p>

<p>To the left of that custom-field definition is its shortcode, which for this form looks like this:</p>

[usp_custom_field form="starter" id="5"]

<p>Including that shortcode in the form results in the display of a textarea field, as defined by its attributes:</p>

<ul>
<li>field = the type of field</li>
<li>label = the field label</li>
<li>placeholder = the field placeholder</li>
<li>data-required = whether or not the field is required</li>
</ul>

<p><a href="https://plugin-planet.com/usp-pro-custom-textarea/">Learn more about custom textarea fields</a>.</p>



<h3>Custom Files Field</h3>

<p>To add a custom Files field, we define a Custom Field on the "Edit Form" screen in the "Custom Fields" meta box. For this Starter Form, a Files field already is defined and ready for use. If you examine the Custom Fields meta box, you will notice a custom-field definition that looks like this:</p>

<p><code>field#input_file|label#Custom Files|multiple#true|data-required#false</code></p>

<p>To the left of that custom-field definition is its shortcode, which for this form looks like this:</p>

[usp_custom_field form="starter" id="6"]

<p>Including that shortcode in the form results in the display of a custom Files field, as defined by its attributes:</p>

<ul>
<li>field = the type of field</li>
<li>label = the field label</li>
<li>multiple = whether to allow user to select multiple files</li>
<li>data-required = whether or not the field is required</li>
</ul>

<p><a href="https://plugin-planet.com/usp-pro-multiple-file-upload-fields/">Learn more about custom Files fields</a> and visit the USP Uploads settings to set the default/global Files options (like min/max files, min/max size, et al).</p>



<h3>More types of custom fields</h3>

<p>Check out the <a href="https://plugin-planet.com/usp-pro-shortcodes/#custom-fields">Custom Fields Shortcode Reference</a> to add many other types of fields, including password, url, search, email, tel, month, week, time, datetime, datetime-local, color, date, range, and number. Tip: you can use the "USP:Custom" Quicktag to insert any Custom Field into the form.</p>



<h2>Other Fields and Items</h2>

<p>In addition to the fields we have covered so far, here are a few more fields and items that may be added to any USP Form.</p>

<h3>Fieldset</h3>

<p>By default, each form input is wrapped in a fieldset tag. If you disable this behavior in the plugin settings, you can use the "USP:Fieldset" Quicktag to add fieldset tags manually, anywhere in the form. <a href="https://plugin-planet.com/usp-pro-customize-fieldsets/">Learn more</a>.</p>

<h3>Reset Link</h3>

<p>The "USP:Reset" Quicktag makes it easy to add a "reset" link to the form. <a href="https://plugin-planet.com/usp-pro-add-link-reset-form/">Learn more</a>.</p>

<h3>Remember Info</h3>

<p>The "USP:Remember" Quicktag makes it easy to add a "Remember info" checkbox to any form. That way, if the form is submitted and returns an error, the user information will be "remembered" automatically. <a href="https://plugin-planet.com/usp-pro-add-remember-me-checkbox/">Learn more</a>.</p>

[usp_remember label="Remember Form Info"]

<h3>Custom Redirect</h3>

<p>The "USP:Redirect" makes it easy to specify a custom redirect URL. Adding this to the form tells USP Pro to redirect the user to the redirect URL once the form is submitted successfully. <a href="https://plugin-planet.com/usp-pro-custom-redirects/">Learn more</a>.</p>

<h3>Submit Button</h3>

<p>By default, a submit button is added to each form. If you disable this behavior in the plugin settings, you can use the "USP:Submit" Quicktag to add a submit button manually, anywhere in the form. <a href="https://plugin-planet.com/usp-pro-add-submit-button/">Learn more</a>.</p>



<h2>Notes</h2>

<p>To learn more about USP Pro, check out the <a href="https://plugin-planet.com/docs/usp/">documentation at Plugin Planet</a>. You also can find a list of useful resources in the plugin settings, under the "Tools" tab, in the "Helpful Resources" section. Before submitting this form (yes, it is an actual working form!), make sure to complete the few required fields: Post Title, Post Content, the Captcha, and Agree to Terms. You can fill out other fields as well, to see how they work.</p>

<p>Note: this is a post-submission form. To create and customize other types of forms, like registration forms and contact forms, check out the other USP Form Demos, located on the USP Forms menu in the WP Admin Area. You can also make combo forms, as explained in this tutorial for <a href="https://plugin-planet.com/usp-pro-contact-submit-register/">making a combo contact, submit, and register form</a>.</p>

<p>One more note: if you try submitting this Starter Form without completing the required fields, an error message for each required field will be displayed at the top of the screen. You can customize these errors in the USP More settings, in the "Primary Field Errors" section. <a href="https://plugin-planet.com/usp-pro-customize-error-messages/">Learn more</a>.</p>

<p>If you notice any typos or errors in this Starter Form, please <a href="https://plugin-planet.com/#contact">report them</a>. Thank you!</p>'. "\n\n" . $usp_submit . "\n\n";
