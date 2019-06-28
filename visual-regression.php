<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://gabeherbert.com
 * @since             1.0.0
 * @package           Visual_Regression
 *
 * @wordpress-plugin
 * Plugin Name:       Visual Regression
 * Plugin URI:        https://gabeherbert.com/wp-plugins/visual-regresion
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Gabe Herbert
 * Author URI:        https://gabeherbert.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       visual-regression
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
define( 'VISUAL_REGRESSION_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-visual-regression-activator.php
 */
function activate_visual_regression() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-visual-regression-activator.php';
	Visual_Regression_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-visual-regression-deactivator.php
 */
function deactivate_visual_regression() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-visual-regression-deactivator.php';
	Visual_Regression_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_visual_regression' );
register_deactivation_hook( __FILE__, 'deactivate_visual_regression' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-visual-regression.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_visual_regression() {

	$plugin = new Visual_Regression();
	$plugin->run();

}
run_visual_regression();
