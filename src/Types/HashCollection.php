<?php

namespace Ethereum\Types;

use Ethereum\Collection;

class HashCollection extends Collection
{
    /**
     * @param array $data
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        foreach ($data as $topic) {
            $this->data[] = $topic instanceof Hash ? $topic : Hash::init($topic);
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(function (Hash $topic) {
            return $topic->toString();
        }, $this->data);
    }
}