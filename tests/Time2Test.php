<?php

use efureev\uTime;


class Time2Test extends PHPUnit_Framework_TestCase
{

    public function testOne()
    {
        $list30 = uTime::timeList();
        $list5 = uTime::timeList(5);

        $this->assertEquals('02:30', $list30['150']);
        $this->assertEquals('08:30', $list5['510']);
    }
}
