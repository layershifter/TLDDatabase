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

use LayerShifter\TLDDatabase\Http\AdapterInterface;

/**
 * Dummy adapter for update tests.
 */
class DummyAdapter implements AdapterInterface
{

    /**
     * @inheritdoc
     */
    public function get($url)
    {
        return preg_split('/[\n\r]+/', file_get_contents(__DIR__ . '/sample-list.txt'));
    }
}