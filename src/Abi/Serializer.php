<?php

namespace Ethereum\Abi;


use Ethereum\Abi\Structs\Parameter;
use Ethereum\Abi\Structs\ParameterCollection;
use Ethereum\Abi\Types\Interfaces\TypeInterface;
use Ethereum\Abi\Types\TypeUint;
use Ethereum\Utils;
use InvalidArgumentException;

class Serializer
{
    public const OUTPUT_KEY_MIXED   = 0;
    public const OUTPUT_KEY_INDEXED = 1;
    public const OUTPUT_KEY_NAMED   = 2;

    public static function serialize(ParameterCollection $parameters, array $arguments = []): string
    {
        $count = count($parameters);

        if (empty($count)) {
            return '';
        }

        if ($count !== count($arguments)) {
            throw new InvalidArgumentException('The length of argument does not match number of declared parameters.');
        }

        $uint      = new TypeUint();
        $headBytes = $count * TypeInterface::PADDING_BYTES;
        $head      = '';
        $dynamic   = '';

        for ($i = 0; $i < $count; ++$i) {
            /** @var Parameter $parameter */
            $parameter = $parameters[$i];
            /** @var mixed $argument */
            $argument  = $arguments[$i];

            if ($parameter->isDynamic()) {
                // offset
                $head      .= $uint->serialize($headBytes);
                $dynamic   .= $parameter->serialize($argument);
                $dynamicLength = strlen($dynamic);
                $headBytes += $dynamicLength > 0 ? $dynamicLength / 2 : 0;
            } else {
                $head .= $parameter->serialize($argument);
            }
        }

        return $head . $dynamic;
    }

    /**
     * @param ParameterCollection $parameters
     * @param string $data
     * @param int $outputKey
     * @return array
     */
    public static function deserialize(ParameterCollection $parameters, string $data, int $outputKey = self::OUTPUT_KEY_MIXED): array
    {
        $data  = Utils::removeHexPrefix($data);
        $count = count($parameters);
        if (empty($data) or empty($count)) {
            return [];
        }

        $count         = count($parameters);
        $paddingLength = TypeInterface::PADDING_BYTES * 2;
        $headLength    = $count * $paddingLength;
        $uint          = new TypeUint();
        $heads         = str_split(substr($data, 0, $headLength), $paddingLength);
        $output        = [];

        for ($i = 0; $i < $count; ++$i) {
            /** @var Parameter $parameter */
            $parameter = $parameters[$i];
            /** @var string $head */
            $head  = $heads[$i];

            if ($parameter->isDynamic()) {
                $offset = (int)$uint->deserialize($head) * 2;
                $value = $parameter->deserialize(substr($data, $offset));
            } else {
                $value = $parameter->deserialize($head);
            }

            $key = $parameters[$i]->name;
            if ($outputKey === self::OUTPUT_KEY_MIXED) {
                if (empty($key)) {
                    $output[] = $value;
                } else {
                    $output[$key] = $value;
                }
            } elseif ($outputKey === self::OUTPUT_KEY_INDEXED) {
                $output[] = $value;
            } elseif ($outputKey === self::OUTPUT_KEY_NAMED) {
                $output[$key] = $value;
            }
        }

        return $output;
    }
}