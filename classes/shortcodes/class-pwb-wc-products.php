<?php

namespace Perfect_Woocommerce_Brands\Shortcodes;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


class PWB_WC_Products_Shortcode extends \WC_Shortcode_Products
{
    public static function render_shortcode( $atts ) {
        if ( empty( $atts['brand'] ) ) {
            return '';
        }
        $atts = array_merge( array(
            'limit'        => '12',
            'columns'      => '4',
            'orderby'      => 'menu_order title',
            'order'        => 'ASC',
            'category'     => '',
            'cat_operator' => 'IN',
        ), (array) $atts );
        $shortcode = new self($atts);

        return $shortcode->get_content();
    }

    protected function parse_attributes($attributes)
    {
        $brand = '';

        if (isset($attributes['brand'])) {
            $brand = $attributes['brand'];
        }

        $attributes = parent::parse_attributes($attributes);

        $attributes['brand'] = $brand;

        return $attributes;
    }

    protected function parse_query_args()
    {
        $query_args = parent::parse_query_args();

        if (!empty($this->attributes['brand'])) {
            $query_args['tax_query'][] = array(
                'taxonomy' => 'pwb-brand',
                'terms' => $this->attributes['brand'],
                'field' => 'slug',
            );
        }
        return $query_args;
    }
}