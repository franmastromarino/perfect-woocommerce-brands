<?php
    namespace Perfect_Woocommerce_Brands;

    defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

    class Pwb_Product_Carousel_Shortcode{

        public static function product_carousel_shortcode( $atts ) {
            $atts = shortcode_atts( array(
                'brand' => "all",
                'products' => "10",
                'products_to_show' => "5",
                'products_to_scroll' => "1",
                'autoplay'  => "true"
            ), $atts, 'pwb-product-carousel' );

            ob_start();

            $foreach_iterator = 0;

            $autoplay = 'true';
            if($atts['autoplay']!='true'){
                $autoplay = 'false';
            }

            ?>

            <div class="pwb-product-carousel" data-slick='{"slidesToShow": <?php echo (int)$atts['products_to_show'];?>, "slidesToScroll": <?php echo (int)$atts['products_to_scroll'];?>, "autoplay": <?php echo $autoplay;?>}'>
              <?php echo Perfect_Woocommerce_Brands::get_products_by_brand($atts['brand'], (int)$atts['products']); ?>
            </div>
            
            <?php
            return ob_get_clean();
        }

    }
