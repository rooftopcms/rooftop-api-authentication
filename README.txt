=== Plugin Name ===
Contributors: rooftopcms
Tags: rooftop, api, admin, headless
Requires at least: 4.3
Tested up to: 4.8.1
Stable tag: 4.3
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

rooftop-api-authentication allows users to be authenticated with a custom token, which is randomly generated.

== Description ==

rooftop-api-authentication adds the ability for you to create client tokens, allowing your sites & apps
to easily authenticate when requesting data from Rooftop CMS

Track progress, raise issues and contribute at http://github.com/rooftopcms/rooftop-api-authentication

== Installation ==

rooftop-api-authentication is a Composer plugin, so you can include it in your Composer.json.

Otherwise you can install manually:

1. Upload the `rooftop-api-authentication` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. There is no step 3 :-)

== Frequently Asked Questions ==

= Can this be used without Rooftop CMS? =

Yes, it's a Wordpress plugin you're welcome to use outside the context of Rooftop CMS. We haven't tested it, though.

= How does the user authenticate? =

The user passes an authorisation header called 'api-token' with their API request. You can choose whether to allow a specific token to have read-only or read-write access.

= Can I re-use a token? =

You can, but it's probably not wise. If you want to have several applications accessing your Rooftop account, we suggest creating several tokens.

== Changelog ==

= 1.2.1 =
* Admin UI cleanup
* Updated readme for packaging

= 1.2.0 =
* Removed deprecated calls to rest_post_query and implement a post-type specific query filter

= 0.0.1 =
* Initial release

== What's Rooftop CMS? ==

Rooftop CMS is a hosted, API-first WordPress CMS for developers and content creators. Use WordPress as your content management system, and build your website or application in the language best suited to the job.

https://www.rooftopcms.com
