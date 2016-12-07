<?php
/**
 * @author Boudewijn Schoon <boudewijn@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\ItertoolsTest\reductions;

use PHPUnit_Framework_TestCase;
use Zicht\Itertools as iter;
use Zicht\Itertools\reductions;

/**
 * Class ChainTest
 *
 * @package Zicht\ItertoolsTest\reductions
 */
class ChainTest extends PHPUnit_Framework_TestCase
{
    /**
     * Simple test
     */
    public function test()
    {
        $lists = [['a' => 1, 'b' => 2, 'c' => 3], ['d' => 4, 'e' => 5, 'f' => 6], ['g' => 7, 'h' => 8, 'i' => 9]];
        $result = iter\iterable($lists)->reduce(reductions\chain());
        $this->assertInstanceOf(iter\lib\ChainIterator::class, $result);
        $this->assertEquals(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i'], $result->keys());
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], $result->values());
    }
}
