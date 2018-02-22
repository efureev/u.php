<?php

use efureev\uList;
use PHPUnit\Framework\TestCase;

class List2Test extends TestCase
{

    public function testOne()
    {
        $list = uList::rangeList(3, 10);

        $this->assertEquals(8, count($list));

        $list = uList::rangeList(1, 100, 10);
        $this->assertEquals(10, count($list));

        $list = uList::rangeList(10, 3);
        $this->assertEquals(8, count($list));
        $this->assertEquals(10, $list[0]);
        $this->assertEquals(3, $list[7]);

        $list = uList::rangeList(10, 10);

        $this->assertCount(1, $list);
        $this->assertEquals(10, $list[0]);
    }
}
