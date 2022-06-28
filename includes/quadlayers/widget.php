<?php

if ( ! class_exists( 'QL_Widget' ) ) {

	class QL_Widget {

		protected static $instance;

		public function __construct() {
			if ( is_admin() ) {
				add_action( 'wp_network_dashboard_setup', array( $this, 'add_dashboard_widget' ), -10 );
				add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ), -10 );
			}
		}

		public function add_dashboard_widget() {
			wp_add_dashboard_widget(
				'quadlayers-dashboard-overview',
				__( 'QuadLayers News', 'perfect-woocommerce-brands' ),
				array( $this, 'display_dashboard_widget' )
			);
		}

		public function display_dashboard_widget() {
			$posts = $this->get_feed();

			?>
		<div>
			<div>
				<div style="margin-top: 11px;float: left;width: 70%;">
					<?php esc_html_e( 'Hi! We are Quadlayers! Welcome to QuadLayers! We’re a team of international people who have been working in the WordPress sphere for the last ten years.', 'perfect-woocommerce-brands' ); ?>
					<div style="margin-top: 11px; float: left; width: 70%;"><a href="<?php echo admin_url( 'admin.php?page=' . PWB_PREFIX . '_suggestions' ); ?>" target="_blank" class="button button-secondary"><?php esc_html_e( 'More products', 'perfect-woocommerce-brands' ); ?></a></div>
				</div>
				<img style="width: 30%;margin-top: 11px;float: right; max-width: 95px;" src="<?php echo plugins_url( '/assets/img/quadlayers.jpg', PWB_PLUGIN_FILE ); ?>" />
			</div>
			<div style="clear: both;"></div>
		</div>
		<div style="margin: 16px -12px 0; padding: 12px 12px 0;border-top: 1px solid #eee;">
			<ul>
				<?php if ( is_array( $posts ) ) { ?>
					<?php
					foreach ( $posts  as $post ) {

						$link = $post['link'];
						while ( stristr( $link, 'http' ) !== $link ) {
							$link = substr( $link, 1 );
						}

						$link  = esc_url( strip_tags( $link . '?utm_source=ql_dashboard' ) );
						$title = esc_html( trim( strip_tags( $post['title'] ) ) );

						if ( empty( $title ) ) {
							$title = __( 'Untitled', 'perfect-woocommerce-brands' );
						}

						$excerpt = esc_attr( wp_trim_words( $post['excerpt'], 15, '...' ) );
						$summary = '<p class="rssSummary">' . $excerpt . '</p>';
						$date    = $post['date'];
						if ( $date ) {
							$date = ' - <span class="rss-date">' . date_i18n( get_option( 'date_format' ), $date ) . '</span>';
						}

						printf( __( '<li><strong><a href="%1$s" target="_blank">%2$s</a></strong>%3$s%4$s</li>', 'perfect-woocommerce-brands' ), $link, $title, $date, $summary );
					}
					?>
					<?php
				} else {
					printf( __( '<li>%s</li>', 'perfect-woocommerce-brands' ), $posts );
				}
				?>
			</ul>
		</div>
		<div style="display: flex; justify-content: space-between;align-items: center;margin: 16px -12px 0;padding: 12px 12px 0; border-top: 1px solid #eee;">
			<a href="<?php printf( 'https://quadlayers.com/blog/?utm_source=%s&utm_medium=software&utm_campaign=wordpress&utm_content=dashboard', PWB_PREFIX ); ?>" target="_blank"><?php esc_html_e( 'Read more like this on our blog', 'perfect-woocommerce-brands' ); ?></a>
			<a class="button-primary" href="<?php printf( 'https://quadlayers.com/?utm_source=%s&utm_medium=software&utm_campaign=wordpress&utm_content=dashboard', PWB_PREFIX ); ?>" target="_blank"><?php esc_html_e( 'QuadLayers', 'perfect-woocommerce-brands' ); ?></a>
		</div>
			<?php
		}

		public function get_feed() {

			$posts = get_transient( 'quadlayers_news_feed' );

			if ( false === $posts ) {

				$response = wp_remote_get( 'https://quadlayers.com/wp-json/wp/v2/posts?categories=1&per_page=3' );

				if ( is_wp_error( $response ) || ! isset( $response['body'] ) ) {
					return 'An error has occurred, which probably means the feed is down. Try again later';
				}

				$posts_array = json_decode( wp_remote_retrieve_body( $response ), true );

				if ( ! is_array( $posts_array ) ) {
					return 'An error has occurred, which probably means the feed is down. Try again later';
				}

				$posts = array();

				foreach ( $posts_array as $post ) {
					$posts[] = array(
						'link'    => $post['link'],
						'title'   => $post['title']['rendered'],
						'excerpt' => $post['excerpt']['rendered'],
						'date'    => strtotime( $post['date'], time() ),
					);
				}

				set_transient( 'quadlayers_news_feed', $posts, DAY_IN_SECONDS );
			}

			return $posts;
		}

		public static function instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}
	}

	QL_Widget::instance();

}
