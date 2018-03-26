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
     * @param $string
     * @param int $padBytes
     * @param int $padMode
     * STR_PAD_LEFT or STR_PAD_RIGHT
     * @return string
     */
    public static function ensureHexPrefix($string, int $padBytes = 0, int $padMode = STR_PAD_LEFT)
    {
        if ($padBytes > 0) {
            $string = static::removeHexPrefix($string);
            if ($padMode === STR_PAD_LEFT or $padMode === STR_PAD_RIGHT) {
                $string = str_pad($string, $padBytes * 2, '0', $padMode);
            }
        } elseif (static::hasHexPrefix($string)) {
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