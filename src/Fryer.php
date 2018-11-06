<?php

namespace MirazMac\DeepFry;

/**
* A Deep Fryer written in PHP to cook some smokin' hot memes!
*
* @author Miraz Mac <mirazmac@gmail.com>
*/
class Fryer
{
    /**
     * Image Quality
     *
     * @var integer
     */
    protected $quality = 30;

    /**
     * GD Instance
     *
     * @var resource
     */
    protected $gd;

    /**
     * File name
     *
     * @var string
     */
    public $fileName;

    /**
     * Create a new Fryer instance
     *
     * @param string $imagePath Path to the image that'd be fried
     */
    public function __construct($imagePath)
    {
        if (!extension_loaded('gd')) {
            throw new \RuntimeException("Lmao. PHP GD extension is required to run this library 😂😂😂");
        }

        if (!is_file($imagePath)) {
            throw new \LogicException("ni🅱🅱a u mad? no such file found at: {$imagePath}");
        }

        $this->fileName = pathinfo($imagePath, PATHINFO_FILENAME);
        $this->gd       = @imagecreatefromstring(file_get_contents($imagePath));

        if (!$this->gd) {
            throw new \LogicException('Failed to create GD instance, make sure your file is a valid image.');
        }
    }

    /**
     * Increase deepfryness
     *
     * @param  integer $saturation Saturation value
     * @return Fryer
     */
    public function fry($saturation = 100)
    {
        $saturation = (int) $saturation;
        $this->imageSaturation($saturation);
        $this->sharpen();
        return $this;
    }

    /**
     * Alias of self::fry() with 3x increment on saturation
     *
     * @return Fryer
     */
    public function moreDeepNibba()
    {
        return $this->fry(300);
    }

    /**
     * Set the image quality
     *
     * @var integer
     * @return Fryer
     */
    public function quality($quality)
    {
        $quality = (int) $quality;

        if ($quality < 0 || $quality > 100) {
            throw new \InvalidArgumentException("Quality must be a valid integer between 0-100!");
        }

        $this->quality = $quality;
        return $this;
    }

    /**
     * Sharpen the image
     *
     * @return Fryer
     * @link http://adamhopkinson.co.uk/blog/2010/08/26/sharpen-an-image-using-php-and-gd/ Base function
     */
    public function sharpen()
    {
        $sharpen = [
            [0.0, -1.0, 0.0],
            [-1.0, 5.0, -1.0],
            [0.0, -1.0, 0.0]
        ];

        $divisor = array_sum(array_map('array_sum', $sharpen));
        imageconvolution($this->gd, $sharpen, $divisor, 0);

        return $this;
    }

    /**
     * Output the image to browser and destroys the instance if enabled
     *
     * @param  boolean $forceDownload Whether to force download or not, defaults to FALSE
     * @param  boolean $destroy       Whether to destroy the GD instance or not, defaults to TRUE
     * @return
     */
    public function output($forceDownload = false, $destroy = true)
    {
        header('Content-type: image/jpeg');
        if ($forceDownload) {
              header('Content-Disposition: attachment; filename="' . $this->fileName . '_deepfried.jpg";');
        }
        imagejpeg($this->gd, null, $this->quality);

        if ($destroy) {
            $this->destroy();
        }
    }

    /**
     * Save the image to disk and destroy the instance if needed
     *
     * @param  string|null $fileName      Provide a filename
     * @param  boolean     $destroy       Whether to destroy the GD instance or not, defaults to TRUE
     * @return
     */
    public function save($fileName = null, $destroy = true)
    {
        if (!$fileName) {
            $fileName = $this->fileName . '_deepfried.jpg';
        }

        imagejpeg($this->gd, $fileName, $this->quality);

        if ($destroy) {
            $this->destroy();
        }

        return true;
    }

    /**
     * Destroy the GD instance
     *
     * @return
     */
    public function destroy()
    {
        if (is_resource($this->gd)) {
            imagedestroy($this->gd);
            return true;
        }

        return false;
    }

    /**
     * Change saturation of image using GD
     *
     * @param  integer $saturationPercentage Saturation percentage
     * @return Fryer
     * @link   https://badbytes.blogspot.com/2013/07/saturating-images-with-php.html Base function
     */
    protected function imageSaturation($saturationPercentage)
    {
        $width  = imagesx($this->gd);
        $height = imagesy($this->gd);

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgb = imagecolorat($this->gd, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $alpha = ($rgb & 0xFF000000) >> 24;
                list($h, $s, $v) = $this->rgb2hsv($r, $g, $b);
                $s = $s * (100 + $saturationPercentage ) / 100;
                if ($s > 1) {
                    $s = 1;
                }

                list($r, $g, $b) = $this->hsv2rgb($h, $s, $v);
                imagesetpixel($this->gd, $x, $y, imagecolorallocatealpha($this->gd, $r, $g, $b, $alpha));
            }
        }

        return $this;
    }

    /**
     * Convert RGB to HSV
     *
     * @param  integer $r
     * @param  integer $g
     * @param  integer $b
     * @return array
     * @link   https://badbytes.blogspot.com/2013/07/saturating-images-with-php.html Base function
     */
    protected function rgb2hsv($r, $g, $b)
    {
        $newR   = ($r / 255);
        $newG   = ($g / 255);
        $newB   = ($b / 255);
        $rgbMin = min($newR, $newG, $newB);
        $rgbMax = max($newR, $newG, $newB);
        $chroma = $rgbMax - $rgbMin;
        $v      = $rgbMax;
        if ($chroma == 0) {
            $h = 0;
            $s = 0;
        } else {
            $s = $chroma / $rgbMax;
            $chromaR = ((($rgbMax - $newR)/6) + ($chroma/2))/$chroma;
            $chromaG = ((($rgbMax - $newG)/6) + ($chroma/2))/$chroma;
            $chromaB = ((($rgbMax - $newB)/6) + ($chroma/2))/$chroma;
            if ($newR == $rgbMax) {
                $h = $chromaB - $chromaG;
            } elseif ($newG == $rgbMax) {
                $h = ( 1 / 3 ) + $chromaR - $chromaB;
            } elseif ($newB == $rgbMax) {
                $h = ( 2 / 3 ) + $chromaG - $chromaR;
            }

            if ($h < 0) {
                $h++;
            }

            if ($h > 1) {
                $h--;
            }
        }
        return [$h, $s, $v];
    }

    /**
     * Convert HSV to RGB
     *
     * @param  integer $h
     * @param  integer $s
     * @param  integer $v
     * @return array
     * @link   https://badbytes.blogspot.com/2013/07/saturating-images-with-php.html Base function
     */
    protected function hsv2rgb($h, $s, $v)
    {
        if ($s == 0) {
            $r = $g = $b = $v * 255;
        } else {
            $newH  = $h * 6;
            $i     = floor($newH);
            $var_1 = $v * ( 1 - $s );
            $var_2 = $v * ( 1 - $s * ( $newH - $i ) );
            $var_3 = $v * ( 1 - $s * (1 - ( $newH - $i ) ) );
            if ($i == 0) {
                $newR = $v;
                $newG = $var_3;
                $newB = $var_1;
            } elseif ($i == 1) {
                $newR = $var_2;
                $newG = $v;
                $newB = $var_1;
            } elseif ($i == 2) {
                $newR = $var_1;
                $newG = $v;
                $newB = $var_3;
            } elseif ($i == 3) {
                $newR = $var_1;
                $newG = $var_2;
                $newB = $v;
            } elseif ($i == 4) {
                $newR = $var_3;
                $newG = $var_1;
                $newB = $v;
            } else {
                $newR = $v;
                $newG = $var_1;
                $newB = $var_2;
            }

            $r = $newR * 255;
            $g = $newG * 255;
            $b = $newB * 255;
        }
        return [$r, $g, $b];
    }
}
