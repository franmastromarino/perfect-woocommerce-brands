<?php
    namespace Perfect_Woocommerce_Brands\Shortcodes;

    defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

    class PWB_Product_Carousel_Shortcode{

        public static function product_carousel_shortcode( $atts ) {
            $atts = shortcode_atts( array(
                'brand'               => "all",
                'products'            => "10",
                'products_to_show'    => "5",
                'products_to_scroll'  => "1",
                'autoplay'            => false,
                'arrows'              => false
            ), $atts, 'pwb-product-carousel' );

            ob_start();

            if($atts['autoplay']){
              $autoplay = 'true';
            }else{
              $autoplay = 'false';
            }
            if($atts['arrows']){
              $arrows = 'true';
            }else{
              $arrows = 'false';
            }

            ?>

            <div class="pwb-product-carousel" data-slick='{"slidesToShow": <?php echo (int)$atts['products_to_show'];?>, "slidesToScroll": <?php echo (int)$atts['products_to_scroll'];?>, "autoplay": <?php echo $autoplay;?>, "arrows": <?php echo $arrows;?>}'>
              <?php echo \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::get_products_by_brand($atts['brand'], (int)$atts['products']); ?>
            </div>

            <?php
            return ob_get_clean();
        }

    }
