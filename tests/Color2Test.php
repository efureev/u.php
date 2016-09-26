<?php

use efureev\uColor;


class Color2Test extends PHPUnit_Framework_TestCase
{

    public function testOne()
    {
        $this->assertEquals('0,255,0', uColor::hex2RGB('00FF00', 1));
        $this->assertEquals('0|255|0', uColor::hex2RGB('00FF00', 1, '|'));
        $this->assertEquals('0,0,0', uColor::hex2RGB('000', 1));
    }
}
