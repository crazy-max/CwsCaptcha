<?php

session_start();
require_once __DIR__.'/../vendor/autoload.php'; // Autoload files using Composer autoload

$cwsDebug = new Cws\CwsDebug();
$cwsDebug->setDebugVerbose();
$cwsDebug->setFileMode(__DIR__.'/cwscaptcha-debug.html', true);

$cwsCaptcha = new Cws\CwsCaptcha($cwsDebug);

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
