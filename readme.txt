=== CP Image Gallery ===
Contributors: Commercepundit
Tags: image gallery, image, galleries, simple, commercepundit, gallery list, popup on gallery images
Requires at least: 3.5
Tested up to: 4.8.3
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easy way to add gallery with images using run time resizer feature as well as with SEO frindly gallery url and display all galleries at one place. 

== Description ==

Most of the plugins of gallery provide you facility for adding gallery but do not provide SEO friendly gallery URL. This plugin provides you extra SEO friendly features with adding gallery with lightbox on gallery images. "CP Image Gallery" add suffix (photos) behide all the gallery link for providing SEO frindly gallery url to make it unique.  This Plugin let you add gallery with images from backend. And also provide feature for displaying all gallery at one place(Using Shortcode). This Plugin allows you to add lightbox on gallery images. "CP Image Gallery" also provides feature for adding SEO meta value for each gallery and also for diffrent post type. It provides pagination feature for gallery images. You can also resize gallery images from bacend feature. This Plugin provides run time image resize feature.

This plugin allows you to easily create an image gallery on any post, page or custom post type. Images of the gallery are can be added and previewed from the metabox. Images of gallery can be re-ordered by drag and drop.

Features:

1. Add custom post type to add "Gallery" from admin area.
2. Drag and drop re-ordering
3. Add gallery to any post, page or custom post type
4. Add pagination for gallery images
5. Setting for adding number of images per gallery.
6. Setting to turn on or off SEO support for adding meta keywords, meta description as well as meta robots
7. If more than one image is added to the gallery, the images become grouped in the lightbox so you can easily view the next one
8. CSS and JS are only loaded on pages where needed
9. Images link to Popup(Lightbox) (can be turned off)
10. Fully Localized (translation ready) with .mo and .po files
11. Add multiple images to the gallery at once
12. Support for fancyBox and prettyPhoto
13. Uses the new WP 3.5+ media manager for a familiar and intuitive way to add your images
14. Uses Run time image resizer to resize gallery images.

= Usage =

Galleries are automatically appended to the bottom of your post/page unless you use the shortcode below. Using the shortcode will give you finer control over placement within the content area. Plugin settings are located under Settings -> Media

= Shortcode Usage =

Use the following shortcode anywhere in the content area to display the all gallery list

    [display_cpgallery]

= Template Tag Usage =

The following template tag is available to display all galleries at one place.

    if( function_exists( 'cp_image_gallery_list_page' ) ) {
	    echo cp_image_gallery_list_page();
    }

If you use the template tag above, you will need remove the default content filter:

    remove_filter( 'the_content', 'cp_gallery_image_append_content' );

= Developer Friendly =

1. Modify the gallery HTML using filters
2. Developed with WP Coding Standards
3. Easily add your preferred lightbox script via hooks and filters
4. Easily unhook CSS and add your own styling
5. Pass in a different image size for the thumbnails via filter
6. Modify pagination query string in url.
7. Modify slug from the gallery link url.

== Installation ==

1. Upload the entire `cp-image-gallery` folder to the `/wp-content/plugins/` directory, or just upload the ZIP package via 'Plugins > Add New > Upload' in your WP Admin
2. Activate Easy Image Gallery from the 'Plugins' page in WordPress
3. Configure the plugin's settings from Settings -> Media
4. Click on "Add New" of the CP Gallery option from the left sidebar in admin area.

== Screenshots ==

1. This plugin's simple configuration screen. (Screenshot-1)
2. Show steps to add gallery and gallery images from backend. (Screenshot-2)
3. WordPress latest media manager is launched when you click "Add gallery images". You can select multiple images to insert into the gallery (Screenshot-3)
4. The plugin's Image Gallery metabox after images have been inserted. (Screenshot-4)
5. The front-end of the website showing the list of galleries which has been automatically appended to the content (Screenshot-5)
6. Clicking on an image launches the lightbox. Here it's shown with prettyPhoto (Screenshot-6)

== Frequently Asked Questions ==

= Where are the plugin's settings? =

In your WordPress admin under Settings -> Media

== Changelog ==

= 1.0.0 =

* Initial release
