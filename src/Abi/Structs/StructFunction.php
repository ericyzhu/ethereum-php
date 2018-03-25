<?php

namespace Ethereum\Abi\Structs;

use Ethereum\Abi\Serializer;
use Ethereum\Crypto\Keccak;
use Ethereum\Utils;
use InvalidArgumentException;

/**
 *
 * @property string $name
 * @property string $type
 * @property ParameterCollection $inputs
 * @property ParameterCollection $outputs
 * @property string $stateMutability
 * @property bool $payable
 * @property bool $constant
 */
class StructFunction
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
     * Array of AbiFunctionInput
     *
     * @var ParameterCollection
     * */
    private $inputs;

    /**
     * Array of AbiFunctionOutput
     *
     * @var ParameterCollection
     * */
    private $outputs;

    /**
     * @var string
     */
    private $stateMutability;

    /**
     * @var bool
     */
    private $payable;

    /**
     * @var bool
     */
    private $constant;

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
        foreach (['name' => true, 'type' => true, 'inputs' => true, 'outputs' => true, 'stateMutability' => true] as $key => $required) {
            if (property_exists($data, $key)) {
                call_user_func([$this, 'set'.ucfirst($key)], $data->{$key});
            } else {
                if ($required) {
                    throw new InvalidArgumentException(sprintf('Missing property \'%s\'.', $key));
                }
            }
        }

        $this->signature = Utils::ensureHexPrefix(substr(Keccak::hash(sprintf('%s(%s)', $this->name, $this->inputs->getStringTypes()), 256), 0, 8));
    }

    /**
     * @param string $value
     * @return $this
     */
    private function setName(string $value) : StructFunction
    {
        $this->name = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    private function setType(string $value) : StructFunction
    {
        if (! in_array($value, ['function', 'constructor', 'fallback'])) {
            throw new InvalidArgumentException('Argument must be one of \'function\', \'constructor\' or \'fallback\'.');
        }
        $this->type = $value;
        return $this;
    }

    /**
     * @param array $value
     * @return $this
     */
    private function setInputs(array $value) : StructFunction
    {
        $array = [];
        foreach ($value as $item) {
            $array[] = new FunctionInput($item);
        }
        $this->inputs = new ParameterCollection($array);
        return $this;
    }

    /**
     * @param array $value
     * @return $this
     */
    private function setOutputs(array $value) : StructFunction
    {
        $array = [];
        foreach ($value as $item) {
            $array[] = new FunctionOutput($item);
        }
        $this->outputs = new ParameterCollection($array);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    private function setStateMutability(string $value) : StructFunction
    {
        switch ($value) {
            case 'pure':
            case 'view':
                $this->payable = false;
                $this->constant = true;
                break;
            case 'payable':
                $this->payable = true;
                $this->constant = false;
                break;
            case 'nonpayable':
                $this->payable = false;
                $this->constant = false;
                break;
            default:
                throw new InvalidArgumentException('Argument must be one of \'pure\', \'view\', \'payable\' or \'nonpayable\'.');
        }
        $this->stateMutability = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
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