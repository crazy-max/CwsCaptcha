<?php
$reload = (isset($_POST['reload'])) ? true : false;
if ($reload) {
    header($_SERVER['REQUEST_URI']);
}
?>
<!DOCTYPE html>
<html>
    <head profile="http://gmpg.org/xfn/11">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>CwsCaptcha</title>
    </head>
    <body>
        <?php
        session_start();
        require_once 'class.cws.captcha.php';
        
        $test = (isset($_POST['test'])) ? true : false;
        $code = (isset($_POST['code']) && !empty($_POST['code'])) ? stripslashes(trim($_POST['code'])) : '';
        
        if ($test) { ?>
            <div style="font-style:monospace;">
                $_SESSION["<?php echo CwsCaptcha::SESSION_VAR; ?>"] = <?php echo $_SESSION[CwsCaptcha::SESSION_VAR]; ?><br />
                Code entered : <strong><?php echo $code; ?></strong><br />
                <?php
                if (CwsCaptcha::check($code)) {
                    ?><strong>Result </strong> : <span style="color:#00CC00">OK!</span><?php
                } else {
                    ?><strong>Result </strong> : <span style="color:#CC0000">KO...</span><?php
                }
                ?>
            </div><br />
            <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <input type="submit" name="reload" value="Reload" />
            </form>
            <?php
        } else { ?>
            <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <div><img src="example-captcha.php" /></div>
                Code : <input type="text" name="code">
                <input type="submit" name="test" value="Check" />
                <input type="submit" name="reload" value="Reload" />
            </form><br />
        <?php
        } ?>
    
        <!-- Debug output -->
        <iframe frameborder="0" scrolling="no" hspace="0" marginheight="0" marginwidth="0" tabindex="-1" vspace="0"
        allowtransparency="true" style="position:static;left:0pt;top:0pt;visibility:visible;width:100%;height:1000px;border:none;"
        src="example-logs.php"></iframe>
    </body>
</html>
