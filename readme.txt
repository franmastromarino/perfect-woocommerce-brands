=== Perfect WooCommerce Brands ===
Contributors: titodevera
Donate link: mailto:hola@albertodevera.es
Tags: woocommerce, brands, brand taxonomy, product brands, woocommerce manufacturer, woocommerce supplier, e-commerce
Requires at least: 4.4
Tested up to: 4.9
Stable tag: 1.6.3
License: GPL3
License URI: http://www.gnu.org/licenses/gpl-3.0.en.html

Perfect WooCommerce Brands allows you to show product brands in your WooCommerce based store

== Description ==
Whether we like to admit it or not, we are all influenced by brands. Brands are a guarantee for quality, they assure product recognition in customers.
Is essential to work with product brands for increase sales and generate reliability on your e-commerce site.
With this extension you can add product brands to your WooCommerce site.

= Requirements =
> * PHP 5.4 or higher (tested on PHP7 too)
> * WordPress 4.4 or higher
> * WooCommerce 2.4.0 or higher
> * Visual Composer (recommended)

= Features =
> * Very easy to use, 100% free, no ads, no premium version exists
> * Assign brands to products
> * Associate a banner and a link to each brand
> * Translation-ready (English and Spanish included)
> * Visual Composer support
> * Minimalist design and fully responsive
> * Very lightweight
> * Shortcode: Display all brands
> * Shortcode: Display brands carousel
> * Shortcode: Display product carousel by brand
> * Shortcode: Display brands for a specific product
> * Widget: Display brands as dropdown
> * Widget: Display brands as list (brand names or brand logos)
> * Widget: Filter products by brand
> * Customizable brands slug
> * Show the brands in products loop
> * Import brands (migrate) from other brands plugins
> * Dummy data installer (logos by heroturko)
> * WooCommerce REST API support
> * WooCommerce built-in product importer/exporter support (WooCommerce 3.1.0+)
> * Brand tab for single product page
> * Favorite brands
> * And much more!


== Installation ==
1. Upload the plugin to the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.


== Frequently Asked Questions ==
= Is Perfect WooCommerce Brands free? =
Yes, of course. This plugin is 100% free. No ads, no premium version exists.

= Where are plugin settings? =
Go to `WooCommerce/Settings/` and click on `Brands` tab

= How can i use the available shortcodes without Visual Composer? =
[Click here](https://github.com/titodevera/perfect-woocommerce-brands/wiki/How-can-i-use-the-available-shortcodes-without-Visual-Composer%3F)

= How to import brands from other brands plugin? =
[Click here](https://github.com/titodevera/perfect-woocommerce-brands/wiki/How-to-import-brands-from-other-brands-plugin)

= REST API docs =
[Click here](https://github.com/titodevera/perfect-woocommerce-brands/wiki/REST-API-docs)


== Screenshots ==
1. Brands carousel
2. Brands page
3. Brands taxonomy
4. Brands shortcodes (with Visual Composer)
5. Product carousel by brand


== Changelog ==
= 1.6.3 =
* Requirements: No more longer support for very old php versions (5.4 minimum)
* Fix: Product carousels shortcode display fixes
* Fix: Missing .pot file
* Fix: Coupon codes stopped working since latest update
* Fix: Carousel preloader is not working
= 1.6.2 =
* Fix: Carousels problems on responsive after the last update
= 1.6.1 =
* Feature: Favorite brands
* Fix: Brand logo full size option for single product
* Fix: Coupons brands restriction (thanks @josk79)
* Fix: Import products fixes
* Fix: PHP7 warning on 'class-perfect-woocommerce-brands.php'
* Fix: Brand not added when Duplicating Product
* Enhancement: Filter by brand widget enhancements
* Enhancement: Carousels enhancements
* Enhancement: JS and CSS minified
= 1.6.0 =
* Feature: Migrate brands from the official WooCommerce Brand's plugin (pull request, thanks Chunkford)
* Feature: Support for the new WooCommerce product importer/exporter
* Feature: Brand tab in single product page
* Enhancement: Brands dropdown widget selects the current brand if is a brand archive page
* Enhancement: Hide the brand name in "Display all brands" shortcode (title_position="none")
* Enhancement: Important enhancements for the REST API
* Fix: The brand property is not recognized by Google
* Fix: No shortcode appeared in some Visual Composer versions
* Fix: Filter by brand widget fixes
* Fix: Brand description adds description twice
* Other minor bug fixes and code improvements
= 1.5.2 =
* Dev: Adding brands to a products via the REST API + updating namespaces to include v2 (pull request, thanks doekenorg)
= 1.5.1 =
* Fix: Fatal error on old php versions (< 5.5)
= 1.5 =
* Feature (Tool): Install dummy data (generate generic brands and assigns them to available products randomly)
* Feature: Restrict coupon by brands
* Feature: Get system status details for a better plugin support
* Feature: Import brands from "Ultimate WooCommerce Brands"
* Dev: 'pwb_before_single_product_brands' and 'pwb_after_single_product_brands' hooks added
* Dev: Basic WordPress REST API support
* Enhancement: Better HTML markup for the brand banner and the brand description for the archive product page
* Enhancement: Brands importer improvements
* Enhancement: New params for "pwb-all-brands" shortcode
* Fix: The brand description is appearing twice
* Fix: Product filter widget does not seems to work in product category page (thanks hassandad)
* Fix: Support for php 5.3 again
* Fix: Removed ES6 code from admin for better browser support
* Other minor bug fixes and code improvements
= 1.4.5 =
* Fix carousel shortcodes bugs
* WooCommerce 2.7 support
= 1.4.4 =
* Important improvement of the user experience when assigning images to a brand
* Feature (Option): Hide brands in single product, show as image or show as link
* Feature: Hide prev/next arrows on carousel shortcodes
* Feature: Hide empty brands option for "Display all brands" shortcode
* Feature: New options for "Display brands as list" widget
* Fix: Autoplay bug on carousel shortcodes
* Tested on PHP7
* Shortcut to the plugin settings added to the plugin list page
* "Help us" notice added
* Minor code improvements
= 1.4.3 =
* Feature (Widget): Filter products by brand
* Feature (Option): Show brand logo in product loop
* Feature: Brand microdata added to product page
* Feature (Tool): Migrate brands from "YITH WooCommerce Brands Add-on"
= 1.4.2 =
* Fix: Table collapses in "Admin > Products > Brands" (thanks eljkmw)
* Fix: "pwb-brand" shortcode does not display the brand when it has not a logo assigned
= 1.4.1 =
* Feature (Option): Hide brand's description in archive page
* Fix: "pwb-all-brands" and "pwb-brand" show the content before they should
* Clean database on uninstallation
* Minor code improvements and fixes
= 1.4 =
* Feature: Product carousel by brand added
* Minor bug fixes
= 1.3 =
* Feature: If is set, show brand description in brand page
* Feature: Change brands position in single product
= 1.2 =
* Feature: Associate a banner and a link to each brand
* Minor tweaks
* Fully tested on WooCommerce 2.6
= 1.1 =
* Minor bug fixes
= 1.0 =
* Initial release
