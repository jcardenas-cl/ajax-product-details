<?php
// Obtener la información de los ajustes
$variation_mode = get_option('apd-variation-mode');
$image_action   = get_option('apd-image-click');
$overlay_color  = get_option('apd-overlay-color');
$buttons_color  = get_option('apd-buttons-color');
$text_color     = get_option('apd-text-color');
$button_style   = get_option( 'apd-quickview-style' );
?>
<div class="apd-general-container">
    <h3><?php _e('Elige la manera de mostrar los productos variables', 'ajax-product-details'); ?></h3>
    <p><?php _e('Para los productos variables, puedes elegir entre mostrar todas las variedades en un solo select o separar cada atributo, de esta manera por ejemplo:', 'ajax-product-details' ); ?></p>
    <div class="variarion-select-example">
        <div class="unified-container">
            <div class="title"><?php _e('Unificado', 'ajax-product-details' ); ?></div>
            <div class="content">
                <label for="cbo-unified-example"><?php _e( 'Variedad', 'ajax-product-details' ); ?></label><br>
                <select name="cbo-unified-example">
                    <option value="">Rojo - S</option>
                    <option value="">Rojo - M</option>
                    <option value="">Verde - M</option>
                    <option value="">Verde - M</option>
                </select>
            </div>
        </div>
        <div class="standard-container">
            <div class="title"><?php _e('Estandar', 'ajax-product-details' ); ?></div>
            <div class="content">
            <label for="cbo-color-example"><?php _e( 'Color', 'ajax-product-details' ); ?></label><br>
            <select name="cbo-color-example">
                <option value="">Rojo</option>
                <option value="">Verde</option>
            </select>
            <br>
            <label for="cbo-size-example"><?php _e( 'Talla', 'ajax-product-details' ); ?></label><br>
            <select name="cbo-size-example">
                <option value="">S</option>
                <option value="">M</option>
                <option value="">L</option>
            </select>
            </div>
        </div>
    </div>

    <form action="" method="post" name="msi-form">
    <div>
        <p><?php _e( 'Selecciona el modo en que se deberia mostrar los productos variables:', 'ajax-product-details' ); ?></p>

        <label for="rd-variation-standard"><?php _e( 'Estandar', 'ajax-product-details' ); ?>
        <input type="radio" name="rd-variation" id="rd-variation-standard" value="standard" <?php if( $variation_mode == 'standard') echo 'checked'; ?>></label>
        <label for="rd-variation-unified"><?php _e( 'Unificado', 'ajax-product-details' ); ?>
        <input type="radio" name="rd-variation" id="rd-variation-unified" value="unified" <?php if( $variation_mode == 'unified') echo 'checked'; ?>></label>
    </div>
    <br>
    <h3>Mostrar quickview al pulsar sobre la imágen</h3>
    <p><?php _e( 'Selecciona que ocurre cuando se pulsa sobre la imágen del producto' ); ?></p>
    <div>
        <label><?php _e( 'Ir a la url con el detalle del producto', 'ajax-product-details' ); ?>
        <input type="radio" name="rd-image-click" id="" value="go-to-product-page" <?php if( $image_action == 'go-to-product-page') echo 'checked'; ?>></label>
        <label><?php _e( 'Mostrar la vista rápida', 'ajax-product-details' ); ?>
        <input type="radio" name="rd-image-click" id="" value="show-quickview" <?php if( $image_action == 'show-quickview') echo 'checked'; ?>></label>
    </div>
    <br>
    <h3>Colorpicker para botones y overlay</h3>
    <p><?php _e('Selecciona el color de fondo (seccion semitrasparente) en la vista rápida.', 'ajax-product-details'); ?></p>
    <input type="text" name="overlay-color" id="" value="<?php echo $overlay_color; ?>" data-coloris>
    <p><?php _e('Selecciona el color de los botones', 'ajax-product-details'); ?></p>
    <input type="text" name="button-color" id="" value="<?php echo $buttons_color; ?>" data-coloris>
    <p><?php _e('Selecciona el color de letra'); ?></p>
    <input type="text" name="text-color" id="" value="<?php echo $text_color; ?>" data-coloris>
    <!--
    <br>
    <h3>Elegir estilo botón quick view</h3>
    <p><?php _e('Selecciona el modo en que se debe mostrar el botón para vista rápida', 'ajax-product-details'); ?></p>
        <label><?php _e('Botón', 'ajax-product-details'); ?>
        <input type="radio" name="button-style" value="button" id="" <?php if( $button_style == 'button') echo 'checked'; ?>></label>
        <label><?php _e('Enlace', 'ajax-product-details'); ?>
        <input type="radio" name="button-style" value="link" id="" <?php if( $button_style == 'link') echo 'checked'; ?>></label>
    <h3>Elegir plantilla</h3>
    <h3>Mostrar reviews del prod.</h3>-->
    <?php submit_button(__('Guardar', 'my_site_info'), 'primary'); ?>
    </form>
    <style>
        .apd-general-container {
            width: 60%;
            margin: auto;
            min-width: 800px;
        }
        .variarion-select-example {
            display: flex;
            border: 1px solid #cdcdcd;
        }
        .unified-container,
        .standard-container {
            width: 50%;
        }
        .unified-container {
            border-right: 1px solid #cdcdcd;
        }
        .variarion-select-example .title {
            background-color: #fff;
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #cdcdcd;
        }
        .variarion-select-example .content {
            padding: 5px;
        }
    </style>
</div>