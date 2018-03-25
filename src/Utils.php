<?php

namespace Ethereum;

abstract class Utils
{
    /**
     * @param string $string
     * @return bool
     */
    public static function hasHexPrefix($string)
    {
        return substr($string, 0, 2) === '0x';
    }

    /**
     * @param string $string
     * @return string
     */
    public static function removeHexPrefix($string)
    {
        if (! static::hasHexPrefix($string)) {
            return $string;
        }
        return substr($string, 2);
    }

    /**
     * @param string $string
     * @return string
     */
    public static function ensureHexPrefix($string)
    {
        if (static::hasHexPrefix($string)) {
            return $string;
        }
        return '0x' . $string;
    }

    /**
     * @param string $hex
     * @return string
     */
    static function trimHex(string $hex): string
    {
        while (substr($hex, 0, 2) === '00') {
            $hex = substr($hex, 2);
        }
        return $hex;
    }
}