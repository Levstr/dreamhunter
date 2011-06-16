=== Plugin Name ===
Contributors: techotronic
Donate link: http://www.techotronic.de/donate/
Tags: jquery, colorbox, lightbox, images, pictures, photos, gallery, javascript, overlay, nextgen gallery, nextgen-gallery, image, picture, photo, media, slideshow, ngg, mu
Requires at least: 2.8
Tested up to: 3.1
Stable tag: 4.0

Adds Colorbox/Lightbox functionality to images, grouped by post or page. Works for WordPress and NextGEN galleries. Comes with different themes.

== Description ==

Yet another Colorbox/Lightbox plugin for Wordpress.
jQuery Colorbox features 11 themes from which you can choose. See <a href="http://www.techotronic.de/plugins/jquery-colorbox/theme-screenshots/">my website</a>.
Works out-of-the-box with WordPress Galleries and <a href="http://wordpress.org/extend/plugins/nextgen-gallery/">NextGEN Gallery</a>! (choose no effect in NextGEN settings)

When adding an image to a post or page, usually a thumbnail is inserted and linked to the image in original size.
All images in posts and pages can be displayed in a layer when the thumbnail is clicked.
Images are grouped as galleries when linked in the same post or page. Groups can be displayed in an automatic slideshow.

Individual images can be excluded by adding a special CSS class.

jQuery Colorbox can also open linked content (external as well as inline) in a Colorbox.

See the <a href="http://www.techotronic.de/plugins/jquery-colorbox/">plugin page</a> for demo pages.

For more information visit the <a href="http://wordpress.org/extend/plugins/jquery-colorbox/faq/">FAQ</a>.
If you have questions or problems, feel free to write an entry at <a href="http://wordpress.org/tags/jquery-colorbox?forum_id=10">the jQuery Colorbox WordPress.org forum</a>

Localization

* Arabic (ar) by <a href="http://www.photokeens.com">Modar Soos</a>
* Belorussian (be_BY) <a href="http://www.pc.de/">Marcis G.</a>
* Bosnian (bs_BA) by <a href="http://www.vjucon.com/">Vedran Jurincic</a>
* Dutch (nl_NL) by <a href="http://nl.linkedin.com/pub/richard-laak/b/b21/672">Richard van Laak</a>
* English (en_EN) by <a href="http://www.techotronic.de/">Arne Franken</a>
* French (fr_FR) by <a href="http://www.tolingo.com/">Tolingo Translations</a>
* German (de_DE) by <a href="http://www.techotronic.de/">Arne Franken</a>
* Hebrew (he_IL) by <a href="http://www.TommyGordon.co.il">Tommy Gordon</a>
* Italian (it_IT) by <a href="http://erkinson.altervista.org/">Erkinson</a>
* Latvian (lv) by <a href=\"http://lapuvieta.lv\">Uldis Jansons</a>
* Malay (ms_MY) by <a href="http://www.inisahaini.com">Saha-ini Ahmad Safian</a>
* Portuguese (pt_BR) by <a href="http://twitter.com/gervasioantonio">Gervásio Antônio</a>
* Russian (ru_RU) by <a href="http://drive2life.ru">Arkadiy Florinskiy</a>
* Slovak (sk_SK) by <a href="http://itec.sk/">Stefan Stieranka</a>
* Spanish (es_ES) by <a href="http://www.redywebs.com/">Inma P.-Zubizarreta</a>
* Swedish (sv_SE) by <a href="http://www.theindiaexperience.se/">Christian</a>
* Turkish (tr_TR) by <a href="http://www.serhatyolacan.com/">Serhat Yolaçan</a>
* Ukrainian (uk) by <a href="http://www.politcult.com/">Yuri Kryzhanivskyi</a>

Is your native language missing? Translating the plugin is easy if you understand english and are fluent in another language. Just send me an email.

Includes <a href="http://colorpowered.com/colorbox/">ColorBox</a> 1.3.16 jQuery plugin from Jack Moore.
Colorbox is licensed under the <a href="http://www.opensource.org/licenses/mit-license.php">MIT License</a>.
jQuery Colorbox uses the jQuery library version 1.4 bundled with WordPress 3.x. Also works with jQuery 1.3.2 bundled with Wordpress 2.9.
The picture I used for the screenshots was designed by <a href="http://wallpapers.vintage.it/">Davide Vicariotto</a>

== Installation ==

###Updgrading From A Previous Version###

To upgrade from a previous version of this plugin, use the built in update feature of WordPress or copy the files on top of the current installation.

###Installing The Plugin###

Either use the built in plugin installation feature of WordPress, or extract all files from the ZIP file, making sure to keep the file structure intact, and then upload it to `/wp-content/plugins/`. Then just visit your admin area and activate the plugin. That's it!

###Configuring The Plugin###

Go to the settings page and choose one of the themes bundled with the plugin and other settings.
Do not forget to activate auto Colorbox if you want Colorbox to work for all images.

**See Also:** <a href="http://codex.wordpress.org/Managing_Plugins#Installing_Plugins">"Installing Plugins" article on the WP Codex</a>

== Screenshots ==

<a href="http://www.techotronic.de/plugins/jquery-colorbox/theme-screenshots/">Please visit my site for screenshots</a>.

== Frequently Asked Questions ==
* I installed your plugin, but when I click on a thumbnail, the original picture is loaded directly instead of in the Colorbox. What could be the problem?

Make sure that your theme uses the `wp_head();` function in the HTML head-tag.

I have seen problems where other plugins include their own versions of the jQuery library my plugin uses.
Chances are that the other jQuery library is loaded after the one that I load. If that happens, the colorbox features are overwritten.

* How does jQuery Colorbox work?

When inserting a picture, the field "Link URL" needs to contain the link to the full-sized image. (press the button "Link to Image" below the field)
When rendering the blog, a special CSS class ("colorbox-postId", e.g. "colorbox-123") is added to linked images.
This CSS class is then passed to the colorbox JavaScript.

* How do I exclude an image from Colorbox in a page or post?

Add the CSS class "colorbox-off" to the image or to the link to the big sized image you want to exclude.
jQuery Colorbox does not add the colorbox effect to images that have the CSS class "colorbox-off".

* How does jQuery Colorbox group images?

For all images in a post or page, the same CSS class is added. All images with the same CSS class are grouped.

* I have Flash (e.g. Youtube videos) embedded on my website. Why do they show up over the layer when I click on an image?

This is a Flash issue, but relatively easy to solve. Just activate the automatic hiding of embedded flash objects on the settings page.

Adobe described on these sites what the problem is and how to fix it manually:


<a href="http://kb2.adobe.com/cps/155/tn_15523.html">Adobe Knowledgebase 1</a>


<a href="http://kb2.adobe.com/cps/142/tn_14201.html">Adobe Knowledgebase 2</a>

* I have a problem with the Colorbox or want to style it my own way. Can you help?

Of course I want to help everyone, but I have a full time job and I don't have the time. You can ask your questions at the <a href="http://groups.google.com/group/colorbox/topics">Colorbox Google group</a>.

* Why is jQuery Colorbox not available in my language?

I speak German and English fluently, but unfortunately no other language well enough to do a translation.

Would you like to help? Translating the plugin is easy if you understand English and are fluent in another language.

* How do I translate jQuery Colorbox?

Take a look at the WordPress site and identify your langyage code:
http://codex.wordpress.org/WordPress_in_Your_Language


e.g. the language code for German is "de_DE".


Step 1) download POEdit (http://www.poedit.net/)


Step 2) download jQuery Colorbox (from your FTP or from http://wordpress.org/extend/plugins/jquery-colorbox/)


Step 3) copy the file localization/jquery-colorbox-en_EN.po and rename it. (in this case jquery-colorbox-de_DE.po)


Step 4) open the file with POEdit.


Step 5) translate all strings. Things like "{total}" or "%1$s" mean that a value will be inserted later.


Step 5a) The string that says "English translation by Arne ...", this is where you put your name, website (or email) and your language in. ;-)


Step 5b) (optional) Go to POEdit -> Catalog -> Settings and enter your name, email, language code etc


Step 6) Save the file. Now you will see two files, jquery-colorbox-de_DE.po and jquery-colorbox-de_DE.mo.


Step 7) Upload your files to your FTP server into the jQuery Colorbox directory (usually /wp-content/plugins/jquery-colorbox/)


Step 8) When you are sure that all translations are working correctly, send the po-file to me and I will put it into the next jQuery Colorbox version.

* My question isn't answered here. What do I do now?

Feel free to write an email to blog [at] techotronic.de or open a thread at <a href="http://wordpress.org/tags/jquery-colorbox?forum_id=10">the jQuery Colorbox WordPress.org forum</a>.

I'll include new FAQs in newer versions.

== Changelog ==
= 4.0 (2011-04-16) =
* CHANGE: Restructured settings page
* CHANGE: plugin again compatible to PHP4.
* CHANGE: Update of Colorbox library to version 1.3.16
* CHANGE: jQuery calls won't break other JavaScript libraries like Prototype or Scriptaculous any more
* NEW: set width and height for Colorbox links once separate from the Colorbox setting for images
* NEW: inline HTML content can now be loaded in the Colorbox.
* NEW: Latvian translation by <a href=\"http://lapuvieta.lv\">Uldis Jansons</a>
* CHANGE: <a href="http://www.photokeens.com">Modar Soos</a> updated the Arabic translation
* CHANGE: <a href="mailto:nettojp@gmail.com">Joao Netto</a> updated the Portuguese translation
* CHANGE: <a href="http://drive2life.ru">Arkadiy Florinskiy</a> updated the Russian translation
* CHANGE: made plugin compatible to the "smugmug" plugin.
* CHANGE: moved Colorbox CSS class add to style dropdown of TinyMCE

= 3.8.3 (2010-12-24) =
* NEW: Spanish translation by <a href="http://www.redywebs.com/">Inma P.-Zubizarreta</a>
* BUGFIX: re-added the option to hide flash objects behind the colorbox overlay.

= 3.8.2 (2010-12-21) =
* BUGFIX: Last bugfix broke minor functionality.

= 3.8.1 (2010-12-21) =
* BUGFIX: Last bugfix broke the plugin, settings are now correctly read again.

= 3.8 (2010-12-21) =
* BUGFIX: Theme change is now saved again.

= 3.7 (2010-12-21) =
* NEW: Experimental feature: jQuery Colorbox can now open external websites/pictures if the link has the class "colorbox-link" and a width/height for the colorbox is set.
* BUGFIX: Plugin now works if "WP_DEBUG" is set to "true". Thx to <a href="http://iversenweb.no">Roy Iversen</a> for the bug report!
* CHANGE: rewrote auto-add JavaScript. Thx to <a href="http://wordpress.org/support/profile/jrevillini">jrevillini</a> for the help!
* NEW: Slovak translation by <a href="http://itec.sk/">Stefan Stieranka</a>
* NEW: Swedish translation by <a href="http://www.theindiaexperience.se/">Christian</a>
* CHANGE: Update of Colorbox library to version 1.3.15
* CHANGE: performance improvement: colorbox-class is only automatically added only to images that do not already have a colorbox-class.

= 3.6 (2010-09-12) =
* CHANGE: Update of Colorbox library to version 1.3.9 which fixes lots of bugs. Most notably the "0 by 0" bug in Chrome.

= 3.5 (2010-06-16) =
* NEW: Ukrainian translation by <a href="http://www.politcult.com/">Yuri Kryzhanivskyi</a>
* NEW: Italian translation by <a href="http://erkinson.altervista.org/">Erkinson</a>
* NEW: Hebrew translation by <a href="http://www.TommyGordon.co.il">Tommy Gordon</a>
* BUGFIX: URLs are now generated correctly for WP-MU installations
* NEW: Added latest donations and top donations to settings page

= 3.4 (2010-05-24) =
* NEW: Colorbox is not applied to image links that have the class "colorbox-off" any more. Useful for NextGEN users.
* NEW: Dutch translation by <a href="http://nl.linkedin.com/pub/richard-laak/b/b21/672">Richard van Laak</a>
* NEW: Malay translation by <a href="http://www.inisahaini.com">Saha-ini Ahmad Safian</a>
* CHANGE: Added CSS id "colorboxLink" to link in Meta container.
* CHANGE: <a href="http://www.photokeens.com">Modar Soos</a> updated the Arabic translation

= 3.3 (2010-05-05) =
* NEW: Belorussian translation by <a href="http://www.pc.de/">Marcis G.</a>
* NEW: Russian translation by <a href="http://drive2life.ru">Drive2Life.ru</a>
* BUGFIX: Screenshot for Theme#10 is now displayed correctly.
* NEW: Added Theme#11, a modified version of Theme#1.
* BUGFIX: Theme#7,9 and 11 will work in Internet Explorer 6 now.
* CHANGE: Minified CSS and JavaScript
* NEW: registered link to plugin page in WordPress Meta widget

= 3.2 (2010-04-20) =
* NEW: Added theme#10, thx to <a href="http://www.serhatyolacan.com/">Serhat Yolaçan</a> for all the hard work! (CSS3 rounded edges, IE does not support that)
* CHANGE: jQuery Colorbox plugin now adds necessary CSS class to all embedded images.
* CHANGE: jQuery Colorbox plugin is now compatible to <a href="http://wordpress.org/extend/plugins/nextgen-gallery/">NextGEN Gallery</a>
* CHANGE: <a href="http://www.vjucon.com/">Vedran Jurincic</a> updated the bosnian translation
* NEW: Arabic translation by <a href="http://www.photokeens.com">Modar Soos</a>

= 3.1 (2010-04-10) =
* BUGFIX: Automatic hiding of embedded flash objects under Colorbox layer now works in Internet Explorer.
* NEW: Added theme#9, a modified version of theme#4.
* NEW: French translation by <a href="http://www.tolingo.com/">Tolingo Translations</a>
* NEW: If auto colorbox is switched on, plugin now adds Colorbox functionality to every image regardless of position
* CHANGE: <a href="http://www.serhatyolacan.com/">Serhat Yolaçan</a> updated the turkish translation.

= 3.0.1 (2010-03-31) =
* BUGFIX: Settings are NOW REALLY not overridden any more every time the plugin gets activated.

= 3.0 (2010-03-31) =
* NEW: Decided to move from 2.0.1 to 3.0 because I implemented many new features
* BUGFIX: Slideshow speed setting works now.
* BUGFIX: Settings are not overridden any more every time the plugin gets activated.
* BUGFIX: jQuery Colorbox now works again for links with uppercase suffixes (IMG,JPG etc) thx to Jason Stapels (jstapels@realmprojects.com) for the bug report and fix!
* NEW: Added theme#6, a modified version of theme#1. (not compatible with IE6) thx to <a href="http://twitter.com/gervasioantonio">Gervásio Antônio</a> for all the hard work!
* NEW: Added theme#7, a modified version of theme#1. thx to <a href="http://www.vjucon.com/">Vedran Jurincic</a> for the feature request!
* NEW: Added theme#8, a modified version of theme#6.
* NEW: Added screenshots of all themes, screenshot of selected theme is shown in admin menu
* NEW: Added warning if the plugin is activated but not set to work for all images on the blog. Warning can be turned off on the settings page.
* NEW: Added setting for automatic hiding of embedded flash objects under Colorbox layer. Default: false
* NEW: Added switch for preloading of "previous" and "next" images. Default: false
* NEW: Added switch for closing of Colorbox on click on opacity layer. Default: false
* NEW: Added setting for transition type. Default: elastic
* NEW: Added setting for transition speed. Default: 250 milliseconds
* NEW: Added setting for overlay opacity. Default: 0.85
* CHANGE: Fixed fonts in Colorbox to Arial 12px
* NEW: Turkish translation by <a href="http://www.serhatyolacan.com/">Serhat Yolaçan</a>
* NEW: Portuguese translation by <a href="http://twitter.com/gervasioantonio">Gervásio Antônio</a>
* NEW: Bosnian translation by <a href="http://www.vjucon.com/">Vedran Jurincic</a>
* NEW: Plugin should be WPMU compatible now. Haven't tested myself, though. Would appreciate any feedback.
* NEW: Successfully tested jQuery Colorbox with jQuery 1.4.2
* CHANGE: Fixed "slideshow" offset from right hand side in the Colorbox of theme#4

= 2.0.1 (2010-02-11) =
* BUGFIX: slideshow does not start automatically any more if the setting is not checked on the settings page.

= 2.0 (2010-02-11) =
* NEW: Decided to move from 1.3.3 to 2.0 because I implemented many new features.
* BUGFIX: fixed relative paths for theme1 and theme4 by adding the CSS for the Internet Explorer workaround directly into the page. Thx to <a href="http://www.deepport.net/">Andrew Radke</a> for the suggestion!
* NEW: switch adding of "colorbox-postId" classes to images in posts and pages on and off through setting. Default: off.
* NEW: now works for images outside of posts (e.g. sidebar or header) if CSS class "colorbox-manual" is added manually
* NEW: jQuery Colorbox now working for WordPress attachment pages
* NEW: Added switch that adds slideshow functionality to all Colorbox groups. (no way to add slideshows individually yet)
* NEW: Added switch that adds automatic start to slideshows (no way to add slideshows individually yet)
* NEW: Added configuration of slideshow speed
* NEW: Added switch that allows the user to decide whether Colorbox scales images
* NEW: Added demos of the plugin on the <a href="http://www.techotronic.de/plugins/jquery-colorbox/">plugin page</a>
* NEW: Added configuration for adding colorbox class only to WordPress galleries
* NEW: Automatically resets settings if settings of a version prior to 1.4 are found upon activation
* NEW: width and height can now be configured as percent relative to browser window size or in pixels (default is percent)
* CHANGE: jQuery Colorbox is now only working on Image links (of type jpeg, jpg, gif, png, bmp)
* CHANGE: Improved translation. Thx to <a href="http://usability-idealist.de/">Fabian Wolf</a> for the help!
* CHANGE: updated the <a href="http://wordpress.org/extend/plugins/jquery-colorbox/faq/">FAQ</a>
* CHANGE: Updated readme.
* CHANGE: Updated descriptions and translations

= 1.3.3 (2010-01-21) =
* BUGFIX: fixed settings page, options can be saved now
* NEW: added settings deletion on uninstall and "delete settings from database" functionality to settings page
* CHANGE: moved adding of CSS class priority lower, hopefully now the CSS class is added to pictures after other plugins update the HTML
* CHANGE: updated the <a href="http://wordpress.org/extend/plugins/jquery-colorbox/faq/">FAQ</a>

= 1.3.2 (2010-01-19) =
* CHANGE: moved back to regexp replacement and implemented a workaround in the JavaScript to still allow images to be excluded by adding the class "colorbox-off"

= 1.3.1 (2010-01-18) =
* CHANGE: changed include calls for Colorbox JavaScript and CSS to version 1.3.6
* CHANGE: optimized modification of the_content

= 1.3 =
* NEW: jQuery-Colorbox won't add Colorbox functionality to images that have the CSS class "colorbox-off"
* CHANGE: Updated Colorbox version to 1.3.6
* CHANGE: should be compatible to jQuery 1.4, still using 1.3.2 at the moment because it is bundled in WordPress 2.9.1
* CHANGE: changed the way that the Colorbox CSS class is added to images to be more reliable
* CHANGE: changed layout of settings page
* CHANGE: updated the <a href="http://wordpress.org/extend/plugins/jquery-colorbox/faq/">FAQ</a>

= 1.2 =
* BUGFIX: fixes bug where colorbox was not working if linked images were used (by the theme) outside of blog posts and pages.
* NEW: adds configuration for Colorbox and picture resizing

= 1.1 =
* BUGFIX: fixes critical bug which would break rendering the blog. Sorry, was not aware that the plugin would be listed before I tagged the files as 1.0 in subversion...

= 1.0 =
* NEW: Initial release.
* NEW: Added Colorbox version 1.3.5