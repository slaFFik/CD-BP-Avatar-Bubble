=== BuddyPress Avatar Bubble ===
Contributors: slaFFik
Tags: buddypress, members, groups, profile, ajax, avatar, admin, privacy
Requires at least: WordPress 3.2 and BuddyPress 1.5
Tested up to: WordPress 4.6.1 and BuddyPress 2.7.2
Stable tag: 2.7.1

After moving your mouse pointer on user/group avatar (or clicking) you will see a bubble with the defined by admin information about it.

== Description ==

After moving your mouse pointer on a BuddyPress user or group avatar (or clicking it) you will see a bubble with the defined by admin information about this user (or group).

Ajax calls save your bandwidth and time spent on waiting. So if you want your users can easily get information about themselves on a fly - use CD BP Avatar Bubble.

[Demo on YouTube.com](http://www.youtube.com/watch?v=cMmjt_Rpz9E "Demo video")

== Installation ==

1. Upload plugin folder `/cd-bp-avatar-bubble/` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to CD Avatar Bubble under Settings menu and make customisations as needed.

== Frequently Asked Questions ==

= Does it really use ajax for data-calls? =

Yes, it does. No need to wait until page will be loaded.

= Does plugin work with avatars only? =

Yes, I think no need to add such functionality to users links or groups avatar.

= Why doesn't this plugin work on my site? =

Are you using it on a live site? If not, there might be problems, because I didn't tested in Denwer, WAMP or LAMP. Try it live.
Try to reinstall the plugin too.

== Screenshots ==

1. Admin Page
2. On a production site

== Changelog ==

= 2.7.1 (10.11.2016) =
* Added Slovak translation, props @kypo

= 2.7 (10.11.2016) =
* Drastically simplified javascript
* Fixed several js related issues

= 2.6.1 (09.11.2016) =
* BuddyPress 2.7 support

= 2.6 (19.04.2013) =
* Full BuddyPress 1.7 support
* Fixed bubble positioning on small screens (props [Payton Swick](http://foolord.com/)

= 2.5.1 (14.12.2012) =
* WordPress 3.5 and BuddyPress 1.6.2 support

= 2.5 (03.11.2012) =
* Respect profile fields visiblility levels introduced in BuddyPress 1.6
* Improved admin area (2 columns of options for WP and 1 column for WPMS)
* Fixed some notices and several bugs

= 2.4 (27.08.2012) =
* Tested compatibility with BuddyPress 1.6.x
* Fixed some notices
* Fixed datetime field errors
* Changed the way i18n is initiated
* Fixed huge bug for WordPress MS

= 2.3.1 (28.03.2012) =
* Admin area fixes (better compatibility with WordPress Multisite)

= 2.3 (28.01.2012) =
* Fixed problems with bubble when Friends/Activity/Messages components are not activated. Degrade without errors.

= 2.2 =
* Fixed several bugs that prevented bubble to appear in some cases

= 2.1.1 =
* Added Persian translation, props [Vahid Masoomi](http://www.azuni.ir)

= 2.1 =
* Tested for fully support of BuddyPress 1.5

= 2.0.1 =
* Updated Japanese translation (thanks to [chestnut_jp](http://buddypress.org/community/members/chestnut_jp/))
* Fixed localization problem (thanks to [chestnut_jp](http://buddypress.org/community/members/chestnut_jp/))

= 2.0 =
* Adding groups avatars bubble with lots of options
* Minifying js files
* Better options page
* Improved bubble preloading display - on a proper place

= 1.2.4 =
* Updated Japanese translation (thanks to [chestnut_jp](http://buddypress.org/community/members/chestnut_jp/))
* Fixed 1 string localization problem (thanks to [chestnut_jp](http://buddypress.org/community/members/chestnut_jp/))

= 1.2.3 =
* Added ability to use not only images for corners but CSS3 border-radius and box-shadow too (looks the same as with images)

= 1.2.2 =
* Backwards compatible up to WordPress 3.1 and BuddyPress 1.2.8

= 1.2.1 =
* Fixed problem with javascript in admin area after activating v1.2
* Added Japanese translation (thanks to [chestnut_jp](http://buddypress.org/community/members/chestnut_jp/))

= 1.2 =
* Added smarter delay for hover effect (thanks to [defunctlife](http://buddypress.org/community/members/defunctlife/))
* Added some filters to use in external code

= 1.1.1 =
* Added some reset styles for bubble selectors
* Fixed rights problem in displaying bubble
* Fixed problems with mention and PM links

= 1.1 =
* Ajax refactoring - now fully supports subdirectory and subdomain WP installations
* Added ability to delay the information reveal (how many seconds user will wait until the required information will be displayed)

= 1.0 =
* Admin page rewrite - absolutely new and fresh
* Grouping fields in a table and in the bubble according their order on profile page
* Added ability to make a time delay before showing the bubble
* Added extra action to show the bubble - not only hover an avatar, but click it
* Added some hooks into the bubble to make it possible to extend information with third-party code
* Minor bug fixes
* Code optimization

= 0.9.5.1 =
* Fixed bug with displaying unnecessary line in a bubble for Add Friend button when user is not logged in

= 0.9.5 =
* Added the ability to control which values will become links, and which will not
* Added Add Friend button into the bubble (optional)
* Minor bug fixes
* Code optimization

= 0.9.1 =
* Minor bug fixes

= 0.9 =
* Added extra colors of bubble borders
* Added privacy control - you can define, who can see the bubble
* Added better bubble positioning
* Added mentions and private messages links into the bubble (optional)
* Added search by data in a bubble (values are searchable)
* Code optimization
* Thank you, Guillaume Coulon, for French translation and Luca Camellini for Italian.

= 0.8 =
* Added synced display of fields in a bubble according to the way you sorted them on Profile field setup page
* Added support for nice displaying multiselect and datebox fileds
* Fluid width of a bubble in all major browsers except IE7 (added IE7 css hack to make it 250px)
* Code optimization

= 0.7 =
* Fixed data display
* You can now choose border color of a bubble

= 0.5 =
* It was a beta that I distributed among interested via twitter updates. Follow me on [twitter](http://twitter.com/slaFFik "slaFFik on Twitter")!
