
<div class="apd-modal apd-override">

    <button class="modal-close">&times;</button>
    <div><div class="close-handler"></div></div>
    
    <div class="apd-content-container">
        <h2 class="product-title"><?php echo $product->get_name(); ?></h2>
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
                            echo "<div class='g-item'>&bull;</div> ";
                        }
                        echo '</div>';
                    }
                ?>
            </div>

            <!-- Sección con los contenidos -->
            <div class="modal-content">
                <div><?php echo $product->get_short_description(); ?></div>
                <div><?php echo wc_price($product->get_price()); ?></div>
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
                    <div><span class="in-stock"><?php _e('En stock','ajax-product-details'); ?></span></div>
                    <input type="number" name="txt-quantity" value="1">
                </div>

                <button class="apd-add-to-cart">Agregar al carrito</button>
                
                <div>
                    <a 
                    class="view-full-details"
                    href="#">Ver todos los detalles</a>
                </div>
            </div>
        </div>
    </div>
</div>
