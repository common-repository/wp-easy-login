=== WP Easy Login - Remember Recent Usernames ===
Contributors: 5um17, ankit-k-gupta
Tags: login, easy login, remember username, recent logins
Requires at least: 4.0
Tested up to: 6.5.2
Stable tag: 1.0.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

WP Easy Login stores the recent logins and makes it easy for you to login by selecting an account.

== Description ==
WP Easy Login remember the username of each user who logged-in to the site. Usernames are stored in the browser and displayed when you visit default login page of the website using the same browser.
It is very useful plugin when users have many accounts on the same site.

= Features =
* Easy login by just entering the password and selecting an account for login
* Define the age of cookie to store the username in a browser
* Customize the users' list by turning off/on Email, Role and Gravatar
* Customize hints and button labels
* Users can remove their accounts from the browser at any time.
* Works with WordPress default login page

== Installation ==

* Install WP Easy Login from the 'Plugins' section in your dashboard (Plugins > Add New > Search for 'WP Easy Login').
  Or
  Download WP Easy Login and upload it to your webserver via your FTP application. The WordPress codex contains [instructions on how to do this here](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).
* Activate the plugin and navigate to (Settings > WP Easy Login) to choose your desired settings.

== Frequently Asked Questions ==

= Does it work with Front-end login pages? =

No, It doesn't work with front-end login pages. It is designed just for the default login page.

= How does it affect the security of login? =

WP Easy login just modifies the login form, no effect on WordPress login process.

= What does it store in the cookies? =

It stores the username along with browser and OS information in base64 encoded format. The age of cookie can be customized. The default value is one year.

== Screenshots ==
1. Demo Video
2. Select an account for login
3. Login with new account
4. WP Easy Login settings

== Changelog ==

= 1.0.2 - 2024-04-14 =
* Fixed issues with PHP 8.1

= 1.0.1 - 2019-11-23 =
* Fixed issues with WP 5.3

= 1.0.0 - 2018-05-29 =
* Initial Release
