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
        // this method isn't  called when used as an mu-plugin
//        self::update_database_schema();
	}


    /**
     * Create the API Keys table
     *
     * domain - the client site domain name
     * api_key - the api key to authenticate the client request
     */
    public static function create_database_tables($blog_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . "${blog_id}_api_keys";

        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $sql = <<<EOSQL
CREATE TABLE $table_name (
    id MEDIUMINT NOT NULL AUTO_INCREMENT,
    key_name VARCHAR(256) NOT NULL,
    domain VARCHAR(256) NOT NULL,
    api_key VARCHAR(256) NOT NULL,
    user_id INTEGER NOT NULL,
PRIMARY KEY(id),
INDEX(domain),
INDEX(api_key))
EOSQL;

            dbDelta($sql);
        }

    }
    public static function drop_database_tables($blog_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . "${blog_id}_api_keys";
        $sql = <<<EOSQL
DROP TABLE $table_name;
EOSQL;

    }
}
