<h2 class="product-title"><?php echo $product->title; ?></h2>
<div class="apd-modal-content-container">
    <!-- Sección con la galería -->
    <div class="product-gallery-section">
        <div class="modal-image">
            <div class="gallery-slider">
                <img 
                    src="<?php echo esc_url($product->main_image); ?>"
                    class="slide active"
                    alt="Imagen principal">
                
                <?php if (is_array($product->gallery)): 
                    foreach ($product->gallery as $imagen_url): ?>
                        <img 
                            src="<?php echo esc_url($imagen_url); ?>"
                            class="slide"
                            alt="Imagen de galería">
                    <?php 
                    endforeach;
                endif; ?>
            </div>
        </div>
        
        <?php if (is_array($product->gallery)): ?>
            <div class="gallery-nav">
                <span class="nav-dot active"></span>
                <?php for($i = 0; $i < count($product->gallery); $i++): ?>
                    <span class="nav-dot"></span>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sección con los contenidos -->
    <div class="modal-content">
        <div><?php echo $product->short_description; ?></div>
        <div class="price">
            <?php echo $product->price_html; ?></div>
        
        <?php
        $variation = $product->variations;
        if ($variation !== false):
            foreach ( $variation->attributes as $attribute ):
                ?>
                <div class="variation-row">
                    <span><?php echo $attribute->label; ?>:</span>
                    <div class="modal-options">
                        <?php
                            foreach( $attribute->options as $option ) {
                                ?>
                                <button 
                                    class="atr-<?=$attribute->name;?> qpd-attr"
                                    value="<?php echo esc_attr($option->value); ?>">
                                    <?php echo $option->label; ?></button>
                                <?php
                            }
                        ?>
                    </div>
                </div>
                <?php
            endforeach;
        endif;
        ?>
        <div class="cart-group">
            <div class="quantity">
                <div><span class="in-stock"><?php _e('En stock','ajax-product-details'); ?></span></div>
                <input 
                    type="number"
                    name="txt-quantity"
                    value="1"
                    max="2">
            </div>

            <button class="apd-add-to-cart">Agregar al carrito</button>
        </div>
        
        <div>
            <a 
            class="view-full-details"
            href="<?php echo $product->permalink; ?>">Ver todos los detalles</a>
        </div>
    </div>
</div>