<?php

namespace Struzik\EPPClient\Extension\SecDNS\Node;

use Struzik\EPPClient\Exception\InvalidArgumentException;
use Struzik\EPPClient\Request\RequestInterface;

class SecDNSAlgorithmNode
{
    // https://www.iana.org/assignments/dns-sec-alg-numbers/dns-sec-alg-numbers.xhtml#dns-sec-alg-numbers-1
    public const ALG_RSAMD5 = '1'; // RSA/MD5 (deprecated, see 5)
    public const ALG_DH = '2'; // Diffie-Hellman
    public const ALG_DSA = '3'; // DSA/SHA1
    public const ALG_RSASHA1 = '5'; // RSA/SHA-1
    public const ALG_DSA_NSEC3_SHA1 = '6'; // DSA-NSEC3-SHA1
    public const ALG_RSASHA1_NSEC3_SHA1 = '7'; // RSASHA1-NSEC3-SHA1
    public const ALG_RSASHA256 = '8'; // RSA/SHA-256
    public const ALG_RSASHA512 = '10'; // RSA/SHA-512
    public const ALG_ECC_GOST = '12'; // GOST R 34.10-2001
    public const ALG_ECDSAP256SHA256 = '13'; // ECDSA Curve P-256 with SHA-256
    public const ALG_ECDSAP384SHA384 = '14'; // ECDSA Curve P-384 with SHA-384
    public const ALG_ED25519 = '15'; // Ed25519
    public const ALG_ED448 = '16'; // Ed448
    public const ALG_INDIRECT = '252'; // Reserved for Indirect Keys
    public const ALG_PRIVATEDNS = '253'; // private algorithm
    public const ALG_PRIVATEOID = '254'; // private algorithm OID

    public static function create(RequestInterface $request, \DOMElement $parentNode, string $algorithm): \DOMElement
    {
        if ($algorithm === '') {
            throw new InvalidArgumentException('Invalid parameter "algorithm".');
        }

        $node = $request->getDocument()->createElement('secDNS:alg', $algorithm);
        $parentNode->appendChild($node);

        return $node;
    }
}
