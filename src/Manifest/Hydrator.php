<?php

namespace Millesime\Manifest;

use Schnittstabil\FinderByConfig\FinderByConfig;

class Hydrator
{
    public function fromStdClass(\stdClass $data) : Manifest
    {
        $packagesInfo = [];

        foreach ($data->packages as $packageData) {

            $signature = null;
            if (property_exists($packageData, 'signature')) {
                $signature = new Signature(
                    $packageData->signature->algorithm,
                    property_exists($packageData->signature, 'public') ? $packageData->signature->public : null,
                    property_exists($packageData->signature, 'private') ? $packageData->signature->private : null
                );
            }

            $packageInfo = new PackageInfo(
                $packageData->name,
                $packageData->finder, 
                property_exists($packageData, 'stub') ? $packageData->stub : '',
                property_exists($packageData, 'web-based') ? $packageData->{'web-based'} : false,
                $signature,
                property_exists($packageData, 'scripts') ? $packageData->scripts : []
            );

            $packagesInfo[] = $packageInfo;
        }

        $manifest = new Manifest(
            $data->project,
            $data->version,
            $data->date,
            $packagesInfo
        );

        return $manifest;
    }
}