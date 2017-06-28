<?php
    namespace Perfect_Woocommerce_Brands\Widgets;

    defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

    class PWB_List_Widget extends \WP_Widget {

        function __construct(){
            $params = array(
                'description' => __( 'Adds a brands list to your site', 'perfect-woocommerce-brands' ),
                'name'        => 'PWB: '.__( 'Brands list', 'perfect-woocommerce-brands' )
            );
            parent::__construct('PWB_List_Widget', '', $params);
        }

        public function form($instance){
            extract($instance);

            if( !isset( $display_as ) ) $display_as = 'brand_logo';
            if( !isset( $columns ) ) $columns = '2';
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
                <div class="pwb-display-as-logo<?php echo ($display_as=='brand_logo') ? ' show' : '' ;?>">
                  <p>
                      <label for="<?php echo esc_attr( $this->get_field_id('columns') ); ?>"><?php echo __( 'Columns:', 'perfect-woocommerce-brands' );?></label>
                      <select
                        class="widefat"
                        id="<?php echo esc_attr( $this->get_field_id('columns') ); ?>"
                        name="<?php echo esc_attr( $this->get_field_name('columns') ); ?>">
                          <option value="1" <?php selected( $columns, '1' ); ?>>1</option>
                          <option value="2" <?php selected( $columns, '2' ); ?>>2</option>
                          <option value="3" <?php selected( $columns, '3' ); ?>>3</option>
                          <option value="4" <?php selected( $columns, '4' ); ?>>4</option>
                      </select>
                  </p>
                </div>

            <?php

        }

        public function widget($args, $instance){
            extract( $args );
            extract( $instance );

            echo $before_widget;

                if( !empty( $title ) ){
                    echo $before_title . $title . $after_title;
                }

                if( !isset( $display_as ) ) $display_as = 'brand_logo';
                if( !isset( $columns ) ) $columns = '2';
                PWB_List_Widget::get_brands( $display_as, $columns );

            echo $after_widget;

        }

        private static function get_brands( $display_as, $columns ){

            $brands = get_terms('pwb-brand',array(
                'hide_empty' => false
            ));

            if(is_array($brands) && count($brands)>0){
                echo '<ul class="pwb-row">';
                    foreach ($brands as $brand) {
                        $brand_name = $brand->name;
                        $brand_link = get_term_link( $brand->term_id );

                        $attachment_id = get_term_meta( $brand->term_id, 'pwb_brand_image', 1 );
                        $brand_logo = wp_get_attachment_image( $attachment_id, 'full' );

                        $li_class = ( $display_as == 'brand_logo' ) ? "pwb-columns pwb-columns-".$columns : "";
                        echo '<li class="'. $li_class .'">';
                          if( $display_as == 'brand_logo' && !empty( $brand_logo ) ){
                            echo '<a href="'.$brand_link.'" title="'.__( 'Go to', 'perfect-woocommerce-brands' ).' '.$brand->name.'">'.$brand_logo.'</a>';
                          }else{
                            echo '<a href="'.$brand_link.'" title="'.__( 'Go to', 'perfect-woocommerce-brands' ).' '.$brand->name.'">'.$brand->name.'</a>';
                          }
                        echo '</li>';
                    }
                echo '</ul>';
            }else{
                echo __( 'There is not available brands', 'perfect-woocommerce-brands' );
            }

        }

    }
