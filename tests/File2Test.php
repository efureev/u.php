<?php

use efureev\uFile;
use PHPUnit\Framework\TestCase;

class File2Test extends TestCase
{

    public function testOne()
    {
        $path = dirname(__FILE__);
        $file = $path . '/testFile.txt';
        $this->assertEquals('txt', uFile::getExt($file));
        $this->assertEquals('369 B', uFile::size($file));
        $this->assertEquals(369, filesize($file));
    }
}
