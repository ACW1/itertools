<?php

namespace Zicht\Itertools\lib;

use ArrayAccess;
use ArrayIterator;
use Closure;
use Countable;
use Iterator;
use IteratorIterator;
use Zicht\Itertools\lib\Traits\ArrayAccessTrait;
use Zicht\Itertools\lib\Traits\DebugInfoTrait;

// todo: add unit tests for Countable interface
// todo: add unit tests for ArrayAccess interface

class GroupedIterator extends IteratorIterator implements Countable, ArrayAccess
{
    use ArrayAccessTrait;
    use DebugInfoTrait;

    protected $groupKey;
    protected $values;

    public function __construct($groupKey)
    {
        $this->groupKey = $groupKey;
        parent::__construct(new ArrayIterator());
    }

    public function getGroupKey()
    {
        return $this->groupKey;
    }

    public function append($key, $value)
    {
        $this->getInnerIterator()->append(array($key, $value));
        // var_dump(['add to' => $this->groupKey, 'key' => $key, 'value' => $value]);
    }

    public function current()
    {
        return $this->getInnerIterator()->current()[1];
    }

    public function key()
    {
        return $this->getInnerIterator()->current()[0];
    }

    public function count()
    {
        return iterator_count($this->getInnerIterator());
    }

    public function toArray()
    {
        return iterator_to_array($this);
    }
}

class GroupbyIterator extends IteratorIterator implements Countable, ArrayAccess
{
    use ArrayAccessTrait;
    use DebugInfoTrait;

    public function __construct(Closure $func, Iterator $iterable)
    {
        // todo: this implementation pre-computes everything... this is
        // not the way an iterator should work.  Please re-write.
        $groupedIterator = null;
        $previousGroupKey = null;
        $data = array();

        foreach ($iterable as $key => $value) {
            $groupKey = call_user_func($func, $value);
            if ($previousGroupKey !== $groupKey || $groupedIterator === null) {
                $previousGroupKey = $groupKey;
                $groupedIterator = new GroupedIterator($groupKey);
                $data []= $groupedIterator;
            }
            $groupedIterator->append($key, $value);
        }

        parent::__construct(new ArrayIterator($data));
    }

    public function key()
    {
        return $this->current()->getGroupKey();
    }

    public function count()
    {
        return iterator_count($this->getInnerIterator());
    }

    public function toArray()
    {
        $array = iterator_to_array($this);
        array_walk($array, function (&$value) { $value = $value->toArray(); });
        return $array;
    }
}
