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
public function live_broadcasts()
{
  return $this->get('/', [
    'title' => Apist::filter('.live_broadcast .title')->text(),
    'items' => Apist::filter('.live_broadcast .post_item')->each([
      'title' => Apist::filter('a'),
      'count' => Apist::filter('.count'),
      'link'  => Apist::filter('a')->attr('href')
    ])
  ]);
}
```

and get the following result:

```json
{
    "title": "Live broadcast",
    "items": [
        {
            "title": "Microsoft Server App-V",
            "count": "4",
            "link": "\/post\/240971#comments"
        },
        {
            "title": "Education",
            "count": "235",
            "link": "\/post\/240421#comments"
        },
        …
    ]
}
```

## Installation

Require this package in your composer.json and run composer update (or run `composer require sleeping-owl/apist:1.x` directly):

		"sleeping-owl/apist": "1.*"

## Documentation

Documentation can be found at [sleeping owl apist](http://sleeping-owl-apist.gopagoda.com).

## Examples

View [examples](http://sleeping-owl-apist.gopagoda.com/#examples).

## Support Library

You can donate in BTC: 13k36pym383rEmsBSLyWfT3TxCQMN2Lekd

## Copyright and License

Apist was written by Sleeping Owl and is released under the MIT License. See the LICENSE file for details.