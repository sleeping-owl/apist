## SleepingOwl Apist

[![Build Status](https://travis-ci.org/sleeping-owl/apist.svg?branch=master)](https://travis-ci.org/sleeping-owl/apist)
[![Latest Stable Version](https://poser.pugx.org/sleeping-owl/apist/v/stable.svg)](https://packagist.org/packages/sleeping-owl/apist)
[![Total Downloads](https://poser.pugx.org/sleeping-owl/apist/downloads.svg)](https://packagist.org/packages/sleeping-owl/apist)
[![License](https://poser.pugx.org/sleeping-owl/apist/license.svg)](https://packagist.org/packages/sleeping-owl/apist)
[![Code Climate](https://codeclimate.com/github/sleeping-owl/apist/badges/gpa.svg)](https://codeclimate.com/github/sleeping-owl/apist)

SleepingOwl Apist is a small library which allows you to access any site in api-like style, based on html parsing.

## Overview

This package allows you to write method like this:

```php
class WikiApi extends Apist
{

	public function getBaseUrl()
	{
		return 'http://en.wikipedia.org';
	}

	public function index()
	{
		return $this->get('/wiki/Main_Page', [
			'welcome_message'  => Apist::filter('#mp-topbanner div:first')->text()->mb_substr(0, -1),
			'portals'          => Apist::filter('a[title^="Portal:"]')->each([
				'link'  => Apist::current()->attr('href')->call(function ($href)
				{
					return $this->getBaseUrl() . $href;
				}),
				'label' => Apist::current()->text()
			]),
			'languages'        => Apist::filter('#p-lang li a[title]')->each([
				'label' => Apist::current()->text(),
				'lang'  => Apist::current()->attr('title'),
				'link'  => Apist::current()->attr('href')->call(function ($href)
				{
					return 'http:' . $href;
				})
			]),
			'sister_projects'  => Apist::filter('#mp-sister b a')->each()->text(),
			'featured_article' => Apist::filter('#mp-tfa')->html()
		]);
	}
}
```

and get the following result:

```json
{
    "welcome_message": "Welcome to Wikipedia",
    "portals": [
        {
            "link": "http:\/\/en.wikipedia.org\/wiki\/Portal:Arts",
            "label": "Arts"
        },
        {
            "link": "http:\/\/en.wikipedia.org\/wiki\/Portal:Biography",
            "label": "Biography"
        },
        ...
    ],
    "languages": [
        {
            "label": "Simple English",
            "lang": "Simple English",
            "link": "http:\/\/simple.wikipedia.org\/wiki\/"
        },
        {
            "label": "العربية",
            "lang": "Arabic",
            "link": "http:\/\/ar.wikipedia.org\/wiki\/"
        },
        {
            "label": "Bahasa Indonesia",
            "lang": "Indonesian",
            "link": "http:\/\/id.wikipedia.org\/wiki\/"
        },
        ...
    ],
    "sister_projects": [
        "Commons",
        "MediaWiki",
        ...
    ],
    "featured_article": "<div style=\"float: left; margin: 0.5em 0.9em 0.4em 0em;\">...<\/div>"
}
```

## Installation

Require this package in your composer.json and run composer update (or run `composer require sleeping-owl/apist:1.x` directly):

		"sleeping-owl/apist": "1.*"

## Documentation

Documentation can be found at [sleeping owl apist](http://sleeping-owl-apist.gopagoda.com/en/php/documentation).

## Examples

View [examples](http://sleeping-owl-apist.gopagoda.com/en/php#examples).

## Support Library

You can donate in BTC: 13k36pym383rEmsBSLyWfT3TxCQMN2Lekd

## Copyright and License

Apist was written by Sleeping Owl and is released under the MIT License. See the LICENSE file for details.