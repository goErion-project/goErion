<?php

namespace App\Marketplace\Utility\MoneroRPC;

use Exception;
use Random\RandomException;

require_once("SHA3.php");
require_once("ed25519.php");
require_once("base58.php");

class cryptonote
{
    private ed25519 $ed25519;
    private base58 $base58;

    public function __construct() {
        $this->ed25519 = new ed25519();
        $this->base58 = new base58();
    }

    /*
     * @param string Hex encoded string of the data to hash
     * @return string Hex encoded string of the hashed data
     *
     */
    public function keccak_256($message): string
    {
        $keccak256 = SHA3::init(SHA3::KECCAK_256);
        $keccak256->absorb(hex2bin($message));
        return bin2hex($keccak256->squeeze(32));
    }

    /*
     * @return string A hex-encoded string of 32 random bytes
     *
     */
    /**
     * @throws RandomException
     */
    public function gen_new_hex_seed(): string
    {
        $bytes = random_bytes(32);
        return bin2hex($bytes);
    }

    public function sc_reduce($input): string
    {
        $integer = $this->ed25519->decodeint(hex2bin($input));

        $modulo = bcmod($integer, $this->ed25519->l);

        return bin2hex($this->ed25519->encodeint($modulo));
    }

    /*
     * Hs in the cryptonote white paper
     *
     * @param string Hex encoded data to hash
     *
     * @return string A 32 byte encoded integer
     */
    public function hash_to_scalar($data): string
    {
        $hash = $this->keccak_256($data);
        return $this->sc_reduce($hash);
    }

    /*
     * Derive a deterministic private view key from a private spent key
     * @param string A private spend key represented as a 32 byte hex string
     *
     * @return string A deterministic private view key represented as a 32 byte hex string
     */
    public function derive_viewKey($spendKey): string
    {
        return $this->hash_to_scalar($spendkey);
    }

    /*
     * Generate a pair of random private keys
     *
     * @param string A hex string to be used as a seed (this should be random)
     *
     * @return array An array containing a private spent key and a deterministic view key
     */
    public function gen_private_keys($seed): array
    {
        $spendKey = $this->sc_reduce($seed);
        $viewKey = $this->derive_viewKey($spendKey);
        return array("spendKey" => $spendKey,
            "viewKey" => $viewKey);
    }

    /*
     * Get a public key from a private key on the ed25519 curve
     *
     * @param string a 32 byte hex-encoded private key
     *
     * @return string a 32 byte hex encoding of a point on the curve to be used as a public key
     */
    public function pk_from_sk($privKey): string
    {
        $keyInt = $this->ed25519->decodeint(hex2bin($privKey));
        $aG = $this->ed25519->scalarmult_base($keyInt);
        return bin2hex($this->ed25519->encodepoint($aG));
    }

    /*
     * Generate key derivation
     *
     * @param string a 32 byte hex encoding of a point on the ed25519 curve used as a public key
     * @param string a 32 byte hex-encoded private key
     *
     * @return string The hex encoded key derivation
     */
    /**
     * @throws \Exception
     */
    public function gen_key_derivation($public, $private): string
    {
        $point = $this->ed25519->scalarmult($this->ed25519->decodepoint(hex2bin($public)), $this->ed25519->decodeint(hex2bin($private)));
        $res = $this->ed25519->scalarmult($point, 8);
        return bin2hex($this->ed25519->encodepoint($res));
    }

    public function encode_varint($data): string
    {
        $orig = $data;

        if ($data < 0x80) {
            return bin2hex(pack('C', $data));
        }

        $encodedBytes = [];
        while ($data > 0) {
            $encodedBytes[] = 0x80 | ($data & 0x7f);
            $data >>= 7;
        }

        $encodedBytes[count($encodedBytes) - 1] &= 0x7f;
        $bytes = call_user_func_array('pack', array_merge(array('C*'), $encodedBytes));;
        return bin2hex($bytes);
    }

    public function derivation_to_scalar($der, $index): string
    {
        $encoded = $this->encode_varint($index);
        $data = $der . $encoded;
        return $this->hash_to_scalar($data);
    }

    // this is a one-way function used for both encrypting and decrypting 8 byte payment IDs

    /**
     * @throws Exception
     */
    public function stealth_payment_id($payment_id, $tx_pub_key, $viewkey): string
    {
        if (strlen($payment_id) != 16) {
            throw new Exception("Error: Incorrect payment ID size. Should be 8 bytes");
        }
        $der = $this->gen_key_derivation($tx_pub_key, $viewkey);
        $data = $der . '8d';
        $hash = $this->keccak_256($data);
        $key = substr($hash, 0, 16);
        return bin2hex(pack('H*', $payment_id) ^ pack('H*', $key));
    }

    // takes transaction extra field as hex string and returns transaction public key 'R' as hex string
    public function txpub_from_extra($extra) {
        $parsed = array_map("hexdec", str_split($extra, 2));

        if ($parsed[0] == 1) {
            return substr($extra, 2, 64);
        }

        if ($parsed[0] == 2) {
            if ($parsed[0] == 2 || $parsed[2] == 1) {
                $offset = (($parsed[1] + 2) * 2) + 2;
                return substr($extra, (($parsed[1] + 2) * 2) + 2, 64);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function derive_public_key($der, $index, $pub): string
    {
        $scalar = $this->derivation_to_scalar($der, $index);
        $sG = $this->ed25519->scalarmult_base($this->ed25519->decodeint(hex2bin($scalar)));
        $pubPoint = $this->ed25519->decodepoint(hex2bin($pub));
        $key = $this->ed25519->encodepoint($this->ed25519->edwards($pubPoint, $sG));
        return bin2hex($key);
    }

    /*
     * Perform the calculation P = P' as described in the cryptonote whitepaper
     *
     * @param string 32 byte transaction public key R
     * @param string 32 byte receiver private view key a
     * @param string 32 byte receiver public spend key B
     * @param int output index
     * @param string output you want to check against P
     */
    /**
     * @throws Exception
     */
    public function is_output_mine($txPublic, $privViewkey, $publicSpendkey, $index, $P): bool
    {
        $derivation = $this->gen_key_derivation($txPublic, $privViewkey);
        $Pprime = $this->derive_public_key($derivation, $index, $publicSpendkey);

        if ($P == $Pprime) {
            return true;
        } else
            return false;
    }

    /*
     * Create a valid base58 encoded Monero address from public keys
     *
     * @param string Public spend key
     * @param string Public view key
     *
     * @return string Base58 encoded Monero address
     */
    /**
     * @throws Exception
     */
    public function encode_address($pSpendKey, $pViewKey): string
    {
        // mainnet network byte is 18 (0x12)
        $data = "12" . $pSpendKey . $pViewKey;
        return $this->base58->encode($data);
    }

    /**
     * @throws Exception
     */
    public function verify_checksum($address): bool
    {
        $decoded = $this->base58->decode($address);
        $checksum = substr($decoded, -8);
        $checksum_hash = $this->keccak_256(substr($decoded, 0, 130));
        $calculated = substr($checksum_hash, 0, 8);
        if ($checksum == $calculated) {
            return true;
        } else
            return false;
    }

    /*
         * Decode a base58 encoded Monero address
         *
         * @param string A base58 encoded Monero address
         *
         * @return array An array containing the Address network byte, public spend key, and public view key
         */
    /**
     * @throws Exception
     */
    public function decode_address($address): array
    {
        $decoded = $this->base58->decode($address);

        if (!$this->verify_checksum($address)) {
            throw new Exception("Error: invalid checksum");
        }

        $network_byte = substr($decoded, 0, 2);
        $public_spendKey = substr($decoded, 2, 64);
        $public_viewKey = substr($decoded, 66, 64);

        return array("networkByte" => $network_byte,
            "spendKey" => $public_spendKey,
            "viewKey" => $public_viewKey);
    }

    /*
     * Get an integrated address from public keys and a payment id
     *
     * @param string A 32 byte hex encoded public spend key
     * @param string A 32 byte hex encoded public view key
     * @param string An 8 byte hex string to use as a payment id
     */
    /**
     * @throws Exception
     */
    public function integrated_addr_from_keys($public_spendkey, $public_viewkey, $payment_id): string
    {
        // 0x13 is the mainnet network byte for integrated addresses
        $data = "13" . $public_spendkey . $public_viewkey . $payment_id;
        $checksum = substr($this->keccak_256($data), 0, 8);
        return $this->base58->encode($data . $checksum);
    }

    /*
     * Generate a Monero address from seed
     *
     * @param string Hex string to use as seed
     *
     * @return string A base58 encoded Monero address
     */
    /**
     * @throws Exception
     */
    public function address_from_seed($hex_seed): string
    {
        $private_keys = $this->gen_private_keys($hex_seed);
        $private_viewKey = $private_keys["viewKey"];
        $private_spendKey = $private_keys["spendKey"];

        $public_spendKey = $this->pk_from_sk($private_spendKey);
        $public_viewKey = $this->pk_from_sk($private_viewKey);

        return $this->encode_address($public_spendKey, $public_viewKey);
    }
}
