<?php
/**
 * The template for displaying the edit-tags.php exporter/importer
 * @version 1.0.0
 */

 defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>

<div class="pwb-brands-exporter pwb-clearfix">
  <button class="button pwb-brands-export"><?php _e('Export brands', 'perfect-woocommerce-brands');?></button>
  <button class="button pwb-brands-import"><?php _e('Import brands', 'perfect-woocommerce-brands');?></button>
  <input type="file" class="pwb-brands-import-file" accept="application/json">
  <p><?php _e( 'This tool allows you to export and import the brands between different sites using PWB.', 'perfect-woocommerce-brands' );?></p>
</div>
