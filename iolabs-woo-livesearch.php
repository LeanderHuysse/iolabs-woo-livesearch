<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.iolabs.nl
 * @since             1.0.0
 * @package           Iolabs_Woo_Livesearch
 *
 * @wordpress-plugin
 * Plugin Name:       IOLabs - WooCommerce Livesearch
 * Plugin URI:        https://www.iolabs.nl
 * Description:       Enables live search for WooCommerce
 * Version:           1.0.0
 * Author:            IOLabs
 * Author URI:        https://www.iolabs.nl
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       iolabs-woo-livesearch
 * Domain Path:       /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 4.2.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-iolabs-woo-livesearch-activator.php
 */
function activate_iolabs_woo_livesearch() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-iolabs-woo-livesearch-activator.php';
	Iolabs_Woo_Livesearch_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-iolabs-woo-livesearch-deactivator.php
 */
function deactivate_iolabs_woo_livesearch() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-iolabs-woo-livesearch-deactivator.php';
	Iolabs_Woo_Livesearch_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_iolabs_woo_livesearch' );
register_deactivation_hook( __FILE__, 'deactivate_iolabs_woo_livesearch' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-iolabs-woo-livesearch.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_iolabs_woo_livesearch() {

	$plugin = new Iolabs_Woo_Livesearch();
	$plugin->run();

}
run_iolabs_woo_livesearch();
