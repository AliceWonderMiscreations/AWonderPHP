<?php
declare(strict_types=1);

/**
 * Unit testing for WPCoreReplace class.
 *
 * @package AWonderPHP
 * @author  Alice Wonder <paypal@domblogger.net>
 * @license https://opensource.org/licenses/MIT MIT
 * @link    https://github.com/AliceWonderMiscreations/AWonderPHP
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for Misc
 */
// @codingStandardsIgnoreLine
final class WPCoreReplaceTest extends TestCase
{
    /**
     * Add query args to a URL.
     *
     * @return void
     */
    public function testModifyQueryArgsAddQueryArgs(): void
    {
        $url = 'http://www.example.org/whatever.php';
        $addQueryArgs = array(
            'a' => 'cats',
            'b' => 'dogs',
            'c' => 'goldfish'
        );
        $expected = 'http://www.example.org/whatever.php?a=cats&b=dogs&c=goldfish';
        $actual = \AWonderPHP\PluggableUnplugged\WPCoreReplace::modifyQueryArgs($url, $addQueryArgs);
        $this->assertEquals($expected, $actual);
    }//end testModifyQueryArgsAddQueryArgs()

    /**
     * Remove query args from a URL.
     *
     * @return void
     */
    public function testModifyQueryArgsRemovalOfQueryArgs(): void
    {
        $url = 'http://www.example.org/whatever.php?a=cats&b=dogs&c=goldfish';
        $removeQueryArgs = array(
            'a',
            'b',
            'c'
        );
        $expected = 'http://www.example.org/whatever.php';
        $actual = \AWonderPHP\PluggableUnplugged\WPCoreReplace::modifyQueryArgs($url, array(), $removeQueryArgs);
        $this->assertEquals($expected, $actual);
    }//end testModifyQueryArgsRemovalOfQueryArgs()

    /**
     * Remove some query args and add some others.
     *
     * @return void
     */
    public function testModifyQueryArgsRemoveAndAddQueryArgs(): void
    {
        $url = 'http://www.example.org/whatever.php?a=cats&b=dogs&c=goldfish';
        $removeQueryArgs = array(
            'a',
            'b'
        );
        $addQueryArgs = array(
            'd' => 'dancing+hamsters',
            'e' => 'quacky',
            'f' => 'whatever'
        );
        $expected = 'http://www.example.org/whatever.php?c=goldfish&d=dancing+hamsters&e=quacky&f=whatever';
        $actual = \AWonderPHP\PluggableUnplugged\WPCoreReplace::modifyQueryArgs($url, $addQueryArgs, $removeQueryArgs);
        $this->assertEquals($expected, $actual);
    }//end testModifyQueryArgsRemoveAndAddQueryArgs()

    /**
     * Change protocol from http to https.
     *
     * @return void
     */
    public function testModifyQueryArgsInsecureToSecureUrl(): void
    {
        $url = 'http://www.example.org/whatever.php?a=cats&b=dogs&c=goldfish';
        $removeQueryArgs = array(
            'a',
            'b'
        );
        $addQueryArgs = array(
            'd' => 'dancing+hamsters',
            'e' => 'quacky',
            'f' => 'whatever'
        );
        $expected = 'https://www.example.org/whatever.php?c=goldfish&d=dancing+hamsters&e=quacky&f=whatever';
        $actual = \AWonderPHP\PluggableUnplugged\WPCoreReplace::modifyQueryArgs(
            $url,
            $addQueryArgs,
            $removeQueryArgs,
            'https'
        );
        $this->assertEquals($expected, $actual);
    }//end testModifyQueryArgsInsecureToSecureUrl()

    /**
     * Use an internationalized domain name.
     *
     * @return void
     */
    public function testModifyQueryArgsWithInternationalized(): void
    {
        $url = 'http://価格.com/whatever.php';
        $addQueryArgs = array(
            'a' => 'cats',
            'b' => 'dogs',
            'c' => 'goldfish'
        );
        $expected = 'http://価格.com/whatever.php?a=cats&b=dogs&c=goldfish';
        $actual = \AWonderPHP\PluggableUnplugged\WPCoreReplace::modifyQueryArgs($url, $addQueryArgs);
        $this->assertEquals($expected, $actual);
    }//end testModifyQueryArgsWithInternationalized()
}//end class

?>