<?php

namespace ItertoolsTest;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class simpleObject2
{
    public function __construct($value)
    {
        $this->prop = $value;
    }

    public function getProp()
    {
        return $this->prop;
    }
}

class GroupbyTest extends PHPUnit_Framework_TestCase
{
    public function testUnsorted()
    {
        $obj = function ($property, $title) {
            return (object)array('prop' => $property, 'title' => $title);
        };

        $list = array($obj('1group', '1A'), $obj('1group', '1B'), $obj('2group', '2A'), $obj('2group', '2B'), $obj('1group', '1C'));

        $iterator = \Itertools\groupby('prop', $list);
        $this->assertInstanceOf('\Itertools\lib\GroupbyIterator', $iterator);
        $iterator->rewind();

        $this->assertTrue($iterator->valid());
        $this->assertEquals('1group', $iterator->key());
        $groupedIterator = $iterator->current();
        $this->assertInstanceOf('\Itertools\lib\GroupedIterator', $groupedIterator);
        $groupedIterator->rewind();
        $this->assertTrue($groupedIterator->valid());
        $this->assertEquals(0, $groupedIterator->key());
        $this->assertEquals($obj('1group', '1A'), $groupedIterator->current());
        $groupedIterator->next();
        $this->assertTrue($groupedIterator->valid());
        $this->assertEquals(1, $groupedIterator->key());
        $this->assertEquals($obj('1group', '1B'), $groupedIterator->current());
        $groupedIterator->next();
        $this->assertFalse($groupedIterator->valid());
        $iterator->next();

        $this->assertTrue($iterator->valid());
        $this->assertEquals('2group', $iterator->key());
        $groupedIterator = $iterator->current();
        $this->assertInstanceOf('\Itertools\lib\GroupedIterator', $groupedIterator);
        $groupedIterator->rewind();
        $this->assertTrue($groupedIterator->valid());
        $this->assertEquals(0, $groupedIterator->key());
        $this->assertEquals($obj('2group', '2A'), $groupedIterator->current());
        $groupedIterator->next();
        $this->assertTrue($groupedIterator->valid());
        $this->assertEquals(1, $groupedIterator->key());
        $this->assertEquals($obj('2group', '2B'), $groupedIterator->current());
        $groupedIterator->next();
        $this->assertFalse($groupedIterator->valid());
        $iterator->next();

        // now this is the repeating key, that *will conflict* when
        // using iterator_to_array($iterator, true)
        $this->assertTrue($iterator->valid());
        $this->assertEquals('1group', $iterator->key());
        $groupedIterator = $iterator->current();
        $this->assertInstanceOf('\Itertools\lib\GroupedIterator', $groupedIterator);
        $groupedIterator->rewind();
        $this->assertTrue($groupedIterator->valid());
        $this->assertEquals(0, $groupedIterator->key());
        $this->assertEquals($obj('1group', '1C'), $groupedIterator->current());
        $groupedIterator->next();
        $this->assertFalse($groupedIterator->valid());
        $iterator->next();

        // nothing left in the iterator
        $this->assertFalse($iterator->valid());
    }

    /**
     * @dataProvider goodSequenceProvider
     */
    public function testGoodKeyCallback(array $arguments, array $expected)
    {
        $iterator = call_user_func_array('\Itertools\groupby', $arguments);
        $this->assertInstanceOf('\Itertools\lib\GroupbyIterator', $iterator);
        $iterator->rewind();

        foreach ($expected as $key => $expectedGroup) {
            $this->assertTrue($iterator->valid());
            $this->assertEquals($key, $iterator->key());
            $groupedIterator = $iterator->current();
            $this->assertInstanceOf('\Itertools\lib\GroupedIterator', $groupedIterator);
            $groupedIterator->rewind();

            foreach ($expectedGroup as $key => $value) {
                $this->assertTrue($groupedIterator->valid());
                $this->assertEquals($key, $groupedIterator->key());
                $this->assertEquals($value, $groupedIterator->current());
                $groupedIterator->next();
            }
            $this->assertFalse($groupedIterator->valid());

            $iterator->next();
        }

        $this->assertFalse($iterator->valid());
    }

    /**
     * @expectedException InvalidArgumentException
     * @dataProvider badArgumentProvider
     */
    public function testBadArgument(array $arguments)
    {
        $iterator = call_user_func_array('\Itertools\groupby', $arguments);
    }

    public function goodSequenceProvider()
    {
        $obj = function ($property, $title) {
            return (object)array('prop' => $property, 'title' => $title);
        };

        return array(
            // callback
            array(
                array(function ($a) { return $a + 10; }, array(1, 2, 2, 3, 3, 3)),
                array(11 => array(1), 12 => array(2, 2), 13 => array(3, 3, 3))),
            // use string to identify array key
            array(
                array('key', array(array('key' => 'k1'), array('key' => 'k2'), array('key' => 'k2'))),
                array('k1' => array(array('key' => 'k1')), 'k2' => array(array('key' => 'k2'), array('key' => 'k2')))),
            array(
                array('key', array(array('key' => 1), array('key' => 2), array('key' => 2))),
                array(1 => array(array('key' => 1)), 2 => array(array('key' => 2), array('key' => 2)))),
            // use string to identify object property
            array(
                array('prop', array(new simpleObject2('p1'), new simpleObject2('p2'), new simpleObject2('p2'))),
                array('p1' => array(new simpleObject2('p1')), 'p2' => array(new simpleObject2('p2'), new simpleObject2('p2')))),
            array(
                array('prop', array(new simpleObject2(1), new simpleObject2(2), new simpleObject2(2))),
                array(1 => array(new simpleObject2(1)), 2 => array(new simpleObject2(2), new simpleObject2(2)))),
            // use string to identify object get method
            array(
                array('getProp', array(new simpleObject2('p1'), new simpleObject2('p2'), new simpleObject2('p2'))),
                array('p1' => array(new simpleObject2('p1')), 'p2' => array(new simpleObject2('p2'), new simpleObject2('p2')))),
            array(
                array('getProp', array(new simpleObject2(1), new simpleObject2(2), new simpleObject2(2))),
                array(1 => array(new simpleObject2(1)), 2 => array(new simpleObject2(2), new simpleObject2(2)))),
         );
    }

    public function badArgumentProvider()
    {
        return array(
            array(array(null, array(1, 2, 3))),
        );
    }
}
