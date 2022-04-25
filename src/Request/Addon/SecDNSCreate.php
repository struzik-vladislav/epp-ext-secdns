<?php

namespace Struzik\EPPClient\Extension\SecDNS\Request\Addon;

use Struzik\EPPClient\Exception\UnexpectedValueException;
use Struzik\EPPClient\Extension\RequestAddonInterface;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSAlgorithmNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSCreateNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSDelegationSignerNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSDigestNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSDigestTypeNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSFlagsNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSKeyDataNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSKeyTagNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSMaxSignatureLifetime;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSProtocolNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSPublicKeyNode;
use Struzik\EPPClient\Extension\SecDNS\Request\Helper\DelegationSignerDataRequest;
use Struzik\EPPClient\Extension\SecDNS\Request\Helper\KeyDataRequest;
use Struzik\EPPClient\Node\Common\ExtensionNode;
use Struzik\EPPClient\Request\RequestInterface;

class SecDNSCreate implements RequestAddonInterface
{
    private string $signatureLifetime = '';
    /** @var DelegationSignerDataRequest[] */
    private array $delegationSignerData = [];
    /** @var KeyDataRequest[] */
    private array $keyData = [];

    public function build(RequestInterface $request): void
    {
        if (!(count($this->delegationSignerData) xor count($this->keyData))) {
            throw new UnexpectedValueException('Invalid parameters "delegationSignerData" or "keyData". One of them must be set.');
        }

        $extensionNode = ExtensionNode::create($request);
        $createNode = SecDNSCreateNode::create($request, $extensionNode);
        if ($this->signatureLifetime !== '') {
            SecDNSMaxSignatureLifetime::create($request, $createNode, $this->signatureLifetime);
        }
        foreach ($this->delegationSignerData as $item) {
            $dataNode = SecDNSDelegationSignerNode::create($request, $createNode);
            SecDNSKeyTagNode::create($request, $dataNode, $item->getKeyTag());
            SecDNSAlgorithmNode::create($request, $dataNode, $item->getAlgorithm());
            SecDNSDigestTypeNode::create($request, $dataNode, $item->getDigestType());
            SecDNSDigestNode::create($request, $dataNode, $item->getDigest());
            $keyData = $item->getKeyData();
            if ($keyData instanceof KeyDataRequest) {
                $keyDataNode = SecDNSKeyDataNode::create($request, $dataNode);
                SecDNSFlagsNode::create($request, $keyDataNode, $keyData->getFlags());
                SecDNSProtocolNode::create($request, $keyDataNode, $keyData->getProtocol());
                SecDNSAlgorithmNode::create($request, $keyDataNode, $keyData->getAlgorithm());
                SecDNSPublicKeyNode::create($request, $keyDataNode, $keyData->getPublicKey());
            }
        }
        foreach ($this->keyData as $item) {
            $keyDataNode = SecDNSKeyDataNode::create($request, $createNode);
            SecDNSFlagsNode::create($request, $keyDataNode, $item->getFlags());
            SecDNSProtocolNode::create($request, $keyDataNode, $item->getProtocol());
            SecDNSAlgorithmNode::create($request, $keyDataNode, $item->getAlgorithm());
            SecDNSPublicKeyNode::create($request, $keyDataNode, $item->getPublicKey());
        }
    }

    public function getSignatureLifetime(): string
    {
        return $this->signatureLifetime;
    }

    public function setSignatureLifetime(string $signatureLifetime): self
    {
        $this->signatureLifetime = $signatureLifetime;

        return $this;
    }

    /**
     * @return DelegationSignerDataRequest[]
     */
    public function getDelegationSignerData(): array
    {
        return $this->delegationSignerData;
    }

    /**
     * @param DelegationSignerDataRequest[] $delegationSignerData
     */
    public function setDelegationSignerData(array $delegationSignerData): self
    {
        $this->delegationSignerData = $delegationSignerData;

        return $this;
    }

    /**
     * @return KeyDataRequest[]
     */
    public function getKeyData(): array
    {
        return $this->keyData;
    }

    /**
     * @param KeyDataRequest[] $keyData
     */
    public function setKeyData(array $keyData): self
    {
        $this->keyData = $keyData;

        return $this;
    }
}
