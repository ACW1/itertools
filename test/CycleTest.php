<?php

class CycleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider goodSequenceProvider
     */
    public function testGoodCycle($p, $expected)
    {
        $iterator = iter\cycle($p);
        $this->assertInstanceOf('iter\CycleIterator', $iterator);
        $iterator->rewind();

        foreach ($expected as $key => $value) {
            $this->assertEquals($key, $iterator->key());
            $this->assertEquals($value, $iterator->current());
            $iterator->next();
        }
    }

    /**
     * @expectedException InvalidArgumentException
     * @dataProvider badArgumentProvider
     */
    public function testBadArgument($p)
    {
        $iterator = iter\cycle($p);
    }

    public function goodSequenceProvider()
    {
        return array(
            array(new ArrayIterator(array(0, 1, 2)), array(0, 1, 2, 0, 1, 2, 0)),
            array(array(0, 1, 2), array(0, 1, 2, 0, 1, 2, 0)),
            array(new ArrayIterator(array(0, -1, -2)), array(0, -1, -2, 0, -1, -2, 0)),
            array(array(0, -1, -2), array(0, -1, -2, 0, -1, -2, 0)),
            array(new ArrayIterator(array(3, 4, 5)), array(3, 4, 5, 3, 4, 5, 3)),
            array(array(3, 4, 5), array(3, 4, 5, 3, 4, 5, 3)),
            array(new ArrayIterator(array(-3, -4, -5)), array(-3, -4, -5, -3, -4, -5, -3)),
            array(array(-3, -4, -5), array(-3, -4, -5, -3, -4, -5, -3)),
            array('Foo', array('F', 'o', 'o', 'F', 'o', 'o', 'F')),
            // todo: add unicode string test
        );
    }

    public function badArgumentProvider()
    {
        return array(
            array(123),
            array(1.0),
            array(null),
        );
    }
}