<?php

/**
 * CwsCaptcha
 *
 * CwsCaptcha is a PHP class to generate a captcha to avoid spam.
 * 
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, either version 3 of the License, or (at your option)
 * any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 * 
 * Please see the GNU General Public License at http://www.gnu.org/licenses/.
 *
 * @package CwsCaptcha
 * @author Cr@zy
 * @copyright 2013, Cr@zy
 * @license GPL licensed
 * @version 1.0
 * @link https://github.com/crazy-max/CwsCaptcha
 *
 */

define('CWSCAP_FONT_FACTOR',    9);            // font size factor [0-100]
define('CWSCAP_FORMAT_PNG',     'png');
define('CWSCAP_FORMAT_JPEG',    'jpeg');
define('CWSCAP_IMAGE_FACTOR',   3);            // image size factor [1-3]
define('CWSCAP_SESSION_VAR',    'cwscaptcha'); // the session var

define('CWSCAP_VERBOSE_QUIET',  0);            // means no output at all.
define('CWSCAP_VERBOSE_SIMPLE', 1);            // means only output simple report.
define('CWSCAP_VERBOSE_REPORT', 2);            // means output a detail report.
define('CWSCAP_VERBOSE_DEBUG',  3);            // means output detail report as well as debug info.

class CwsCaptcha
{
    /**
     * CwsCaptcha version.
     * @var string
     */
    public $version = "1.0";
    
    /**
     * Captcha width in px.
     * default 250
     * @var int
     */
    public $width = 250;
    
    /**
     * Captcha height in px.
     * default 60
     * @var int
     */
    public $height = 60;
    
    /**
     * Captcha minimum length.
     * default 6
     * @var int
     */
    public $min_length = 6;
    
    /**
     * Captcha maximum length.
     * default 10
     * @var int
     */
    public $max_length = 10;
    
    /**
     * Hexadecimal foreground colors list for font letters.
     * @var array
     */
    public $fgd_colors = array(
        "#006acc", // blue
        "#00cc00", // green
        "#cc0000", // red
        "#8b28fa", // purple
        "#ff7007", // orange
    );
    
    /**
     * Max clockwise rotations for a letter.
     * default 7
     * @var int
     */
    public $max_rotation = 7;
    
    /**
     * Fonts definition (letter_space, min and max size, filename)
     * @var array
     */
    public $fonts = array(
        array(
            'letter_space'    => 1,
            'min_size'        => 14,
            'max_size'        => 20,
            'filename'        => 'BoomBox.ttf',
        ),
        array(
            'letter_space'    => 0,
            'min_size'        => 22,
            'max_size'        => 38,
            'filename'        => 'Duality.ttf',
        ),
        array(
            'letter_space'    => 1,
            'min_size'        => 28,
            'max_size'        => 32,
            'filename'        => 'Monof.ttf',
        ),
        array(
            'letter_space'    => 0,
            'min_size'        => 22,
            'max_size'        => 28,
            'filename'        => 'OrionPax.ttf',
        ),
        array(
            'letter_space'    => 0,
            'min_size'        => 26,
            'max_size'        => 34,
            'filename'        => 'Stark.ttf',
        ),
        array(
            'letter_space'    => 1.5,
            'min_size'        => 24,
            'max_size'        => 30,
            'filename'        => 'StayPuft.ttf',
        ),
        array(
            'letter_space'    => 1,
            'min_size'        => 12,
            'max_size'        => 18,
            'filename'        => 'VenusRisingRg.ttf',
        ),
        array(
            'letter_space'    => 0.5,
            'min_size'        => 22,
            'max_size'        => 30,
            'filename'        => 'WhiteRabbit.ttf',
        ),
    );
    
    /**
     * Hexadecimal background color.
     * default #ffffff
     * @var string
     */
    public $bgd_color = "#ffffff";
    
    /**
     * Set background transparent for PNG image type. If enabled, this will disable the background color.
     * default true
     * @var boolean
     */
    public $bgd_transparent = false;
    
    /**
     * Generated image period (x, y)
     * default array(11, 12)
     * @var array
    */
    public $period = array(11, 12);
    
    /**
     * Generated image amplitude (x, y)
     * default array(5, 14)
     * @var array
    */
    public $amplitude = array(5, 14);
    
    /**
     * Add blur effect using the Gaussian method.
     * default false
     * @var boolean
    */
    public $blur = false;
    
    /**
     * Add emboss effect
     * default false
     * @var boolean
     */
    public $emboss = false;
    
    /**
     * Add pixelate effect
     * default false
     * @var boolean
     */
    public $pixelate = false;
    
    /**
     * Image format
     * default CWSCAP_FORMAT_PNG
     * @var string
     */
    public $format = CWSCAP_FORMAT_PNG;
    
    /**
     * The last error message.
     * @var string
     */
    public $error_msg;
    
    /**
     * Control the debug output.
     * default CWSCAP_VERBOSE_SIMPLE
     * @var int
     */
    public $debug_verbose = CWSCAP_VERBOSE_SIMPLE;
    
    /**
     * The resource handler for the image
     * @var object
     */
    private $_handler = false;
    
    /**
     * Defines new line ending.
     * @var string
     */
    private $_newline = "<br />\n";
    
    /**
     * Class constructor
     */
    public function __construct()
    {
        if ($this->debug_verbose != CWSCAP_VERBOSE_QUIET) {
            $handle = @fopen(dirname(__FILE__) . '/cwsCaptcha.log', 'w');
            fclose($handle);
        } else {
            @unlink(dirname(__FILE__) . '/cwsCaptcha.log');
        }
    }
    
    /**
     * Log additional msg for debug to a temporary file
     * @param string $msg : if not given, log the last error msg
     * @param int $verbose_level : the log level of this message
     */
    private function log($msg=false, $verbose_level=CWSCAP_VERBOSE_SIMPLE, $newline=true, $code=false)
    {
        if ($this->debug_verbose >= $verbose_level) {
            $handle = @fopen(dirname(__FILE__) . '/cwsCaptcha.log', 'a+');
            
            if (empty($msg)) {
                fwrite($handle, 'ERROR: ' . $this->error_msg);
            } else {
                if ($code) {
                    fwrite($handle, '<textarea style="width:100%;height:300px;">');
                    fprintf($handle, $msg);
                    fwrite($handle, '</textarea>');
                } else {
                    fwrite($handle, $msg);
                }
            }
            if ($newline) {
                fwrite($handle, $this->_newline);
            }
            
            fclose($handle);
        }
    }
    
    /**
     * Start process
     */
    public function process()
    {
        $this->destroy();
        
        // create a blank image
        $this->log('<strong>Create blank image</strong> : ' . ($this->width * CWSCAP_IMAGE_FACTOR) . 'x' . ($this->height * CWSCAP_IMAGE_FACTOR), CWSCAP_VERBOSE_REPORT);
        $this->_handler = imagecreatetruecolor($this->width * CWSCAP_IMAGE_FACTOR, $this->height * CWSCAP_IMAGE_FACTOR);
        
        // background color
        if ($this->bgd_transparent) {
            $this->log('<strong>Background color</strong> : transparent', CWSCAP_VERBOSE_REPORT);
            
            // disable blending
            imagealphablending($this->_handler, false);
            
            // allocate a transparent color
            $trans_color = imagecolorallocatealpha($this->_handler, 0, 0, 0, 127);
            
            // fill the image with the transparent color
            imagefill($this->_handler, 0, 0, $trans_color);
            
            // save full alpha channel information
            imagesavealpha($this->_handler, true);
        } elseif (!empty($this->bgd_color)) {
            // allocate background color
            $rgb_bgd_color = $this->getRgbFromHex($this->bgd_color);
            $this->log('<strong>Background color</strong> : ' . implode(" ; ", $rgb_bgd_color), CWSCAP_VERBOSE_REPORT);
            $bgd_color = imagecolorallocate($this->_handler, $rgb_bgd_color[0], $rgb_bgd_color[1], $rgb_bgd_color[2]);
            imagefill($this->_handler, 0, 0, $bgd_color);
        }
        
        // allocate foreground color
        $rgb_fgd_color = $this->getRgbFromHex($this->fgd_colors[mt_rand(0, sizeof($this->fgd_colors) - 1)]); // pick a random color
        $this->log('<strong>Foreground color (letters)</strong> : ' . implode(" ; ", $rgb_fgd_color), CWSCAP_VERBOSE_REPORT);
        $fgd_color = imagecolorallocate($this->_handler, $rgb_fgd_color[0], $rgb_fgd_color[1], $rgb_fgd_color[2]);
        
        // write text on image
        $rdm_str = $this->getRandomString();
        $this->log('Captcha string : <strong>' . $rdm_str . '</strong>', CWSCAP_VERBOSE_SIMPLE);
        $this->writeText($rdm_str, $fgd_color);
        
        // add text in session
        $_SESSION[CWSCAP_SESSION_VAR] = $rdm_str;
        $this->log('Captcha string wrote in <strong>$_SESSION[\'' . CWSCAP_SESSION_VAR . '\']</strong>', CWSCAP_VERBOSE_SIMPLE);
        
        // distorted and add effects
        $this->distortImage();
        $this->addEffects();
        
        // resampled image
        $this->resampledImage();
        
        // write image
        $this->writeImage();
        
        $this->destroy();
    }
    
    /**
     * Check the captcha code entered
     * @param string $code : the captcha to verify
     * @return boolean
     */
    public static function check($code)
    {
        return isset($_SESSION[CWSCAP_SESSION_VAR]) && strtolower($_SESSION[CWSCAP_SESSION_VAR]) === strtolower($code);
    }
    
    /**
     * Display image
     */
    private function writeImage()
    {
        if ($this->format == CWSCAP_FORMAT_PNG && function_exists('imagepng')) {
            $this->writeHeaders();
            $this->log('Display image as <strong>PNG<strong>', CWSCAP_VERBOSE_SIMPLE);
            
            header("Content-type: image/png");
            if ($this->bgd_transparent) {
                imagealphablending($this->_handler, false);
                imagesavealpha($this->_handler, true);
            }
            imagepng($this->_handler);
        } else {
            $this->writeHeaders();
            $this->log('Display image as <strong>JPEG<strong>', CWSCAP_VERBOSE_SIMPLE);
            
            header("Content-type: image/jpeg");
            imagejpeg($this->_handler, null, 90);
        }
    }
    
    /**
     * Write custom headers to clear cache
     */
    private function writeHeaders()
    {
        $this->log('<strong>Write headers<strong>', CWSCAP_VERBOSE_DEBUG);
        
        // already expired
        header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
        
        // always modified
        header("Last-Modified: " . gmdate("D, d M Y H:i:s", time()) . " GMT");
        
        // HTTP/1.1
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Cache-Control: max-age=0", false);
        
        // HTTP/1.0
        header("Pragma: no-cache");
        
        // generate a unique etag each time
        header("Etag: " . microtime());
    }
    
    /**
     * Insert text
     * @param string $text : the random string
     * @param object $color : color identifier representing the color composed of the given RGB color
     */
    private function writeText($text, $color)
    {
        $this->log('<strong>Write text</strong>', CWSCAP_VERBOSE_DEBUG);
        $font = $this->fonts[array_rand($this->fonts)];
        
        $font_path = dirname(__FILE__) . '/fonts/' . $font['filename'];
        $this->log('<strong>Font</strong> : ' . $font['filename'], CWSCAP_VERBOSE_DEBUG);
        
        $font_size_factor = 1 + (($this->max_length - strlen($text)) * (CWSCAP_FONT_FACTOR / 100));
        $coord_x = 20 * CWSCAP_IMAGE_FACTOR;
        $coord_y = round(($this->height * 27 / 40) * CWSCAP_IMAGE_FACTOR);
        
        for ($i = 0; $i < strlen($text); $i++) {
            $angle = rand($this->max_rotation * -1, $this->max_rotation);
            $font_size = rand($font['min_size'], $font['max_size']) * CWSCAP_IMAGE_FACTOR * $font_size_factor;
            $letter = substr($text, $i, 1);
            
            $this->log('Letter <strong>' . $letter . '</strong> at <strong>' . $coord_x . 'x' . $coord_y . '</strong> with a fontsize of <strong>' . $font_size . 'pt</strong> and an angle of <strong>' . $angle . '°</strong>', CWSCAP_VERBOSE_DEBUG);
            
            $coords = imagettftext($this->_handler, $font_size, $angle, $coord_x, $coord_y, $color, $font_path, $letter);
            $coord_x += ($coords[2] - $coord_x) + ($font['letter_space'] * CWSCAP_IMAGE_FACTOR);
        }
    }
    
    /**
     * Reduce the image to the standard size
     */
    private function resampledImage()
    {
        $resampled = imagecreatetruecolor($this->width, $this->height);
        if ($this->bgd_transparent) {
            imagealphablending($resampled, false);
        }
        
        imagecopyresampled($resampled, $this->_handler, 0, 0, 0, 0,
            $this->width, $this->height, $this->width * CWSCAP_IMAGE_FACTOR, $this->height * CWSCAP_IMAGE_FACTOR
        );
        
        $this->log('Image resampled from <strong>' . ($this->width * CWSCAP_IMAGE_FACTOR) . 'x' . ($this->height * CWSCAP_IMAGE_FACTOR) . '</strong> to <strong>' . $this->width . 'x' . $this->height . '</strong>', CWSCAP_VERBOSE_REPORT);
        
        if ($this->bgd_transparent) {
            imagealphablending($resampled, true);
        }
        
        $this->destroy();
        $this->_handler = $resampled;
    }
    
    /**
     * Random string generation
     * @return string
     */
    private function getRandomString()
    {
        $str = '';
        $letters = 'abcdefghijklmnopqrstuvwxyz';
        $length = rand($this->min_length, $this->max_length);
        
        $last_index = strlen($letters) - 1;
        for ($i = 0; $i < $length; $i++) {
            mt_srand(hexdec(uniqid()));
            $str .= $letters[mt_rand(0, $last_index)];
        }
        
        return $str;
    }
    
    /**
     * Distort filter
     */
    private function distortImage()
    {
        $x_axis = $this->period[0] * rand(1,3) * CWSCAP_IMAGE_FACTOR;
        $y_axis = $this->period[1] * rand(1,2) * CWSCAP_IMAGE_FACTOR;
        
        // X process
        $rand = rand(0, 100);
        for ($i = 0; $i < ($this->width * CWSCAP_IMAGE_FACTOR); $i++) {
            imagecopy($this->_handler, $this->_handler,
                $i - 1, sin($rand + $i / $x_axis) * ($this->amplitude[0] * CWSCAP_IMAGE_FACTOR),
                $i, 0, 1, $this->height * CWSCAP_IMAGE_FACTOR);
        }
        
        // Y process
        $k = rand(0, 100);
        for ($i = 0; $i < ($this->height * CWSCAP_IMAGE_FACTOR); $i++) {
            imagecopy($this->_handler, $this->_handler,
                sin($rand + $i / $y_axis) * ($this->amplitude[1] * CWSCAP_IMAGE_FACTOR), $i - 1,
                0, $i, $this->width * CWSCAP_IMAGE_FACTOR, 1);
        }
    }
    
    /**
     * Add some effects to the image
     */
    private function addEffects()
    {
        // add blur effect
        if ($this->blur) {
            $this->log('<strong>Blur effect</strong> added', CWSCAP_VERBOSE_REPORT);
            imagefilter($this->_handler, IMG_FILTER_GAUSSIAN_BLUR);
        }
        
        // add emboss effect
        if ($this->emboss) {
            $this->log('<strong>Emboss effect</strong> added', CWSCAP_VERBOSE_REPORT);
            imagefilter($this->_handler, IMG_FILTER_EMBOSS);
        }
        
        // add pixelate effect
        if ($this->pixelate) {
            $this->log('<strong>Pixelate effect</strong> added', CWSCAP_VERBOSE_REPORT);
            imagefilter($this->_handler, IMG_FILTER_PIXELATE);
        }
    }
    
    /**
     * Destroy the image
     */
    private function destroy()
    {
        if (!empty($this->_handler)) {
            imagedestroy($this->_handler);
            unset($this->_handler);
        }
    }
    
    /**
     * Convert hexadecimal color to RGB type
     * @param string $hex : hexadecimal color
     * @return array
     */
    private function getRgbFromHex($hex)
    {
        $hex = str_replace("#", "", $hex);
        
        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        
        $this->log('<strong>Hex2Rgb</strong> : #' . $hex . ' => ' . implode(" ; ", array($r, $g, $b)), CWSCAP_VERBOSE_DEBUG);
        
        return array($r, $g, $b);
    }
    
    /**
     * Static method that returns the logs
     * @return string
     */
    public static function getLogs()
    {
        if (file_exists(dirname(__FILE__) . '/cwsCaptcha.log')) {
            return '<div style="font-family:monospace">' . file_get_contents(dirname(__FILE__) . '/cwsCaptcha.log') . '</div>';
        }
    }
}

?>
