<?php // USP Pro - General Funtionz

if (!defined('ABSPATH')) die();

if (usp_is_session_started() === false) session_start();

include (dirname(__FILE__) .'/usp-login.php');

/* 
	Function: display usp form
	Displays a USP Form based on input ID (uses usp_form in usp-shortcodes.php)
	Usage: <?php if (function_exists('display_usp_form')) display_usp_form($id, $class); ?>
	Parameters:
		$id = id of form to display (required)
		$class = optional custom classes as comma-sep list (displayed as class="aaa bbb ccc") 
*/
if (!function_exists('display_usp_form')) : 
function display_usp_form($id = false, $class = false) {
	echo do_shortcode('[usp_form id="' . $id . '" class="' . $class . '"]');
}
endif;



/* 
	Function: is submitted
	Returns a boolean value indicating whether the specified post is user-submitted
	Usage: <?php if (function_exists('usp_is_submitted')) usp_is_submitted($postId); ?>
	Parameters:
		$postId = the post to check -> default: none (current ID when used in loop)
*/
if (!function_exists('usp_is_submitted')) : 
function usp_is_submitted($postId = false) {
	global $post;
	if (empty($postId)) {
		$postId = $post ? $post->ID : null;
	}
	if (empty($postId)) return false;
	if (get_post_meta($postId, 'is_submission', true) == true) return true;
	return false;
}
endif;

/* 
	Shortcode: is submitted
	Displays a boolean value indicating whether the specified post is user-submitted
	Syntax: [usp_is_submission deny=""][/usp_is_submission]
*/
if (!function_exists('usp_is_submission')) :
function usp_is_submission($attr, $content = null) {
	extract(shortcode_atts(array('deny' => ''), $attr));
	if (usp_is_submitted()) return do_shortcode($content);
	else return $deny;
}
add_shortcode('usp_is_submission', 'usp_is_submission');
endif;



/* 
	Function: get submitted files
	Returns an array of submitted file URLs for the specified/current post
	Usage: <?php $images = usp_get_submitted($postId); foreach ($images as $image) echo $image; ?>
*/
if (!function_exists('usp_get_submitted')) : 
function usp_get_submitted($postId = false) {
	global $post;
	if (empty($postId)) $postId = $post->ID;
	if (usp_is_submitted($postId)) {
		$submitted_files = get_post_meta($postId);
		foreach ($submitted_files as $key => $value) {
			if (!preg_match("/usp-file/i", $key)) unset($submitted_files[$key]);
		}
		$files = array();
		foreach ($submitted_files as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $k => $v) $files[] = trim($v);
			}
		}
		return $files;
	} else {
		return array();
	}
}
endif;

/* 
	Shortcode: get submitted files
	Displays an array of submitted file URLs for the specified/current post
	Syntax: [usp_submitted id="" link="" number="" before="" after=""]
	Attributes:
		$id     = optional post id (uses current post if not specified)
		$link   = string option specifying the href value for optional link: 'parent', 'http://example.com/custom/url/', or '' (default is empty = no link, image only)
		$before = optional text/markup to display before each URL
		$after  = optional text/markup to display after each URL
*/
if (!function_exists('usp_submitted')) :
function usp_submitted($attr, $content = null) {
	global $post;
	extract(shortcode_atts(array(
		'id'     => false,
		'link'   => false,
		'number' => false,
		'before' => false,
		'after'  => false,
	), $attr));
	$args = compact('before', 'after');
	$new = array();
	foreach ($args as $key => $value) {
		$value = str_replace("{", "<", $value);
		$value = str_replace("}", ">", $value);
		if (isset($value)) $new[$key] = $value;
	}
	if (empty($number)) $number = -1;

	if     ($link == 'parent')             $href = get_permalink($post->post_parent);
	elseif (preg_match("/^http/i", $link)) $href = $link;
	elseif (preg_match("/^#/i", $link))    $href = $link;
	else                                   $href = '';
	if (!empty($href)) {
		$open  = '<a href="' . $href . '">';
		$close = '</a>';
	} else {
		$open  = '';
		$close = '';
	}
	$i = 1;
	$display_files = '';
	$files = usp_get_submitted($id);
	foreach ($files as $file) {
		$display_files .= $new['before'] . $open . $file . $close . $new['after'];
		if ($i === intval($number)) break;
		$i++;
	}
	return $display_files;
}
add_shortcode('usp_submitted', 'usp_submitted');
endif;



/*
	Function: get image attachments 
	Returns an array of the formatted URLs for image attachments
	Usage:  <?php if (function_exists('usp_get_images')) $images = usp_get_images('thumbnail', '', '', false, false); foreach ($images as $image) echo $image; ?>
	Syntax: <?php if (function_exists('usp_get_images')) $images = usp_get_images($size, $before, $after, $number, $postId); ?>
	Parameters:
		$size   = image size as thumbnail, medium, large or full -> default = thumbnail
		$before = text/markup displayed before the image URL     -> default = <img src="
		$after  = text/markup displayed after the image URL      -> default = " />
		$number = the number of images to display for each post  -> default = false (display all)
		$postId = an optional post ID to use                     -> default = uses global/current post
*/
if (!function_exists('usp_get_images')) : 
function usp_get_images($size = false, $before = false, $after = false, $number = false, $postId = false) {
	global $post;

	if (false === $postId || !is_numeric($postId)) $postId = $post->ID;
	if (false === $number || !is_numeric($number)) $number = apply_filters('usp_image_attachments', 100);
	if (false === $size)                           $size   = 'thumbnail';
	if (false === $before)                         $before = '<img src="';
	if (false === $after)                          $after  = '" />';

	$args = compact('before', 'after');
	$new = array();
	foreach ($args as $key => $value) {
		$value = str_replace("{", "<", $value);
		$value = str_replace("}", ">", $value);
		if (isset($value)) $new[$key] = $value;
	}
	$args = array(
			'post_status'    => 'publish', 
			'post_type'      => 'attachment', 
			'post_parent'    => $postId, 
			'post_status'    => 'inherit', 
			'posts_per_page' => $number,
			'fields'         => 'ids'
	);
	$image_ids = get_posts($args);
	$urls = array(); $i = 1;
	foreach ($image_ids as $id) {
		$url = wp_get_attachment_image_src($id, $size);
		if ($url !== false) {
			$before = isset($new['before']) ? $new['before'] : '';
			$after  = isset($new['after'])  ? $new['after']  : '';
			$urls[] = isset($url[0]) ? $before . $url[0] . $after : '';
			if ($i == intval($number)) break;
			$i++;
		}
	}
	return $urls;
}
endif;

/* 
	Shortcode: get image attachments
	Displays formatted URLs for image attachments
	Syntax: [usp_images size="" before="" after="" number="" id=""]
*/
if (!function_exists('usp_images')) :
function usp_images($attr, $content = null) {
	extract(shortcode_atts(array(
		'size'   => false,
		'before' => false,
		'after'  => false,
		'number' => false,
		'id'     => false,
	), $attr));
	$urls = usp_get_images($size, $before, $after, $number, $id);
	$display_urls = '';
	foreach ($urls as $url) $display_urls .= $url;
	return $display_urls;
}
add_shortcode('usp_images', 'usp_images');
endif;



/*
	Function: get file attachments 
	Returns an array of the formatted URLs for post attachments
	Usage:  <?php if (function_exists('usp_get_files')) $files = usp_get_files(); foreach ($files as $file) echo $file; ?>
	Syntax: <?php if (function_exists('usp_get_files')) $files = usp_get_files($before, $after, $number, $postId); ?>
	Parameters:
		$before = text/markup displayed before the file URL -> default = ''
		$after  = text/markup displayed after the file URL  -> default = ''
		$number = number of URLs to display for each post   -> default = false (display all)
		$postId = an optional post ID to use                -> default = uses global/current post
*/
if (!function_exists('usp_get_files')) : 
function usp_get_files($before = false, $after = false, $number = false, $postId = false) {
	global $post;
	
	if (false === $postId || !is_numeric($postId)) $postId = $post->ID;
	if (false === $number || !is_numeric($number)) $number = apply_filters('usp_file_attachments', 100);
	if (false === $before)                         $before = '';
	if (false === $after)                          $after  = '';
	
	$args = compact('before', 'after');
	$new = array();
	foreach ($args as $key => $value) {
		$value = str_replace("{", "<", $value);
		$value = str_replace("}", ">", $value);
		if (isset($value)) $new[$key] = $value;
	}
	$args = array(
			'post_status'    => 'publish', 
			'post_type'      => 'attachment', 
			'post_parent'    => $postId, 
			'post_status'    => 'inherit', 
			'posts_per_page' => $number,
			'fields'         => 'ids'
	);
	$file_ids = get_posts($args);
	$urls = array();
	foreach ($file_ids as $id) {
		$url = wp_get_attachment_url($id);
		if ($url !== false) {
			$before = isset($new['before']) ? $new['before'] : '';
			$after  = isset($new['after'])  ? $new['after']  : '';
			$urls[] = $before . $url . $after;
		}
	}
	return $urls;
}
endif;

/* 
	Shortcode: get file attachments
	Displays the formatted URLs for post attachments
	Syntax: [usp_file before="" after="" number="" id=""]
*/
if (!function_exists('usp_file')) :
function usp_file($attr, $content = null) {
	extract(shortcode_atts(array(
		'before' => false,
		'after'  => false,
		'number' => false,
		'id'     => false,
	), $attr));
	$files = usp_get_files($before, $after, $number, $id);
	$display_files = '';
	foreach ($files as $file) $display_files .= $file;
	return $display_files;
}
add_shortcode('usp_file', 'usp_file');
endif;



/*
	Function: get file IDs 
	Returns an array of the IDs for post attachments
	Usage:  <?php if (function_exists('usp_ids')) $ids = usp_ids(); foreach ($ids as $id) echo $id; ?>
	Syntax: <?php if (function_exists('usp_ids')) usp_ids($number, $postId); ?>
	Parameters:
		$number = number of URLs to display for each post -> default = false (display all)
		$postId = an optional post ID to use              -> default = uses global/current post
*/
if (!function_exists('usp_ids')) : 
function usp_ids($number = false, $postId = false) {
	if (false === $postId) {
		global $post;
		$postId = $post->ID;
	}
	$number = (false === $number || !is_numeric($number)) ? apply_filters('usp_file_ids', 100) : $number;
	$args = array(
			'post_status'    => 'publish', 
			'post_type'      => 'attachment', 
			'post_parent'    => $postId, 
			'post_status'    => 'inherit', 
			'posts_per_page' => $number,
			'fields'         => 'ids'
	);
	return get_posts($args);
}
endif;



/*
	Function: get link(s) to file attachment pages
	Returns a formatted set of links to attachment pages for each post
	Usage:  <?php if (function_exists('usp_attachment_link')) echo usp_attachment_link(); ?>
	Syntax: <?php if (function_exists('usp_attachment_link')) echo usp_attachment_link(false, false, false, false, false, false, false); ?>
	Parameters:
		$postId       = an optional post ID to use                       -> default = uses global/current post
		$fieldId      = an optional, specific uploaded file              -> default = false (display all)
		$number       = number of links to display for each post         -> default = false (display all)
		$before_item  = optional text/markup to include before each item -> default = '{li}'
		$after_item   = optional text/markup to include after each item  -> default = '{/li}'
		$before_list  = optional text/markup to include before all items -> default = '{ul}'
		$after_list   = optional text/markup to include after all items  -> default = '{/ul}'
		
*/
if (!function_exists('usp_attachment_link')) : 
function usp_attachment_link($postId = false, $fieldId = false, $number = false, $before_item = false, $after_item = false, $before_list = false, $after_list = false) {
	global $post;
	if (false === $postId || !is_numeric($postId)) $postId = $post->ID;
	if (false === $number || !is_numeric($number)) $number = 0;
	
	if (false === $before_item) $before_item = '{li}';
	if (false === $after_item)  $after_item  = '{/li}';
	if (false === $before_list) $before_list = '{ul class="usp-attach-links"}';
	if (false === $after_list)  $after_list  = '{/ul}';

	$args = compact('postId', 'fieldId', 'number', 'before_item', 'after_item', 'before_list', 'after_list');
	$new = array();
	foreach ($args as $key => $value) {
		$value = str_replace("{", "<", $value);
		$value = str_replace("}", ">", $value);
		if (isset($value)) $new[$key] = $value;
	}
	$i = 1;
	$ids = usp_ids();
	$links = $new['before_list'];
	if (false === $fieldId || !is_numeric($fieldId)) {
		foreach ($ids as $id) {
			$links .=  $new['before_item'] .'<a class="usp-attachment-link" href="'. get_attachment_link($id) .'">'. get_the_title($id) .'</a>'. $new['after_item'] ."\n";
			if ($i === intval($number)) break;
			$i++;
		}
	} else {
		$id = intval($fieldId) - 1;
		if (isset($ids[$id])) $links .= $new['before_item'] .'<a class="usp-attachment-link" href="'. get_attachment_link($ids[$id]) .'">'. get_the_title($ids[$id]) .'</a>'. $new['after_item'] ."\n";
	}
	$links .= $new['after_list'];
	return $links;
}
endif;

/* 
	Shortcode: get link(s) to file attachment pages
	Displays links to attachment pages for each post
	Syntax: [usp_attachments id="" file="" number="" beforeitem="" afteritem="" beforelist="" afterlist=""]
*/
if (!function_exists('usp_attachments')) :
function usp_attachments($attr, $content = null) {
	$args = array(
		'id'         => false,
		'file'       => false,
		'number'     => false,
		'beforeitem' => false,
		'afteritem'  => false,
		'beforelist' => false,
		'afterlist'  => false,
	);
	extract(shortcode_atts($args, $attr));
	$links = usp_attachment_link($id, $file, $number, $beforeitem, $afteritem, $beforelist, $afterlist);
	return $links;
}
add_shortcode('usp_attachments', 'usp_attachments');
endif;



/*
	Function: get attachment filename
	Returns the filename of the specified attachment
	Usage (in loop): <?php if (function_exists('usp_get_filename')) echo usp_get_filename(false, '2'); // current post, filename of 2nd attached file ?>
	Usage (out of loop): <?php if (function_exists('usp_get_filename')) echo usp_get_filename('2544', '3'); // post ID 2544, filename of 3rd attached file ?>
	Parameters:
		$postId  = optional post ID
		$fieldId = optional uploaded file number (e.g., 1, 2, 3)
*/
if (!function_exists('usp_get_filename')) : 
function usp_get_filename($postId = false, $fieldId = false) {
	global $post;
	if (false === $postId) $postId = $post->ID;
	if (false === $fieldId) $fieldId = '1';
	$attachment = get_post_meta($postId, 'usp-file-'.$fieldId, true);
	$filepath = parse_url($attachment, PHP_URL_PATH);
	$basename = basename($filepath);
	$filename = strtok($basename, '.');
	return $filename;
}
endif;

/* 
	Shortcode: get attachment filename
	Displays the filename of the specified attachment
	Syntax: [usp_filename id="" file=""]
*/
if (!function_exists('usp_filename')) :
function usp_filename($attr, $content = null) {
	extract(shortcode_atts($args = array(
		'id'   => '',
		'file' => '',
	), $attr));
	if ($id == '') $id = false;
	if ($file == '') $file = false;
	$filename = usp_get_filename($id, $file);
	return $filename;
}
add_shortcode('usp_filename', 'usp_filename');
endif;



/*
	Function: get latest submitted attachment
	Returns optionally formatted URL(s) of the latest attachment(s)
	Usage:  <?php if (function_exists('usp_latest_attachment')) usp_latest_attachment(); ?>
	Syntax: <?php usp_latest_attachment(false, false, false, false, false); ?>
	Parameters:
		$before = text/markup displayed before URL -> default = ''
		$after  = text/markup displayed after URL  -> default = ''
		$number = the number of recent attachments -> default = 1
		$postId = an optional post ID to use       -> default = uses global/current post
		$url    = URL is attachment, post, or file -> default = 'file' (options: 'file', 'post', 'attachment'
*/
if (!function_exists('usp_latest_attachment')) : 
function usp_latest_attachment($before = false, $after = false, $number = false, $postId = false, $url = false) {
	global $post;
	if (false === $postId || !is_numeric($postId)) $postId = $post->ID;
	if (false === $number || !is_numeric($number)) $number = 1;

	if (false === $before) $before = '';
	if (false === $after)  $after = '';
	if (false === $url)    $url = 'file';

	$args = compact('before', 'after');
	$new = array();
	foreach ($args as $key => $value) {
		$value = str_replace("{", "<", $value);
		$value = str_replace("}", ">", $value);
		if (isset($value)) $new[$key] = $value;
	}
	$files = get_posts(array(
		'post_status'    => 'publish', 
		'post_type'      => 'attachment', 
		'post_parent'    => $postId, 
		'post_status'    => 'inherit', 
		'posts_per_page' => $number, 
		'orderby'        => 'date', 
		'order'          => 'ASC'
		));
	$latest = '';
	foreach ($files as $file) {
		if (usp_is_submitted($postId)) {
			$latest .= $new['before'];
			if ($url == 'post') $latest .= get_permalink($postId);
			elseif ($url == 'attachment') $latest .= get_permalink($file->ID);
			else $latest .= wp_get_attachment_url($file->ID);
			$latest .= $new['after'];
		}
	}
	return $latest;
}
endif;

/* 
	Shortcode: get latest submitted attachment
	Displays optionally formatted URL(s) of the latest attachment(s)
	Syntax: [usp_latest before="" after="" number="" id="" url=""]
*/
if (!function_exists('usp_latest')) :
function usp_latest($attr, $content = null) {
	extract(shortcode_atts($args = array(
		'before' => false,
		'after'  => false,
		'number' => false,
		'id'     => false,
		'url'    => false,
	), $attr));
	$latest = usp_latest_attachment($before, $after, $number, $id, $url);
	return $latest;
}
add_shortcode('usp_latest', 'usp_latest');
endif;



/*
	Function: get usp meta
	Returns the value of the specified custom field
	Usage: <?php if (function_exists('usp_get_meta')) echo usp_get_meta(false, false); ?>
	Parameters:
		$postId = an optional post ID to use -> default false = uses global/current post
		$meta   = name of custom field       -> defaults to 'usp-author'
*/
if (!function_exists('usp_get_meta')) : 
function usp_get_meta($postId = false, $meta = false) {
	global $post;
	if (empty($postId)) $postId = $post->ID;
	if (empty($meta)) $meta = 'usp-author';
	return get_post_meta($postId, $meta, true);
}
endif;

/* 
	Shortcode: get usp meta
	Displays the value of the specified custom field
	Syntax: [usp_meta id="" meta=""]
*/
if (!function_exists('usp_meta')) :
function usp_meta($attr, $content = null) {
	extract(shortcode_atts($args = array(
		'id'   => false,
		'meta' => false,
	), $attr));
	return usp_get_meta($id, $meta);
}
add_shortcode('usp_meta', 'usp_meta');
endif;



/*
	Function: get all usp meta
	Returns all usp custom fields for current or specified post
	Usage: <?php if (function_exists('usp_get_all_meta')) $usp_meta = usp_get_all_meta(false); foreach ($usp_meta as $key => $value) echo $key . ' => ' . $value . "\n"; ?>
	Parameters:
		$postId = an optional post ID to use -> default false = uses global/current post
*/
if (!function_exists('usp_get_all_meta')) : 
function usp_get_all_meta($postId = false) {
	global $post;
	if (false === $postId) $postId = $post->ID;
	$post_meta = get_post_custom($postId);
	$usp_meta = array();
	foreach ($post_meta as $key => $value) {
		if (preg_match("/usp-/i", $key, $match)) {
			foreach ($value as $k => $v) $usp_meta[$key] = $v;
		}
	}
	return $usp_meta;
}
endif;

/* 
	Shortcode: get all usp meta
	Displays all usp custom fields for current or specified post
	Syntax: [usp_all_meta id="" before="" after="" sep=""]
*/
if (!function_exists('usp_all_meta')) :
function usp_all_meta($attr, $content = null) {
	extract(shortcode_atts($args = array(
		'id'     => false,
		'before' => '{div}',
		'after'  => '{/div}',
		'sep'    => ' => ',
	), $attr));
	$args = compact('before', 'after', 'sep');
	$new = array();
	foreach ($args as $key => $value) {
		$value = str_replace("{", "<", $value);
		$value = str_replace("}", ">", $value);
		if (isset($value)) $new[$key] = $value;
	}
	$usp_meta = usp_get_all_meta($id);
	$display_meta = '';
	foreach ($usp_meta as $key => $value) $display_meta .= $new['before'] . $key . $new['sep'] . $value . $new['after'] . "\n";
	return $display_meta;
}
add_shortcode('usp_all_meta', 'usp_all_meta');
endif;



/*
	Function: get image data
	Returns formatted images from within post loop, or the same for specified images outside the loop (i.e., anywhere in the theme)
	Usage: <?php if (function_exists('usp_get_image')) echo usp_get_image(false, false, false, false, false, false, false, false, false, false, false, false, false, false); ?>
	Parameters:
		$ids    = comma-separated list of attachment ID(s) (leave empty or false to get all images)   -> default = false
		$size   = size of the attachment (e.g., thumbnail, medium, large or full)                     -> default = 'thumbnail'
		$icon   = (boolean) whether to use a media icon to represent the attachment                   -> default = false
		$number = the number of images for which to retrieve data (leave empty/false to display all)  -> default = false
		$link   = string option specifying the href value for link (leave empty/false for image only) -> default = false (other options: 'file', 'attachment', 'parent', 'image', 'http://example.com/custom/url/', or '')

		$before_item => text/markup included before each item  -> default (false/empty) => '{span class="usp-image-wrap"}', 
		$after_item  => text/markup included after each item   -> default (false/empty) => '{/span}', 
		$before_list => text/markup included before all items  -> default (false/empty) => '{div class="usp-images-wrap"}', 
		$after_list  => text/markup included after all items   -> default (false/empty) => '{/div}',
		$link_class  => class name(s) for links                -> default (false/empty) => 'lightbox',
		$link_att    => optional attribute(s) for links        -> default (false/empty) => 'rel="lightbox"', // can use "#id#" (without the quotes) to include the current post ID
		$link_title  => title for links when enabled           -> default (false/empty) => '', // set false to use attachment description for title
		$img_class   => class name(s) for each image           -> default (false/empty) => 'usp-image',
		$img_att     => optional attribute(s) for images       -> default (false/empty) => '', // e.g., 'target="_blank"', can use "#id#" (without the quotes) to include the current post ID
*/
if (!function_exists('usp_get_image')) : 
function usp_get_image(
		$ids         = false, 
		$size        = false, 
		$icon        = false, 
		$number      = false, 
		$link        = false, 
		$before_item = false, 
		$after_item  = false, 
		$before_list = false, 
		$after_list  = false, 
		$link_class  = false, 
		$link_att    = false, 
		$link_title  = false, 
		$img_class   = false, 
		$img_att     = false
	) {
	global $post;
	
	$postId = isset($post->ID) ? $post->ID : null;
	if (!$postId) return '';
	
	if (empty($ids))            $ids         = usp_ids();
	if (empty($size))           $size        = 'thumbnail';
	if (false === $icon)        $icon        = false;
	if (false === $number)      $number      = 0;
	if (false === $link)        $link        = false;
	if (false === $before_item) $before_item = '{span class="usp-image-wrap"}';
	if (false === $after_item)  $after_item  = '{/span}';
	if (false === $before_list) $before_list = '{div class="usp-images-wrap"}';
	if (false === $after_list)  $after_list  = '{/div}';
	if (false === $link_class)  $link_class  = 'lightbox';
	if (false === $link_att)    $link_att    = 'rel="lightbox"';
	if (false === $link_title)  $link_title  = false;
	if (false === $img_class)   $img_class   = 'usp-image';
	if (false === $img_att)     $img_att     = '';
	
	$args = compact('before_item', 'after_item', 'before_list', 'after_list');
	$new = array();
	foreach ($args as $key => $value) {
		$value = str_replace("{", "<", $value);
		$value = str_replace("}", ">", $value);
		if (isset($value)) $new[$key] = $value;
	}
	if (is_string($ids)) {
		$id_arr = explode(",", $ids);
		$ids = array();
		foreach ($id_arr as $id) $ids[] = rtrim(trim($id), ',');
	}
	if (empty($ids)) return '';
	
	if (!empty($link_att)) $link_att = ' ' . preg_replace("/#id#/i", $postId, $link_att);
	if (!empty($img_att))  $img_att  = ' ' . preg_replace("/#id#/i", $postId, $img_att);
	
	$i = 1;
	$image_data = $new['before_list'];
	foreach ($ids as $id) {
		$alt = '';
		$alt = get_post_meta($id, '_wp_attachment_image_alt', true);
		if (!empty($alt)) $alt = ' alt="' . $alt . '"';

		if ($link_title === false)   $title = ' title="' . get_post_meta($postId, 'usp-desc-'.$i, true) . '"';
		elseif (!empty($link_title)) $title = ' title="' . $link_title . '"';
		else                         $title = '';

		$atts = wp_get_attachment_image_src($id, $size, $icon);
		if ($atts !== false) {
			if     ($link == 'file')               $href = wp_get_attachment_url($id);
			elseif ($link == 'attachment')         $href = get_attachment_link($id);
			elseif ($link == 'parent')             $href = get_permalink($post->post_parent);
			elseif ($link == 'image')              $href = $atts[0];
			elseif (preg_match("/^http/i", $link)) $href = esc_url($link);
			elseif (preg_match("/^#/i", $link))    $href = esc_url($link);
			else                                   $href = '';
			if (!empty($href)) {
				if (isset($atts[0], $atts[1], $atts[2])) {
					$image_data .= $new['before_item'] . '<a href="' . $href. '" class="' . $link_class . '"' . $link_att . $title . '>';
					$image_data .= '<img id="usp-attach-id-' . $id . '" class="' . $img_class . '" src="' . $atts[0] . '" width="' . $atts[1] . '" height="' . $atts[2] . '"' . $img_att . $alt . ' />';
					$image_data .= '</a>' . $new['after_item'];
				}
			} else {
				if (isset($atts[0], $atts[1], $atts[2])) {
					$image_data .= $new['before_item'];
					$image_data .= '<img id="usp-attach-id-' . $id . '" class="' . $img_class . '" src="' . $atts[0] . '" width="' . $atts[1] . '" height="' . $atts[2] . '"' . $img_att . $alt . ' />';
					$image_data .= $new['after_item'];
				}
			}
			if ($i === intval($number)) break;
			$i++;
		} else {
			continue;
		}
	}
	$image_data .= $new['after_list'];
	return $image_data;
}
endif;

/* 
	Shortcode: get image data
	Displays formatted images from within post loop, or the same for specified images outside the loop (i.e., anywhere in the theme)
	Syntax: [usp_image ids="" size="" icon="" number="" link="" before_item="" after_item="" before_list="" after_list="" link_class="" link_att="" link_title="" img_class="" img_att=""]
*/
if (!function_exists('usp_image')) :
function usp_image($attr, $content = null) {
	$args = array(
		'ids'         => false,
		'size'        => false,
		'icon'        => false,
		'number'      => false,
		'link'        => false,
		'before_item' => false, 
		'after_item'  => false, 
		'before_list' => false, 
		'after_list'  => false,
		'link_class'  => false,
		'link_att'    => false,
		'link_title'  => false,
		'img_class'   => false,
		'img_att'     => false,
	);
	extract(shortcode_atts($args, $attr));
	return usp_get_image($ids, $size, $icon, $number, $link, $before_item, $after_item, $before_list, $after_list, $link_class, $link_att, $link_title, $img_class, $img_att);
}
add_shortcode('usp_image', 'usp_image');
endif;



/*
	Function: get author link
	Returns author name from custom field as a link (if URL exists) or plain text (if URL not available)
	Usage: <?php if (function_exists('usp_get_author_link')) usp_get_author_link(); ?>
*/
if (!function_exists('usp_get_author_link')) : 
function usp_get_author_link() {
	global $post;
	$is_usp     = get_post_meta($post->ID, 'is_submission', true);
	$author     = get_post_meta($post->ID, 'usp-author', true);
	$author_url = get_post_meta($post->ID, 'usp-url', true);
	if ($is_usp && !empty($author)) {
		if (empty($author_url)) return '<span class="usp-author-link">'. $author .'</span>';
		else return '<span class="usp-author-link"><a href="'. $author_url .'">'. $author .'</a></span>';
	} else {
		return '<span class="usp-author-link"><a href="'. get_author_posts_url(get_the_author_meta('ID')) .'">'. get_the_author_meta('display_name') .'</a></span>';
	}
}
endif;

/* 
	Shortcode: get author link
	Displays author name from custom field as a link (if URL provided) or plain text (if URL not provided)
	Syntax: [usp_author_name]
*/
if (!function_exists('usp_author_name')) :
function usp_author_name($attr, $content = null) {
	return usp_get_author_link();
}
add_shortcode('usp_author_name', 'usp_author_name');
endif;



/*
	Function: get submitter avatar
	Returns the avatar (image) of the specified submitter
	Usage: <?php if (function_exists('usp_get_avatar')) echo usp_get_avatar(false, false, false, false, false); ?>
	Parameters:
		$postId      = an optional post ID to use           -> default false = uses global/current post
		$id_or_email = an optional email to use             -> default false
		$size        = optional size of avatar (max is 512) -> default is 96
		$default     = optional default image URL           -> defaults to WP's "Mystery Man"
		$alt         = optional alt-text for image att      -> defaults to false
*/
if (!function_exists('usp_get_avatar')) : 
function usp_get_avatar($postId = false, $id_or_email = false, $size = false, $default = false, $alt = false) {
	global $post;
	if (false === $postId)  $postId = $post->ID;
	if (false === $size)    $size = 96;
	if (false === $default) $default = '';

	if (false === $id_or_email)       $user_ID = get_post_meta($postId, 'usp-author-id', true);
	elseif (is_numeric($id_or_email)) $user_ID = $id_or_email;
	else                              $user_ID = get_current_user_id();

	$avatar = '';
	if (isset($user_ID) && !empty($user_ID) && is_numeric($user_ID)) {
		$avatar = get_avatar($user_ID, $size, $default, $alt);
	}
	return $avatar;
}
endif;

/* 
	Shortcode: get submitter avatar
	Displays the avatar (image) of the specified submitter
	Syntax: [usp_avatar postid="" userid="" size="" default="" alt=""]
*/
if (!function_exists('usp_avatar')) :
function usp_avatar($attr, $content = null) {
	$args = array(
		'postid'  => false,
		'userid'  => false,
		'size'    => false,
		'default' => false,
		'alt'     => false,
	);
	extract(shortcode_atts($args, $attr));
	return usp_get_avatar($postid, $userid, $size, $default, $alt);
}
add_shortcode('usp_avatar', 'usp_avatar');
endif;



/* 
	Function: display usp video URL
	Returns url(s) of uploaded video(s)
	Usage: <?php if (function_exists('usp_video_url')) $video_urls = usp_video_url(false, false, false); foreach ($video_urls as $video_url) echo $video_url; ?>
	Parameters:
		$postId  = the post to check -> default: none (current ID when used in loop)
		$fieldId = optional uploaded file number (e.g., 1, 2, 3)
		$number  = the number of video URLs to retrieve
	Note:
		WP currently supports:
		mp4
		m4v
		webm
		ogv
		wmv
		flv
*/
if (!function_exists('usp_video_url')) :
function usp_video_url($postId = false, $fileId = false, $number = false) {
	global $post;
	if ($postId === false) $postId = $post->ID;
	if ($number === false) $number = -1;
	if ($fileId === false) $fileId = null;

	$post_meta = get_post_custom($postId);
	$video_extensions = wp_get_video_extensions();
	$video_urls = array();
	$i = 0;
	foreach ($post_meta as $key => $value) {
		if (preg_match("/^usp-file-([0-9]+)$/i", $key, $matches)) {
			foreach ($value as $k => $v) {
				$url = pathinfo($v);
				if (isset($url['extension']) && in_array($url['extension'], $video_extensions)) {
					if (!empty($fileId)) {
						if (isset($matches[1]) && $matches[1] == $fileId) {
							if (!empty($v)) {
								$video_urls[] = $v;
								$i++;
							}
						}
					} else {
						$video_urls[] = $v;
						$i++;
					}
				}
			}
		}
		if ($i === intval($number)) break;
	}
	return $video_urls;
}
endif;



/* 
	Shortcode: display usp video
	Syntax: [usp_video id="" file="" poster="" loop="" autoplay="" preload="" height="" width=""]
	See @ https://codex.wordpress.org/Video_Shortcode
*/
if (!function_exists('usp_video')) :
function usp_video($atts, $content = null) {
	global $post;
	extract(shortcode_atts(array(
		'id'   => '',
		'file' => '',
		//
		'poster'   => '',
		'loop'     => 'off',
		'autoplay' => 'off',
		'preload'  => 'metadata', // none, auto
		'height'   => '',
		'width'    => '',
	), $atts));
	if (empty($id)) $id = false;
	if (empty($file)) $file = false;
	$video_urls = usp_video_url($id, $file, 1);
	$display = '';
	foreach ($video_urls as $video_url) {
		$display .= do_shortcode('[video src="'. $video_url .'" poster="'. $poster .'" loop="'. $loop .'" autoplay="'. $autoplay .'" preload="'. $preload .'" height="'. $height .'" width="'. $width .'"]');
	}
	return $display;
}
add_shortcode('usp_video', 'usp_video');
endif;



/* 
	Function: display usp audio URL
	Returns url(s) of uploaded audio file(s)
	Usage: <?php if (function_exists('usp_audio_url')) $audio_urls = usp_audio_url(false, false, false); foreach ($audio_urls as $audio_url) echo $audio_url; ?>
	Parameters:
		$postId  = the post to check -> default: none (current ID when used in loop)
		$fieldId = optional uploaded file number (e.g., 1, 2, 3)
		$number  = the number of audio URLs to retrieve
	Note:
		WP currently supports:
		mp3
		ogg
		wma
		m4a
		wav
*/
if (!function_exists('usp_audio_url')) :
function usp_audio_url($postId = false, $fileId = false, $number = false) {
	global $post;
	if ($postId === false) $postId = $post->ID;
	if ($number === false) $number = -1;
	if ($fileId === false) $fileId = null;

	$post_meta = get_post_custom($postId);
	$audio_extensions = wp_get_audio_extensions();
	$audio_urls = array();
	$i = 0;
	foreach ($post_meta as $key => $value) {
		if (preg_match("/^usp-file-([0-9]+)$/i", $key, $matches)) {
			foreach ($value as $k => $v) {
				$url = pathinfo($v);
				if (isset($url['extension']) && in_array($url['extension'], $audio_extensions)) {
					if (!empty($fileId)) {
						if (isset($matches[1]) && $matches[1] == $fileId) {
							if (!empty($v)) {
								$audio_urls[] = $v;
								$i++;
							}
						}
					} else {
						$audio_urls[] = $v;
						$i++;
					}
				}
			}
		}
		if ($i === intval($number)) break;
	}
	return $audio_urls;
}
endif;



/* 
	Shortcode: display usp audio
	Syntax: [usp_audio id="" file="" number="" before="" after="" loop="" autoplay="" preload=""]
	See @ https://codex.wordpress.org/Audio_Shortcode
*/
if (!function_exists('usp_audio')) :
function usp_audio($atts, $content = null) {
	global $post;
	extract(shortcode_atts(array(
		'id'     => '',
		'file'   => '',
		'number' => '', // note: multiple files via shortcode works for audio not video as of WP 3.8
		'before' => '',
		'after'  => '',
		//
		'loop'     => 'off',
		'autoplay' => 'off',
		'preload'  => 'metadata', // none, auto
	), $atts));
	if (empty($id)) $id = false;
	if (empty($file)) $file = false;
	if (empty($number)) $number = 1;
	$audio_urls = usp_audio_url($id, $file, $number);
	$display = '';
	foreach ($audio_urls as $audio_url) {
		$display .= $before . do_shortcode('[audio src="'. $audio_url .'" loop="'. $loop .'" autoplay="'. $autoplay .'" preload="'. $preload .'"]') . $after;
	}
	return $display;
}
add_shortcode('usp_audio', 'usp_audio');
endif;



/* 
	Shortcode: require login based on capability
	Syntax: [usp_access cap="read" deny=""][/usp_access]
	Can use {tag} to output <tag>
	See the USP Post "Shortcode Demo" for an example of this shortcode
	See @ http://codex.wordpress.org/Roles_and_Capabilities#Capabilities
*/
if (!function_exists('usp_access')) :
function usp_access($attr, $content = null) {
	extract(shortcode_atts(array(
		'cap'  => 'read',
		'deny' => '',
	), $attr));
	
	$deny = str_replace("{", "<", $deny);
	$deny = str_replace("}", ">", $deny);
	
	$caps = array_map('trim', explode(',', $cap));
	
	foreach ($caps as $c) {
		if (current_user_can($c) && !is_null($content) && !is_feed()) return do_shortcode($content);
	}
	return $deny;
}
add_shortcode('usp_access', 'usp_access');
endif;



/* 
	Shortcode: show content to members
	Syntax: [usp_member deny=""][/usp_member]
	Can use {tag} to output <tag>
	See the USP Post "Shortcode Demo" for an example of this shortcode
*/
if (!function_exists('usp_member')) :
function usp_member($attr, $content = null) {
	extract(shortcode_atts(array(
		'deny' => '',
	), $attr));
	
	$deny = str_replace("{", "<", $deny);
	$deny = str_replace("}", ">", $deny);
	
	if (is_user_logged_in() && !is_null($content) && !is_feed()) return do_shortcode($content);
	return $deny;
}
add_shortcode('usp_member', 'usp_member');
endif;



/* 
	Shortcode: show content to visitors
	Syntax: [usp_visitor deny=""][/usp_visitor]
	Can use {tag} to output <tag>
	See the USP Post "Shortcode Demo" for an example of this shortcode
*/
if (!function_exists('usp_visitor')) : 
function usp_visitor($attr, $content = null) {
	extract(shortcode_atts(array(
		'deny' => '',
	), $attr));
	
	$deny = str_replace("{", "<", $deny);
	$deny = str_replace("}", ">", $deny);
	
	if ((!is_user_logged_in() && !is_null($content)) || is_feed()) return do_shortcode($content);
	return $deny;
}
add_shortcode('usp_visitor', 'usp_visitor');
endif;



/*
	Shortcode Empty Paragraph Fix
*/
if (!function_exists('usp_shortcode_empty_p_fix')) :
function usp_shortcode_empty_p_fix($content) {
    $array = array(
        '<p>['    => '[',
        ']</p>'   => ']',
        ']<br />' => ']',
        ']<br>'   => ']'
    );
    $content = strtr($content, $array);
    return $content;
}
add_filter('the_content', 'usp_shortcode_empty_p_fix');
endif;



/* 
	Shortcode: display user logged in status and info
	Syntax: [usp_status before="" after="" display="" logintext="" logouttext=""]
		before:    text/markup to display before the status
		after:     text/markup to display after the status
		display:   'status' (default), 'name', 'role', 'email', or 'id'
		logintext:  text to display for logged-out status (when using display="status") default = "logged in"
		logouttext: text to display for logged-in status (when using display="status") default = "logged out"

	See the USP Post "Shortcode Demo" for an example of this shortcode
*/
if (!function_exists('usp_status')) :
function usp_status($attr, $content = null) {
	extract(shortcode_atts(array(
		'before'     => '',
		'after'      => '',
		'display'    => 'status',
		'logintext'  => 'logged in',
		'logouttext' => 'logged out',
	), $attr));
	if (empty($display)) $display = 'status';
	if (empty($logintext)) $logintext = 'logged in';
	if (empty($logouttext)) $logouttext = 'logged out';
	if (is_user_logged_in()) {
		$user = wp_get_current_user();
		if ($user) {
			if ($display == 'status') {
				$status = $logintext;
			} elseif ($display == 'name') {
				$status = $user->display_name;
			} elseif ($display == 'role') {
				foreach ($user->roles as $role) $status = $role . ', ';
				$status = rtrim(trim($status), ',');
			} elseif ($display == 'email') {
				$status = $user->user_email;
			} elseif ($display == 'id') {
				$status = $user->ID;
			} else {
				$status = 'Undefined. Please check shortcode.';	
			}
		}
	} else {
		$status = $logouttext;
	}
	return $before . $status . $after;
}
add_shortcode('usp_status', 'usp_status');
endif;



/*
	Shortcode: display posts from given category or other query (basically a shortcode wrapper for WP's get_posts function)
	Displays posts from specified category using attributes to customize the query
	Syntax: [usp_get_posts posts_per_page="" offset="" category="" orderby="" order="" include="" exclude="" meta_key="" meta_value="" post_type="" post_mime_type="" post_parent="" post_status="" suppress_filters="" before="" after="" classes="" content="" meta=""]
	Parameters: exactly the same as for get_posts() : see @ http://codex.wordpress.org/Template_Tags/get_posts
		Plus:
			before:  text/markup to display before each post (default: none) - Note: use curly brackets {} instead of angle brackets <> for markup
			after:   text/markup to display after each post (default: none) - Note: use curly brackets {} instead of angle brackets <> for markup
			classes: extra CSS classes for outer <div>
			content: display the post content (true or false)
			meta:    display basic post meta (true or false)
			 
*/
if (!function_exists('usp_posts_from_cat')) : 
function usp_get_posts($attr, $content = null) {
	extract(shortcode_atts(array(
		'posts_per_page'   => 5,
		'offset'           => 0,
		'category'         => '',
		'orderby'          => 'post_date',
		'order'            => 'DESC',
		'include'          => '',
		'exclude'          => '',
		'meta_key'         => '',
		'meta_value'       => '',
		'post_type'        => 'post',
		'post_mime_type'   => '',
		'post_parent'      => '',
		'post_status'      => 'publish',
		'suppress_filters' => true,
		//
		'before'  => '',
		'after'   => '',
		'classes' => '',
		'content' => false,
		'meta'    => false
	), $attr));
	$query = array(
		'posts_per_page'   => $posts_per_page,
		'offset'           => $offset,
		'category'         => $category,
		'orderby'          => $orderby,
		'order'            => $order,
		'include'          => $include,
		'exclude'          => $exclude,
		'meta_key'         => $meta_key,
		'meta_value'       => $meta_value,
		'post_type'        => $post_type,
		'post_mime_type'   => $post_mime_type,
		'post_parent'      => $post_parent,
		'post_status'      => $post_status,
		'suppress_filters' => $suppress_filters
	);
	if (!empty($classes)) $classes = ' ' . $classes;
	$args = compact('before', 'after');
	$new = array();
	foreach ($args as $key => $value) {
		$value = str_replace("{", "<", $value);
		$value = str_replace("}", ">", $value);
		if (isset($value)) $new[$key] = $value;
	}
	$usp_posts = '';
	$get_posts = get_posts($query);
	foreach ($get_posts as $post) {
		setup_postdata($post);
		$user_info = get_userdata($post->post_author);
		$user_name = $user_info->display_name;
		$usp_posts .= "\n" . $new['before'] . "\n" . '<div class="usp-get-post'. $classes .'">' . "\n" . '<h2><a href="' . get_permalink($post->ID) .'">' . get_the_title($post->ID) . '</a></h2>' . "\n";
		if ($meta) $usp_posts .= '<p class="usp-get-post-meta">' . the_time('F jS, Y') . esc_html__(' by ', 'usp-pro') . '<a href="' . get_author_posts_url($post->post_author) . '">' . $user_name . '</a>' . '</p>' . "\n";
		if ($content) $usp_posts .= '<div class="usp-get-post-content">' . get_the_content() . '</div>' . "\n";
		$usp_posts .= '</div>' . "\n" .  $new['after'] . "\n";
	}
	wp_reset_postdata();
	return $usp_posts;
}
add_shortcode('usp_get_posts', 'usp_get_posts');
endif;



/* 
	Function: art directed content
	Includes any art-directed content for the current post
	Usage: automatic functionality; just attach any of the following to a post:
		usp-custom-001 = any art-directed CSS (will be wrapped with <style> tags)
		usp-custom-002 = any art-directed JavaScript (will be wrapped with <script> tags)
		usp-custom-000 = any art-directed text/HTML (will not be wrapped with anything)
*/
if (!function_exists('usp_get_art_directed')) : 
function usp_get_art_directed($content) {
	global $post;
	$html = ''; $css = ''; $js = '';
	if (is_object($post)) {
		if (usp_is_submitted($post->ID)) {
			$post_meta = get_post_custom($post->ID);
			foreach ($post_meta as $key => $value) {
				if (preg_match("/^usp-custom-(000|001|002|003)$/i", $key, $match)) {
					foreach ($value as $k => $v) {
						if (!empty($v) && isset($match[1])) {
							$v = htmlspecialchars_decode($v, ENT_QUOTES);
							// $v = nl2br($v);
							if     ($match[1] == '001') $css  .= '<style type="text/css">'. $v .'</style>'. "\n";
							elseif ($match[1] == '002') $js   .= '<script type="text/javascript">'. $v .'</script>'. "\n";
							elseif ($match[1] == '003') $html .= $v . "\n";
							elseif ($match[1] == '000') $html .= $v . "\n"; // supports USP Pro < 2.8
						}
					}
				}
			}
		}
	}
	$art_directed = apply_filters('usp_art_directed', $css . $js . $html, $css, $js, $html);
	return $art_directed . $content;
}
add_filter('the_content', 'usp_get_art_directed');
endif;



/* 
	Functions: display uploaded images with user shortcodes
	Enables the user to specify the location(s) of their uploaded image(s)
	Enable this option in the plugin settings, then tell your visitors about the following user shortcodes:
		[img-1] = first uploaded image
		[img-2] = second uploaded image
		...
*/
if (!function_exists('usp_user_shortcode_img_1') && !shortcode_exists('img-1')) : 
function usp_user_shortcode_img_1($content = null) {
	global $post, $wpdb, $usp_uploads;
	if ($usp_uploads['user_shortcodes']) {
		$return = '<span class="usp-meta-img-1 usp-shortcode-error">[img-1]</span>';
		$postID = $post->ID;
		$key = 'usp-file-1';
		if (usp_is_submitted($postID)) {
			$url = get_post_meta($postID, $key, true);
			if ($url) $return = '<span class="usp-meta-img-1"><img src="'. $url .'" alt="" /></span>';
		}
		return apply_filters('usp_shortcode_img_1', $return);
	}	
}
add_shortcode('img-1', 'usp_user_shortcode_img_1');
endif;

if (!function_exists('usp_user_shortcode_img_2') && !shortcode_exists('img-2')) : 
function usp_user_shortcode_img_2($content = null) {
	global $post, $wpdb, $usp_uploads;
	if ($usp_uploads['user_shortcodes']) {
		$return = '<span class="usp-meta-img-2 usp-shortcode-error">[img-2]</span>';
		$postID = $post->ID;
		$key = 'usp-file-2';
		if (usp_is_submitted($postID)) {
			$url = get_post_meta($postID, $key, true);
			if ($url) $return = '<span class="usp-meta-img-2"><img src="'. $url .'" alt="" /></span>';
		}
		return apply_filters('usp_shortcode_img_2', $return);
	}
}
add_shortcode('img-2', 'usp_user_shortcode_img_2');
endif;

if (!function_exists('usp_user_shortcode_img_3') && !shortcode_exists('img-3')) : 
function usp_user_shortcode_img_3($content = null) {
	global $post, $wpdb, $usp_uploads;
	if ($usp_uploads['user_shortcodes']) {
		$return = '<span class="usp-meta-img-3 usp-shortcode-error">[img-3]</span>';
		$postID = $post->ID;
		$key = 'usp-file-3';
		if (usp_is_submitted($postID)) {
			$url = get_post_meta($postID, $key, true);
			if ($url) $return = '<span class="usp-meta-img-3"><img src="'. $url .'" alt="" /></span>';
		}
		return apply_filters('usp_shortcode_img_3', $return);
	}
}
add_shortcode('img-3', 'usp_user_shortcode_img_3');
endif;

if (!function_exists('usp_user_shortcode_img_4') && !shortcode_exists('img-4')) : 
function usp_user_shortcode_img_4($content = null) {
	global $post, $wpdb, $usp_uploads;
	if ($usp_uploads['user_shortcodes']) {
		$return = '<span class="usp-meta-img-4 usp-shortcode-error">[img-4]</span>';
		$postID = $post->ID;
		$key = 'usp-file-4';
		if (usp_is_submitted($postID)) {
			$url = get_post_meta($postID, $key, true);
			if ($url) $return = '<span class="usp-meta-img-4"><img src="'. $url .'" alt="" /></span>';
		}
		return apply_filters('usp_shortcode_img_4', $return);
	}
}
add_shortcode('img-4', 'usp_user_shortcode_img_4');
endif;

if (!function_exists('usp_user_shortcode_img_5') && !shortcode_exists('img-5')) : 
function usp_user_shortcode_img_5($content = null) {
	global $post, $wpdb, $usp_uploads;
	if ($usp_uploads['user_shortcodes']) {
		$return = '<span class="usp-meta-img-5 usp-shortcode-error">[img-5]</span>';
		$postID = $post->ID;
		$key = 'usp-file-5';
		if (usp_is_submitted($postID)) {
			$url = get_post_meta($postID, $key, true);
			if ($url) $return = '<span class="usp-meta-img-5"><img src="'. $url .'" alt="" /></span>';
		}
		return apply_filters('usp_shortcode_img_5', $return);
	}
}
add_shortcode('img-5', 'usp_user_shortcode_img_5');
endif;



/* 
	Function: user shortcode to display submitted URL
	Enables the user to specify where in the post content the URL should be displayed
	Enable this option in the plugin settings, then tell your visitors about the following user shortcode:
		[url] = the URL specified in the URL field
*/
if (!function_exists('usp_user_shortcode_url') && !shortcode_exists('url')) : 
function usp_user_shortcode_url($content = null) {
	global $post, $wpdb, $usp_uploads;
	if ($usp_uploads['user_shortcodes']) {
		$return = '<span class="usp-user-submit-url usp-shortcode-error">[url]</span>';
		$postID = $post->ID;
		$key = 'usp-url';
		if (usp_is_submitted($postID)) {
			$url = get_post_meta($postID, $key, true);
			if ($url) $return = '<span class="usp-user-submit-url">'. $url .'</span>';
		}
		return apply_filters('usp_shortcode_url', $return);
	}
}
add_shortcode('url', 'usp_user_shortcode_url');
endif;



/* 
	Function: user shortcode to display submitted URL and Title as a link
	Enables the user to specify where in the post content a link to their post should be displayed
	Enable this option in the plugin settings, then tell your visitors about the following user shortcode:
		[link] = a hyperlink using URL field as the href value and the Title field as anchor text
*/
if (!function_exists('usp_user_shortcode_link') && !shortcode_exists('link')) : 
function usp_user_shortcode_link($content = null) {
	global $post, $wpdb, $usp_uploads;
	if ($usp_uploads['user_shortcodes']) {
		$return = '<span class="usp-user-submit-link usp-shortcode-error">[link]</span>';
		$postID = $post->ID;
		if (usp_is_submitted($postID)) {
			$url = get_post_meta($postID, 'usp-url', true);
			$title  = get_the_title($postID);
			if (!empty($url) && !empty($title)) $return = '<a class="usp-user-submit-link" href="'. $url .'">'. $title .'</a>';
		}
		return apply_filters('usp_shortcode_link', $return);
	}
}
add_shortcode('link', 'usp_user_shortcode_link');
endif;



/* 
	Function: user shortcode to display submitted URL and Name as a link
	Enables the user to specify where in the post content a link to their post should be displayed
	Enable this option in the plugin settings, then tell your visitors about the following user shortcode:
		[name] = a hyperlink using URL field as the href value and the Name field as anchor text
*/
if (!function_exists('usp_user_shortcode_name') && !shortcode_exists('name')) : 
function usp_user_shortcode_name($content = null) {
	global $post, $wpdb, $usp_uploads;
	if ($usp_uploads['user_shortcodes']) {
		$return = '<span class="usp-user-submit-name usp-shortcode-error">[name]</span>';
		$postID = $post->ID;
		if (usp_is_submitted($postID)) {
			$name = get_post_meta($postID, 'usp-author', true);
			$url  = get_post_meta($postID, 'usp-url', true);
			if (!empty($name) && !empty($url)) $return = '<a class="usp-user-submit-name" href="'. $url .'">'. $name .'</a>';
		}
		return apply_filters('usp_shortcode_name', $return);
	}
}
add_shortcode('name', 'usp_user_shortcode_name');
endif;



/* 
	Function: user shortcodes to display a link to submitted file(s)
	Enables the user to specify where in the post content a link to submitted file(s) should be displayed
	Enable this option in the plugin settings, then tell your visitors about the following user shortcode:
		[file-1] = link to first uploaded file
		[file-2] = link to second uploaded file
		[file-3] = link to third uploaded file
		(if you need more of these shortcodes, let me know or add your own via your theme's functions.php)
*/
if (!function_exists('usp_user_shortcode_file_1') && !shortcode_exists('file-1')) : 
function usp_user_shortcode_file_1($content = null) {
	global $post, $wpdb, $usp_uploads;
	if ($usp_uploads['user_shortcodes']) {
		$return = '<span class="usp-user-file-1 usp-shortcode-error">[file-1]</span>';
		$postID = $post->ID;
		$key = 'usp-file-1';
		if (usp_is_submitted($postID)) {
			$url = get_post_meta($postID, $key, true);
			if ($url) $return = '<a class="usp-user-file-1" href="'. $url .'">'. esc_html__('File 1', 'usp-pro') .'</a>';
		}
		return apply_filters('usp_shortcode_file_1', $return);
	}
}
add_shortcode('file-1', 'usp_user_shortcode_file_1');
endif;

if (!function_exists('usp_user_shortcode_file_2') && !shortcode_exists('file-2')) : 
function usp_user_shortcode_file_2($content = null) {
	global $post, $wpdb, $usp_uploads;
	if ($usp_uploads['user_shortcodes']) {
		$return = '<span class="usp-user-file-2 usp-shortcode-error">[file-2]</span>';
		$postID = $post->ID;
		$key = 'usp-file-2';
		if (usp_is_submitted($postID)) {
			$url = get_post_meta($postID, $key, true);
			if ($url) $return = '<a class="usp-user-file-2" href="'. $url .'">'. esc_html__('File 2', 'usp-pro') .'</a>';
		}
		return apply_filters('usp_shortcode_file_2', $return);
	}
}
add_shortcode('file-2', 'usp_user_shortcode_file_2');
endif;

if (!function_exists('usp_user_shortcode_file_3') && !shortcode_exists('file-3')) : 
function usp_user_shortcode_file_3($content = null) {
	global $post, $wpdb, $usp_uploads;
	if ($usp_uploads['user_shortcodes']) {
		$return = '<span class="usp-user-file-3 usp-shortcode-error">[file-3]</span>';
		$postID = $post->ID;
		$key = 'usp-file-3';
		if (usp_is_submitted($postID)) {
			$url = get_post_meta($postID, $key, true);
			if ($url) $return = '<a class="usp-user-file-3" href="'. $url .'">'. esc_html__('File 3', 'usp-pro') .'</a>';
		}
		return apply_filters('usp_shortcode_file_3', $return);
	}
}
add_shortcode('file-3', 'usp_user_shortcode_file_3');
endif;


/* 
	Function: user shortcodes to display custom fields (default custom fields: usp-custom-1, usp-custom-2, etc.)
	Enables the user to specify where in the post content the custom field should be displayed
	Enable this option in the plugin settings, then tell your visitors about the following user shortcode:
		[item-1] = link to first uploaded file
		[item-2] = link to second uploaded file
		[item-3] = link to third uploaded file
		(if you need more of these shortcodes, let me know or add your own via your theme's functions.php)
*/
if (!function_exists('usp_user_shortcode_item_1') && !shortcode_exists('item-1')) : 
function usp_user_shortcode_item_1($content = null) {
	global $post, $wpdb, $usp_uploads;
	if ($usp_uploads['user_shortcodes']) {
		$return = '<span class="usp-user-item-1 usp-shortcode-error">[item-1]</span>';
		$postID = $post->ID;
		$key = 'usp-custom-1';
		if (usp_is_submitted($postID)) {
			$item = get_post_meta($postID, $key, true);
			if ($item) $return = '<span class="usp-user-item-1">'. $item .'</span>';
		}
		return apply_filters('usp_shortcode_item_1', $return);
	}
}
add_shortcode('item-1', 'usp_user_shortcode_item_1');
endif;

if (!function_exists('usp_user_shortcode_item_2') && !shortcode_exists('item-2')) : 
function usp_user_shortcode_item_2($content = null) {
	global $post, $wpdb, $usp_uploads;
	if ($usp_uploads['user_shortcodes']) {
		$return = '<span class="usp-user-item-2 usp-shortcode-error">[item-2]</span>';
		$postID = $post->ID;
		$key = 'usp-custom-2';
		if (usp_is_submitted($postID)) {
			$item = get_post_meta($postID, $key, true);
			if ($item) $return = '<span class="usp-user-item-2">'. $item .'</span>';
		}
		return apply_filters('usp_shortcode_item_2', $return);
	}
}
add_shortcode('item-2', 'usp_user_shortcode_item_2');
endif;

if (!function_exists('usp_user_shortcode_item_3') && !shortcode_exists('item-3')) : 
function usp_user_shortcode_item_3($content = null) {
	global $post, $wpdb, $usp_uploads;
	if ($usp_uploads['user_shortcodes']) {
		$return = '<span class="usp-user-item-3 usp-shortcode-error">[item-3]</span>';
		$postID = $post->ID;
		$key = 'usp-custom-3';
		if (usp_is_submitted($postID)) {
			$item = get_post_meta($postID, $key, true);
			if ($item) $return = '<span class="usp-user-item-3">'. $item .'</span>';
		}
		return apply_filters('usp_shortcode_item_3', $return);
	}
}
add_shortcode('item-3', 'usp_user_shortcode_item_3');
endif;


/* 
	Function: display image from URL
	Displays an image anywhere in submitted post content given its URL
	Enable this option in the plugin settings, then tell your visitors about the following user shortcode:
		[image url="http://example.com/image.jpg"]
*/
if (!function_exists('usp_user_shortcode_image') && !shortcode_exists('image')) : 
function usp_user_shortcode_image($attr) {
	global $post, $usp_uploads;
	if ($usp_uploads['user_shortcodes']) {
		$postID = $post->ID;
		$return = '<span class="usp-meta-image usp-shortcode-error">[image]</span>';
		extract(shortcode_atts(array('url' => ''), $attr));
		if (usp_is_submitted($postID) && !empty($attr['url'])) {
			$return = '<span class="usp-meta-image"><img src="'. $attr['url'] .'" alt="" /></span>';
		}
		return apply_filters('usp_shortcode_image', $return);
	}
}
add_shortcode('image', 'usp_user_shortcode_image');
endif;


/*
	Function: shortcode to display a list of all user submitted posts
	Bonus: includes any posts submitted by the free version of USP :)
	
	@ https://plugin-planet.com/usp-pro-display-list-submitted-posts/
	
	Shortcode: [usp_pro_display_posts]
	
	Attributes:
		
		userid
					all         : display submitted posts from all users (default)
					logged      : display submitted posts from current logged-in user
					current     : display submitted posts from author of current archive page
					Author Name : display submitted posts from specified USP Author Name (value of "usp-author" Custom Field)
					Author ID   : display submitted posts from specified WP Author ID (e.g., 1, 2, 3, etc.)
					
		numposts
					integer : displays the specified number of submitted posts (default: display all posts)
					
		display
					title   : displays a list of submitted post titles (default)
					content : displays the submitted post title and content
					excerpt : displays the submitted post title and excerpt
					thumb   : displays only the featured image
					
		loggedin
					false : display submitted posts for all users (default)
					true  : display submitted posts only if user is logged in
					
		status
					publish : display only published submitted posts (default)
					pending : display only pending submitted posts
					draft   : display only draft submitted posts
					etc..   : display any valid Post Status: http://bit.ly/2bbUWFt
					
		modlinks
					false : do not display "edit" and "delete" links for the post (default)
					true  : display "edit" and "delete" links for the post
					
		formid
					integer : limits displayed posts to a specific USP Form (no default)
					
		cat
					integer  : limits displayed posts to a specific category (e.g., 2) (no default)
					integers : limits displayed posts to multiple categories (e.g., 2,6,17,38) 
					current  : limits displayed posts to current category/categories
					
		order
					DESC : orders posts in descending order (default)
					ASC  : orders posts in ascending order
					
		orderby
					param : orders posts by any valid parameter (e.g., ID, author, title, et al) (default = date)
					
		post_type
					any valid post type or 'any' for any post type (default = any)
		
		
	More info @ https://codex.wordpress.org/Class_Reference/WP_Query#Category_Parameters
	
	Examples:
		
		[usp_pro_display_posts userid="1"]                       : displays all submitted posts by registered user with ID = 1
		[usp_pro_display_posts userid="current"]                 : displays all submitted posts by author of current archive page
		[usp_pro_display_posts status="pending" userid="logged"] : displays all pending submitted posts from the current logged-in user
		[usp_pro_display_posts userid="Pat Smith"]               : displays all submitted posts by author name "Pat Smith"
		[usp_pro_display_posts loggedin="true"]                  : displays all submitted posts only to logged-in users
		[usp_pro_display_posts userid="all"]                     : displays all submitted posts by all users/authors
		[usp_pro_display_posts numposts="5"]                     : displays 5 most recent submitted posts
		[usp_pro_display_posts display="excerpt"]                : display excerpts of submitted posts
		
*/
if (!function_exists('usp_pro_display_posts')) : 
function usp_pro_display_posts($attr, $content = null) {
	
	global $post;
	
	extract(shortcode_atts(array(
		
		'userid'    => 'all',
		'numposts'  =>  apply_filters('usp_display_posts_default', 100),
		'display'   => 'title',
		'loggedin'  => 'false',
		'status'    => 'publish',
		'modlinks'  => 'false',
		'formid'    => '',
		'cat'       => '',
		'order'     => '',
		'orderby'   => '',
		'post_type' => 'any',
		
	), $attr));
	
	$display_posts = '';
	
	if ((!is_user_logged_in()) && ($loggedin === 'true' || $userid === 'logged')) return $display_posts;
	
	if (!empty($formid) && is_numeric($formid)) {
		
		$meta_query = array('relation' => 'AND', array('key' => 'is_submission', 'value' => '1'), array('key' => 'usp-form-id', 'value' => $formid));
		
	} else {
		
		$meta_query = array(array('key' => 'is_submission', 'value' => '1'));
		
	}
	
	$cat_key = 'cat';
	$cat_val = $cat;
	
	if ($cat === 'current') {
		
		$cats = wp_get_post_terms($post->ID, 'category', array('fields' => 'ids'));
		
		if (!is_wp_error($cats)) {
			
			$cat_key = 'category__in';
			$cat_val = $cats;
			
		}
		
	}
	
	if (ctype_digit($userid)) {
		
		$args = array(
			'author'         => $userid,
			'posts_per_page' => $numposts,
			'post_type'      => $post_type,
			'post_status'    => $status,
			'meta_query'     => $meta_query,
			$cat_key         => $cat_val,
			'order'          => $order,
			'orderby'        => $orderby,
		);
		
	} elseif ($userid === 'all') {
		
		$args = array(
			'posts_per_page' => $numposts,
			'post_type'      => $post_type,
			'post_status'    => $status,
			'meta_query'     => $meta_query,
			$cat_key         => $cat_val,
			'order'          => $order,
			'orderby'        => $orderby,
		);
		
	} elseif ($userid === 'current') {
		
		the_post(); 
		$userid = get_the_author_meta('ID'); 
		rewind_posts();
		
		$args = array(
			'author'         => $userid,
			'posts_per_page' => $numposts,
			'post_type'      => $post_type,
			'post_status'    => $status,
			'meta_query'     => $meta_query,
			$cat_key         => $cat_val,
			'order'          => $order,
			'orderby'        => $orderby,
		);
		
	} elseif ($userid === 'logged') {
		
		$userid = get_current_user_id();
		
		$args = array(
			'author'         => $userid,
			'posts_per_page' => $numposts,
			'post_type'      => $post_type,
			'post_status'    => $status,
			'meta_query'     => $meta_query,
			$cat_key         => $cat_val,
			'order'          => $order,
			'orderby'        => $orderby,
		);
		
	} else {
		
		$args = array(
			'posts_per_page' => $numposts,
			'post_type'      => $post_type,
			'post_status'    => $status,
			$cat_key         => $cat_val,
			'order'          => $order,
			'orderby'        => $orderby,
			'meta_query' => array(
				'relation' => 'AND',
				array('key' => 'is_submission', 'value' => '1'),
				array('key' => 'usp-author', 'value' => $userid)
			)
		);
		
	}
	
	$args = apply_filters('usp_display_posts_args', $args);
	
	$submitted_posts = get_posts($args);
	
	if (empty($submitted_posts)) return $display_posts;
	
	$display_posts .= '<div class="usp-pro-display-posts">';
	$display_posts .= ($display !== 'content' && $display !== 'excerpt' && $display !== 'thumb') ? '<ul>' : '';
	
	foreach ($submitted_posts as $post) {
		
		setup_postdata($post);
		
		$postid    = get_the_ID();
		$permalink = get_the_permalink();
		$posttitle = get_the_title();
		$excerpt   = get_the_excerpt();
		$content   = strip_shortcodes(get_the_content());
		
		$title     = apply_filters('usp_shortcode_display_posts_title', esc_attr__('View full post', 'usp-pro'));
		$thumb     = get_the_post_thumbnail(get_the_id(), apply_filters('usp_shortcode_display_posts_size', 'thumbnail'));
		
		$editurl   = get_edit_post_link($postid);
		$edittitle = apply_filters('usp_shortcode_display_posts_edit_title', esc_attr__('Edit this post', 'usp-pro'));
		$edittext  = apply_filters('usp_shortcode_display_posts_edit_text', esc_attr__('Edit', 'usp-pro'));
		
		$delurl    = get_delete_post_link($postid);
		$deltitle  = apply_filters('usp_shortcode_display_posts_delete_title', esc_attr__('Delete this post', 'usp-pro'));
		$deltext   = apply_filters('usp_shortcode_display_posts_delete_text', esc_attr__('Delete', 'usp-pro'));
		
		$modsep1   = apply_filters('usp_shortcode_display_posts_modlink_sep1', esc_attr__(' - ', 'usp-pro'));
		$modsep2   = apply_filters('usp_shortcode_display_posts_modlink_sep2', esc_attr__(' ', 'usp-pro'));
		
		$editlink  = '<a target="_blank" rel="noopener noreferrer" href="'. $editurl .'" title="'. $edittitle .'">'. $edittext .'</a>';
		$dellink   = '<a target="_blank" rel="noopener noreferrer" href="'. $delurl  .'" title="'. $deltitle  .'">'. $deltext .'</a>';
		
		$mod_links = ($modlinks === 'true') ? '<span class="usp-pro-display-posts-modlinks">'. $modsep1 . $editlink . $modsep2 . $dellink .'</span>' : '';
		
		$display_posts .= apply_filters('usp_shortcode_display_posts_before', '<div class="usp-pro-display-posts-wrap">', $permalink, $posttitle, $excerpt, $content, $title, $thumb);
		
		if ($display === 'content') {
			
			$display_posts .= '<div class="usp-pro-display-posts-title"><strong><a href="'. $permalink .'" title="'. $title .'">'. $posttitle .'</a></strong>'. $mod_links .'</div>';
			$display_posts .= '<div class="usp-pro-display-posts-content">'. $content .'</div>';
			
		} elseif ($display === 'excerpt') {
			
			$display_posts .= '<div class="usp-pro-display-posts-title"><strong><a href="'. $permalink .'" title="'. $title .'">'. $posttitle .'</a></strong>'. $mod_links .'</div>';
			$display_posts .= '<div class="usp-pro-display-posts-content">'. $excerpt .'</div>';
		
		} elseif ($display === 'thumb') {
			
			$display_posts .= $thumb . $mod_links;
			
		} elseif ($display === 'content+thumb') {
			
			$display_posts .= '<div class="usp-pro-display-posts-title"><strong><a href="'. $permalink .'" title="'. $title .'">'. $posttitle .'</a></strong>'. $mod_links .'</div>';
			$display_posts .= '<div class="usp-pro-display-posts-content">'. $thumb .' '. $content .'</div>';
			
		} elseif ($display === 'excerpt+thumb') {
			
			$display_posts .= '<div class="usp-pro-display-posts-title"><strong><a href="'. $permalink .'" title="'. $title .'">'. $posttitle .'</a></strong>'. $mod_links .'</div>';
			$display_posts .= '<div class="usp-pro-display-posts-content">'. $thumb .' '. $excerpt .'</div>';
			
		} elseif ($display === 'title+thumb') {
			
			$display_posts .= '<li>'. $thumb .' <a href="'. $permalink .'" title="'. $title .'">'. $posttitle .'</a>'. $mod_links .'</li>';
			
		} else {
			
			$display_posts .= '<li><a href="'. $permalink .'" title="'. $title .'">'. $posttitle .'</a>'. $mod_links .'</li>';
		}
		
		$display_posts .= apply_filters('usp_shortcode_display_posts_after', '</div>', $permalink, $posttitle, $excerpt, $content, $title, $thumb);
		
	}
	
	$display_posts .= ($display !== 'content' && $display !== 'excerpt' && $display !== 'thumb') ? '</ul>' : '';
	$display_posts .= '</div>';
	
	wp_reset_postdata();
	
	return $display_posts;
	
}
add_shortcode('usp_pro_display_posts', 'usp_pro_display_posts');
endif;

