<?php
    namespace Perfect_Woocommerce_Brands\Shortcodes;

    defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

    class PWB_Carousel_Shortcode{

        public static function carousel_shortcode( $atts ) {
            $atts = shortcode_atts( array(
                'items'             => "10",
                'items_to_show'     => "5",
                'items_to_scroll'   => "1",
                'image_size'        => "thumbnail",
                'autoplay'          => false,
                'arrows'            => false
            ), $atts, 'pwb-carousel' );

            ob_start();

            $foreach_iterator = 0;

            $slick_settings = array(
              'slidesToShow'   => (int)$atts['items_to_show'],
              'slidesToScroll' => (int)$atts['items_to_scroll'],
              'autoplay'       => (bool)$atts['autoplay'],
              'arrows'         => (bool)$atts['arrows']
            );
            ?>

            <div class="pwb-carousel" data-slick='<?php echo json_encode($slick_settings); ?>'>
            <?php
            foreach(\Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::get_brands() as $brand){
                if($foreach_iterator>=(int)$atts['items']){
                    break;
                }

                $brand_id = $brand->term_id;
                $brand_link = get_term_link($brand_id);

                $attachment_id = get_term_meta( $brand_id, 'pwb_brand_image', 1 );
                $attachment_html = $brand->name;
                if($attachment_id!=''){
                    $attachment_html = wp_get_attachment_image($attachment_id,$atts['image_size']);
                }

                echo '<div>';
                    echo '<a href="'.$brand_link.'" title="'.__( 'View brand', 'perfect-woocommerce-brands' ).'">'.$attachment_html.'</a>';
                echo '</div>';

                $foreach_iterator++;
            }
            echo '</div>';

            return ob_get_clean();
        }

    }
