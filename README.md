silverstripe-rateable
=====================

An extension that adds a star rating system + UI to any DataObject type 

## Requirements

Silverstripe 3.1

## Installation

composer require sheadawson/silverstripe-rateable dev-master

Apply the Rateable DataExtension to the Objects you want to rate. ie. in mysite/_config/config.yml

	Page:
	  extensions:
	    - Rateable

Then in your templates you can use $RateableUI, when in the context of your rateable object to render the star rating UI

Run dev/build?flush=all

## Sorting objects by rating 

```php
$pages = Page::get();
$sortedPages = singleton('RateableService')->sortByRating($pages);
```

