<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://errorstudio.co.uk
 * @since      1.0.0
 *
 * @package    Rooftop_Api_Authentication
 * @subpackage Rooftop_Api_Authentication/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rooftop_Api_Authentication
 * @subpackage Rooftop_Api_Authentication/public
 * @author     Error Studio <info@errorstudio.co.uk>
 */
class Rooftop_Api_Authentication_Public {

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
		 * defined in Rooftop_Api_Authentication_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rooftop_Api_Authentication_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rooftop-api-authentication-public.css', array(), $this->version, 'all' );

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
		 * defined in Rooftop_Api_Authentication_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rooftop_Api_Authentication_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rooftop-api-authentication-public.js', array( 'jquery' ), $this->version, false );

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
        $api_key = array_key_exists('HTTP_API_TOKEN', $_SERVER) ? $_SERVER['HTTP_API_TOKEN'] : null;

        if($api_key) {
            // look up the API key against in the api keys table, along with the request domain
            global $wpdb;

            $table_name = $wpdb->prefix . "api_keys";
            $sql = "SELECT id, key_name, domain, api_key, user_id FROM $table_name WHERE api_key = '$api_key'";
            $result = $wpdb->get_row($sql, OBJECT);

            // set_current_user should return either a valid user ID, or a WP_Error
            if($result) {
                $read_request = ($_SERVER['REQUEST_METHOD'] == "GET" || $_SERVER['REQUEST_METHOD'] == "OPTIONS");
                $api_user = get_userdata($result->user_id);

                $api_user_is_read_only = in_array('api-read-only', $api_user->roles);

                if($api_user_is_read_only && !$read_request){
                    $wp_rest_auth_error = new WP_Error('unauthorized', 'Authentication failed', array('status'=>403));
                }else {
                    $wp_rest_auth_error = $result->user_id;
                }
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

	public function set_preview_user( $user ) {
		global $wpdb;

		// check that we've been given a valid preview key
		$bearer_header = array_key_exists('HTTP_AUTHORIZATION', $_SERVER) ? $_SERVER['HTTP_AUTHORIZATION'] : null;
		preg_match('/Bearer ([^:]+):([^$]+)/', $bearer_header, $matches);
		list($subject, $revision_id, $preview_token) = $matches;

		// only authorise these tokens when querying the graph...
		if( !preg_match('/^\/graphql/', $_SERVER['REQUEST_URI']) ) {
			return $user;
		}

		// no bearer token given
		if( !count($matches)>1 ) {
			return $user;
		}

		$wp_auth_response = null;

		if( $preview_token && $revision_id ) {
			$token = apply_filters( 'rooftop/preview_api_key', $revision_id );

			// if we have a token, we can fetch the user and check they have the api-preview role and return their id
			if($token === $preview_token) {
				$api_user_id = apply_filters( 'rooftop/preview_api_user_id', $revision_id, $preview_token );
				$api_user    = get_userdata( $api_user_id );

				$is_preview_user = in_array( 'api-preview', $api_user->roles );
				$revision        = null;

				try {
					// fixme why wont wp_get_post_revisions work here?
					$revision = $wpdb->get_row("SELECT id FROM wp_posts WHERE post_type = 'revision' AND id = $revision_id");
				}catch(Exception $e) {
				}

				if ( $is_preview_user && $revision ) {
					return $api_user_id;
				}
			}else {
				status_header(403);
			}
		}

		return $wp_auth_response;
	}

    /**
     * @param $error
     * @return mixed
     *
     * Checks the request for the API key and ensures it's valid for the given endpoint.
     * Called after set_current_user.
     */
    public function validate_api_key( $error ) {
        if( !empty( $error ) ) {
            return $error;
        }

        global $wp_rest_auth_error;
        return $wp_rest_auth_error;
    }

    public function add_drafts_query_filters( $request ) {
        $types = get_post_types(array('public' => true));

        foreach($types as $key => $type) {
            add_action( "rest_{$type}_query", array( $this, 'adjust_query_for_drafts' ), 10, 1 );
        }
    }

    /**
     * @param $query_args
     * @return mixed
     *
     * if we're in preview mode, return any post which is visible, or intended to be visible (ie not deleted)
     *
     * note: this rest_post_query is only called on collection endpoints (get_posts), not single resource (get_post(id))
     */
    public function adjust_query_for_drafts( $query_args ) {
        if( apply_filters( 'rooftop_include_drafts', false ) ) {
            $query_args['post_status'] = apply_filters( 'rooftop_published_statuses', array() );
        }

        return $query_args;
    }
}
