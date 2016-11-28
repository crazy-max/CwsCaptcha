<?php

/**
 * CwsCaptcha.
 *
 * @author Cr@zy
 * @copyright 2013-2016, Cr@zy
 * @license GNU LESSER GENERAL PUBLIC LICENSE
 *
 * @link https://github.com/crazy-max/CwsCaptcha
 */
namespace Cws;

class CwsCaptcha
{
    const FONT_FACTOR = 9; // font size factor [0-100]
    const IMAGE_FACTOR = 3; // image size factor [1-3]
    const SESSION_VAR = 'cwscaptcha'; // the session var

    const FORMAT_PNG = 'png';
    const FORMAT_JPEG = 'jpeg';

    /**
     * Captcha width in px.
     *
     * @var int
     */
    private $width = 250;

    /**
     * Captcha height in px.
     *
     * @var int
     */
    private $height = 60;

    /**
     * Captcha minimum length.
     *
     * @var int
     */
    private $minLength = 6;

    /**
     * Captcha maximum length.
     *
     * @var int
     */
    private $maxLength = 10;

    /**
     * Hexadecimal background color.
     *
     * @var string
     */
    private $bgdColor = '#FFFFFF';

    /**
     * Set background transparent for PNG image type. If enabled, this will disable the background color.
     *
     * @var bool
     */
    private $bgdTransparent = false;

    /**
     * Hexadecimal foreground colors list for font letters.
     *
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
     * Fonts definition (letter_space, min and max size, filename).
     *
     * @var array
     */
    private $fonts = array(
        array(
            'letter_space' => 1,
            'min_size'     => 14,
            'max_size'     => 20,
            'filename'     => 'BoomBox.ttf',
        ),
        array(
            'letter_space' => 0,
            'min_size'     => 22,
            'max_size'     => 38,
            'filename'     => 'Duality.ttf',
        ),
        array(
            'letter_space' => 1,
            'min_size'     => 28,
            'max_size'     => 32,
            'filename'     => 'Monof.ttf',
        ),
        array(
            'letter_space' => 0,
            'min_size'     => 22,
            'max_size'     => 28,
            'filename'     => 'OrionPax.ttf',
        ),
        array(
            'letter_space' => 0,
            'min_size'     => 26,
            'max_size'     => 34,
            'filename'     => 'Stark.ttf',
        ),
        array(
            'letter_space' => 1.5,
            'min_size'     => 24,
            'max_size'     => 30,
            'filename'     => 'StayPuft.ttf',
        ),
        array(
            'letter_space' => 1,
            'min_size'     => 12,
            'max_size'     => 18,
            'filename'     => 'VenusRisingRg.ttf',
        ),
        array(
            'letter_space' => 0.5,
            'min_size'     => 22,
            'max_size'     => 30,
            'filename'     => 'WhiteRabbit.ttf',
        ),
    );

    /**
     * Max clockwise rotations for a letter.
     *
     * @var int
     */
    private $maxRotation = 7;

    /**
     * Generated image period (x, y).
     *
     * @var array
     */
    private $period = array(11, 12);

    /**
     * Generated image amplitude (x, y).
     *
     * @var array
     */
    private $amplitude = array(5, 14);

    /**
     * Add blur effect using the Gaussian method.
     *
     * @var bool
     */
    private $blur = false;

    /**
     * Add emboss effect.
     *
     * @var bool
     */
    private $emboss = false;

    /**
     * Add pixelate effect.
     *
     * @var bool
     */
    private $pixelate = false;

    /**
     * Image format.
     *
     * @var string
     */
    private $format;

    /**
     * The last error.
     *
     * @var string
     */
    private $error;

    /**
     * The image handler.
     *
     * @var resource
     */
    private $handler;

    /**
     * The cws debug instance.
     *
     * @var CwsDebug
     */
    private $cwsDebug;

    public function __construct(CwsDebug $cwsDebug)
    {
        $this->cwsDebug = $cwsDebug;
        $this->format = self::FORMAT_PNG;
    }

    /**
     * Start process.
     */
    public function process()
    {
        $this->cwsDebug->titleH2('process');

        $this->destroy();

        // create a blank image
        $this->cwsDebug->labelValue('Create blank image', ($this->width * self::IMAGE_FACTOR).'x'.($this->height * self::IMAGE_FACTOR), CwsDebug::VERBOSE_REPORT);
        $this->handler = imagecreatetruecolor($this->width * self::IMAGE_FACTOR, $this->height * self::IMAGE_FACTOR);

        // background color
        if ($this->bgdTransparent) {
            $this->cwsDebug->labelValue('Background color', 'transparent', CwsDebug::VERBOSE_REPORT);

            // disable blending
            imagealphablending($this->handler, false);

            // allocate a transparent color
            $trans_color = imagecolorallocatealpha($this->handler, 0, 0, 0, 127);

            // fill the image with the transparent color
            imagefill($this->handler, 0, 0, $trans_color);

            // save full alpha channel information
            imagesavealpha($this->handler, true);
        } elseif (!empty($this->bgdColor)) {
            // allocate background color
            $rgbBgdColor = $this->getRgbFromHex($this->bgdColor);
            $this->cwsDebug->labelValue('Background color', implode(' ; ', $rgbBgdColor), CwsDebug::VERBOSE_REPORT);
            $bgdColor = imagecolorallocate($this->handler, $rgbBgdColor[0], $rgbBgdColor[1], $rgbBgdColor[2]);
            imagefill($this->handler, 0, 0, $bgdColor);
        }

        // allocate foreground color
        $rgbFgdColor = $this->getRgbFromHex($this->fgdColors[mt_rand(0, count($this->fgdColors) - 1)]); // pick a random color
        $this->cwsDebug->labelValue('Foreground color (letters)', implode(' ; ', $rgbFgdColor), CwsDebug::VERBOSE_REPORT);
        $fgd_color = imagecolorallocate($this->handler, $rgbFgdColor[0], $rgbFgdColor[1], $rgbFgdColor[2]);

        // write text on image
        $rdmStr = $this->getRandomString();
        $this->writeText($rdmStr, $fgd_color);

        // add text in session
        $_SESSION[self::SESSION_VAR] = $rdmStr;
        $this->cwsDebug->labelValue('Captcha string wrote in', '$_SESSION[\''.self::SESSION_VAR.'\']');

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
     * Check the captcha code entered.
     *
     * @param string $code : the captcha to verify
     *
     * @return bool
     */
    public static function check($code)
    {
        return isset($_SESSION[self::SESSION_VAR]) && strtolower($_SESSION[self::SESSION_VAR]) === strtolower($code);
    }

    /**
     * Display image.
     */
    private function writeImage()
    {
        $this->cwsDebug->titleH3('writeImage');

        if ($this->format == self::FORMAT_PNG && function_exists('imagepng')) {
            $this->writeHeaders();
            $this->cwsDebug->labelValue('Display image as', 'PNG');

            header('Content-type: image/png');
            if ($this->bgdTransparent) {
                imagealphablending($this->handler, false);
                imagesavealpha($this->handler, true);
            }
            imagepng($this->handler);
        } else {
            $this->writeHeaders();
            $this->cwsDebug->labelValue('Display image as', 'JPEG');

            header('Content-type: image/jpeg');
            imagejpeg($this->handler, null, 90);
        }
    }

    /**
     * Write custom headers to clear cache.
     */
    private function writeHeaders()
    {
        $this->cwsDebug->titleH3('writeHeaders', CwsDebug::VERBOSE_REPORT);

        $expires = 'Thu, 01 Jan 1970 00:00:00 GMT';
        $lastModified = gmdate('D, d M Y H:i:s', time()).' GMT';
        $cacheNoStore = 'no-store, no-cache, must-revalidate';
        $cachePostCheck = 'post-check=0, pre-check=0';
        $cacheMaxAge = 'max-age=0';
        $pragma = 'no-cache';
        $etag = microtime();

        header('Expires: '.$expires); // already expired
        header('Last-Modified: '.$lastModified); // always modified
        header('Cache-Control: '.$cacheNoStore); // HTTP/1.1
        header('Cache-Control: '.$cachePostCheck, false); // HTTP/1.1
        header('Cache-Control: '.$cacheMaxAge, false); // HTTP/1.1
        header('Pragma: '.$pragma); // HTTP/1.0
        header('Etag: '.$etag); // generate a unique etag each time

        $this->cwsDebug->labelValue('Expires', $expires, CwsDebug::VERBOSE_REPORT);
        $this->cwsDebug->labelValue('Last-Modified', $lastModified, CwsDebug::VERBOSE_REPORT);
        $this->cwsDebug->labelValue('Cache-Control', $cacheNoStore, CwsDebug::VERBOSE_REPORT);
        $this->cwsDebug->labelValue('Cache-Control', $cachePostCheck, CwsDebug::VERBOSE_REPORT);
        $this->cwsDebug->labelValue('Cache-Control', $cacheMaxAge, CwsDebug::VERBOSE_REPORT);
        $this->cwsDebug->labelValue('Pragma', $pragma, CwsDebug::VERBOSE_REPORT);
        $this->cwsDebug->labelValue('Etag', $etag, CwsDebug::VERBOSE_REPORT);
    }

    /**
     * Insert text.
     *
     * @param string $text  : the random string
     * @param int    $color : color identifier representing the color composed of the given RGB color
     */
    private function writeText($text, $color)
    {
        $this->cwsDebug->titleH3('writeText', CwsDebug::VERBOSE_DEBUG);

        $font = $this->fonts[array_rand($this->fonts)];
        $fontPath = dirname(__FILE__).'/Resources/public/fonts/'.$font['filename'];
        $this->cwsDebug->labelValue('Font', $font['filename'], CwsDebug::VERBOSE_DEBUG);

        $fontSizeFactor = 1 + (($this->maxLength - strlen($text)) * (self::FONT_FACTOR / 100));
        $coordX = 20 * self::IMAGE_FACTOR;
        $coordY = round(($this->height * 27 / 40) * self::IMAGE_FACTOR);

        for ($i = 0; $i < strlen($text); $i++) {
            $angle = rand($this->maxRotation * -1, $this->maxRotation);
            $fontSize = rand($font['min_size'], $font['max_size']) * self::IMAGE_FACTOR * $fontSizeFactor;
            $letter = substr($text, $i, 1);

            $this->cwsDebug->simple('Letter <strong>'.$letter.'</strong> at <strong>'
                .$coordX.'x'.$coordY.'</strong> with a fontsize of <strong>'
                .$fontSize.'pt</strong> and an angle of <strong>'
                .$angle.'°</strong>', CwsDebug::VERBOSE_DEBUG);

            $coords = imagettftext($this->handler, $fontSize, $angle, $coordX, $coordY, $color, $fontPath, $letter);
            $coordX += ($coords[2] - $coordX) + ($font['letter_space'] * self::IMAGE_FACTOR);
        }
    }

    /**
     * Reduce the image to the standard size.
     */
    private function resampledImage()
    {
        $this->cwsDebug->titleH3('resampledImage', CwsDebug::VERBOSE_REPORT);

        $resampled = imagecreatetruecolor($this->width, $this->height);
        if ($this->bgdTransparent) {
            imagealphablending($resampled, false);
        }

        imagecopyresampled($resampled, $this->handler, 0, 0, 0, 0,
            $this->width, $this->height, $this->width * self::IMAGE_FACTOR, $this->height * self::IMAGE_FACTOR
        );

        $this->cwsDebug->simple('Image resampled from <strong>'
            .($this->width * self::IMAGE_FACTOR).'x'.($this->height * self::IMAGE_FACTOR).'</strong> to <strong>'
            .$this->width.'x'.$this->height.'</strong>', CwsDebug::VERBOSE_REPORT);

        if ($this->bgdTransparent) {
            imagealphablending($resampled, true);
        }

        $this->destroy();
        $this->handler = $resampled;
    }

    /**
     * Random string generation.
     *
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
     * Distort filter.
     */
    private function distortImage()
    {
        $xAxis = $this->period[0] * rand(1, 3) * self::IMAGE_FACTOR;
        $yAxis = $this->period[1] * rand(1, 2) * self::IMAGE_FACTOR;

        // X process
        $rand = rand(0, 100);
        for ($i = 0; $i < ($this->width * self::IMAGE_FACTOR); $i++) {
            imagecopy($this->handler, $this->handler,
                $i - 1, sin($rand + $i / $xAxis) * ($this->amplitude[0] * self::IMAGE_FACTOR),
                $i, 0, 1, $this->height * self::IMAGE_FACTOR);
        }

        // Y process
        $rand = rand(0, 100);
        for ($i = 0; $i < ($this->height * self::IMAGE_FACTOR); $i++) {
            imagecopy($this->handler, $this->handler,
                sin($rand + $i / $yAxis) * ($this->amplitude[1] * self::IMAGE_FACTOR), $i - 1,
                0, $i, $this->width * self::IMAGE_FACTOR, 1);
        }
    }

    /**
     * Add some effects to the image.
     */
    private function addEffects()
    {
        $this->cwsDebug->titleH3('addEffects', CwsDebug::VERBOSE_REPORT);

        // add blur effect
        if ($this->blur) {
            $this->cwsDebug->simple('<strong>Blur effect</strong> added', CwsDebug::VERBOSE_REPORT);
            imagefilter($this->handler, IMG_FILTER_GAUSSIAN_BLUR);
        }

        // add emboss effect
        if ($this->emboss) {
            $this->cwsDebug->simple('<strong>Emboss effect</strong> added', CwsDebug::VERBOSE_REPORT);
            imagefilter($this->handler, IMG_FILTER_EMBOSS);
        }

        // add pixelate effect
        if ($this->pixelate) {
            $this->cwsDebug->simple('<strong>Pixelate effect</strong> added', CwsDebug::VERBOSE_REPORT);
            imagefilter($this->handler, IMG_FILTER_PIXELATE);
        }
    }

    /**
     * Destroy the image.
     */
    private function destroy()
    {
        if (!empty($this->handler)) {
            imagedestroy($this->handler);
            unset($this->handler);
        }
    }

    /**
     * Convert hexadecimal color to RGB type.
     *
     * @param string $hex : hexadecimal color
     *
     * @return array
     */
    private function getRgbFromHex($hex)
    {
        $this->cwsDebug->titleH3('getRgbFromHex', CwsDebug::VERBOSE_DEBUG);

        $hex = str_replace('#', '', $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1).substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1).substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1).substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        $this->cwsDebug->labelValue('Hex2Rgb', '#'.$hex.' => '.implode(' ; ', array($r, $g, $b)), CwsDebug::VERBOSE_DEBUG);

        return array($r, $g, $b);
    }

    /**
     * Getters and setters.
     */

    /**
     * Captcha width in px.
     *
     * @return int $width
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set the captcha width in px.
     * default 250.
     *
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * Captcha height in px.
     *
     * @return int $height
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set the captcha height in px.
     * default 60.
     *
     * @param number $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * Captcha minimum length.
     *
     * @return int $minLength
     */
    public function getMinLength()
    {
        return $this->minLength;
    }

    /**
     * Set the captcha minimum length.
     * default 6.
     *
     * @param number $minLength
     */
    public function setMinLength($minLength)
    {
        $this->minLength = $minLength;
    }

    /**
     * Captcha maximum length.
     *
     * @return int $maxLength
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * Set the captcha maximum length.
     * default 10.
     *
     * @param number $maxLength
     */
    public function setMaxLength($maxLength)
    {
        $this->maxLength = $maxLength;
    }

    /**
     * Hexadecimal background color.
     *
     * @return string $bgdColor
     */
    public function getBgdColor()
    {
        return $this->bgdColor;
    }

    /**
     * Set the hexadecimal background color.
     * default '#FFFFFF'.
     *
     * @param string $bgdColor
     */
    public function setBgdColor($bgdColor)
    {
        $this->bgdColor = $bgdColor;
    }

    /**
     * The background transparent for PNG image type.
     *
     * @return string $bgdTransparent
     */
    public function getBgdTransparent()
    {
        return $this->bgdTransparent;
    }

    /**
     * Set background transparent for PNG image type.
     * If enabled, this will disable the background color.
     * default false.
     *
     * @param bool $bgdTransparent
     */
    public function setBgdTransparent($bgdTransparent)
    {
        $this->bgdTransparent = $bgdTransparent;
    }

    /**
     * Hexadecimal foreground colors list for font letters.
     *
     * @return string $fgdColors
     */
    public function getFgdColors()
    {
        return $this->fgdColors;
    }

    /**
     * Set the Hexadecimal foreground colors list for font letters.
     * default array('#006ACC', '#00CC00', '#CC0000', '#8B28FA', '#FF7007').
     *
     * @param array $fgdColors
     */
    public function setFgdColors($fgdColors)
    {
        $this->fgdColors = $fgdColors;
    }

    /**
     * Fonts definition (letter_space, min and max size, filename).
     *
     * @return string $fonts
     */
    public function getFonts()
    {
        return $this->fonts;
    }

    /**
     * Max clockwise rotations for a letter.
     *
     * @return string $maxRotation
     */
    public function getMaxRotation()
    {
        return $this->maxRotation;
    }

    /**
     * Set the max clockwise rotations for a letter.
     * default 7.
     *
     * @param number $maxRotation
     */
    public function setMaxRotation($maxRotation)
    {
        $this->maxRotation = $maxRotation;
    }

    /**
     * Generated image period (x, y).
     *
     * @return string $period
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Set the generated image period (x, y)
     * default array(11, 12).
     *
     * @param array $period
     */
    public function setPeriod($period)
    {
        $this->period = $period;
    }

    /**
     * Generated image amplitude (x, y).
     *
     * @return string $amplitude
     */
    public function getAmplitude()
    {
        return $this->amplitude;
    }

    /**
     * Set the generated image amplitude (x, y)
     * default array(5, 14).
     *
     * @param array $amplitude
     */
    public function setAmplitude($amplitude)
    {
        $this->amplitude = $amplitude;
    }

    /**
     * The blur effect using the Gaussian method.
     *
     * @return string $blur
     */
    public function getBlur()
    {
        return $this->blur;
    }

    /**
     * Add blur effect using the Gaussian method.
     * default false.
     *
     * @param bool $blur
     */
    public function setBlur($blur)
    {
        $this->blur = $blur;
    }

    /**
     * The emboss effect.
     *
     * @return string $emboss
     */
    public function getEmboss()
    {
        return $this->emboss;
    }

    /**
     * Add emboss effect
     * default false.
     *
     * @param bool $emboss
     */
    public function setEmboss($emboss)
    {
        $this->emboss = $emboss;
    }

    /**
     * The pixelate effect.
     *
     * @return string $pixelate
     */
    public function getPixelate()
    {
        return $this->pixelate;
    }

    /**
     * Add pixelate effect
     * default false.
     *
     * @param bool $pixelate
     */
    public function setPixelate($pixelate)
    {
        $this->pixelate = $pixelate;
    }

    /**
     * Image format.
     *
     * @return string $format
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set the png image format.
     */
    public function setPngFormat()
    {
        $this->setFormat(self::FORMAT_PNG);
    }

    /**
     * Set the jpeg image format.
     */
    public function setJpegFormat()
    {
        $this->setFormat(self::FORMAT_JPEG);
    }

    /**
     * Set the image format
     * default FORMAT_PNG.
     *
     * @param string $format
     */
    private function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * The last error.
     *
     * @return string $error
     */
    public function getError()
    {
        return $this->error;
    }
}
