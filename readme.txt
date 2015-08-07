=== Easy Development Mode ===
Contributors: DeusMachineLLC, Lee Ralls
Tags: development, ip address, restrict
Requires at least: 3.4.1
Tested up to: 4.0
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Restrict access on your globally-accessible development server to a single (or multiple) IP address, with the option to redirect somewhere else.

== Description ==

= This plugin is useful for: =

* Development web servers that are publicly accessible
* Allowing only you and your clients to view the development project

= Features =

* Redirect unauthorized traffic elsewhere
* Whitelist single or multiple IP addresses


== Installation ==

1. Unzip the ZIP file and drop the 'easy-development-mode' folder into your 'wp-content/plugins/' folder.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Enter the settings page: Settings -> Development Mode Settings
4. Enter a URL to redirect all traffic to (http://google.com by default)
5. Enter whitelisted IP addresses separated by commas. Your IP address is added by default.

== Frequently Asked Questions ==

= I added multiple IP addresses and now all traffic is blocked. What do I do? =

Make sure that your list of IP addresses are comma separated. Ex: 127.0.0.1, 192.168.1.2, 10.0.0.2

== Changelog ==

= 1.0 =
Initial build