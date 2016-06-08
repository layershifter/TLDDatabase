<?php

namespace Layershifter\TLDDatabase;

use Layershifter\TLDDatabase\Http\Curl;

class Parser
{

    const PUBLIC_SUFFIX_LIST_URL = '';

    private $isIccanSuffix = true;
    private $httpAdapter;
    private $outputFileName;
    private $suffixes = [];

    /**
     * Parser constructor.
     *
     * @param string $outputFileName
     * @param string $httpAdapter
     */
    public function __construct($outputFileName, $httpAdapter = null)
    {
        $this->outputFileName = $outputFileName;

        if (null === $httpAdapter) {
            $this->httpAdapter = new Curl();

            return;
        }

        if (!class_exists($httpAdapter)) {
            // TODO: Exception
        }

        // TODO: Check inheritance of interface
    }

    public function parse()
    {
        foreach ($this->httpAdapter->get() as $line) {

        }
    }
}
