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

            $slick_settings = [
              'slidesToShow'   => (int)$atts['products_to_show'],
              'slidesToScroll' => (int)$atts['products_to_scroll'],
              'autoplay'       => (bool)$atts['autoplay'],
              'arrows'         => (bool)$atts['arrows'],
            ];
            ?>

            <div class="pwb-product-carousel" data-slick='<?php echo json_encode($slick_settings); ?>'>
              <?php echo \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::get_products_by_brand($atts['brand'], (int)$atts['products']); ?>
            </div>

            <?php
            return ob_get_clean();
        }

    }
