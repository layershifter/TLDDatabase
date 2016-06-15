<?php
/**
 * TLDDatabase: Abstraction for Public Suffix List in PHP.
 *
 * @link      https://github.com/layershifter/TLDDatabase
 *
 * @copyright Copyright (c) 2016, Alexander Fedyashov
 * @license   https://raw.githubusercontent.com/layershifter/TLDDatabase/master/LICENSE Apache 2.0 License
 */

namespace LayerShifter\TLDDatabase\Http;

use LayerShifter\TLDDatabase\Exceptions\HttpException;

/**
 * AdapterInterface for HTTP adapters that can be used for fetching Public Suffix List.
 */
interface AdapterInterface
{

    /**
     * AdapterInterface constructor.
     *
     * @param string $url URL of Public Suffix List file.
     *
     * @throws HttpException
     */
    public function __construct($url);

    /**
     * Fetches Public Suffix List file and returns its content as array of strings.
     *
     * @return array|string[]
     *
     * @throws HttpException
     */
    public function get();
}