jQuery( document ).ready( function() {
    jQuery( '.apd-quick-view' ).click( function() {
        const product_id    = jQuery( this ).attr('product-id')
        apd_trigger_load_content( product_id, jQuery( this ) )
    });

    jQuery( '.apd-product-detail-container .swiper-slide img' ).click( function() {
        const url_img = jQuery( this ).attr( 'src' )
        jQuery( '.apd-main-image' ).attr( 'src', url_img )
    });
});

function apd_action_triggers() {
    jQuery('.apd-cbo-variation').change( function() {
        const me                    = jQuery( this )
        const parent                = jQuery( me ).closest( '.apd-product-detail-container' )
        const values                = JSON.parse(jQuery( me ).val())

        jQuery( parent ).find('.apd-product-price').html( values.price )
        jQuery( parent ).find('.apd-variation-id').val( values.variation_id )
        jQuery( parent ).find('.apd-product-quantity').attr( 'max-quantity', values.stock )
    });

    jQuery('.apd-add-to-cart').click( function() {
        const me                    = jQuery( this )
        const parent                = jQuery( '.apd-product-detail-container' );
        const product_id            = jQuery('.apd-product-id').val()
        const variation_id          = jQuery('.apd-variation-id').val()
        const max_quantity          = jQuery('.apd-product-quantity').attr('max-quantity')
        const quantity              = 1
        const variations_selected   = jQuery('.apd-cbo-variation')

        // Validar que haya seleccionado valores para cada variedad
        if ( !apd_has_selected_valid_variations( variations_selected ) ) {
            jQuery( '.small-title' ).html( 'Error' )
            jQuery( '.notify-content' ).html( `Por favor, seleccione una variedad` )
            var toastLiveExample    = document.getElementById('liveToast')
            var toast               = new bootstrap.Toast(toastLiveExample)
            toast.show()
        } else if ( !apd_is_valid_quantity( parent ) ) {
            jQuery( '.small-title' ).html( 'Error' )
            jQuery( '.notify-content' ).html( `Verifique que la cantidad sea mayor a 0 y no mayor a ${max_quantity}` )
            var toastLiveExample    = document.getElementById('liveToast')
            var toast               = new bootstrap.Toast(toastLiveExample)
            toast.show()
        } else {
            jQuery( me ).text( jQuery( me ).attr( 'adding-text' ) )
            jQuery.post(
                apd_site_config.ajaxurl,
                {
                    'action': 'apd_add_to_cart',
                    'product_id': product_id,
                    'variation_id': variation_id,
                    'quantity': quantity
                }, function ( response ) {
                    if ( response.status == 'OK' ) {
                        if ( jQuery ( me ).hasClass( 'apd-quick-buy' ) ) {
                            window.location.href = response.url_to_cart
                        } else {
                            jQuery( '.notify-content' ).html( response.message )
                            jQuery( '.small-title' ).html( response.products_on_cart + ' Producto/s')
                            var toastLiveExample = document.getElementById('liveToast')
                            var toast = new bootstrap.Toast(toastLiveExample)
                            toast.show()
                            update_minicart()
                            jQuery( '.quantity-on-cart' ).text( response.products_on_cart )
                        }
                    } else {
                        jQuery( '.small-title' ).html( 'Error' )
                        jQuery( '.notify-content' ).html( response.message )
                        var toastLiveExample = document.getElementById('liveToast')
                        var toast = new bootstrap.Toast(toastLiveExample)
                        toast.show()
                    }
                    jQuery( me ).text( jQuery( me ).attr( 'standard-text' ) )
                }
            )

        }

    });

    apd_modify_quantity_product();
}

function apd_has_selected_valid_variations( variations ) {
    let is_valid = true
    jQuery( variations ).each( function() {
        if ( '-1' === jQuery( this ).val() ) {
            is_valid = false
            return
        }
    })
    return is_valid
}

/**
 * 
 * @param {object} product Elemento contenedor con la informacion del producto
 * @returns {boolean} Falso en caso que no cumpla con los requisitos, Verdadero si no hay problemas.
 */
 function apd_is_valid_quantity( product ) {
    const quantity      = parseInt(jQuery( product ).find('.apd-product-quantity').val())
    const max_quantity  = parseInt(jQuery( product ).find('.apd-product-quantity').attr( 'max-quantity' ))
    if ( quantity <= 0 ) return false
    if ( undefined === max_quantity ) {
        if ( quantity > max_quantity ) return false   
    }

    return true
}

function apd_modify_quantity_product() {
    const parent = jQuery( '.apd-product-detail-container' )

    jQuery( '.apd-product-count-down' ).click( function() {
        let current_count = jQuery( parent ).find( '.apd-product-count' ).val()
        if ( current_count > 0 ) {
            current_count--
            jQuery( parent ).find( '.apd-product-count' ).val( current_count )
        }
    });

    jQuery( '.apd-product-count-up' ).click( function() {
        let current_count = jQuery( parent ).find( '.apd-product-count' ).val()
        const max_quantity  = jQuery( this ).parent().find( '.apd-product-count' ).attr( 'max-quantity' )
        current_count++
        if ( current_count <= max_quantity || undefined === max_quantity ) {
            jQuery( this ).parent().find( '.apd-product-count' ).val( current_count )
        } else {
            jQuery( '.notify-content' ).html( `La mayor cantidad disponible es de ${max_quantity}` )
            jQuery( '.small-title' ).html( 'Error' )

            var toastLiveExample = document.getElementById('liveToast')
            var toast = new bootstrap.Toast(toastLiveExample)
            toast.show()
        }
    });
}

function apd_get_variation_data() {
    variations_avalilable.forEach( function(variation) {
        const attributes = variation.attributes
        if ( JSON.stringify(attributes) === JSON.stringify(selected_elements) ) {
            jQuery('.apd-product-price').html( variation.price )
            jQuery('.apd-variation-id').val( variation.variation_id )
            return
        } else {
            // En caso de que no se encuentre la variedad, se muestra el rango de precios y reinicia el ID de variedad
            const range_price = jQuery( '.apd-range-price' ).val()
            jQuery('.apd-product-price').html( range_price )
            jQuery('.apd-variation-id').val( '-1' )
        }
    });

    return 
}

if ( typeof apd_load_content_to_html !== 'function' ) {

}

if ( typeof apd_add_to_cart !== 'function' ) {
    function apd_add_to_cart( product_id, variation_id, quantity ) {
        jQuery.post(
            apd_site_config.ajaxurl,
            {
                'action': 'apd_add_to_cart',
                'product_id': product_id,
                'variation_id': variation_id,
                'quantity': quantity
            }, function ( response ) {
                if ( response.status == 'OK' ) {
                    jQuery( '.notify-content' ).html( response.message )
                    jQuery( '.small-title' ).html( response.products_on_cart + ' Producto/s')
                    var toastLiveExample = document.getElementById('liveToast')
                    var toast = new bootstrap.Toast(toastLiveExample)
                    toast.show()
                    update_minicart()
                    jQuery( '.quantity-on-cart' ).text( response.products_on_cart )

                    return true
                } else {
                    jQuery( '.small-title' ).html( 'Error' )
                    jQuery( '.notify-content' ).html( response.message )
                    var toastLiveExample = document.getElementById('liveToast')
                    var toast = new bootstrap.Toast(toastLiveExample)
                    toast.show()

                    return false
                }
            }
        )
    }
}

if ( typeof apd_trigger_load_content !== 'function' ) {
    /**
     * Obtiene la información del producto y la envia a otras funciones para que la muestren en el sitio.
     * Esta función permite ser sobreescrita para ajustarla a las necesidades del sitio.
     * @since 1.0.0
     * 
     * @param {int} product_id ID del producto a mostrar
     */
    function apd_trigger_load_content( product_id ) {
        jQuery.post( apd_site_config.ajaxurl, {
            'action': 'apd_get_product',
            'product_id': product_id
        }, function( response ) {
            apd_load_content_to_html( response )
        });
    }
}

/**
 * Encargada de cargar el contenido obtenido mediante ajax al HTML del sitio, se deja separada de la obtención de información en caso de que necesite ser modificada.
 * @since 1.0.0
 * 
 * @param {string} content Texto HTML con el contenido del producto.
 */
function apd_load_content_to_html( content ) {
    jQuery( '.apd-general-container' ).html( content )
}