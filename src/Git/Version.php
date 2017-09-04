<?php

namespace Millesime\Git;

use Symfony\Component\Process\ProcessBuilder;

class Version
{
    public function __construct(ProcessBuilder $builder = null)
    {
        $this->builder = $builder ?: new ProcessBuilder();
    }

    public function resolve($path)
    {
        $gitdir = $path.DIRECTORY_SEPARATOR.'.git';

        if (!file_exists($gitdir)) {
            return 'dev-master';
        }

        $this->builder->add("--git-dir={$gitdir}");

        if ($commit = $this->getCommit()) {
            if ($tag = $this->getTag($commit)) {
                return $tag;
            }
        }

        return $this->getBranch();
    }

    private function getCommit()
    {
        $git_show = $this->builder->getProcess();
        $git_show->setCommandLine("git show -q");
        $git_show->run();

        $commit = substr(explode(PHP_EOL, $git_show->getOutput())[0], 7);

        return $commit;
    }

    private function getTag($commit)
    {
        $git_tag = $this->builder->getProcess();
        $git_tag->setCommandLine("git tag --contains {$commit}");
        $git_tag->run();

        $tag = explode(PHP_EOL, $git_tag->getOutput())[0];

        return $tag;
    }

    private function getBranch()
    {
        $git_branch = $this->builder->getProcess();
        $git_branch->setCommandLine("git branch");
        $git_branch->run();

        $branch = explode(PHP_EOL, $git_branch->getOutput());
        foreach ($branch as $b) {
            if (substr($b, 0, 1)==='*') {
                $branch = trim($b, '* ');
            }
        }

        return $branch;
    }
}
