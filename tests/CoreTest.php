<?php

use uPhp\u;
/**
 * Class CoreTest
 */
class CoreTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers uPhp\u::autoload
     */
    public function testCoreLoaded() {
        $this->assertTrue(class_exists('uPhp\u'));
    }

    public function testVersion() {
        $this->assertEquals(u::version(),'0.1.0');
    }

    public function test__toString() {
        $this->assertEquals(new u(),'0.1.0');
    }
}
