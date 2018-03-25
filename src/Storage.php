<?php

namespace Ethereum;

class Storage implements StorageInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param string $name
     * @param string $value
     * @return StorageInterface
     */
    public function set(string $name, string $value): StorageInterface
    {
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * @param string|null $name
     * @return string
     */
    public function get(string $name): ?string
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * @param string $name
     * @return void
     */
    public function remove(string $name): void
    {
        unset($this->data[$name]);
    }
}