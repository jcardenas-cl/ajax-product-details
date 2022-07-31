<?php
$gallery_images = array(); // Imagenes a tamaño completo
$gallery_thumbs = array();  // Imagenes a escala para mostrar en las miniaturas de la galería

$gallery_images[]   = wp_get_attachment_url( $product->get_image_id(), 'full' );
$gallery_thumbs[]   = wp_get_attachment_url( $product->get_image_id(), array(300,300) );

$attachment_ids     = $product->get_gallery_image_ids(); // Obtener los IDs de imágenes correspondientes solo a la galería.
foreach( $attachment_ids as $attachment_id ) {
    $gallery_images[] = wp_get_attachment_image_src( $attachment_id, 'full' )[0];
    $gallery_thumbs[] = wp_get_attachment_image_src( $attachment_id, array(300,300) )[0];
}

if ( $product->is_type('variable') ) {
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
} else {
    $product_price = wc_price( $product->get_price() );
}

// Obtener la configuracion de colores ajustada por el usuario
$buttons_color  = get_option('apd-buttons-color');
$text_color     = get_option('apd-text-color');

$button_color_style = ( '' != $buttons_color ) ? 'background-color: ' . $buttons_color . ';' : '';
$text_color_style   = ( '' != $text_color ) ? 'color: ' . $text_color . ';' : '';
$style_button_css   = ( '' != $button_color_style or '' != $text_color_style ) ? ' style="' . $button_color_style.$text_color_style . '" ' : '';
?>
<div class="apd-product-detail-container d-flex">
    <div class="apd-gallery-container">
        <div
            style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff;"
            class="swiper mySwiper2">
            <div class="swiper-wrapper">
                <?php foreach( $gallery_images as $gallery_image ): ?>
                <div class="swiper-slide">
                    <img src="<?php echo esc_url($gallery_image); ?>" class="gallery-main-image"/>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
        <div thumbsslider="" class="swiper mySwiper mt-2">
            <div class="swiper-wrapper">
                <?php foreach( $gallery_thumbs as $gallery_thumb ): ?>
                <div class="swiper-slide">
                    <img src="<?php echo esc_url($gallery_thumb); ?>"/>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="apd-description-container">
        <h3 class="apd-product-title"><?php echo $product->get_title(); ?></h3>
        <div class="apd-product-price"><?php echo $product_price; ?></div>
        <div class="apd-short-description">
            <?php echo $product->get_short_description(); ?>
        </div>
        <div class="apd-variation-description"></div>
        <div class="d-flex">
            <div class="apd-price-and-options">
                <?php if ( $product->is_type( 'variable' ) ): ?>
                <div class="apd-variation-container">
                    <?php 
                    if ( 'unified' == get_option('apd-variation-mode') ) {
                        require 'fragment-simple-variation.php';
                    } else {
                        require 'fragment-standard-variation.php';
                    }
                    ?>
                </div>
                <?php endif; ?>
                <div class="apd-quantity-container">
                    <label class="label" for="number"><?php _e( 'Cantidad', 'ajax-product-detail' ); ?></label>
                    <button>-</button>
                    <input
                        type="number"
                        class="number apd-product-quantity"
                        id="number"
                        value="0">
                    <button>+</button>
                    <div class="apd-hint-quantity"></div>
                </div>
            </div>
            <div class="apd-related-products">
                <strong><?php _e( 'Tal vez te interese', 'ajax-product-details' ); ?></strong>
                <div class="products">
                    <?php
                    $upsells = $product->get_upsells(); // Retorna el ID de los productos
                    foreach( $upsells as $product_id ):
                        $upsell_product = new WC_Product( $product_id );
                        ?>
                        <a href="<?php echo esc_url($upsell_product->get_permalink()); ?>">
                            <img 
                                class="apd-upsell-product-image"
                                src="<?php echo esc_url(wp_get_attachment_url( $upsell_product->get_image_id() )); ?>"
                                alt="<?php echo esc_attr( $upsell_product->get_title() ); ?>"
                                title="<?php echo esc_attr( $upsell_product->get_title() ); ?>"></a>
                        <?php
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
        <div class="apd-buy-buttons">
            <input
                type="hidden"
                name="apd-product-id"
                class="apd-product-id"
                value="<?php echo esc_attr($product->get_ID()); ?>"/>
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
                    <?php echo $style_button_css; ?>
                    standard-text="<?php echo esc_attr( __( 'Agregar al carrito', 'ajax-product-details' ) ); ?>"
                    adding-text="<?php echo esc_attr( __( 'Espere...', 'ajax-product-details' ) ); ?>"
                    class="apd-add-to-cart d-block py-1 text-center mb-3"><?php _e( 'Agregar al carrito', 'ajax-product-details' ); ?></button>
            <?php endif; // Stock ?>
            <div class="apd-notify"></div>
        </div>
        
        <div class="apd-long-description-container">
            <?php echo $product->get_description(); ?>
        </div>
    </div>

</div>