# Changelog

## 7.7 2020-08-17

* Updated to Craft 3.5
* Added a very basic GraphQL query for suggestions
* Published on GitHub on multiple requests.

## 7.6 2020-03-27

* Added Awards section
* Added crew field for films

## 7.5 2020-03-19

* Added Screenings Exporter
* Added Content Utility

## 7.4 2020-03-17

* Added Page Content Builder entry type
* Added Background Color/Background Image field types
* Added matrix mate plugin
* Reworked suggestions module, split active record into model + active record

## 7.3 2020-03-03

* Added Media-Text entry type to page section
* Even more code cleanup
* Added ide-twig.json file to provide better twig support
* Updated Craft CMS and plugins

## 7.2 2020-02-29

* No changed functionality, but some under the hood rework.
* Use custom version of abandoned element-map plugin

## 7.1 2020-02-10

* Headless App: Dropped element-api in favor of GraphQl
* Dropped dotenv in favor of defining constants in a Env Module.That should fix some issues 
with thread safety using getenv() in Windows 10/Apache/mod_php
* Added cart debug info when devMode is active 

## 7.0 2020-02-05

Shop example
 
## 7.0-beta.1 2020-02-04

Added shop (lite), ready for testing

## 6.1 2020-02-02

## 6.1-beta.1 2020-01-30

Update to Craft 3.4, ready for testing

## 6.0 2020-01-02

Thanks to Clarissa and Aylin:

* Added simple membership and watchlist functionality


## 5.0.3 2019-12-19

* Some template tweaks to demonstrate twig 'use'

## 5.0.2 2019-12-15

* Minor tweaks

## 5.0 2019-12-12

Thanks to Meri and Clarissa:

* Added a kids site as an example for site groups and different propagation methods.

## 4.1 2019-12-10

Thanks to Meri:

* Better cookie consent with different options
* Added embeds
* Removed most template caching in favor of generating static html
* Added ajax based solution for using dynamic content in cached pages
* Prepared cp styles for Craft 3.4 

## 4.0 2019-11-10

Thanks to workshop with Clarissa and Aylin:

Upgraded the starter from a simple film database to a more complex festival site.

* Added Festival Sections, Locations, Screenings
* Added a new 'featured' section to home page
* Added a second layout option for entry listings
* Added some partials for displaying complex relationships 

## 3.2.2 / 3.2.3 2019-11-06

* Fixed wrong namespaces/missing preview targets in project.yaml
* Updated snapshot

## 3.2.1 2019-11-05

* Update translate plugin to 2.0

## 3.2-beta1

Testing

## 3.1 2019-10-18

Workshop results (not properly tested yet...)

* Some (very) basic semantic markup
* Reorg of templates for better separation of layout (better reusability) and information model specific 
things
* Added 'Trivia' field
* Added pdf generation example
* Added MatrixBlockBehavior for advanced matrix block queries
* Updated Craft to 3.3.11

## 3.0 2019-10-07

* Added 'suggestions' module as a starter example for a (vaguely) comprehensive yii2 mvc app

## 2.2

* Better styling for featured entries in news
* Added some example initial migration
* Added an example cli command (clearing image transform directories)

## 2.1

* New section on home page
* Renamed all the images in snapshot
* Get rid of image transform directories in snapshot

## 2.0

Workshop results:

* Better styling (fancy colors...)
* Moved assets to assetbundle
* Enable fancyBox gallery 
* Added basic SEO stuff with seomate plugin
* Added main module with behavior, cp styles, validation examples
* Added film/person index sections
* Update Craft to 3.3.6

## 1.2

Added entry type 'link' for section 'news'

Updated Craft to 3.3.4.1

## 1.1.6

Fixes

Updated to 3.3.3


## 1.1.5

Changed image folder structure

Updated to 3.2.10

## 1.1.4 2019-08-09

Updated to Craft 3.2.9, some tweaks for live preview


## 1.1.3 2019-07-20

Updated to Craft 3.2.5.1, so dropped own workarounds. Changed headless routes to include the entry id for 
better reliability. Added some translations for headless app.

## 1.1.2 2019-07-18

Provisionally fixed some issues with headless preview. [Craft issue](https://github.com/craftcms/cms/issues/4581)
Updated _data demo data with 1.1.2.1

## 1.1.1 2019-07-15

Added guide module. Some demo content from [this repo.](https://github.com/wbrowar/craft-guide-templates)
Should be replaced when the Guide 2 plugin is out. 

## 1.1.0 2019-07-14

Updated to Craft 3.2

### Added

* Example Vue JS App

## 1.0.2.1
Fixed project.yaml (multiple linkedText blocks)
Uploaded a fixed starter db.

## 1.0.2
Updated to Craft 3.1.32.1

## 1.0.1

Updated to Craft 3.1.31
Added db and images to _data. Copy files to /web and run .\craft restore to start with a populated database.


## 1.0

Initial commit
