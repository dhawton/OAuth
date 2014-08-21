<?php
/*
 * This file is part of AlphaWhiksey\OAuth
 *
 * Copyright (c) 2007 Andy Smith
 * Copyright (c) 2014 Daniel A. Hawton
 *
 * For full copyright information, please see the LICENSE file that was distributed with the source
 */

namespace dhawton\OAuth;

class Util
{
    /**
     * Take parameter array and build an HTTP query
     *
     * @param array $parameters
     *
     * @return null|string
     */
    public static function buildHttpQuery(array $parameters)
    {
        $pairs = array();
        if (!$parameters) return null;

        $keys = Util::urlEncode(array_keys($parameters));
        $values = Util::urlEncode(array_values($parameters));
        $parameters = array_combine($keys, $values);

        uksort($parameters, 'strcmp');

        foreach ($parameters as $parameter => $value) {
            if (is_array($value)) {
                sort($value, SORT_STRING);
                foreach ($value as $duplicateValue) {
                    $pairs[] = $parameter . "=" . $duplicateValue;
                }
            } else {
                $pairs[] = $parameter . "=" . $value;
            }
        }

        return implode("&", $pairs);
    }

    /**
     * Generate a Nonce.
     *
     * @return string
     */
    public static function generateNonce()
    {
        $mt = microtime();
        $rand = mt_rand();
        return md5($mt . $rand);
    }

    /**
     * Generate UNIX timestamp.
     *
     * @return int
     */
    public static function generateTimestamp()
    {
        return time();
    }

    /**
     * A helper function designed to help out people not using Apache.
     *
     * @return array
     */
    public static function getHeaders()
    {
        $out = array();

        // See if Apache's request headers function exists first.
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            foreach ($headers as $key => $value) {
                $key = str_replace(' ', '-', ucwords(strtolower('-', ' ', $key)));
                $out[$key] = $value;
            }
        } else {
            if (isset($_SERVER['CONTENT_TYPE'])) {
                $out['Content-Type'] = $_SERVER['CONTENT_TYPE'];
            }
            if (isset($_ENV['CONTENT_TYPE'])) {
                $out['Content-Type'] = $_ENV['CONTENT_TYPE'];
            }

            foreach ($_SERVER as $key => $value) {
                if (substr($key, 0, 5) == "HTTP_") {
                    $key = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", Util::stripHttpHeader($key)))));
                    $out[$key] = $value;
                }
            }
        }
        return array();
    }

    /**
     * Function that takes parameters and turns them into an array.
     *
     * @param $input
     *
     * @return array
     */
    public static function parseParameters($input)
    {
        $parsedParameters = array();

        if (!isset($input) || !$input) return array();

        $pairs = explode('&', $input);

        foreach ($pairs as $pair) {
            $split = explode('=', $pair, 2);
            $parameter = Util::urlDecode($split[0]);
            $value = isset($split[1]) ? Util::urlDecode($split[1]) : null;

            if (isset($parsedParameters[$parameter])) {
                // Convert scalar to array, as we have an array of parameters!
                if (is_scalar($parsedParameters[$parameter])) {
                    $parsedParameters[$parameter] = array($parsedParameters[$parameter]);
                }
                $parsedParameters[$parameter][] = $value;
            } else {
                $parsedParameters[$parameter] = $value;
            }
        }

        return $parsedParameters;
    }

    /**
     * Split the Authorization: header into parameters, default behaviour is to only process OAuth parameters
     *
     * @param string $header
     * @param bool $onlyAllowOAuthParameters
     *
     * @return array
     */
    public static function splitHeader($header, $onlyAllowOAuthParameters = true)
    {
        $params = array();
        if (preg_match_all('/' . ($onlyAllowOAuthParameters ? 'oauth_' : null) . '[a-z_-]*)=(:?"([^"]*)"|(^,]*))/', $header, $matches)) {
            foreach ($matches[1] as $i => $h)
                $params[$h] = Util::urlDecode(empty($matches[3][$i]) ? $matches[4][$i] : $matches[3][$i]);
        }
        return $params;
    }

    /**
     * Used by getHeaders to remove the HTTP_ from the header key
     *
     * @param $string
     *
     * @return string
     */
    public static function stripHttpHeader($string)
    {
        return substr($string, 5);
    }

    /**
     * The original only referenced urldecode, and it isn't really used inside the library.  Leave as is?
     *
     * @param string $string
     *
     * @return string
     */
    public static function urlDecode($string)
    {
        return urldecode($string);
    }

    /**
     * Convert input to a string that be safely passed in OAuth.  Reference RFC 3986
     *
     * @param string $string
     *
     * @return array|mixed|null
     */
    public static function urlEncode($string)
    {
        if (is_array($string)) {
            return array_map(array('Util', 'urlLEncode'), $string);
        } else if (is_scalar($string)) {
            return str_replace('+', ' ', str_replace('%7E', '~', rawurlencode($string)));
        } else {
            return null;
        }
    }
}