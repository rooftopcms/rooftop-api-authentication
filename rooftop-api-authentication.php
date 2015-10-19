<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.rooftopcms.com
 * @since             1.0.0
 * @package           Rooftop_Api_Authentication
 *
 * @wordpress-plugin
 * Plugin Name:       Rooftop API Authentication
 * Plugin URI:        https://bitbucket.org/errorstudio/rooftop-api-authentication
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Error Studio
 * Author URI:        https://www.rooftopcms.com
 * License:           MIT
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       rooftop-api-authentication
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-rooftop-api-authentication-activator.php
 */
function activate_Rooftop_Api_Authentication() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rooftop-api-authentication-activator.php';
	Rooftop_Api_Authentication_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-rooftop-api-authentication-deactivator.php
 */
function deactivate_Rooftop_Api_Authentication() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rooftop-api-authentication-deactivator.php';
	Rooftop_Api_Authentication_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_Rooftop_Api_Authentication' );
register_deactivation_hook( __FILE__, 'deactivate_Rooftop_Api_Authentication' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-rooftop-api-authentication.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_Rooftop_Api_Authentication() {

	$plugin = new Rooftop_Api_Authentication();
	$plugin->run();

}
run_Rooftop_Api_Authentication();
