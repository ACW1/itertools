<?php
/**
 * @author Boudewijn Schoon <boudewijn@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\ItertoolsTest\reductions;

use Zicht\Itertools;
use Zicht\Itertools\reductions;

/**
 * Class MaxTest
 *
 * @package Zicht\ItertoolsTest\reductions
 */
class MaxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test good arguments
     *
     * @param array $data
     * @param mixed $expected
     *
     * @dataProvider goodArgumentProvider
     */
    public function testGoodArguments(array $data, $expected)
    {
        $closure = reductions\max();
        $this->assertInstanceOf('\Closure', $closure);
        $this->assertEquals($expected, Itertools\iterable($data)->reduce($closure));
    }

    /**
     * Provides sequences with expected results
     *
     * @return array
     */
    public function goodArgumentProvider()
    {
        return [
            [[1, 2, 3], 3],
            [[1.5, -1.5, 1.5], 1.5],
            [['1.5', '1.0', '1.0'], 1.5],

            // mixed types
            [[1, '-1.0'], 1.0],
        ];
    }

    /**
     * Test invalid arguments
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @dataProvider badArgumentProvider
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidArguments($a, $b)
    {
        $closure = reductions\max();
        $this->assertInstanceOf('\Closure', $closure);
        $closure($a, $b);
    }

    /**
     * Provides arguments that should result in \InvalidArgumentException
     */
    public function badArgumentProvider()
    {
        return [
            // test argument $a
            [null, 42],
            [true, 42],
            ['', 42],
            [[1, 2, 3], 42],
            [(object)[1, 2, 3], 42],

            // test argument $b
            [42, null],
            [42, true],
            [42, ''],
            [42, [1, 2, 3]],
            [42, (object)[1, 2, 3]],
        ];
    }

    /**
     * Test get_reduction
     *
     * @param array $arguments
     * @param array $data
     * @param mixed $expected
     *
     * @dataProvider goodSequenceProvider
     */
    public function testGetReduction(array $arguments, array $data, $expected)
    {
        $closure = call_user_func_array('\Zicht\Itertools\reductions\get_reduction', $arguments);
        $this->assertInstanceOf('\Closure', $closure);
        $this->assertEquals($expected, Itertools\iterable($data)->reduce($closure));
    }

    /**
     * Test deprecated getReduction
     *
     * @param array $arguments
     * @param array $data
     * @param mixed $expected
     *
     * @dataProvider goodSequenceProvider
     */
    public function testDeprecatedGetReduction(array $arguments, array $data, $expected)
    {
        $closure = call_user_func_array('\Zicht\Itertools\reductions\getReduction', $arguments);
        $this->assertInstanceOf('\Closure', $closure);
        $this->assertEquals($expected, Itertools\iterable($data)->reduce($closure));
    }

    /**
     * Provides tests
     *
     * @return array
     */
    public function goodSequenceProvider()
    {
        return [
            [['max'], [1, -2, 3], 3],
        ];
    }
}
