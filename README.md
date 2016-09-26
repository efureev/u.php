# u.php
_utilites4php_

[![GitHub version](https://badge.fury.io/gh/efureev%2Fu.php.svg)](https://badge.fury.io/gh/efureev%2Fu.php) [![Build Status](https://travis-ci.org/efureev/u.php.svg?branch=master)](https://travis-ci.org/efureev/u.php) [![Dependency Status](https://gemnasium.com/badges/github.com/efureev/u.php.svg)](https://gemnasium.com/github.com/efureev/u.php) ![](https://reposs.herokuapp.com/?path=efureev/u.php) [![Code Climate](https://codeclimate.com/github/efureev/u.php/badges/gpa.svg)](https://codeclimate.com/github/efureev/u.php) [![Test Coverage](https://codeclimate.com/github/efureev/u.php/badges/coverage.svg)](https://codeclimate.com/github/efureev/u.php/coverage)

# Run Tests Example
run all test: `vendor/bin/phpunit`
run test unit: `vendor/bin/phpunit tests/BaseTest`

# Functions

## uFile

- getExt
_Return File Extension_
`getExt($filename)`
```php
uFile::getExt('/var/log/error.log');
uFile::size('/var/log/error.log');
uFile::size('/var/log/error.log', false);
uFile::sizeFormat(323423);
```


## uList

- rangeList
_Return range integer list_
`rangeList($from, $to, $step = 1)`
```php
uList::rangeList(1,4); // 1,2,3,4
uList::rangeList(1,50, 10); // 10,20,30,40,50
uList::rangeList(10, 7); // 10,9,8,7
```

## uTime

- timeList
_Return time list_
`timeList($minutes = 30)`
```php
uTime::timeList();
uTime::timeList(60);
```

## uColor

- hex2RGB
_HEX to RGB_
`hex2RGB($hexStr, $returnAsString = false, $separator = ',')`
```php
uColor::hex2RGB('00FF00', 1) // '0,255,0'
uColor::hex2RGB('000', 1) // '0,0,0'
uColor::hex2RGB('000', 1,'|') // '0|0|0'
uColor::hex2RGB('000') // ['red' => 0,'green' => 0,'blue' => 0]
```

## uArray

- isAssociative
_Associative array or not_
`uArray::isAssociative($array, $allStrings = true)`


- isIndexed
_Indexed array or not_
`uArray::isAssociative($array, $allStrings = true)`


- clean
_Remove empty values from array: FALSE, 0, '0', '', null_
`uArray::clean(array $array)`


- removeValue
_Remove from array by value_
`uArray::removeValue(array $array, $arg, $arg2, ..n)`


- exists
_Checks if the given key exists in the array by a string representation_
`uArray::exists($key, $array)`
```php
$data = [
    'k0' => 'v0',
    'k1' => [
        'k1-1' => 'v1-1'
    ],
    'complex_[name]_!@#$&%*^' => 'complex',
    'k2' => 'string'
];
Arrays::exists('k0', $data); // returns: true
Arrays::exists('k9', $data); // returns: false
Arrays::exists('[k1][k1-1]', $data); // returns: true
Arrays::exists('[k1][k1-2]', $data); // returns: false
Arrays::exists('["complex_[name]_!@#$&%*^"]', $data); // returns: true
Arrays::exists('[k2][2]', $data); // returns: false
```


- save
_Save element to the array by a string representation_
`uArray::save($key, &$array, $value, $replace = true)`
```php
$data = [
    'k2' => 'string'
];

Arrays::save('k0', $data, 'v0'); // returns: true, save as 'k0' => 'v0'
Arrays::save('[k1][k1-1]', $data, 'v1-1'); // returns: true, save as 'k1' => ['k1-1' => 'v1-1']
Arrays::save('[k2][2]', $data, 'p'); // returns: false, can't save value to string

// Broken key names
Arrays::save('k3[', $data, 'v3'); // returns: false, can't save, bad syntax
Arrays::save('["k4["]', $data, 'v4'); // returns: true, save as 'k4[' => 'v4'
Arrays::save('"k4["', $data, 'v4'); // returns: false, can't save, bad syntax

// Append
Arrays::save('k5', $data, []); // returns: true, create array 'k5' => []
Arrays::save('k5[]', $data, 'v5-0'); // returns: true, append value to exists array 'k5' => [ 'v5-0' ]
Arrays::save('k6[k6-1][]', $data, 'v6-1-0'); // returns: true, save as 'k6' => [ 'k6-1' => [ 'v6-1-0' ] ]

// Replace if not exists
Arrays::save('k2', $data, 'something', false); // returns false, value not replaced because value is exists
```


- delete
_Delete element from the array by a string representation_
`uArray::delete($key, &$array)`
```php
$data = [
    'k0' => 'v0',
    'k1' => [
        'k1-1' => 'v1-1'
    ],
    'complex_[name]_!@#$&%*^' => 'complex'
];

Arrays::delete('k0', $data); // returns: true, delete element from array
Arrays::delete('k9', $data); // returns: false

Arrays::delete('[k1][k1-1]', $data); // returns: true, delete element from array
Arrays::delete('[k1][k1-2]', $data); // returns: false

Arrays::delete('["complex_[name]_!@#$&%*^"]', $data); // returns: true, delete element from array
```


- get
_Get element of the array by a string representation_
`uArray::get($key, $array, $default = null, $ignoreString = true)`
```php
$data = [
    'k0' => 'v0',
    'k1' => [
        'k1-1' => 'v1-1'
    ],
    'complex_[name]_!@#$&%*^' => 'complex',
    'k2' => 'string'
];

Arrays::get('k0', $data); // returns: 'v0'
Arrays::get('k9', $data, '0'); // returns: '0', key isn't exists in array

Arrays::get('[k1][k1-1]', $data); // returns: 'v1-1'
Arrays::get('[k1][k1-2]', $data, 'default'); // returns: 'default', key isn't exists in array

Arrays::get('["complex_[name]_!@#$&%*^"]', $data); // returns: 'complex'

Arrays::get('[k2][2]', $data); // returns: null, key isn't exists in array

// If you want get a symbol from string value, you may switch off option $ignoreString = false
Arrays::get('[k2][2]', $data, null, false); // returns: 'r'
Arrays::get('[k2][null]', $data, null, false); // returns: null, offset isn't exists in string
```