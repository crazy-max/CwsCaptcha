# CwsCaptcha

CwsCaptcha is a PHP class to generate a captcha to avoid spam.

## Installation

* Enable the [php_gd2](http://www.php.net/manual/en/book.image.php) extension.
* Download [CwsDump](https://github.com/crazy-max/CwsDump) and [CwsDebug](https://github.com/crazy-max/CwsDebug).
* Copy the ``class.cws.captcha.php`` file in a folder on your server.

## Getting started

See ``example.php``, ``example-captcha.php`` files samples to help you.

To create a captcha, copy/edit ``example-captcha.php`` file and insert this in your HTML :

```html
<img src="example-captcha.php" />
```

## Example

![](https://raw.github.com/crazy-max/CwsCaptcha/master/example.png)

## Methods

**process** - Process the captcha generation.<br />
**check** - Static method that checks the captcha code entered.<br />

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
**setPngFormat** - Set the png image format. (default)<br />
**setJpegFormat** - Set the jpeg image format.<br />
**getError** - The last error.<br />

## License

LGPL. See ``LICENSE`` for more details.

## More infos

http://www.crazyws.fr/dev/classes-php/cwscaptcha-une-classe-php-de-generation-de-captcha-IS7V3.html
