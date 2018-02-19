<?php
    namespace Perfect_Woocommerce_Brands\Widgets;

    defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

  	class PWB_Filter_By_Brand_Widget extends \WP_Widget {

  		function __construct() {
        $params = array(
            'description' => __( 'Recommended for product categories or shop page', 'perfect-woocommerce-brands' ),
            'name'        => 'PWB: '.__( 'Filter products by brand', 'perfect-woocommerce-brands' )
        );
        parent::__construct('PWB_Filter_By_Brand_Widget', '', $params);
  		}

      public function form( $instance ) {
        $title = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : __('Brands', 'perfect-woocommerce-brands');
        ?>
        <p>
          <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php
      }

  		public function widget( $args, $instance ) {

        if( !is_tax('pwb-brand') && !is_product()  ){
          $title = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : __('Brands', 'perfect-woocommerce-brands');
          $title = apply_filters( 'widget_title', $title );

          echo $args['before_widget'];
              if ( ! empty( $title ) ) echo $args['before_title'] . $title . $args['after_title'];
              $this->render_widget();
          echo $args['after_widget'];
        }

  		}

      public function render_widget(){

    		$brands = get_terms('pwb-brand',array(
    			'hide_empty' => false
    		));
    		$brands_ids = array();
    		foreach ($brands as $brand) {
    			$brands_ids[] = $brand->term_id;
    		}

    		$cat = get_queried_object();

    		if( is_product_category() ){

    				$cat_id = $cat->term_taxonomy_id;
    				$cat_id_array = get_term_children( $cat_id, 'product_cat' );
    				$cat_id_array[] = $cat_id;

    				$result_brands = array();
    				$args = array(
    					'posts_per_page' => -1,
    					'post_type' => 'product',
    					'tax_query' => array(
    					'relation' => 'AND',
    						array(
    							'taxonomy' => 'product_cat',
    							'field'    => 'term_id',
    							'terms'    => $cat_id_array
    						),
    						array(
    							'taxonomy' => 'pwb-brand',
    							'field'    => 'term_id',
    							'terms'    => $brands_ids
    						)
    					)
    				);

    				$the_query = new \WP_Query($args);
    				if ( $the_query->have_posts() ) {
    					while ( $the_query->have_posts() ) {
    						$the_query->the_post();

    						$product_brands = wp_get_post_terms(get_the_ID(), 'pwb-brand');

    						foreach ($product_brands as $brand) {
    							$result_brands[] = $brand->term_id;
    						}

    					}
    				} else {
    					// no posts found
    				}
    				wp_reset_postdata();

    				$cate = get_queried_object();
    				$cateID = $cate->term_id;
    				$cate_url = get_term_link($cateID);

    			}else{
    				//no product category
    				$cate_url = get_permalink( wc_get_page_id( 'shop' ));
    				shuffle($brands_ids);
    				$result_brands = array_slice($brands_ids, 0, 20);
    			}

          global $wp;
          $current_url = home_url(add_query_arg(array(),$wp->request));

          if( !empty( $result_brands ) ){

            $result_brands         = array_unique($result_brands);
            $result_brands_ordered = array();
            foreach( $result_brands as $brand ){
              $brand = get_term($brand);
              $result_brands_ordered[$brand->name] = $brand;
            }
            ksort($result_brands_ordered);

            $result_brands_ordered = apply_filters( 'pwb_widget_brand_filter', $result_brands_ordered );

            echo \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::render_template(
              'filter-by-brand',
              'widgets',
              array( 'cate_url' => $cate_url, 'brands' => $result_brands_ordered )
            );

          }

      }

  	}
