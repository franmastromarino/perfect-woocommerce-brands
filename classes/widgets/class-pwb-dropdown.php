<?php
namespace Perfect_Woocommerce_Brands\Widgets;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class PWB_Dropdown_Widget extends \WP_Widget {

  function __construct(){
    $params = array(
      'description' => __( 'Adds a brands dropdown to your site', 'perfect-woocommerce-brands' ),
      'name'        => 'PWB: '.__( 'Brands dropdown', 'perfect-woocommerce-brands' )
    );
    parent::__construct('PWB_Dropdown_Widget', '', $params);
  }

  public function form($instance){
    extract($instance);

    $hide_empty = ( isset( $hide_empty ) && $hide_empty == 'on' ) ? true : false;
    ?>

    <p>
      <label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>">
        <?php echo __( 'Title', 'perfect-woocommerce-brands' );?>
      </label>
      <input
      class="widefat"
      type="text"
      id="<?php echo esc_attr( $this->get_field_id('title') ); ?>"
      name="<?php echo esc_attr( $this->get_field_name('title') ); ?>"
      value="<?php if(isset($title)) echo esc_attr($title); ?>">
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

    <?php
  }

  public function widget($args, $instance){
    extract($args);
    extract($instance);

    echo $before_widget;

      if(!empty($title)) echo $before_title . $title . $after_title;

      $hide_empty = ( isset( $hide_empty ) && $hide_empty == 'on' ) ? true : false;
      $brands_data = $this->get_brands( $hide_empty );

      if( !empty( $brands_data ) ){
        echo \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::render_template(
          'dropdown',
          'widgets',
          array( 'brands' => $brands_data )
        );
      }else{
        echo __( 'There is not available brands', 'perfect-woocommerce-brands' );
      }

    echo $after_widget;

  }

  private function get_brands( $hide_empty ){
    $brands_data = [];
    $brands = get_terms( 'pwb-brand', array( 'hide_empty' => $hide_empty ) );

    $queried_obj = get_queried_object();
    $queried_brand_id = ( isset( $queried_obj->term_id ) ) ? $queried_obj->term_id : false;

    if( is_array($brands) && count($brands)>0 ){

      foreach( $brands as $brand ){
        $brands_data[] = [
          'name'     => $brand->name,
          'link'     => get_term_link( $brand ),
          'selected' => ( $brand->term_id == $queried_brand_id ) ? 'selected="selected"' : ''
        ];
      }

    }

    return $brands_data;

  }

}
