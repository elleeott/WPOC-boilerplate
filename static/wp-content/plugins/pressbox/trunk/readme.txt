=== Plugin Name ===
Contributors: lesharris
Donate link: http://www.lesharris.com/pressbox
Tags: dropbox, media, images, admin
Requires at least: 3.0
Tested up to: 3.1.1
Stable tag: 1.0

Integrates DropBox with your blog allowing you to post images and files directly from your DropBox. Requires PHP 5.2 and cURL. 

== Description ==

The Pressbox plugin adds a new tab to the Media Upload screen that allows you to insert images and files from Dropbox into your posts.

Additionally, the plugin provides a new shortcode that lets you add links or display images anywhere you want on your blog.

This plugin is under heavy development so please feel free to send me feedback, bug reports, and feature requests!

Credits:

This plugin bears a great debt to the work of Joe Doleson's Wp To Twitter plugin for his excellent example of OAuth integration with Wordpress.

== Changelog ==

= 1.0 = 

*Initial release.

== Installation ==

1. Upload the `pressbox` folder to your `/wp-content/plugins/` directory
2. Activate the plugin using the `Plugins` menu in WordPress
3. Go to Settings > Pressbox
4. Adjust the Pressbox Options as you prefer them. 
5. Supply your DropBox Key and Secret, the settings page has detailed instructions on how to get these.
6. Start using Pressbox!

== Frequently Asked Questions ==

= I can't connect to DropBox using OAuth. What's wrong? =

Common problems are you don't have cURL support or that your server time is incorrect. Check with your host to verify these possibilities. 

= Do I have to have a DropBox account to use this plugin? =

Yes, you need an account with DropBox to use this plugin.

= What if my server doesn't support the methods you use to contact these other sites? =

There is not much to be done about this except ask your provider to update.  The plugin will check for requirements on activation and let you know if something is missing.

== Upgrade Notice ==



== Screenshots ==

1. Pressbox displays thumbnails for your images to allow for easy image picking.
2. Pressbox gives you access to your entire Dropbox.
3. Pressbox uses Dropbox's new OAuth based Web API. No need to move all the files you want to use to the Public folder.
4. Pressbox can display image thumbnails, allow you to save favorite folders for easy navigation, and set a custom default folder.
