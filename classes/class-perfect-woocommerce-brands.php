<?php
    namespace Perfect_Woocommerce_Brands;

    defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

    class Perfect_Woocommerce_Brands{

        function __construct(){
          add_action('plugins_loaded', array($this,'load_textdomain'));
          add_action('woocommerce_init', array($this,'register_brands_taxonomy'), 10, 0);
          add_action('init',array($this,'add_brands_metafields'));
          add_action('pwb-brand_add_form_fields', array($this,'add_brands_metafields_form') );
          add_action('pwb-brand_edit_form_fields', array($this,'add_brands_metafields_form_edit') );
          add_action('edit_pwb-brand', array($this,'add_brands_metafields_save') );
          add_action('create_pwb-brand', array($this,'add_brands_metafields_save') );
          add_filter('manage_edit-pwb-brand_columns', array($this,'brand_taxonomy_columns_head'));
          add_filter('manage_pwb-brand_custom_column', array($this,'brand_taxonomy_columns' ), 10, 3);
          add_action('admin_enqueue_scripts', array($this,'register_admin_scripts'));
          $this->brand_logo_position();
          add_action('woocommerce_before_shop_loop', array($this,'archive_page_banner'), 9);
          add_action('woocommerce_before_shop_loop', array($this,'show_brand_description'), 9);
          add_action('widgets_init', function(){register_widget('\Perfect_Woocommerce_Brands\Pwb_Dropdown_Widget');});
          add_action('widgets_init', function(){register_widget('\Perfect_Woocommerce_Brands\Pwb_List_Widget');});
          if ( !is_admin() ) {
              add_action('init', array($this,'register_frontend_scripts'));
          }
          add_shortcode( 'pwb-carousel', array('\Perfect_Woocommerce_Brands\Pwb_Carousel_Shortcode','carousel_shortcode') );
          add_shortcode( 'pwb-product-carousel', array('\Perfect_Woocommerce_Brands\Pwb_Product_Carousel_Shortcode','product_carousel_shortcode') );
          add_shortcode( 'pwb-all-brands', array('\Perfect_Woocommerce_Brands\Pwb_All_Brands_Shortcode','all_brands_shortcode') );
          add_shortcode( 'pwb-brand', array('\Perfect_Woocommerce_Brands\Pwb_Brand_Shortcode','brand_shortcode') );
          if(is_plugin_active('js_composer/js_composer.php')){
              add_action('vc_before_init', array($this,'vc_map_shortcodes') );
          }
        }

        public function load_textdomain() {
          load_plugin_textdomain( 'perfect-woocommerce-brands', false, PWB_PLUGIN_PATH . '/lang' );
        }

        public function show_brand_description(){
          $show_desc = get_option('wc_pwb_admin_tab_brand_desc');
        	$queried_object = get_queried_object();
        	if(is_a($queried_object,'WP_Term') && $queried_object->taxonomy == 'pwb-brand' && $queried_object->description != '' && $show_desc !== 'no'){
        		echo '<div class="pwb-brand-description">';
        		echo $queried_object->description;
        		echo '</div>';
        	}
        }

        /**
  			 * woocommerce_single_product_summary hook.
  			 *
  			 * @hooked woocommerce_template_single_title - 5
  			 * @hooked woocommerce_template_single_rating - 10
  			 * @hooked woocommerce_template_single_price - 10
  			 * @hooked woocommerce_template_single_excerpt - 20
  			 * @hooked woocommerce_template_single_add_to_cart - 30
  			 * @hooked woocommerce_template_single_meta - 40
  			 * @hooked woocommerce_template_single_sharing - 50
  			 */
        private function brand_logo_position(){
          $position = 41;
          $position_selected = get_option('wc_pwb_admin_tab_brand_single_position');
          if(!$position_selected){
            update_option('wc_pwb_admin_tab_brand_single_position','after_meta');
          }
          switch ($position_selected) {
            case 'before_title':
              $position = 4;
              break;
            case 'after_title':
              $position = 6;
              break;
            case 'after_price':
              $position = 11;
              break;
            case 'after_excerpt':
              $position = 21;
              break;
            case 'after_add_to_cart':
              $position = 31;
              break;
            case 'after_meta':
              $position = 41;
              break;
            case 'after_sharing':
              $position = 51;
              break;
          }
          add_action('woocommerce_single_product_summary', array($this,'action_woocommerce_single_product_summary'), $position);
        }


        /*
         * Maps shortcode (for visual composer plugin)
         *
         * @since 1.0
         * @link https://vc.wpbakery.com/
         * @return mixed
         */
        public function vc_map_shortcodes(){
            $available_image_sizes_adapted = array();
            $available_image_sizes = get_intermediate_image_sizes();

            foreach($available_image_sizes as $image_size){
                $available_image_sizes_adapted[$image_size] = $image_size;
            }

            vc_map(array(
                "name" => __( "PWB Product carousel", "perfect-woocommerce-brands" ),
                "description" => __( "Product carousel by brand or by category", "perfect-woocommerce-brands" ),
                "base" => "pwb-product-carousel",
                "class" => "",
                "icon" => PWB_PLUGIN.'/assets/img/icon_pwb.jpg',
                "category" =>  "WooCommerce",
                "params" => array(
                    array(
                        "type"        => "dropdown",
                        "heading" => __( "Brand", "perfect-woocommerce-brands" ),
                        "param_name"  => "brand",
                        "admin_label" => true,
                        "value"       => $this->get_brands_array()
                    ),
                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "heading" => __( "Products", "perfect-woocommerce-brands" ),
                        "param_name" => "products",
                        "value" => "10",
                        "description" => __( "Number of products to load", "perfect-woocommerce-brands" )
                    ),
                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "heading" => __( "Products to show", "perfect-woocommerce-brands" ),
                        "param_name" => "products_to_show",
                        "value" => "5",
                        "description" => __( "Number of products to show", "perfect-woocommerce-brands" )
                    ),
                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "heading" => __( "Products to scroll", "perfect-woocommerce-brands" ),
                        "param_name" => "products_to_scroll",
                        "value" => "1",
                        "description" => __( "Number of products to scroll", "perfect-woocommerce-brands" )
                    ),
                    array(
                        "type" => "checkbox",
                        "holder" => "div",
                        "heading" => __( "Autoplay", "perfect-woocommerce-brands" ),
                        "param_name" => "autoplay",
                        "description" => __( "Autoplay carousel", "perfect-woocommerce-brands" )
                    )
                )


            ));

            vc_map(array(
                "name" => __( "PWB Brands carousel", "perfect-woocommerce-brands" ),
                "description" => __( "Brands carousel", "perfect-woocommerce-brands" ),
                "base" => "pwb-carousel",
                "class" => "",
                "icon" => PWB_PLUGIN.'/assets/img/icon_pwb.jpg',
                "category" =>  "WooCommerce",
                "params" => array(
                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "heading" => __( "Items", "perfect-woocommerce-brands" ),
                        "param_name" => "items",
                        "value" => "10",
                        "description" => __( "Number of items to load", "perfect-woocommerce-brands" )
                    ),
                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "heading" => __( "Items to show", "perfect-woocommerce-brands" ),
                        "param_name" => "items_to_show",
                        "value" => "5",
                        "description" => __( "Number of items to show", "perfect-woocommerce-brands" )
                    ),
                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "heading" => __( "Items to scroll", "perfect-woocommerce-brands" ),
                        "param_name" => "items_to_scroll",
                        "value" => "1",
                        "description" => __( "Number of items to scroll", "perfect-woocommerce-brands" )
                    ),
                    array(
                        "type" => "checkbox",
                        "holder" => "div",
                        "heading" => __( "Autoplay", "perfect-woocommerce-brands" ),
                        "param_name" => "autoplay",
                        "description" => __( "Autoplay carousel", "perfect-woocommerce-brands" )
                    ),
                    array(
                        "type"        => "dropdown",
                        "heading" => __( "Brand logo size", "perfect-woocommerce-brands" ),
                        "param_name"  => "image_size",
                        "admin_label" => true,
                        "value"       => $available_image_sizes_adapted
                    )
                )


            ));

            vc_map(array(
                "name" => __( "PWB All brands", "perfect-woocommerce-brands" ),
                "description" => __( "Show all brands", "perfect-woocommerce-brands" ),
                "base" => "pwb-all-brands",
                "class" => "",
                "icon" => PWB_PLUGIN.'/assets/img/icon_pwb.jpg',
                "category" =>  "WooCommerce",

                "params" => array(
                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "heading" => __( "Brands per page", "perfect-woocommerce-brands" ),
                        "param_name" => "per_page",
                        "value" => "10",
                        "description" => __( "Show x brands per page", "perfect-woocommerce-brands" )
                    ),
                    array(
                        "type"        => "dropdown",
                        "heading" => __( "Brand logo size", "perfect-woocommerce-brands" ),
                        "param_name"  => "image_size",
                        "admin_label" => true,
                        "value"       => $available_image_sizes_adapted
                    )
                )


            ));

            vc_map(array(
                "name" => __( "PWB brand", "perfect-woocommerce-brands" ),
                "description" => __( "Show brand for a specific product", "perfect-woocommerce-brands" ),
                "base" => "pwb-brand",
                "class" => "",
                "icon" => PWB_PLUGIN.'/assets/img/icon_pwb.jpg',
                "category" =>  "WooCommerce",

                "params" => array(
                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "heading" => __( "Product id", "perfect-woocommerce-brands" ),
                        "param_name" => "product_id",
                        "value" => null,
                        "description" => __( "Product id (post id)", "perfect-woocommerce-brands" )
                    ),
                    array(
                        "type"        => "dropdown",
                        "heading" => __( "Brand logo size", "perfect-woocommerce-brands" ),
                        "param_name"  => "image_size",
                        "admin_label" => true,
                        "value"       => $available_image_sizes_adapted
                    )

                )


            ));
        }

        public function action_woocommerce_single_product_summary() {
            $brands = wp_get_post_terms( get_the_ID(), 'pwb-brand');

            if(!is_wp_error($brands)){
                if(sizeof($brands>0)){
                  echo '<div class="pwb-single-product-brands pwb-clearfix">';
                  foreach($brands as $brand){
                      $brand_link = get_term_link ( $brand->term_id, 'pwb-brand' );
                      $attachment_id = get_term_meta( $brand->term_id, 'pwb_brand_image', 1 );

                      $image_size = 'thumbnail';
                      $image_size_selected = get_option('wc_pwb_admin_tab_brand_logo_size');
                      if($image_size_selected!=false){
                          $image_size = $image_size_selected;
                      }

                      $attachment_html = wp_get_attachment_image($attachment_id,$image_size);
                      if(!empty($attachment_html)){
                        echo '<a href="'.$brand_link.'" title="'.__( 'View brand', 'perfect-woocommerce-brands' ).'">'.$attachment_html.'</a>';
                      }else{
                        echo '<a href="'.$brand_link.'" title="'.__( 'View brand', 'perfect-woocommerce-brands' ).'">'.$brand->name.'</a>';
                      }
                  }
                  echo '</div>';
                }
            }
        }

        public function register_frontend_scripts(){

            wp_register_script(
                'pwb_slick_lib',
                PWB_PLUGIN . '/assets/js/slick/slick.min.js',
                array('jquery'),
                '1.5.9',
                true
            );
            wp_enqueue_script('pwb_frontend_functions');

            wp_enqueue_style (
                'pwb_slick_lib',
                PWB_PLUGIN . '/assets/js/slick/slick.css',
                array(),
                '1.5.9',
                'all'
            );

            wp_enqueue_style (
                'pwb_frontend_styles',
                PWB_PLUGIN . '/assets/css/styles-frontend.css',
                array('pwb_slick_lib'),
                PWB_PLUGIN_VERSION,
                'all'
            );

            wp_register_script(
                'pwb_frontend_functions',
                PWB_PLUGIN . '/assets/js/pwb_frontend_functions.js',
                array('jquery','pwb_slick_lib'),
                PWB_PLUGIN_VERSION,
                true
            );
            wp_enqueue_script('pwb_frontend_functions');

        }

        public function register_admin_scripts($hook){
            $screen = get_current_screen();

            wp_register_style('pwb_styles_brands', plugins_url('perfect-woocommerce-brands/assets/css/styles-admin.css'), array(), PWB_PLUGIN_VERSION);
            wp_register_script('pwb_brands_js', plugins_url('perfect-woocommerce-brands/assets/js/pwb_admin_functions.js'), array('jquery'), PWB_PLUGIN_VERSION, true);

            if ($hook == 'edit-tags.php' && $screen->taxonomy == 'pwb-brand' || $hook == 'term.php' && $screen->taxonomy == 'pwb-brand') {
                wp_enqueue_style('pwb_styles_brands');
                wp_enqueue_media();
                wp_enqueue_script('pwb_brands_js');
            }else{
                return;
            }

        }

        public function register_brands_taxonomy(){
            $labels = array(
                'name'                       => __( 'Brands', 'perfect-woocommerce-brands' ),
                'singular_name'              => __( 'Brand', 'perfect-woocommerce-brands' ),
                'menu_name'                  => __( 'Brands', 'perfect-woocommerce-brands' ),
                'all_items'                  => __( 'All Brands', 'perfect-woocommerce-brands' ),
                'edit_item'                  => __( 'Edit Brand', 'perfect-woocommerce-brands' ),
                'view_item'                  => __( 'View Brand', 'perfect-woocommerce-brands' ),
                'update_item'                => __( 'Update Brand', 'perfect-woocommerce-brands' ),
                'add_new_item'               => __( 'Add New Brand', 'perfect-woocommerce-brands' ),
                'new_item_name'              => __( 'New Brand Name', 'perfect-woocommerce-brands' ),
                'parent_item'                => __( 'Parent Brand', 'perfect-woocommerce-brands' ),
                'parent_item_colon'          => __( 'Parent Brand:', 'perfect-woocommerce-brands' ),
                'search_items'               => __( 'Search Brands', 'perfect-woocommerce-brands' ),
                'popular_items'              => __( 'Popular Brands', 'perfect-woocommerce-brands' ),
                'separate_items_with_commas' => __( 'Separate brands with commas', 'perfect-woocommerce-brands' ),
                'add_or_remove_items'        => __( 'Add or remove brands', 'perfect-woocommerce-brands' ),
                'choose_from_most_used'      => __( 'Choose from the most used brands', 'perfect-woocommerce-brands' ),
                'not_found'                  => __( 'No brands found', 'perfect-woocommerce-brands' )
            );

            $new_slug = get_option('wc_pwb_admin_tab_slug');
            $old_slug = get_option('old_wc_pwb_admin_tab_slug');

            $new_slug = ($new_slug!=false) ? $new_slug : 'brand';
            $old_slug = ($old_slug!=false) ? $old_slug : 'null';

            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'query_var'         => true,
                'public'            => true,
                'show_admin_column' => true,
                'rewrite'           => array(
                    'slug'      => $new_slug,
                    'hierarchical'  => true,
                    'ep_mask'   => EP_PERMALINK
                )
            );

            register_taxonomy( 'pwb-brand', array( 'product' ), $args );

            if($new_slug != false && $old_slug!= false && $new_slug != $old_slug){
                flush_rewrite_rules();
                update_option( 'old_wc_pwb_admin_tab_slug', $new_slug );
            }

        }

        public function add_brands_metafields(){
            register_meta( 'term', 'pwb_brand_image', array($this,'add_brands_metafields_sanitize') );
        }

        public function add_brands_metafields_sanitize($brand_img){
            return $brand_img;
        }

        public function add_brands_metafields_form(){
          ob_start();
          ?>
          <div class="form-field pwb_brand_cont">
              <label for="pwb_brand_image"><?php _e( 'Brand logo', 'perfect-woocommerce-brands' ); ?></label>
              <input type="text" name="pwb_brand_image" id="pwb_brand_image" value="" >
              <a href="#" id="pwb_brand_image_select" class="button"><?php _e('Select image','perfect-woocommerce-brands');?></a>
              <?php echo wp_nonce_field( basename( __FILE__ ), 'pwb_brand_image_nonce' );?>
          </div>

          <div class="form-field pwb_brand_cont">
              <label for="pwb_brand_banner"><?php _e( 'Brand banner', 'perfect-woocommerce-brands' ); ?></label>
              <input type="text" name="pwb_brand_banner" id="pwb_brand_banner" value="" >
              <a href="#" id="pwb_brand_banner_select" class="button"><?php _e('Select image','perfect-woocommerce-brands');?></a>
              <p><?php _e( 'This image will be shown on brand page', 'perfect-woocommerce-brands' ); ?></p>
              <?php echo wp_nonce_field( basename( __FILE__ ), 'pwb_brand_banner_nonce' );?>
          </div>

          <div class="form-field pwb_brand_cont">
              <label for="pwb_brand_banner_link"><?php _e( 'Brand banner link', 'perfect-woocommerce-brands' ); ?></label>
              <input type="text" name="pwb_brand_banner_link" id="pwb_brand_banner_link" value="" >
              <p><?php _e( 'This link should be relative to site url. Example: product/product-name', 'perfect-woocommerce-brands' ); ?></p>
              <?php echo wp_nonce_field( basename( __FILE__ ), 'pwb_brand_banner_link_nonce' );?>
          </div>
          <?php
          echo ob_get_clean();
        }

        public function add_brands_metafields_form_edit($term){
          $term_value_image = get_term_meta( $term->term_id, 'pwb_brand_image', true );
          $term_value_banner = get_term_meta( $term->term_id, 'pwb_brand_banner', true );
          $term_value_banner_link = get_term_meta( $term->term_id, 'pwb_brand_banner_link', true );
          ob_start();
          ?>
          <table class="form-table pwb_brand_cont">
            <tr class="form-field">
              <th>
                <label for="pwb_brand_image"><?php _e( 'Brand logo', 'perfect-woocommerce-brands' ); ?></label>
              </th>
              <td>
                <input type="text" name="pwb_brand_image" id="pwb_brand_image" value="<?php echo $term_value_image;?>" >
                <a href="#" id="pwb_brand_image_select" class="button"><?php _e('Select image','perfect-woocommerce-brands');?></a>
                <div id="pwb_brand_image_result"><?php echo wp_get_attachment_image ( $term_value_image, array('90','90'), false );?></div>
                <?php echo wp_nonce_field( basename( __FILE__ ), 'pwb_brand_image_nonce' );?>
              </td>
            </tr>
            <tr class="form-field">
              <th>
                <label for="pwb_brand_banner"><?php _e( 'Brand banner', 'perfect-woocommerce-brands' ); ?></label>
              </th>
              <td>
                <input type="text" name="pwb_brand_banner" id="pwb_brand_banner" value="<?php echo $term_value_banner;?>" >
                <a href="#" id="pwb_brand_banner_select" class="button"><?php _e('Select image','perfect-woocommerce-brands');?></a>
                <div id="pwb_brand_banner_result"><?php echo wp_get_attachment_image ( $term_value_banner, array('90','90'), false );?></div>
                <?php echo wp_nonce_field( basename( __FILE__ ), 'pwb_brand_banner_nonce' );?>
              </td>
            </tr>
            <tr class="form-field">
              <th>
                <label for="pwb_brand_banner_link"><?php _e( 'Brand banner link', 'perfect-woocommerce-brands' ); ?></label>
              </th>
              <td>
                <input type="text" name="pwb_brand_banner_link" id="pwb_brand_banner_link" value="<?php echo $term_value_banner_link;?>" >
                <p class="description"><?php _e( 'This link should be relative to site url. Example: product/product-name', 'perfect-woocommerce-brands' ); ?></p>
                <div id="pwb_brand_banner_link_result"><?php echo wp_get_attachment_image ( $term_value_banner_link, array('90','90'), false );?></div>
                <?php echo wp_nonce_field( basename( __FILE__ ), 'pwb_brand_banner_link_nonce' );?>
              </td>
            </tr>
          </table>
          <?php
          echo ob_get_clean();
        }

        public function add_brands_metafields_save($term_id){

          /* ·············· Brand image ·············· */
          if ( ! isset( $_POST['pwb_brand_image_nonce'] ) || ! wp_verify_nonce( $_POST['pwb_brand_image_nonce'], basename( __FILE__ ) ) )
              return;

          $old_img = get_term_meta( $term_id, 'pwb_brand_image', true );
          $new_img = isset( $_POST['pwb_brand_image'] ) ? $_POST['pwb_brand_image'] : '';

          if ( $old_img && '' === $new_img )
              delete_term_meta( $term_id, 'pwb_brand_image' );

          else if ( $old_img !== $new_img )
              update_term_meta( $term_id, 'pwb_brand_image', $new_img );
          /* ·············· /Brand image ·············· */

          /* ·············· Brand banner ·············· */
          if ( ! isset( $_POST['pwb_brand_banner_nonce'] ) || ! wp_verify_nonce( $_POST['pwb_brand_banner_nonce'], basename( __FILE__ ) ) )
              return;

          $old_img = get_term_meta( $term_id, 'pwb_brand_banner', true );
          $new_img = isset( $_POST['pwb_brand_banner'] ) ? $_POST['pwb_brand_banner'] : '';

          if ( $old_img && '' === $new_img )
              delete_term_meta( $term_id, 'pwb_brand_banner' );

          else if ( $old_img !== $new_img )
              update_term_meta( $term_id, 'pwb_brand_banner', $new_img );
          /* ·············· /Brand banner ·············· */

          /* ·············· Brand banner link ·············· */
          if ( ! isset( $_POST['pwb_brand_banner_link_nonce'] ) || ! wp_verify_nonce( $_POST['pwb_brand_banner_link_nonce'], basename( __FILE__ ) ) )
              return;

          $old_img = get_term_meta( $term_id, 'pwb_brand_banner_link', true );
          $new_img = isset( $_POST['pwb_brand_banner_link'] ) ? $_POST['pwb_brand_banner_link'] : '';

          if ( $old_img && '' === $new_img )
              delete_term_meta( $term_id, 'pwb_brand_banner_link' );

          else if ( $old_img !== $new_img )
              update_term_meta( $term_id, 'pwb_brand_banner_link', $new_img );
          /* ·············· /Brand banner link ·············· */
        }

        public function brand_taxonomy_columns_head($defaults) {
            $newColumns = array(
                'cb' => $defaults['cb'],
                'logo' => __( 'Logo', 'perfect-woocommerce-brands' )
            );

            unset($defaults['description']);
            unset($defaults['cb']);

            return array_merge($newColumns,$defaults);
        }

        public function brand_taxonomy_columns($c, $column_name, $term_id){
            $image = wp_get_attachment_image( get_term_meta( $term_id, 'pwb_brand_image', 1 ), array('60','60') );
            if($image){
              return $image;
            }else{
              return "-";
            }
        }

        public static function get_brands(){
            $result = array();

            $brands = get_terms('pwb-brand',array(
                'hide_empty' => false
            ));

            if(is_array($brands) && count($brands)>0){
                $result = $brands;
            }

            return $result;

        }

        public static function get_brands_array(){
            $result = array();
            $result[0] = 'all';

            $brands = get_terms('pwb-brand',array(
                'hide_empty' => false
            ));

            foreach ($brands as $brand) {
              $result[$brand->term_id] = $brand->slug;
            }

            return $result;

        }

        public static function get_products_by_brand($brand_id, $count){

          if($brand_id === 'all'){
            $args = array(
        			'post_type' => 'product',
        			'posts_per_page' => $count,
              'paged' => false
        		);
          }else{
            $args = array(
        			'post_type' => 'product',
              'tax_query' => array(
                  array(
                      'taxonomy' => 'pwb-brand',
                      'field' => 'slug',
                      'terms' => $brand_id,
                  )
              ),
        			'posts_per_page' => $count,
              'paged' => false
        		);
          }

          ob_start();

      		$loop = new \WP_Query( $args );
      		if ( $loop->have_posts() && function_exists('get_product') && function_exists('woocommerce_template_loop_add_to_cart') && function_exists('woocommerce_get_product_thumbnail') ) {
      			while ( $loop->have_posts() ) : $loop->the_post();
            $product = get_product( get_the_ID() );

              echo '<div>';
                echo '<a href="'.get_the_permalink().'">';
                  echo woocommerce_get_product_thumbnail();
                  echo '<h3>'.$product->post->post_title.'</h3>';
                  echo '<span class="pwb-amount">'.$product->get_price_html().'</span>';
                  woocommerce_template_loop_add_to_cart();
                echo '</a>';
              echo '</div>';

      			endwhile;
      		} else {
      			echo __( 'No products found', 'perfect-woocommerce-brands' );
      		}
      		wp_reset_postdata();

          return ob_get_clean();

        }

        public function archive_page_banner(){
          $queried_object = get_queried_object();

          if(is_a($queried_object,'WP_Term') && $queried_object->taxonomy == 'pwb-brand'){
            //pwb-brand archive
            $brand_banner = get_term_meta( $queried_object->term_id, 'pwb_brand_banner', true );
            $brand_banner_link = get_term_meta( $queried_object->term_id, 'pwb_brand_banner_link', true );
            if($brand_banner!=''){
              echo '<div class="pwb-brand-banner pwb-clearfix">';
              if($brand_banner_link!=''){
                echo '<a href="'.site_url($brand_banner_link).'">'.wp_get_attachment_image ( $brand_banner, 'full', false ).'</a>';
              }else{
                echo wp_get_attachment_image ( $brand_banner, 'full', false );
              }
              echo '</div>';
            }
          }

        }

    }
