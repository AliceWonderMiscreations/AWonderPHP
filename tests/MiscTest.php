<?php
declare(strict_types=1);

/**
 * Unit testing for Misc class.
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
final class MiscTest extends TestCase
{
    /**
     * Generation of default 16 byte nonce.
     *
     * @return void
     */
    public function testGenerateNonceDefault(): void
    {
        //test 16 byte nonce default
        $foo = \AWonderPHP\PluggableUnplugged\Misc::generateNonce();
        $actual = strlen($foo);
        $expected = 24;
        $this->assertEquals($expected, $actual);
    }//end testGenerateNonceDefault()

    /**
     * Ask for 8 byte nonce, should get 16.
     *
     * @return void
     */
    public function testGenerateNonceSpecifyingTooSmall(): void
    {
        //ask for an 8 byte nonce, should get 16
        $foo = \AWonderPHP\PluggableUnplugged\Misc::generateNonce(8);
        $actual = strlen($foo);
        $expected = 24;
        $this->assertEquals($expected, $actual);
    }//end testGenerateNonceSpecifyingTooSmall()

    /**
     * Ask for 24 byte nonce, should get 24.
     *
     * @return void
     */
    public function testGenerateNonceSpecifyingLarge(): void
    {
        //ask for an 8 byte nonce, should get 16
        $foo = \AWonderPHP\PluggableUnplugged\Misc::generateNonce(24);
        $actual = strlen($foo);
        $expected = 32;
        $this->assertEquals($expected, $actual);
    }//end testGenerateNonceSpecifyingLarge()

    /**
     * Generate 256 bit salt.
     *
     * @return void
     */
    public function testGenerateASalt(): void
    {
        $foo = \AWonderPHP\PluggableUnplugged\Misc::saltShaker();
        $actual = strlen($foo);
        $expected = 44;
        $this->assertEquals($expected, $actual);
    }//end testGenerateASalt()
}//end class

?>