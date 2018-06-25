<?php
namespace Perfect_Woocommerce_Brands\Widgets;
use WP_Query;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class PWB_Filter_By_Brand_Widget extends \WP_Widget {

	function __construct() {
    $params = array(
      'description' => __( 'Recommended for product categories or shop page', 'perfect-woocommerce-brands' ),
      'name'        => __( 'Filter products by brand', 'perfect-woocommerce-brands' )
    );
    parent::__construct('PWB_Filter_By_Brand_Widget', '', $params);
	}

  public function form( $instance ) {
    extract($instance);

    $title = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : __('Brands', 'perfect-woocommerce-brands');
    $limit = ( isset( $instance[ 'limit' ] ) ) ? $instance[ 'limit' ] : 20;
    $hide_empty = ( isset( $hide_empty ) && $hide_empty == 'on' ) ? true : false;
    $hide_submit_btn = ( isset( $hide_submit_btn ) && $hide_submit_btn == 'on' ) ? true : false;
    ?>
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id( 'limit' ); ?>">
        <?php echo __( 'Max number of brands', 'perfect-woocommerce-brands' );?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo esc_attr( $limit ); ?>" />
    </p>
    <p>
      <input
      type="checkbox"
      id="<?php echo esc_attr( $this->get_field_id('hide_empty') ); ?>"
      name="<?php echo esc_attr( $this->get_field_name('hide_empty') ); ?>"
      <?php checked( $hide_empty ); ?>>
      <label for="<?php echo esc_attr( $this->get_field_id('hide_empty') ); ?>">
        <?php echo __( 'Hide empty', 'perfect-woocommerce-brands' );?>
      </label>
    </p>
    <p>
      <input
      type="checkbox"
      id="<?php echo esc_attr( $this->get_field_id('hide_submit_btn') ); ?>"
      name="<?php echo esc_attr( $this->get_field_name('hide_submit_btn') ); ?>"
      <?php checked( $hide_submit_btn ); ?>>
      <label for="<?php echo esc_attr( $this->get_field_id('hide_submit_btn') ); ?>">
        <?php echo __( 'Hide filter button', 'perfect-woocommerce-brands' );?>
      </label>
    </p>
    <?php
  }

  public function update( $new_instance, $old_instance ) {
    $limit = trim( strip_tags( $new_instance['limit'] ) );
    $limit = filter_var( $limit, FILTER_VALIDATE_INT, [ 'options' => [ 'min_range' => 1 ] ] );

    $instance = array();
		$instance['title']      		 = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['limit']      		 = ( $limit != false ) ? $limit : $old_instance['limit'];
    $instance['hide_empty'] 		 = $new_instance['hide_empty'];
    $instance['hide_submit_btn'] = $new_instance['hide_submit_btn'];
    return $instance;
  }

	public function widget( $args, $instance ) {
    extract( $args );
    extract( $instance );

    if( !is_tax('pwb-brand') && !is_product()  ){

      $hide_submit_btn = ( isset( $hide_submit_btn ) && $hide_submit_btn == 'on' ) ? true : false;
      $hide_empty = ( isset( $hide_empty ) && $hide_empty == 'on' ) ? true : false;
      $brands = get_terms( 'pwb-brand', array( 'hide_empty' => $hide_empty ) );
  		$brands_ids = array();
  		foreach( $brands as $brand ) $brands_ids[] = $brand->term_id;

      $show_widget = true;
      $current_products_query = false;
      if( is_product_category() ){
        $current_products_query = $this->current_products_query( $brands_ids );
        if( !$current_products_query->have_posts() ) $show_widget = false;
      }

      if( $show_widget ){

        $title = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : __('Brands', 'perfect-woocommerce-brands');
        $title = apply_filters( 'widget_title', $title );
        $limit = ( isset( $instance[ 'limit' ] ) ) ? $instance[ 'limit' ] : 20;

        echo $args['before_widget'];
            if ( ! empty( $title ) ) echo $args['before_title'] . $title . $args['after_title'];
            $this->render_widget( $current_products_query, $brands_ids, $limit, $hide_submit_btn );
        echo $args['after_widget'];
      }

    }

	}

  public function render_widget( $the_query, $brands_ids, $limit, $hide_submit_btn ){

		if( is_product_category() ){

				if( $the_query->have_posts() ) {
					while ( $the_query->have_posts() ) {
						$the_query->the_post();

						$product_brands = wp_get_post_terms(get_the_ID(), 'pwb-brand');
						foreach ($product_brands as $brand) $result_brands[] = $brand->term_id;
					}
				}
				wp_reset_postdata();

				$cate = get_queried_object();
				$cateID = $cate->term_id;
				$cate_url = get_term_link($cateID);

				if( $limit > 0 ) $result_brands = array_slice( $result_brands, 0, $limit );

			}else{
				//no product category
				$cate_url = get_permalink( wc_get_page_id( 'shop' ));
				$result_brands = $brands_ids;
				if( $limit > 0 ) $result_brands = array_slice( $brands_ids, 0, $limit );
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
          array( 'cate_url' => $cate_url, 'brands' => $result_brands_ordered, 'hide_submit_btn' => $hide_submit_btn )
        );

      }

  }

  private function current_products_query( $brands_ids ){

    $cat = get_queried_object();
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

    return new WP_Query($args);

  }

}
