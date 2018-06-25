<?php
/**
 * The template for displaying the dropdown widget
 * @version 1.0.0
 */

 namespace Perfect_Woocommerce_Brands\Templates;

 defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

 extract( $data );

?>

<select class="pwb-dropdown-widget">
  <option selected="true" disabled="disabled"><?php _e( 'Brands', 'perfect-woocommerce-brands' ); ?></option>
  <?php foreach( $brands as $brand ): ?>
    <option value="<?php echo $brand->get('link');?>" <?php selected( $data['selected'], $brand->get('id') );?>>
      <?php echo $brand->get('name');?>
    </option>
  <?php endforeach; ?>
</select>
