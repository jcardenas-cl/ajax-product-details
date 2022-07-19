<?php
/**
 * Plantilla utilizada para los casos en donde un producto con 2 o mas atributos se muestran en un mismo select, 
 * por ejemplo dado un producto con atriburos de color y talla, se vería de esta manera:
 * 
 * Rojo - M
 * Rojo - L
 * Azúl - S
 * Azúl - M
 */
$variations         = $product->get_available_variations();
$display_variations = array();
$i                  = 0;
foreach ( $variations as $variation ):
    $status = '';
    $variation_id 	= $variation['variation_id'];
    $variation_obj	= new WC_Product_variation($variation_id);
    if ( $variation_obj->managing_stock() ) {
        if ( is_numeric($variation_obj->get_stock_quantity()) and $variation_obj->get_stock_quantity() <= 0 ) {
            $status = 'disabled no-units';
        }
    } else {
        if ( !$variation_obj->is_in_stock() ) {
            $status = 'disabled no-stock';
        }
    }
    $price			= wc_price( $variation_obj->get_price() );
    $title_encoded 	= htmlentities(get_the_title( $variation_obj->get_ID() ));
    $index_start	= strpos( $title_encoded, '&amp;#8211;') + 12;
    $title			= substr( $title_encoded, $index_start, strlen($title_encoded));

    $values = array(
        'variation_id'  => $variation_obj->get_ID(),
        'price'         => $price,
        'stock'			=> $variation_obj->get_stock_quantity(),
    );

    $display_variations[$i]['status']   = $status;
    $display_variations[$i]['value']    = esc_html( json_encode($values));
    $display_variations[$i]['title']    = $title;
    $i++;
endforeach;
?>

<label for="apd-cbo-variation"><?php _e( 'Variedad', 'ajax-product-details' ); ?></label>
<select class="apd-cbo-variation" name="apd-cbo-variation">
    <option value="-1"><?php _e( '- Seleccione -', 'ajax-product-details' ); ?></option>
    <?php
    for ( $i = 0; $i <= count( $display_variations) - 1; $i++ ):
        echo '<option status="'.$display_variations[$i]['status'].'" value="'.$display_variations[$i]['value'].'">'.$display_variations[$i]['title'].'</option>';
    endfor;
    ?>
</select>