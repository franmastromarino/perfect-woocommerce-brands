<?php
/**
 * The template for displaying the a-z Listing
 * @version 1.0.1
 */

 defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>

<?php if( !empty( $grouped_brands ) ): ?>

  <div class="pwb-az-listing">

    <div class="pwb-az-listing-header">

      <ul class="pwb-clearfix">

        <?php foreach( $grouped_brands as $letter => $brand_group ): ?>
          <li><a href="#pwb-az-listing-<?php echo $letter;?>"><?php echo $letter;?></a></li>
        <?php endforeach; ?>

      </ul>

    </div>

    <div class="pwb-az-listing-content">

      <?php foreach( $grouped_brands as $letter => $brand_group ): ?>

        <div id="pwb-az-listing-<?php echo $letter;?>" class="pwb-az-listing-row pwb-clearfix">
          <p class="pwb-az-listing-title"><?php echo $letter;?></p>
          <div class="pwb-az-listing-row-in">

            <?php foreach( $brand_group as $brand ): ?>

              <div class="pwb-az-listing-col">
                <a href="<?php echo get_term_link($brand['brand_term']->term_id);?>">
                  <?php echo $brand['brand_term']->name;?>
                </a>
              </div>

            <?php endforeach; ?>

          </div>
        </div>

      <?php endforeach; ?>

    </div>

  </div>

<?php endif; ?>
