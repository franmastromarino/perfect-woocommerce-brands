<?php
/**
 * The template for displaying filter by brand widget
 * @version 1.0.0
 */

 namespace Perfect_Woocommerce_Brands\Templates;

 defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

 extract( $data );
?>

<div class="pwb-filter-products" data-cat-url="<?php echo $cate_url;?>">
  <ul>
    <?php foreach( $brands as $brand ): ?>
      <li>
        <label>
          <input type="checkbox" data-brand="<?php echo $brand->term_id;?>" value="<?php echo $brand->slug;?>"><?php echo $brand->name; ?>
        </label>
      </li>
    <?php endforeach; ?>
  </ul>
  <button><?php _e('Apply filter','perfect-woocommerce-brands') ?></button>
</div>
