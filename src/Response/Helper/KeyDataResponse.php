<?php

namespace Struzik\EPPClient\Extension\SecDNS\Response\Helper;

use Struzik\EPPClient\Exception\UnexpectedValueException;
use Struzik\EPPClient\Response\ResponseInterface;

class KeyDataResponse
{
    private ResponseInterface $response;
    private \DOMNode $node;

    public function __construct(ResponseInterface $response, \DOMNode $node)
    {
        if ($node->nodeName !== 'secDNS:keyData') {
            throw new UnexpectedValueException(sprintf('The name of the passed node must be "secDNS:keyData", "%s" given.', $node->nodeName));
        }

        $this->response = $response;
        $this->node = $node;
    }

    public function getFlags(): string
    {
        return $this->response->getFirst('secDNS:flags', $this->node)->nodeValue;
    }

    public function getProtocol(): string
    {
        return $this->response->getFirst('secDNS:protocol', $this->node)->nodeValue;
    }

    public function getAlgorithm(): string
    {
        return $this->response->getFirst('secDNS:alg', $this->node)->nodeValue;
    }

    public function getPublicKey(): string
    {
        return $this->response->getFirst('secDNS:pubKey', $this->node)->nodeValue;
    }
}
