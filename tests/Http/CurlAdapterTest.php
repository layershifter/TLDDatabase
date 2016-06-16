<?php
/**
 * TLDDatabase: Abstraction for Public Suffix List in PHP.
 *
 * @link      https://github.com/layershifter/TLDDatabase
 *
 * @copyright Copyright (c) 2016, Alexander Fedyashov
 * @license   https://raw.githubusercontent.com/layershifter/TLDDatabase/master/LICENSE Apache 2.0 License
 */

namespace Layershifter\TLDDatabase\Tests\Http;

use Layershifter\TLDDatabase\Exceptions\HttpException;
use Layershifter\TLDDatabase\Http\AdapterInterface;
use Layershifter\TLDDatabase\Http\CurlAdapter;

/**
 * Class CurlAdapterTest
 * @package Layerschifter\TLDDatabase\Tests\Http
 */
class CurlAdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     *
     */
    protected function setUp()
    {
        $this->adapter = new CurlAdapter();

        parent::setUp();
    }


    /**
     *
     */
    public function getTest()
    {
        $this->setExpectedException(HttpException::class);
        $this->adapter->get('http://google.google');
    }
}
