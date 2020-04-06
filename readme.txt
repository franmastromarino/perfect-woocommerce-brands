=== Perfect WooCommerce Brands ===
Contributors: titodevera
Donate link: https://quadlayers.com
Tags: woocommerce, woocommerce brands, woocommerce product, woocommerce manufacturer, woocommerce supplier, e-commerce
Requires at least: 4.7
Tested up to: 5.4.0
Requires PHP: 5.6
Stable tag: 1.8.2
WC requires at least: 3.0
WC tested up to: 4.0
License: GPL3
License URI: http://www.gnu.org/licenses/gpl-3.0.en.html

Perfect WooCommerce Brands allows you to show product brands in your WooCommerce based store

== Description ==

Perfect WooCommerce Brands is a perfect tool to organize your site, highlight the brands you have, and also helps as a filter for your customers at UX exploration. PWB extendes the product's description and presentation at your e-commerce site.

== PRESENTATION ==

[About Us](https://quadlayers.com/) | [Community](https://www.facebook.com/groups/quadlayers/) | [Documentation](https://quadlayers.com/documentation/perfect-woocommerce-brands/)

Whether we like to admit it or not, we are all influenced by brands. Brands are a guarantee for quality, they assure product recognition in customers.
Is essential to work with product brands for increase sales and generate reliability on your e-commerce site.
With this extension you can add product brands to your WooCommerce site.

= Requirements =
> * PHP 5.6 or higher (PHP7 recommended)
> * WordPress 4.7 or higher
> * WooCommerce 3.1.0 or higher

= Features =
> * Very easy to use, 100% free, no ads, no premium version exists
> * Assign brands to products
> * Associate a banner and a link to each brand
> * Translation-ready
> * Visual Composer support
> * Minimalist design and fully responsive
> * Very lightweight
> * Shortcode: Display all brands
> * Shortcode: Display brands carousel
> * Shortcode: Display product carousel by brand
> * Shortcode: Display brands for a specific product
> * Shortcode: A-Z Listing
> * Widget: Display brands as dropdown
> * Widget: Display brands as list (brand names or brand logos)
> * Widget: Filter products by brand
> * Customizable brands slug
> * Show the brands in products loop
> * Import brands (migrate) from other brands plugins
> * Dummy data installer (logos by heroturko)
> * WooCommerce REST API support
> * WooCommerce built-in product importer/exporter support
> * Brand tab for single product page
> * Favorite brands
> * Brands json import/export
> * Brand structured data
> * And much more!


== Installation ==
1. Upload the plugin to the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.


== Frequently Asked Questions ==
= Is Perfect WooCommerce Brands free? =
Yes, of course. This plugin is 100% free. No ads, no premium version exists.

= Where are the plugin settings? =
Go to `WooCommerce/Settings/` and click on `Brands` tab

= 404 error on brand pages =
Go to `Settings/Permalinks` and click on `Save Changes` button to flush the permalinks

= PWB is awesome! Can I contribute? =
Yes you can! Join in on our [GitHub repository](https://github.com/quadlayers/perfect-woocommerce-brands) 🙂
You can also contribute [translating the plugin](https://translate.wordpress.org/projects/wp-plugins/perfect-woocommerce-brands/)

= Developer Documentation =
[Click here](https://github.com/quadlayers/perfect-woocommerce-brands/wiki)

== Screenshots ==
1. Brands carousel
2. Brands page
3. Brands taxonomy
4. Brands shortcodes (with Visual Composer)
5. Product carousel by brand


== Changelog ==
= 1.8.2 =
* Enhancement: Update documentation
= 1.8.1 =
* Enhancement: Update documentation link
= 1.8.0 =
* Enhancement: Main file renamed according to WordPress standards
* Enhancement: "Show only first level brands" option added for the filter by brand widget
= 1.7.9 =
* Fix: Breadcrumbs issues
* Fix: Ajax search plugins conflict since previous release
* Feature: New position option "in meta" for brands in the single product page
* Feature: New param "only_parents" for "pwb-az-listing"
* Enhancement: Sections for brands settings tab in admin
* Enhancement: "[pwb-az-listing]" transient for better performance in large sites
= 1.7.8 =
* Fix: Hidden products contribute towards product count for "pwb-all-brands" shortcode
* Enhancement: Replace hardcoded thumbnail size for product loops with selected size (thanks @orjhor)
* Feature: Redirect if the search matches with a brand name. Better product search experience.
* Feature: Add the possibility to filter by product category on product carousel shortcode (thanks @lasdou)
* Dev: New filter hook "pwb_html_before_brands_links"
* Tested on WooCommerce 3.9
= 1.7.7 =
* Enhancement: Better support for special characters in "AZ-Listing" shortcode
* Fix: Filter by brand widget shows incorrect brands under certain conditions
* Fix: Filter by brands in product tags pages and other product taxonomies
* Update: WC 3.8 compatibility
* Dev: New filter hook "pwb_text_before_brands_links"
* Dev: New filter hook "pwb_product_tab_brand_logo_size"
* Dev: "[pwb-az-listing]" and "[pwb-brand]" templates for theme overrides
= 1.7.6 =
* Tested up to, bump
* Dev: New filter hook "woocommerce_product_brand_heading" for editing the "Brand" tab heading
* Fix: Some problems with the "Brand" product tab
* Fix: Avoid a possible JS conflict caused by "wp_localize_script"
= 1.7.5 =
* Fix: Stupid JS error introduced in the previous version
= 1.7.4 =
* Fix: Mismatched columns when a brand is added
* Tweak: Disable brand tab when product not have brand
* Dev: New filter hook 'pwb_dropdown_placeholder'
* Dev: New filter hooks 'pwb_carousel_prev' and 'pwb_carousel_next' for customize the carousel arrows
* Dev: Override templates via a theme
* Minor fixes and tweaks
= 1.7.3 =
* Enhancement: Performance improvements
* Fix: Breadcrumbs doesn't take in account pagination
= 1.7.2 =
* Feature: Now is possible to use %pwb-brand% in the product URLs
* Fix: Fatal error in some themes (like Salient)
* Fix: Some php notices and warnings fixed
* Fix: Don't show brand description/banner in paged
= 1.7.1 =
* Feature: Extends the [products] shortcode to allow 'brands' attribute
* Feature: The brands column is sortable now
* Catalan translation (thanks to Lluisma)
* Enhancement: Easier select brands page link in breadcrumbs
* Enhancement: Configure the brand banner and the brand description position independently
* Dev: New filter 'pwb_taxonomy_rewrite' to change the rewrite slug
* Dev: New filter 'pwb_taxonomy_with_front' to change default "with_front" value
* Fix: Fatal error in PHP5.4
= 1.7.0 =
* Feature: A-Z Listing shortcode
* Feature: New brands exporter/importer introduced
* Fix: Missing some brands to filter
* Fix: Coupon doesn’t work for product variations (thanks @gekomees)
* Fix: The brand description is not shown if there are no products
* Enhancement: wpautop() for brand descriptions
* Enhancement: Other filter by brand widget enhancements
* Enhancement: Brands page link in breadcrumbs
* Enhancement: Enqueue the carousel lib conditionally
* Dev: New filter hook 'pwb_description_allowed_tags'
* Update: WC 3.5 compatibility
