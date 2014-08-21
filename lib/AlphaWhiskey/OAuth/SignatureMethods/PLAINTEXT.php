<?php
/*
 * This file is part of AlphaWhiksey\OAuth
 *
 * Copyright (c) 2007 Andy Smith
 * Copyright (c) 2014 Daniel A. Hawton
 *
 * For full copyright information, please see the LICENSE file that was distributed with the source
 */

namespace AlphaWhiskey\OAuth\SignatureMethods;


class PLAINTEXT extends SignatureMethodInterface
{
    public function getName()
    {
        return "PLAINTEXT";
    }

    public function buildSignature(\AlphaWhiskey\OAuth\Request $request, \AlphaWhiskey\OAuth\Consumer $consumer, \AlphaWhiskey\OAuth\Token $token)
    {
        $keyParts = array($consumer->secret, ($token) ? $token->secret : null);

        $keyParts = \AlphaWhiskey\OAuth\Util::urlEncode($keyParts);
        $key = implode("&", $keyParts);
        $request->baseString = $key;

        return $key;
    }
} 