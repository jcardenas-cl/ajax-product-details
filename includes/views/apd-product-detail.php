<div class="apd-product-container">
    <div class="apd-image-container">
    <?php
        if ( has_post_thumbnail($post->ID) ) {
            $product_image = wp_get_attachment_url( $product->get_image_id() );
        } else { 
            $upload_dir = wp_upload_dir();
            $product_image = $upload_dir['baseurl'] . '/woocommerce-placeholder.png';
        }
        ?>
        <img src="<?php echo $product_image; ?>" class="product-image"/>
    </div>
    <div class="apd-product-information-container">
        <h2 class="apd-product-title">
            <?php echo $product->get_title(); ?></h2>
        <p class="apd-product-description">
            <?php echo $product->get_description(); ?></p>
        <p class="apd-product-price">
            <?php 
            if ( $product->is_type('variable')) {
                $min_price = $product->get_variation_price( 'min' );
                $max_price = $product->get_variation_price( 'max' );

                echo wc_format_price_range( $min_price, $max_price );
                echo '<input type="hidden" class="apd-price-range" value="' . esc_html(wc_format_price_range( $min_price, $max_price )) . '" />';
            } else {
                echo wc_price( $product->get_price() );
            }
            ?></p>
        <div class="properties">
            <?php
                if ( isset( $variations ) ) {
                    foreach ( $variations as $key => $value ) :
                        $options = $value;
            ?>
            <div class="variation">
                <label 
                    class="lbl-attribute" 
                    for="<?php echo $key; ?>">
                    <?php echo wc_attribute_label( str_replace('attribute_', '', $key), $product ); ?></label>
                <select
                    name="<?php echo $key; ?>"
                    class="<?php echo $key; ?> apd-cbo-variation">
                    <option value="-1"><?php echo _e('-Seleccione-', 'simple-add-to-cart'); ?></option>
                    <?php 
                        for ( $i = 0; $i <= count( $options ) - 1; $i++ ) {
                            echo "<option value=\"$options[$i]\">$options[$i]</option>";
                        }
                        ?>
                </select>
            </div>
            <?php endforeach; ?>
            <input
                type="hidden"
                name="apd-variation-data"
                class="apd-variation-data"
                value="<?php echo esc_html(json_encode( $variations_json )); ?>"/>
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
            <?php
                }
            ?>
        </div>
        <div class="quantity-container">
            <label
                for="apd-product-quantity"
                class="lbl-product-quantity">
                <?php _e( 'Cantidad', 'ajax-product-details' ); ?></label>
            <input
                type="number"
                class="apd-product-quantity"
                name="apd-product-quantity"
                value="1"/>
        </div>
        <div class="buy-buttons">
            <input
                type="button"
                class="apd-add-to-cart"
                value="<?php echo esc_html( $product->single_add_to_cart_text() ); ?>"/>
        </div>
    </div>
</div>