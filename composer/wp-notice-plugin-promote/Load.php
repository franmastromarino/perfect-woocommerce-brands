<?php

namespace QuadLayers\WP_Notice_Plugin_Promote;

/**
 * Class Load
 *
 * @package QuadLayers\WP_Notice_Plugin_Promote
 */

 class Load {

	/**
	 * Required Plugins.
	 *
	 * @var array
	 */
	protected $plugins;
	/**
	 * Current Plugin name.
	 *
	 * @var string
	 */
	protected $current_plugin_data = array();

	public function __construct( string $current_plugin_file, array $notices = array() ) {

		if(!is_admin()) {
			return;
		}

		$this->current_plugin_data = new PluginByFile( $current_plugin_file );
		
		$this->current_plugin_name = $this->current_plugin_data->name;
		$this->notices             = $plugins;
		
		register_activation_hook( $current_plugin_file, array( $this, 'add_plugin_transient' ) );
		
		add_action( 'wp_ajax_quadlayers_notice_plugin_promote_dismiss', array( $this, 'ajax_notice_plugin_promote_dismiss' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}
	
	/**
	 * Reset notice transient dismiss to on month.
	 *
	 * @return void
	 */
	/** TODO: flexbilidad para eliminar cada noticia */
	public function ajax_notice_plugin_promote_dismiss() {
		if ( check_admin_referer( 'quadlayers_notice_plugin_promote_dismiss', 'nonce' ) && isset( $_REQUEST['notice_id'] ) ) {
			$notice_id = sanitize_key( $_REQUEST['notice_id'] );
			update_user_meta( get_current_user_id(), $notice_id, true );
			set_transient( 'xxxxxxxxxx-notice-delay', true, MONTH_IN_SECONDS );
			wp_send_json( $notice_id );
		}
		wp_die();
	}	

	/**
	 * Create transient on plugin activation to delay notice one month.
	 *
	 * @return void
	 */
	public function add_plugin_transient() {
		set_transient( 'xxxxxxxxxx-notice-delay', true, MONTH_IN_SECONDS );
	}

	function admin_notices() {

		$screen = get_current_screen();

		if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
			return;
		}

		/**TODO: implementar delays */
		foreach ( $this->notices as $notice ) {

			switch ( $notice['type'] ) {
				case 'plugin':
					$plugin = new PluginBySlug( $notice['slug'] );
					if ( $this->add_notice_install( $plugin ) ) {
						return;
					}
					break;
				case 'promote':
					if ( $this->add_notice_promote( ) ) {
						return;
					}
					break;
				case 'ranking':
					if ( $this->add_notice_ranking( ) ) {
						return;
					}
					break;
			}
		}
	}

	/**TODO: implementar vistas de view */
	private function add_notice_innstall( PluginBySlug $plugin ) {

		if ( $plugin->is_plugin_activated() ) {
			return false;
		}

		if ( $plugin->is_plugin_installed() ) {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return false;
			}
			?>
			<div class="error">
				<p>
					<a href="<?php echo esc_url( $plugin->get_plugin_activate_link() ); ?>" class='button button - secondary'><?php printf( esc_html__( 'Activate % s', 'wp-notice-plugin-promote' ), esc_html( $plugin->get_plugin_name() ) ); ?></a>
					<?php printf( esc_html__( '%1$s not working because you need to activate the %2$s plugin . ', 'wp-notice-plugin-promote' ), esc_html( $this->current_plugin_name ), esc_html( $plugin->get_plugin_name() ) ); ?>
				</p>
			</div>
			<?php
			return true;
		}

		if ( ! current_user_can( 'install_plugins' ) ) {
			return false;
		}
		?>
		<div class="error">
			<p>
				<a href="<?php echo esc_url( $plugin->get_plugin_install_link() ); ?>" class='button button - secondary'><?php printf( esc_html__( 'Install % s', 'wp-notice-plugin-promote' ), esc_html( $plugin->get_plugin_name() ) ); ?></a>
				<?php printf( esc_html__( '%1$s not working because you need to install the %2$s plugin . ', 'wp-notice-plugin-promote' ), esc_html( $this->current_plugin_name ), esc_html( $plugin->get_plugin_name() ) ); ?>
			</p>
		</div>
		<?php
		return true;
	}

	/**TODO: implementar vistas de view */
	private function add_notice_promote( PluginBySlug $plugin ) {

		
	}

}