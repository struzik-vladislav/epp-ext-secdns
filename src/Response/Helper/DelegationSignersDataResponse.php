<?php

namespace Struzik\EPPClient\Extension\SecDNS\Response\Helper;

use Struzik\EPPClient\Exception\UnexpectedValueException;
use Struzik\EPPClient\Response\ResponseInterface;

class DelegationSignersDataResponse
{
    private ResponseInterface $response;
    private \DOMNode $node;

    public function __construct(ResponseInterface $response, \DOMNode $node)
    {
        if ($node->nodeName !== 'secDNS:dsData') {
            throw new UnexpectedValueException(sprintf('The name of the passed node must be "secDNS:dsData", "%s" given.', $node->nodeName));
        }

        $this->response = $response;
        $this->node = $node;
    }

    public function getKeyTag(): string
    {
        return $this->response->getFirst('secDNS:keyTag', $this->node)->nodeValue;
    }

    public function getAlgorithm(): string
    {
        return $this->response->getFirst('secDNS:alg', $this->node)->nodeValue;
    }

    public function getDigestType(): string
    {
        return $this->response->getFirst('secDNS:digestType', $this->node)->nodeValue;
    }

    public function getDigest(): string
    {
        return $this->response->getFirst('secDNS:digest', $this->node)->nodeValue;
    }

    public function getKeyData(): ?KeyDataResponse
    {
        $node = $this->response->getFirst('secDNS:keyData', $this->node);
        if ($node === null) {
            return null;
        }

        return new KeyDataResponse($this->response, $node);
    }
}
