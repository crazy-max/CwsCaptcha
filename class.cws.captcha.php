<?php

/**
 * CwsCaptcha
 *
 * CwsCaptcha is a PHP class to generate a captcha to avoid spam.
 * 
 * CwsCaptcha is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option)
 * or (at your option) any later version.
 *
 * CwsCaptcha is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License
 * for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see http://www.gnu.org/licenses/.
 * 
 * Related post: http://goo.gl/mMvc9
 *
 * @package CwsCaptcha
 * @author Cr@zy
 * @copyright 2013-2015, Cr@zy
 * @license GNU LESSER GENERAL PUBLIC LICENSE
 * @version 1.4
 * @link https://github.com/crazy-max/CwsCaptcha
 *
 */

class CwsCaptcha
{
    const FONT_FACTOR = 9; // font size factor [0-100]
    const IMAGE_FACTOR = 3; // image size factor [1-3]
    const SESSION_VAR = 'cwscaptcha'; // the session var
    
    const FORMAT_PNG = 'png';
    const FORMAT_JPEG = 'jpeg';
    
    /**
     * Captcha width in px.
     * @var int
     */
    private $width = 250;
    
    /**
     * Captcha height in px.
     * @var int
     */
    private $height = 60;
    
    /**
     * Captcha minimum length.
     * @var int
     */
    private $minLength = 6;
    
    /**
     * Captcha maximum length.
     * @var int
     */
    private $maxLength = 10;
    
    /**
     * Hexadecimal background color.
     * @var string
     */
    private $bgdColor = '#FFFFFF';
    
    /**
     * Set background transparent for PNG image type. If enabled, this will disable the background color.
     * @var boolean
     */
    private $bgdTransparent = false;
    
    /**
     * Hexadecimal foreground colors list for font letters.
     * @var array
     */
    private $fgdColors = array(
        '#006ACC', // blue
        '#00CC00', // green
        '#CC0000', // red
        '#8B28FA', // purple
        '#FF7007', // orange
    );
    
    /**
     * Fonts definition (letter_space, min and max size, filename)
     * @var array
     */
    private $fonts = array(
        array(
            'letter_space' => 1,
            'min_size' => 14,
            'max_size' => 20,
            'filename' => 'BoomBox.ttf',
        ),
        array(
            'letter_space' => 0,
            'min_size' => 22,
            'max_size' => 38,
            'filename' => 'Duality.ttf',
        ),
        array(
            'letter_space' => 1,
            'min_size' => 28,
            'max_size' => 32,
            'filename' => 'Monof.ttf',
        ),
        array(
            'letter_space' => 0,
            'min_size' => 22,
            'max_size' => 28,
            'filename' => 'OrionPax.ttf',
        ),
        array(
            'letter_space' => 0,
            'min_size' => 26,
            'max_size' => 34,
            'filename' => 'Stark.ttf',
        ),
        array(
            'letter_space' => 1.5,
            'min_size' => 24,
            'max_size' => 30,
            'filename' => 'StayPuft.ttf',
        ),
        array(
            'letter_space' => 1,
            'min_size' => 12,
            'max_size' => 18,
            'filename' => 'VenusRisingRg.ttf',
        ),
        array(
            'letter_space' => 0.5,
            'min_size' => 22,
            'max_size' => 30,
            'filename' => 'WhiteRabbit.ttf',
        ),
    );
    
    /**
     * Max clockwise rotations for a letter.
     * @var int
     */
    private $maxRotation = 7;
    
    /**
     * Generated image period (x, y)
     * @var array
    */
    private $period = array(11, 12);
    
    /**
     * Generated image amplitude (x, y)
     * @var array
    */
    private $amplitude = array(5, 14);
    
    /**
     * Add blur effect using the Gaussian method.
     * @var boolean
    */
    private $blur = false;
    
    /**
     * Add emboss effect
     * @var boolean
     */
    private $emboss = false;
    
    /**
     * Add pixelate effect
     * @var boolean
     */
    private $pixelate = false;
    
    /**
     * Image format
     * @var string
     */
    private $format;
    
    /**
     * The last error.
     * @var string
     */
    private $error;
    
    /**
     * The cws debug instance.
     * @var CwsDebug
     */
    private $cwsDebug;
    
    public function __construct(CwsDebug $cwsDebug)
    {
        $this->cwsDebug = $cwsDebug;
        $this->format = self::FORMAT_PNG;
    }
    
    /**
     * Start process
     */
    public function process()
    {
        $this->cwsDebug->titleH2('process');
        
        $this->destroy();
        
        // create a blank image
        $this->cwsDebug->labelValue('Create blank image', ($this->width * self::IMAGE_FACTOR) . 'x' . ($this->height * self::IMAGE_FACTOR), CwsDebug::VERBOSE_REPORT);
        $this->_handler = imagecreatetruecolor($this->width * self::IMAGE_FACTOR, $this->height * self::IMAGE_FACTOR);
        
        // background color
        if ($this->bgdTransparent) {
            $this->cwsDebug->labelValue('Background color', 'transparent', CwsDebug::VERBOSE_REPORT);
            
            // disable blending
            imagealphablending($this->_handler, false);
            
            // allocate a transparent color
            $trans_color = imagecolorallocatealpha($this->_handler, 0, 0, 0, 127);
            
            // fill the image with the transparent color
            imagefill($this->_handler, 0, 0, $trans_color);
            
            // save full alpha channel information
            imagesavealpha($this->_handler, true);
        } elseif (!empty($this->bgdColor)) {
            // allocate background color
            $rgbBgdColor = $this->getRgbFromHex($this->bgdColor);
            $this->cwsDebug->labelValue('Background color', implode(' ; ', $rgbBgdColor), CwsDebug::VERBOSE_REPORT);
            $bgdColor = imagecolorallocate($this->_handler, $rgbBgdColor[0], $rgbBgdColor[1], $rgbBgdColor[2]);
            imagefill($this->_handler, 0, 0, $bgdColor);
        }
        
        // allocate foreground color
        $rgbFgdColor = $this->getRgbFromHex($this->fgdColors[mt_rand(0, sizeof($this->fgdColors) - 1)]); // pick a random color
        $this->cwsDebug->labelValue('Foreground color (letters)', implode(' ; ', $rgbFgdColor), CwsDebug::VERBOSE_REPORT);
        $fgd_color = imagecolorallocate($this->_handler, $rgbFgdColor[0], $rgbFgdColor[1], $rgbFgdColor[2]);
        
        // write text on image
        $rdmStr = $this->getRandomString();
        $this->writeText($rdmStr, $fgd_color);
        
        // add text in session
        $_SESSION[self::SESSION_VAR] = $rdmStr;
        $this->cwsDebug->labelValue('Captcha string wrote in', '$_SESSION[\'' . self::SESSION_VAR . '\']');
        
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
        return isset($_SESSION[self::SESSION_VAR]) && strtolower($_SESSION[self::SESSION_VAR]) === strtolower($code);
    }
    
    /**
     * Display image
     */
    private function writeImage()
    {
        $this->cwsDebug->titleH3('writeImage');
        
        if ($this->format == self::FORMAT_PNG && function_exists('imagepng')) {
            $this->writeHeaders();
            $this->cwsDebug->labelValue('Display image as', 'PNG');
            
            header('Content-type: image/png');
            if ($this->bgdTransparent) {
                imagealphablending($this->_handler, false);
                imagesavealpha($this->_handler, true);
            }
            imagepng($this->_handler);
        } else {
            $this->writeHeaders();
            $this->cwsDebug->labelValue('Display image as', 'JPEG');
            
            header('Content-type: image/jpeg');
            imagejpeg($this->_handler, null, 90);
        }
    }
    
    /**
     * Write custom headers to clear cache
     */
    private function writeHeaders()
    {
        $this->cwsDebug->titleH3('writeHeaders', CwsDebug::VERBOSE_REPORT);
        
        $expires = 'Thu, 01 Jan 1970 00:00:00 GMT';
        $lastModified = gmdate('D, d M Y H:i:s', time()) . ' GMT';
        $cacheNoStore = 'no-store, no-cache, must-revalidate';
        $cachePostCheck = 'post-check=0, pre-check=0';
        $cacheMaxAge = 'max-age=0';
        $pragma = 'no-cache';
        $etag = microtime();
        
        header('Expires: ' . $expires); // already expired
        header('Last-Modified: ' . $lastModified); // always modified
        header('Cache-Control: ' . $cacheNoStore); // HTTP/1.1
        header('Cache-Control: ' . $cachePostCheck, false); // HTTP/1.1
        header('Cache-Control: ' . $cacheMaxAge, false); // HTTP/1.1
        header('Pragma: ' . $pragma); // HTTP/1.0
        header('Etag: ' . $etag); // generate a unique etag each time
        
        $this->cwsDebug->labelValue('Expires', $expires, CwsDebug::VERBOSE_REPORT);
        $this->cwsDebug->labelValue('Last-Modified', $lastModified, CwsDebug::VERBOSE_REPORT);
        $this->cwsDebug->labelValue('Cache-Control', $cacheNoStore, CwsDebug::VERBOSE_REPORT);
        $this->cwsDebug->labelValue('Cache-Control', $cachePostCheck, CwsDebug::VERBOSE_REPORT);
        $this->cwsDebug->labelValue('Cache-Control', $cacheMaxAge, CwsDebug::VERBOSE_REPORT);
        $this->cwsDebug->labelValue('Pragma', $pragma, CwsDebug::VERBOSE_REPORT);
        $this->cwsDebug->labelValue('Etag', $etag, CwsDebug::VERBOSE_REPORT);
    }
    
    /**
     * Insert text
     * @param string $text : the random string
     * @param object $color : color identifier representing the color composed of the given RGB color
     */
    private function writeText($text, $color)
    {
        $this->cwsDebug->titleH3('writeText', CwsDebug::VERBOSE_DEBUG);
        
        $font = $this->fonts[array_rand($this->fonts)];
        $fontPath = dirname(__FILE__) . '/fonts/' . $font['filename'];
        $this->cwsDebug->labelValue('Font', $font['filename'], CwsDebug::VERBOSE_DEBUG);
        
        $fontSizeFactor = 1 + (($this->maxLength - strlen($text)) * (self::FONT_FACTOR / 100));
        $coordX = 20 * self::IMAGE_FACTOR;
        $coordY = round(($this->height * 27 / 40) * self::IMAGE_FACTOR);
        
        for ($i = 0; $i < strlen($text); $i++) {
            $angle = rand($this->maxRotation * -1, $this->maxRotation);
            $fontSize = rand($font['min_size'], $font['max_size']) * self::IMAGE_FACTOR * $fontSizeFactor;
            $letter = substr($text, $i, 1);
            
            $this->cwsDebug->simple('Letter <strong>' . $letter . '</strong> at <strong>'
                . $coordX . 'x' . $coordY . '</strong> with a fontsize of <strong>'
                . $fontSize . 'pt</strong> and an angle of <strong>'
                . $angle . '°</strong>', CwsDebug::VERBOSE_DEBUG);
            
            $coords = imagettftext($this->_handler, $fontSize, $angle, $coordX, $coordY, $color, $fontPath, $letter);
            $coordX += ($coords[2] - $coordX) + ($font['letter_space'] * self::IMAGE_FACTOR);
        }
    }
    
    /**
     * Reduce the image to the standard size
     */
    private function resampledImage()
    {
        $this->cwsDebug->titleH3('resampledImage', CwsDebug::VERBOSE_REPORT);
        
        $resampled = imagecreatetruecolor($this->width, $this->height);
        if ($this->bgdTransparent) {
            imagealphablending($resampled, false);
        }
        
        imagecopyresampled($resampled, $this->_handler, 0, 0, 0, 0,
            $this->width, $this->height, $this->width * self::IMAGE_FACTOR, $this->height * self::IMAGE_FACTOR
        );
        
        $this->cwsDebug->simple('Image resampled from <strong>'
            . ($this->width * self::IMAGE_FACTOR) . 'x' . ($this->height * self::IMAGE_FACTOR) . '</strong> to <strong>'
            . $this->width . 'x' . $this->height . '</strong>', CwsDebug::VERBOSE_REPORT);
        
        if ($this->bgdTransparent) {
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
        $this->cwsDebug->titleH3('getRandomString');
        
        $str = '';
        $letters = 'abcdefghijklmnopqrstuvwxyz';
        $length = rand($this->minLength, $this->maxLength);
        
        $last_index = strlen($letters) - 1;
        for ($i = 0; $i < $length; $i++) {
            mt_srand(hexdec(uniqid()));
            $str .= $letters[mt_rand(0, $last_index)];
        }
        
        $this->cwsDebug->labelValue('Captcha string', $str);
        
        return $str;
    }
    
    /**
     * Distort filter
     */
    private function distortImage()
    {
        $xAxis = $this->period[0] * rand(1,3) * self::IMAGE_FACTOR;
        $yAxis = $this->period[1] * rand(1,2) * self::IMAGE_FACTOR;
        
        // X process
        $rand = rand(0, 100);
        for ($i = 0; $i < ($this->width * self::IMAGE_FACTOR); $i++) {
            imagecopy($this->_handler, $this->_handler,
                $i - 1, sin($rand + $i / $xAxis) * ($this->amplitude[0] * self::IMAGE_FACTOR),
                $i, 0, 1, $this->height * self::IMAGE_FACTOR);
        }
        
        // Y process
        $rand = rand(0, 100);
        for ($i = 0; $i < ($this->height * self::IMAGE_FACTOR); $i++) {
            imagecopy($this->_handler, $this->_handler,
                sin($rand + $i / $yAxis) * ($this->amplitude[1] * self::IMAGE_FACTOR), $i - 1,
                0, $i, $this->width * self::IMAGE_FACTOR, 1);
        }
    }
    
    /**
     * Add some effects to the image
     */
    private function addEffects()
    {
        $this->cwsDebug->titleH3('addEffects', CwsDebug::VERBOSE_REPORT);
        
        // add blur effect
        if ($this->blur) {
            $this->cwsDebug->simple('<strong>Blur effect</strong> added', CwsDebug::VERBOSE_REPORT);
            imagefilter($this->_handler, IMG_FILTER_GAUSSIAN_BLUR);
        }
        
        // add emboss effect
        if ($this->emboss) {
            $this->cwsDebug->simple('<strong>Emboss effect</strong> added', CwsDebug::VERBOSE_REPORT);
            imagefilter($this->_handler, IMG_FILTER_EMBOSS);
        }
        
        // add pixelate effect
        if ($this->pixelate) {
            $this->cwsDebug->simple('<strong>Pixelate effect</strong> added', CwsDebug::VERBOSE_REPORT);
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
        $this->cwsDebug->titleH3('getRgbFromHex', CwsDebug::VERBOSE_DEBUG);
        
        $hex = str_replace('#', '', $hex);
        
        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        
        $this->cwsDebug->labelValue('Hex2Rgb', '#' . $hex . ' => ' . implode(' ; ', array($r, $g, $b)), CwsDebug::VERBOSE_DEBUG);
        
        return array($r, $g, $b);
    }
    
    /**
     * Getters and setters
     */
    
    /**
     * Captcha width in px.
     * @return the $width
     */
    public function getWidth() {
        return $this->width;
    }
    
    /**
     * Set the captcha width in px.
     * default 250
     * @param int $width
     */
    public function setWidth($width) {
        $this->width = $width;
    }
    
    /**
     * Captcha height in px.
     * @return the $height
     */
    public function getHeight() {
        return $this->height;
    }
    
    /**
     * Set the captcha height in px.
     * default 60
     * @param number $height
     */
    public function setHeight($height) {
        $this->height = $height;
    }
    
    /**
     * Captcha minimum length.
     * @return the $minLength
     */
    public function getMinLength() {
        return $this->minLength;
    }
    
    /**
     * Set the captcha minimum length.
     * default 6
     * @param number $minLength
     */
    public function setMinLength($minLength) {
        $this->minLength = $minLength;
    }
    
    /**
     * Captcha maximum length.
     * @return the $maxLength
     */
    public function getMaxLength() {
        return $this->maxLength;
    }
    
    /**
     * Set the captcha maximum length.
     * default 10
     * @param number $maxLength
     */
    public function setMaxLength($maxLength) {
        $this->maxLength = $maxLength;
    }
    
    /**
     * Hexadecimal background color.
     * @return the $bgdColor
     */
    public function getBgdColor() {
        return $this->bgdColor;
    }
    
    /**
     * Set the hexadecimal background color.
     * default '#FFFFFF'
     * @param string $bgdColor
     */
    public function setBgdColor($bgdColor) {
        $this->bgdColor = $bgdColor;
    }
    
    /**
     * The background transparent for PNG image type.
     * @return the $bgdTransparent
     */
    public function getBgdTransparent() {
        return $this->bgdTransparent;
    }
    
    /**
     * Set background transparent for PNG image type.
     * If enabled, this will disable the background color.
     * default false
     * @param boolean $bgdTransparent
     */
    public function setBgdTransparent($bgdTransparent) {
        $this->bgdTransparent = $bgdTransparent;
    }
    
    /**
     * Hexadecimal foreground colors list for font letters.
     * @return the $fgdColors
     */
    public function getFgdColors() {
        return $this->fgdColors;
    }
    
    /**
     * Set the Hexadecimal foreground colors list for font letters.
     * default array('#006ACC', '#00CC00', '#CC0000', '#8B28FA', '#FF7007')
     * @param array $fgdColors
     */
    public function setFgdColors($fgdColors) {
        $this->fgdColors = $fgdColors;
    }
    
    /**
     * Fonts definition (letter_space, min and max size, filename)
     * @return the $fonts
     */
    public function getFonts() {
        return $this->fonts;
    }
    
    /**
     * Max clockwise rotations for a letter.
     * @return the $maxRotation
     */
    public function getMaxRotation() {
        return $this->maxRotation;
    }
    
    /**
     * Set the max clockwise rotations for a letter.
     * default 7
     * @param number $maxRotation
     */
    public function setMaxRotation($maxRotation) {
        $this->maxRotation = $maxRotation;
    }
    
    /**
     * Generated image period (x, y)
     * @return the $period
     */
    public function getPeriod() {
        return $this->period;
    }
    
    /**
     * Set the generated image period (x, y)
     * default array(11, 12)
     * @param array $period
     */
    public function setPeriod($period) {
        $this->period = $period;
    }
    
    /**
     * Generated image amplitude (x, y)
     * @return the $amplitude
     */
    public function getAmplitude() {
        return $this->amplitude;
    }
    
    /**
     * Set the generated image amplitude (x, y)
     * default array(5, 14)
     * @param array $amplitude
     */
    public function setAmplitude($amplitude) {
        $this->amplitude = $amplitude;
    }
    
    /**
     * The blur effect using the Gaussian method.
     * @return the $blur
     */
    public function getBlur() {
        return $this->blur;
    }
    
    /**
     * Add blur effect using the Gaussian method.
     * default false
     * @param boolean $blur
     */
    public function setBlur($blur) {
        $this->blur = $blur;
    }
    
    /**
     * The emboss effect
     * @return the $emboss
     */
    public function getEmboss() {
        return $this->emboss;
    }
    
    /**
     * Add emboss effect
     * default false
     * @param boolean $emboss
     */
    public function setEmboss($emboss) {
        $this->emboss = $emboss;
    }
    
    /**
     * The pixelate effect
     * @return the $pixelate
     */
    public function getPixelate() {
        return $this->pixelate;
    }
    
    /**
     * Add pixelate effect
     * default false
     * @param boolean $pixelate
     */
    public function setPixelate($pixelate) {
        $this->pixelate = $pixelate;
    }
    
    /**
     * Image format
     * @return the $format
     */
    public function getFormat() {
        return $this->format;
    }
    
    /**
     * Set the png image format
     */
    public function setPngFormat() {
        $this->setFormat(self::FORMAT_PNG);
    }
    
    /**
     * Set the jpeg image format
     */
    public function setJpegFormat() {
        $this->setFormat(self::FORMAT_JPEG);
    }
    
    /**
     * Set the image format
     * default FORMAT_PNG
     * @param string $format
     */
    private function setFormat($format) {
        $this->format = $format;
    }
    
    /**
     * The last error.
     * @return the $error
     */
    public function getError() {
        return $this->error;
    }
}
