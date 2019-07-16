<?php
namespace Perfect_Woocommerce_Brands\Widgets;
use WP_Query, WC_Query, WP_Meta_Query, WP_Tax_Query;

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
    $instance['hide_submit_btn'] = ( isset( $new_instance['hide_submit_btn'] ) ) ? $new_instance['hide_submit_btn'] : '';
    return $instance;
  }

	public function widget( $args, $instance ) {
    extract( $args );
    extract( $instance );

    if( !is_tax('pwb-brand') && !is_product()  ){

      $cat = get_queried_object();

      $hide_submit_btn = ( isset( $hide_submit_btn ) && $hide_submit_btn == 'on' ) ? true : false;

      $show_widget = true;
	  $result_brands = array();

      if( is_product_category() || is_shop() ){
        $result_brands = $this->get_products_brands();
        if( empty( $result_brands ) ) $show_widget = false;
      }

      if( $show_widget ){

        $title = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : __('Brands', 'perfect-woocommerce-brands');
        $title = apply_filters( 'widget_title', $title );
        $limit = ( isset( $instance[ 'limit' ] ) ) ? $instance[ 'limit' ] : 20;
        ob_start();
        echo $args['before_widget'];
            if ( ! empty( $title ) ) echo $args['before_title'] . $title . $args['after_title'];
            $this->render_widget( $result_brands, $limit, $hide_submit_btn );
        echo $args['after_widget'];
        return ob_get_clean();
      }

    }

	}

  public function render_widget( $result_brands, $limit, $hide_submit_btn ){

		if( is_product_category() || is_shop() ){
				if( is_shop() ){
					$cate_url = get_permalink( wc_get_page_id( 'shop' ) );
				}else{
					$cate = get_queried_object();
					$cateID = $cate->term_id;
					$cate_url = get_term_link($cateID);
				}
			}else{
				$cate_url = get_permalink( wc_get_page_id( 'shop' ) );
				$result_brands =  get_terms( 'pwb-brand', array( 'hide_empty' => true, 'fields' => 'ids' ) );
			}

		if( $limit > 0 ) $result_brands = array_slice( $result_brands, 0, $limit );

      global $wp;
      $current_url = home_url(add_query_arg(array(),$wp->request));

      if( !empty( $result_brands ) ){

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
          array( 'cate_url' => $cate_url, 'brands' => $result_brands_ordered, 'hide_submit_btn' => $hide_submit_btn ),
					false
        );

      }

  }
	private function get_products_brands(){
		global $wpdb;
		$category_query = "";

		$args       = WC()->query->get_main_query()->query_vars;
		$tax_query  = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
		$meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();
		if ( ! is_post_type_archive( 'product' ) && ! empty( $args['taxonomy'] ) && ! empty( $args['term'] ) ) {
			$tax_query[] = array(
				'taxonomy' => $args['taxonomy'],
				'terms'    => array( $args['term'] ),
				'field'    => 'slug',
			);
		}

		foreach ( $meta_query + $tax_query as $key => $query ) {
			if ( ! empty( $query['price_filter'] ) || ! empty( $query['rating_filter'] ) ) {
				unset( $meta_query[ $key ] );
			}
		}

		$meta_query = new WP_Meta_Query( $meta_query );
		$tax_query  = new WP_Tax_Query( $tax_query );
		$search     = WC_Query::get_main_search_query_sql();

		$meta_query_sql   = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql    = $tax_query->get_sql( $wpdb->posts, 'ID' );
		$search_query_sql = $search ? ' AND ' . $search : '';

		
		$brand_ids = $wpdb->get_col( "SELECT DISTINCT t.term_id
			FROM {$wpdb->prefix}terms AS t
			INNER JOIN {$wpdb->prefix}term_taxonomy AS tt
			ON t.term_id = tt.term_id
			INNER JOIN {$wpdb->prefix}term_relationships AS tr
			ON tr.term_taxonomy_id = tt.term_taxonomy_id
			WHERE tt.taxonomy IN ('pwb-brand')
			$category_query
			and tr.object_id IN (
				SELECT ID FROM {$wpdb->posts}
				" . $tax_query_sql['join'] . $meta_query_sql['join'] . "
				WHERE {$wpdb->posts}.post_type IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_post_type', array( 'product' ) ) ) ) . "')
				AND {$wpdb->posts}.post_status = 'publish'
				" . $tax_query_sql['where'] . $meta_query_sql['where'] . $search_query_sql . '
			)' );

		return ( $brand_ids ) ? $brand_ids : false;

	}

}
