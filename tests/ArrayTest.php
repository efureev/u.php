<?php

use uPhp\u;


class ArrayTest extends PHPUnit_Framework_TestCase
{

    public function testArrayClean()
    {
        $input = ['a', 'b', '', null, '0', false, 0];
        $expect = ['a', 'b'];
        $input = u::arrayClean($input);
        $this->assertEquals($expect, $input);
        $this->assertNotEmpty($input);
    }

    public function testIsIndexed()
    {
        $wKeys = ['a' => 1, 'b' => 'b2', 'v' => null];
        $wKeys2 = [0 => 1, 1 => 'b2', 3 => null];
        $wKeys3 = [0 => 1, 1 => 'b2', 3 => null, 6 => 12];
        $woKeys = ['a', 'b', '', null, '0', false, 0];
        $emptyKeys = [];
        $this->assertFalse(u::isIndexed($wKeys));
        $this->assertTrue(u::isIndexed($wKeys2));
        $this->assertTrue(u::isIndexed($wKeys3));
        $this->assertFalse(u::isIndexed($wKeys2, true));
        $this->assertTrue(u::isIndexed($woKeys));
        $this->assertTrue(u::isIndexed($emptyKeys));
    }

    public function testIsAssociative()
    {
        $wKeys = ['a' => 1, 'b' => 'b2', 'v' => null];
        $wKeys2 = [0 => 1, 1 => 'b2', 3 => null];
        $wKeys3 = [0 => 1, 1 => 'b2', 3 => null, 6 => 12];
        $woKeys = ['a', 'b', '', null, '0', false, 0];
        $emptyKeys = [];
        $this->assertTrue(u::isAssociative($wKeys));
        $this->assertFalse(u::isAssociative($wKeys2));
        $this->assertFalse(u::isAssociative($wKeys3));
        $this->assertTrue(u::isAssociative($wKeys, false));
        $this->assertFalse(u::isAssociative($wKeys2, false));
        $this->assertFalse(u::isAssociative($woKeys));
        $this->assertFalse(u::isAssociative($emptyKeys));
    }

}
