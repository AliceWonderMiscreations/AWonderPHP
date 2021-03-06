<?php
declare(strict_types=1);

/**
 * Alternative to PHP built in functions.
 *
 * In most cases, these are either core PHP functions that have been deprecated
 * or are likely to be deprecated.
 *
 * When the name is identical to a built-in PHP function the parameters and
 * output should be the same. When the name is not identical, the parameters and/or
 * output differ but usually it still should behave as a drop-in replacement.
 *
 * Intent is API compatible drop in replacements. However please note that in
 * cases where the PHP function is deprecated, it usually is deprecated for a
 * good reason, so a different way should be found.
 *
 * @package AWonderPHP/Compat
 * @author  Alice Wonder <paypal@domblogger.net>
 * @license https://opensource.org/licenses/MIT MIT
 * @version 0.1
 * @link    https://github.com/AliceWonderMiscreations/AWonderPHP
 */

namespace AWonderPHP;

/**
 * Static methods that provides alternatives to PHP functions of same name.
 */
class Compat
{
    /**
     * Replacement for the php uniqid function but using a pRNG for initial $more_entropy.
     * The uniqid function in php may become deprecated so this can be used instead if it
     * is actually needed, and produces compatible output.
     *
     * Like the native function, not suitable for cryptography purposes.
     *
     * In most cases you should use Compat::cryptoUniqid but if you need exact same output
     * format and do not need cryptographically strong, this one will do the job.
     *
     * About 2 times slower than native but much more collision resistant.
     *
     * @param string      $prefix       Optional. A prefix to use. Defaults to empty string.
     * @param bool        $more_entropy Optional. Whether or not additional entropy is needed.
     *
     * @return string The unique ID.
     */
    public static function uniqid(string $prefix = '', bool $more_entropy = false): string
    {
        static $nonce = null;
        if (is_null($nonce)) {
            $nonce = random_bytes(16);
        }
        $m = microtime(true);
        $return = sprintf("%8x%05x", floor($m), ($m-floor($m))*1000000);
        if ($more_entropy) {
            sodium_increment($nonce);
            $x = hexdec(substr(bin2hex($nonce), 0, 12));
            $x = (string) $x;
            $return = $return . substr($x, 2, 1) . '.' . substr($x, -8);
        }
        return $prefix . $return;
    }//end uniqid()
    
    /**
     * Mostly API compatible replacement for uniqid that still includes timestamp but provides
     * actual collision resistant nonce capabilities, but output is not same format. Should still
     * work as drop in replacement in most cases.
     *
     * When the second argument is false, the random portion can be guessed if more than one token
     * is generated by the same script. When the second argument is true, the random portion is not
     * guessable.
     *
     * @param string      $prefix Optional. A prefix to use. Defaults to empty string.
     * @param bool        $prng   Optional. Whether or not to use pRNG on each call. Defaults to
     *                            false. If you only need collision resistance then leave false. If
     *                            it must also not be predictable then set to true.
     * @param int         $bytes  Optional. Number of bytes to use for random part. Defaults to 16,
     *                            uses 12 if less than 12 is specified.
     *
     * @return string The unique ID.
     */
    public static function cryptoUniqid(string $prefix = '', bool $prng = false, int $bytes = 16): string
    {
        static $nonce = null;
        if ($bytes < 12) {
            $byres = 12;
        }
        if ($prng || is_null($nonce)) {
            $nonce = random_bytes(16);
        } else {
            sodium_increment($nonce);
        }
        $return = dechex(time()) . '.' . base64_encode($nonce);
        return $prefix . $return;
    }//end cryptoUniqid()
    
    /**
     * Drop in replacement for png2wbmp/jpeg2wmp function. Note the [png|jepg]2wbmp function have
     * height parameter before width which is counter convention, this replacement has to follow
     * what the original functions did. The threshold parameter is ignored, I could not find
     * adequate documentation on what it actually meant so I could not emulate it. Patches welcome.
     *
     * @param string $bitmapname  The path to a bitmap file to be converted.
     * @param string $wbmpname    The path to a WBMP file to be created.
     * @param int    $dest_height The pixel height of destination WBMP.
     * @param int    $dest_width  The pixel width of destination WBMP.
     * @param int    $threshold  The threshold value, between 0 and 8 inclusive.
     *
     * @return bool True on success, False on failure
     */
    public static function bitmap2wbmp(string $bitmapname, string $wbmpname, int $dest_height = 0, int $dest_width = 0, $threshold = 0)
    {
        if (! file_exists($bitmapname)) {
            throw new \InvalidArgumentException('File ' . $bitmapname . ' does not exist.');
            return false;
        }
        // make sure we can create the destination
        if (file_put_contents($wbmpname, '') === false) {
            throw new \InvalidArgumentException('Can not write to file ' . $wbmpname);
            return false;
        }
        unlink($wbmpname);
        $imtype = null;
        // detect image type
        if (! class_exists('\finfo', false)) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            if (! $mime = $finfo->file($bitmapname)) {
                return false;
            }
            switch ($mime) {
                case 'image/jpeg':
                    $imtype = 'jpeg';
                    break;
                case 'image/png':
                    $imtype = 'png';
                    break;
                case 'image/webp':
                    $imtype = 'webp';
                    break;
            }
        } else {
            //sniff type
            $str = strtolower(basename($bitmapname));
            $arr = explode('.', $str);
            $ext = end($arr);
            switch ($ext) {
                case 'jpg':
                    $imtype = 'jpeg';
                    break;
                default:
                    $imtype = $ext;
                    break;
            }
        }

        // gd image from bitmap
        switch ($imtype) {
            case 'png':
                if (! $image = imagecreatefrompng($bitmapname)) {
                    return false;
                }
                break;
            case 'jpeg':
                if (! $image = imagecreatefromjpeg($bitmapname)) {
                    return false;
                }
                break;
            case 'webp':
                if (! function_exists('imagecreatefromwebp')) {
                    throw new \InvalidArgumentException('Your version of PHP does not have imagecreatefromwebp.');
                    return false;
                }
                if (! $image = imagecreatefromwebp($bitmapname)) {
                    return false;
                }
                break;
            default:
                if (is_null($imtype)) {
                    $imtype = 'Not Detected';
                }
                throw new \InvalidArgumentException('Unsupported image type. Image type was: ' . $imtype);
                return false;
        }

        list($o_width, $o_height) = getimagesize($bitmapname);
        // might not be needed but php documentation does not specify return types of getimagesize
        $o_width =  intval($o_width, 10);
        $o_height = intval($o_height, 10);
        
        if (($dest_height + $dest_width) === 0) {
            $dest_width = $o_width;
            $dest_height = $o_height;
        }
        if ($dest_height === 0) {
            $ratio = round(($dest_width / $o_width), 3);
            $dest_height = intval(($o_height * $ratio), 10);
        } elseif ($dest_width === 0) {
            $ratio = round(($dest_height / $o_height), 3);
            $dest_width = intval(($o_width * $ratio), 10);
        }
        // resize image if needed
        if (($dest_width !== $o_width) || ($dest_height !== $o_height)) {
            $newimage = imagecreate($dest_width, $dest_height);
            imagecopyresampled($newimage, $image, 0, 0, 0, 0, $dest_width, $dest_height, $o_width, $o_height);
            imagedestroy($image);
        }
        // make the WBMP
        if (isset($newimage)) {
            $return = imagewbmp($newimage, $wbmpname);
            imagedestroy($newimage);
        } else {
            $return = imagewbmp($image, $wbmpname);
            imagedestroy($image);
        }
        return $return;
    }//end bitmap2wbmp()

    /**
     * Replacement for gmp_random($limiter) function. Note that the php documentation claims that
     * if the limiter is negative, it spits out negative numbers. That's not the behavior I saw but
     * that is what I am going to emulate.
     *
     * Generates a random integer between 0 and (2^($limiter * pseudolimb)) - 1. If the $limiter is
     * negative it generates a negative integer.
     *
     * @param int $limiter    The limiter.
     * @param int $pseudolimb The function this emulates uses what GMP calls a limb. It is not
     *                        static and varies from system to system, but seems to usually be
     *                        either 32 or 64. It is not public exposed. So I default to a limb
     *                        size of 64 as that is what it is on my system, but if you want the
     *                        genuine limb size of your system used and you know it (e.g. from a
     *                        debugger) you can set it as the second argument.
     *
     * @return \GMP A gmp object.
     */
    public static function gmpRandom(int $limiter, int $pseudolimb = 64)
    {
        $negative = false;
        if ($limiter < 0) {
            $negative = true;
            $limiter = abs($limiter);
        }
        if ($pseudolimb < 0) {
            $pseudolimb = 64;
        }
        $exp = ($limiter * $pseudolimb);
        $rand = gmp_random_bits($exp);
        if ($negative) {
            return gmp_neg($rand);
        }
        return $rand;
    }//end gmpRandom()
}//end class

?>