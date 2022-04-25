<?php

namespace Struzik\EPPClient\Extension\SecDNS\Response\Addon;

use Struzik\EPPClient\Extension\SecDNS\Response\Helper\DelegationSignersDataResponse;
use Struzik\EPPClient\Extension\SecDNS\Response\Helper\KeyDataResponse;
use Struzik\EPPClient\Response\ResponseInterface;

/**
 * Object representation of the add-on for domain information command.
 */
class SecDNSInfo
{
    private ResponseInterface $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function getMaxSignatureLifetime(): ?string
    {
        $node = $this->response->getFirst('//epp:epp/epp:response/epp:extension/secDNS:infData/secDNS:maxSigLife');
        if ($node === null) {
            return null;
        }

        return $node->nodeValue;
    }

    /**
     * @return DelegationSignersDataResponse[]
     */
    public function getDelegationSignersData(): array
    {
        $nodes = $this->response->get('//epp:epp/epp:response/epp:extension/secDNS:infData/secDNS:dsData');

        return array_map(fn (\DOMNode $node) => new DelegationSignersDataResponse($this->response, $node), iterator_to_array($nodes));
    }

    /**
     * @return KeyDataResponse[]
     */
    public function getKeyData(): array
    {
        $nodes = $this->response->get('//epp:epp/epp:response/epp:extension/secDNS:infData/secDNS:keyData');

        return array_map(fn (\DOMNode $node) => new KeyDataResponse($this->response, $node), iterator_to_array($nodes));
    }
}
