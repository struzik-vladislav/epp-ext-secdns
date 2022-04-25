<?php

namespace Struzik\EPPClient\Extension\SecDNS\Node;

use Struzik\EPPClient\Request\RequestInterface;

class SecDNSKeyDataNode
{
    public static function create(RequestInterface $request, \DOMElement $parentNode): \DOMElement
    {
        $node = $request->getDocument()->createElement('secDNS:keyData');
        $parentNode->appendChild($node);

        return $node;
    }
}
