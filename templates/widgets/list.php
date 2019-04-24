<?php
/**
* The template for displaying the list widget
* @version 1.0.0
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
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
