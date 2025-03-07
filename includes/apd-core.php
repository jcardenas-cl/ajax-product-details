<?php
/**
 * Agrega la acción de obtener un producto mediante ajax.
 * Además carga la información en la plantilla correspondiente, se ofrece una por defecto, pero esta puede ser desechada en caso de que se encuentre
 * un archivo con el nombre apd-product-detail.php al interior de la carpeta apd, la cual debe estar alojada en la raíz del tema usado.
 * @since 1.0.0
 */
add_action( 'wp_ajax_nopriv_qpd_get_product', 'qpd_get_product' );
add_action( 'wp_ajax_qpd_get_product', 'qpd_get_product' );

function qpd_get_product() {

    if (!isset($_POST['product_id'])) {
        wp_send_json_error('No product_id provided');
        return;
    }

    $product_id = intval($_POST['product_id']);
    $the_product = get_product_data($product_id);

    //$_product = wc_get_product($product_id);
    
    if (!$the_product) {
        wp_send_json_error('Product not found');
        return;
    }
    
    // Para simplicidad en la vista, se separaran las variedades del producto en caso que las tenga
    /*if ( $_product->is_type( 'variable' ) ) {
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
                if ( !isset($variation_group[$attribute_key]) or !in_array( $value, $variation_group[$attribute_key] ) ) {
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
    }*/
    
    set_query_var( 'product', $the_product );
    
    // Con este codigo, se podra sobreescribir la plantilla original del sistema si se deja con la ruta determinada
    if ( file_exists( get_stylesheet_directory() . '/apd/apd-product-detail.php' ) ) {
        load_template( get_stylesheet_directory() . '/apd/apd-product-detail.php'  );
    } else {
        load_template( plugin_dir_path( __FILE__ ) . 'views/apd-template-1.php' );
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
    ?>
    <div class="apd-overlay apd-hidden">
        <div class="apd-modal apd-override">
            <div class="close-handlers-container">
                <button class="modal-close">&times;</button>
                <div><div class="close-handler"></div></div>
            </div>

            <div class="loading">
                <?php _e( 'Cargando...', 'ajax-product-details' ); ?>
            </div>
            <div class="apd-content-container">
                <!-- The content -->
            </div>
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

function get_product_image_url($product_id) {
    // Obtén el ID del producto de WooCommerce
    $product = wc_get_product($product_id);

    // Obtén el ID de la imagen destacada
    $image_id = $product->get_image_id();

    if ($image_id) {
        // Si existe una imagen, obtenemos la URL
        $image_url = wp_get_attachment_url($image_id);
    } else {
        // Si no existe una imagen, usamos la imagen predeterminada de WooCommerce
        $image_url = wc_placeholder_img_src();
    }

    return $image_url;
}

function get_product_gallery_image_url($product_id) {
    // Obtén la instancia del producto de WooCommerce
    $product = wc_get_product($product_id);

    // Obtén los IDs de las imágenes de la galería
    $gallery_image_ids = $product->get_gallery_image_ids();

    // Si no hay imágenes en la galería, retorna false
    if (empty($gallery_image_ids)) {
        return false;
    }

    // Obtén las URLs de las imágenes de la galería
    $gallery_images = array_map('wp_get_attachment_url', $gallery_image_ids);

    return $gallery_images;
}

function get_product_variations_by_attribute($product_id) {
    $product = wc_get_product($product_id);

    if (!$product || !$product->is_type('variable')) {
        return []; // Asegúrate de que sea un producto variable.
    }

    $variations = [];
    $attributes = $product->get_variation_attributes(); // Obtén los atributos del producto.

    foreach ($attributes as $attribute_name => $attribute_values) {
        $variations[$attribute_name] = array_values($attribute_values);
    }

    // Incluye las combinaciones completas con el ID de cada producto variable.
    $variable_products = $product->get_available_variations();
    $variation_map = [];

    foreach ($variable_products as $variation) {
        $variation_attributes = $variation['attributes'];
        $variation_id = $variation['variation_id'];

        // Normaliza las claves de los atributos (remueve "attribute_" para uniformidad).
        $normalized_attributes = [];
        foreach ($variation_attributes as $key => $value) {
            $key = str_replace('attribute_', '', $key);
            $normalized_attributes[$key] = $value;
        }

        $variation_map[] = [
            'attributes' => $normalized_attributes,
            'variation_id' => $variation_id,
        ];
    }

    return [
        'attributes' => $variations,
        'variations_map' => $variation_map,
    ];
}

function get_product_data( $product_id ) {
    if ( !$product_id or is_nan($product_id) ) return;
    $WcProduct = wc_get_product($product_id);
    if ( is_null($WcProduct) or $WcProduct === false ) return;

    $theProduct                     = new stdClass();
    $theProduct->ID                 = $WcProduct->get_ID();
    $theProduct->title              = $WcProduct->get_title();
    $theProduct->price              = $WcProduct->get_price();
    $theProduct->regular_price      = $WcProduct->get_regular_price();
    $theProduct->sale_price         = $WcProduct->get_sale_price();
    $theProduct->type               = $WcProduct->get_type();
    $theProduct->permalink          = $WcProduct->get_permalink();
    $theProduct->stock_status       = $WcProduct->get_stock_status();
    $theProduct->description        = $WcProduct->get_description();
    $theProduct->short_description  = $WcProduct->get_short_description();
    $theProduct->main_image         = wc_placeholder_img_src();
    $theProduct->gallery            = false;
    $theProduct->variations         = false;
    $theProduct->sku                = $WcProduct->get_sku();
    $theProduct->stock_quantity     = $WcProduct->get_stock_quantity();
    $theProduct->categories         = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'names'));
    $theProduct->tags               = wp_get_post_terms($product_id, 'product_tag', array('fields' => 'names'));
    $theProduct->weight             = $WcProduct->get_weight();
    $theProduct->dimensions         = array(
        'length'    => $WcProduct->get_length(),
        'width'     => $WcProduct->get_width(),
        'height'    => $WcProduct->get_height()
    );
    $theProduct->tax_status         = $WcProduct->get_tax_status();
    $theProduct->tax_class          = $WcProduct->get_tax_class();
    $theProduct->purchase_note      = $WcProduct->get_purchase_note();
    $theProduct->featured           = $WcProduct->is_featured();

    // Main image
    $image_id = $WcProduct->get_image_id();
    if ($image_id) {
        // Si existe una imagen, obtenemos la URL
        $theProduct->main_image = wp_get_attachment_url($image_id);
    }
    
    // Gallery
    $gallery_image_ids = $WcProduct->get_gallery_image_ids();
    if (!empty($gallery_image_ids)) {
        $gallery_images = array_map('wp_get_attachment_url', $gallery_image_ids);
        $theProduct->gallery = $gallery_images;
    }

    // Variations
    if ($WcProduct->is_type('variable')) {
        $attributes = $WcProduct->get_variation_attributes();
        $formatted_attributes = [];
    
        // Iterar sobre cada atributo
        foreach ($attributes as $attribute_name => $values) {
            // Limpiar el nombre del atributo
            $clean_name = str_replace('pa_', '', $attribute_name);
            
            // Preparar el objeto del atributo
            $attribute_data = new stdClass();
            $attribute_data->name = $clean_name;
            $attribute_data->label = wc_attribute_label($attribute_name);
            $attribute_data->options = [];
    
            // Obtener las opciones del atributo
            if (taxonomy_exists($attribute_name)) {
                // Para atributos taxonomía
                $terms = get_terms([
                    'taxonomy' => $attribute_name,
                    'hide_empty' => false,
                    'include' => $values
                ]);
    
                foreach ($terms as $term) {
                    $option = new stdClass();
                    $option->value = $term->slug;
                    $option->label = $term->name;
                    $option->description = $term->description;
                    array_push($attribute_data->options, $option);
                }
            } else {
                // Para atributos personalizados
                foreach ($values as $value) {
                    $option = new stdClass();
                    $option->value = $value;
                    $option->label = $value;
                    $option->description = '';
                    array_push($attribute_data->options, $option);
                }
            }
    
            $formatted_attributes[] = $attribute_data;
        }
    
        // Obtener variaciones disponibles
        $variations_data = [];
        $available_variations = $WcProduct->get_available_variations();
    
        foreach ($available_variations as $variation) {
            $variation_obj = [
                'variation_id' => $variation['variation_id'],
                'price' => $variation['display_price'],
                'regular_price' => $variation['display_regular_price'],
                'is_in_stock' => $variation['is_in_stock'],
                'attributes' => []
            ];
    
            foreach ($variation['attributes'] as $key => $value) {
                $clean_key = str_replace(['attribute_', 'pa_'], '', $key);
                $variation_obj['attributes'][$clean_key] = $value;
            }
    
            $variations_data[] = $variation_obj;
        }

        $output = new stdClass();
        $output->attributes = $formatted_attributes;
        $output->variations = $variations_data;

        $theProduct->variations = $output;
    }

    return $theProduct;
}