<?php
/*
 * This file is part of dhawton\OAuth
 *
 * Copyright (c) 2007 Andy Smith
 * Copyright (c) 2014 Daniel A. Hawton
 *
 * For full copyright information, please see the LICENSE file that was distributed with the source
 */

namespace dhawton\OAuth;


class Request
{
    protected $parameters;
    protected $httpMethod;
    protected $httpUrl;
    public $baseString;
    public static $version = "1.0";
    public static $POST_INPUT = "php://input";

    function __construct($httpMethod, $httpUrl, $parameters = null)
    {
        $parameters = ($parameters) ? $parameters : array();
        $parameters = array_merge(Util::parseParameters(parse_url($httpUrl, PHP_URL_QUERY)), $parameters);
        $this->parameters = $parameters;
        $this->httpMethod = $httpMethod;
        $this->httpUrl = $httpUrl;
    }

    /**
     * Helper function, create Request from Consumer, Token, http method and http url.
     *
     * @param Consumer $consumer
     * @param Token $token
     * @param $httpMethod
     * @param $httpUrl
     * @param null $parameters
     *
     * @return Request
     */
    public static function fromConsumerAndToken(Consumer $consumer, Token $token, $httpMethod, $httpUrl, $parameters = null)
    {
        $parameters = ($parameters) ? $parameters : array();
        $defaults = array ("oauth_version" => Request::$version,
                           "oauth_nonce" => Util::generateNonce(),
                           "oauth_timestamp" => Util::generateTimestamp(),
                           "oauth_consumer_key" => $consumer->key
                           );

        if ($token) {
            $defaults['oauth_token'] = $token->key;
        }

        $parameters = array_merge($defaults, $parameters);

        return new Request($httpMethod, $httpUrl, $parameters);
    }

    /**
     * Build Request from what was passed to us
     *
     * @param null $httpMethod
     * @param null $httpUrl
     * @param null $parameters
     *
     * @return Request
     */
    public static function fromRequest($httpMethod = null, $httpUrl = null, $parameters = null)
    {
        $scheme = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') ? 'http' : 'https';
        $httpUrl = ($httpUrl) ? $httpUrl : $scheme . "://" . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] .
            $_SERVER['REQUEST_URI'];
        $httpMethod = ($httpMethod) ? $httpMethod : $_SERVER['REQUEST_METHOD'];

        if (!$parameters) {
            $requestHeaders = Util::getHeaders();
            $parameters = Util::parseParameters($_SERVER['QUERY_STRING']);

            if ($httpMethod == "POST"
                && isset($requestHeaders['Content-Type'])
                && strstr($requestHeaders['Content-Type'], 'application/x-www-form-urlencoded')) {
                $postData = Util::parseParameters(self::$POST_INPUT);
                $parameters = array_merge($parameters, $postData);
            }

            if (isset($requestHeaders['Authorization'])
                && substr($requestHeaders['Authorization'], 0, 6) == "OAuth ") {
                $headerParameters = Util::splitHeader($requestHeaders['Authorization']);
                $parameters = array_merge($parameters, $headerParameters);
            }
        }

        return new Request($httpMethod, $httpUrl, $parameters);
    }

    /**
     * Returns HTTP Method in proper casing
     *
     * @return string
     */
    public function getNormalizedHttpMethod()
    {
        return strtoupper($this->httpMethod);
    }

    public function getNormalizedHttpUrl()
    {
        $parts = parse_url($this->httpUrl);

        $scheme = isset($parts['scheme']) ? $parts['scheme'] : 'http';
        $port = isset($parts['port']) ? $parts['port'] : (($scheme == "https") ? '443' : '80');
        $host = isset($parts['host']) ? strtolower($parts['host']) : null;
        $path = isset($parts['path']) ? $parts['path'] : null;


        if (($scheme == 'https' && $port != '443') ||
            ($scheme == 'http' && $port != '80')) {
            $host = "$host:$port";
        }

        return "$scheme://$host$path";
    }

    /**
     * Get value of parameter
     *
     * @param $name
     *
     * @return string|null
     */
    public function getParameter($name)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }

    /**
     * Get all set parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Build signable parameters query string
     *
     * @return null|string
     */
    public function getSignableParameters()
    {
        $params = $this->parameters;

        // Cannot sign a signature!
        if (isset($params['oauth_signature']))
            unset($params['oauth_signature']);

        return Util::buildHttpQuery($params);
    }

    public function getSignatureBaseString()
    {
        $parts = array($this->getNormalizedHttpMethod(),
                       $this->getNormalizedHttpUrl(),
                       $this->getSignableParameters());

        $parts = Util::urlEncode($parts);

        return implode("&", $parts);
    }

    /**
     * Set a parameter
     *
     * @param $name
     * @param $value
     * @param bool $allowDuplicates
     */
    public function setParameter($name, $value, $allowDuplicates = true)
    {
        if ($allowDuplicates && isset($this->parameters[$name])) {
            if (is_scalar($this->parameters[$name])) {
                $this->parameters[$name] = array($this->parameters[$name]);
            }

            $this->parameters[$name][] = $value;
        } else {
            $this->parameters[$name] = $value;
        }
    }

    /**
     * Unset a specific parameter
     *
     * @param $name
     */
    public function unsetParameter($name)
    {
        unset($this->parameters[$name]);
    }

    /**
     * Clear all parameters
     */
    public function unsetParameters()
    {
        $this->parameters = array();
    }

    /**
     * Generate Header
     *
     * @param null $realm
     *
     * @return string
     * @throws OAuthException
     */
    public function toHeader($realm = null)
    {
        $first = true;
        if ($realm) {
            $out = "Authorization: OAuth realm=\"" . Util::urlEncode($realm) . "\"";
            $first = false;
        } else {
            $out = "Authorization: OAuth";
        }

        foreach ($this->getParameters() as $key => $val) {
            if (substr($key, 0, 5) != "oauth") continue;

            if (is_array($val)) {
                throw new OAuthException("Arrays are not supported in headers");
            }

            $out .= ($first) ? ' ' : ', ';
            $out .= Util::urlEncode($key) . '="' . Util::urlEncode($val) . '"';
            $first = false;
        }

        return $out;
    }

    /**
     * Creates string from parameters
     *
     * @return null|string
     */
    public function toPostData()
    {
        return Util::buildHttpQuery($this->parameters);
    }

    public function __toString()
    {
        return $this->toUrl();
    }

    public function toString()
    {
        return $this->__toString();
    }

    /**
     * Converts to URL
     *
     * @return string
     */
    public function toUrl()
    {
        $postData = $this->toPostData();
        $out = $this->getNormalizedHttpUrl();
        if ($postData)
            $out .= '?' . $postData;

        return $out;
    }
} 