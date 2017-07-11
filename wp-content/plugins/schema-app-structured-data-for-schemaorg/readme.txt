=== Schema App Structured Data ===
Contributors: vberkel
Plugin Name: Schema App Structured Data
Tags: schema, structured data, schema.org, rich snippets
Author URI: https://www.hunchmanifest.com
Author: Mark van Berkel (vberkel)
Requires at least: 3.5
Tested up to: 4.4.1
Stable tag: 0.5.7
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin adds schema.org structured data to all Wordpress pages and posts. Extend with Schema App Tools to specify any schema.org structured data.

== Description ==
So you do some on page SEO and now you want to take it to the next level with structured data. This plugin (and the Schema App tool) is designed for people who want to add schema.org markup/structured data extensively across your Wordpress site without having to worry about code.  

How do we do this? By default, the plugin deploys schema.org markup on your WordPress site on your pages, posts, author pages and more. The plugin sets your Pages and Posts with schema.org data for [Accelerated Mobile Page](https://www.ampproject.org/) (or AMP, for short), a project from Google and Twitter designed to make really fast mobile pages. 

However what makes it the most powerful schema.org plugin out there is that the plugin connects with the Schema App Editor tool. [Schema App](http://www.schemaapp.com) is designed to let Marketers, Business Owners, SEO Experts add their content/details and then generates the code and adds it to your Wordpress website automatically.  We include the entire Schema.org v2.2 vocabulary and have built in validation and business rules to make sure the time you put in results in valuable results from search engines.  You don't need a developer to use our tools and you can update your markup anytime without having to do re-work. 

[Schema App](http://www.schemaapp.com) also allows you to manage markup across domains, making it easy to scale your schema.org deployments and manage them easily.  If you want to add structured data to your Wordpress website in the most productive and smart way, you have found it!

> <strong>Premium Features</strong><br>
> Schema App has a freemium pricing model. Advanced features like Validation, Crawler & Reporting are available with [Schema App Pro and Agency](http://www.schemaapp.com/product-pricing/) accounts. 
> 
> <strong>Schema App WooCommerce</strong><br>
> An ecommerce add-on [Schema App WooCommerce](https://www.schemaapp.com/product/schema-app-woocommerce/) integrates with this Schema App plugin. This add-on plugin is the most comprehensive Schema.org plugin for WooCommerce, doubling the structured data available to search engines.
> 
> <strong>Premium Support</strong><br>
> Want some help to get all the benefits of structured data on a small website but don’t need a Pro account? Buy the [Premium Support package](http://www.schemaapp.com/product-pricing/#premium-support) so that you get direct (email) access to our professional support team who will answer all your questions related to implementing Schema App.
> 
> We know that Structured Data and Schema.org can be a bit overwhelming, so don’t hesitate to ask our support team if you don’t understand how to achieve your desired results. Our support is made up of semantic search experts so know that you’re going to get great support.

= Baseline Schema.org Markup =
Just by installing the plugin you will get the schema.org AMP compliant markup for the all your page and post content. Page types such as Search< Author and Category also get default markup. 

* Page : http://schema.org/Article
* Post : http://schema.org/BlogPosting
* Search : http://search.org/SearchResultsPage
* Author : http://schema.org/Author
* Category : http://schema.org/CollectionPage

= Schema App Enabled Features =
Extend your schema.org structured data to speak to Google's many advanced search features with your Wordpress site using [Schema App](http://www.schemaapp.com). Common features include:

* Customize Knowledge Graph (Logo, Corporate Contacts, Social Profile Links)
* Event Promotion (for Performers, Venues, Ticketers)
* Actions (Music Play, Movie Watch, Promote Critic Reviews)
* Content Carousels (Live Blogs)
* Rich Snippets (Recipe, Events, Articles, Videos)
* Local Business (Place Actions, e.g. Reservation, Order)
* Style Search Results (Breadcrumbs, Sitelinks Search Box, Show Name in Search)

== Installation ==

Installation is straightforward

1. Upload hunch-schema to the /wp-content/plugins/ directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.`
1. Add the GRAPH ID found in http://app.schemaapp.com/wordpress 
1. Add publisher settings for AMP Content

== Frequently Asked Questions ==
You\'ll find the [FAQ on SchemaApp.com] (http://www.schemaapp.com/wordpress/faq/).

== Screenshots ==
1. Schema App Tools Admin
2. Settings Menu Navigation
3. Schema.org Page Meta Box
4. Schema.org Editor UI
5. Link to Validation

== Changelog ==
= 0.5.7 = 
- Fix, Publisher image dimensions
- Fix, Author name for Pages
- Fix, API results filter null

= 0.5.6 = 
- Feature, Rename menu item 'Schema Settings' as 'Schema App'
- Feature, Admin Settings redesign as tabs
- Feature, Tab for Quick Guide
- Feature, License Tab for enabling WooCommerce plugin extension

= 0.5.5 = 
- Fix, Setting Publisher Image Upload
- Feature, Add Admin Notices
- Security, Prevent scripts being accessed directly

= 0.5.4 = 
- Fix for Publisher Logo Upload

= 0.5.3 = 
- Fix Editor JSON-LD Preview

= 0.5.2 = 
- Timeout after 5 seconds
- Tested up to WP 4.4.1

= 0.5.1 = 
- Suppress Warning when no content found

= 0.5.0 = 
- Extend Page and Post Markup for Accelerated Mobile Pages

= 0.4.4 = 
- Plugin Description Update
- Fix Meta Box Update (Create) Link

= 0.4.3 = 
- Fix Meta Box Update Link

= 0.4.2 = 
- Fix Category page error

= 0.4.1 = 
- Fix PHP Warning from empty Graph ID

= 0.4.0 = 
- Add Author, Category and Search page types
- Show formatted and default markup in Meta Box
- Change date formats to ISO8601
- Code refactoring
- Add Banner and Icon

= 0.3.3 = 
- Fixes to getResource routine

= 0.3.2 = 
- Fix PHP warning

= 0.3.1 = 
- Fix server file_get_contents warning

= 0.3.0 =
- When no data found in Schema App, add some default page and post structured data

= 0.2.0 =
- Add Post and Page Edit meta box
- Server does caching, switch from Javascript to PHP API to retrieve data for header

= 0.1.0 =
- First version 