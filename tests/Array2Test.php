<?php

use efureev\uArray;
use PHPUnit\Framework\TestCase;

class Array2Test extends TestCase
{

    public function testClean()
    {
        $input = ['a', 'b', '', null, '0', false, 0];
        $expect = ['a', 'b'];
        $input = uArray::clean($input);
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
        $this->assertFalse(uArray::isIndexed($wKeys));
        $this->assertTrue(uArray::isIndexed($wKeys2));
        $this->assertTrue(uArray::isIndexed($wKeys3));
        $this->assertFalse(uArray::isIndexed($wKeys2, true));
        $this->assertTrue(uArray::isIndexed($woKeys));
        $this->assertTrue(uArray::isIndexed($emptyKeys));
    }

    public function testIsAssociative()
    {
        $wKeys = ['a' => 1, 'b' => 'b2', 'v' => null];
        $wKeys2 = [0 => 1, 1 => 'b2', 3 => null];
        $wKeys3 = [0 => 1, 1 => 'b2', 3 => null, 6 => 12];
        $woKeys = ['a', 'b', '', null, '0', false, 0];
        $emptyKeys = [];
        $this->assertTrue(uArray::isAssociative($wKeys));
        $this->assertFalse(uArray::isAssociative($wKeys2));
        $this->assertFalse(uArray::isAssociative($wKeys3));
        $this->assertTrue(uArray::isAssociative($wKeys, false));
        $this->assertFalse(uArray::isAssociative($wKeys2, false));
        $this->assertFalse(uArray::isAssociative($woKeys));
        $this->assertFalse(uArray::isAssociative($emptyKeys));
    }


    public function testRemoveValue()
    {
        $wKeys = ['a' => 1, 'b' => 'b2', 'v' => null];

        $this->assertEquals(['b' => 'b2', 'v' => null], uArray::removeValue($wKeys, 1));
        $this->assertEquals(['b' => 'b2', 'v' => null], uArray::removeValue($wKeys, 1));
        $this->assertEquals(['a' => 1, 'b' => 'b2'], uArray::removeValue($wKeys, null));
        $this->assertEquals(['a' => 1, 'b' => 'b2'], uArray::removeValue($wKeys, ''));

    }

    public function testExist()
    {
        $data = [
            'k0'                      => 'v0',
            'k1'                      => [
                'k1-1' => 'v1-1'
            ],
            'complex_[name]_!@#$&%*^' => 'complex',
            'k2'                      => 'string',
            ''                        => 'mes',
        ];
        $this->assertTrue(uArray::exists('k0', $data)); // returns: true
        $this->assertTrue(uArray::exists('', $data));

    }

    public function testSave()
    {
        $data = [
            'k2' => 'string'
        ];

        $this->assertTrue(uArray::save('k0', $data, 'v0')); // returns: true, save as 'k0' => 'v0'
        $this->assertTrue(uArray::save('[k1][k1-1]', $data, 'v1-1'));   // returns: true, save as 'k1' => ['k1-1' => 'v1-1']
        $this->assertFalse(uArray::save('[k2][2]', $data, 'p')); // returns: false, can't save value to string

        $this->assertTrue(uArray::save('', $data, 'Empty'));

        $this->assertTrue(uArray::save('[]', $data, 'Brackets 1'));
        $this->assertTrue(uArray::save('[]', $data, 'Brackets 2'));

// Broken key names
        $this->assertFalse(uArray::save('k3[', $data, 'v3')); // returns: false, can't save, bad syntax
        $this->assertTrue(uArray::save('["k4["]', $data, 'v4')); // returns: true, save as 'k4[' => 'v4'
        $this->assertFalse(uArray::save('"k4["', $data, 'v4')); // returns: false, can't save, bad syntax

// Append
        $this->assertTrue(uArray::save('k5', $data, [])); // returns: true, create array 'k5' => []
        $this->assertTrue(uArray::save('k5[]', $data, 'v5-0')); // returns: true, append value to exists array 'k5' => [ 'v5-0' ]
        $this->assertTrue(uArray::isIndexed(uArray::get('k5', $data)));
        $this->assertTrue(uArray::save('k6[k6-1][]', $data, 'v6-1-0')); // returns: true, save as 'k6' => [ 'k6-1' => [ 'v6-1-0' ] ]
        $this->assertTrue(uArray::isIndexed(uArray::get('k6[k6-1]', $data)));
        $this->assertTrue(uArray::isAssociative(uArray::get('k6', $data)));

// Replace if not exists
        $this->assertFalse(uArray::save('k2', $data, 'something', false)); // returns false, value not replaced because value is exists
    }

    public function testDelete()
    {
        $data = [
            'k0'                      => 'v0',
            'k1'                      => [
                'k1-1' => 'v1-1'
            ],
            'complex_[name]_!@#$&%*^' => 'complex',
            ''                        => 'empty'
        ];

        $this->assertTrue(uArray::delete('k0', $data)); // returns: true, delete element from array
        $this->assertFalse(uArray::delete('k9', $data));

        $this->assertTrue(uArray::delete('[k1][k1-1]', $data)); // returns: true, delete element from array
        $this->assertFalse(uArray::delete('[k1][k1-2]', $data));

        $this->assertTrue(uArray::delete('["complex_[name]_!@#$&%*^"]', $data)); // returns: true, delete element from array

        $this->assertTrue(uArray::delete('', $data));

        $this->assertFalse(uArray::delete('[]', $data));
    }

    public function testGet()
    {
        $data = [
            'k0'                      => 'v0',
            'k1'                      => [
                'k1-1' => 'v1-1'
            ],
            'complex_[name]_!@#$&%*^' => 'complex',
            'k2'                      => 'string',
        ];

        $value = uArray::get('k0', $data); // returns: 'v0'
        $this->assertEquals('v0', $value);

        $value = uArray::get('k9', $data, '0'); // returns: '0', key isn't exists in array
        $this->assertEquals($value, '0');

        $value = uArray::get('k9', $data); // returns: '0', key isn't exists in array
        $this->assertEquals(null, $value);

        $value = uArray::get('[k1][k1-1]', $data); // returns: 'v1-1'
        $this->assertEquals('v1-1', $value);

        $value = uArray::get('[k1][k1-2]', $data, 'default'); // returns: 'default', key isn't exists in array
        $this->assertEquals('default', $value);

        $value = uArray::get('["complex_[name]_!@#$&%*^"]', $data); // returns: 'complex'
        $this->assertEquals('complex', $value);

        $value = uArray::get('[k2][2]', $data); // returns: null, key isn't exists in array
        $this->assertEquals(null, $value);

        // If you want get a symbol from string value, you may switch off option $ignoreString = false
        $value = uArray::get('[k2][2]', $data, null, false); // returns: 'r'
        $this->assertEquals('r', $value);

        $value = uArray::get('[k2][null]', $data, null, false); // returns: null, offset isn't exists in string
        $this->assertEquals(null, $value);

        $value = uArray::get('', $data); //null
        $this->assertEquals(null, $value);

        $value = uArray::get('', $data, 120);
        $this->assertEquals(120, $value);

        $value = uArray::get('[]', $data, 'default');
        $this->assertEquals('default', $value);

        $data = [
            'k2' => 'string',
            ''   => [
                'str' => 'message'
            ]
        ];

        $value = uArray::get('', $data);
        $this->assertEquals($value['str'], 'message');

    }

    public function testFirst()
    {
        $data = [
            'k0'                      => 'v0',
            'k1'                      => [
                'k1-1' => 'v1-1'
            ],
            'complex_[name]_!@#$&%*^' => 'complex',
            ''                        => 'empty'
        ];
        $firstValue = uArray::first($data);
        $this->assertEquals('v0', $firstValue);

        $data = ['k0', 'k1', 'complex_[name]_!@#$&%*^', 'empty', ''];
        $firstValue = uArray::first($data);
        $this->assertEquals('k0', $firstValue);

    }

    public function testLast()
    {
        $data = [
            'k0'                      => 'v0',
            'k1'                      => [
                'k1-1' => 'v1-1'
            ],
            'complex_[name]_!@#$&%*^' => 'complex',
            ''                        => 'empty'
        ];
        $value = uArray::last($data);
        $this->assertEquals('empty', $value);

        $data = ['k0', 'k1', 'complex_[name]_!@#$&%*^', 'empty', ''];
        $value = uArray::last($data);
        $this->assertEquals('', $value);

    }

    public function testUnique()
    {
        $data = [
            'k0' => 'v0',
            'k1' => 'v3',
            ''   => 'empty',
            '31' => 'v3'
        ];
        $value = uArray::unique($data);
        $this->assertArrayNotHasKey('k1', $value);


        $data = ['k0', 'k1', 'k1', 'complex_[name]_!@#$&%*^', 'empty', '', 'k1', '', 'empty', 'k0', 'k1'];

        $value = uArray::unique($data);

        $this->assertCount(5, $value);
    }

}
