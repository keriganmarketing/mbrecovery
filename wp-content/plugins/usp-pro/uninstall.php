<?php // uninstall remove options

if(!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) exit();

// delete options
delete_option('usp_admin');
delete_option('usp_advanced');
delete_option('usp_general');
delete_option('usp_style');
delete_option('usp_uploads');
delete_option('usp_more');
delete_option('usp_tools');

// delete widgets
delete_option('widget_usp_form_widget');

// delete license
delete_option('usp_license_key');
delete_option('usp_license_status');
