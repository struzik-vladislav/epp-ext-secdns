<?php

namespace Struzik\EPPClient\Extension\SecDNS\Node;

use Struzik\EPPClient\Exception\InvalidArgumentException;
use Struzik\EPPClient\Request\RequestInterface;

class SecDNSProtocolNode
{
    public const DEFAULT = '3';

    public static function create(RequestInterface $request, \DOMElement $parentNode, string $protocol): \DOMElement
    {
        if ($protocol === '') {
            throw new InvalidArgumentException('Invalid parameter "protocol".');
        }

        $node = $request->getDocument()->createElement('secDNS:protocol', $protocol);
        $parentNode->appendChild($node);

        return $node;
    }
}
