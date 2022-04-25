<?php

namespace Struzik\EPPClient\Extension\SecDNS\Node;

use Struzik\EPPClient\Exception\UnexpectedValueException;
use Struzik\EPPClient\Extension\SecDNS\SecDNSExtension;
use Struzik\EPPClient\Request\RequestInterface;

class SecDNSCreateNode
{
    public static function create(RequestInterface $request, \DOMElement $parentNode): \DOMElement
    {
        $namespace = $request->getClient()
            ->getExtNamespaceCollection()
            ->offsetGet(SecDNSExtension::NS_NAME_SECDNS);
        if (!$namespace) {
            throw new UnexpectedValueException('URI of the secDNS namespace cannot be empty.');
        }

        $node = $request->getDocument()->createElement('secDNS:create');
        $node->setAttribute('xmlns:secDNS', $namespace);
        $parentNode->appendChild($node);

        return $node;
    }
}
