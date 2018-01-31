<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.iolabs.nl
 * @since      1.0.0
 *
 * @package    Iolabs_Woo_Livesearch
 * @subpackage Iolabs_Woo_Livesearch/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Iolabs_Woo_Livesearch
 * @subpackage Iolabs_Woo_Livesearch/includes
 * @author     Leander Huysse <leander@iomedia.nl>
 */
class Iolabs_Woo_Livesearch_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'iolabs-woo-livesearch',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
