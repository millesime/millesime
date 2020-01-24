<?php

namespace Millesime\Exception;

class ManifestNotFound extends \Exception
{
	const MESSAGE = 'Manifest not found';

	public string $filename;
	public string $path;

    public function __construct(string $filename, string $path = null)
    {
    	$this->filename = $filename;
    	$this->path = $path;

        parent::__construct(self::MESSAGE);
    }
}
