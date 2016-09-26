<?php

use efureev\uFile;


class File2Test extends PHPUnit_Framework_TestCase
{

    public function testOne()
    {
        $file = __DIR__ . '/phpunit.xml';
        $this->assertEquals('xml', uFile::getExt($file));
        $this->assertEquals('374 B', uFile::size(dirname($file)));
        $this->assertEquals(374, filesize(dirname($file)));
    }
}
