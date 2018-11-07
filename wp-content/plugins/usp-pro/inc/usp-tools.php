<?php // USP Pro - Tools (Settings Tab)

if (!defined('ABSPATH')) die();

/*
	Tools - Reset Default Settings
*/
if (!function_exists('usp_tools_reset')) : 
function usp_tools_reset() {
	
	$tools_reset  = '<p>'. esc_html__('To restore USP Pro default settings:', 'usp-pro') .'</p>'; 
	$tools_reset .= '<ol>';
	$tools_reset .= '<li>'. esc_html__('Check the box below and click &ldquo;Save Changes&rdquo;.', 'usp-pro') .'</li>';
	$tools_reset .= '<li><a href="'. get_admin_url() .'plugins.php?page=usp-pro-license">'. esc_html__('Deactivate your USP License', 'usp-pro') .'</a> '. esc_html__('(make a note of your License Key before deactivation; you will need it to reactivate the plugin).', 'usp-pro') .'</li>';
	$tools_reset .= '<li>'. esc_html__('Deactivate and then reactivate the plugin to make it so.', 'usp-pro') .'</li>';
	$tools_reset .= '</ol>';
	$tools_reset .= '<p><strong>'. esc_html__('Note: ', 'usp-pro') .'</strong> '. esc_html__('restoring default settings does not affect any submitted post data or existing USP Forms.', 'usp-pro') .'</p>';
	
	return $tools_reset;
}
endif;

/*
	Tools - Display Resources
*/
if (!function_exists('usp_tools_display')) : 
function usp_tools_display() {
	
	$tools_display  = '<div class="usp-pro-tools-display">';
	
	$tools_display .= '<h3><a id="usp-toggle-s1" class="usp-toggle-s1" href="#usp-toggle-s1" title="'. esc_attr__('Show/Hide Backup &amp; Restore', 'usp-pro') .'">'. esc_html__('Backup &amp; Restore', 'usp-pro') .'</a></h3>';
	$tools_display .= '<div class="usp-s1 usp-toggle default-hidden">'. usp_display_import_export() .'</div>';
	
	$tools_display .= '<h3><a id="usp-toggle-s2" class="usp-toggle-s2" href="#usp-toggle-s2" title="'. esc_attr__('Show/Hide USP Shortcodes', 'usp-pro') .'">'. esc_html__('USP Shortcodes', 'usp-pro') .'</a></h3>';
	$tools_display .= '<div class="usp-s2 usp-toggle default-hidden">'. usp_tools_shortcodes() .'</div>';
	
	$tools_display .= '<h3><a id="usp-toggle-s3" class="usp-toggle-s3" href="#usp-toggle-s3" title="'. esc_attr__('Show/Hide USP Template Tags', 'usp-pro') .'">'. esc_html__('USP Template Tags', 'usp-pro') .'</a></h3>';
	$tools_display .= '<div class="usp-s3 usp-toggle default-hidden">'. usp_tools_tags() .'</div>';
	
	$tools_display .= '<h3><a id="usp-toggle-s4" class="usp-toggle-s4" href="#usp-toggle-s4" title="'. esc_attr__('Show/Hide Helpful Resources', 'usp-pro') .'">'. esc_html__('Helpful Resources', 'usp-pro') .'</a></h3>';
	$tools_display .= '<div class="usp-s4 usp-toggle default-hidden">'. usp_tools_resources() .'</div>';
	
	$tools_display .= '<h3><a id="usp-toggle-s5" class="usp-toggle-s5" href="#usp-toggle-s5" title="'. esc_attr__('Show/Hide Tips &amp; Tricks', 'usp-pro') .'">'. esc_html__('Tips &amp; Tricks', 'usp-pro') .'</a></h3>';
	$tools_display .= '<div class="usp-s5 usp-toggle default-hidden">'. usp_tools_tips() .'</div>';
	
	$tools_display .= '</div>';
	
	return $tools_display;
}
endif;

/*
	Tools - Shortcodes
*/
if (!function_exists('usp_tools_shortcodes')) : 
function usp_tools_shortcodes() {
	
	$tools_shortcodes = '<p class="toggle-intro">' . esc_html__('USP Pro provides shortcodes that make it easy to display forms and submitted content virtually anywhere. ', 'usp-pro');
	$tools_shortcodes .= esc_html__('To get started,', 'usp-pro') . ' <a href="http://codex.wordpress.org/Shortcode_API" target="_blank" rel="noopener noreferrer">' . esc_html__('learn how to use Shortcodes', 'usp-pro') . '</a> ';
	$tools_shortcodes .= esc_html__('and then include USP Shortcodes as needed in your Posts and Pages.', 'usp-pro') .'</p>';
	$tools_shortcodes .= '<p><a href="https://plugin-planet.com/usp-pro-shortcodes/" target="_blank" rel="noopener noreferrer">'. esc_html__('Check out the complete list of USP Shortcodes &raquo;', 'usp-pro') .'</a></p>';
	$tools_shortcodes .= '<p>' . esc_html__('In addition to those provided by USP Pro, there are numerous', 'usp-pro') . ' <a href="http://codex.wordpress.org/Shortcode" target="_blank" rel="noopener noreferrer">' . esc_html__('default WP shortcodes', 'usp-pro') . '</a>, ';
	$tools_shortcodes .= esc_html__('as well as any shortcodes that may be included with your theme and/or plugin(s). Also, FYI, more information about shortcodes may be found in the USP source code (as inline comments), ', 'usp-pro');
	$tools_shortcodes .= esc_html__('specifically see', 'usp-pro') . ' <code>/inc/usp-functions.php</code>.</p>';
	return $tools_shortcodes;	
}
endif;

/*
	Tools - Template Tags
*/
if (!function_exists('usp_tools_tags')) : 
function usp_tools_tags() {
	
	$tools_tags = '<p class="toggle-intro">' . esc_html__('USP Pro provides template tags for displaying submitted post content, author information, file uploads and more. ', 'usp-pro');
	$tools_tags .= esc_html__('To get started,', 'usp-pro') . ' <a href="http://codex.wordpress.org/Template_Tags" target="_blank" rel="noopener noreferrer">' . esc_html__('learn how to use Template Tags', 'usp-pro') . '</a> ';
	$tools_tags .= esc_html__('and then include USP Template Tags as needed in your theme template.', 'usp-pro') . '</p>';
	$tools_tags .= '<p><a href="https://plugin-planet.com/usp-pro-template-tags/" target="_blank" rel="noopener noreferrer">'. esc_html__('Check out the complete list of USP Template Tags &raquo;', 'usp-pro') .'</a></p>';
	$tools_tags .= '<p>' . esc_html__('In addition to those provided by USP Pro, there are a great many template tags provided by WordPress, making it possible to display just about any information anywhere on your site. ', 'usp-pro');
	$tools_tags .= esc_html__('Also, FYI, more information about each of these template tags may be found in the USP source code (as inline comments), specifically see', 'usp-pro') . ' <code>/inc/usp-functions.php</code>.</p>';
	
	return $tools_tags;
}
endif;

/*
	Tools - Helpful Resources
*/
if (!function_exists('usp_tools_resources')) : 
function usp_tools_resources() {
	
	$tools_resources  = '<p class="toggle-intro">'. esc_html__('Here are some useful resources for working with USP Pro and WordPress.', 'usp-pro') .'</p>';
	
	$tools_resources .= '<h3>'. esc_html__('Useful resources and places to get help', 'usp-pro') .'</h3>';
	$tools_resources .= '<ul>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="https://m0n.co/usp-video-tuts">'.                               esc_html__('Video Tutorials', 'usp-pro')                              .'</a></li>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/docs/usp/">'.                         esc_html__('USP Pro Docs', 'usp-pro')                                 .'</a></li>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/tuts/">'.                             esc_html__('USP Pro Tutorials', 'usp-pro')                            .'</a></li>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/forum/usp/">'.                        esc_html__('USP Pro Forum', 'usp-pro')                                .'</a></li>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/news/">'.                             esc_html__('USP Pro News', 'usp-pro')                                 .'</a></li>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro/#contact">'.                  esc_html__('Bug reports, help requests, and feedback', 'usp-pro')     .'</a></li>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/wp/wp-login.php">'.                   esc_html__('Log in to your account for current downloads', 'usp-pro') .'</a></li>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="https://digwp.com/2011/09/where-to-get-help-with-wordpress/">'. esc_html__('Where to Get Help with WordPress', 'usp-pro')             .'</a></li>';
	$tools_resources .= '</ul>';
	
	$tools_resources .= '<h3>'. esc_html__('Key resources at the WordPress Codex', 'usp-pro') .'</h3>';
	$tools_resources .= '<ul>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="http://codex.wordpress.org/Templates">'.         esc_html__('WP Theme Templates', 'usp-pro')       .'</a></li>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="http://codex.wordpress.org/WordPress_Widgets">'. esc_html__('WP Widgets', 'usp-pro')               .'</a></li>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="http://codex.wordpress.org/Shortcode_API">'.     esc_html__('WP Shortcodes', 'usp-pro')            .'</a></li>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="http://codex.wordpress.org/Template_Tags">'.     esc_html__('WP Template Tags', 'usp-pro')         .'</a></li>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="http://codex.wordpress.org/Quicktags_API">'.     esc_html__('WP Quicktags', 'usp-pro')             .'</a></li>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="http://codex.wordpress.org/Post_Types">'.        esc_html__('WP Custom Post Types', 'usp-pro')     .'</a></li>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="http://codex.wordpress.org/The_Loop">'.          esc_html__('The WordPress Loop', 'usp-pro')       .'</a></li>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="http://codex.wordpress.org/Troubleshooting">'.   esc_html__('WP Troubleshooting Guide', 'usp-pro') .'</a></li>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="http://www.wordpress.org/support/">'.            esc_html__('WP Help Forum', 'usp-pro')            .'</a></li>';
	$tools_resources .= '</ul>';
	
	$tools_resources .= '<h3>'. esc_html__('More WordPress plugins by Jeff Starr', 'usp-pro') .'</h3>';
	$tools_resources .= '<ul>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/bbq-pro/">'. esc_html__('BBQ Pro - Advanced WordPress Firewall', 'usp-pro') .'</a></li>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/ses-pro/">'. esc_html__('SES Pro - Simple Ajax Signup Forms', 'usp-pro') .'</a></li>';
	$tools_resources .= '</ul>';
	
	$tools_resources .= '<h3>'. esc_html__('WordPress books and resources by Jeff Starr', 'usp-pro') .'</h3>';
	$tools_resources .= '<ul>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="https://digwp.com/">'.                        esc_html__('Digging Into WordPress, by Chris Coyier and Jeff Starr', 'usp-pro')                      .'</a></li>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="https://wp-tao.com/">'.                       esc_html__('The Tao of WordPress &ndash; Complete guide for users, admins, and everyone', 'usp-pro') .'</a></li>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="https://wp-tao.com/wordpress-themes-book/">'. esc_html__('WordPress Themes In Depth &ndash; Complete guide to building awesome themes', 'usp-pro') .'</a></li>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="https://htaccessbook.com/">'.                 esc_html__('.htaccess made easy &ndash; Configure, optimize, and secure your site', 'usp-pro')       .'</a></li>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="https://perishablepress.com/">'.              esc_html__('Perishable Press &ndash; WordPress, Web Design, Code &amp; Tutorials', 'usp-pro')        .'</a></li>';
	$tools_resources .= '<li><a target="_blank" rel="noopener noreferrer" href="https://wp-mix.com/">'.                       esc_html__('WP-Mix &ndash; Useful code snippets for WordPress and more', 'usp-pro')                  .'</a></li>';
	$tools_resources .= '</ul>';
	
	return $tools_resources;	
}
endif;

/*
	Tools - Tips & Tricks
*/
if (!function_exists('usp_tools_tips')) : 
function usp_tools_tips() {
	
	$tools_tips = '<p class="toggle-intro">' . esc_html__('Here is a growing collection of useful notes, tips &amp; tricks for working with USP Pro.', 'usp-pro') . '</p>';
	$tools_tips .= '<dl>';
	$tools_tips .= '<dt>' . esc_html__('Post Type Bug Fix', 'usp-pro') . '</dt>';
	$tools_tips .= '<dd>' . esc_html__('As explained in the ', 'usp-pro') . '<a target="_blank" rel="noopener noreferrer" href="http://codex.wordpress.org/Taxonomies#404_Error">'. esc_html__('WP Codex', 'usp-pro') .'</a>' . esc_html__(', an extra step is required to get WordPress to recognize theme templates for custom post types ', 'usp-pro');
	$tools_tips .= esc_html__('(e.g.,', 'usp-pro') .' <code>single-post_type.php</code> '. esc_html__('and', 'usp-pro') .' <code>archive-post_type.php</code>'. esc_html__('). So if/when you get a 404 &ldquo;Not Found&rdquo; error when trying to view a custom post type ', 'usp-pro');
	$tools_tips .= esc_html__('(e.g., at', 'usp-pro') .' <code>/usp_post/example/</code>'. esc_html__('), try the well-known fix, which is to simply', 'usp-pro') . ' <a target="_blank" rel="noopener noreferrer" href="' . get_admin_url() . 'options-permalink.php">' . esc_html__('visit the WP Permalinks Settings', 'usp-pro') . '</a>. ';
	$tools_tips .= esc_html__('After doing that, things should be working normally again. If not, try clicking the &ldquo;Save Changes&rdquo; button on the Permalink Settings page, which is another reported solution.', 'usp-pro') . '</dd>';
	
	$tools_tips .= '<dt>' . esc_html__('Template Tags Best Practice', 'usp-pro') . '</dt>';
	$tools_tips .= '<dd>' . esc_html__('When including template tags provided by a plugin or theme, it&rsquo;s good practice to precede the tag with a conditional check to make sure that the function exists. For example: ', 'usp-pro');
	$tools_tips .= esc_html__('Instead of writing this:', 'usp-pro') . ' <code>echo usp_get_images();</code>, ' . esc_html__('we can write this:', 'usp-pro') . ' <code>if (function_exists(&quot;usp_get_images&quot;)) echo usp_get_images();</code>. ';
	$tools_tips .= esc_html__('The first method works fine, but PHP will throw an error if the plugin is not installed or otherwise available. So to avoid the site-breaking error, the second method is preferred.', 'usp-pro') . '</dd>';
	
	$tools_tips .= '<dt>' . esc_html__('Force Forms to Clear Contents', 'usp-pro') . '</dt>';
	$tools_tips .= '<dd>' . esc_html__('If you are savvy with CSS, it is trivial to style forms however and get them to clear preceding/parent elements. If you&rsquo;re new to the game and just want a sure-fire way to get form fields to line up ', 'usp-pro');
	$tools_tips .= esc_html__('and look right, here is a well-known snippet of HTML/CSS that you can add to any form:', 'usp-pro') . ' <code>&lt;div style="clear:both;"&gt;&lt;/div&gt;</code>. ' . esc_html__('Just add that snippet after the last item in your form. ', 'usp-pro');
	$tools_tips .= esc_html__('It&rsquo;s not exactly best-practices design-wise, but it&rsquo;s pretty much guaranteed to do the job. Then later on you can replace the snippet with some proper CSS.', 'usp-pro') . '</dd>';
	
	$tools_tips .= '<dt>' . esc_html__('Minimum Posting Requirements', 'usp-pro') . '</dt>';
	$tools_tips .= '<dd>' . esc_html__('There are basically four types of USP Forms: user-registration, content posting, contact form, and combo registration/posting. The minimum form requirements (in terms of input fields) for the contact form are ', 'usp-pro');
	$tools_tips .= esc_html__('email, subject, and content. The minimum requirements for user registration are name and email address. The minimum for content posting is the content/textarea field. For the combo registration/posting, the minimum ', 'usp-pro');
	$tools_tips .= esc_html__('requirements are determined by the plugin settings. Likewise, other requirements may vary depending on how the plugin settings have been configured.', 'usp-pro') . '</dd>';
	
	$tools_tips .= '<dt>' . esc_html__('Shortcodes in Widgets', 'usp-pro') . '</dt>';
	$tools_tips .= '<dd>' . esc_html__('By default, shortcodes do not work when included in widgets. To make them work, just add this snippet to your theme&rsquo;s', 'usp-pro') . ' <code>functions.php</code> ' . esc_html__('file:', 'usp-pro');
	$tools_tips .= ' <code>add_filter(&quot;widget_text&quot;, &quot;do_shortcode&quot;);</code> ' . esc_html__('Nothing more to do, but remember to re-add the snippet if/when you change themes.', 'usp-pro') . '</dd>';
	
	$tools_tips .= '<dt>' . esc_html__('On Install, On Uninstall', 'usp-pro') . '</dt>';
	$tools_tips .= '<dd>' . esc_html__('Just FYI: when USP Pro is installed it creates four new options in the WordPress', 'usp-pro') .' <code>options</code> '. esc_html__('table. That&rsquo;s it. No new database tables are created. While the plugin is active, new content (post data, user info) may ', 'usp-pro');
	$tools_tips .= esc_html__('be added to the database, but no other changes are made anywhere by the plugin. Lastly, when the plugin is uninstalled (deleted from the server), the four options it created are removed from the database. ', 'usp-pro');
	$tools_tips .= esc_html__('Note that the plugin does not delete any posted/submitted content or registered users. If any posts or users were added, it is up to the admin whether or not to remove them.', 'usp-pro') . '</dd>';
	
	$tools_tips .= '<dt>' . esc_html__('Alternate Way to Reset Form', 'usp-pro') . '</dt>';
	$tools_tips .= '<dd>' . esc_html__('USP Pro includes a shortcode/quicktag for a link that will reset the form. To use a button instead, add this code to any form:', 'usp-pro') . ' <code>&lt;input type="reset" value="Reset all form values"&gt;</code>';
	$tools_tips .= esc_html__('Note that the shortcode requires the form URL as one of its attributes, but the reset', 'usp-pro') .' <code>&lt;input&gt;</code> '. esc_html__('tag works without requiring any URL.', 'usp-pro') . '</dd>';
	
	$tools_tips .= '<dt>' . esc_html__('Custom Field Recipes', 'usp-pro') . '</dt>';
	$tools_tips .= '<dd>' . esc_html__('USP Pro supports unlimited Custom Fields. Here is a cheatsheet for various types of form elements. Click the links for more details.', 'usp-pro');
	$tools_tips .= '<ul>';
	$tools_tips .= '<li><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-add-custom-textarea/">'. esc_html__('Textarea', 'usp-pro') .'</a> &ndash; <code>field#textarea</code></li>';
	$tools_tips .= '<li><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-custom-fields/">'. esc_html__('Text Input', 'usp-pro') .'</a> &ndash; <code>type#text|placeholder#Orange|label#Orange</code></li>';
	$tools_tips .= '<li><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-custom-radio-fields/">'. esc_html__('Radio Select', 'usp-pro') .'</a> &ndash; <code>type#radio|name#1|for#1|value#Oranges</code></li>';
	$tools_tips .= '<li><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-custom-checkbox-fields/">'. esc_html__('Checkbox', 'usp-pro') .'</a> &ndash; <code>type#checkbox|value#Oranges</code></li>';
	$tools_tips .= '<li><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-custom-select-fields/">'. esc_html__('Select/Option', 'usp-pro') .'</a> &ndash; <code>field#select|options#null:Option 1:Option 2:Option 3|option_default#Please Select..|option_select#null|label#Options</code></li>';
	$tools_tips .= '</ul>';
	
	$tools_tips .= '<p>'. esc_html__('For more fields and other options, check out the', 'usp-pro') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-shortcodes/#custom-fields">'. esc_html__('Custom Field Reference', 'usp-pro') .'</a> '. esc_html__('and', 'usp-pro');
	$tools_tips .= ' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro-custom-field-recipes/" title="'. esc_html__('USP Pro - Custom Field Recipes', 'usp-pro') .'">'. esc_html__('Custom Field Recipes', 'usp-pro') .'&nbsp;&raquo;</a></p></dd>';
	$tools_tips .= '</dl>';
	
	return $tools_tips;	
}
endif;


