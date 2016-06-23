<?php

namespace Zicht\Itertools\lib;

use Countable;
use Iterator;
use Closure;
use Zicht\Itertools\lib\Traits\CountableTrait;
use Zicht\Itertools\lib\Traits\DebugInfoTrait;

class AccumulateIterator implements Iterator, Countable
{
    use CountableTrait;
    use DebugInfoTrait;

    protected $iterable;
    protected $func;
    protected $value;

    public function __construct(Iterator $iterable, Closure $func)
    {
        $this->iterable = $iterable;
        $this->func = $func;
        $this->value = null;
    }

    public function rewind()
    {
        $this->iterable->rewind();
        $this->value = $this->iterable->valid() ? $this->iterable->current() : null;
    }

    public function current()
    {
        return $this->value;
    }

    public function key()
    {
        return $this->iterable->key();
    }

    public function next()
    {
        $this->iterable->next();
        if ($this->iterable->valid()) {
            // must assign $this->func to $func before calling the closure
            // because otherwise it will try fo find a method called func,
            // which doesn't exist
            $func = $this->func;
            $this->value = $func($this->value, $this->iterable->current());
        }
    }

    public function valid()
    {
        return $this->iterable->valid();
    }
}
