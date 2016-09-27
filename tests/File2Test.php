<?php

use efureev\uFile;


class File2Test extends PHPUnit_Framework_TestCase
{

    public function testOne()
    {
        $file = __DIR__ . '/LICENSE.md';
        $this->assertEquals('md', uFile::getExt($file));
        $this->assertEquals('340 B', uFile::size(dirname($file)));
        $this->assertEquals(340, filesize(dirname($file)));
    }
}
