<?php
/**
 * Archivo inicial del plugin
 *
 * @category Install
 * @package  Install
 * @author   Julio Cárdenas <julio@arknite.dev>
 * @license  GPLv2 or later
 * @link     https://arknite.dev/plugins/ajax-product-details
 */

/*
Plugin Name: Ajax product details
Plugin URI: https://arknite.dev/plugins/ajax-product-details
Description: Obtiene el detalle de un producto mediante ajax, asi se puede mostrar en donde sea necesario en la página
Version: 1.0
Author: Julio Cárdenas
Author URI: https://arknite.dev
Text Domain: ajax-product-details
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ajax_product_details' ) ) {

	class Ajax_product_details {

		/**
		 * Definir la versión del plugin actual.
		 *
		 * @var String $plugin_version Versión del plugin.
		 */
		private $plugin_version = '1.0.0';

		/**
		 * Constructor del plugin, de momento se inicia vacio
		 */
		public function __construct() {

		}

		/**
		 * Realiza la instalación del plugin
		 *
		 * @return void
		 */
		public static function install() {
			// Do nothing
		}

		/**
		 * Metodo que se ejecuta al cargar el plugin, cuando ya esta activado
		 *
		 * @return void
		 */
		public function init_setup() {
            include_once plugin_dir_path( __FILE__ ) . 'includes/apd-init.php';
			include_once plugin_dir_path( __FILE__ ) . 'includes/apd-core.php';
		}
	}
	
}

$ajax_product_details = new Ajax_product_details();
$ajax_product_details->init_setup();

register_activation_hook( __FILE__, [ 'Ajax_product_details', 'install' ] );