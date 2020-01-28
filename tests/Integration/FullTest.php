<?php

use PHPUnit\Framework\TestCase;

use Millesime\Millesime;

class FullTest extends TestCase
{
    protected function copy_dir($src, $dst)
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

    public function testFull()
    {
        $src = __DIR__.'/_project';
        $dest = sys_get_temp_dir().DIRECTORY_SEPARATOR.uniqid();

        $this->copy_dir($src, $dest);

        $millesime = new Millesime();
        $packages = $millesime($dest);

        $this->assertTrue(file_exists($dest.'/test-millesime-1.phar'));
        $this->assertTrue(file_exists($dest.'/test-millesime-2.phar'));
    }
}