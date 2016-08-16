<?php
    namespace Perfect_Woocommerce_Brands;

    defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

    class Pwb_Brand_Shortcode{

        public static function brand_shortcode( $atts ) {
            $atts = shortcode_atts( array(
                'product_id' => null,
                'image_size' => 'thumbnail'
            ), $atts, 'pwb-brand' );

            ob_start();

            $brands = wp_get_post_terms( $atts['product_id'], 'pwb-brand');

            foreach($brands as $brand){
                if(is_array($brands) && count($brands)>0){
                    $brand_link = get_term_link ( $brand->term_id, 'pwb-brand' );
                    $attachment_id = get_term_meta( $brand->term_id, 'pwb_brand_image', 1 );

                    $attachment_html = wp_get_attachment_image($attachment_id,$atts['image_size']);
                    echo '<a href="'.$brand_link.'" title="'.__( 'View brand', 'perfect-woocommerce-brands' ).'">'.$attachment_html.'</a>';
                }
            }

            return ob_get_clean();

        }

    }
