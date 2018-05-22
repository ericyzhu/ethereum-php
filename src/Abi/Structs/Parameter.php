<?php

namespace Ethereum\Abi\Structs;

use Ethereum\Abi\Structs\Interfaces\ParameterInterface;
use Ethereum\Abi\Types\Interfaces\TypeInterface;
use Ethereum\Abi\Types\TypeUint;
use InvalidArgumentException;

/**
 *
 * @property string $name
 * @property string $type
 */
abstract class Parameter implements ParameterInterface
{
    /**
     * @var TypeInterface
     */
    private $typeResolver;

    /**
     * @var int
     */
    private $containerType;

    /**
     * @var int
     */
    private $containerLength;

    /**
     * @var bool
     */
    private $isDynamic;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $properties = [];

    /**
     * @var array
     */
    protected $required = [];

    /**
     * @param object $data
     */
    public function __construct(object $data)
    {
        $this->required = array_unique(array_merge(['name', 'type']));
        foreach ($this->properties as $name => $type) {
            if (property_exists($data, $name)) {
                $this->fillData($name, $type,  $data->{$name});
            } else {
                if (in_array($name, $this->required)) {
                    throw new InvalidArgumentException(sprintf('Missing property \'%s\'.', $name));
                }
                $this->fillData($name, $type);
            }
        }
        $this->validateType();
    }

    private function fillData($name, $type, $value = null)
    {
        switch ($type) {
            case 'string':
                $value = (string)$value;
                break;
            case 'boolean':
                $value = (bool)$value;
                break;
            case 'integer':
                $value = (int)$value;
                break;
            case 'array':
                $value = (array)$value;
                break;
            default:
                throw new InvalidArgumentException(sprintf('Property \'%s\' must by one of string, boolean, integer and array.', $name));
        }
        $this->data[$name] = $value;
    }

    protected function validateType()
    {
        /**
         * Currently, we needn't validate the fixed or ufixed type.
         * Fixed point numbers are not fully supported by Solidity yet.
         * They can be declared, but cannot be assigned to or from.
         */
        $pattern = "#^(?P<elementaryType>[a-z]+)(?P<elementaryTypeSize>(\d+x\d+|\d*))(?P<arrayString>\[(?P<arrayLength>\d*)\]){0,1}$#";
        $valid = (bool)preg_match($pattern, $this->type, $matches);

        if (! $valid) {
            throw new InvalidArgumentException('Invalid Solidity type.');
        }
        $elementaryType = $matches['elementaryType'];

        $namespace = explode('\\', __NAMESPACE__);
        array_pop($namespace);
        $class = implode('\\', $namespace) . '\Types\Type' . ucfirst($elementaryType);
        if (! class_exists($class)) {
            throw new InvalidArgumentException('Invalid Solidity type.');
        }

        $this->typeResolver    = new $class($matches['elementaryTypeSize']);
        $this->containerType   = empty($matches['arrayString']) ? static::CONTAINER_NONE : static::CONTAINER_ARRAY;
        $this->containerLength = $this->containerType === static::CONTAINER_ARRAY ? (int)$matches['arrayLength'] : 0;
        $this->isDynamic       = ($this->typeResolver->isDynamic() or ($this->containerType === static::CONTAINER_ARRAY and $this->containerLength === 0));
    }

    /**
     * @param mixed $data
     * @return string
     */
    public function serialize($data): string
    {
        $output = '';
        if ($this->containerType === static::CONTAINER_ARRAY) {
            if (! is_array($data)) {
                throw new InvalidArgumentException('The argument must be array.');
            }
            $length = count($data);
            if ($this->containerLength > 0 and $this->containerLength !== $length) {
                throw new InvalidArgumentException('The length of argument does not match length of declared.');
            }
            $output .= (new TypeUint())->serialize($length);
            foreach ($data as $item) {
                $output .= $this->typeResolver->serialize($item);
            }
        } elseif ($this->containerType === static::CONTAINER_TUPLE) {
        } else {
            $output .= $this->typeResolver->serialize($data);
        }
        return $output;
    }

    /**
     * @param string $data
     * @return mixed
     */
    public function deserialize(string $data)
    {
        $paddingLength = TypeInterface::PADDING_BYTES * 2;
        $chunk         = substr($data, 0, $paddingLength);

        if ($this->isDynamic()) {
            $length = (new TypeUint())->deserialize($chunk);
            if ($this->containerType === static::CONTAINER_ARRAY) {
                $output = [];
                $data   = substr($data, $paddingLength, $paddingLength * $length);
                $chunks = str_split($data, $paddingLength);
                foreach ($chunks as $item) {
                    $output[] = $this->typeResolver->deserialize($item);
                }
                return $output;
            } elseif ($this->containerType === static::CONTAINER_TUPLE) {
            } else {
                return $this->typeResolver->deserialize($data);
            }
        }
        return $this->typeResolver->deserialize($chunk);
    }

    /**
     * @return bool
     */
    public function isDynamic(): bool
    {
        return $this->isDynamic;
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