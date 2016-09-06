<?php

use uPhp\u;


class ValidateTest extends PHPUnit_Framework_TestCase {

    public function test_isDateFormat() {
        $this->assertTrue(u::isDateFormat('2016-12-20'));
        $this->assertTrue(u::isDateFormat('2016-12-20 12:10:01'));

        $this->assertFalse(u::isDateFormat('2016-32-02'));
        $this->assertFalse(u::isDateFormat('2016-01-02 12:00'));
    }
}
