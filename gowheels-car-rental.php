<?php
/**
 * Plugin Name: GoWheels Car Rental
 * Plugin URI: https://github.com/jasminaramim/GoWheels-Car-Rental-plugin
 * Description: Single-company car rental management: fleet, availability search, admin-approved bookings, deposits, cash and online payments, shortcodes and Elementor widgets.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Jasmin
 * Author URI: https://github.com/jasminaramim
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: gowheels
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit;

define( 'GOWHEELS_VERSION', '1.0.0' );
define( 'GOWHEELS_DB_VERSION', '1.0.0' );
define( 'GOWHEELS_FILE', __FILE__ );
define( 'GOWHEELS_PATH', plugin_dir_path( __FILE__ ) );
define( 'GOWHEELS_URL', plugin_dir_url( __FILE__ ) );

require_once GOWHEELS_PATH . 'includes/class-plugin.php';
GoWheels_Plugin::load_files();

register_activation_hook( __FILE__, array( 'GoWheels_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'GoWheels_Deactivator', 'deactivate' ) );
add_action( 'plugins_loaded', array( 'GoWheels_Plugin', 'instance' ) );
