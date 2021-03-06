<?php
/**
 * @author Boudewijn Schoon <boudewijn@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\ItertoolsTest\mappings;

use Zicht\Itertools;
use Zicht\Itertools\mappings;

/**
 * Class UpperTest
 *
 * @package Zicht\ItertoolsTest\mappings
 */
class UpperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Simple test
     */
    public function test()
    {
        $data = [
            'FOO',
            'key 1' => 'Bar',
            'key 2' => 'mOo',
            'milk',
        ];

        $expected = ['FOO', 'key 1' => 'BAR', 'key 2' => 'MOO', 'MILK'];

        $closure = mappings\upper();
        $this->assertEquals($expected, Itertools\map($closure, $data)->toArray());
    }
}
