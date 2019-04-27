<?php
/**
 * The template for displaying the edit-tags.php exporter/importer
 * @version 1.8.0
 */

 defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>

<div class="pwb-brands-exporter pwb-clearfix">
  <button class="button pwb-brands-export">
    <?php printf( __('Export %s', 'perfect-woocommerce-brands'), $tax_name['plural'] );?>
  </button>
  <button class="button pwb-brands-import">
    <?php printf( __('Import %s', 'perfect-woocommerce-brands'), $tax_name['plural'] );?>
  </button>
  <input type="file" class="pwb-brands-import-file" accept="application/json">
  <p>
    <?php printf( __( 'This tool allows you to export and import the %s between different sites using PWB.', 'perfect-woocommerce-brands' ), $tax_name['plural'] );?>
  </p>
</div>
