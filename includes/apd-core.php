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
 * Obtiene un arreglo con información de todas las variedades disponibles del producto, además información de los atributos relacionados al producto para ser mostrados
 * en un select.
 * @since 1.0.0
 * 
 * @param int $product_id ID del producto
 * @return array|bool Arreglo con los datos del producto, false en caso de no encotrar datos.
 */
function apd_list_product_variations( $product_id ) {
    if ( !is_numeric( $product_id) ) return false;
    $product   = wc_get_product( $product_id );
	
    if ( $product->is_type( 'variable' ) ) {
        $variation_group 	= array();
        $json_output		= array(); // Todas las variedades que se imprimiran en un string json
        $variations 		= $product->get_available_variations();
        print_r($product->get_attributes());
        /**
         * Ciclo de cada variación, la cual esta compuesta de una serie de caracteristicas estandar como el stock, precio, sku, etc.
         * También cuenta con la llave "attributes", la cual contiene la combinación de sus atributos, por ejemplo:
         * [attributes] => Array (
         *    [attribute_color] => Rojo
         *    [attribute_talla] => M
         * ),
         * [display_price] => 1000,
         * [variation_id] => 22,
         * [is_purchasable] => 1,
         * (...)
         */
        $attribute_options = array();
        foreach ( $variations as $variation ) {
            $variation_data = array();
            foreach( $variation as $variation_key => $variation_value ):
                // Recorriendo las variedades, aislamos los atributos para poder presentar los select con cada opcion disponible.
                if ( 'attributes' == $variation_key ) {
                    $attributes = $variation_value;
                    /**
                     * Aquí empezamos a rescatar los atributos del productos, puede darse el caso que no todos los productos tengan
                     * los mismos valores (por ejemplo, que no tenga stock en color azul) asi que se debe consultar si el valor ya
                     * se encuentra en el arreglo para agregarlo si otro producto tuviera otros valores.
                     */
                    foreach ( $attributes as $attribute_key => $attribute_value ) {
                        if ( !in_array( $attribute_value, $attribute_options[$attribute_key] ) ) {
                            $attribute_options[$attribute_key][] = $attribute_value;
                        }
                    }
                }
                if ( 'display_price' == $variation_key ) {
                    $variation_data[$variation_key] = wc_price($variation_value);
                } else {
                    $variation_data[$variation_key] = $variation_value; // Copiar todos los datos de la variedad
                }
                
            endforeach;
        }
    } else {
        return false;
    }

    $select_info    = array();
    $i              = 0;
    // Lo mismo de talla y color de polera
    foreach ( $attribute_options as $key => $value ) {
        $select_options = array(); 
        $j              = 0;
        $options        = $value; // Ej: Array ( [0] => 30gr [1] => 50gr [2] => 100gr [3] => 250gr )
        // $key es la llave del atributo, como por ejemplo "attribute_color"
        $select_info[$i]['select_label']    = 'Definir';
        $select_info[$i]['select_name']     = $key;
        $select_options[$j]['option_label'] = __( '- Seleccione -', 'ajax-product-details' );
        $select_options[$j]['option_value'] = -1;
        $j++;
        for ( $k = 0; $k <= count( $options ) - 1; $k++ ) { // Cada opcion de una caracteristica especifica
            $select_options[$j]['option_label'] = $options[$k];
            $select_options[$j]['option_value'] = $options[$k];
            $j++;
        }
        $select_info[$i]['select_options'] = $select_options;
        $i++;
    }
    /**
     * Salida esperada
     * $output['variation_json'] = <info de cada variedad del producto como precios, stock, sku, etc>
     * $output['options'] = array(
     *   array(
     *     'select_label => 'Color',
     *     'select_name' => 'attribute_color',
     *     'options' => array(
     *       array( 'option_label' => 'Rojo', 'option_value' => 'rojo' ),
     *       array( 'option_label' => 'Azul', 'option_value' => 'azul' ),
     *     )
     *   )
     * )
     */
	
    return array(
        'variarion_info'=> $variation_data,
        'options'       => $select_info,
    );
}

/**
 * Agrega una entrada de menú a WooCommerce para controlar distintos aspectos del plugin.
 * @since 1.0.0
 */
function apd_settings() {
    add_submenu_page(
        'woocommerce',
        __( 'Ajax Product Details', 'ajax-product-details' ),
        __( 'Ajax Product Details', 'ajax-product-details' ),
        'manage_options',
        'apd-settings',
        'apd_settings_screen'
    ); 
}
add_action( 'admin_menu', 'apd_settings' );

function apd_settings_screen () {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'Necesita un perfil con otro nivel de acceso para editar esta configuración', 'my_site_info' ) );
	}
    apd_collect_and_update();
    include plugin_dir_path( __FILE__ ) . '../admin/views/apd-settings.php';
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
				'status' 		=> 'OK',
				'url_to_cart'   => wc_get_cart_url(),
				'message'		=> __( 'Producto agregado, ir al ', 'ajax-product-details') . '<a href="'.wc_get_cart_url().'">' . __('carrito', 'ajax-product-details') . '</a>',
			)
		);
	} else {
        $message = '';
        if ( !$passed_validation ) {
            $message = __( 'Favor revise los datos ingresados', 'ajax-product-details' );
        }
		wp_send_json( array(
			'status'        => 'error',
			'product_url'   => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
			'message'		=> strip_tags(wc_print_notices( true ), '<a>'),
		));
	}

	wp_die();
}

add_action( 'wp_footer', function() {
    $overlay_color  = get_option('apd-overlay-color');
    $overlay_style  = "";
    if ( ''!= trim($overlay_color) ) {
        $overlay_style = 'style="background-color: '.$overlay_color.'";';
    }
    ?>
    <div class="apd-overlay apd-overwrite apd-hidden"<?php echo $overlay_style; ?>>
        <div class="apd-general-container">
            <div class="apd-close-modal-container">
                <div class="apd-close-modal-btn">X</div>
            </div>
            <div class="apd-general-content-container"></div>
        </div>
    </div>
    <?php
} );

function apd_collect_and_update() {
    if ( isset( $_POST['rd-variation'] ) ) {
        update_option( 'apd-variation-mode', $_POST['rd-variation'] );
        update_option( 'apd-image-click', $_POST['rd-image-click'] );
        update_option( 'apd-overlay-color', $_POST['overlay-color'] );
        update_option( 'apd-buttons-color', $_POST['button-color'] );
        update_option( 'apd-text-color', $_POST['text-color'] );
        update_option( 'apd-quickview-style', $_POST['button-style'] );
    }
}

function apd_custom_product_link() {
	global $product;
	echo '<a
        product-id="'.$product->get_ID().'"
        class="woocommerce-LoopProduct-link woocommerce-loop-product__link apd-quick-view"
        href="#apd-product-info">';
}

function apd_remove_links() {
    if ( 'show-quickview' == get_option('apd-image-click') ) {
        remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
        add_action( 'woocommerce_before_shop_loop_item_title', 'apd_custom_product_link', 9);
    }
}
add_action( 'woocommerce_before_shop_loop', 'apd_remove_links' );

add_image_size( 'apd-product-thumbnail', 200, 200 );