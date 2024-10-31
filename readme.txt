=== Matt's PeepSo Blog Posts ===
Contributors: jaworskimatt
Tags: peepso, social networking, community, stream, posts, post, activity, notifications, social, networks, networking, social media, network
Requires at least: 4.0
Tested up to: 4.5
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add Blog Posts tab to user profiles, featuring blog entries created by your community members.

Automatically create a new PeepSo activity stream post when a new WordPress post is published.

== Description ==

This plugin adds a configurable **Blog Posts tab** to user profiles, featuring blog entries created by your community members.

it is also capable of creating a **PeepSo activity stream item** each time a **new WordPress post** is published. This includes regular blog posts and pages, as well as CPTs (custom post types). Only regular blog posts are enabled by default.

**All post types are separately configurable** with a customizable *"action text"* and can be enabled/disabled. An activity stream item is created only once, no matter how many times you re-publish the related item.

This is a basic release, open for criticism, suggestions and bug reports :)

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. This plugin only activates if PeepSo  plugin is present and active
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the PeepSo->Config->New Post Notifications tab to configure the options

== Screenshots ==

1. Blog Posts profile tab - single column
2. Blog Posts profile tab - two columns
3. The config panel
4. An Activity Stream item

== Changelog ==

= 2.1.2 =
* Removed duplicate plugin file
* Improved PeepSoInstall compatibility

= 2.1.1 =
* Improved translations support

= 2.1.0 =
* Configurable date position
* Configurable image position
* Template overrides
* Hide some admin options if their parent option is disabled

= 2.0.0 =
* Stable

= 2.0.0-BETA4 =
* Configurable post length limit
* Set limit to 0 to disable post content

= 2.0.0-BETA3 =
* Configurable featured images: on/off, height, alignment, placeholder
* Configurable two column layout: on/off, box height, clip long content
* HTML and CSS improvements

= 2.0.0-BETA2 =
* New version open for public bet testing
* Very basic Blog Posts profile tab
* Simple layout
* Use excerpt or full content
* Use featured image

= 2.0.0-BETA1 =
* Closed beta introducing the Blog Posts profile tab
* Redesigned configuration panel with new configuration names

= 1.0.4 =
* New plugin name in preparation for 2.0.0

= 1.0.3 =
* Version bump to force sutomatic updates to renamed 1.0.4

= 1.0.2 =
* Extended the post type black list with peepso_comment and peepso_profile_field
* Added a double check for blackliste post types for backwards compatibility

= 1.0.1 =
* Removed the ability to enable peepso-post and peepso-message due to risk of infinite loop

= 1.0.0 =
* clean up unnecessary/unimplemented preference options
* stable

= 1.0.0 beta 6 =
* new version numbering scheme
* cleanup, preparing for stable

= 1.0.0 beta 5 =
* separate configuration for each available post type

= 1.0.0 beta 4 =
* fix for custom post types not firing
* removed customizable action text

= 1.0.0 beta 3 =
* configurable action text eg "wrote a new post"

= 1.0.0 beta 2 =
* fix for "class PeepSo not found"

= 1.0.0 beta 1 =
* initial release
* option to enable/disable integration
* option to define custom post types
* simple automatic post to activity