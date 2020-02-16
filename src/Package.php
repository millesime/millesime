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

    /**
     * Get the Phar archive.
     *
     * @return Phar
     */
    public function open() : Phar
    {
        return new Phar($this->file->getPathname());
    }

    public function info() : SplFileInfo
    {
        return $this->file;
    }

    public function getName() : string
    {
        return $this->file->getFilename();
    }

    public function needPublicKey() : bool
    {
        return $this->needPublicKey;
    }
}
