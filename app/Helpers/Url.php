<?php

namespace App\Helpers;

class Url
{
    /**
     * Encode data to Base64URL
     *
     * @param  string  $data
     * @return bool|string
     */
    public static function base64urlEncode($data)
    {
        // First of all you should encode $data to Base64 string
        $b64 = base64_encode($data);

        // Make sure you get a valid result, otherwise, return FALSE, as the base64_encode() function do
        if ($b64 === false) {
            return false;
        }

        // Convert Base64 to Base64URL by replacing “+” with “-” and “/” with “_”
        $url = strtr($b64, '+/', '-_');

        // Remove padding character from the end of line and return the Base64URL result
        return rtrim($url, '=');
    }

    /**
     * Decode data from Base64URL
     *
     * @param  string  $data
     * @param  bool  $strict
     * @return bool|string
     */
    public static function base64urlDecode($data, $strict = false)
    {
        // Convert Base64URL to Base64 by replacing “-” with “+” and “_” with “/”
        $b64 = strtr($data, '-_', '+/');

        // Decode Base64 string and return the original data
        return base64_decode($b64, $strict);
    }
}
