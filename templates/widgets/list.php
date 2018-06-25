<?php
/**
* The template for displaying the list widget
* @version 1.0.0
*/

namespace Perfect_Woocommerce_Brands\Templates;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

extract( $data );
?>

<ul class="pwb-row">

  <?php foreach( $data['brands'] as $brand ): ?>

    <li>
      <a href="<?php echo $brand->get('link');?>" title="<?php echo $data['title_prefix'] . ' ' . $brand->get('name'); ?>">
        <?php echo $brand->get('name'); ?>
      </a>
    </li>

  <?php endforeach; ?>

</ul>
