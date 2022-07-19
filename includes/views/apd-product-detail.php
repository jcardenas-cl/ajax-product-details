<?php
$full_gallery   = array(); // Arreglo con la imágen principal del producto y su galería.
$product_image  = wp_get_attachment_url( $product->get_image_id() );
$full_gallery[] = $product_image; // Asignamos la imagen principal de la galería al arreglo, de esta manera siempre tendrá al menos un elemento.

$attachment_ids = $product->get_gallery_image_ids(); // Obtener los IDs de imágenes correspondientes solo a la galería.
foreach( $attachment_ids as $attachment_id ) {
    $full_gallery[] = wp_get_attachment_url( $attachment_id );
}

if ( $product->is_type('variable')) {
    $min_price = $product->get_variation_price( 'min' );
    $max_price = $product->get_variation_price( 'max' );
    /**
     * En caso que el minimo y el maximo sean iguales, solo mostramos un precio, no tengo claro
     * si exista alguna funcion que haga esto sin la necesitad de programarlo por separado.
     */
    if ( $min_price === $max_price ) {
        $product_price = wc_price( $product->get_price() );
    } else {
        $product_price = wc_format_price_range( $min_price, $max_price );
    }

    $variations = $product->get_available_variations();
} else {
    $product_price = wc_price( $product->get_price() );
}

?>
<div class="apd-product-detail-container d-flex">
    <div class="apd-gallery-container">
        <div
            style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff;"
            class="swiper mySwiper2">
            <div class="swiper-wrapper">
                <?php foreach( $full_gallery as $gallery_image ): ?>
                <div class="swiper-slide">
                    <img src="<?php echo $gallery_image; ?>" class="gallery-main-image"/>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
        <div thumbsslider="" class="swiper mySwiper mt-2">
            <div class="swiper-wrapper">
                <?php foreach( $full_gallery as $gallery_image ): ?>
                <div class="swiper-slide">
                    <img src="<?php echo $gallery_image; ?>"/>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <div class="apd-description-container">
        <h3 class="apd-product-title"><?php echo $product->get_title(); ?></h3>
        <div class="apd-product-price"><?php echo $product_price; ?></div>
        <div class="d-flex">
            <div class="apd-price-and-options">
                <?php if ( $product->is_type( 'variable' ) ): ?>
                <div class="apd-variation-container">
                    <?php require 'fragment-simple-variation.php'; ?>
                </div>
                <?php endif; ?>
                <div class="apd-quantity-container">
                    <label class="label" for="number"><?php _e( 'Cantidad', 'ajax-product-detail' ); ?></label>
                    <input
                        type="number"
                        class="number apd-product-quantity"
                        id="number"
                        value="0">
                </div>
            </div>
            <div class="apd-buy-buttons">
                <input
                    type="hidden"
                    name="apd-product-id"
                    class="apd-product-id"
                    value="<?php echo $product->get_ID(); ?>"/>
                <input
                    type="hidden"
                    name="apd-variation-id"
                    class="apd-variation-id"
                    value="-1"/>
                <?php if ( !$product->is_in_stock() ): ?>
                    <div class="no-stock-lbl">
                        <?php _e( 'Agotado', 'ajax-product-details' ); ?>
                    </div>
                <?php else: ?>
                    <button 
                        standard-text="<?php _e( 'Agregar al carrito', 'ajax-product-details' ); ?>"
                        adding-text="<?php _e( 'Espere...', 'ajax-product-details' ); ?>"
                        class="apd-add-to-cart d-block py-1 text-center mb-3"><?php _e( 'Agregar al carrito', 'ajax-product-details' ); ?></button>
                <?php endif; // Stock ?>
                <div class="apd-notify"></div>
            </div>
        </div>
        <div class="apd-long-description-container">
            <?php echo $product->get_description(); ?>
        </div>
    </div>

</div>