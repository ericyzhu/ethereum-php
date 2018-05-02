<?php

namespace Ethereum\Types;

use BitWasp\Buffertools\BufferInterface;
use Ethereum\Utils;

class BlockNumber extends TypeAbstract
{
    public const LATEST   = 'latest';
    public const PENDING  = 'pending';
    public const EARLIEST = 'earliest';

    /**
     * @var bool
     */
    private $isTag;

    /**
     * @param BufferInterface $buffer
     * @param bool $isTag
     */
    protected function __construct(BufferInterface $buffer, bool $isTag)
    {
        parent::__construct($buffer);
        $this->isTag = $isTag;
    }

    /**
     * @param Uint|string $tag
     * @return BlockNumber
     */
    public static function init($tag = self::LATEST): BlockNumber
    {
        if ($tag instanceof Uint) {
            $buffer = $tag->getBuffer();
            $isTag = false;
        } elseif (is_string($tag)) {
            if (! in_array($tag, [self::LATEST, self::PENDING, self::EARLIEST])) {
                throw new \InvalidArgumentException('Wrong BlockNumber');
            }
            $buffer = new Buffer($tag);
            $isTag = true;
        } else {
            throw new \InvalidArgumentException('Wrong BlockNumber');
        }
        return new static($buffer, $isTag);
    }

    /**
     * @param string $hex
     * @return BlockNumber
     * @throws \Exception
     */
    public static function initWithHex(?string $hex): BlockNumber
    {
        return new static(Buffer::hex(Utils::removeHexPrefix((string)$hex)), false);
    }

    /**
     * @return bool
     */
    public function isTag(): bool
    {
        return $this->isTag;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->isTag ? $this->getBinary() : $this->getInt();
    }
}
