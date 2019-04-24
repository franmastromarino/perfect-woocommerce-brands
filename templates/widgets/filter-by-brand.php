<?php
/**
 * The template for displaying filter by brand widget
 * @version 1.0.0
 */

 defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>

<div class="pwb-filter-products<?php if( $hide_submit_btn ) echo ' pwb-hide-submit-btn'; ?>" data-cat-url="<?php echo $cate_url;?>">
  <ul>
    <?php foreach( $brands as $brand ): ?>
      <li>
        <label>
          <input type="checkbox" data-brand="<?php echo $brand->term_id;?>" value="<?php echo $brand->slug;?>"><?php echo $brand->name; ?>
        </label>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php if( !$hide_submit_btn ): ?>
    <button><?php _e('Apply filter','perfect-woocommerce-brands') ?></button>
  <?php endif;?>
</div>
