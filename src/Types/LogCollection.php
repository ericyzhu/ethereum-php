<?php

namespace Ethereum\Types;

use Ethereum\Collection;

class LogCollection extends Collection
{
    /**
     * LogCollection constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        foreach ($data as $item) {
            $this->data[] = new Log($item);
        }
    }
}