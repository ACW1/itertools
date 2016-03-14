<?php

namespace Zicht\ItertoolsTest;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class ChainTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider goodSequenceProvider
     */
    public function testGoodChain(array $arguments, array $expected)
    {
        $iterator = call_user_func_array('\Zicht\Itertools\chain', $arguments);
        $this->assertInstanceOf('\Zicht\Itertools\lib\ChainIterator', $iterator);

        foreach ($expected as $key => $value) {
            $this->assertTrue($iterator->valid(), 'Failure in $iterator->value()');
            $this->assertEquals($key, $iterator->key(), 'Failure in $iterator->key()');
            $this->assertEquals($value, $iterator->current(), 'Failure in $iterator->current()');
            $iterator->next();
        }

        $this->assertFalse($iterator->valid());
    }

    /**
     * @expectedException InvalidArgumentException
     * @dataProvider badArgumentProvider
     */
    public function testBadArgument($iterables)
    {
        $iterator = call_user_func_array('\Zicht\Itertools\chain', $iterables);
    }

    public function goodSequenceProvider()
    {
        return array(
            array(
                array(array(1, 2, 3), array(4, 5, 6), array(7, 8, 9)),
                array(1, 2, 3, 4, 5, 6, 7, 8, 9)),
            array(
                array(array(1, 2, 3), array(), array(7, 8, 9)),
                array(1, 2, 3, 7, 8, 9)),
            array(
                array(array(1, 2, 3), array()),
                array(1, 2, 3)),
            array(
                array(array(), array(4, 5, 6)),
                array(4, 5, 6)),
            array(
                array(),
                array()),
            // todo: iterator
            // todo: strings
        );
    }

    public function badArgumentProvider()
    {
        return array(
            array(array(1)),
            array(array(1.0)),
            array(array(null)),
        );
    }
}
