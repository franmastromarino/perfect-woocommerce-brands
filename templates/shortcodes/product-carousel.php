<?php
/**
 * The template for displaying the product carousels
 * @version 1.0.0
 */

 namespace Perfect_Woocommerce_Brands\Templates;

 defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

 extract( $data );
?>

<?php if( !empty( $products ) ): ?>

  <div class="pwb-product-carousel" data-slick="<?php echo $slick_settings; ?>">

    <?php foreach( $products as $product ): ?>
      <div class="pwb-slick-slide">
        <a href="<?php echo $product['permalink']; ?>">
          <?php echo $product['thumbnail']; ?>
          <h3><?php echo $product['title']; ?></h3>
          <?php echo do_shortcode('[add_to_cart id="'.$product['id'].'" style=""]'); ?>
        </a>
      </div>
    <?php endforeach; ?>

    <div class="pwb-carousel-loader"><?php _e('Loading','perfect-woocommerce-brands');?>...</div>

  </div>

<?php else: ?>

  <div><?php _e('Nothing found');?></div>

<?php endif; ?>
