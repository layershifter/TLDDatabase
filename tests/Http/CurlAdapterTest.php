<?php
/**
 * TLDDatabase: Abstraction for Public Suffix List in PHP.
 *
 * @link      https://github.com/layershifter/TLDDatabase
 *
 * @copyright Copyright (c) 2016, Alexander Fedyashov
 * @license   https://raw.githubusercontent.com/layershifter/TLDDatabase/master/LICENSE Apache 2.0 License
 */

namespace LayerShifter\TLDDatabase\Tests\Http;

use LayerShifter\TLDDatabase\Exceptions\HttpException;
use LayerShifter\TLDDatabase\Http\AdapterInterface;
use LayerShifter\TLDDatabase\Http\CurlAdapter;

/**
 * Test for Http\CurlAdapter class.
 */
class CurlAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AdapterInterface Object with CurlAdapter
     */
    private $adapter;

    /**
     * Bootstrap method.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->adapter = new CurlAdapter();

        parent::setUp();
    }

    /**
     * Test interface implementing.
     *
     * @return void
     */
    public function testImplements()
    {
        self::assertInstanceOf(AdapterInterface::class, new CurlAdapter());
    }

    /**
     * Test for failed response with unresolved domain.
     *
     * @return void
     */
    public function testGetDomainException()
    {
        $this->setExpectedException(HttpException::class, 'Get cURL error while fetching PSL file:');
        $this->adapter->get('http://google.google');
    }

    /**
     * Test for failed response with code.
     *
     * @return void
     */
    public function testGetCodeException()
    {
        $this->setExpectedException(HttpException::class, 'Get invalid HTTP');
        $this->adapter->get('http://google.com/404.html');
    }

    /**
     * Test for valid response.
     *
     * @return void
     */
    public function testGet()
    {
        $validResponse = $this->adapter->get('http://www.google.com/robots.txt');

        self::assertInternalType('array', $validResponse);
        self::assertGreaterThan(0, count($validResponse));
        self::assertArrayHasKey(0, $validResponse);
    }

    /**
     * Test for valid response.
     *
     * @return void
     */
    public function testGetForWindows()
    {
        if (!defined('PHP_WINDOWS_VERSION_MAJOR')) {
            define('PHP_WINDOWS_VERSION_MAJOR', 10);
        }

        $validResponse = $this->adapter->get('http://www.google.com/robots.txt');

        self::assertInternalType('array', $validResponse);
        self::assertGreaterThan(0, count($validResponse));
        self::assertArrayHasKey(0, $validResponse);
    }
}
