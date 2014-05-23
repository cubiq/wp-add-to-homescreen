<?php
/*
Plugin Name: Official Add to Homescreen
Plugin URI: http://addtohome.cubiq.org
Description: Official WordPress plugin for <em>Add to Homescreen</em> javascript widget. The plugin opens a call out on iPhone and Chrome for Android inviting the user to add the website to the home screen. This plugin is developed by the same author of the javascript widget, so it is presumably the best way to get the latest updates and bug fixes.
Version: 1.0.3
Author: Matteo Spinelli
Author URI: http://cubiq.org
Text Domain: cubiq-add-to-home
Domain Path: /languages
License: GPL-2.0+
Licence URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

/*
Official Add to Homescreen Wordpress Plugin
Copyright (C) 2014 Matteo Spinelli <matteo@cubiq.org>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}

class Cubiq_Add_To_Home {

	const VERSION = '1.0.3';

	public static function activate () {
		global $wpdb;

		$slug = strtolower( str_replace('_', '-', get_class()) );

		$basepath = wp_upload_dir();
		$basepath = $basepath['basedir'] . DIRECTORY_SEPARATOR . $slug;

		// try to create the application icon directory
		if ( !is_dir($basepath) ) {
			@mkdir($basepath, 0755, true);
		}

		// create the database
		$table = $wpdb->prefix . strtolower( get_class() );
		$sql = "CREATE TABLE $table (
			id int NOT NULL AUTO_INCREMENT,
			time bigint UNSIGNED NOT NULL DEFAULT 0,
			device varchar(16) NOT NULL DEFAULT '',
			action smallint NOT NULL DEFAULT 0,
			PRIMARY KEY  (id),
			KEY time (time)
		);";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public static function deactivate () {

	}

	public static function load_locale () {
		$domain = strtolower( str_replace('_', '-', get_class()) );
		load_plugin_textdomain($domain, false, dirname(plugin_basename(__FILE__)) . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR);
	}

}

$custom_plugin = 'Cubiq_Add_To_Home';

register_activation_hook( __FILE__, array($custom_plugin, 'activate') );
register_deactivation_hook(	__FILE__, array($custom_plugin, 'deactivate') );

if ( !is_admin() ) {		// Public plugin

	require_once( plugin_dir_path( __FILE__ ) . 'includes/public.php' );
	add_action('plugins_loaded', array( $custom_plugin . '_Public', 'get_instance' ));

} else {					// Admin plugin

	require_once( plugin_dir_path( __FILE__ ) . 'includes/admin.php' );

	// Load locale in admin only, so far we don't it in public
	// we are ready to localize, but not just yet...
	//$custom_plugin::load_locale();

	// Register ajax calls
	add_action('wp_ajax_ath_stats', array( $custom_plugin . '_Admin', 'message_stats' ));
	add_action('wp_ajax_nopriv_ath_stats', array( $custom_plugin . '_Admin', 'message_stats' ));

	if ( !defined('DOING_AJAX') || !DOING_AJAX ) {
		add_action('plugins_loaded', array( $custom_plugin . '_Admin', 'get_instance' ));
	}

}