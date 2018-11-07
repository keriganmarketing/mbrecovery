<?php // Google reCAPTCHA for PHP < 5.3.0

// PHP Global space backslash class requires >= 5.3.0

// Google reCAPTCHA 2014 version

// Requires allow_url_fopen/file_get_contents

if (!defined('ABSPATH')) die();

require_once('recaptcha-2014.php');

$private = $recaptcha_secret;

if (ini_get('allow_url_fopen')) {
	
	// allow_url_fopen = on
	$recaptcha = new ReCaptcha($private);
	
} else {
	
	$recaptcha = null;
	
	error_log('WP Plugin USP: Google reCAPTCHA: PHP less than 5.3 and allow_url_fopen disabled!', 0);
	
}

if (isset($recaptcha)) {
	
	$response = isset($_POST['g-recaptcha-response']) ? $recaptcha->verifyResponse(usp_get_ip(true), $_POST['g-recaptcha-response']) : null;
	
} else {
	
	$response = null;
	
	error_log('WP Plugin USP: Google reCAPTCHA: $recaptcha variable not set!', 0);
	
}

if ($response->success) {
	
	return true;
	
} else {
	
	$errors = $response->errorCodes;
	
	if (!empty($errors) && is_array($errors)) {
		
		foreach ($errors as  $error) {
			
			error_log('WP Plugin USP: Google reCAPTCHA: '. $error, 0);
			
		}
		
	} else {
		
		error_log('WP Plugin USP: Google reCAPTCHA: '. $errors, 0);
		
	}
	
}

return false;