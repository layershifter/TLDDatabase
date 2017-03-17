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

use LayerShifter\TLDDatabase\Exceptions\IOException;
use LayerShifter\TLDDatabase\Exceptions\StoreException;
use LayerShifter\TLDDatabase\Store;

/**
 * Tests for Store class.
 */
class StoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Store Object of Store class.
     */
    private $store;

    /**
     * Bootstrap for tests.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->store = new Store(__DIR__ . '/sample-database.php');

        parent::setUp();
    }

    /**
     * Test for constructor() method.
     *
     * @return void
     */
    public function testConstructor()
    {
        new Store();
    }

    /**
     * Test for constructor() method.
     *
     * @return void
     */
    public function testConstructorCustom()
    {
        $databaseFile = __DIR__ . '/sample-database.php';

        $store = new Store($databaseFile);

        self::assertAttributeEquals(require $databaseFile, 'suffixes', $store);
        self::assertAttributeCount(4, 'suffixes', $store);
    }

    /**
     * Test for constructor() method exception.
     *
     * @return void
     */
    public function testConstructorNotExistFile()
    {
        $this->setExpectedException(IOException::class, 'does not exists');
        new Store(__DIR__ . '/test');
    }

    /**
     * Test for constructor() method exception.
     *
     * @return void
     */
    public function testConstructorWrongFile()
    {
        $this->setExpectedException(IOException::class, 'is seriously malformed');
        new Store(__DIR__ . '/sample-list.txt');
    }

    /**
     * Test for isExists() method.
     *
     * @return void
     */
    public function testIsExists()
    {
        self::assertTrue($this->store->isExists('ac'));
        self::assertTrue($this->store->isExists('com.ac'));
        self::assertTrue($this->store->isExists('佛山'));
        self::assertTrue($this->store->isExists('appspot.com'));

        self::assertFalse($this->store->isExists('佛'));
        self::assertFalse($this->store->isExists('com'));
    }

    /**
     * Test for getType() method.
     *
     * @return void
     */
    public function testGetType()
    {
        self::assertEquals(Store::TYPE_ICANN, $this->store->getType('ac'));
        self::assertEquals(Store::TYPE_ICANN, $this->store->getType('com.ac'));
        self::assertEquals(Store::TYPE_ICANN, $this->store->getType('佛山'));

        self::assertEquals(Store::TYPE_PRIVATE, $this->store->getType('appspot.com'));
    }

    /**
     * Test for getType() method's exception.
     *
     * @return void
     */
    public function testGetTypeException()
    {
        $this->setExpectedException(StoreException::class, 'does not exists in database');
        $this->store->getType('com');
    }

    /**
     * Test for isICANN() method.
     *
     * @return void
     */
    public function testIsICANN()
    {
        self::assertTrue($this->store->isICANN('ac'));
        self::assertTrue($this->store->isICANN('com.ac'));
        self::assertTrue($this->store->isICANN('佛山'));

        self::assertFalse($this->store->isICANN('appspot.com'));
    }

    /**
     * Test for isPrivate() method.
     *
     * @return void
     */
    public function testIsPrivate()
    {
        self::assertFalse($this->store->isPrivate('ac'));
        self::assertFalse($this->store->isPrivate('com.ac'));
        self::assertFalse($this->store->isPrivate('佛山'));

        self::assertTrue($this->store->isPrivate('appspot.com'));
    }
}
