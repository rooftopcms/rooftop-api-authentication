<?php

/**
 * Fired during plugin activation
 *
 * @link       http://errorstudio.co.uk
 * @since      1.0.0
 *
 * @package    Justified_Api_Authentication
 * @subpackage Justified_Api_Authentication/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Justified_Api_Authentication
 * @subpackage Justified_Api_Authentication/includes
 * @author     Error Studio <info@errorstudio.co.uk>
 */

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

class Justified_Api_Authentication_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        self::update_database_schema();
	}


    /**
     * Create the API Keys table
     *
     * domain - the client site domain name
     * api_key - the api key to authenticate the client request
     */
    public static function update_database_schema() {
        global $wpdb;

        $table_name = $wpdb->prefix . "api_keys";

        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
            $sql = <<<EOSQL
CREATE TABLE $table_name (
    domain VARCHAR(256) NOT NULL,
    api_key VARCHAR(256) NOT NULL,
    user_id INTEGER NOT NULL,
INDEX(domain),
INDEX(api_key))
EOSQL;

            dbDelta($sql);
        }

    }
}
