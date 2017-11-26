<?php
  namespace Perfect_Woocommerce_Brands;

  defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

  delete_option('wc_pwb_admin_tab_section_title');
  delete_option('wc_pwb_admin_tab_slug');
  delete_option('wc_pwb_admin_tab_brand_logo_size');
  delete_option('wc_pwb_admin_tab_brand_single_position');
  delete_option('wc_pwb_admin_tab_brand_single_product_tab');
  delete_option('wc_pwb_admin_tab_brand_desc');
  delete_option('wc_pwb_admin_tab_section_end');
  delete_option('wc_pwb_notice_plugin_review');

  //update permalinks and clean cache
  flush_rewrite_rules();
  wp_cache_flush();
