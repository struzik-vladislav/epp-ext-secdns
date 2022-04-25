<?php

namespace Struzik\EPPClient\Extension\SecDNS\Node;

use Struzik\EPPClient\Request\RequestInterface;

class SecDNSAddNode
{
    public static function create(RequestInterface $request, \DOMElement $parentNode): \DOMElement
    {
        $node = $request->getDocument()->createElement('secDNS:add');
        $parentNode->appendChild($node);

        return $node;
    }
}
