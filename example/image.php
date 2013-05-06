<?php

session_start();

include('../class.cws.captcha.php');

$cwsCaptcha = new CwsCaptcha();
$cwsCaptcha->debug_verbose = CWSCAP_VERBOSE_DEBUG;
$cwsCaptcha->process();

?>