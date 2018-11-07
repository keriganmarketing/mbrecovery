<?php // USP Pro - Default Options

if (!defined('ABSPATH')) die();

function send_mail_options() {
	$send_mail = array(
		'wp_mail' => array(
			'value' => 'wp_mail',
			'label' => esc_html__('WP&rsquo;s', 'usp-pro') .' <code>wp_mail()</code> '. esc_html__('function', 'usp-pro')
		),
		'php_mail' => array(
			'value' => 'php_mail',
			'label' => esc_html__('PHP&rsquo;s', 'usp-pro') .' <code>mail()</code> '. esc_html__('function', 'usp-pro')
		),
		'no_mail' => array(
			'value' => 'no_mail',
			'label' => esc_html__('Disable all email alerts', 'usp-pro')
		),
	);
	return $send_mail;
}

function mail_format() {
	$mail_format = array(
		'text' => array(
			'value' => 'text',
			'label' => esc_html__('Plain-text format', 'usp-pro')
		),
		'html' => array(
			'value' => 'html',
			'label' => esc_html__('HTML format', 'usp-pro')
		),
	);
	return $mail_format;
}

function post_type_options() {
	$post_type = array(
		'post' => array(
			'value' => 'post',
			'label' => esc_html__('Regular WP Posts', 'usp-pro') .' <span class="usp-lite-text">'. esc_html__('(default)', 'usp-pro') .'</span>',
		),
		'page' => array(
			'value' => 'page',
			'label' => esc_html__('Regular WP Pages', 'usp-pro'),
		),
		'usp_post' => array(
			'value' => 'usp_post',
			'label' => esc_html__('USP Posts', 'usp-pro') .' <span class="usp-lite-text">'. esc_html__('(see related setting, &ldquo;Slug for USP Post&rdquo;)', 'usp-pro') .'</span>',
		),
		'other' => array(
			'value' => 'other',
			'label' => esc_html__('Existing Post Type', 'usp-pro') .' <span class="usp-lite-text">'. esc_html__('(see related setting, &ldquo;Slug for Existing Post Type&rdquo;)', 'usp-pro') .'</span>',
		),
	);
	return $post_type;
}

function cats_menu_options() {
	$cats_menu = array(
		'dropdown' => array(
			'value' => 'dropdown',
			'label' => esc_html__('Dropdown/select menu', 'usp-pro')
		),
		'checkbox' => array(
			'value' => 'checkbox',
			'label' => esc_html__('Checkboxes', 'usp-pro')
		),
	);
	return $cats_menu;
}

function tags_menu_options() {
	$tags_menu = array(
		'dropdown' => array(
			'value' => 'dropdown',
			'label' => esc_html__('Dropdown/select menu', 'usp-pro')
		),
		'checkbox' => array(
			'value' => 'checkbox',
			'label' => esc_html__('Checkboxes', 'usp-pro')
		),
		'input' => array(
			'value' => 'input',
			'label' => esc_html__('Text input field', 'usp-pro')
		),
	);
	return $tags_menu;
}

function tags_order_options() {
	$tags_order = array(
		'name_asc' => array(
			'value' => 'name_asc',
			'label' => esc_html__('Name, ascending (default)', 'usp-pro')
		),
		'name_desc' => array(
			'value' => 'name_desc',
			'label' => esc_html__('Name, descending', 'usp-pro')
		),
		'id_asc' => array(
			'value' => 'id_asc',
			'label' => esc_html__('Tag ID, ascending', 'usp-pro')
		),
		'id_desc' => array(
			'value' => 'id_desc',
			'label' => esc_html__('Tag ID, descending', 'usp-pro')
		),
		'count_asc' => array(
			'value' => 'count_asc',
			'label' => esc_html__('Count, ascending', 'usp-pro')
		),
		'count_desc' => array(
			'value' => 'count_desc',
			'label' => esc_html__('Count, descending', 'usp-pro')
		),
	);
	return $tags_order;
}

function style_options() {
	$style_option = array(
		'simple' => array(
			'value' => 'simple',
			'label' => esc_html__('Super simple style (default)', 'usp-pro')
		),
		'minimal' => array(
			'value' => 'minimal',
			'label' => esc_html__('Clean minimal style', 'usp-pro')
		),
		'small' => array(
			'value' => 'small',
			'label' => esc_html__('Smaller form style', 'usp-pro')
		),
		'large' => array(
			'value' => 'large',
			'label' => esc_html__('Larger form style', 'usp-pro')
		),
		'custom' => array(
			'value' => 'custom',
			'label' => esc_html__('Define custom style', 'usp-pro')
		),
		'disable' => array(
			'value' => 'disable',
			'label' => esc_html__('Disable styles', 'usp-pro')
		),
	);
	return $style_option;
}

function display_images_options() {
	$display_images = array(
		'before' => array(
			'value' => 'before',
			'label' => esc_html__('Before post content', 'usp-pro')
		),
		'after' => array(
			'value' => 'after',
			'label' => esc_html__('After post content', 'usp-pro')
		),
		'disable' => array(
			'value' => 'disable',
			'label' => esc_html__('Do not auto-display images', 'usp-pro')
		),
	);
	return $display_images;
}

function display_size_options() {
	$display_sizes = array(
		'thumbnail' => array(
			'value' => 'thumbnail',
			'label' => esc_html__('Thumbnail (default)', 'usp-pro')
		),
		'medium' => array(
			'value' => 'medium',
			'label' => esc_html__('Medium', 'usp-pro')
		),
		'large' => array(
			'value' => 'large',
			'label' => esc_html__('Large', 'usp-pro')
		),
		'full' => array(
			'value' => 'full',
			'label' => esc_html__('Full', 'usp-pro')
		),
	);
	return $display_sizes;
}

function recaptcha_options() {
	$recaptcha = array(
		'v1' => array(
			'value' => 'v1',
			'label' => esc_html__('Version 1 (Original reCAPTCHA)', 'usp-pro')
		),
		'v2' => array(
			'value' => 'v2',
			'label' => esc_html__('Version 2 (noCAPTCHA reCAPTCHA)', 'usp-pro')
		),
	);
	return $recaptcha;
}
