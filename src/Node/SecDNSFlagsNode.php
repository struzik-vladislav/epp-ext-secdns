<?php

namespace Struzik\EPPClient\Extension\SecDNS\Node;

use Struzik\EPPClient\Exception\InvalidArgumentException;
use Struzik\EPPClient\Request\RequestInterface;

class SecDNSFlagsNode
{
    public const DEFAULT = '257';

    public static function create(RequestInterface $request, \DOMElement $parentNode, string $flags): \DOMElement
    {
        if ($flags === '') {
            throw new InvalidArgumentException('Invalid parameter "flags".');
        }

        $node = $request->getDocument()->createElement('secDNS:flags', $flags);
        $parentNode->appendChild($node);

        return $node;
    }
}
