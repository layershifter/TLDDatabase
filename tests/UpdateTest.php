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
use LayerShifter\TLDDatabase\Exceptions\UpdateException;
use LayerShifter\TLDDatabase\Http\CurlAdapter;
use LayerShifter\TLDDatabase\Store;
use LayerShifter\TLDDatabase\Update;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * Test for Update class.
 */
class UpdateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $rootDirectory;

    /**
     * Bootstrap test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->rootDirectory = vfsStream::setup('update');
    }

    /**
     * Test for constructor().
     *
     * @return void
     */
    public function testConstructor()
    {
        new Update();
        new Update(__DIR__ . Store::DATABASE_FILE);
        new Update(__DIR__ . Store::DATABASE_FILE, CurlAdapter::class);
    }

    /**
     * Test for constructor().
     *
     * @return void
     */
    public function testConstructorUndefinedClass()
    {
        $this->setExpectedException(UpdateException::class, 'is not defined');
        new Update(null, 'TestUndefinedClass');
    }

    /**
     * Test for constructor().
     *
     * @return void
     */
    public function testConstructorNotInstanceClass()
    {
        $this->setExpectedException(UpdateException::class, 'is implements adapter interface');
        new Update(null, self::class);
    }

    public function testRun()
    {
        $update = new Update($this->rootDirectory->url() . '/test', DummyAdapter::class);
        $update->run();
    }

    public function testRunExceptionOpen()
    {
        $this->setExpectedException(IOException::class);

        $update = new Update($this->rootDirectory->url() . '/test/fopen', DummyAdapter::class);
        $update->run();
    }

    public function testRunExceptionLock()
    {
        vfsStream::newFile('test')->at($this->rootDirectory);

        $handle = fopen($this->rootDirectory->url() . '/test', 'w+');
        flock($handle, LOCK_EX);

        $this->setExpectedException(IOException::class, 'Cannot obtain lock to output file');

        $update = new Update($this->rootDirectory->url() . '/test', DummyAdapter::class);
        $update->run();

        flock($handle, LOCK_UN);
        fclose($handle);
    }

    public function testRunExceptionWrite()
    {
        vfsStream::setQuota(10);

        $this->setExpectedException(IOException::class, 'Write to output file');

        $update = new Update($this->rootDirectory->url() . '/test', DummyAdapter::class);
        $update->run();
    }
}
