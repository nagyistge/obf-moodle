OBF Displayer Moodle plugin
=================

This plugin displays user's Open Badges in a Moodle block.

Supported services
------------------

- [Open Badge Factory](https://openbadgefactory.com/) (Badges issued by your system via OBF)
- [Open Badge Passport](https://openbadgepassport.com/)
- [CanCred Passport](https://passport.cancred.ca/)
- [Salava](http://salava.org)
- [Mozilla Backpack](https://backpack.openbadges.org)
- Moodle's internal badges

For developers
--------

[See the project README](../../../README.md)

How to install, and set up the block on profile pages
--------------

Moodle 2.5 and up:

1. Install the zip via Moodle's plugin page. Select "block" as the type of the plugin.
2. Update the database using the notifications page
3. Navigate to "Site home" on your Moodle site
4. Turn editing on
5. Add a block called "Open Badge Factory"
6. Configure the block (from the gear icon on the block), and set page context to "Display throughout the entire site"
7. Go to you profile page
8. Configure the block again, and now set the "Display on page types" option to "Only user profile pages"
   (Optionally position it where you want it, by setting the default region)

(Steps 6-8 are required to ensure the block is visible in every users profile page)
