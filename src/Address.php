<?php

namespace TronTool;

use Exception;
use kornrunner\Keccak;
use StephenHill\Base58;

define('TRON_ALPHABET', '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');

class Address
{
    protected $base58;
    protected $hex;

    function hex()
    {
        return $this->hex;
    }

    function base58(): string
    {
        return $this->base58;
    }

    protected function __construct($hex)
    {
        $this->hex = $hex;
        $this->base58 = $this->encode($this->hex);
    }

    /**
     * @throws Exception
     */
    static function fromPublicKey($key): Address
    {
        $hex = self::compute($key);
        return new self($hex);
    }

    static function fromHex($hex): Address
    {
        return new self($hex);
    }

    static function fromBase58($b58): Address
    {
        $hex = self::decode($b58);
        return new self($hex);
    }

    /**
     * @throws Exception
     */
    static function compute($publicKey): string
    {
        $bin = hex2bin($publicKey);
        $bin = substr($bin, 1);
        $hash = Keccak::hash($bin, 256);
        return '41' . substr($hash, 24);
    }

    static function encode($hex): string
    {
        $base58 = new Base58(TRON_ALPHABET);
        $bin = hex2bin($hex);
        $hash0 = hash('sha256', $bin, true);
        $hash1 = hash('sha256', $hash0, true);
        $checksum = substr($hash1, 0, 4);
        return $base58->encode($bin . $checksum);
    }

    static function decode($b58): ?string
    {
        if (is_null($b58)) return null;
        $base58 = new Base58(TRON_ALPHABET);
        $decoded = $base58->decode($b58);
        $decoded = substr($decoded, 0, -4);
        return bin2hex($decoded);
    }

    function __toString()
    {
        return $this->base58;
    }

    static function base58_encode($string)
    {
        $base = strlen(TRON_ALPHABET);
        if (is_string($string) === false) {
            return false;
        }
        if (strlen($string) === 0) {
            return false;
        }
        $bytes = array_values(unpack('C*', $string));
        $decimal = $bytes[0];
        for ($i = 1, $l = count($bytes); $i < $l; $i++) {
            $decimal = bcmul($decimal, 256);
            $decimal = bcadd($decimal, $bytes[$i]);
        }
        $output = '';
        while ($decimal >= $base) {
            $div = bcdiv($decimal, $base, 0);
            $mod = bcmod($decimal, $base);
            $output .= TRON_ALPHABET[$mod];
            $decimal = $div;
        }
        if ($decimal > 0) {
            $output .= TRON_ALPHABET[$decimal];
        }
        $output = strrev($output);
        foreach ($bytes as $byte) {
            if ($byte === 0) {
                $output = TRON_ALPHABET[0] . $output;
                continue;
            }
            break;
        }
        return $output;
    }
}
