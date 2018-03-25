<?php

namespace Ethereum\Abi;

use Ethereum\Abi\Structs\StructEvent;
use Ethereum\Abi\Structs\StructFunction;
use Exception;
use InvalidArgumentException;

class Abi
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Abi constructor.
     * @param string $data
     */
    public function __construct(string $data)
    {
        try {
            $data = json_decode($data);
        } catch (Exception $e) {
            throw new InvalidArgumentException('Argument is not a valid JSON string.');
        }
        foreach ($data as $item) {
            if (! isset($item->type)) {
                throw new InvalidArgumentException('Missing property \'type\'.');
            }
            switch ($item->type) {
                case 'function':
//                case 'constructor':
//                case 'fallback':
                    $item = new StructFunction($item);
                    break;
                case 'event':
                    $item = new StructEvent($item);
                    break;
                default:
                    continue 2;
            }
            $this->data[$item->name] = $item;
            $this->data[$item->getSignature()] = $item;
        }
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->data[$name];
    }

}