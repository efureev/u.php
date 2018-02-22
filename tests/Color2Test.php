<?php

use efureev\uColor;
use PHPUnit\Framework\TestCase;

class Color2Test extends TestCase
{

    public function testOne()
    {
        $this->assertEquals('0,255,0', uColor::hex2RGB('00FF00', 1));
        $this->assertEquals('0|255|0', uColor::hex2RGB('00FF00', 1, '|'));
        $this->assertEquals('0,0,0', uColor::hex2RGB('000', 1));
    }
}
