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

/**
 * OAuth Consumer class
 * @author Daniel A. Hawton <daniel@hawton.com>
 */

class Consumer {
    public $key;
    public $secret;
    public $callbackUrl;

    function __construct ($key, $secret, $callbackUrl = null)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->callbackUrl = $callbackUrl;
    }

    function __toString()
    {
        return "OAuthConsumer[key=$this->key,secret=$this->secret]";
    }
}