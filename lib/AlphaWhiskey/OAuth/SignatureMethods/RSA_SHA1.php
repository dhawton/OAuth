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
     * @param \AlphaWhiskey\OAuth\Request $request
     *
     * @return string   Return string representation of cert
     */
    private function fetchPrivateCert(\AlphaWhiskey\OAuth\Request &$request)
    {
        return $this->cert;
    }

    public function buildSignature(\AlphaWhiskey\OAuth\Request $request, \AlphaWhiskey\OAuth\Consumer $consumer, \AlphaWhiskey\OAuth\Token $token)
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