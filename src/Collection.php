<?php

namespace Ethereum;

use ArrayAccess;
use Countable;
use Iterator;

class Collection implements ArrayAccess, Countable, Iterator
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @return void
     */
    function rewind(): void
    {
        reset($this->data);
    }

    /**
     * @return mixed
     */
    function current()
    {
        return current($this->data);
    }

    /**
     * @return int|mixed|null|string
     */
    function key()
    {
        return key($this->data);
    }

    /**
     * @return mixed|void
     */
    function next(): void
    {
        next($this->data);
    }

    /**
     * @return bool
     */
    function valid(): bool
    {
        return key($this->data) !== null;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function __set(string $key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function __isset (string $key)
    {
        return $this->offsetExists($key);
    }

    /**
     * @param string $key
     */
    public function __unset(string $key)
    {
        $this->offsetUnset($key);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->data[$offset]);
        }
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->data[$offset] : null;
    }

}