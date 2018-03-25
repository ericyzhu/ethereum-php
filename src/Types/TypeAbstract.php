<?php

namespace Ethereum\Types;

use BitWasp\Buffertools\BufferInterface;
use ReflectionClass;
use BadMethodCallException;
use ArgumentCountError;
use Exception;
use GMP;

class TypeAbstract implements TypeInterface
{
    /**
     * @var Buffer
     */
    protected $buffer;

    /**
     * @param BufferInterface $buffer
     */
    protected function __construct(BufferInterface $buffer)
    {
        $this->buffer = $buffer;
    }

    /**
     * @param int      $start
     * @param integer|null $end
     * @return TypeInterface
     * @throws \Exception
     */
    public function slice(int $start, int $end = null): TypeInterface
    {
        return new static($this->buffer->slice($start, $end));
    }

    /**
     * Get the size of the buffer to be returned
     *
     * @return int
     */
    public function getSize(): int
    {
        return $this->buffer->getSize();
    }

    /**
     * Get the size of the value stored in the buffer
     *
     * @return int
     */
    public function getInternalSize(): int
    {
        return $this->buffer->getInternalSize();
    }

    /**
     * @return string
     */
    public function getBinary(): string
    {
        return $this->buffer->getBinary();
    }

    /**
     * @return string
     */
    public function getHex(): string
    {
        return $this->buffer->getHex();
    }

    /**
     * @return GMP
     */
    public function getGmp(): GMP
    {
        return $this->buffer->getGmp();
    }

    /**
     * @return int|string
     */
    public function getInt()
    {
        return $this->buffer->getInt();
    }

    /**
     * @return TypeInterface
     */
    public function flip(): TypeInterface
    {
        return new static($this->buffer->flip());
    }

    /**
     * @param TypeInterface $other
     * @return bool
     */
    public function equals(TypeInterface $other): bool
    {
        return ($other->getSize() === $this->getSize() and $other->getBinary() === $this->getBinary());
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->getBinary();
    }

    /**
     * @return Buffer
     */
    public function getBuffer(): Buffer
    {
        return $this->buffer;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->getSize()) or empty($this->getInt());
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @param Buffer $buffer
     * @return static
     * @throws Exception
     */
    public static function initWithBuffer(Buffer $buffer)
    {
        return new static($buffer);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return object
     * @throws ArgumentCountError
     */
    public static function __callStatic(string $name, array $arguments)
    {
        switch ($name) {
            case 'init':
                break;

            default:
                throw new BadMethodCallException(sprintf('Call to undefined method "%s".', $name));
        }

        $reflectionClass = new ReflectionClass(static::class);
        return $reflectionClass->newInstanceArgs($arguments);
    }
}