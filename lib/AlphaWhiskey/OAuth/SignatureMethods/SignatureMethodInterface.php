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
use \AlphaWhiskey\OAuth;

abstract class SignatureMethodInterface
{
    /* Return name of Signature Method (ie HMAC-SHA1, RSA-SHA1, PLAINTEXT)
     * @return string
     */
    abstract public function getName();

    /* Build signature.  Output must not be url encoded as encoding is handled when request is serialized.
     * @param \AlphaWhiskey\OAuth\Request $request
     * @param \AlphaWhiskey\OAuth\Consumer $consumer
     * @param \AlphaWhiskey\OAuth\Token $token
     * @return string
     */
    abstract public function buildSignature(\AlphaWhiskey\OAuth\Request $request, \AlphaWhiskey\OAuth\Consumer $consumer, \AlphaWhiskey\OAuth\Token $token);

    /**
     * Verifies signature is correct.
     *
     * @param \AlphaWhiskey\OAuth\Request $request
     * @param \AlphaWhiskey\OAuth\Consumer $consumer
     * @param \AlphaWhiskey\OAuth\Token $token
     * @param $signature
     *
     * @return bool
     */
    public function checkSignature(\AlphaWhiskey\OAuth\Request $request, \AlphaWhiskey\OAuth\Consumer $consumer, \AlphaWhiskey\OAuth\Token $token, $signature)
    {
        $built = $this->buildSignature($request, $consumer, $token);

        if (strlen($built) == 0 || strlen($signature) == 0)
            return false;

        if (strlen($built) != strlen($signature))
            return false;

        $result = 0;
        for ($i = 0; $i < strlen($signature); $i++) {
            $result |= ord($built{$i}) ^ ord($signature{$i});
        }

        return $result == 0;
    }
} 