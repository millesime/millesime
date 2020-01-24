<?php

namespace Millesime\Manifest;

class Manifest
{
    private string $projectName;
    private string $version;
    private \DateTimeInterface $date;
    private array $packagesInfos = [];

    public function __construct(
        string $projectName,
        string $version,
        \DateTimeInterface $date,
        array $packagesInfos = []
    ) {
        $this->projectName = $projectName;
        $this->version = $version;
        $this->date = $date;
        $this->packagesInfos = $packagesInfos;
    }

    public function getProjectName() : string
    {
        return $this->projectName;
    }

    public function getVersion() : string
    {
        return $this->version;
    }

    public function getDate() : \DateTimeInterface
    {
        return $this->date;
    }

    /** 
     * @return PackageInfo[]
     */
    public function getPackagesInfo() : Iterable
    {
        return $this->packagesInfos;
    }
}