<?php

session_start();

// Download CwsDump at https://github.com/crazy-max/CwsDump
require_once '../CwsDump/class.cws.dump.php';
$cwsDump = new CwsDump();

// Download CwsDebug at https://github.com/crazy-max/CwsDebug
require_once '../CwsDebug/class.cws.debug.php';
$cwsDebug = new CwsDebug($cwsDump);
$cwsDebug->setDebugVerbose();
$cwsDebug->setFileMode('./cwscaptcha-debug.html', true);

require_once 'class.cws.captcha.php';
$cwsCaptcha = new CwsCaptcha($cwsDebug);

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
$cwsCaptcha->setPngFormat();

// Start!
$cwsCaptcha->process();
