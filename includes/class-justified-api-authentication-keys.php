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
 * @package    Justified_Api_Authentication
 * @subpackage Justified_Api_Authentication/includes
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
 * @package    Justified_Api_Authentication_Keys
 * @subpackage Justified_Api_Authentication/includes
 * @author     Error Studio <info@errorstudio.co.uk>
 */
class Justified_Api_Authentication_Keys {
    public static function generate_api_keys($user_id){
        global $wpdb;

        update_user_option($user_id, "has_api_key", true);
        $blog = self::get_blog($user_id);
        $domain = $blog->domain;

        $current_key_count_sql = "SELECT COUNT(*) FROM wp_2_api_keys WHERE domain = '$domain' AND user_id = $user_id;";
        $current_key_count = (int)$wpdb->get_var($current_key_count_sql);
        if($current_key_count != 0){
            wp_die(__("API key not generated - this user already has an API key"));
        }

        $key = self::api_key();
        $table_name = $wpdb->prefix . "api_keys";
        $new_key = array('domain'=>$domain, 'api_key'=>$key, 'user_id'=>$user_id);
        $wpdb->insert($table_name, $new_key, array('%s', '%s', '%d'));
    }
    public static function delete_api_keys($user_id){
        global $wpdb;

        update_user_option($user_id, "has_api_key", false);

        $blog = self::get_blog($user_id);
        $domain = $blog->domain;

        $table_name = $wpdb->prefix . "api_keys";
        $old_key = array('domain'=>$domain, 'user_id'=>$user_id);
        $wpdb->delete($table_name, $old_key, array('%s', '%d'));
    }

    public static function api_key() {
        $key = md5(uniqid(rand(), true));
        return $key;
    }

    public static function get_api_key($user_id){
        global $wpdb;

        $blog = self::get_blog($user_id);
        $domain = $blog->domain;
        $table_name = $wpdb->prefix . "api_keys";

        $sql = "SELECT domain, api_key FROM $table_name WHERE user_id = $user_id";
        $result = $wpdb->get_results($sql, OBJECT);

        return $result;
    }

    public static function get_blog($user_id) {
        $blogs = get_blogs_of_user($user_id);
        // if the user is associated with more than one site, we should assume it's an internal administrator(with no API keys), rather than a site user
        if(count($blogs)!=1) {
            wp_die(__("This user is associated with more than 1 site. Can't create API keys for these users"));
        }

        $blog = array_values($blogs)[0];
        return $blog;
    }
}
