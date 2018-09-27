<?php

/**
 *
 * @link              http://itsanubhav.com
 * @since             1.0.1
 * @package           Wordroid
 *
 * @wordpress-plugin
 * Plugin Name:       WorDroid
 * Plugin URI:        http://itsanubhav.com
 * Description:       This plugins provides the features to your WorDroid Android app.
 * Version:           1.0.0
 * Author:            Anubhav Anand
 * Author URI:        https://codecanyon.net/item/wordroid-full-wordpress-blog-app/19753667
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wordroid
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wordroid-activator.php
 */
function activate_wordroid() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wordroid-activator.php';
	Wordroid_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wordroid-deactivator.php
 */
function deactivate_wordroid() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wordroid-deactivator.php';
	Wordroid_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wordroid' );
register_deactivation_hook( __FILE__, 'deactivate_wordroid' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once __DIR__ . '/cmb2/init.php';
require_once __DIR__ . '/admin/class-send-notification.php';
require plugin_dir_path( __FILE__ ) . '/customized-api.php';
require plugin_dir_path( __FILE__ ) . '/new_api.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-wordroid.php';
add_action( 'transition_post_status', 'post_transition_action', 10, 3 );
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wordroid() {

	$plugin = new Wordroid();
	$plugin->run();

}
run_wordroid();
