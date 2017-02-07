<?php
/**
 * @author Boudewijn Schoon <boudewijn@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\ItertoolsTest\mappings;

use Zicht\Itertools as iter;

/**
 * Class TypeTest
 *
 * @package Zicht\ItertoolsTest\mappings
 */
class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Simple test
     */
    public function test()
    {
        $data = [
            null,
            1,
            true,
            [],
            '',
            new \Exception('test'),
        ];

        $expected = ['NULL', 'integer', 'boolean', 'array', 'string', 'Exception'];

        $closure = iter\mappings\type();
        $this->assertEquals($expected, iter\iterable($data)->map($closure)->values());
    }

    /**
     * Test with a strategy
     */
    public function testStrategy()
    {
        $data = [
            [
                'key' => 42,
            ],
            [
                'key' => new \Exception('test'),
            ],
        ];

        $expected = ['integer', 'Exception'];

        $closure = iter\mappings\type('key');
        $this->assertEquals($expected, iter\iterable($data)->map($closure)->values());
    }

    /**
     * Test get_mapping
     *
     * @param array $arguments
     * @param array $data
     * @param array $expected
     *
     * @dataProvider goodSequenceProvider
     */
    public function testGetMapping(array $arguments, array $data, array $expected)
    {
        $closure = call_user_func_array('\Zicht\Itertools\mappings\get_mapping', $arguments);
        $this->assertEquals($expected, iter\iterable($data)->map($closure)->toArray());
    }

    /**
     * Test deprecated getMapping
     *
     * @param array $arguments
     * @param array $data
     * @param array $expected
     *
     * @dataProvider goodSequenceProvider
     */
    public function testDeprecatedGetMapping(array $arguments, array $data, array $expected)
    {
        $closure = call_user_func_array('\Zicht\Itertools\mappings\getMapping', $arguments);
        $this->assertEquals($expected, iter\iterable($data)->map($closure)->toArray());
    }

    /**
     * Provides tests
     *
     * @return array
     */
    public function goodSequenceProvider()
    {
        return [
            [['type'], [null, 1, true, [], '', new \Exception('test')], ['NULL', 'integer', 'boolean', 'array', 'string', 'Exception']],
            [['type', 'key'], [['key' => 42], ['key' => new \Exception('test')]], ['integer', 'Exception']],
        ];
    }
}
