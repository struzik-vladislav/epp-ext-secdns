<?php

namespace Struzik\EPPClient\Extension\SecDNS\Node;

use Struzik\EPPClient\Exception\InvalidArgumentException;
use Struzik\EPPClient\Request\RequestInterface;

class SecDNSDigestTypeNode
{
    // https://www.iana.org/assignments/ds-rr-types/ds-rr-types.xhtml
    public const TYPE_SHA_1 = '1'; // SHA-1
    public const TYPE_SHA_256 = '2'; // SHA-256
    public const TYPE_GOST_R_34_11_94 = '3'; // GOST R 34.11-94
    public const TYPE_SHA_384 = '4'; // SHA-384

    public static function create(RequestInterface $request, \DOMElement $parentNode, string $digestType): \DOMElement
    {
        if ($digestType === '') {
            throw new InvalidArgumentException('Invalid parameter "digestType".');
        }

        $node = $request->getDocument()->createElement('secDNS:digestType', $digestType);
        $parentNode->appendChild($node);

        return $node;
    }
}
