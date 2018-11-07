<?php // USP Pro - Dashboard Widget

if (!defined('ABSPATH')) die();

function usp_pro_dashboard_widget_draft_posts() {
	
	global $post, $usp_advanced;
	
	$userid = apply_filters('usp_widget_drafts_user', get_current_user_id());
	
	$posts_per_page = apply_filters('usp_widget_drafts_number', -1);
	
	$post_types = apply_filters('usp_widget_drafts_type', array('post', 'page', 'usp_post'));
	
	$existing_post_type = (isset($usp_advanced['other_type']) && !empty($usp_advanced['other_type'])) ? $usp_advanced['other_type'] : null;
	
	if (post_type_exists($existing_post_type)) $post_types[] = $existing_post_type;
	
	$post_status = apply_filters('usp_widget_drafts_status', array('draft', 'future', 'pending'));
	
	$args = array(
		'author'         => $userid,
		'posts_per_page' => $posts_per_page,
		'post_type'      => $post_types,
		'post_status'    => $post_status,
		'meta_key'       => 'is_submission',
		'meta_value'     => '1'
	);
	
	$submitted_posts = get_posts($args);
	$post_count = count($submitted_posts);
	
	echo '<style>';
	echo '.metabox-prefs .usp-pro-dashboard-widget { display: none; } ';
	echo '.postbox .usp-pro-dashboard-widget { padding-left: 5px; color: #ccc; font-size: 90%; font-weight: lighter; }';
	echo '</style>';
	
	if (isset($post_count) && (int) $post_count > 0) {
		
		echo '<p>'. esc_html__('Submitted posts waiting to be published:', 'usp-pro') .'</p>';
		echo '<ul>';
		
		foreach ($submitted_posts as $post) {
			
			setup_postdata($post);
			
			$post_author    = get_the_author();
			$post_title     = get_the_title();
			$post_edit      = get_edit_post_link();
			
			$post_schedule  = apply_filters('usp_widget_drafts_time', get_the_time('l, F j, Y @ h:i:s a'));
			$post_date      = get_post_meta(get_the_ID(), 'usp-post-time', true) ? get_post_meta(get_the_ID(), 'usp-post-time', true) : $post_schedule;
			$post_status    = get_post_status() === 'future' ? esc_html__(' Scheduled: ', 'usp-pro') . $post_schedule : ucfirst(get_post_status());
			
			$post_type      = get_post_type();
			$post_type_obj  = get_post_type_object($post_type);
			$post_type_name = is_object($post_type_obj) ? $post_type_obj->labels->singular_name : ucwords(str_replace('_', ' ', $post_type));
			$post_type_name = strcasecmp($post_type_name, 'usp post') == 0 ? 'USP Post' : $post_type_name;
			
			if (current_user_can('edit_posts') && !empty($post_edit)) {
				
				$post_link = '<a href="'. $post_edit .'" title="'. esc_attr__('Edit this post', 'usp-pro') .'">'. $post_title .'</a>';
				
			} else {
				
				$post_link = $post_title;
				
			}
			
			echo '<li>';
			echo '<strong>'. $post_link .'</strong> &ndash; <small><em>'. $post_status .'</em></small><br />';
			echo '<small>'. $post_type_name . esc_html__(' submitted by ', 'usp-pro') . $post_author . esc_html__(' on ', 'usp-pro') . $post_date .'</small>';
			echo '</li>';
			
		}
		
		echo '</ul>';
		
		wp_reset_postdata();
		
	} else {
		
		echo '<p>'. esc_html__('No submitted posts waiting to be published.', 'usp-pro') .'</p>';
		
	}
	
}



function usp_pro_dashboard_widgets() {
	
	wp_add_dashboard_widget(
		'usp_pro_dashboard_widget_draft_posts', 
		esc_html__('Submitted Post Queue', 'usp-pro') .' <span class="usp-pro-dashboard-widget">USP Pro</span>', 
		'usp_pro_dashboard_widget_draft_posts'
	);
	
}
add_action('wp_dashboard_setup', 'usp_pro_dashboard_widgets');



function usp_pro_add_glance_items() {
	
	global $post, $usp_advanced;
	
	$post_types = array(
		'post'     => __('Submitted Post',     'usp-pro'),
		'page'     => __('Submitted Page',     'usp-pro'),
		'usp_post' => __('Submitted USP Post', 'usp-pro'),
	);
	
	$existing_post_type = (isset($usp_advanced['other_type']) && !empty($usp_advanced['other_type'])) ? $usp_advanced['other_type'] : null;
	
	if (post_type_exists($existing_post_type)) {
		
		$post_type_object = get_post_type_object($existing_post_type);
		
		$existing_label = $post_type_object->labels->singular_name;
		
		$post_types[$existing_post_type] = $existing_label;
		
	}
	
	$post_types = apply_filters('usp_widget_submitted_type', $post_types);
	
	$post_status = apply_filters('usp_widget_submitted_status', array('draft', 'future', 'pending', 'publish'));
	
	foreach ($post_types as $post_type => $post_name) {
		
		if (!$post_type || !$post_name) continue;
		
		$count = 0;
		
		$args = array(
			'posts_per_page' => -1,
			'post_type'      => $post_type,
			'post_status'    => $post_status,
		);
		
		$posts = get_posts($args);
		
		if (!$posts) continue;
		
	    foreach ($posts as $post) {
			
			if (usp_is_submitted($post->ID)) $count++;
			
		}
		
		if (!$count) continue;
		
		$count = number_format_i18n($count);
		
		$post_name = ($count == 1) ? $post_name : $post_name .'s';
		
		$link = '<a href="'. admin_url('edit.php?post_type='. esc_attr($post_type)) .'">'. esc_html($count) .' '. esc_html($post_name) .'</a>';
		
		echo '<li class="post-count '. esc_attr($post_type) .'-count">'. $link .'</li>';
		
	}
	
}
add_action('dashboard_glance_items', 'usp_pro_add_glance_items');
