<?php

/**
 * Fired during plugin activation
 *
 * @link       http://errorstudio.co.uk
 * @since      1.0.0
 *
 * @package    Rooftop_Api_Authentication
 * @subpackage Rooftop_Api_Authentication/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Rooftop_Api_Authentication
 * @subpackage Rooftop_Api_Authentication/includes
 * @author     Error Studio <info@errorstudio.co.uk>
 */

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

class Rooftop_Api_Authentication_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        // this method isn't  called when used as an mu-plugin
	}

}
