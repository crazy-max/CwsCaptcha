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