<?php

namespace Struzik\EPPClient\Extension\SecDNS\Node;

use Struzik\EPPClient\Request\RequestInterface;

class SecDNSAllNode
{
    public static function create(RequestInterface $request, \DOMElement $parentNode): \DOMElement
    {
        $node = $request->getDocument()->createElement('secDNS:all', 'true');
        $parentNode->appendChild($node);

        return $node;
    }
}
