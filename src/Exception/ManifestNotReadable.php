<?php

namespace Millesime\Exception;

use \SplFileInfo;

class ManifestNotReadable extends \Exception
{
    const MESSAGE = 'Manifest not readable';

    public SplFileInfo $jsonFile;

    public function __construct(SplFileInfo $jsonFile)
    {
        $this->jsonFile = $jsonFile;

        parent::__construct(self::MESSAGE);
    }
}
