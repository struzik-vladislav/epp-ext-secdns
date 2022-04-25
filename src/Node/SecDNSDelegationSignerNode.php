<?php

namespace Struzik\EPPClient\Extension\SecDNS\Node;

use Struzik\EPPClient\Request\RequestInterface;

class SecDNSDelegationSignerNode
{
    public static function create(RequestInterface $request, \DOMElement $parentNode): \DOMElement
    {
        $node = $request->getDocument()->createElement('secDNS:dsData');
        $parentNode->appendChild($node);

        return $node;
    }
}
