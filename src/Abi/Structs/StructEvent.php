<?php

namespace Ethereum\Abi\Structs;

use Ethereum\Abi\Serializer;
use Ethereum\Crypto\Keccak;
use Ethereum\Types\Log;
use Ethereum\Utils;
use InvalidArgumentException;

/**
 *
 * @property string $name
 * @property string $type
 * @property ParameterCollection $inputs
 * @property bool $anonymous
 */
class StructEvent
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * Array of AbiEventInput
     *
     * @var ParameterCollection
     * */
    private $inputs;

    /**
     * @var bool
     */
    private $anonymous;

    /**
     * @var string
     */
    private $signature;

    /**
     * @param object $data
     * @throws \Exception
     */
    public function __construct(object $data)
    {
        foreach (['name' => true, 'type' => true, 'inputs' => true, 'anonymous' => true] as $key => $required) {
            if (property_exists($data, $key)) {
                call_user_func([$this, 'set'.ucfirst($key)], $data->{$key});
            } else {
                if ($required) {
                    throw new InvalidArgumentException(sprintf('Missing property \'%s\'.', $key));
                }
            }
        }

        $this->signature = Utils::ensureHexPrefix(substr(Keccak::hash(sprintf('%s(%s)', $this->name, $this->inputs->getStringTypes()), 256), 0, 64));
    }

    /**
     * @param string $value
     * @return $this
     */
    private function setName(string $value) : StructEvent
    {
        $this->name = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    private function setType(string $value) : StructEvent
    {
        if ($value !== 'event') {
            throw new InvalidArgumentException('Argument must be string \'event\'.');
        }
        $this->type = $value;
        return $this;
    }

    /**
     * @param array $value
     * @return $this
     */
    private function setInputs(array $value) : StructEvent
    {
        $array = [];
        foreach ($value as $item) {
            $array[] = new EventInput($item);
        }
        $this->inputs = new ParameterCollection($array);
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    private function setAnonymous(bool $value) : StructEvent
    {
        $this->anonymous = $value;
        return $this;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param Log $log
     * @return mixed
     */
    public function deserialize(Log $log)
    {
        $parameters         = new ParameterCollection;
        $indexedIndexes     = [];
        $nonIndexedIndexes  = [];
        $indexedIndexOffset = (int) ! $this->anonymous;
        $inputLength        = count($this->inputs);
        for ($inputIndex = 0; $inputIndex < $inputLength; ++$inputIndex) {
            $parameter = $this->inputs[$inputIndex];
            if ($parameter->indexed === true) {
                $indexedIndexes[$inputIndex] = count($indexedIndexes) + $indexedIndexOffset;
            } else {
                $parameters[] = $parameter;
                $nonIndexedIndexes[$inputIndex] = count($nonIndexedIndexes);
            }
        }

        $indexedOutput = [];
        foreach ($indexedIndexes as $inputIndex => $i) {
            /** @var EventInput $input */
            $input = $this->inputs[$inputIndex];
            $indexedOutput[$inputIndex] = $input->isDynamic() === true ? $log->topics[$i]->toString() : $input->deserialize(Utils::removeHexPrefix($log->topics[$i]->toString()));
        }
        $nonIndexedOutput = array_combine(array_keys($nonIndexedIndexes), Serializer::deserialize($parameters, $log->data, Serializer::OUTPUT_KEY_INDEXED));
        unset($parameters);

        $output = [];
        for ($inputIndex = 0; $inputIndex < $inputLength; ++$inputIndex) {
            $parameter = $this->inputs[$inputIndex];
            $key = $parameter->name;
            $value = $parameter->indexed === true ? $indexedOutput[$inputIndex] : $nonIndexedOutput[$inputIndex];
            if (empty($key)) {
                $output[] = $value;
            } else {
                $output[$key] = $value;
            }
        }
        unset($indexedOutput, $nonIndexedOutput);

        return $output;
    }
    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->{$name};
    }
}