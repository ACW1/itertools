<?php
/**
 * @author Boudewijn Schoon <boudewijn@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Itertools\util;

use ArrayIterator;
use Closure;
use Doctrine\Common\Collections\Collection;
use InvalidArgumentException;
use Iterator;
use Traversable;
use Zicht\Itertools\lib\StringIterator;

class Conversions
{
    /**
     * Transforms anything into an Iterator or throws an InvalidArgumentException
     *
     * > mixedToIterator([1, 2, 3])
     * 1 2 3
     *
     * > mixedToIterator('foo')
     * f o o
     *
     * @param array|string|Iterator $iterable
     * @return Iterator
     */
    public static function mixedToIterator($iterable)
    {
        // NULL is often used to indicate that nothing is there,
        // for robustness we will deal with NULL as it is an empty array
        if (is_null($iterable)) {
            $iterable = new ArrayIterator([]);
        }

        // an array is *not* an instance of Traversable (as it is not an
        // object and hence can not 'implement Traversable')
        if (is_array($iterable)) {
            $iterable = new ArrayIterator($iterable);
        }

        // a string is considered iterable in Python
        if (is_string($iterable)) {
            $iterable = new StringIterator($iterable);
        }

        // todo: add unit tests for Collection
        // a doctrine Collection (i.e. Array or Persistent) is also an iterator
        if ($iterable instanceof Collection) {
            $iterable = $iterable->getIterator();
        }

        // todo: add unit tests for Traversable
        if ($iterable instanceof Traversable and !($iterable instanceof Iterator)) {
            $iterable = new \IteratorIterator($iterable);
        }

        // by now it should be an Iterator, otherwise throw an exception
        if (!($iterable instanceof Iterator)) {
            throw new InvalidArgumentException('Argument $ITERABLE must be a Traversable');
        }

        return $iterable;
    }

    /**
     * Try to transforms something into a Closure that gets a value from $STRATEGY.
     *
     * When $STRATEGY is null the returned Closure behaves like an identity function,
     * i.e. it will return the value that it gets.
     *
     * When $STRATEGY is a string the returned Closure tries to find a properties,
     * methods, or array indexes named by the string.  Multiple property, method,
     * or index names can be separated by a dot.
     * - 'getId'
     * - 'getData.key'
     *
     * When $STRATEGY is callable it is converted into a Closure (see mixedToClosure).
     *
     * @param null|string|Closure
     * @return Closure
     */
    public static function mixedToValueGetter($strategy)
    {
        if (is_null($strategy)) {
            return function ($value) {
                return $value;
            };
        }

        if (is_string($strategy)) {
            $keyParts = explode('.', $strategy);
            $strategy = function ($value) use ($keyParts) {
                foreach ($keyParts as $keyPart) {
                    if (is_object($value)) {
                        // property_exists does not distinguish between public, protected, or private properties, hence we need to use reflection
                        $reflection = new \ReflectionObject($value);
                        if ($reflection->hasProperty($keyPart)) {
                            $property = $reflection->getProperty($keyPart);
                            if ($property->isPublic()) {
                                $value = $property->getValue($value);
                                continue;
                            }
                        }
                    }

                    if (is_callable(array($value, $keyPart))) {
                        $value = call_user_func(array($value, $keyPart));
                        continue;
                    }

                    if (is_array($value) && array_key_exists($keyPart, $value)) {
                        $value = $value[$keyPart];
                        continue;
                    }

                    // no match found
                    $value = null;
                    break;
                }

                return $value;
            };
        }

        if (is_callable($strategy)) {
            $strategy = function () use($strategy) {
                return call_user_func_array($strategy, func_get_args());
            };
        }

        if (!($strategy instanceof Closure)) {
            throw new InvalidArgumentException('Argument $KEYSTRATEGY must be a Closure');
        }

        return $strategy;
    }
}
