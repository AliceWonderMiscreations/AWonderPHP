<?php
declare(strict_types=1);

/**
 * Unit testing for SimpleCache abstract class. This not only tests the concrete
 * methods that do not depend upon cache implementation but it can serve as a template
 * for implementation unit tests, just do not create the anonymous class.
 *
 * @package AWonderPHP
 * @author  Alice Wonder <paypal@domblogger.net>
 * @license https://opensource.org/licenses/MIT MIT
 * @link    https://github.com/AliceWonderMiscreations/AWonderPHP
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for PunycodeStatic
 */
// @codingStandardsIgnoreLine
final class PunycodeStaticTest extends TestCase
{
    /**
     * Test conversion of domain name from UTF8 to ASCII.
     *
     * @return void
     */
    public function testPunycodeDomain(): void
    {
        $domains = array(
            'österreich.icom.museum' => 'xn--sterreich-z7a.icom.museum',
            'србијаицрнагора.иком.museum' => 'xn--80aaabm1ab4blmeec9e7n.xn--h1aegh.museum',
            '価格.com' => 'xn--1sqt31d.com',
            'example.org' => 'example.org'
        );
        foreach ($domains as $idn => $expected) {
            $actual = \AWonderPHP\PluggableUnplugged\PunycodeStatic::punycodeDomain($idn);
            $this->assertEquals($expected, $actual);
        }
    }//end testPunycodeDomain()

    /**
     * Test conversion of domain name from ASCII to UTF8.
     *
     * @return void
     */
    public function testUnpunycodeDomain(): void
    {
        $domains = array(
            'xn--sterreich-z7a.icom.museum' => 'österreich.icom.museum',
            'xn--80aaabm1ab4blmeec9e7n.xn--h1aegh.museum' => 'србијаицрнагора.иком.museum',
            'xn--1sqt31d.com' => '価格.com',
            'example.org' => 'example.org'
        );
        foreach ($domains as $ascii => $expected) {
            $actual = \AWonderPHP\PluggableUnplugged\PunycodeStatic::unpunycodeDomain($ascii);
            $this->assertEquals($expected, $actual);
        }
    }//end testUnpunycodeDomain()

    /**
     * Test conversion of e-mail address from UTF8 to ASCII.
     *
     * @return void
     */
    public function testPunycodeEmail(): void
    {
        $addresses = array(
            'user@österreich.icom.museum' => 'user@xn--sterreich-z7a.icom.museum',
            'user@србијаицрнагора.иком.museum' => 'user@xn--80aaabm1ab4blmeec9e7n.xn--h1aegh.museum',
            'user@価格.com' => 'user@xn--1sqt31d.com',
            'user@example.org' => 'user@example.org'
        );
        foreach ($addresses as $idn => $expected) {
            $actual = \AWonderPHP\PluggableUnplugged\PunycodeStatic::punycodeEmail($idn);
            $this->assertEquals($expected, $actual);
        }
    }//end testPunycodeEmail()
}//end class

?>