<?php

namespace Struzik\EPPClient\Extension\SecDNS\Node;

use Struzik\EPPClient\Request\RequestInterface;

class SecDNSMaxSignatureLifetime
{
    public static function create(RequestInterface $request, \DOMElement $parentNode, string $signatureLifetime): \DOMElement
    {
        $node = $request->getDocument()->createElement('secDNS:maxSigLife', $signatureLifetime);
        $parentNode->appendChild($node);

        return $node;
    }
}
