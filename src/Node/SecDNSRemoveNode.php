<?php

namespace Struzik\EPPClient\Extension\SecDNS\Node;

use Struzik\EPPClient\Request\RequestInterface;

class SecDNSRemoveNode
{
    public static function create(RequestInterface $request, \DOMElement $parentNode): \DOMElement
    {
        $node = $request->getDocument()->createElement('secDNS:rem');
        $parentNode->appendChild($node);

        return $node;
    }
}
