<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://itsanubhav.com
 * @since      1.0.0
 *
 * @package    Wordroid
 * @subpackage Wordroid/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wordroid
 * @subpackage Wordroid/admin
 * @author     Anubhav Anand <anubhavanand884@gmail.com>
 */
class Wordroid_Admin {

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
		 * defined in Wordroid_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wordroid_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wordroid-admin.css', array(), $this->version, 'all' );

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
		 * defined in Wordroid_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wordroid_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wordroid-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	public function wordroid_admin_menu(){
		add_menu_page(
			__( 'WorDroid', 'textdomain' ),
			__( 'WorDroid', 'textdomain' ),
			'manage_options',
			'wordroid-home',
			array( $this, 'page_dashboard' ),
			'dashicons-smartphone'
		);

		add_submenu_page('wordroid-home',
			__( 'Send Notifications', 'notifications' ),
			__( 'Send Notifications', 'notifications' ),
				'manage_options',
				'wordroid-home',
				array( $this, 'page_dashboard' )
		);
		
		/*add_submenu_page('wordroid-home',
			__( 'Config', 'textdomain' ),
        	__( 'Config', 'textdomain' ),
				'manage_options',
				'wordroid-config',
				array( $this, 'page_config' )
		);
		
		add_submenu_page('wordroid-home',
			__( 'Settings', 'textdomain' ),
        	__( 'Settings', 'textdomain' ),
				'manage_options',
				'wordroid-settings',
				array( $this, 'page_settings' )
		);*/
	}

	public function page_dashboard() {
		include( plugin_dir_path( __FILE__ ) . 'partials/wordroid-admin-dashboard.php' );
	}

	public function page_config() {
		include( plugin_dir_path( __FILE__ ) . 'partials/wordroid-admin-config-page.php' );
	}

	public function page_settings() {
		include( plugin_dir_path( __FILE__ ) . 'partials/wordroid-admin-settings-page.php' );
	}

}
