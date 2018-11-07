<?php // Google reCAPTCHA for PHP >= 5.2

// Google reCAPTCHA 2007 version

// Requires fsockopen()

if (!defined('ABSPATH')) die();

require_once('recaptcha-2007.php');

$private = $recaptcha_secret;

if (function_exists('fsockopen')) {
	
	$response  = isset($_POST['recaptcha_response_field'])  ? $_POST['recaptcha_response_field']  : null;
	$challenge = isset($_POST['recaptcha_challenge_field']) ? $_POST['recaptcha_challenge_field'] : null;
	
	$recaptcha = recaptcha_check_answer($private, usp_get_ip(true), $challenge, $response);
	
} else {
	
	$recaptcha = null;
	
	error_log('WP Plugin USP: Google reCAPTCHA: fsockopen() does not exist!', 0);
	
}

if ($recaptcha->is_valid) {
	
	return true;
	
} else {
	
	$errors = $recaptcha->error;
	
	if (!empty($errors) && is_array($errors)) {
		
		foreach ($errors as  $error) {
			
			error_log('WP Plugin USP: Google reCAPTCHA: '. $error, 0);
			
		}
		
	} else {
		
		error_log('WP Plugin USP: Google reCAPTCHA: '. $errors, 0);
		
	}
	
}

return false;