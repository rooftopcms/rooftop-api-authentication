<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://errorstudio.co.uk
 * @since      1.0.0
 *
 * @package    Rooftop_Api_Authentication
 * @subpackage Rooftop_Api_Authentication/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Rooftop_Api_Authentication_Keys
 * @subpackage Rooftop_Api_Authentication/includes
 * @author     Error Studio <info@errorstudio.co.uk>
 */
class Rooftop_Api_Authentication_Keys {
    public static function generate_api_key($key_name, $user_id){
        global $wpdb;

        update_user_option($user_id, "has_api_key", true);
        
        $current_key_count_sql = "SELECT COUNT(*) FROM wp_2_api_keys WHERE user_id = $user_id AND key_name = '$key_name';";
        $current_key_count = (int)$wpdb->get_var($current_key_count_sql);

        if($current_key_count != 0){
            wp_die(__("API key not generated - this user already has an API key"));
        }

        $key = self::api_key();
        $table_name = $wpdb->prefix . "api_keys";
        $new_key = array('key_name' => $key_name, 'api_key'=>$key, 'user_id'=>$user_id);
        $inserted = $wpdb->insert($table_name, $new_key, array('%s', '%s', '%s', '%d'));

        return $inserted;
    }
    public static function delete_api_keys($user_id){
        global $wpdb;

        update_user_option($user_id, "has_api_key", false);

        $table_name = $wpdb->prefix . "api_keys";
        $old_key = array('user_id'=>$user_id);
        $wpdb->delete($table_name, $old_key, array('%s', '%d'));
    }
    public static function key_name_exists($key_name) {
        global $wpdb;

        $table_name = $wpdb->prefix . "api_keys";

        $sql = "SELECT COUNT(*) FROM $table_name WHERE key_name = %s";
        $result = $wpdb->get_var($wpdb->prepare($sql, $key_name));

        return $result;
    }

    public static function api_key() {
        $key = md5(uniqid(rand(), true));
        return $key;
    }

    public static function get_api_key($user_id){
        global $wpdb;

        $table_name = $wpdb->prefix . "api_keys";

        $sql = "SELECT domain, api_key FROM $table_name WHERE user_id = $user_id";
        $result = $wpdb->get_results($sql, OBJECT);

        return $result;
    }
}
