[![Latest Stable Version](https://poser.pugx.org/alphazygma/usps-zonechart-php/v/stable)](https://packagist.org/packages/alphazygma/usps-zonechart-php)
[![Build Status](https://travis-ci.org/alphazygma/usps-zonechart-php.svg?branch=master)](https://travis-ci.org/alphazygma/usps-zonechart-php)
[![Coverage Status](https://coveralls.io/repos/github/alphazygma/usps-zonechart-php/badge.svg?branch=master)](https://coveralls.io/github/alphazygma/usps-zonechart-php?branch=master)
[![Total Downloads](https://poser.pugx.org/alphazygma/usps-zonechart-php/downloads)](https://packagist.org/packages/alphazygma/usps-zonechart-php)
[![Latest Unstable Version](https://poser.pugx.org/alphazygma/usps-zonechart-php/v/unstable)](https://packagist.org/packages/alphazygma/usps-zonechart-php)
[![License](https://poser.pugx.org/alphazygma/usps-zonechart-php/license)](https://packagist.org/packages/alphazygma/usps-zonechart-php)



# USPS ZoneChart (for PHP)
Provide zone identification between Source and Target Destination

## Requirements

PHP 5.5+ is required. (_The `[]` short array syntax was introduced on [5.4](http://php.net/manual/en/migration54.new-features.php>), and GuzzleHttp 6 is not compatible with PHP 5.4_)


## Disclaimer
The ConfigGenerator and the helper tool to run the generator does not have unittests, however, since these are not intended for library use, but more for updating the JSON configuration files if USPS updates their charts because new ZipCodes are added to the US.

As such, these files are not included for test coverage purposes.


## Changelog
 - 1.0 Retrieve a Zone for a given source/destination ZipCodes



## Usage

_This usage considers that you have an autoloader running_. (see [Install](#Install) for more reference)

```
<?php
$sourceZip1 = '94040'; // Mountain View, CA
$sourceZip2 = '40342'; // Lawrenceburg, KY
$destinationZip = '94118'; // San Francisco, CA

// Same or close zone example (CA -> CA)
$zoneChart = new \Shipping\ZoneChart\ZoneChart($sourceZip1);
$zoneA     = $zoneChart->getZoneFor($destinationZip);
// $zoneA would have 1 or 2 for example

// different zone example (KY -> CA)
$zoneChart = new \Shipping\ZoneChart\ZoneChart($sourceZip2);
$zoneB     = $zoneChart->getZoneFor($destinationZip);
// $zoneB would have 4 or 5 for example
```


## Install

The easiest way to install is through [composer](http://getcomposer.org).

Just create a composer.json file for your project:

```JSON
{
    "require": {
        "alphazygma/usps-zonechart-php": "~1.0"
    }
}
```

Then you can run these two commands to install it:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar install

or simply run `composer install` if you have have already [installed the composer globally](http://getcomposer.org/doc/00-intro.md#globally).

Then you can include the autoloader, and you will have access to the library classes:

```php
<?php
require 'vendor/autoload.php';
```
