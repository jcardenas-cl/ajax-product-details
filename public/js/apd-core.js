/**
 * Agrega al carrito de compras un producto o variedad, con la cantidad seleccionada por el usuario.
 * 
 * @param {int} product_id ID del producto
 * @param {int} variation_id ID de la variedad en caso de existir
 * @param {int} quantity Cantidad a agregar
 * @returns {json} Resultado obtenido desde el servidor
 */
async function apd_add_to_cart (product_id, variation_id, quantity) {
    if ( !product_id || !variation_id || !quantity ) throw new Error('Revisar la lista de parametros')
    let form_data = new FormData()
    form_data.append( 'action', 'apd_add_to_cart' )
    form_data.append( 'product_id', product_id )
    form_data.append( 'variation_id', variation_id )
    form_data.append( 'quantity', quantity )
    const promise = await fetch( apd_site_config.ajaxurl, {
        'method': 'post',
        'body': form_data
    })

    return await promise.json()
}

if ( typeof apd_trigger_load_content !== 'function' ) {
    /**
     * Funcion ejecutada al momento de hacer clic sobre el botón "Vista Rápida". Inicia la secuencia de cargar el contenido a la ventana modal según el producto seleccionado.
     * @since 1.0.0
     * 
     * @param {int} product_id ID del producto a mostrar
     */
    function apd_trigger_load_content (product_id) {
        jQuery.post( apd_site_config.ajaxurl, {
            'action': 'apd_get_product',
            'product_id': product_id
        }, function( response ) {
            apd_load_content_to_html( response )
        });
    }
}

if ( typeof apd_load_content_to_html !== 'function' ) {
    /**
     * Encargada de cargar el contenido obtenido mediante ajax al HTML del sitio, se deja separada de la obtención de información en caso de que necesite ser modificada.
     * Ejecutada mediante la función apd_trigger_load_content luego de obtener el contenido del producto.
     * 
     * @since 1.0.0
     * 
     * @param {string} content Texto HTML con el contenido del producto.
     */
    function apd_load_content_to_html (content) {
        jQuery( '.apd-overlay' ).html( content )
        jQuery( '.apd-overlay' ).removeClass('apd-hidden')

        apd_action_triggers()
    }
}

/**
 * Funcion encargada de inicializar todos los eventos JS en la ventana modal con los detalles del producto.
 * Al cargar de manera asincrona, se debe volver a cargar los eventos de JS en los nuevos elementos de la página.
 * @since 1.0.0
 */
 function apd_action_triggers () {
    if ( document.getElementsByName('.apd-cbo-variation').length > 0 ) {
        const variation_select = document.querySelector('.apd-cbo-variation')
        variation_select.addEventListener('change', () => {
            const parent    = variation_select.closest('.apd-product-detail-container')
            const values    = JSON.parse(variation_select.value)
    
            parent.querySelector('.apd-product-price').innerHTML    = values.price
            parent.querySelector('.apd-variation-id').value         = values.variation_id
            if ( values.stock ) {
                parent.querySelector('.apd-product-quantity').setAttribute('max-quantity', values.stock)
            }
        })
    }

    document.querySelector('.apd-add-to-cart').addEventListener('click', () => {
        apd_init_add_to_cart()
    })

    jQuery( '.apd-modal .modal-close' ).click( function() {
        jQuery( '.apd-overlay' ).addClass('apd-hidden')
    })

    init_swiper()
    apd_check_quantity()
    init_attribute_actions()
    apd_change_quantity_buttons()
}

/**
 * Inicializa el slider del producto.
 * @since 1.0.0
 */
function init_swiper () {
    const swiper = new Swiper(".mySwiper", {
        spaceBetween: 10,
        slidesPerView: 3,
        freeMode: true,
        watchSlidesProgress: true
    });
    const swiper2 = new Swiper(".mySwiper2", {
        spaceBetween: 10,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev"
        },
        thumbs: {
            swiper: swiper
        }
    });
}

/**
 * Controla la cantidad minima y máxima al momento de ingresar la cantidad de un producto a comprar.
 * @since 1.0.0
 */
 function apd_check_quantity () {
    jQuery( '.apd-product-quantity' ).on( 'change', function() {
        const quantity      = jQuery( this ).val()
        const max_quantity  = jQuery( this ).attr('max-quantity')
        if ( quantity > max_quantity ) {
            jQuery( this ).val( max_quantity )
        }
        if ( quantity < 0 ) {
            jQuery( this ).val( 0 )
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

/**
 * Valida que se haya seleccionado una variedad, se debe reformar para considerar cada una de las variedades disponibles.
 * @since 1.0.0
 * 
 * @param {Elements} variations Lista de elementos con la variedad elegida.
 * @returns {Boolean} true en caso de que se haya seleccionado una variedad valida para todas las opciones, false en caso contrario.
 */
function apd_has_selected_valid_variations( variations ) {
    variations.forEach( element => {
        if ( element.value == '-1' ) {
            return false
        }
    })

    return true
}

/**
 * Revisa que la cantidad elegida por el usuario, se encuentre entre el rango disponible a la venta.
 * @since 1.0.0
 * 
 * @param {int} quantity Cantidad a agregar.
 * @returns {boolean} Falso en caso que no cumpla con los requisitos, Verdadero si no hay problemas.
 */
 function apd_is_valid_quantity( quantity ) {
    const max_quantity  = parseInt(document.querySelector('.apd-product-quantity').getAttribute( 'max-quantity' ))
    if ( quantity <= 0 ) return false
    if ( undefined != max_quantity ) {
        if ( quantity > max_quantity ) return false   
    }

    return true
}

/**
 * Disparado en el momento que se hace clic sobre el boton agregar al carrito.
 * Verifica que se haya seleccionado una variedad (de ser el caso) e ingresado una cantidad valida de un producto. Si todo es correcto, llama a la funcion para agregar
 * el producto al carro y muestra un mensaje con el resultado de la operación.
 * @since 1.0.0
 */
async function apd_init_add_to_cart () {
    const add_button            = document.querySelector('.apd-add-to-cart')
    const product_id            = document.querySelector('.apd-product-id').value
    const variation_id          = document.querySelector('.apd-variation-id').value
    const quantity              = document.querySelector('.apd-product-quantity').value
    const variations_selected   = document.querySelectorAll('.apd-cbo-variation')

    // Validar que haya seleccionado valores para cada variedad
    if ( !apd_has_selected_valid_variations( variations_selected ) ) {
        alert("Seleccione variedad")
    } else if ( !apd_is_valid_quantity( quantity ) ) {
        alert("Seleccione cantidad")
    } else {
        add_button.innerHTML    = add_button.getAttribute('adding-text')
        const result            = await apd_add_to_cart(product_id, variation_id, quantity)
        if (result.status == 'OK') {
            document.querySelector('.apd-notify').classList.add('result-ok')
        } else {
            document.querySelector('.apd-notify').classList.add('result-error')
        }
        document.querySelector('.apd-notify').innerHTML = result.message
        add_button.innerHTML = add_button.getAttribute('standard-text')
    }
}

const apd_quick_view_buttons = document.querySelectorAll('.apd-quick-view')
apd_quick_view_buttons.forEach( quick_view_button => {
    const product_id = quick_view_button.getAttribute('product-id')
    quick_view_button.addEventListener('click', (btn) => {
        this.innerText = 'Espere'
        apd_trigger_load_content( product_id )
    })
})

/**
 * En base a los atributos seleccionados de un producto, muestra los datos de la combinación, como precio, stock, etc.
 */
function apd_display_variation_info() {
    const attribute_selects = document.querySelectorAll('.apd-variation-select')
    const variation_data    = JSON.parse(document.querySelector('.apd_product_variations').value)
    let user_selection = {}
    attribute_selects.forEach( select => {
        if ( select.value == '' ) { 
            // Reestablecer a vista inicial
            document.querySelector('.apd-variation-description').innerHTML = ''
            document.querySelector('.apd-variation-description').classList.add('apd-hidden')
            document.querySelector('.apd-short-description').classList.remove('apd-hidden')
            document.querySelector('.apd-product-regular-price').classList.remove('apd-hidden')
            document.querySelector('.apd-product-variation-price').classList.add('apd-hidden')
            document.querySelector('.apd-gallery-container').classList.remove('apd-hidden')
            document.querySelector('.apd-variation-image-container').classList.add('apd-hidden')
        }
        user_selection[select.getAttribute('name')] = select.value
    })

    variation_data.forEach( item => {
        const attributes = item.attributes
        if ( lodash.isEqual( attributes, user_selection ) ) {
            console.log(item)
            document.querySelector('.apd-product-variation-price').innerHTML    = item.price_html
            document.querySelector('.apd-hint-quantity').innerHTML              = item.availability_html
            document.querySelector('.apd-variation-description').innerHTML      = item.variation_description
            document.querySelector('.apd-variation-id').value                   = item.variation_id
            document.querySelector('.apd-product-quantity').setAttribute('apd-max-quantity', item.max_qty)
            document.querySelector('.apd-product-quantity').setAttribute('apd-min-quantity', item.min_qty)
            document.querySelector('.apd-product-regular-price').classList.add('apd-hidden')
            document.querySelector('.apd-product-variation-price').classList.remove('apd-hidden')
            document.querySelector('.apd-variation-description').classList.remove('apd-hidden')
            document.querySelector('.apd-short-description').classList.add('apd-hidden')

            if ( item.image ) {
                document.querySelector('.apd-variation-image-container').innerHTML = `<img src="${item.image.url}" />`
                document.querySelector('.apd-gallery-container').classList.add('apd-hidden')
                document.querySelector('.apd-variation-image-container').classList.remove('apd-hidden')
            }
        }
    })
}

function init_attribute_actions () {
    const attribute_selects = document.querySelectorAll('.apd-variation-select')
    attribute_selects.forEach(element => {
        element.addEventListener('change', () => { apd_display_variation_info() })
    });
}

/**
 * 
 */
function apd_change_quantity_buttons () {
    const increase_btn  = document.querySelector('.apd-increase-quantity')
    const decrease_btn  = document.querySelector('.apd-decrease-quantity')
    const quantity      = document.querySelector('.apd-product-quantity')

    increase_btn.addEventListener('click', () => {
        if ( '' == quantity.getAttribute('apd-max-quantity') || quantity.value < quantity.getAttribute('apd-max-quantity') ) {
            quantity.value = parseInt(quantity.value) + 1
        }
    })

    decrease_btn.addEventListener('click', () => {
        if ( quantity.value > quantity.getAttribute('apd-min-quantity') ) {
            quantity.value = parseInt(quantity.value) - 1
        }
    })
}