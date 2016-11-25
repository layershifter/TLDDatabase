<?php
/**
 * TLDDatabase: Abstraction for Public Suffix List in PHP.
 *
 * @link      https://github.com/layershifter/TLDDatabase
 *
 * @copyright Copyright (c) 2016, Alexander Fedyashov
 * @license   https://raw.githubusercontent.com/layershifter/TLDDatabase/master/LICENSE Apache 2.0 License
 */

namespace LayerShifter\TLDDatabase\Tests;

use LayerShifter\TLDDatabase\Exceptions\ParserException;
use LayerShifter\TLDDatabase\Parser;
use LayerShifter\TLDDatabase\Store;

/**
 * Test for Parser class.
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test constructor.
     *
     * @return void
     */
    public function testConstructor()
    {
        $parser = new Parser([]);

        self::assertAttributeEquals([], 'lines', $parser);
    }

    /**
     * Test wrong input exception.
     *
     * @return void
     */
    public function testConstructorWrongTypeException()
    {
        $this->setExpectedException(ParserException::class, 'Invalid argument type, expecting array');

        new Parser('test');
    }

    /**
     * Test empty input exception.
     *
     * @return void
     */
    public function testParseEmptyException()
    {
        $this->setExpectedException(ParserException::class, 'Input array of lines does not have any valid suffix');

        $parser = new Parser([]);
        $parser->parse();
    }

    /**
     * Test on real data.
     *
     * @return void
     */
    public function testParse()
    {
        $sampleData = preg_split('/[\n\r]+/', file_get_contents(__DIR__ . '/sample-list.txt'));

        $parser = new Parser($sampleData);
        $result = $parser->parse();

        // Basic test.

        self::assertInternalType('array', $result);
        self::assertCount(7, $result);

        // Test entries:
        // - normal;
        // - IDN;
        // - private.

        self::assertArrayHasKey('ac', $result);
        self::assertArrayHasKey('com.ac', $result);
        self::assertArrayHasKey('ею', $result);
        self::assertArrayHasKey('გე', $result);
        self::assertArrayHasKey('*.compute.estate', $result);
        self::assertArrayHasKey('*.alces.network', $result);
        self::assertArrayHasKey('cloudfront.net', $result);
        self::assertArrayNotHasKey('com', $result);

        // Test proper assignment of type.

        self::assertEquals(Store::TYPE_ICCAN, $result['ac']);
        self::assertEquals(Store::TYPE_ICCAN, $result['com.ac']);
        self::assertEquals(Store::TYPE_PRIVATE, $result['*.compute.estate']);
        self::assertEquals(Store::TYPE_PRIVATE, $result['cloudfront.net']);
    }
}
