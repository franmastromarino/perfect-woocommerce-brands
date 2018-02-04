<?php
/**
 * The template for displaying the edit-tags.php table footer
 * @version 1.0.0
 */

 namespace Perfect_Woocommerce_Brands\Templates;

 defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

 extract( $data );
?>

<div class="pwb-edit-brands-bottom pwb-clearfix">
  <span class="dashicons dashicons-admin-collapse"></span>
  <p class="pwb-featured-count">
    <span><?php echo $data['featured_count'];?></span> <?php echo $data['text_featured'];?>
  </p>
</div>
