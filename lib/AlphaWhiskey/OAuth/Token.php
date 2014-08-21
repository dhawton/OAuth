<?php
/*
 * This file is part of AlphaWhiksey\OAuth
 *
 * Copyright (c) 2007 Andy Smith
 * Copyright (c) 2014 Daniel A. Hawton
 *
 * For full copyright information, please see the LICENSE file that was distributed with the source
 */

namespace AlphaWhiskey\OAuth;


class Token
{
    public $key;
    public $secret;

    public function toString()
    {
        return $this->__toString();
    }

    function __construct($key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    function __toString()
    {
        return "oauth_token=" . Util::urlEncode($this->key) .
            '&oauth_token_secret=' . Util::urlEncode($this->secret);
    }
} 