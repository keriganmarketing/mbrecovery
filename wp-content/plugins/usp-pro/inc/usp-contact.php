<?php // USP Pro - Contact

if (!defined('ABSPATH')) die();

function send_email_form($fields, $contact_ids, $custom_content, $post_id) {
	
	global $usp_admin, $usp_advanced, $usp_general;
	
	do_action('usp_send_email_form_before', $fields, $contact_ids, $custom_content, $post_id);
	
	$message_sent  = false;
	$from_prefix   = '';
	$custom_prefix = '';
	
	$from_url      = home_url();
	$charset       = get_option('blog_charset', 'UTF-8');
	
	$send_mail     = $usp_admin['send_mail'];
	$mail_format   = $usp_admin['mail_format'];
	$admin_email   = $usp_admin['admin_email'];
	$from_subject  = $usp_admin['contact_subject'];
	$from_email    = $usp_admin['contact_from'];
	
	if (isset($fields['usp_email']) && !empty($fields['usp_email'])) $from_email = stripslashes($fields['usp_email']);
	
	$from_name = stripslashes($fields['usp_author']);
	$message   = stripslashes($fields['usp_content']);
	
	if ($mail_format == 'html') $message .= '<br />'. "\n\n\n";
	else $message .= "\n\n\n";
	
	if (!empty($usp_admin['contact_sub_prefix'])) $from_prefix   = $usp_admin['contact_sub_prefix'];
	if (!empty($usp_advanced['custom_prefix']))   $custom_prefix = $usp_advanced['custom_prefix'];
	if (!empty($fields['usp_subject']))           $from_subject  = stripslashes($fields['usp_subject']);
	if (!empty($fields['usp_url']))               $from_url      = stripslashes($fields['usp_url']);
	
	if (empty($post_id)) $post_id = esc_html__('undefined', 'usp-pro');
	
	$args = USP_Pro_Process::get_email_info($post_id);
	
	$from_subject = htmlspecialchars_decode($from_subject, ENT_QUOTES);
	$from_subject = USP_Pro_Process::regex_filter($from_subject, $args);
	
	// custom content
	if (isset($custom_content) && !empty($custom_content)) {
		if ($mail_format == 'html') $message .= '<hr /></br />';
		$message .= USP_Pro_Process::regex_filter($custom_content, $args);
		
		if ($mail_format == 'html') $message .= '<br />'. "\n\n\n";
		else $message .= "\n\n\n";
	}
	
	// custom fields
	if ($usp_admin['contact_custom']) {
		$print_fields = array();
		foreach ($fields as $keys => $values) {
			if (is_array($values)) {
				foreach ($values as $key => $value) {
					if (is_array($value)) {
						foreach ($value as $k => $v) {
							if (empty($k)) $k = $key;
							if (!empty($v)) $print_fields[$k] = $v;
						}
					} else {
						if (empty($key)) $key = $keys;
						if (!empty($value)) $print_fields[$key] = $value;
					}
				}
			} else {
				if (!empty($values)) $print_fields[$keys] = $values;
			}
		}
		$custom_fields = '';
		$add_custom = false;
		
		if ($mail_format == 'html') $custom_fields = '<ul>';
		
		$print_fields = apply_filters('usp_send_email_print_fields', $print_fields);
		$custom_custom = usp_merge_custom_fields();
		
		foreach ($print_fields as $key => $value) {
			
			$m1 = ''; $m2 = array(); $m3 = array(); $m4 = array();
			
			if (in_array($key, $custom_custom)) {
				$m1 = $key;
			} else {
				preg_match("/^usp-custom-([0-9a-z_-]+)$/i", $key, $m2);
				preg_match("/^$custom_prefix([0-9a-z_-]+)?$/i", $key, $m3);
				preg_match("/^usp_(nicename|displayname|nickname|firstname|lastname|description|password)/i", $key, $m4);
			}
			
			if     (isset($m1) && !empty($m1)) $match = $m1;
			elseif (isset($m2) && !empty($m2)) $match = $m2[1];
			elseif (isset($m3) && !empty($m3)) $match = $m3[1];
			elseif (isset($m4) && !empty($m4)) $match = $m4[1];
			else $match = '';
			
			if (!empty($match)) {
				if     (isset($usp_advanced['usp_label_c'. $match]) && !empty($usp_advanced['usp_label_c'. $match])) $key = $usp_advanced['usp_label_c'. $match];
				elseif (isset($usp_advanced['usp_custom_label_'. $match]) && !empty($usp_advanced['usp_custom_label_'. $match])) $key = $usp_advanced['usp_custom_label_'. $match];
				else $key = $match;
				
				if (is_array($value)) {
					foreach ($value as $val) {
						if ($mail_format == 'html') $custom_fields .= '<li><strong>'. ucwords($key) .'</strong> : '. stripslashes(htmlspecialchars_decode($val, ENT_QUOTES)) .'</li>';
						else                        $custom_fields .= ucwords($key) .' : '. stripslashes(htmlspecialchars_decode($val, ENT_QUOTES)) ."\n\n";
					}
				} else {
						if ($mail_format == 'html') $custom_fields .= '<li><strong>'. ucwords($key) .'</strong> : '. stripslashes(htmlspecialchars_decode($value, ENT_QUOTES)) .'</li>';
						else                        $custom_fields .= ucwords($key) .' : '. stripslashes(htmlspecialchars_decode($value, ENT_QUOTES)) ."\n\n";
				}
				if (!empty($custom_fields)) $add_custom = true;
			}
		}
		if ($mail_format == 'html') $custom_fields .= '</ul><br />' . "\n\n\n";
		else $custom_fields .= "\n";
		
		if ($add_custom) {
			if ($mail_format == 'html') $custom_heading = '<hr /></br /><p><strong>'. esc_html__('Additional Information', 'usp-pro') .'</strong></p>';
			else                        $custom_heading = esc_html__('Additional Information', 'usp-pro') . "\n" . esc_html__('----------------------', 'usp-pro') . "\n\n";
			
			$custom_heading = apply_filters('usp_send_email_custom_heading', $custom_heading);
			$custom_fields  = apply_filters('usp_send_email_custom_fields', $custom_fields);
			$message .= $custom_heading . $custom_fields;
		}
	}
	
	// user stats
	if ($usp_admin['contact_stats']) {
		$stats = USP_Pro_Process::get_user_stats();
		
		if ($mail_format == 'html') {
			$message .= '<hr /></br /><p><strong>'. esc_html__('Message Details', 'usp-pro') .'</strong></p>';
			$message .= '<ul>';
			$message .= '<li><strong>Name</strong> : '       . $from_name .'</li>';
			$message .= '<li><strong>Email</strong> : '      . $from_email .'</li>';
			$message .= '<li><strong>URL</strong> : '        . $from_url .'</li>';
			$message .= '<li><strong>Time</strong> : '       . $stats['usp-time'] .'</li>';
			$message .= '<li><strong>IP Address</strong> : ' . $stats['usp-address'] .'</li>';
			$message .= '<li><strong>Request</strong> : '    . $stats['usp-request'] .'</li>';
			$message .= '<li><strong>Referrer</strong> : '   . $stats['usp-referer'] .'</li>';
			$message .= '<li><strong>User Agent</strong> : ' . $stats['usp-agent'] .'</li>';
			$message .= '</ul><br />';
		} else {
			$message .=  esc_html__('Message Details', 'usp-pro') . "\n" . esc_html__('---------------', 'usp-pro') . "\n\n";
			$message .= 'Name: '. $from_name . "\n" .'Email: '. $from_email . "\n" .'URL: '. $from_url . "\n";
			$message .= 'Time: '. $stats['usp-time'] . "\n" .'IP Address: '. $stats['usp-address'] . "\n" .'Request: '. $stats['usp-request'] . "\n";
			$message .= 'Referrer: '. $stats['usp-referer'] . "\n" .'User Agent: '. $stats['usp-agent'] . "\n";
		}
	}
	$message = apply_filters('usp_send_email_message', $message);
	
	// headers
	$headers  = 'X-Mailer: USP Pro'. "\n";
	$headers .= 'From: '. $from_name .' <'. $from_email .'>'. "\n";
	$headers .= 'Reply-To: '. $from_name .' <'. $from_email .'>'. "\n";
	
	if ($usp_admin['contact_cc_user']) {
		$headers .= 'Cc: '. $from_email . "\n";
	}
	if ($usp_admin['contact_cc'] !== '') {
		$cc_emails = trim($usp_admin['contact_cc']);
		$cc_emails = explode(',', $cc_emails);
		foreach ($cc_emails as $email) $headers .= 'Bcc: '. trim($email) . "\n";
	}
	if ($mail_format == 'html') {
		$headers .= 'Content-Type: text/html; charset='. $charset . "\n";
	} else {
		$headers .= 'Content-Type: text/plain; charset='. $charset . "\n";
	}
	
	// recipients
	$contact_emails = array();
	$contact_ids = trim($contact_ids);
	$ids_array = explode(",", $contact_ids);
	
	foreach ($ids_array as $id) {
		$id = trim($id);
		if (!empty($usp_admin['custom_contact_'. $id])) $contact_emails[] = $usp_admin['custom_contact_'. $id];
	}
	
	// send mail
	if (empty($contact_emails)) {
		if ($send_mail == 'wp_mail') {
			$message_sent = wp_mail($admin_email, $from_prefix . $from_subject, $message, $headers);
		} else {
			if (mail($admin_email, $from_prefix . $from_subject, $message, $headers)) $message_sent = true;
		}
	} else {
		foreach ($contact_emails as $contact_email) {
			
			do_action('usp_send_email_form_during', $contact_email, $from_prefix, $from_subject, $message, $headers);
			
			if ($send_mail == 'wp_mail') {
				$message_sent = wp_mail($contact_email, $from_prefix . $from_subject, $message, $headers);
			} else {
				if (mail($contact_email, $from_prefix . $from_subject, $message, $headers)) $message_sent = true;
			}
		}
	}
	return apply_filters('usp_send_email_form', $message_sent);
}


