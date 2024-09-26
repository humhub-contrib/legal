Changelog
=========

1.4.3 (Unreleased)
--------------------------
- Fix #85: Fix downloading of large user export data file
- Enh: Check User Birthday field

1.4.2 (September 13, 2024)
--------------------------
- Fix #74: Save datetime of last update of a page
- Fix #80: Remove module name translation
- Enh #83: Replace theme variables with CSS variables
- Enh #77: Added "Export your user data" feature 

1.4.1 (November 28, 2023)
-------------------------
- Enh #64: Tests for `next` version
- Fix #65: Fix hiding of the cookie consent window

1.4.0 (June 27, 2023)
---------------------
- Fix #59: Changed HumHub v1.14 deprecated methods 
- Enh #62: Show legal pages to check just after saving the configuration

1.3.3 (January 24, 2023)
------------------------
- Fix #58: LDAP login user auto registration broken

1.3.2 (November 18, 2022)
-------------------------
- Fix #57: Fix accept terms by admins

1.3.1 (November 16, 2022)
---------------------
- Enh #38: Not require registration checks on admin user creation
- Fix #56: Support for REST API requests

1.3.0 (July 19, 2022)
---------------------
- Fix #49: After accepting page, user should be redirected to the `Yii::$app->user->getReturnUrl()` URL.
- Fix #51: Improved "Back" button
- Enh #60: Optional notice for external links in Posts and Comments

1.2.4 (February 23, 2022)
------------------------
- Enh #45: Use longtext for page content

1.2.3 (February 1, 2022)
------------------------
- Fix #41: Allow file includes
- Fix #42: Fix reset confirmations with enabled cache
- Fix #187: Avoid endless loop with module "termsbox"

1.2.2 (August 26, 2021)
-----------------------
- Fix: Allow changing password if required before being able to access the platform
- Fix: French translation for 'I am older than {age} years'
- Enh #37: Update logout link to POST method

1.2.1  (April 13, 2021)
-----------------------
- Enh: Replace depreciated MarkdownField widget with new RichTextField widget
- Enh: Add an option to show legal pages in full screen after account creation ([#23](https://github.com/humhub-contrib/legal/issues/23))
- Enh: Add missing translations (automatic from Google translation)
- Fix: If age verification was disabled and an admin enable it, do not hide Humhub menu in the module configuration for admin not have age verification checked

1.1.4  (February 17, 2021)
--------------------------
- Fix: Infinite redirect with 2FA module
- Enh: Make minimum age configurable ([#10](https://github.com/humhub-contrib/legal/issues/10))
- Fix: Problem with REST API module
- Enh: Updated translations

1.1.4  (February 17, 2021)
--------------------------
- Fix: Infinite redirect with 2FA module
- Enh: Make minimum age configurable ([#10](https://github.com/humhub-contrib/legal/issues/10))
- Fix: Problem with REST API module
- Enh: Updated translations

1.1.2  (August 4, 2020)
-------------------------
- Fix #8: 1.6 controller support
- Enh: Updated translations

1.1.1  (April 20, 2020)
-------------------------
- Enh: 1.5 defer support
- Enh: Added script nonce support

1.1.0  (February 5, 2020)
-------------------------
- Enh: Updated translations
- Enh: Use updated language codes
- Enh: Increased min. Humhub Version to 1.4

1.0.9  (October 2, 2019)
------------------------
- Fix #3: Registration form checkboxes links
- Enh: Updated translations

1.0.8  (July 10, 2018)
-----------------------
- Fix: Added max-height for cookie content + button white space wrap

1.0.7  (July 10, 2018)
-----------------------
- Fix: LDAP Sync issues

1.0.6  (July 5, 2018)
-----------------------
- Fix: LDAP Sync issues

1.0.5  (June 5, 2018)
-----------------------
- Fix: Swapped registration check labels

1.0.4  (May 23, 2018)
-----------------------
- Enh: Added module screenshots

1.0.2  (May 15, 2018)
-----------------------
- Fix: Removed debug statement

1.0.1  (May 15, 2018)
-----------------------
- Fix: Event methods not method call

1.0.0  (May 15, 2018)
-----------------------
- Enh: Initial release
