<?php

namespace Ethereum\Types;

use GMP;

interface TypeInterface
{
    /**
     * @param int              $start
     * @param int|null         $end
     * @return TypeInterface
     * @throws \Exception
     */
    public function slice(int $start, int $end = null): TypeInterface;

    /**
     * Get the size of the buffer to be returned
     *
     * @return int
     */
    public function getSize(): int;

    /**
     * Get the size of the value stored in the buffer
     *
     * @return int
     */
    public function getInternalSize(): int;

    /**
     * @return string
     */
    public function getBinary(): string;

    /**
     * Alias of getBinary
     * @return string
     */
    public function toString(): string;

    /**
     * @return string
     */
    public function getHex(): string;

    /**
     * @return int|string
     */
    public function getInt();

    /**
     * @return GMP
     */
    public function getGmp(): GMP;

    /**
     * @return TypeInterface
     */
    public function flip(): TypeInterface;

    /**
     * @param TypeInterface $other
     * @return bool
     */
    public function equals(TypeInterface $other): bool;

    /**
     * @return Buffer
     */
    public function getBuffer(): Buffer;

    /**
     * @return bool
     */
    public function isEmpty(): bool;
}