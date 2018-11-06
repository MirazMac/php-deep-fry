# PHP Deep Fryer
A Deep Fryer written in PHP to cook some smokin' hot memes!

## Installation

```shell
composer require mirazmac/php-deep-fry
```

## Usage

First of all you need to create an instance of the fryer. The fryer expects path to a valid image file. For detailed usage check **usage** folder.

```php
require '../vendor/autoload.php';

$fryer = new MirazMac\DeepFry\Fryer('meme.jpg');

// Fry
$fryer->fry()
     // Extreme!
      ->moreDeepNibba()
    // Lower quality results in better frying
      ->quality(20)
    // Output to the browser
    ->output();
```

### Result

## ![](https://i.postimg.cc/x15MrtZY/preview.jpg)