<?php

namespace Millesime\Exception;

class ManifestContainsInvalidJson extends \Exception
{
	const MESSAGE = 'Manifest contains invalid json';

    public string $json;
    public string $reason;

    public function __construct(string $json, string $reason)
    {
        $this->json = $json;
        $this->reason = $reason;

        parent::__construct(self::MESSAGE);
    }
}
