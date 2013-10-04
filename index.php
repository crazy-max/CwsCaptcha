<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></meta>
<title>CwsCaptcha</title>
</head>
<body>
    <?php
    session_start();
    
    // Download CwsDump at https://github.com/crazy-max/CwsDump
	require_once '../CwsDump/class.cws.dump.php';

	// Download CwsDebug at https://github.com/crazy-max/CwsDebug
	require_once '../CwsDebug/class.cws.debug.php';
	
    require_once 'class.cws.captcha.php';
    
    $test = (isset($_POST['test'])) ? true : false;
    $code = (isset($_POST['code']) && !empty($_POST['code'])) ? stripslashes(trim($_POST['code'])) : '';
    
    if ($test) {
        ?>
        <div style="font-style:monospace;">
            $_SESSION["<?php echo CWSCAP_SESSION_VAR; ?>"] = <?php echo $_SESSION[CWSCAP_SESSION_VAR]; ?><br />
            Code entered : <strong><?php echo $code; ?></strong><br />
            <?php
            if (CwsCaptcha::check($code)) {
                echo '<strong>Result </strong> : <span style="color:#00CC00">OK!</span>';
            } else {
                echo '<strong>Result </strong> : <span style="color:#CC0000">KO...</span>';
            }
            ?>
        </div><br /><a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Reload</a><br /><br />
        <?php
    } else {
        ?>
        <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
            <div><img src="image.php" /></div>
            Code : <input type="text" name="code">
            <input type="submit" name="test" value="Check" />
        </form><br />
    <?php
    }
    ?>
    
    <!-- Debug output -->
    <iframe frameborder="0" scrolling="no" hspace="0" marginheight="0" marginwidth="0" tabindex="-1" vspace="0"
    allowtransparency="true" style="position:static;left:0pt;top:0pt;visibility:visible;width:100%;height:1000px;border:none;"
    src="logs.php"></iframe>
</body>
</html>