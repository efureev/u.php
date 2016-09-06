<?php

use uPhp\u;

class BaseTest extends PHPUnit_Framework_TestCase
{

    public function test_htmlentities()
    {
        $this->assertEquals('One &amp; Two', u::htmlentities('One & Two'));
        $this->assertEquals('One &amp; Two', u::htmlentities('One &amp; Two', true));
    }

    public function test_htmlspecialchars()
    {
        $this->assertEquals('One &amp; Two', u::htmlspecialchars('One & Two'));
        $this->assertEquals('One &amp; Two', u::htmlspecialchars('One &amp; Two', true));
    }
}
