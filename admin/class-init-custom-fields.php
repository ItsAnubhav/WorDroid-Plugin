<?php

class Init_Custom_Fields{

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

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
	}

	public function admin_send_notification_page(){
		
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

	$cmb->add_field( array(
		'name'    => 'Toolbar Color',
		'id'      => 'app_color',
		'type'    => 'colorpicker',
		'default' => '#0084DA',
	) );

	$group_field_id = $cmb->add_field( array(
		'id'          => 'wiki_test_repeat_group',
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

	$cmb->add_group_field( $group_field_id, array(
		'name' => 'Category ID',
		'desc' => 'Leave empty to show latest post from site',
		'id'   => 'category_id',
		'type' => 'text_small',
		'sanitization_cb' => 'sanitize_greater_than_100',
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