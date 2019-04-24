<?php
/**
* The template for displaying the list logo widget
* @version 1.0.0
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>

<ul class="pwb-row">

  <?php foreach( $data['brands'] as $brand ): ?>

    <li class="<?php echo $data['li_class'];?>">

      <a href="<?php echo $brand->get('link');?>" title="<?php echo $data['title_prefix'] . ' ' . $brand->get('name'); ?>">

        <?php if( !empty( html_entity_decode( $brand->get('image') ) ) ): ?>
          <?php echo html_entity_decode( $brand->get('image') ); ?>
        <?php endif; ?>

      </a>

    </li>

  <?php endforeach; ?>

</ul>
