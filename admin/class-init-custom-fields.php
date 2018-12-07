<?php

class Init_Custom_Fields{

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}
	
	function wp_get_option($prefix,$key = '', $default = false ) {
		if ( function_exists( 'cmb2_get_option' ) ) {
			// Use cmb2_get_option as it passes through some key filters.
			return cmb2_get_option( $prefix, $key, $default );
		}
		// Fallback to get_option if CMB2 is not loaded yet.
		$opts = get_option( $prefix, $default );
		$val = $default;
		if ( 'all' == $key ) {
			$val = $opts;
		} elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
			$val = $opts[ $key ];
		}
		return $val;
	}


	public function categories_custom_fields(){
		// Start with an underscore to hide fields from custom fields list
		$prefix = '_wordroid_fields';

		/**
		 * Initiate the metabox
		 */
		$cmb = new_cmb2_box( array(
			'id'            => 'wordroid_fields',
			'title'         => __( 'Test Metabox', 'cmb2' ),
			'object_types'  => array( 'term' ), // Post type
			'taxonomies'    => array( 'category'),
			'context'       => 'normal',
			'priority'      => 'high',
			'show_names'    => true, // Show field names on the left
			'show_in_rest' => WP_REST_Server::READABLE,
			 'cmb_styles' => true, // false to disable the CMB stylesheet
			// 'closed'     => true, // Keep the metabox closed by default
		) );

		$cmb->add_field( array(
			'name'    => 'Choose Color',
			'id'      => 'category_color',
			'type'    => 'colorpicker',
			'default' => '#ffffff',
			// 'options' => array(
			// 	'alpha' => true, // Make this a rgba color picker.
			// ),
		) );

		$cmb->add_field( array(
			'name'    => 'Choose Image',
			'desc'    => 'Upload an image or enter an URL.',
			'id'      => 'category_image',
			'type'    => 'file',
			// Optional:
			'options' => array(
				'url' => false, // show the text input for the url
			),
			'text'    => array(
				'add_upload_file_text' => 'Choose Image' // Change upload button text. Default: "Add or Upload File"
			),
			'preview_size' => 'medium', // Image size to use when previewing in the admin.
		) );
		// Add other metaboxes as needed
		$cmb->add_field( array(
			'name' => 'Hide category',
			'desc' => 'Check this box to hide the category in the WorDroid APP',
			'id'   => 'hide_category',
			'type' => 'checkbox',
		) );
	}

	public function admin_settings_page(){
		$prefix = '_wordroid_settings';

		$cmb = new_cmb2_box( array(
			'id'           => $prefix . 'wp-wordroid',
			'title'        => __( 'Settings', 'settings' ),
			'object_types'  => array( 'options-page' ),
			'option_key'      => 'wordroid-settings', // The option key and admin menu page slug.
			'parent_slug'     => 'wordroid-home', // Make options page a submenu item of
			'context'      => 'normal',
			'priority'     => 'default',
		) );
		$cmb->add_field( array(
			'name' => __( 'User Key', 'settings' ),
			'id' => 'app_user_key',
			'type' => 'text',
		) );
		$cmb->add_field( array(
			'name' => __( 'OneSingnal APP ID', 'settings' ),
			'id' => 'os_app_id',
			'type' => 'text',
		) );
		$cmb->add_field( array(
			'name' => __( 'OneSingnal REST API Key', 'settings' ),
			'id' => 'os_api_key',
			'type' => 'text',
		) );
		$cmb->add_field( array(
			'name' => 'New Post Notification',
			'desc' => 'Send Notification automatically when a new post is published',
			'id'   => 'enable_newpost_notify',
			'type' => 'checkbox',
		) );
		$cmb->add_field( array(
			'name' => __( 'New Post Notification\'s Title', 'settings' ),
			'default' => 'New Post',
			'id' => 'new_notify_title',
			'type' => 'text',
		) );
		$cmb->add_field( array(
			'name' => 'Updated Post Notification',
			'desc' => 'Send Notification automatically when a post is updated',
			'id'   => 'enable_updatepost_notify',
			'type' => 'checkbox',
		) );
		$cmb->add_field( array(
			'name' => __( 'Updated Post Notification\'s Title', 'settings' ),
			'default' => 'Post Updated',
			'id' => 'update_notify_title',
			'type' => 'text',
		) );
	}

	public function admin_update_page(){
		$prefix = '_wordroid_update';

		$cmb = new_cmb2_box( array(
			'id'           => $prefix . 'wp-wordroid',
			'title'        => __( 'Update App', 'update' ),
			'object_types'  => array( 'options-page' ),
			'option_key'      => 'wordroid-update', // The option key and admin menu page slug.
			'parent_slug'     => 'wordroid-home', // Make options page a submenu item of
			'show_in_rest' => WP_REST_Server::READABLE,
			'context'      => 'normal',
			'priority'     => 'default',
		) );
		$cmb->add_field( array(
			'name' => __( 'Update Message Title', 'config' ),
			'id' => 'update_title',
			'default' => 'New Update',
			'desc' => 'Max 50 characters',
			'type' => 'text',
		) );
		$cmb->add_field( array(
		    'name' => 'Update Message Body',
		    'desc' => 'What\'s new in this update ',
		    'id' => 'update_body',
		    'type' => 'textarea_small'
		) );
		$cmb->add_field( array(
			'name' => __( 'Version', 'config' ),
			'id' => 'version',
			'type'    => 'text_small',
		) );

		$cmb->add_field( array(
			'name' => 'Force Update',
			'desc' => 'Force users to update the app',
			'id'   => 'force_update',
			'type' => 'checkbox',
		) );
	}


	public function admin_option_menu_fields(){
		$prefix = '_wordroid_config';

		$cmb = new_cmb2_box( array(
			'id'           => $prefix . 'wp-wordroid',
			'title'        => __( 'Config', 'config' ),
			'object_types'  => array( 'options-page' ),
			'option_key'      => 'wordroid-config', // The option key and admin menu page slug.
			'parent_slug'     => 'wordroid-home', // Make options page a submenu item of
			'show_in_rest' => WP_REST_Server::READABLE,
			'context'      => 'normal',
			'priority'     => 'default',
		) );

	$cmb->add_field( array(
		'name' => __( 'App Name', 'config' ),
		'id' => 'app_name',
		'type' => 'text',
	) );
	$cmb->add_field( array(
		'name' => 'Enable Slider',
		'desc' => 'Enable Slider on home screen',
		'id'   => 'slider_enabled',
		'type' => 'checkbox',
	) );
	$cmb->add_field( array(
		'name' => __( 'Slider Category ID', 'config' ),
		'id' => 'slider_category',
		'type'    => 'text_small',
	) );
	$cmb->add_field( array(
		'name'           => 'Categories',
		'desc'           => 'Selected categories will be visible on home screen',
		'id'             => 'home_screen_categories',
		'taxonomy'       => 'category', //Enter Taxonomy Slug
		'type'           => 'taxonomy_multicheck_inline',
		'classes_cb'     => 'cmb-type-taxonomy-multicheck-inline',
		// Optional :
		'text'           => array(
			'no_terms_text' => 'Sorry, no terms could be found.' // Change default text. Default: "No terms"
		),
		'remove_default' => 'true' // Removes the default metabox provided by WP core. Pending release as of Aug-10-16
	) );
	$group_field_id = $cmb->add_field( array(
		'id'          => 'wordroid_section_group',
		'type'        => 'group',
		'description' => __( 'Your sections on app homepage', 'cmb2' ),
		// 'repeatable'  => false, // use false if you want non-repeatable group
		'options'     => array(
			'group_title'   => __( 'Entry {#}', 'cmb2' ), // since version 1.1.4, {#} gets replaced by row number
			'add_button'    => __( 'Add Another Section', 'cmb2' ),
			'remove_button' => __( 'Remove Section', 'cmb2' ),
			'sortable'      => true, // beta
			// 'closed'     => true, // true to have the groups closed by default
		),
	) );

	// Id's for group's fields only need to be unique for the group. Prefix is not needed.
	$cmb->add_group_field( $group_field_id, array(
		'name' => 'Category Title',
		'desc' => 'Title of your section',
		'id'   => 'title',
		'type' => 'text_small',
	) );
	$cmb->add_group_field($group_field_id, array(
		'name'    => 'Category Id',
		'desc'    => 'The category to show lastest posts from',
		'id'      => 'category_id',
		'type'    => 'text_small',
	) );
	$cmb->add_group_field($group_field_id, array(
		'name'             => 'Section Type',
		'desc'             => 'Select the layout type of the sections',
		'id'               => 'type',
		'type'             => 'select',
		'show_option_none' => true,
		'default'          => '1',
		'options'          => array(
			'1' => __( 'Slider', 'cmb2' ),
			'2' => __( 'Vertical List', 'cmb2' ),
			'3' => __( 'Horizontal List', 'cmb2' ),
		),
	) );
	$cmb->add_group_field($group_field_id, array(
		'name'             => 'Posts Count',
		'desc'             => 'No of posts to show in the section',
		'id'               => 'post_count',
		'type'             => 'select',
		'show_option_none' => true,
		'default'          => '4',
		'options'          => array(
			'1' => __( '1', 'cmb3' ),
			'2' => __( '2', 'cmb3' ),
			'3' => __( '3', 'cmb3' ),
			'4' => __( '4', 'cmb3' ),
			'5' => __( '5', 'cmb3' ),
			'6' => __( '6', 'cmb3' ),
			'7' => __( '7', 'cmb3' ),
			'8' => __( '8', 'cmb3' ),
			'9' => __( '9', 'cmb3' ),
			'10' => __( '10', 'cmb3' ),
		),
	) );
	$cmb->add_group_field( $group_field_id, array(
		'name' => 'Small Icon',
		'desc' => 'Small icon',
		'id'   => 'image',
		'type' => 'file',
		'options' => array(
			'url' => false, // Hide the text input for the url
		),
	) );
	}


}
?>