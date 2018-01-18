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
		$prefix = '_wordroid_fields_';

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
			'name' => 'Hide',
			'desc' => 'check this field to hide this category from mobile device',
			'id'   => 'hide_category',
			'type' => 'checkbox',
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
				'url' => true, // show the text input for the url
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
		$prefix = '_cmb_';

		$cmb = new_cmb2_box( array(
			'id'           => $prefix . 'wp-wordroid',
			'title'        => __( 'Config', 'cmb2' ),
			'object_types'  => array( 'options-page' ),
			'option_key'      => 'wordroid-config', // The option key and admin menu page slug.
			'parent_slug'     => 'wordroid-home', // Make options page a submenu item of
			'show_in_rest' => WP_REST_Server::READABLE,
			'context'      => 'normal',
			'priority'     => 'default',
		) );

		$cmb->add_field( array(
		'name' => __( 'App Name', 'cmb2' ),
		'id' => $prefix . 'app_name',
		'type' => 'text',
	) );

	$cmb->add_field( array(
		'name' => __( 'My Little Box', 'cmb2' ),
		'id' => $prefix . 'my_little_box',
		'type' => 'text',
	) );

	$cmb->add_field( array(
		'name' => __( 'A Big Old Box', 'cmb2' ),
		'id' => $prefix . 'a_big_old_box',
		'type' => 'textarea',
	) );

	$cmb->add_field( array(
		'name' => __( 'Do You Love the Radio?', 'cmb2' ),
		'id' => $prefix . 'do_you_love_the_radio_',
		'type' => 'radio',
		'options' => array(
			'' => __( '', 'cmb2' ),
		),
	) );

	$my_group_field = $cmb->add_field( array(
		'name' => __( 'My Group Field', 'cmb2' ),
		'id' => $prefix . 'my_group_field',
		'type' => 'group',
	) );

		$cmb->add_group_field( $my_group_field, array(
			'name' => __( 'My Group Sub-field', 'cmb2' ),
			'id' => $prefix . 'my_group_sub_field',
			'type' => 'textarea_small',
		) );

		$cmb->add_group_field( $my_group_field, array(
			'name' => __( 'My Second Sub-field', 'cmb2' ),
			'id' => $prefix . 'my_second_sub_field',
			'type' => 'file',
		) );

	}

}
?>