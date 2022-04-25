<?php

namespace Struzik\EPPClient\Extension\SecDNS\Node;

use Struzik\EPPClient\Exception\InvalidArgumentException;
use Struzik\EPPClient\Request\RequestInterface;

class SecDNSKeyTagNode
{
    public static function create(RequestInterface $request, \DOMElement $parentNode, string $keyTag): \DOMElement
    {
        if ($keyTag === '') {
            throw new InvalidArgumentException('Invalid parameter "keyTag".');
        }

        $node = $request->getDocument()->createElement('secDNS:keyTag', $keyTag);
        $parentNode->appendChild($node);

        return $node;
    }
}
