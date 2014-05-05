<?php

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$slug = 'cubiq-add-to-home';

// delete options
delete_option($slug);
$wpdb->query( "OPTIMIZE TABLE `" . $wpdb->prefix . "options`" );

// remove directory
$basepath = wp_upload_dir();
$basepath = $basepath['basedir'] . DIRECTORY_SEPARATOR . $slug . DIRECTORY_SEPARATOR;

if ( is_dir($basepath) ) {
	foreach ( glob($basepath . '*.*') as $v) {
		unlink($v);
	}
	rmdir($basepath);
}

// remove stats table
$wpdb->query( "DROP TABLE `" . $wpdb->prefix . str_replace('-', '_', $slug) . "`" );
