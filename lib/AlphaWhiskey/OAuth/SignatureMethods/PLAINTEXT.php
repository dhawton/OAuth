<?php
/*
 * This file is part of dhawton\OAuth
 *
 * Copyright (c) 2007 Andy Smith
 * Copyright (c) 2014 Daniel A. Hawton
 *
 * For full copyright information, please see the LICENSE file that was distributed with the source
 */

namespace dhawton\OAuth\SignatureMethods;


class PLAINTEXT extends SignatureMethodInterface
{
    public function getName()
    {
        return "PLAINTEXT";
    }

    public function buildSignature(\dhawton\OAuth\Request $request, \dhawton\OAuth\Consumer $consumer, \dhawton\OAuth\Token $token)
    {
        $keyParts = array($consumer->secret, ($token) ? $token->secret : null);

        $keyParts = \dhawton\OAuth\Util::urlEncode($keyParts);
        $key = implode("&", $keyParts);
        $request->baseString = $key;

        return $key;
    }
} 