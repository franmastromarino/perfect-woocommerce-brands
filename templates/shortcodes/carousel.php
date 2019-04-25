<?php
/**
 * The template for displaying the carousels
 * @version 1.0.0
 */

 defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>

<div class="pwb-carousel" data-slick="<?php echo $slick_settings; ?>">

  <?php foreach( $brands as $brand ): ?>
    <div class="pwb-slick-slide">
      <a href="<?php echo $brand['link'];?>" title="<?php echo $brand['name']; ?>">
        <?php echo $brand['attachment_html'];?>
      </a>
    </div>
  <?php endforeach; ?>

  <div class="pwb-carousel-loader"><?php _e('Loading','perfect-woocommerce-brands');?>...</div>

</div>
