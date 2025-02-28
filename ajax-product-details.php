<?php
/*
Plugin Name: Ajax Product Details
Plugin URI: https://arknite.dev/plugins/ajax-product-details
Description: Muestra las caracteristicas de un producto directamente en el listado de productos. También permite agregar el producto al carrito de compras y seleccionar sus atributos.
Version: 0.5.0
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