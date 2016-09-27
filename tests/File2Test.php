<?php

use efureev\uFile;


class File2Test extends PHPUnit_Framework_TestCase
{

    public function testOne()
    {
        $path = dirname(__FILE__);
        $file = $path.'/testFile.txt';
        $this->assertEquals('txt', uFile::getExt($file));
        $this->assertEquals('369 B', uFile::size($file));
        $this->assertEquals(369, filesize($file));
    }
}
