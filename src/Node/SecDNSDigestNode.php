<?php

namespace Struzik\EPPClient\Extension\SecDNS\Node;

use Struzik\EPPClient\Exception\InvalidArgumentException;
use Struzik\EPPClient\Request\RequestInterface;

class SecDNSDigestNode
{
    public static function create(RequestInterface $request, \DOMElement $parentNode, string $digest): \DOMElement
    {
        if ($digest === '') {
            throw new InvalidArgumentException('Invalid parameter "digest".');
        }

        $node = $request->getDocument()->createElement('secDNS:digest', $digest);
        $parentNode->appendChild($node);

        return $node;
    }
}
