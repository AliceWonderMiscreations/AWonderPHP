<?php
declare(strict_types=1);

/**
 * Create OpenGraph and TwitterCards. This class supports the basics only, if
 * you are writing a plugin that wants things like custom object namespaces in
 * OpenGraph, then extend this class and write the methods you need.
 *
 * @package AWonderPHP/Compat
 * @author  Alice Wonder <paypal@domblogger.net>
 * @license https://opensource.org/licenses/MIT MIT
 * @version 0.1
 * @link    https://github.com/AliceWonderMiscreations/AWonderPHP
 */

namespace AWonderPHP;

/**
 * Class for creating OpenGraph and TwitterCards
 */
class OpenGraph
{
    /**
     * @var \DOMDocument|null When the class is instantiated with a DOMDocument object, the
     *                        constructor assigns this to that object.
     */
    protected $dom = null;
    
    /**
     * None structured property types
     *
     * @var array An array of the valid non-structured property types
     */
    protected $ogproptypes = array(
        'title',
        'type',
        'url',
        'description',
        'determiner',
        'locale',
        'locale:alternate',
        'site_name'
    );
    
    /**
     * Array of valid image mime types. Property is public to allow a class to intentionally
     * add image types if needed.
     *
     * @var array The array of valid mime types.
     */
    public $imagemime = array(
        'image/gif',
        'image/jpeg',
        'image/png',
        'image/webp'
    );
    
    /**
     * Make sure an attribute is safe to add when returning string.
     * This function is not needed when adding attributes to a
     * \DOMNode as setAttribute() already does this.
     *
     * @param string $attribute The attribute to clean.
     *
     * @return string The cleaned attribute.
     */
    protected function cleanHtmlAttribute(string $attribute): string
    {
        $attribute = html_entity_decode($attribute, ENT_COMPAT | ENT_HTML5, 'UTF-8');
        $attribute = preg_replace('/javascript:/i', '', $attribute);
        $attribute = htmlspecialchars($attribute, ENT_COMPAT | ENT_HTML5, 'UTF-8');
        return $attribute;
    }//end cleanHtmlAttribute()

    
    /**
     * Validates a URL
     *
     * @param string $url The URL to validate.
     *
     * @return string|bool The valid URL or false on failure.
     */
    protected function validUrl($url)
    {
        $parsed = parse_url($url);
        if (! isset($parsed['scheme'])) {
            return false;
        }
        if (! in_array($parsed['scheme'], array('http', 'https'))) {
            return false;
        }
        if (! isset($parsed['host'])) {
            return false;
        }
        $hostname = $parsed['host'];
        if (class_exists('\AWonderPHP\PluggableUnplugged\PunycodeStatic')) {
            $hostname = \AWonderPHP\PluggableUnplugged\PunycodeStatic::punycodeDomain($parsed['host']);
        }
        $clean = $parsed['scheme'] . '://' . $hostname;
        if (isset($parsed['port'])) {
            $clean = $clean . ':' . $parsed['port'];
        }
        if (isset($parsed['path'])) {
            $clean .= $parsed['path'];
        } else {
            $clean .= '/';
        }
        if (isset($parsed['query'])) {
            $clean = $clean . '?' . $parsed['query'];
        }
        if (isset($parsed['fragment'])) {
            $clean = $clean . '#' . $parsed['fragment'];
        }
        return filter_var($clean, FILTER_VALIDATE_URL);
    }//end validUrl()

    
    /**
     * Creates a non-image OpenGraph meta tag
     *
     * @param string $property The property to add.
     * @param string $content  The property content.
     *
     * @return \DOMNode|string|bool The DOMDocument node or a string representing
     *                              the meta tag. False if it can not create.
     */
    public function addProperty(string $property, string $content)
    {
        $proprty = trim(strtolower($property));
        if (! in_array($property, $this->ogproptypes)) {
            return false;
        }
        $property = 'og:' . $proprty;
        $content = trim($content);
        
        if (! is_null($this->dom)) {
            $meta = $this->dom->createElement('meta');
            $meta->setAttribute('property', $property);
            $meta->setAttribute('content', $content);
        } else {
            $content = $this->cleanHtmlAttribute($content);
            $meta = '<meta property="' . $property . '" content="' . $content . '"/>';
        }
        return $meta;
    }//end addProperty()

    /**
     * Creates image meta tags.
     *
     * @param string $url    The url of the image.
     * @param string $mime   The MIME type of the image.
     * @param int    $width  The pixel width of the image.
     * @param int    $height The pixel height of the image.
     * @param string $alt    The alt tag for the image.
     *
     * @return array|bool An array containing either the \DOMNode meta objects or strings
     *                    representing the image. False if it can not create.
     */
    public function addImage(string $url, string $mime, int $width, int $height, string $alt = '')
    {
        $url = $this->validUrl($url));
        if (is_bool($url)) {
            return false;
        }
        $mime = trim(strtolower($mime));
        if (substr_count($mime, 'image/') !== 1) {
            $mime = 'image/' . $mime;
        }
        if ($mime === 'image/jpg') {
            $mime = 'image/jpeg';
        }
        if (! in_array($mime, $this->imagemime)) {
            return false;
        }
        if (($width <= 0) || ($height <= 0)) {
            return false;
        }
        $width = (string) $width;
        $height = (string) $height;
        $return = array();
        if (! is_null($this->dom)) {
            $meta = $this->dom->createElement('meta');
            $meta->setAttribute('property', 'og:image');
            $meta->setAttribute('content', $url);
            $return[] = $meta;
            $meta = $this->dom->createElement('meta');
            $meta->setAttribute('property', 'og:image:type');
            $meta->setAttribute('content', $mime);
            $return[] = $meta;
            $meta = $this->dom->createElement('meta');
            $meta->setAttribute('property', 'og:image:width');
            $meta->setAttribute('content', $width);
            $return[] = $meta;
            $meta = $this->dom->createElement('meta');
            $meta->setAttribute('property', 'og:image:height');
            $meta->setAttribute('content', $height);
            $return[] = $meta;
            if (strlen($alt) > 0) {
                $meta = $this->dom->createElement('meta');
                $meta->setAttribute('property', 'og:image:alt');
                $meta->setAttribute('content', $alt);
                $return[] = $meta;
            }
        } else {
            $return[] = '<meta property="og:image" content="' . $url . '"/>';
            $return[] = '<meta property="og:image:type" content="' . $mime . '"/>';
            $return[] = '<meta property="og:image:width" content="' . $width . '"/>';
            $return[] = '<meta property="og:image:height" content="' . $height . '"/>';
            $alt = $this->cleanHtmlAttribute($alt);
            if (strlen($alt) > 0) {
                $return[] = $return[] = '<meta property="og:image:alt" content="' . $alt . '"/>';
            }
        }
        return $return;
    }//end addImage()

    
    /**
     * The constructor
     *
     * @param null|\DOMDocument $dom If \DOMDocument is being used, the instance of DOMDocument.
     */
    public function __construct($dom = null)
    {
        if ($dom instanceof \DOMDocument) {
            $this->dom = $dom;
        }
    }//end __construct()
}//end class

?>