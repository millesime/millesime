<?php

namespace Millesime\Manifest;

use Symfony\Component\Finder\Finder;
use Millesime\Factory\ProcessFactory;

class Loader
{
    private Finder $finder;
    private Hydrator $hydrator;
    private ProcessFactory $processFactory;
    private string $defaultFilename;

    public function __construct(
        Finder $finder,
        Hydrator $hydrator,
        ProcessFactory $processFactory,
        $defaultFilename = 'millesime.json'
    ) {
        $this->finder = $finder;
        $this->hydrator = $hydrator;
        $this->processFactory = $processFactory;
        $this->defaultFilename = $defaultFilename;
    }

    /**
     * @throws Exception\ManifestNotFound
     * @throws Exception\ManifestNotReadable
     * @throws Exception\ManifestContainsInvalidJson
     */
    public function fromFile($workingDirectory) : Manifest
    {
        $files = $this
            ->finder
            ->files()
            ->name($this->defaultFilename)
            ->in($workingDirectory)
            ->depth('== 0');
        ;
        if (!$files->hasResults()) {
            throw new \Millesime\Exception\ManifestNotFound(
                self::MANIFEST,
                $workingDirectory
            );
        }

        /* @var Symfony\Component\Finder\SplFileInfo $file */
        $file = iterator_to_array($files, false)[0];
        if (!$file->isReadable()) {
            throw new \Millesime\Exception\ManifestNotReadable($file);
        }

        $json = $file->getContents();
        $manifest = json_decode($json);
        if(JSON_ERROR_NONE !== json_last_error()) {
            throw new \Millesime\Exception\ManifestContainsInvalidJson(
                $json,
                json_last_error_msg()
            );
        }


        try {
            $process = $this->processFactory->__invoke(
                'git log HEAD -n1 --pretty=%h', 
                $workingDirectory
            );
            $process->mustRun();
            $version = trim($process->getOutput());
        } catch (\Exception $e) {
            $version = 'dev-master';
        }

        $manifest->version = $version;


        try {
            $process = $this->processFactory->__invoke(
                'git log HEAD -n1 --pretty=%ci', 
                $workingDirectory
            );
            $process->mustRun();
            $date = new \DateTime(trim($process->getOutput()));
        } catch (\Exception $e) {
            $date = new \DateTime();
        }

        $manifest->date = $date;

        return $this->hydrator->fromStdClass($manifest);
   }
}
