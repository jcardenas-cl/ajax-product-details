<?php
$attributes             = $product->get_attributes();
$available_variations   = $product->get_available_variations();
if ( empty( $available_variations ) && false !== $available_variations ) : ?>
    <p class="stock out-of-stock">
        <?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'woocommerce' ) ) ); ?></p>
<?php else:
    foreach ( $attributes as $attribute_name => $options ):
        ?>
        <div class="apd-variation-row">
            <label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo wc_attribute_label( $attribute_name ); ?></label>
            <?php
            wc_dropdown_variation_attribute_options( array(
                'options'   => $options->get_options(),
                'attribute' => $attribute_name,
                'product'   => $product,
                'class'     => 'apd-variation-select',
            ) );
            ?>
        </div>
        <?php
    endforeach;
    ?>
    <input type="hidden" class="apd_product_variations" value="<?php echo htmlspecialchars( wp_json_encode( $available_variations ) ); ?>">
    <?php
endif;