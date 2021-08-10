<?php

class QL_Widget
{
    protected static $instance;

    public function __construct()
    {
        if (is_admin()) {
            add_action('wp_network_dashboard_setup', [$this, 'add_dashboard_widget'], -10);
            add_action('wp_dashboard_setup', [$this, 'add_dashboard_widget'], -10);
        }
    }

    public function add_dashboard_widget()
    {
        wp_add_dashboard_widget(
            'quadlayers-dashboard-overview',
            __('QuadLayers News', 'perfect-woocommerce-brands'),
            [$this, 'display_dashboard_widget']
        );
    }

    public function display_dashboard_widget()
    {
        $rss = fetch_feed('https://quadlayers.com/news/feed/');

        if (is_wp_error($rss)) {
            printf('<p><strong>%s: </strong>%s</p>', __('Error', 'perfect-woocommerce-brands'), $rss->get_error_message());
            return;
        }

        if (!$rss->get_item_quantity()) {
            printf('<ul><li>%s</li></ul>', __('An error has occurred, which probably means the feed is down. Try again later', 'perfect-woocommerce-brands'));
            $rss->__destruct();
            unset($rss);
            return;
        }
?>
        <div>
            <div>
                <div style="margin-top: 11px;float: left;width: 70%;">
                    <?php esc_html_e('Hi! We are Quadlayers! Welcome to QuadLayers! We’re a team of international people who have been working in the WordPress sphere for the last ten years.', 'perfect-woocommerce-brands'); ?>
                    <div style="margin-top: 11px; float: left; width: 70%;"><a href="<?php echo admin_url('admin.php?page=' . PWB_PREFIX . '_suggestions'); ?>" target="_blank" class="button button-secondary"><?php esc_html_e('More products', 'perfect-woocommerce-brands'); ?></a></div>
                </div>
                <img style="width: 30%;margin-top: 11px;float: right; max-width: 95px;" src="<?php echo plugins_url('/assets/img/quadlayers.jpg', PWB_PLUGIN_FILE); ?>" />
            </div>
            <div style="clear: both;"></div>
        </div>
        <div style="margin: 16px -12px 0; padding: 12px 12px 0;border-top: 1px solid #eee;">
            <ul>
                <?php
                foreach ($rss->get_items(0, 3) as $item) {
                    $link = $item->get_link();
                    while (stristr($link, 'http') !== $link) {
                        $link = substr($link, 1);
                    }

                    $link = esc_url(strip_tags($link . '?utm_source=ql_dashboard'));
                    $title = esc_html(trim(strip_tags($item->get_title())));

                    if (empty($title)) {
                        $title = __('Untitled', 'perfect-woocommerce-brands');
                    }

                    $desc = html_entity_decode($item->get_description(), ENT_QUOTES, get_option('blog_charset'));
                    $desc = esc_attr(wp_trim_words($desc, 15, '...'));
                    $summary = $desc;
                    $summary = '<div class="rssSummary">' . $summary . '</div>';

                    $date = $item->get_date('U');
                    if ($date) {
                        $date = '<span class="rss-date">' . date_i18n(get_option('date_format'), $date) . '</span>';
                    }
                    $author = $item->get_author();
                    $author = ucfirst($author->get_name());
                    $author = ' <cite>' . esc_html(strip_tags($author)) . '</cite>';

                    printf(__('<li><a href="%s" target="_blank">%s </a>%s%s%s</li>', 'perfect-woocommerce-brands'), $link, $title, $date, $summary, $author);
                }  ?>
            </ul>
        </div>
        <div style="display: flex; justify-content: space-between;align-items: center;margin: 16px -12px 0;padding: 12px 12px 0; border-top: 1px solid #eee;">
            <a href="<?php printf('https://quadlayers.com/blog/?utm_source=%s&utm_medium=software&utm_campaign=wordpress&utm_content=dashboard', 'perfect-woocommerce-brands'); ?>" target="_blank"><?php esc_html_e('Read more like this on our blog', 'perfect-woocommerce-brands') ?></a>
            <a class="button-primary" href="<?php printf('https://quadlayers.com/?utm_source=%s&utm_medium=software&utm_campaign=wordpress&utm_content=dashboard', 'perfect-woocommerce-brands'); ?>" target="_blank"><?php esc_html_e('QuadLayers', 'perfect-woocommerce-brands') ?></a>
        </div>
<?php
    }

    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}


QL_Widget::instance();
