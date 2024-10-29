=== Add Spam Filters ===
Contributors: adaptsites, keganquimby
Tags: stopforumspam, emailable, validate, email, spam, filter,
Requires at least: 5.0
Tested up to: 5.8.2
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Stable tag: trunk

Stop Spam User Registrations by Adding StopForumSpam.com, Emailable.com, and Email Domain Extension Filters.

== Description ==
This plugin adds filters to the WordPress user registration process. It lets you check user signups against StopForumSpam.com and/or Emailable.com

When a user registers, you can configure this plugin to use the submitted email address and IP address to first check against StopForumSpam.com to see if there is a match to the IP address and/or the email address in their FREE database. Then you can check if the ending of the email address is .ru or another country extension you want to block to cut down on spam registrations. Finally, if it passes these checks, you can check the email address against Emailable.com to see if the email is valid.

[Emailable.com](https://emailable.com)
Validate user registration emails using Emailable.com to see if an email is: deliverable, risky, unknown, or undeliverable.


== Installation ==
1. Upload \"add-spam-filters.zip\" to the \"/wp-content/plugins/\" directory.
2. Then activate this plugin.
3. Visit the plugin settings page to customize the spam filters.

== Changelog ==
June 30, 2020
* Initial release.

November 12, 2021
* Updated plugin to use Emailable.com