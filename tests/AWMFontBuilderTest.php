<?php
declare(strict_types=1);

/**
 * Unit testing for AWMFontBuilder class.
 *
 * @package AWonderPHP
 * @author  Alice Wonder <paypal@domblogger.net>
 * @license https://opensource.org/licenses/MIT MIT
 * @link    https://github.com/AliceWonderMiscreations/AWonderPHP
 */

use PHPUnit\Framework\TestCase;

define('WEBFONT_MIRROR', 'fonts.example.org');

/**
 * Test class for AWMFontBuilder
 */
// @codingStandardsIgnoreLine
final class AWMFontBuilderTest extends TestCase
{
    /**
     * Test adding a single font.
     *
     * @return void
     */
    public function testAddSingleFont(): void
    {
        $webfonts = new \AWonderPHP\AWMFontBuilder();
        $webfonts->setFont('Comic Sans MS');
        $expected = 'https://fonts.example.org/css?family=Comic+Sans+MS:Italic&subset=latin,latin-ext';
        $actual = $webfonts->addWebfontToHead('foobar');
        $this->assertEquals($expected, $actual);
    }//end testAddSingleFont()

    /**
     * Test adding multiple fonts.
     *
     * @return void
     */
    public function testAddMultipleFonts(): void
    {
        $webfonts = new \AWonderPHP\AWMFontBuilder();
        $webfonts->setFont('Comic Sans MS');
        $webfonts->setFont('Monaco', array(), false);
        $expected = 'https://fonts.example.org/css?family=Comic+Sans+MS:Italic|Monaco&subset=latin,latin-ext';
        $actual = $webfonts->addWebfontToHead('foobar');
        $this->assertEquals($expected, $actual);
    }//end testAddMultipleFonts()

    /**
     * Test adding a single font with weights.
     *
     * @return void
     */
    public function testAddSingleFontWithWeights(): void
    {
        $webfonts = new \AWonderPHP\AWMFontBuilder();
        $webfonts->setFont('Helvetica', array(400, 700));
        $expected = 'https://fonts.example.org/css?family=Helvetica:400,400i,700,700i&subset=latin,latin-ext';
        $actual = $webfonts->addWebfontToHead('foobar');
        $this->assertEquals($expected, $actual);
    }//end testAddSingleFontWithWeights()

    /**
     * Test adding a single font with weights and subsets.
     *
     * @return void
     */
    public function testAddSingleFontWithWeightsAndSubsets(): void
    {
        $webfonts = new \AWonderPHP\AWMFontBuilder();
        $webfonts->setFont('Helvetica', array(400, 700));
        $webfonts->setSubset(array('Hebrew', 'Greek'));
        $expected = 'https://fonts.example.org/css?family=Helvetica:400,400i,700,700i&subset=latin,latin-ext,hebrew,greek';
        $actual = $webfonts->addWebfontToHead('foobar');
        $this->assertEquals($expected, $actual);
    }//end testAddSingleFontWithWeightsAndSubsets()

    /**
     * Test adding a font with named weights.
     *
     * @return void
     */
    public function testAddingFontWithNamedFontWeights(): void
    {
        $webfonts = new \AWonderPHP\AWMFontBuilder();
        $weights = array(
            'ExtraLight',
            'Regular',
            'SemiBold',
            'book'
        );
        $webfonts->setFont('Libre Franklin', $weights);
        $expected = 'https://fonts.example.org/css?family=Libre+Franklin:200,200i,400,400i,600,600i,900,900i&subset=latin,latin-ext';
        $actual = $webfonts->addWebfontToHead('foobar');
        $this->assertEquals($expected, $actual);
    }//end testAddingFontWithNamedFontWeights()

    /**
     * Test parsing a font string with italics.
     *
     * @return void
     */
    public function testParseFontParametersWithItalics(): void
    {
        $testString = 'Noto+Sans:400,400i,700,700i';
        $arr = \AWonderPHP\AWMFontBuilder::parseFontParameters($testString);
        $expected = 'Noto+Sans';
        $actual = $arr[0];
        $this->assertEquals($expected, $actual);
        $expected = 2;
        $actual = count($arr[1]);
        $expected = 400;
        $actual = $arr[1][0];
        $this->assertEquals($expected, $actual);
        $expected = 700;
        $actual = $arr[1][1];
        $this->assertEquals($expected, $actual);
        $this->assertTrue($arr[2]);
    }//end testParseFontParametersWithItalics()

    /**
     * Test parsing a font string without italics.
     *
     * @return void
     */
    public function testParseFontParametersWithoutItalics(): void
    {
        $testString = 'Noto+Sans:400,700';
        $arr = \AWonderPHP\AWMFontBuilder::parseFontParameters($testString);
        $expected = 'Noto+Sans';
        $actual = $arr[0];
        $this->assertEquals($expected, $actual);
        $expected = 2;
        $actual = count($arr[1]);
        $expected = 400;
        $actual = $arr[1][0];
        $this->assertEquals($expected, $actual);
        $expected = 700;
        $actual = $arr[1][1];
        $this->assertEquals($expected, $actual);
        $this->assertFalse($arr[2]);
    }//end testParseFontParametersWithoutItalics()
}//end class

?>