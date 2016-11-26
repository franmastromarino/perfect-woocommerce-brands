<?php
    namespace Perfect_Woocommerce_Brands\Widgets;

    defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

    class PWB_List_Widget extends \WP_Widget {

        function __construct(){
            $params = array(
                'description' => __( 'Adds a brands list to your site', 'perfect-woocommerce-brands' ),
                'name' => __( 'Brands list', 'perfect-woocommerce-brands' )
            );
            parent::__construct('PWB_List_Widget', '', $params);
        }

        public function form($instance){
            extract($instance);
            ?>

                <p>
                    <label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>"><?php echo __( 'Title', 'perfect-woocommerce-brands' );?></label>
                    <input
                        class="widefat"
                        type="text"
                        id="<?php echo esc_attr( $this->get_field_id('title') ); ?>"
                        name="<?php echo esc_attr( $this->get_field_name('title') ); ?>"
                        value="<?php if(isset($title)) echo esc_attr($title); ?>">
                </p>

            <?php
        }

        public function widget($args, $instance){
            extract($args);
            extract($instance);

            echo $before_widget;

                if(!empty($title)){
                    echo $before_title . $title . $after_title;
                }

                $this->get_brands();

            echo $after_widget;

        }

        private function get_brands(){
            $brands = get_terms('pwb-brand',array(
                'hide_empty' => false
            ));

            if(is_array($brands) && count($brands)>0){
                echo '<ul>';
                    foreach ($brands as $brand) {
                        $brand_name = $brand->name;
                        $brand_link = get_term_link($brand->term_id);
                        echo '<li>';
                        echo '<a href="'.$brand_link.'" title="'.__( 'Go to', 'perfect-woocommerce-brands' ).' '.$brand->name.'">'.$brand->name.'</a>';
                        echo '</li>';
                    }
                echo '</ul>';
            }else{
                echo __( 'There is not available brands', 'perfect-woocommerce-brands' );
            }

        }

    }
