
<div class="apd-modal">
    <!-- Botón de cerrar -->
    <button class="modal-close">&times;</button>
    <div class="apd-modal-content-container">
        <!-- Sección con la galería -->
        <div class="product-gallery-section">
            <div class="modal-image">
                <img src="<?php echo esc_url(get_product_image_url($product->get_ID())); ?>" class="main-image" alt="Imagen principal">
            </div>

            <?php
                $gallery_images = get_product_gallery_image_url($product->get_ID());

                if ($gallery_images) {
                    echo '<div class="modal-gallery">';
                    foreach ($gallery_images as $imagen_url) {
                        echo '<img class="gallery-thumb" src="'.esc_url($imagen_url).'" alt="Miniatura 1">';
                    }
                    echo '</div>';
                }
            ?>
        </div>

        <!-- Sección con los contenidos -->
        <div class="modal-content">
            <h2><?php echo $product->get_name(); ?></h2>
            <div><?php echo wc_price($product->get_price()); ?></div>
            <p><?php echo $product->get_short_description(); ?></p>
            <?php
            $variation = get_product_variations_by_attribute($product->get_ID());
            $variation = $variation['attributes'];
            foreach ( $variation as $variationName => $variationValue ) {
                ?>
                <div class="variation-row">
                    <span>Elige <?php echo $variationName; ?>:</span>
                    <div class="modal-options">
                        <?php
                            foreach( $variationValue as $variationLabel ) {
                                ?>
                                <button value="<?php echo esc_attr($variationLabel); ?>"><?php echo $variationLabel; ?></button>
                                <?php
                            }
                        ?>
                    </div>
                </div>
                <?php
            }
            ?>

            <div class="quantity">
                <div><span class="in-stock">En stock</span></div>
                <input type="number" name="txt-quantity" value="1"><button class="apd-add-to-cart">Agregar al carrito</button>
            </div>
            
            <div>
                <a href="#"><small>Ver todos los detalles de <?php echo $product->get_name(); ?></small></a>
            </div>
        </div>
    </div>
</div>
