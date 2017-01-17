<?php

namespace Methylbro\Compiler\Git;

class Version
{
    private $version;

    public function __construct($path)
    {
        $version = 'dev-master';
        $gitdir = $path.DIRECTORY_SEPARATOR.'.git';
        
        if (file_exists($gitdir)) {
            $c = "git --git-dir={$gitdir} show -q";
            $show = shell_exec($c);
            $commit = explode(PHP_EOL, $show)[0];
            $commit = substr($commit, 7);

            if ($commit) {
                $c = "git --git-dir={$gitdir} tag --contains {$commit}";
                $tags = shell_exec($c);
                $tags = explode(PHP_EOL, $tags);
                $tag = $tags[0];

                if ($tag) {
                    $version = $tag;
                } else {
                    $c = "git --git-dir={$gitdir} branch";
                    $branch = shell_exec($c);
                    $branch = explode(PHP_EOL, $branch);
                    foreach ($branch as $b) {
                        if (substr($b, 0, 1)==='*') {
                            $branch = trim($b, '* ');
                        }
                    }
                    $version = 'dev-'.$branch;
                }
            }
        }

        $this->version = $version;
    }

    public function getVersion()
    {
        return $this->version;
    }
}
