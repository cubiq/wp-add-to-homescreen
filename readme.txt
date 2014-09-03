=== Official Add to Homescreen ===
Contributors: wordcubiq
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2MDE6YGQM7BCY
Tags: mobile, homescreen, home screen, bookmark, iphone, android
Requires at least: 3.5.1
Tested up to: 3.9.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Official Add To Homescreen plugin displays a callout to mobile users inviting them to add the website to the home screen.

== Description ==

**Official Add To Homescreen** is a WordPress wrapper for the [Add To Homescreen](http://addtohome.cubiq.org) javascript plugin. This WordPress plugin guides you through the rather complicated javascript widget configuration.

This is the only WordPress plugin developed by the same author of the javascript widget (hence "official"). If you wish to be always up to date and receive the latest bug fixes, this plugin is probably your best bet.

The plugin opens an always-on-top message inviting the mobile user to add the application to the home screen. This is currently supported on iOS and Mobile Chrome. While other devices have the ability to bookmark any website to the home screen, only iOS and Mobile Chrome have a straightforward way to do it. Windows Phone support is planned for a future release.

To enhance performance and reduce complexity I'm supporting the latest two OS version for each device only.

= Important! Please read =

Add To Homescreen development started when Apple introduced the `apple-mobile-web-app-capable` meta tag and `standalone` mode with it. It was a great news for web developers who had the opportunity to release full screen web applications (and not just websites). The script evolved over time and extended to a more general purpose use case.

It is important to note that **there's no native event we could hook to to know when a user actually added the page to the homescreen**. That's also the reason why this script has become so complicated despite the apparent simple task it has to accomplish.

Unless you explicitly developed your application to be `mobile-web-app-capable`, this script can't do miracles and all the alternative solutions have to be considered <em>hacks</em>.

The **Basic configuration** gives you four presets, the only 100% fail-safe solution is to select the **Anonymous** option. This disables user tracking but it doesn't interfere with other plugins you may have.

Considering the complexity of the script, before filing a negative feedback, please drop a support request or a bug report. It's the only way for me to enhance this software. Thanks!

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the full directory into your `wp-content/plugins` directory
2. Activate the plugin at the plugin administration page
3. Open the plugin settings page under Settings > Add to homescreen and add an application icon. Optionally customize the options to your liking
4. There's no 4. Enjoy!

== Frequently Asked Questions ==

= Why doesn't the call-out show up? =

By default the call out is set to show up only from the second time you access the website. Once displayed it doesn't bother your users for another 24h. These are all parameters you can vary in the **Advanced settings**.

Also, remember that this works only on iOS and Chrome for Android. No other mobile device or desktop browser will show the message.

= Can I upgrade to this plugin if I'm using an Add To Homescreen wrapper from another developer/vendor? =

Yes. This plugin detects previous versions of the javascript component and knows if users have already added the website to the home screen.

Just remember to deactivate the other plugins before installing this one.

== Screenshots ==

1. Basic configuration
2. Statistics

== Changelog ==

= 1.1.0 =
* Added "Homescreen Title" option
* Added "Destination Page" option
* Added Google Analytics integration
* Fixed custom message not showing up
* [JS] Added French translation

= 1.0.3 =
* [JS] Minor bug fixes
* [JS] Added German translation

= 1.0.2 =
* [JS] Fixed language detection
* [JS] Added Chinese translation
* [JS] Fixed close button

= 1.0.1 =
* Fixed paths

= 1.0.0 =
* Initial release

== License ==

This WordPress plugin is released under the GPL license. You can use it free of charge for personal and commercial use, but further modifications and derivatives have to be released under the same GPL license.

The Add To Homescreen javascript component is released under [MIT license](http://cubiq.org/license), which basically means you can do whatever you want with it.
