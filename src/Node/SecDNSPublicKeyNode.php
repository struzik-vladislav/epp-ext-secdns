<?php

namespace Struzik\EPPClient\Extension\SecDNS\Node;

use Struzik\EPPClient\Exception\InvalidArgumentException;
use Struzik\EPPClient\Request\RequestInterface;

class SecDNSPublicKeyNode
{
    public static function create(RequestInterface $request, \DOMElement $parentNode, string $publicKey): \DOMElement
    {
        if ($publicKey === '') {
            throw new InvalidArgumentException('Invalid parameter "publicKey".');
        }

        $node = $request->getDocument()->createElement('secDNS:pubKey', $publicKey);
        $parentNode->appendChild($node);

        return $node;
    }
}
