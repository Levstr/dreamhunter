=== Plugin Name ===
Contributors: ryelle
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=YB5AWJMBLCCVC&lc=US&item_name=redradar%2enet&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted
Tags: quick post, frontend, insert post, post, Post
Requires at least: 2.7
Tested up to: 3.1
Stable tag: 2.0.3

This plugin allows you to post straight from your front-end (i.e. website) - perfect for a quick update, or if you just don't want to deal with the backend.

== Description ==

Versions 2.0.1+ are compatible with 3.0! 2.0.1 corrects an error from 2.0.0, which caused a conflict with 3.0. Newer versions are compatible with previous versions of WordPress, though you should be using 3.0.

This new wordpress plugin allows you to post straight from your front-end (i.e. website) - perfect for a quick update! Also useful if you have multiple users and don’t need them to see the admin side of things. It creates a link on your website which, when clicked, will bring up a simple text-box. Users can enter a post title (required), content (also required), add categories and tags (including created new ones), and upload images. Images can be placed in your post with custom tags (`[!--image1--]`), or all appended to the end of the post.

On the admin side, there is a settings page where you can edit the plugin to your preferences. You can customize the link text, post-box background color, title/text color, and even add your own CSS to tailor pfs to your site. As for permissions, you can limit the categories pfs can post to and allow/disallow uploading of images. If you'd rather have pfs's posts approved before they are visible, you can set the post status to ‘pending’ or ‘draft’. Same with the comment status, it can default to ‘open’ (allowing comments) or ‘closed’ (not allowing comments).

== Installation ==

1. Unzip `pfs.zip`
1. Upload all files to the `/wp-content/plugins/post-from-site` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php if (function_exists('post_from_site')) {post_from_site();} ?>` in your templates where you want to see the link.

== Changelog ==
2.0.3

* Fixed the call to the non-existant 'pfs-widget.php'.

2.0.2

* Fixed an issue with headers

* Changed the `div` tag back to an `a` tag.

2.0.1

* Compatibility with 3.0

2.0.0

* scrapped a lot of code, most of it never made a release

* moved over to strictly using jQuery

* multiple file upload support

* submits using ajax, then refreshes page, so you can see you addition immediately

* also gets rid of the double-post if you refresh the page

* default style has been changed

1.9.0 

* fixes double posting; 

* better image support; 

* introduction of '[!--image--]' tag; 

* existing category/tag dropdown with multiple selection;  

* ability to create new categories/tags;  

* other minor adjustments

1.7.0

* addition of tags 

* bugfixes

1.6.x

* Initial releases

== Frequently Asked Questions ==

This is left over from versions 1.x of the plugin.

1. The pop-up won't pop-up! (aka there is a link, but clicking it does nothing.)

Check that you have the javascript and css files in the plugin's folder (`post-from-site`). A problem with the first version of this plugin was that the plugin was looking for the files in the wrong directory. For other people it was also a problem with my code assuming a Linux filestructure, so on Windows servers it broke. 2.0.0+ shouldn't have this problem, as I'm using a different method of calling other files.

[ask a question](http://www.redradar.net/wp/?p=95)?

== Screenshots ==

1. Post-from-site's setting page in admin
2. Post-from-site in action - *note: you can customize the CSS yourself so it doesn't need to look like the above.*
