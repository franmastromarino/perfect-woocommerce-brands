<?php
namespace Perfect_Woocommerce_Brands\Widgets;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class PWB_List_Widget extends \WP_Widget {

  function __construct(){
    $params = array(
      'description' => __( 'Adds a brands list to your site', 'perfect-woocommerce-brands' ),
      'name'        => __( 'Brands list', 'perfect-woocommerce-brands' )
    );
    parent::__construct('PWB_List_Widget', '', $params);
  }

  public function form($instance){
    extract($instance);

    $title = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : __('Brands', 'perfect-woocommerce-brands');
    if( !isset( $display_as ) ) $display_as = 'brand_logo';
    if( !isset( $columns ) ) $columns = '2';
    $hide_empty = ( isset( $hide_empty ) && $hide_empty == 'on' ) ? true : false;
    $only_featured = ( isset( $only_featured ) && $only_featured == 'on' ) ? true : false;
    $randomize = ( isset( $randomize ) && $randomize == 'on' ) ? true : false;
    ?>

    <p>
      <label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>"><?php echo __( 'Title:', 'perfect-woocommerce-brands' );?></label>
      <input
      class="widefat"
      type="text"
      id="<?php echo esc_attr( $this->get_field_id('title') ); ?>"
      name="<?php echo esc_attr( $this->get_field_name('title') ); ?>"
      value="<?php if(isset($title)) echo esc_attr($title); ?>">
    </p>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id('display_as') ); ?>"><?php echo __( 'Display as:', 'perfect-woocommerce-brands' );?></label>
      <select
        class="widefat pwb-select-display-as"
        id="<?php echo esc_attr( $this->get_field_id('display_as') ); ?>"
        name="<?php echo esc_attr( $this->get_field_name('display_as') ); ?>">
        <option value="brand_name" <?php selected( $display_as, 'brand_name' ); ?>><?php _e( 'Brand name', 'perfect-woocommerce-brands' );?></option>
        <option value="brand_logo" <?php selected( $display_as, 'brand_logo' ); ?>><?php _e( 'Brand logo', 'perfect-woocommerce-brands' );?></option>
      </select>
    </p>
    <p class="pwb-display-as-logo<?php echo ($display_as=='brand_logo') ? ' show' : '' ;?>">
      <label for="<?php echo esc_attr( $this->get_field_id('columns') ); ?>"><?php echo __( 'Columns:', 'perfect-woocommerce-brands' );?></label>
      <select
        class="widefat"
        id="<?php echo esc_attr( $this->get_field_id('columns') ); ?>"
        name="<?php echo esc_attr( $this->get_field_name('columns') ); ?>">
        <option value="1" <?php selected( $columns, '1' ); ?>>1</option>
        <option value="2" <?php selected( $columns, '2' ); ?>>2</option>
        <option value="3" <?php selected( $columns, '3' ); ?>>3</option>
        <option value="4" <?php selected( $columns, '4' ); ?>>4</option>
        <option value="5" <?php selected( $columns, '5' ); ?>>5</option>
        <option value="6" <?php selected( $columns, '6' ); ?>>6</option>
      </select>
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
    <p class="pwb-display-as-logo<?php echo ($display_as=='brand_logo') ? ' show' : '' ;?>">
      <input
      type="checkbox"
      id="<?php echo esc_attr( $this->get_field_id('randomize') ); ?>"
      name="<?php echo esc_attr( $this->get_field_name('randomize') ); ?>"
      <?php checked( $randomize ); ?>>
      <label for="<?php echo esc_attr( $this->get_field_id('randomize') ); ?>">
        <?php echo __( 'Randomize', 'perfect-woocommerce-brands' );?>
      </label>
    </p>

  <?php
  }

  public function widget( $args, $instance ){
    extract( $args );
    extract( $instance );

    $hide_empty    = ( isset( $hide_empty ) && $hide_empty == 'on' ) ? true : false;
    $only_featured = ( isset( $only_featured ) && $only_featured == 'on' ) ? true : false;
    $randomize     = ( isset( $randomize ) && $randomize == 'on' ) ? true : false;
    $brands = \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::get_brands(
      $hide_empty, 'name', 'ASC', $only_featured, true
    );
    if( isset( $randomize ) && $randomize == 'on' && $display_as == 'brand_logo' ) shuffle( $brands );

    if( is_array( $brands ) && count( $brands ) > 0 ){

      echo $before_widget;

        if( !empty( $title ) ) echo $before_title . $title . $after_title;

        if( !isset( $display_as ) ) $display_as = 'brand_logo';
        if( !isset( $columns ) ) $columns = '2';
        $li_class = ( $display_as == 'brand_logo' ) ? "pwb-columns pwb-columns-".$columns : "";

        echo \Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::render_template(
          ( $display_as == 'brand_logo' ) ? 'list-logo' : 'list',
          'widgets',
          array( 'brands' => $brands, 'li_class' => $li_class, 'title_prefix' => __( 'Go to', 'perfect-woocommerce-brands' ) ),
          false
        );

      echo $after_widget;

    }

  }

}
