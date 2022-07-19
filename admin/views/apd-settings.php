 <h3>Como ver productos variables</h3>
<p><?php _e('Para los productos variables, puedes elegir entre mostrar todas las variedades en un solo select o separar cada atributo, de esta manera por ejemplo:', 'ajax-product-details' ); ?></p>
<p><?php _e('Unificado', 'ajax-product-details' ); ?></p>
Variedad
<select name="" id="">
    <option value="">Rojo - S</option>
    <option value="">Rojo - M</option>
    <option value="">Verde - M</option>
    <option value="">Verde - M</option>
</select>
<p><?php _e('Estandar', 'ajax-product-details' ); ?></p>
Color
<select name="" id="">
    <option value="">Rojo</option>
    <option value="">Verde</option>
</select>
<br>Talla
<select name="" id="">
    <option value="">S</option>
    <option value="">M</option>
    <option value="">L</option>
</select>
<div>
    <label for="rd-variation"><?php _e( 'Estandar', 'ajax-product-details' ); ?>
    <input type="radio" name="rd-variation" id="" value="standard"></label>
    <label for="rd-variation"><?php _e( 'Unificado', 'ajax-product-details' ); ?>
    <input type="radio" name="rd-variation" id="" value="unified"></label>
</div>
<h3>Usar ajax para agregar productos al carrito</h3>
<h3>Mostrar quickview al pulsar sobre la imágen</h3>
<p><?php _e( 'Selecciona que ocurre cuando se pulsa sobre la imágen del producto' ); ?></p>
<div>
    <label><?php _e( 'Ir a la url con el detalle del producto', 'ajax-product-details' ); ?>
    <input type="radio" name="rd-image-click" id="" value="go-to-product-page"></label>
    <label><?php _e( 'Mostrar la vista rápida', 'ajax-product-details' ); ?>
    <input type="radio" name="rd-image-click" id="" value="show-quickview"></label>
</div>
<h3>Colorpicker para botones y overlay</h3>
<p><?php _e('Selecciona el color de fondo (seccion semitrasparente) en la vista rápida.', 'ajax-product-details'); ?></p>
<input type="color" name="overlay-color" id="">
<p><?php _e('Selecciona el color de los botones', 'ajax-product-details'); ?></p>
<input type="color" name="button-color" id="">
<p><?php _e('Selecciona el color de letra'); ?></p>
<input type="color" name="text-color" id="">
<h3>Elegir estilo botón quick view</h3>
<p><?php _e('Selecciona el modo en que se debe mostrar el botón para vista rápida', 'ajax-product-details'); ?></p>
    <label><?php _e('Botón', 'ajax-product-details'); ?>
    <input type="radio" name="button-style" value="buttom" id=""></label>
    <label><?php _e('Enlace', 'ajax-product-details'); ?>
    <input type="radio" name="button-style" value="link" id=""></label>
<h3>Elegir plantilla</h3>
<h3>Mostrar reviews del prod.</h3>