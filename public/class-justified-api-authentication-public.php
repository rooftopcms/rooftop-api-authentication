<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://errorstudio.co.uk
 * @since      1.0.0
 *
 * @package    Justified_Api_Authentication
 * @subpackage Justified_Api_Authentication/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Justified_Api_Authentication
 * @subpackage Justified_Api_Authentication/public
 * @author     Error Studio <info@errorstudio.co.uk>
 */
class Justified_Api_Authentication_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

//        $debug_tags = array();
//        add_action( 'all', function ( $tag ) {
//            global $debug_tags;
//            if ( in_array( $tag, $debug_tags ) ) {
//                return;
//            }
//            echo "<pre>" . $tag . "</pre>";
//            $debug_tags[] = $tag;
//        } );
//        print_r($debug_tags);
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Justified_Api_Authentication_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Justified_Api_Authentication_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/justified-api-authentication-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Justified_Api_Authentication_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Justified_Api_Authentication_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/justified-api-authentication-public.js', array( 'jquery' ), $this->version, false );

	}


    /**
     *
     * Return the user ID of the user associated with the given API key, or raise a WP_Error
     * which is later returned by the validate_api_key hook
     */
    public function set_current_user($user) {
        global $wp_rest_auth_error;
        $wp_rest_auth_error = null;

        /**
         * return if we already have a user via a wordpress session.
         * if we dont want logged in users to have access without an API key, then we should check
         * whether we're hitting a rest enpoint or a regular WP-Admin page
         */
        if (!empty($user)) {
            return $user;
        }

        remove_filter( 'determine_current_user', 'json_basic_auth_handler', 20 );

        // check that we've been given an api key header
        $request_domain = $_SERVER['HTTP_HOST'];
        $api_key = array_key_exists('HTTP_API_KEY', $_SERVER) ? $_SERVER['HTTP_API_KEY'] : null;

        if($api_key) {
            // look up the API key against in the api keys table, along with the request domain
            global $wpdb;

            $table_name = $wpdb->prefix . "api_keys";
            $sql = "SELECT domain, api_key, user_id FROM $table_name WHERE domain = '$request_domain' AND api_key = '$api_key'";
            $result = $wpdb->get_row($sql, OBJECT);



            // set_current_user should return either a valid user ID, or a WP_Error
            if($result) {
                $wp_rest_auth_error = $result->user_id;
            }else {
                // we were given a key, but it's wrong, or invalid for this domain
                $wp_rest_auth_error = new WP_Error('unauthorized', 'Authentication failed', array('status'=>403));
            }
        }else {
            // no key given
            $wp_rest_auth_error = new WP_Error('forbidden', 'Authentication failed', array('status'=>401));
        }

        add_filter('determine_current_user', 'json_basic_auth_handler', 20);

        if (is_wp_error($wp_rest_auth_error)) {
            return null;
        }
        return $wp_rest_auth_error;
    }

    /**
     * @param $error
     * @return mixed
     *
     * Checks the request for the API key and ensures it's valid for the given endpoint.
     * Called after set_current_user.
     */
    public function validate_api_key($error){
        if(!empty($error)){
            return $error;
        }

        global $wp_rest_auth_error;
        return $wp_rest_auth_error;
    }

    /**
     * @param $query_args
     * @return mixed
     *
     * if we're in preview mode, return any post which is visible, or intended to be visible (ie not deleted)
     */
    public function adjust_query_for_preview_mode($query_args){
        if(defined("JUSTIFIED_PREVIEW_MODE") && true == JUSTIFIED_PREVIEW_MODE) {
            $query_args['post_status'] = array('publish', 'draft', 'scheduled', 'pending');
        }

        return $query_args;
    }
}
