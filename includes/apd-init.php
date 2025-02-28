<?php
/**
 * Carga de scripts necesarios para agregar los productos al carrito mediante ajax.
 */

function apd_enqueue_scripts_and_styles() {

	if ( ! wp_script_is( 'jquery' ) ) {
		wp_enqueue_script( 'jquery' );
	}

	if ( ! wp_script_is( 'lodash' ) ) {
		wp_enqueue_script('lodash');
	}
    wp_enqueue_script (
        'apd-core-script',
        plugin_dir_url( __FILE__ ) . '../public/js/apd-core.js',
        array( 'jquery', 'lodash' ),
		time(),
		true
    );
	wp_enqueue_script (
        'swiperjs',
        'https://unpkg.com/swiper@8/swiper-bundle.min.js',
        array(),
		'8.3.1',
		true
    );

	wp_enqueue_style (
		'apd-core',
		plugin_dir_url( __FILE__ ) . '../dist/css/apd.min.css'
	);
	wp_enqueue_style (
		'swiperjs',
		'https://unpkg.com/swiper@8/swiper-bundle.min.css'
	);

	wp_localize_script (
		'apd-core-script',
		'apd_site_config',
		array(
			'ajaxurl'  => admin_url( 'admin-ajax.php' ),
			'base_url' => get_site_url(),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'apd_enqueue_scripts_and_styles');

function apd_admin_scripts_and_styles( $hook ) {
	if ( 'woocommerce_page_apd-settings' == $hook ) {
		wp_enqueue_script (
			'coloris',
			plugin_dir_url( __FILE__ ) . '../vendor/js/coloris.min.js',
			array(),
			'1.0.0',
			true
		);
		wp_enqueue_script (
			'apd-script',
			plugin_dir_url( __FILE__ ) . '../admin/js/apd-admin.js',
			array('coloris'),
			'1.0.0',
			true
		);
		wp_enqueue_style (
			'coloris',
			plugin_dir_url( __FILE__ ) . '../vendor/css/coloris.min.css'
		);
	}
}
add_action( 'admin_enqueue_scripts', 'apd_admin_scripts_and_styles' );