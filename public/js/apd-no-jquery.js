/**
 * Permite agregar un producto (variación y cantidad inclusive) al carrito de compras.
 * @param {int} product_id El ID del producto principal.
 * @param {int} variation_id El ID de la variación.
 * @param {int} quantity La Cantidad de productos a agregar.
 * 
 * @return {bool} True si se agregó correctamente, False en caso contrario.
 */
let apd_add_to_cart = ( product_id, variation_id, quantity ) => {

}

/**
 * Inicia el proceso de obtener la informacion de un producto, esta funcion se dispara al momento de hacer clic sobre el botón bajo el producto en la tienda.
 * @param {int} product_id El ID del producto que obtendremos la información.
 */
let apd_trigger_request_product_info = ( product_id ) => {
    apd_request_info_ajax( product_id )
}

/**
 * Realiza mediante ajax la peticion de información del producto.
 * @param {int} product_id ID del producto que deseamos obtener.
 */
let apd_request_info_ajax = async ( product_id ) => {
    const response = await (apd_site_config.ajax_url)
}