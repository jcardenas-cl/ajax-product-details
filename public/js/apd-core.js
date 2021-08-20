jQuery( document ).ready( function() {
    jQuery( '.apd-quick-view' ).click( function() {
        const product_id    = jQuery( this ).attr('product-id')
        const me            = jQuery( this )
        jQuery.post( apd_site_config.ajaxurl, {
            'action': 'apd_get_product',
            'product_id': product_id
        }, function( response ) {
            apd_load_content_to_html( response )
            apd_action_triggers()
        });
    });
});

function apd_action_triggers() {
    jQuery('.apd-cbo-variation').change( function() {
        const variations_selected   = jQuery('.apd-cbo-variation')
        let variations_avalilable   = jQuery('.apd-variation-data').val()
        variations_avalilable       = JSON.parse(variations_avalilable)
        // Recorrer las variedades seleccionadas
        let selected_elements       = Array()
        jQuery(variations_selected).each( function() {
            let obj_selected        = {}
            const attribute         = jQuery( this ).attr('name')
            const value             = jQuery( this ).val()
            obj_selected.att_name   = attribute
            obj_selected.att_value  = value
            selected_elements.push(obj_selected)
        })
        
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
    });

    jQuery('.apd-add-to-cart').click( function() {
        const product_id            = jQuery('.apd-product-id').val()
        const variation_id          = jQuery('.apd-variation-id').val()
        if ( jQuery('.apd-product-quantity').length == 1 ) {
            const quantity              = jQuery('.apd-product-quantity').val()
        } else {
            const quantity              = 1
        }
        
        const variations_selected   = jQuery('.apd-cbo-variation')

        // Validar que haya seleccionado valores para cada variedad
        if ( !apd_has_selected_valid_variations( variations_selected ) ) {
            jQuery('.apd-status-cart').html('Seleccione todas las variedades antes de agregar')
        } else if ( !apd_is_valid_quantity( quantity ) ) {
            jQuery('.apd-status-cart').html('La cantidad debe ser al menos de 1')
        } else {
            apd_add_to_cart( product_id, variation_id, quantity )
        }

    });
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

function apd_is_valid_quantity( quantity ) {
    if ( quantity > 0 ) {
        return true
    }

    return false
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
    function apd_load_content_to_html( content ) {
        jQuery( '.apd-product-detail' ).remove()
        jQuery( 'ul.products' ).after(`<div id="apd-product-detail" class="apd-product-detail">${content}</div>`)
        jQuery('html, body').stop().animate({
            scrollTop: jQuery( '#apd-product-detail' ).offset().top 
        }, 500)
    }
}

if ( typeof apd_add_to_cart !== 'function' ) {
    function apd_add_to_cart( product_id, variation_id, quantity ) {
        jQuery.post(
            site_config.ajaxurl,
            {
                'action': 'apd_add_to_cart',
                'product_id': product_id,
                'variation_id': variation_id,
                'quantity': quantity
            }, function ( response ) {
                if ( response.status == 'OK' ) {
                    jQuery( parent ).find('.status-cart').html('Producto agregado con éxito<br> ir a la <a href="/cart">carta</a>')
                } else {
                    jQuery( parent ).find('.status-cart').html('Algo falló')
                }
            }
        )
    }
}