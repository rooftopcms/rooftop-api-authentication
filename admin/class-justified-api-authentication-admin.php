<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://errorstudio.co.uk
 * @since      1.0.0
 *
 * @package    Justified_Api_Authentication
 * @subpackage Justified_Api_Authentication/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Justified_Api_Authentication
 * @subpackage Justified_Api_Authentication/admin
 * @author     Error Studio <info@errorstudio.co.uk>
 */
class Justified_Api_Authentication_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/justified-api-authentication-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/justified-api-authentication-admin.js', array( 'jquery' ), $this->version, false );

	}


    /**
     * Using the edit_user_profile hook, add the API Keys form to the user edit form if they have the manage_options capability
     */
    public function add_fields_to_user_admin($user) {
        if(!current_user_can('manage_options')){
            wp_die(__("You don't have permissions to manage this page"));
        }

        $userdata = get_userdata($_GET['user_id']);
        $required_roles = array('administrator', 'contributer');

        if($userdata && count(count(array_intersect($userdata->roles, $required_roles)))) {
            $has_api_key = get_user_option("has_api_key", $user->ID);
            if($has_api_key) {
                $keys = Justified_Api_Authentication_Keys::get_api_key($user->ID);
            }
            require_once plugin_dir_path( __FILE__ ) . 'partials/justified-api-authentication-user-form.php';
        }
    }

    public function handle_user_admin_update($user_id) {
        $is_api_user = array_key_exists("api_user", $_POST) && $_POST["api_user"]=="on";
        $has_api_key = get_user_option("has_api_key", $user_id);

        if($is_api_user && $is_api_user!=$has_api_key) {
            // create api keys if is_api_user is true, and we're updating this user option (new value != old value)
            Justified_Api_Authentication_Keys::generate_api_keys($user_id);
        }elseif(!$is_api_user && $has_api_key){
            // delete api keys
            Justified_Api_Authentication_Keys::delete_api_keys($user_id);
        }
    }

    public function api_menu_links() {
        // add a top-level admin page
//        add_menu_page("API Admin", "API Admin", "manage-options", $this->plugin_name."-api-admin", array($this, "api_admin_page"));
        add_options_page("API Overview", "API Overview", "manage-options", $this->plugin_name."-api-overview-page", array($this, "justified_api_overview_page"));
    }
    function justified_api_overview_page() {
        global $wpdb;

        $request_domain = $_SERVER['HTTP_HOST'];
        $table_name = $wpdb->prefix . "api_keys";
        $sql = "SELECT domain, api_key, user_id FROM $table_name WHERE domain = '$request_domain';";

        $api_users = array();
        $results = $wpdb->get_results($sql, OBJECT);
        foreach($results as $result) {
            $user = get_userdata($result->user_id);
            $api_users[] = array('id' => $user->ID, 'email' => $user->user_email, 'api_key' => $result->api_key);
        }

        require_once plugin_dir_path( __FILE__ ) . 'partials/justified-api-authentication-admin-api-details.php';
    }
}
