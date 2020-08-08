<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.flance.info
 * @since             1.0.0
 * @package           Flance_aliexpress_dropship
 *
 * @wordpress-plugin
 * Plugin Name:       Flance Aliexpress Dropship Api
 * Description:       Creates api for aliexpress.com
 *
 * The component uses  the Aliexpress official providers APIs.
 * Version:           1.0.0
 * Author:            Rusty
 * Author URI:        http://www.flance.info
 * Text Domain:       flance_aliexpress_dropship_api
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

require_once 'helpers/ajax_calls.php';


