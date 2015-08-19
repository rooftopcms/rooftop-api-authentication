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

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-justified-api-authentication-activator.php';

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

    public function api_menu_links() {
        add_options_page("API Overview", "API Overview", "manage_options", $this->plugin_name."-api-overview-page", function(){
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
        });

        add_submenu_page(null, "Add API User", "Add API User", "manage_options", $this->plugin_name."-api-add-user", function(){
            if($_POST){
                if(!isset($_POST['api-field-token']) || !wp_verify_nonce($_POST['api-field-token'], 'justified-api-authentication-api-add-user')) {
                    print 'Form token not verified';
                    exit;
                }

                if(!$_POST['key_name']){
                    echo "Key name required";
                    require_once plugin_dir_path( __FILE__ ) . 'partials/justified-api-authentication-admin-api-add-user.php';
                    return;
                }

                $role = (isset($_POST['key_read_only']) && $_POST['key_read_only']=="on") ? 'api-read-only' : 'api-read-write';

                $api_key_name = $_POST['key_name'];
                $api_username = "$api_key_name API User $role";
                $api_password = md5(uniqid(rand(), true));

                $api_user_id = wp_create_user($api_username, $api_password);
                $api_user = get_userdata($api_user_id);
                $api_user->set_role($role);

                if($api_user_id && Justified_Api_Authentication_Keys::generate_api_key($api_key_name, $api_user_id)) {
                    echo "Added API Key";
                    return;
                }else {
                    echo "Couldn't generate API key - please contact support";
                    return;
                }
            }else {
                require_once plugin_dir_path( __FILE__ ) . 'partials/justified-api-authentication-admin-api-add-user.php';
            }
        });
    }

    /**
     * @param $roles
     * @return mixed
     *
     * When rendering the user-edit form, remove the API specific user roles from the roles that are available in the dropdown.
     *
     * Called via ('option_wp_'.get_current_blog_id().'_user_roles')
     */
    public function filter_api_user_roles($roles) {
        unset($roles['api-read-only']);
        unset($roles['api-read-write']);

        return $roles;
    }

    /**
     * Add the roles required for a valid 'api user' account - these roles shouldn't be visible in the admin area, and
     * are removed by a filter ('option_wp_'.get_current_blog_id().'_user_roles')
     *
     * Called via admin_init
     */
    public function add_api_user_roles($blog_id) {
        $roles_set = get_blog_option($blog_id, "api_roles_added");

        if(!$roles_set){
            add_role("api-read-only", "Read Only API User", array('manage_options'));
            add_role("api-read-write", "Read/Write API User", array('manage_options'));

            update_blog_option($blog_id, "api_roles_added", true);
        }
    }

    public function add_api_key_tables($blog_id){
        Justified_Api_Authentication_Activator::create_database_schema($blog_id);
        return $blog_id;
    }

    public function add_api_roles_and_users($blog_id){
        $api_user_password = wp_generate_password(256, true); //not sent to the user
        $read_only_user_id = wp_create_user("$blog_id API Read only", $api_user_password);
        $read_write_user_id = wp_create_user("$blog_id API Read Write", $api_user_password);

        $read_only_user = new WP_User($read_only_user_id);
        $read_only_user->add_role('api-read-only');
        $read_write_user = new WP_User($read_write_user_id);
        $read_write_user->add_role('api-read-write');

        add_user_to_blog($blog_id, $read_only_user_id, 'api-read-only');
        add_user_to_blog($blog_id, $read_write_user_id, 'api-read-write');
    }

    public function remove_blog_tables($blog_id){
        // fixme: remove the tables we create in Justified_Api_Authentication_Activator
        return $blog_id;
    }

    /**
     * Using the edit_user_profile hook, add the API Keys form to the user edit form if they have the manage_options capability
     */
//    public function add_fields_to_user_admin($user) {
//        if(!current_user_can('manage_options')){
//            wp_die(__("You don't have permissions to manage this page"));
//        }
//
//        $userdata = get_userdata($_GET['user_id']);
//        $required_roles = array('administrator', 'contributer');
//
//        if($userdata && count(count(array_intersect($userdata->roles, $required_roles)))) {
//            $has_api_key = get_user_option("has_api_key", $user->ID);
//            if($has_api_key) {
//                $keys = Justified_Api_Authentication_Keys::get_api_key($user->ID);
//            }
//            require_once plugin_dir_path( __FILE__ ) . 'partials/justified-api-authentication-user-form.php';
//        }
//    }
//
//    public function handle_user_admin_update($user_id) {
//        $is_api_user = array_key_exists("api_user", $_POST) && $_POST["api_user"]=="on";
//        $has_api_key = get_user_option("has_api_key", $user_id);
//
//        if($is_api_user && $is_api_user!=$has_api_key) {
//            // create api keys if is_api_user is true, and we're updating this user option (new value != old value)
//            Justified_Api_Authentication_Keys::generate_api_keys($user_id);
//        }elseif(!$is_api_user && $has_api_key){
//            // delete api keys
//            Justified_Api_Authentication_Keys::delete_api_keys($user_id);
//        }
//    }
}
