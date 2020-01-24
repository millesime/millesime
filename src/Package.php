<?php

namespace Millesime;

use \SplFileInfo;
use \Phar;

/**
 * Final built package.
 * 
 * @author Thomas Gasc <thomas@gasc.fr>
 */
class Package
{
    private SplFileInfo $file;
    private bool $needPublicKey;

    /**
     * Constructor
     *
     * @param SplFileInfo $file
     */
    public function __construct(SplFileInfo $file, bool $needPublicKey = false)
    {
        $this->file = $file;
        $this->needPublicKey = $needPublicKey;
    }

    public function getName() : string
    {
        return $this->file->getFilename();
    }

    /**
     * Get the Phar archive.
     *
     * @return Phar
     */
    public function open() : Phar
    {
        return new Phar($this->file->getPathname());
    }

    public function needPublicKey() : bool
    {
        return $this->needPublicKey;
    }
}
