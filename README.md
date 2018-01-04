[![Latest Stable Version](https://img.shields.io/packagist/v/crazy-max/cws-captcha.svg?style=flat-square)](https://packagist.org/packages/crazy-max/cws-captcha)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%205.3.0-8892BF.svg?style=flat-square)](https://php.net/)
[![Build Status](https://img.shields.io/travis/crazy-max/CwsCaptcha/master.svg?style=flat-square)](https://travis-ci.org/crazy-max/CwsCaptcha)
[![Code Quality](https://img.shields.io/codacy/grade/82d66e708a4a43ca9416d1a7f4b34f09.svg?style=flat-square)](https://www.codacy.com/app/crazy-max/CwsCaptcha)
[![StyleCI](https://styleci.io/repos/9643298/shield?style=flat-square)](https://styleci.io/repos/9643298)
[![Gemnasium](https://img.shields.io/gemnasium/crazy-max/CwsCaptcha.svg?style=flat-square)](https://gemnasium.com/github.com/crazy-max/CwsCaptcha)
[![Donate Paypal](https://img.shields.io/badge/donate-paypal-7057ff.svg?style=flat-square)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WTZ7TL8BSSG9Y)

## About

PHP class to generate a captcha to avoid spam.

## Requirements

* PHP >= 5.3.0
* CwsDebug >= 1.8
* Enable [php_gd2](http://www.php.net/manual/en/book.image.php) extension.

## Installation with Composer

```bash
composer require crazy-max/cws-captcha
```

And download the code:

```bash
composer install # or update
```

## Getting started

See `tests/test.php`, `tests/testCaptcha.php` files samples to help you.

To create a captcha, copy/edit `testCaptcha.php` file and insert this in your HTML :

```html
<img src="testCaptcha.php" />
```

## Example

![](.res/example.png)

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

## How can i help ?

We welcome all kinds of contributions :raised_hands:!<br />
The most basic way to show your support is to star :star2: the project, or to raise issues :speech_balloon:<br />
Any funds donated will be used to help further development on this project! :gift_heart:

[![Donate Paypal](.res/paypal.png)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WTZ7TL8BSSG9Y)

## License

MIT. See `LICENSE` for more details.
