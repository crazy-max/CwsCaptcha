# CwsCaptcha

CwsCaptcha is a PHP class to generate a captcha to avoid spam.

## Installation

* Enable the [php_gd2](http://www.php.net/manual/en/book.image.php) extension.
* Download and copy the [CwsDump](https://github.com/crazy-max/CwsDump) and [CwsDebug](https://github.com/crazy-max/CwsDebug) PHP classes.
* Copy the ``class.cws.captcha.php`` file in a folder on your server.
* You can use the ``index.php`` file sample to help you.

## Getting started

Create a blank php file called ``captcha.php`` and insert this :

```php
<?php

session_start();

// Download CwsDump at https://github.com/crazy-max/CwsDump
require_once '../CwsDump/class.cws.dump.php';

// Download CwsDebug at https://github.com/crazy-max/CwsDebug
require_once '../CwsDebug/class.cws.debug.php';

require_once 'class.cws.captcha.php';

$cwsCaptcha = new CwsCaptcha();
$cwsCaptcha->setDebugVerbose(CWSDEBUG_VERBOSE_DEBUG);
$cwsCaptcha->setDebugMode(CWSDEBUG_MODE_FILE, './cwscaptcha-debug.html', true);
$cwsCaptcha->setWidth(250);
$cwsCaptcha->setHeight(60);
$cwsCaptcha->setMinLength(6);
$cwsCaptcha->setMaxLength(10);
$cwsCaptcha->setBgdColor('#FFFFFF');
$cwsCaptcha->setBgdTransparent(false);
$cwsCaptcha->setFgdColors(array('#006ACC', '#00CC00', '#CC0000', '#8B28FA', '#FF7007'));
$cwsCaptcha->setMaxRotation(7);
$cwsCaptcha->setPeriod(array(11, 12));
$cwsCaptcha->setAmplitude(array(5, 14));
$cwsCaptcha->setBlur(false);
$cwsCaptcha->setEmboss(false);
$cwsCaptcha->setPixelate(false);
$cwsCaptcha->setFormat(CWSCAP_FORMAT_PNG);

// Start!
$cwsCaptcha->process();

?>
```

Then add this in your HTML file :

```html
<img src="captcha.php" />
```

## Example

An example is available in ``index.php`` :

![](http://static.crazyws.fr/resources/blog/2013/05/cwscaptcha-example.png)

## Methods

**process** - Process the captcha generation.<br />
**check** - Static method that checks the captcha code entered.<br />

**setDebugVerbose** - Set the debug verbose. (see CwsDebug class)<br />
**setDebugMode** - Set the debug mode. (see CwsDebug class)<br />
**getWidth** - Captcha width in px.<br />
**setWidth** - Set the captcha width in px.<br />
**getHeight** - Captcha height in px.<br />
**setHeight** - Set the captcha height in px.<br />
**getMinLength** - Captcha minimum length.<br />
**setMinLength** - Set the captcha minimum length.<br />
**getMaxLength** - Captcha maximum length.<br />
**setMaxLength** - Set the captcha maximum length.<br />
**getBgdColor** - Hexadecimal background color.<br />
**setBgdColor** - Set the hexadecimal background color.<br />
**getBgdTransparent** - The background transparent for PNG image type.<br />
**setBgdTransparent** - Set background transparent for PNG image type. If enabled, this will disable the background color.<br />
**getFgdColors** - Hexadecimal foreground colors list for font letters.<br />
**setFgdColors** - Set the Hexadecimal foreground colors list for font letters.<br />
**getFonts** - Fonts definition (letter_space, min and max size, filename).<br />
**getMaxRotation** - Max clockwise rotations for a letter.<br />
**setMaxRotation** - Set the max clockwise rotations for a letter.<br />
**getPeriod** - Generated image period (x, y).<br />
**setPeriod** - Set the generated image period (x, y).<br />
**getAmplitude** - Generated image amplitude (x, y).<br />
**setAmplitude** - Set the generated image amplitude (x, y).<br />
**getBlur** - The blur effect using the Gaussian method.<br />
**setBlur** - Add blur effect using the Gaussian method.<br />
**getEmboss** - The emboss effect.<br />
**setEmboss** - Add emboss effect.<br />
**getPixelate** - The pixelate effect.<br />
**setPixelate** - Add pixelate effect.<br />
**getFormat** - Image format.<br />
**setFormat** - Set the image format (CWSCAP_FORMAT_PNG or CWSCAP_FORMAT_JPEG).<br />
**getError** - The last error.<br />

## License

LGPL. See ``LICENSE`` for more details.

## More infos

http://www.crazyws.fr/dev/classes-php/cwscaptcha-une-classe-php-de-generation-de-captcha-IS7V3.html
