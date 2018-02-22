<?php

use efureev\uString;
use PHPUnit\Framework\TestCase;

class String2Test extends TestCase
{

    protected static $string = 'Example string';

    public function testOne()
    {
        $this->assertTrue(uString::isStartStr(self::$string, 'Example'));
        $this->assertFalse(uString::isStartStr(self::$string, ''));
        $this->assertFalse(uString::isStartStr(self::$string, 'Examples'));

        $this->assertTrue(uString::endsWith(self::$string, 'string'));
    }
}
