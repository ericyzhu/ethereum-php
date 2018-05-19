<?php

namespace Ethereum\Types;

use Ethereum\Rlp;
use Ethereum\Utils;
use Exception;

/**
 * @property Address $from;
 * @property Address $to;
 * @property Byte    $data;
 * @property Uint    $gas;
 * @property Uint    $gasPrice;
 * @property Uint    $value;
 * @property Uint    $nonce;
 * @property Uint    $v;
 * @property Uint    $r;
 * @property Uint    $s;
 */
class Transaction
{
    /**
     * @var Address $from
     */
    protected $from;
    /**
     * @var Uint $chainId
     */
    protected $chainId;

    /**
     * @var Uint $nonce
     */
    protected $nonce;

    /**
     * @var Uint $gasPrice
     */
    protected $gasPrice;

    /**
     * @var Uint $gas
     */
    protected $gas;

    /**
     * @var Address $to
     */
    protected $to;

    /**
     * @var Uint $value
     */
    protected $value;

    /**
     * @var Byte $data
     */
    protected $data;

    /**
     * @var Uint|null $v
     */
    protected $v;

    /**
     * @var Uint|null $r
     */
    protected $r;

    /**
     * @var Uint|null $s
     */
    protected $s;

    /**
     * @param Address $from
     * @param Address|null $to
     * @param Uint|null $value
     * @param Byte|null $data
     * @param Uint|null $nonce
     * @param Uint|null $gasPrice
     * @param Uint|null $gas
     * @throws Exception
     */
    public function __construct(Address $from, Address $to = null, Byte $data = null, Uint $value = null, Uint $gasPrice = null, Uint $gas = null, Uint $nonce = null)
    {
        $this->from     = $from;
        $this->to       = $to ?? Address::init();
        $this->nonce    = null === $nonce    ? Uint::init() : $nonce;
        $this->gasPrice = null === $gasPrice ? Uint::init() : $gasPrice;
        $this->gas      = null === $gas      ? Uint::init() : $gas;
        $this->value    = null === $value    ? Uint::init() : $value;
        $this->data     = $data ??  Byte::init();
        $this->v        = Uint::init();
        $this->r        = Uint::init();
        $this->s        = Uint::init();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $transaction = [
            'from' => Utils::ensureHexPrefix($this->from->getHex()),
            'to'   => Utils::ensureHexPrefix($this->to->getHex()),
        ];

        if (! $this->data->isEmpty()) {
            $transaction['data'] = Utils::ensureHexPrefix($this->data->getHex());
        }

        if (! $this->gas->isEmpty()) {
            $transaction['gas'] = Utils::ensureHexPrefix(ltrim($this->gas->getHex(), '0'));
        }

        if (! $this->gasPrice->isEmpty()) {
            $transaction['gasPrice'] = Utils::ensureHexPrefix(ltrim($this->gasPrice->getHex(), '0'));
        }

        if (! $this->value->isEmpty()) {
            $transaction['value'] = Utils::ensureHexPrefix(ltrim($this->value->getHex(), '0'));
        }

        if (! $this->nonce->isEmpty()) {
            $transaction['nonce'] = Utils::ensureHexPrefix(ltrim($this->nonce->getHex(), '0'));
        }

        return $transaction;
    }

    /**
     * @param Uint $v
     * @param Uint $r
     * @param Uint $s
     * @return Byte
     * @throws Exception
     */
    public function withSignature(Uint $v, Uint $r, Uint $s): Byte
    {
        $this->v = $v;
        $this->r = $r;
        $this->s = $s;

        return Rlp::encode([
            $this->nonce,
            $this->gasPrice,
            $this->gas,
            $this->to,
            $this->value,
            $this->data,
            $this->v,
            $this->r,
            $this->s,
        ]);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function __set(string $name, $value)
    {
        $this->{$name} = $value;
        return $this;
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