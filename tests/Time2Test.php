<?php

use efureev\uTime;
use PHPUnit\Framework\TestCase;

class Time2Test extends TestCase
{

    public function testOne()
    {
        $list30 = uTime::timeList();
        $list5 = uTime::timeList(5);

        $this->assertEquals('02:30', $list30['150']);
        $this->assertEquals('08:30', $list5['510']);
    }
}
