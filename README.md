CwsCaptcha
==========

CwsCaptcha is a PHP class to generate a captcha to avoid spam.
A static method is available to display logs.

Installation
------------

* Enable the [php_gd2](http://www.php.net/manual/en/book.image.php) extension.
* Copy the ``class.cws.captcha.php`` file in a folder on your server.
* Go to ``example/index.php`` to see an example.

Example
-------

An example is available in ``example/index.php`` :

![](http://static.crazyws.fr/resources/blog/2013/05/cwscaptcha-example.png)


Getting started
------------

Create a blank php file called ``captcha.php`` for example in the same folder as ``class.cws.captcha.php`` and insert this :

```php
<?php

session_start();

include('../class.cws.captcha.php');

$cwsCaptcha = new CwsCaptcha();
$cwsCaptcha->debug_verbose = CWSCAP_VERBOSE_DEBUG;
$cwsCaptcha->process();

?>
```

Then add this in your HTML file :

```html
<img src="captcha.php" />
```

Options
-------

Public vars :

* **width** - Captcha width in px.
* **height** - Captcha height in px.
* **min_length** - Captcha minimum length.
* **max_length** - Captcha maximum length.
* **fgd_colors** - Array of hexadecimal foreground colors for font letters.
* **max_rotation** - Max clockwise rotations for a letter.
* **fonts** - Array list of fonts definition (letter_space, min and max size, filename).
* **bgd_color** - Hexadecimal background color.
* **bgd_transparent** - Set background transparent for PNG image type. If enabled, this will disable the background color.
* **period** - Generated image period (x, y).
* **amplitude** - Generated image amplitude (x, y).
* **blur** - Enable blur effect using the Gaussian method.
* **emboss** - Enable emboss effect.
* **pixelate** - Enable pixelate effect.
* **format** - Image format (CWSCAP_FORMAT_PNG or CWSCAP_FORMAT_JPEG).
* **error_msg** - The last error message.
* **debug_verbose** - Control the debug output.

Public methods :

* **process** - Process the captcha generation.
* **check** - Static method that checks the captcha code entered.
* **getLogs** - Static method that returns the logs.

More infos
----------

http://www.crazyws.fr/dev/classes-php/cwscaptcha-une-classe-php-de-generation-de-captcha-IS7V3.html
