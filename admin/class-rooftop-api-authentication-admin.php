<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://errorstudio.co.uk
 * @since      1.0.0
 *
 * @package    Rooftop_Api_Authentication
 * @subpackage Rooftop_Api_Authentication/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rooftop_Api_Authentication
 * @subpackage Rooftop_Api_Authentication/admin
 * @author     Error Studio <info@errorstudio.co.uk>
 */

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rooftop-api-authentication-activator.php';

class Rooftop_Api_Authentication_Admin {

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
		 * defined in Rooftop_Api_Authentication_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rooftop_Api_Authentication_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rooftop-api-authentication-admin.css', array(), $this->version, 'all' );

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
		 * defined in Rooftop_Api_Authentication_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rooftop_Api_Authentication_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rooftop-api-authentication-admin.js', array( 'jquery' ), $this->version, false );

	}

    public function api_menu_links() {
        $rooftop_api_menu_slug = "rooftop-api-authentication-overview";

        add_menu_page("Rooftop CMS", "Rooftop sCMS", "manage_options", $rooftop_api_menu_slug, function(){
        });
        add_submenu_page($this->plugin_name."-overview", "API Keys", "API Keys", "manage_options", $this->plugin_name."-overview", function() {
            if($_POST && array_key_exists('method', $_POST)) {
                $method = strtoupper($_POST['method']);
            }elseif($_POST && array_key_exists('id', $_POST)){
                $method = 'PATCH';
            }else {
                $method = $_SERVER['REQUEST_METHOD'];
            }

            switch($method) {
                case 'GET':
                    if(!array_key_exists('id', $_GET) && !array_key_exists('new', $_GET)){
                        $this->api_keys_index();
                    }elseif(array_key_exists('new', $_GET)){
                        $this->api_keys_edit();
                    }elseif(array_key_exists('id', $_GET)) {
                        $this->api_keys_show();
                    }
                    break;
                case 'POST':
                    $this->api_keys_create();
                    break;
                case 'DELETE':
                    $this->api_keys_delete();
                    break;
            }
        });
    }

    public function api_keys_index() {
        global $wpdb;

        $request_domain = $_SERVER['HTTP_HOST'];
        $table_name = $wpdb->prefix . "api_keys";

        $api_keys = array();
        $sql = "SELECT id, key_name, domain, api_key, user_id FROM $table_name WHERE domain = '$request_domain';";
        $results = $wpdb->get_results($sql, OBJECT);
        foreach($results as $result) {
            $key_user = get_userdata($result->user_id);
            $api_keys[] = array('id' => $result->id, 'user' => $key_user, 'key_name' => $result->key_name, 'api_key' => $result->api_key);
        }

        $api_keys = array_reverse($api_keys);
        require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-api-authentication-admin-api-index.php';
    }

    public function api_keys_edit() {
        require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-api-authentication-admin-api-new.php';
    }

    public function api_keys_show() {
        global $wpdb;

        $table_name = $wpdb->prefix . "api_keys";
        $blog = get_blog_details(get_current_blog_id());
        $api_key = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE domain = %s and id = %d", array($blog->domain, $_GET['id'])));

        require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-api-authentication-admin-api-show.php';
    }

    public function api_keys_create() {
        if(!isset($_POST['api-field-token']) || !wp_verify_nonce($_POST['api-field-token'], 'rooftop-api-authentication-api-add-key')) {
            print '<div class="wrap"><div class="errors"><p>Form token not verified</p></div></div>';
            exit;
        }

        $blog = get_blog_details(get_current_blog_id());
        $new_key_name = $_POST['key_name'];
        if(!$new_key_name || Rooftop_Api_Authentication_Keys::key_name_exists($new_key_name, $blog)){
            echo '<div class="wrap"><div class="errors"><p>Unique key name required</p></div></div>';
            require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-api-authentication-admin-api-new.php';
            return;
        }

        $role = (isset($_POST['key_read_only']) && $_POST['key_read_only']=="on") ? get_role('api-read-only') : get_role('api-read-write');

        $blog_id = get_current_blog_id();
        $api_key_name = $_POST['key_name'];
        $api_username = "API USER $blog_id $api_key_name";
        $api_password = md5(uniqid(rand(), true));

        $api_user_id = wp_create_user($api_username, $api_password);
        $api_user = get_userdata($api_user_id);
        $api_user->add_role($role->name);
        add_user_to_blog($blog_id, $api_user_id, $role->name);

        if($api_user_id && Rooftop_Api_Authentication_Keys::generate_api_key($api_key_name, $api_user_id)) {
            echo '<div class="wrap"><p>New key added</p></div>';
            $this->api_keys_index();
            return;
        }else {
            echo "Couldn't generate API key - please contact support";
            return;
        }
    }

    public function api_keys_delete() {
        global $wpdb;

        $table_name = $wpdb->prefix . "api_keys";
        $blog = get_blog_details(get_current_blog_id());
        $api_key = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE domain = %s and id = %d", array($blog->domain, $_GET['id'])));

        if(!isset($_POST['api-field-token']) || !wp_verify_nonce($_POST['api-field-token'], 'rooftop-api-authentication-api-view-key')) {
            print '<div class="wrap"><div class="errors"><p>Form token not verified</p></div></div>';
            exit;
        }

        if($api_key) {
            // delete the key and the user account that we created to be associated with it
            $wpdb->delete($table_name, array('id' => $_GET['id']));
            do_action('delete_user', $api_key->user_id, null);

            print '<div class="wrap"><div class="errors"><p>Key Deleted</p> </div></div>';
        }else {
            print '<div class="wrap"><div class="errors"><p>Key not found</p></div></div>';
        }

        $this->api_keys_index();
    }

    /**
     * Add the roles required for a valid 'api user' account - these roles shouldn't be visible in the admin area, and
     * are removed by a filter ('option_wp_'.get_current_blog_id().'_user_roles')
     *
     * Called via admin_init
     */
    public function add_api_user_roles($blog_id) {
        $roles_set = get_option("api_roles_added");

        if(!$roles_set){
            add_role("api-read-only", "Read Only API User", array(
                'edit_others_attachments' => true,
                'edit_others_pages' => true,
                'edit_others_posts' => true,
                'read' => true
            ));

            add_role("api-read-write", "Read/Write API User", array(
                'delete_attachments' => true,
                'delete_others_attachments' => true,
                'delete_others_pages' => true,
                'delete_others_posts' => true,
                'delete_pages' => true,
                'delete_posts' => true,
                'delete_private_pages' => true,
                'delete_private_posts' => true,
                'delete_published_pages' => true,
                'delete_published_posts' => true,
                'edit_attachments' => true,
                'edit_others_attachments' => true,
                'edit_others_pages' => true,
                'edit_others_posts' => true,
                'edit_pages' => true,
                'edit_posts' => true,
                'edit_private_pages' => true,
                'edit_private_posts' => true,
                'edit_published_pages' => true,
                'edit_published_posts' => true,
                'manage_categories' => true,
                'publish_pages' => true,
                'publish_posts' => true,
                'read' => true,
                'read_others_attachments' => true,
                'read_private_pages' => true,
                'read_private_posts' => true,
                'unfiltered_html' => true,
                'upload_files' => true
            ));

            update_option("api_roles_added", true);
        }
    }

    /**
     * @param $blog_id
     * @return mixed
     *
     * Add the blog specific api_keys tables
     */
    public function add_api_key_tables($blog_id){
        Rooftop_Api_Authentication_Activator::create_database_tables($blog_id);
        return $blog_id;
    }

    /**
     * @param $blog_id
     * @return mixed
     *
     * remove the blog specific api_keys tables
     */
    public function remove_api_key_tables($blog_id){
        Rooftop_Api_Authentication_Activator::drop_database_tables($blog_id);
        return $blog_id;
    }

    /**
     * @param $user_id
     *
     * When we remove an API key, we also remove the corresponding user account from the site.
     * However, the user isn't also deleted from the parent network (only its association to the sub-site)
     *
     * The delete_user hook calls this, with the $user_id and we remove the user manually (but only if it
     * only belongs to 1 blog)
     */
    public function remove_user_from_network($user_id){
        global $wpdb;

        $blogs = get_blogs_of_user($user_id);
        if(count($blogs)==1){
            $table_name = $wpdb->base_prefix."users";
            $wpdb->delete($table_name, array('id' => $user_id));
        }
    }
}
