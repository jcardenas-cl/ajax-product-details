<?php
/**
 * Carga de scripts necesarios para agregar los productos al carrito mediante ajax.
 */

function apd_enqueue_scripts_and_styles() {

	if ( ! wp_script_is( 'jquery' ) ) {
		wp_enqueue_script( 'jquery' );
	}
    
    wp_enqueue_script (
        'apd-core-script',
        plugin_dir_url( __FILE__ ) . '../public/js/apd-core.js',
        array( 'jquery' ),
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
		plugin_dir_url( __FILE__ ) . '../public/css/apd-styles.css'
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