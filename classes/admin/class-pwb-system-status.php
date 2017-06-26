<?php
  namespace Perfect_Woocommerce_Brands\Admin;

  defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

  class PWB_System_Status{

    function __construct(){
      add_action( 'wp_ajax_pwb_system_status', array( $this, 'pwb_system_status' ) );
    }

    public function pwb_system_status(){
      print_r(array(
        'home_url'                  => get_option( 'home' ),
        'site_url'                  => get_option( 'siteurl' ),
        'version'                   => WC()->version,
        'wp_version'                => get_bloginfo( 'version' ),
        'wp_multisite'              => is_multisite(),
        'wp_memory_limit'           => WP_MEMORY_LIMIT,
        'wp_debug_mode'             => ( defined( 'WP_DEBUG' ) && WP_DEBUG ),
        'wp_cron'                   => !( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ),
        'language'                  => get_locale(),
        'server_info'               => $_SERVER['SERVER_SOFTWARE'],
        'php_version'               => phpversion(),
        'php_post_max_size'         => ini_get( 'post_max_size' ),
        'php_max_execution_time'    => ini_get( 'max_execution_time' ),
        'php_max_input_vars'        => ini_get( 'max_input_vars' ),
        'max_upload_size'           => wp_max_upload_size(),
        'default_timezone'          => date_default_timezone_get(),
        'theme'                     => $this->theme_info(),
        'active_plugins'            => get_option( 'active_plugins' )
      ));
      wp_die();
    }

    private function theme_info(){
      $current_theme = wp_get_theme();
      return array(
        'name'          => $current_theme->__get('name'),
        'version'       => $current_theme->__get('version'),
        'parent_theme'  => $current_theme->__get('parent_theme')
      );
    }

  }
