<?php
namespace Perfect_Woocommerce_Brands\Widgets;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class PWB_Dropdown_Widget extends \WP_Widget {

  function __construct(){
    $params = array(
      'description' => __( 'Adds a brands dropdown to your site', 'perfect-woocommerce-brands' ),
      'name'        => __( 'Brands dropdown', 'perfect-woocommerce-brands' )
    );
    parent::__construct('PWB_Dropdown_Widget', '', $params);
  }

  public function form($instance){
    extract($instance);

    $title = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : __('Brands', 'perfect-woocommerce-brands');
    $hide_empty = ( isset( $hide_empty ) && $hide_empty == 'on' ) ? true : false;
    $only_featured = ( isset( $only_featured ) && $only_featured == 'on' ) ? true : false;
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

    <p>
      <input
      type="checkbox"
      id="<?php echo esc_attr( $this->get_field_id('only_featured') ); ?>"
      name="<?php echo esc_attr( $this->get_field_name('only_featured') ); ?>"
      <?php checked( $only_featured ); ?>>
      <label for="<?php echo esc_attr( $this->get_field_id('only_featured') ); ?>">
        <?php echo __( 'Only favorite brands', 'perfect-woocommerce-brands' );?>
      </label>
    </p>

    <?php
  }

  public function widget( $args, $instance ){
    extract($args);
    extract($instance);

    $queried_obj = get_queried_object();
    $queried_brand_id = ( isset( $queried_obj->term_id ) ) ? $queried_obj->term_id : false;

    $hide_empty = ( isset( $hide_empty ) && $hide_empty == 'on' ) ? true : false;
    $only_featured = ( isset( $only_featured ) && $only_featured == 'on' ) ? true : false;
    $brands = \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::get_brands(
      $hide_empty, 'name', 'ASC', $only_featured, true
    );

    if( is_array( $brands ) && count( $brands ) > 0 ){

      echo $before_widget;

        if( !empty( $title ) ) echo $before_title . $title . $after_title;

        echo \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::render_template(
          'dropdown',
          'widgets',
          array( 'brands' => $brands, 'selected' => $queried_brand_id ),
          false
        );

      echo $after_widget;

    }

  }

}
