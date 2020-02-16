<?php

namespace Millesime\Tests\Integration;

use PHPUnit\Framework\TestCase;

class IntegrationTestCase extends TestCase
{
    const TEST_PROJECT_PATH = __DIR__.'/_project';

    protected function installTestProject($source = self::TEST_PROJECT_PATH)
    {
        $dest = sys_get_temp_dir().DIRECTORY_SEPARATOR.uniqid();
        $this->copy_dir($source, $dest);

        return $dest;
    }

    private function copy_dir($src, $dst)
    {
        $dir = opendir($src);  
        @mkdir($dst);  
        while($file = readdir($dir)) {  
            if (( $file != '.' ) && ( $file != '..' )) {  
                if ( is_dir($src . '/' . $file) ) {  
                    $this->copy_dir($src . '/' . $file, $dst . '/' . $file);  
                } else {  
                    copy($src . '/' . $file, $dst . '/' . $file);  
                }  
            }  
        }        
        closedir($dir); 
    }  
}
