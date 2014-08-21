<?php
/*
 * This file is part of AlphaWhiksey\OAuth
 *
 * Copyright (c) 2007 Andy Smith
 * Copyright (c) 2014 Daniel A. Hawton
 *
 * For full copyright information, please see the LICENSE file that was distributed with the source
 */

namespace dhawton\OAuth\SignatureMethods;


class RSA_SHA1 extends SignatureMethodInterface
{
    private $cert = false;

    public function getName()
    {
        return "RSA-SHA1";
    }

    public function __construct($cert)
    {
        $this->cert = $cert;
    }

    /**
     * @param \dhawton\OAuth\Request $request
     *
     * @return string   Return string representation of cert
     */
    private function fetchPrivateCert(\dhawton\OAuth\Request &$request)
    {
        return $this->cert;
    }

    public function buildSignature(\dhawton\OAuth\Request $request, \dhawton\OAuth\Consumer $consumer, \dhawton\OAuth\Token $token)
    {
        $baseString = $request->getSignatureBaseString();
        $request->baseString = $baseString;

        $cert = $this->fetchPrivateCert($request);
        $privatekeyid = openssl_get_privatekey($cert);
        $ok = openssl_sign($baseString, $signature, $privatekeyid);
        openssl_free_key($privatekeyid);

        return base64_encode($signature);
    }
} 