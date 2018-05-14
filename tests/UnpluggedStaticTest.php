<?php
declare(strict_types=1);

/**
 * Unit testing for UnpluggedStatic class.
 *
 * @package AWonderPHP
 * @author  Alice Wonder <paypal@domblogger.net>
 * @license https://opensource.org/licenses/MIT MIT
 * @link    https://github.com/AliceWonderMiscreations/AWonderPHP
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for UnpluggedStatic class
 */
// @codingStandardsIgnoreLine
final class UnpluggedStaticTest extends TestCase
{
    /**
     * Testing hash with default (32) bytes.
     *
     * @return void
     */
    public function testGenerateHashWithDefaultBytes(): void
    {
        $data = 'This is a test string we are taking a hash of.';
        $salt = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $expected = 'pxhvdMSFPp3HSAB7QT2vDYMlHJdU5l8wCVgL42UOQSk=';
        $actual = \AWonderPHP\PluggableUnplugged\UnpluggedStatic::cryptoHash($data, $salt);
        $this->assertEquals($expected, $actual);
    }//end testGenerateHashWithDefaultBytes()

    /**
     * Testing hash specifying too few bytes.
     *
     * @return void
     */
    public function testGenerateHashWithSpecifiedTooFewBytes(): void
    {
        $data = 'This is a test string we are taking a hash of.';
        $salt = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $expected = 'exa0i+jLj279aLsiEDSsmA==';
        $actual = \AWonderPHP\PluggableUnplugged\UnpluggedStatic::cryptoHash($data, $salt, 2);
        $this->assertEquals($expected, $actual);
    }//end testGenerateHashWithSpecifiedTooFewBytes()

    /**
     * Testing hash specifying too many bytes.
     *
     * @return void
     */
    public function testGenerateHashWithSpecifiedTooManyBytes(): void
    {
        $data = 'This is a test string we are taking a hash of.';
        $salt = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $expected = 'nsafnEEUYGd6UgE+UBXoeYBaCMFjcdPlZn9adz2qyFi8ml08vPTOeJD47S2k7+ybY0BncqoYrHyxObHBM0ZkfQ==';
        $actual = \AWonderPHP\PluggableUnplugged\UnpluggedStatic::cryptoHash($data, $salt, 1024);
        $this->assertEquals($expected, $actual);
    }//end testGenerateHashWithSpecifiedTooManyBytes()

    /**
     * Testing hash specifying 24 bytes.
     *
     * @return void
     */
    public function testGenerateHashWithSpecifiedTwentyFourBytes(): void
    {
        $data = 'This is a test string we are taking a hash of.';
        $salt = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $expected = 'XcfOu/YiApvqQzbMUofvvWAbGFHOwMBJ';
        $actual = \AWonderPHP\PluggableUnplugged\UnpluggedStatic::cryptoHash($data, $salt, 24);
        $this->assertEquals($expected, $actual);
    }//end testGenerateHashWithSpecifiedTwentyFourBytes()

    /**
     * Testing pRNG with positive int, method assuming 0 for min.
     *
     * @return void
     */
    public function testRandomIntOnlySpecifyingPositiveNumber(): void
    {
        $n = \AWonderPHP\PluggableUnplugged\UnpluggedStatic::safeRandInt(24);
        $this->assertLessThanOrEqual(24, $n);
        $this->assertLessThanOrEqual($n, 0);
    }//end testRandomIntOnlySpecifyingPositiveNumber()

    /**
     * Testing pRNG with negative int, method assuming 0 for max.
     *
     * @return void
     */
    public function testRandomIntOnlySpecifyingNegativeNumber(): void
    {
        $n = \AWonderPHP\PluggableUnplugged\UnpluggedStatic::safeRandInt(-24);
        $this->assertLessThanOrEqual($n, -24);
        $this->assertLessThanOrEqual(0, $n);
    }//end testRandomIntOnlySpecifyingNegativeNumber()

    /**
     * Testing pRNG specifying smaller first.
     *
     * @return void
     */
    public function testRandomIntSmallerParameterFirst(): void
    {
        $n = \AWonderPHP\PluggableUnplugged\UnpluggedStatic::safeRandInt(24, 72);
        $this->assertLessThanOrEqual(72, $n);
        $this->assertLessThanOrEqual($n, 24);
    }//end testRandomIntSmallerParameterFirst()

    /**
     * Testing pRNG specifying larger first.
     *
     * @return void
     */
    public function testRandomIntLargerParameterFirst(): void
    {
        $n = \AWonderPHP\PluggableUnplugged\UnpluggedStatic::safeRandInt(72, 24);
        $this->assertLessThanOrEqual(72, $n);
        $this->assertLessThanOrEqual($n, 24);
    }//end testRandomIntLargerParameterFirst()

    /**
     * Testing random password generation with default (16) length.
     *
     * @return void
     */
    public function testGeneratePasswordDefault(): void
    {
        $str = \AWonderPHP\PluggableUnplugged\UnpluggedStatic::generatePassword();
        $actual = strlen($str);
        $expected = 16;
        $this->assertEquals($expected, $actual);
    }//end testGeneratePasswordDefault()

    /**
     * Testing random password generation with minimum (12) length.
     *
     * @return void
     */
    public function testGeneratePasswordMinimumLength(): void
    {
        $str = \AWonderPHP\PluggableUnplugged\UnpluggedStatic::generatePassword(12);
        $actual = strlen($str);
        $expected = 12;
        $this->assertEquals($expected, $actual);
    }//end testGeneratePasswordMinimumLength()

    /**
     * Testing random password generation with below length.
     *
     * @return void
     */
    public function testGeneratePasswordTooShort(): void
    {
        $str = \AWonderPHP\PluggableUnplugged\UnpluggedStatic::generatePassword(8);
        $actual = strlen($str);
        $expected = 12;
        $this->assertEquals($expected, $actual);
    }//end testGeneratePasswordTooShort()

    /**
     * Testing random password generation with maximum (255) length.
     *
     * @return void
     */
    public function testGeneratePasswordMaxLength(): void
    {
        $str = \AWonderPHP\PluggableUnplugged\UnpluggedStatic::generatePassword(255);
        $actual = strlen($str);
        $expected = 255;
        $this->assertEquals($expected, $actual);
    }//end testGeneratePasswordMaxLength()

    /**
     * Testing random password generation with above maximum length.
     *
     * @return void
     */
    public function testGeneratePasswordTooLong(): void
    {
        $str = \AWonderPHP\PluggableUnplugged\UnpluggedStatic::generatePassword(512);
        $actual = strlen($str);
        $expected = 255;
        $this->assertEquals($expected, $actual);
    }//end testGeneratePasswordTooLong()

    /**
     * Testing random password generation with only alphanurmeric.
     *
     * @return void
     */
    public function testGeneratePasswordBasicAlphabetOnly(): void
    {
        $str = \AWonderPHP\PluggableUnplugged\UnpluggedStatic::generatePassword(255, false, false);
        $str = preg_replace('/[^A-Za-z0-9]/', '', $str);
        $actual = strlen($str);
        $expected = 255;
        $this->assertEquals($expected, $actual);
    }//end testGeneratePasswordBasicAlphabetOnly()

    /**
     * Testing random password generation with alphanurmeric + special.
     *
     * @return void
     */
    public function testGeneratePasswordBasicPlusSpecial(): void
    {
        $str = \AWonderPHP\PluggableUnplugged\UnpluggedStatic::generatePassword(255, true, false);
        $str = preg_replace('/[^A-Za-z0-9]/', '', $str);
        $actual = strlen($str);
        $expected = 255;
        $this->assertLessThan($expected, $actual);
    }//end testGeneratePasswordBasicPlusSpecial()

    /**
     * Testing random password generation with alphanurmeric + extra special.
     *
     * @return void
     */
    public function testGeneratePasswordBasicPlusExtraSpecial(): void
    {
        $str = \AWonderPHP\PluggableUnplugged\UnpluggedStatic::generatePassword(255, false, true);
        $str = preg_replace('/[^A-Za-z0-9]/', '', $str);
        $actual = strlen($str);
        $expected = 255;
        $this->assertLessThan($expected, $actual);
    }//end testGeneratePasswordBasicPlusExtraSpecial()

    /**
     * Testing random password generation with alphanurmeric both special + extra special.
     *
     * @return void
     */
    public function testGeneratePasswordBasicPlusSpecialPlusExtraSpecial(): void
    {
        $str = \AWonderPHP\PluggableUnplugged\UnpluggedStatic::generatePassword(255, true, true);
        $s = array();
        $r = array();
        //!@#$%^&*()
        $s[] = '/!/';
        $r[] = '';
        $s[] = '/@/';
        $r[] = '';
        $s[] = '/#/';
        $r[] = '';
        $s[] = '/$/';
        $r[] = '';
        $s[] = '/%/';
        $r[] = '';
        $s[] = '/\^/';
        $r[] = '';
        $s[] = '/&/';
        $r[] = '';
        $s[] = '/\*/';
        $r[] = '';
        $s[] = '/\(/';
        $r[] = '';
        $s[] = '/\)/';
        $r[] = '';
        $str = preg_replace($s, $r, $str);
        $actual = strlen($str);
        $expected = 255;
        $this->assertLessThan($expected, $actual);
        $str = preg_replace('/[^A-Za-z0-9]/', '', $str);
        $expected = $actual;
        $actual = strlen($str);
        $this->assertLessThan($expected, $actual);
        $str = preg_replace('/[^A-Za-z0-9]/', '', $str);
        // make sure our test nuked what needed to be nuked
        $expected = $actual;
        $actual = strlen($str);
        $this->assertEquals($expected, $actual);
    }//end testGeneratePasswordBasicPlusSpecialPlusExtraSpecial()

    /**
     * Testing generation of Argon2id hash.
     *
     * @return void
     */
    public function testArgon2idPasswordHash(): void
    {
        $pass = \AWonderPHP\PluggableUnplugged\UnpluggedStatic::generatePassword(16, true, true);
        $hash = \AWonderPHP\PluggableUnplugged\UnpluggedStatic::hashPassword($pass);
        $expected = '$argon2id$';
        $actual = substr($hash, 0, 10);
        $this->assertEquals($expected, $actual);
    }//end testArgon2idPasswordHash()

    /**
     * Testing validation of password against Argon2id hash.
     *
     * @return void
     */
    public function testValidateArgon2idPasswordHash(): void
    {
        $pass = 'v)*LM/f*Bi0[.v*d';
        $hash = '$argon2id$v=19$m=65536,t=2,p=1$mTDN3FtLdGmhXyQ8Z9+69w$ukEQ0xyFwGMhFZMlk17gZQpqvKZ9hJ0bKRpaKWz9pgQ';
        $res = \AWonderPHP\PluggableUnplugged\UnpluggedStatic::checkPassword($pass, $hash);
        $this->assertTrue($res);
    }//end testValidateArgon2idPasswordHash()

    /**
     * Testing rejection of password against Argon2id hash.
     *
     * @return void
     */
    public function testRejectNonValidArgon2idPassword(): void
    {
        $pass = \AWonderPHP\PluggableUnplugged\UnpluggedStatic::generatePassword(16, false, false);
        $hash = '$argon2id$v=19$m=65536,t=2,p=1$mTDN3FtLdGmhXyQ8Z9+69w$ukEQ0xyFwGMhFZMlk17gZQpqvKZ9hJ0bKRpaKWz9pgQ';
        $res = \AWonderPHP\PluggableUnplugged\UnpluggedStatic::checkPassword($pass, $hash);
        $this->assertFalse($res);
    }//end testRejectNonValidArgon2idPassword()
}//end class

?>