<?php

namespace Perfect_Woocommerce_Brands\Admin;

use WC_Admin_Settings,
    WC_Settings_Page;

defined('ABSPATH') or die('No script kiddies please!');

function pwb_admin_tab() {

  class Pwb_Admin_Tab extends WC_Settings_Page {

    public function __construct() {

      $this->id = 'pwb_admin_tab';
      $this->label = __('Brands', 'perfect-woocommerce-brands');

      add_filter('woocommerce_settings_tabs_array', [$this, 'add_settings_page'], 20);
      add_action('woocommerce_settings_' . $this->id, [$this, 'output']);
      add_action('woocommerce_sections_' . $this->id, [$this, 'output_sections']);
      add_action('woocommerce_settings_save_' . $this->id, [$this, 'save']);
    }

    public function get_sections() {

      $sections = array(
          '' => __('General', 'perfect-woocommerce-brands'),
          'brand-pages' => __('Archives', 'perfect-woocommerce-brands'),
          'single-product' => __('Products', 'perfect-woocommerce-brands'),
          'tools' => __('Tools', 'perfect-woocommerce-brands'),
      );

      return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
    }

    public function output_sections() {
      global $current_section;

      $sections = $this->get_sections();

      if (empty($sections) || 1 === sizeof($sections)) {
        return;
      }

      echo '<ul class="subsubsub">';

      $array_keys = array_keys($sections);

      foreach ($sections as $id => $label) {
        echo '<li><a href="' . admin_url('admin.php?page=wc-settings&tab=' . $this->id . '&section=' . sanitize_title($id)) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . $label . '</a> ' . ( end($array_keys) == $id ? '' : '|' ) . ' </li>';
      }

      echo ' | <li><a target="_blank" href="' . admin_url('edit-tags.php?taxonomy=pwb-brand&post_type=product') . '">' . __('Brands', 'perfect-woocommerce-brands') . '</a></li>';
      echo ' | <li><a target="_blank" href="' . PWB_DOCUMENTATION_URL . '">' . __('Documentation', 'perfect-woocommerce-brands') . '</a></li>';

      echo '</ul><br class="clear" />';
    }

    public function get_settings($current_section = '') {

      $available_image_sizes_adapted = array();
      $available_image_sizes = get_intermediate_image_sizes();
      foreach ($available_image_sizes as $image_size)
        $available_image_sizes_adapted[$image_size] = $image_size;
      $available_image_sizes_adapted['full'] = 'full';

      $pages_select_adapted = array('-' => '-');
      $pages_select = get_pages();
      foreach ($pages_select as $page)
        $pages_select_adapted[$page->ID] = $page->post_title;

      if ('single-product' == $current_section) {

        $settings = apply_filters('wc_pwb_admin_tab_settings', array(
            'section_title' => array(
                'name' => __('Products', 'perfect-woocommerce-brands'),
                'type' => 'title',
                'desc' => '',
                'id' => 'wc_pwb_admin_tab_section_title'
            ),
            'brand_single_product_tab' => array(
                'name' => __('Products tab', 'perfect-woocommerce-brands'),
                'type' => 'checkbox',
                'default' => 'yes',
                'desc' => __('Show brand tab in single product page', 'perfect-woocommerce-brands'),
                'id' => 'wc_pwb_admin_tab_brand_single_product_tab'
            ),
            'show_brand_in_single' => array(
                'name' => __('Show brands in single product', 'perfect-woocommerce-brands'),
                'type' => 'select',
                'class' => 'pwb-admin-tab-field',
                'desc' => __('Show brand logo (or name) in single product', 'perfect-woocommerce-brands'),
                'default' => 'brand_image',
                'id' => 'wc_pwb_admin_tab_brands_in_single',
                'options' => array(
                    'no' => __('No', 'perfect-woocommerce-brands'),
                    'brand_link' => __('Show brand link', 'perfect-woocommerce-brands'),
                    'brand_image' => __('Show brand image (if is set)', 'perfect-woocommerce-brands')
                )
            ),
            'brand_single_position' => array(
                'name' => __('Brand position', 'perfect-woocommerce-brands'),
                'type' => 'select',
                'class' => 'pwb-admin-tab-field',
                'desc' => __('For single product', 'perfect-woocommerce-brands'),
                'id' => 'wc_pwb_admin_tab_brand_single_position',
                'options' => array(
                    'before_title' => __('Before title', 'perfect-woocommerce-brands'),
                    'after_title' => __('After title', 'perfect-woocommerce-brands'),
                    'after_price' => __('After price', 'perfect-woocommerce-brands'),
                    'after_excerpt' => __('After excerpt', 'perfect-woocommerce-brands'),
                    'after_add_to_cart' => __('After add to cart', 'perfect-woocommerce-brands'),
                    'meta' => __('In meta', 'perfect-woocommerce-brands'),
                    'after_meta' => __('After meta', 'perfect-woocommerce-brands'),
                    'after_sharing' => __('After sharing', 'perfect-woocommerce-brands')
                )
            ),
            'section_end' => array(
                'type' => 'sectionend',
                'id' => 'wc_pwb_admin_tab_section_end'
            )
        ));
      } elseif ('brand-pages' == $current_section) {

        $settings = apply_filters('wc_pwb_admin_tab_brand_pages_settings', array(
            'section_title' => array(
                'name' => __('Archives', 'perfect-woocommerce-brands'),
                'type' => 'title',
                'desc' => '',
                'id' => 'wc_pwb_admin_tab_section_title'
            ),
            'brand_description' => array(
                'name' => __('Show brand description', 'perfect-woocommerce-brands'),
                'type' => 'select',
                'class' => 'pwb-admin-tab-field',
                'default' => 'yes',
                'desc' => __('Show brand description (if is set) on brand archive page', 'perfect-woocommerce-brands'),
                'id' => 'wc_pwb_admin_tab_brand_desc',
                'options' => array(
                    'yes' => __('Yes, before product loop', 'perfect-woocommerce-brands'),
                    'yes_after_loop' => __('Yes, after product loop', 'perfect-woocommerce-brands'),
                    'no' => __('No, hide description', 'perfect-woocommerce-brands')
                )
            ),
            'brand_banner' => array(
                'name' => __('Show brand banner', 'perfect-woocommerce-brands'),
                'type' => 'select',
                'class' => 'pwb-admin-tab-field',
                'default' => 'yes',
                'desc' => __('Show brand banner (if is set) on brand archive page', 'perfect-woocommerce-brands'),
                'id' => 'wc_pwb_admin_tab_brand_banner',
                'options' => array(
                    'yes' => __('Yes, before product loop', 'perfect-woocommerce-brands'),
                    'yes_after_loop' => __('Yes, after product loop', 'perfect-woocommerce-brands'),
                    'no' => __('No, hide banner', 'perfect-woocommerce-brands')
                )
            ),
            'show_brand_on_loop' => array(
                'name' => __('Show brands in loop', 'perfect-woocommerce-brands'),
                'type' => 'select',
                'class' => 'pwb-admin-tab-field',
                'desc' => __('Show brand logo (or name) in product loop', 'perfect-woocommerce-brands'),
                'id' => 'wc_pwb_admin_tab_brands_in_loop',
                'options' => array(
                    'no' => __('No', 'perfect-woocommerce-brands'),
                    'brand_link' => __('Show brand link', 'perfect-woocommerce-brands'),
                    'brand_image' => __('Show brand image (if is set)', 'perfect-woocommerce-brands')
                )
            ),
            'section_end' => array(
                'type' => 'sectionend',
                'id' => 'wc_pwb_admin_tab_section_end'
            )
        ));
      } elseif ('tools' == $current_section) {

        $settings = apply_filters('wc_pwb_admin_tab_tools_settings', array(
            'section_title' => array(
                'name' => __('Tools', 'perfect-woocommerce-brands'),
                'type' => 'title',
                'desc' => '',
                'id' => 'wc_pwb_admin_tab_section_tools_title'
            ),
            'brand_import' => array(
                'name' => __('Import brands', 'perfect-woocommerce-brands'),
                'type' => 'select',
                'class' => 'pwb-admin-tab-field',
                'desc' => sprintf(
                        __('Import brands from other brand plugin. <a href="%s" target="_blank">Click here for more details</a>', 'perfect-woocommerce-brands'), str_replace('/?', '/brands/?', PWB_DOCUMENTATION_URL)
                ),
                'id' => 'wc_pwb_admin_tab_tools_migrate',
                'options' => array(
                    '-' => __('-', 'perfect-woocommerce-brands'),
                    'yith' => __('YITH WooCommerce Brands Add-On', 'perfect-woocommerce-brands'),
                    'ultimate' => __('Ultimate WooCommerce Brands', 'perfect-woocommerce-brands'),
                    'woobrands' => __('Offical WooCommerce Brands', 'perfect-woocommerce-brands')
                )
            ),
            'brand_dummy_data' => array(
                'name' => __('Dummy data', 'perfect-woocommerce-brands'),
                'type' => 'select',
                'class' => 'pwb-admin-tab-field',
                'desc' => __('Import generic brands and assign it to products randomly', 'perfect-woocommerce-brands'),
                'id' => 'wc_pwb_admin_tab_tools_dummy_data',
                'options' => array(
                    '-' => __('-', 'perfect-woocommerce-brands'),
                    'start_import' => __('Start import', 'perfect-woocommerce-brands')
                )
            ),
            'brands_system_status' => array(
                'name' => __('System status', 'perfect-woocommerce-brands'),
                'type' => 'textarea',
                'desc' => __('Show system status', 'perfect-woocommerce-brands'),
                'id' => 'wc_pwb_admin_tab_tools_system_status'
            ),
            'section_end' => array(
                'type' => 'sectionend',
                'id' => 'wc_pwb_admin_tab_section_tools_end'
            )
        ));
      } else {

        $brands_url = get_option('wc_pwb_admin_tab_slug', __('brands', 'perfect-woocommerce-brands')) . '/' . __('brand-name', 'perfect-woocommerce-brands') . '/';

        $settings = apply_filters('wc_pwb_admin_tab_product_settings', array(
            'section_title' => array(
                'name' => __('General', 'perfect-woocommerce-brands'),
                'type' => 'title',
                'desc' => '',
                'id' => 'wc_pwb_admin_tab_section_title'
            ),
            'slug' => array(
                'name' => __('Slug', 'perfect-woocommerce-brands'),
                'type' => 'text',
                'class' => 'pwb-admin-tab-field',
                'desc' => __('Brands taxonomy slug', 'perfect-woocommerce-brands'),
                'desc_tip' => sprintf(
                        __('Your brands URLs will look like "%s"', 'perfect-woocommerce-brands'), 'https://site.com/' . $brands_url
                ),
                'id' => 'wc_pwb_admin_tab_slug',
                'placeholder' => get_taxonomy('pwb-brand')->rewrite['slug']
            ),
            'brand_logo_size' => array(
                'name' => __('Brand logo size', 'perfect-woocommerce-brands'),
                'type' => 'select',
                'class' => 'pwb-admin-tab-field',
                'desc' => __('Select the size for the brand logo image around the site', 'perfect-woocommerce-brands'),
                'desc_tip' => __('The default image sizes can be configured under "Settings > Media". You can also define your own image sizes', 'perfect-woocommerce-brands'),
                'id' => 'wc_pwb_admin_tab_brand_logo_size',
                'options' => $available_image_sizes_adapted
            ),
            'brands_page_id' => array(
                'name' => __('Brands page', 'perfect-woocommerce-brands'),
                'type' => 'select',
                'class' => 'pwb-admin-tab-field pwb-admin-selectwoo',
                'desc' => __('For linking breadcrumbs', 'perfect-woocommerce-brands'),
                'desc_tip' => __('Select your "Brands" page (if you have one), it will be linked in the breadcrumbs.', 'perfect-woocommerce-brands'),
                'id' => 'wc_pwb_admin_tab_brands_page_id',
                'options' => $pages_select_adapted
            ),
            'section_end' => array(
                'type' => 'sectionend',
                'id' => 'wc_pwb_admin_tab_section_end'
            )
        ));
      }

      return apply_filters('woocommerce_get_settings_' . $this->id, $settings, $current_section);
    }

    public function output() {

      global $current_section;

      $settings = $this->get_settings($current_section);
      WC_Admin_Settings::output_fields($settings);
    }

    public function save() {

      update_option('old_wc_pwb_admin_tab_slug', get_taxonomy('pwb-brand')->rewrite['slug']);
      if (isset($_POST['wc_pwb_admin_tab_slug'])) {
        $_POST['wc_pwb_admin_tab_slug'] = sanitize_title($_POST['wc_pwb_admin_tab_slug']);
      }

      global $current_section;

      $settings = $this->get_settings($current_section);
      WC_Admin_Settings::save_fields($settings);
    }

  }

  return new Pwb_Admin_Tab();
}

add_filter('woocommerce_get_settings_pages', 'Perfect_Woocommerce_Brands\Admin\pwb_admin_tab', 15);
