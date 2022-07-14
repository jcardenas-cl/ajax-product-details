<?php
/**
 * Agrega la acción de obtener un producto mediante ajax.
 * Además carga la información en la plantilla correspondiente, se ofrece una por defecto, pero esta puede ser desechada en caso de que se encuentre
 * un archivo con el nombre apd-product-detail.php al interior de la carpeta apd, la cual debe estar alojada en la raíz del tema usado.
 * @since 1.0.0
 */
add_action( 'wp_ajax_nopriv_apd_get_product', 'apd_get_product' );
add_action( 'wp_ajax_apd_get_product', 'apd_get_product' );
function apd_get_product() {
    $product_id = $_POST['product_id'];
    $_product   = wc_get_product( $product_id );
    // Para simplicidad en la vista, se separaran las variedades del producto en caso que las tenga
    if ( $_product->is_type( 'variable' ) ) {
        $variation_group 	= array();
        $variations_info	= array();
        $variations 		= $_product->get_available_variations();
        // Ciclo de cada variacion.
        for ( $i = 0; $i <= count($variations) - 1; $i++ ) {
            $variation 		= $variations[$i];
            $attributes 	= $variation['attributes'];
            $variation_data	= array();
            $att_group		= array();
            $j = 0;
            foreach ( $attributes as $attribute_key => $value ) {
                if ( !in_array( $value, $variation_group[$attribute_key] ) ) {
                    $variation_group[$attribute_key][] = $value;
                }
                $att_group[$j]['att_name'] 	= $attribute_key;
                $att_group[$j]['att_value']	= $value;
                $j++;
            }
            $variation_data['variation_id'] = $variation['variation_id'];
            $variation_data['price'] 		= wc_price( $variation['display_price'] );
            $variation_data['attributes']	= $att_group;
            $variations_info[] 				= $variation_data;
        }			
    }
    set_query_var( 'product', $_product );
    set_query_var( 'variations', $variation_group );
    set_query_var( 'variations_json', $variations_info );
    
    // Con este codigo, se podra sobreescribir la plantilla original del sistema si se deja con la ruta determinada
    if ( file_exists( get_stylesheet_directory() . '/apd/apd-product-detail.php' ) ) {
        load_template( get_stylesheet_directory() . '/apd/apd-product-detail.php'  );
    } else {
        load_template( plugin_dir_path( __FILE__ ) . 'views/apd-product-detail.php' );
    }
    
    wp_die();
}

/**
 * Agrega un botón personalizado en el listado de productos por cada producto, en conjunto con las variedades y la cantidad, agrega mediante ajax el producto al carrito.
 * @since 1.0.0
 */
add_action( 'woocommerce_after_shop_loop_item', 'apd_add_quick_view_button', 25 );
function apd_add_quick_view_button() {
    global $product;
	if( (is_product_category() || is_shop()) and ( $product->is_type( 'variable' ) or $product->is_type( 'simple' ) ) ) {
	?>
    <div class="apd-quick-view-container">
    <a
        product-id="<?php echo $product->get_ID(); ?>"
        class="apd-quick-view"
        data-bs-toggle="collapse"
        href="#apd-product-info"
        role="button"
        aria-expanded="false"
        aria-controls="apd-product-info"><?php _e( 'Vista rápida', 'ajax-product-details' ); ?></a></div>
	<?php
	}
}

/**
 * Metodo que permite agregar un producto al carrito de manera asincrona
 */
add_action( 'wp_ajax_nopriv_apd_add_to_cart', 'apd_add_to_cart' );
add_action( 'wp_ajax_apd_add_to_cart', 'apd_add_to_cart' );
function apd_add_to_cart() {
	header('Content-type: text/json');
	$product_id 		= $_POST['product_id'];
	$variation_id		= ($_POST['variation_id'] > 0 ) ? $_POST['variation_id'] : null;
	$quantity			= $_POST['quantity'];
	$passed_validation	= apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
	$product_status    	= get_post_status( $product_id );

	if (
		$passed_validation && 
		WC()->cart->add_to_cart( $product_id, $quantity, $variation_id ) && 
		'publish' === $product_status
		) {
		do_action( 'woocommerce_ajax_added_to_cart', $product_id );
		wp_send_json(
			array(
				'status' 			=> 'OK',
				'products_on_cart'	=> woo_get_quantity_products_on_cart(),
				'url_to_cart'		=> wc_get_cart_url(),
				'message'			=> __( 'Producto agregado, ir al ', 'simple-add-to-cart') . '<a href="'.wc_get_cart_url().'">' . __('carrito', 'simple-add-to-cart') . '</a>',
				'mini_cart'			=> woo_get_minicart(), // Funcion del tema, no del plugin 
			)
		);
	} else {
		// If there was an error adding to the cart, redirect to the product page to show any errors
		$data = array(
			'error'       => true,
			'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
			'message'		=> $passed_validation,
		);

		wp_send_json( $data );
	}

	wp_die();
}

add_action( 'wp_footer', function() {
    ?>
    <div class="apd-general-container"></div>
    <?php
} );