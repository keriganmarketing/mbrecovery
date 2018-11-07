<?php // USP Pro - Utility Functions

if (!defined('ABSPATH')) die();

function blank_or_zero($string) {
	
	return (is_null($string) || ($string === ''));
	
}
