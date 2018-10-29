## === WP Rocket | Simple LoadCSS Preloader ===
### by [Ensemble Group](https://ensemblegroup.net)  
Contributors: ensemblebd
Tags: wprocket, loadcss, pagespeed, lighthouse, defer, async
Requires at least: 4.6
Tested up to: 4.9.8
Stable tag: 1.1
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html


Simple high performance plugin to enhance WP Rocket's css output to use the loadCSS preload polyfill.  
This will ensure **any css** being loaded via the page output **is deferred**, and utilizing a preload [rel] attribute.  
Note: This will not affect admin pages. It's designed only to process front-end pages.  
  
It is recommended to enable the WP Rocket setting for "Optimize CSS", which will provide your pages with inline Critical-CSS.  
That way an unstyled flash is not seen on page load (if properly configured of course).  

**This plugin will boost your [Lighthouse](https://developers.google.com/web/tools/lighthouse/) and [PageSpeed Insight](https://developers.google.com/speed/pagespeed/insights/) scores.**
  
## Plugin Mission 
We firmly believe that [WP Rocket](https://wp-rocket.me/) should implement this as a **feature** of their plugin.  
If that takes place, we will update this plugin to mark it as deprecated.  

## Requirements: 
* If you intend for logged-in users to see the effect, you must enable WP-Rocket's setting called "caching for Logged-In Users"
* Alternatively, you can run the plugin without WP-Rocket, despite it's intended purpose.    (>^.^)>

## Features:  
* [FilamentGroup's LoadCSS(v2.0.1)](https://github.com/filamentgroup/loadCSS/tree/v2.0.1) inline injector 
  (optional - see settings page next to WP Rocket), *triggered via wp_head*  
* High performance Regular Expression approach to process WP Rocket's (php's) output buffer, to replace stylesheets with the appropriate loadCSS syntax  
* Option to enable the buffer processor when you don't have WP Rocket installed.  
** This will respect AMP pages, and Yoast Sitemap output (by not executing). No other considerations have been implemented.  

No fluff. Just a robust loadCSS implementation.  

-----------------------------

You can see how it works, & measure the performance, with this(via repl.it): [Run the Plugin's code](https://repl.it/@ensemblebd/WPRocketLoadCSSMeasurement)  
Just paste your own page's url or html into the appropriate variable @ the top.  

And you can test this plugin's regex as well: [Wordpress.com html - links filtered by regex](https://regex101.com/r/xsugT7/1/)  

Github link is here: [Click](https://github.com/ensemblebd/wp-rocket-loadcss)  

## Last tested with:
* WP Rocket 3.1.x
* WordPress 4.9.x

## Dependencies
* WP Rocket  (optional via settings)  
* file_get_contents() php function  (if included loadCSS lib is used via options -- default = yes)

== Frequently Asked Questions ==
* Where are the settings?
  Right next to WP Rocket underneath the standard Wordpress Admin -> Settings
* Can I use this without WP Rocket? 
  Yes, just make sure and check the box to do so under settings. 
* What ignore conditions are supported at this time? 
  Yoast XML sitemap, and AMP pages. By default all Admin pages are ignored. 


== Upgrade Notice ==

= 1.0 =
The first release of the plugin.

== Screenshots ==
1. Settings Page
2. Resultant HTML
3. Sample Measurement
4. Sample Regex

== Changelog ==

= 1.0 =

* Release date: October 29, 2018

