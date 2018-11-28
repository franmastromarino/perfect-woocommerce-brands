<?php
namespace Perfect_Woocommerce_Brands\Admin;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class Brands_Custom_Fields {

  function __construct(){
    add_action( 'pwb-brand_add_form_fields', array( $this, 'add_brands_metafields_form' ) );
    add_action( 'pwb-brand_edit_form_fields', array( $this, 'add_brands_metafields_form_edit' ) );
    add_action( 'edit_pwb-brand', array( $this, 'add_brands_metafields_save' ) );
    add_action( 'create_pwb-brand', array( $this, 'add_brands_metafields_save' ) );
  }

  public function add_brands_metafields_form(){
    ob_start();
    ?>

    <div class="form-field pwb_brand_cont">
        <label for="pwb_brand_desc"><?php _e( 'Description' ); ?></label>
        <textarea id="pwb_brand_description_field" name="pwb_brand_description_field" rows="5" cols="40"></textarea>
        <p id="brand-description-help-text"><?php _e( 'Brand description for the archive pages. You can include some html markup and shortcodes.', 'perfect-woocommerce-brands' ); ?></p>
    </div>

    <div class="form-field pwb_brand_cont">
        <label for="pwb_brand_image"><?php _e( 'Brand logo', 'perfect-woocommerce-brands' ); ?></label>
        <input type="text" name="pwb_brand_image" id="pwb_brand_image" value="" >
        <a href="#" id="pwb_brand_image_select" class="button"><?php _e('Select image','perfect-woocommerce-brands');?></a>
    </div>

    <div class="form-field pwb_brand_cont">
        <label for="pwb_brand_banner"><?php _e( 'Brand banner', 'perfect-woocommerce-brands' ); ?></label>
        <input type="text" name="pwb_brand_banner" id="pwb_brand_banner" value="" >
        <a href="#" id="pwb_brand_banner_select" class="button"><?php _e('Select image','perfect-woocommerce-brands');?></a>
        <p><?php _e( 'This image will be shown on brand page', 'perfect-woocommerce-brands' ); ?></p>
    </div>

    <div class="form-field pwb_brand_cont">
        <label for="pwb_brand_banner_link"><?php _e( 'Brand banner link', 'perfect-woocommerce-brands' ); ?></label>
        <input type="text" name="pwb_brand_banner_link" id="pwb_brand_banner_link" value="" >
        <p><?php _e( 'This link should be relative to site url. Example: product/product-name', 'perfect-woocommerce-brands' ); ?></p>
    </div>

    <?php wp_nonce_field( basename( __FILE__ ), 'pwb_nonce' ); ?>

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
          <label for="pwb_brand_desc"><?php _e( 'Description' ); ?></label>
        </th>
        <td>
          <?php wp_editor( html_entity_decode( $term->description ), 'pwb_brand_description_field', array( 'editor_height' => 120 ) ); ?>
          <p id="brand-description-help-text"><?php _e( 'Brand description for the archive pages. You can include some html markup and shortcodes.', 'perfect-woocommerce-brands' ); ?></p>
        </td>
      </tr>
      <tr class="form-field">
        <th>
          <label for="pwb_brand_image"><?php _e( 'Brand logo', 'perfect-woocommerce-brands' ); ?></label>
        </th>
        <td>
          <input type="text" name="pwb_brand_image" id="pwb_brand_image" value="<?php echo $term_value_image;?>" >
          <a href="#" id="pwb_brand_image_select" class="button"><?php _e('Select image','perfect-woocommerce-brands');?></a>

          <?php $current_image = wp_get_attachment_image ( $term_value_image, array('90','90'), false ); ?>
          <?php if( !empty($current_image) ): ?>
            <div class="pwb_brand_image_selected">
              <span>
                <?php echo $current_image;?>
                <a href="#" class="pwb_brand_image_selected_remove">X</a>
              </span>
            </div>
          <?php endif; ?>

        </td>
      </tr>
      <tr class="form-field">
        <th>
          <label for="pwb_brand_banner"><?php _e( 'Brand banner', 'perfect-woocommerce-brands' ); ?></label>
        </th>
        <td>
          <input type="text" name="pwb_brand_banner" id="pwb_brand_banner" value="<?php echo $term_value_banner;?>" >
          <a href="#" id="pwb_brand_banner_select" class="button"><?php _e('Select image','perfect-woocommerce-brands');?></a>

          <?php $current_image = wp_get_attachment_image ( $term_value_banner, array('90','90'), false ); ?>
          <?php if( !empty($current_image) ): ?>
            <div class="pwb_brand_image_selected">
              <span>
                <?php echo $current_image;?>
                <a href="#" class="pwb_brand_image_selected_remove">X</a>
              </span>
            </div>
          <?php endif; ?>

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
        </td>
      </tr>
    </table>

    <?php wp_nonce_field( basename( __FILE__ ), 'pwb_nonce' );?>

    <?php
    echo ob_get_clean();
  }

  public function add_brands_metafields_save( $term_id ){

    if ( ! isset( $_POST['pwb_nonce'] ) || ! wp_verify_nonce( $_POST['pwb_nonce'], basename( __FILE__ ) ) )
        return;

    /* ·············· Brand image ·············· */
    $old_img = get_term_meta( $term_id, 'pwb_brand_image', true );
    $new_img = isset( $_POST['pwb_brand_image'] ) ? $_POST['pwb_brand_image'] : '';

    if ( $old_img && '' === $new_img )
        delete_term_meta( $term_id, 'pwb_brand_image' );

    else if ( $old_img !== $new_img )
        update_term_meta( $term_id, 'pwb_brand_image', $new_img );
    /* ·············· /Brand image ·············· */

    /* ·············· Brand banner ·············· */
    $old_img = get_term_meta( $term_id, 'pwb_brand_banner', true );
    $new_img = isset( $_POST['pwb_brand_banner'] ) ? $_POST['pwb_brand_banner'] : '';

    if ( $old_img && '' === $new_img )
        delete_term_meta( $term_id, 'pwb_brand_banner' );

    else if ( $old_img !== $new_img )
        update_term_meta( $term_id, 'pwb_brand_banner', $new_img );
    /* ·············· /Brand banner ·············· */

    /* ·············· Brand banner link ·············· */
    $old_img = get_term_meta( $term_id, 'pwb_brand_banner_link', true );
    $new_img = isset( $_POST['pwb_brand_banner_link'] ) ? $_POST['pwb_brand_banner_link'] : '';

    if ( $old_img && '' === $new_img )
        delete_term_meta( $term_id, 'pwb_brand_banner_link' );

    else if ( $old_img !== $new_img )
        update_term_meta( $term_id, 'pwb_brand_banner_link', $new_img );
    /* ·············· /Brand banner link ·············· */

    /* ·············· Brand desc ·············· */
    if( isset( $_POST['pwb_brand_description_field'] ) ){
      $allowed_tags = apply_filters(
        'pwb_description_allowed_tags',
        '<p><span><a><ul><ol><li><h1><h2><h3><h4><h5><h6><pre><strong><em><blockquote><del><ins><img><code><hr>'
      );
      $desc = strip_tags( wp_unslash( $_POST['pwb_brand_description_field'] ), $allowed_tags );
      global $wpdb;
      $wpdb->update( $wpdb->term_taxonomy, [ 'description' => $desc ], [ 'term_id' => $term_id ]  );
    }
    /* ·············· /Brand desc ·············· */

  }

}
