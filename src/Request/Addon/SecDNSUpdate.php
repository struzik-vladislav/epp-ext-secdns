<?php

namespace Struzik\EPPClient\Extension\SecDNS\Request\Addon;

use Struzik\EPPClient\Exception\UnexpectedValueException;
use Struzik\EPPClient\Extension\RequestAddonInterface;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSAddNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSAlgorithmNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSAllNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSChangeNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSDelegationSignerNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSDigestNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSDigestTypeNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSFlagsNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSKeyDataNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSKeyTagNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSMaxSignatureLifetime;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSProtocolNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSPublicKeyNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSRemoveNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSUpdateNode;
use Struzik\EPPClient\Extension\SecDNS\Request\Helper\DelegationSignerDataRequest;
use Struzik\EPPClient\Extension\SecDNS\Request\Helper\KeyDataRequest;
use Struzik\EPPClient\Node\Common\ExtensionNode;
use Struzik\EPPClient\Request\RequestInterface;

class SecDNSUpdate implements RequestAddonInterface
{
    private bool $urgent = false;
    private bool $removeAll = false;
    /** @var DelegationSignerDataRequest[] */
    private array $delegationSignerDataForRemoving = [];
    /** @var KeyDataRequest[] */
    private array $keyDataForRemoving = [];
    /** @var DelegationSignerDataRequest[] */
    private array $delegationSignerDataForAdding = [];
    /** @var KeyDataRequest[] */
    private array $keyDataForAdding = [];
    private string $signatureLifetime = '';

    public function build(RequestInterface $request): void
    {
        $extensionNode = ExtensionNode::create($request);
        $updateNode = SecDNSUpdateNode::create($request, $extensionNode, $this->urgent);

        if ($this->removeAll || count($this->delegationSignerDataForRemoving) || count($this->keyDataForRemoving)) {
            if (($this->removeAll && (count($this->delegationSignerDataForRemoving) || count($this->keyDataForRemoving)))
                || (!$this->removeAll && !(count($this->delegationSignerDataForRemoving) xor count($this->keyDataForRemoving)))
            ) {
                throw new UnexpectedValueException('Invalid parameters "removeAll" or "delegationSignerDataForRemoving" or "keyDataForRemoving". One of them must be set.');
            }

            $removeNode = SecDNSRemoveNode::create($request, $updateNode);
            if ($this->removeAll) {
                SecDNSAllNode::create($request, $removeNode);
            }
            foreach ($this->delegationSignerDataForRemoving as $item) {
                $dataNode = SecDNSDelegationSignerNode::create($request, $removeNode);
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
            foreach ($this->keyDataForRemoving as $item) {
                $keyDataNode = SecDNSKeyDataNode::create($request, $removeNode);
                SecDNSFlagsNode::create($request, $keyDataNode, $item->getFlags());
                SecDNSProtocolNode::create($request, $keyDataNode, $item->getProtocol());
                SecDNSAlgorithmNode::create($request, $keyDataNode, $item->getAlgorithm());
                SecDNSPublicKeyNode::create($request, $keyDataNode, $item->getPublicKey());
            }
        }

        if (count($this->delegationSignerDataForAdding) || count($this->keyDataForAdding)) {
            if (!(count($this->delegationSignerDataForAdding) xor count($this->keyDataForAdding))) {
                throw new UnexpectedValueException('Invalid parameters "delegationSignerDataForAdding" or "keyDataForAdding". One of them must be set.');
            }

            $addNode = SecDNSAddNode::create($request, $updateNode);
            foreach ($this->delegationSignerDataForAdding as $item) {
                $dataNode = SecDNSDelegationSignerNode::create($request, $addNode);
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
            foreach ($this->keyDataForAdding as $item) {
                $keyDataNode = SecDNSKeyDataNode::create($request, $addNode);
                SecDNSFlagsNode::create($request, $keyDataNode, $item->getFlags());
                SecDNSProtocolNode::create($request, $keyDataNode, $item->getProtocol());
                SecDNSAlgorithmNode::create($request, $keyDataNode, $item->getAlgorithm());
                SecDNSPublicKeyNode::create($request, $keyDataNode, $item->getPublicKey());
            }
        }

        if ($this->signatureLifetime !== '') {
            $changeNode = SecDNSChangeNode::create($request, $updateNode);
            SecDNSMaxSignatureLifetime::create($request, $changeNode, $this->signatureLifetime);
        }
    }

    public function isUrgent(): bool
    {
        return $this->urgent;
    }

    public function setUrgent(bool $urgent): void
    {
        $this->urgent = $urgent;
    }

    public function isRemoveAll(): bool
    {
        return $this->removeAll;
    }

    public function setRemoveAll(bool $removeAll): void
    {
        if ($removeAll) {
            $this->delegationSignerDataForRemoving = [];
            $this->keyDataForRemoving = [];
        }
        $this->removeAll = $removeAll;
    }

    /**
     * @return DelegationSignerDataRequest[]
     */
    public function getDelegationSignerDataForRemoving(): array
    {
        return $this->delegationSignerDataForRemoving;
    }

    /**
     * @param DelegationSignerDataRequest[] $delegationSignerDataForRemoving
     */
    public function setDelegationSignerDataForRemoving(array $delegationSignerDataForRemoving): void
    {
        if (count($delegationSignerDataForRemoving) > 0) {
            $this->removeAll = false;
        }
        $this->delegationSignerDataForRemoving = $delegationSignerDataForRemoving;
    }

    /**
     * @return KeyDataRequest[]
     */
    public function getKeyDataForRemoving(): array
    {
        return $this->keyDataForRemoving;
    }

    /**
     * @param KeyDataRequest[] $keyDataForRemoving
     */
    public function setKeyDataForRemoving(array $keyDataForRemoving): void
    {
        if (count($keyDataForRemoving) > 0) {
            $this->removeAll = false;
        }
        $this->keyDataForRemoving = $keyDataForRemoving;
    }

    /**
     * @return DelegationSignerDataRequest[]
     */
    public function getDelegationSignerDataForAdding(): array
    {
        return $this->delegationSignerDataForAdding;
    }

    /**
     * @param DelegationSignerDataRequest[] $delegationSignerDataForAdding
     */
    public function setDelegationSignerDataForAdding(array $delegationSignerDataForAdding): void
    {
        $this->delegationSignerDataForAdding = $delegationSignerDataForAdding;
    }

    /**
     * @return KeyDataRequest[]
     */
    public function getKeyDataForAdding(): array
    {
        return $this->keyDataForAdding;
    }

    /**
     * @param KeyDataRequest[] $keyDataForAdding
     */
    public function setKeyDataForAdding(array $keyDataForAdding): void
    {
        $this->keyDataForAdding = $keyDataForAdding;
    }

    public function getSignatureLifetime(): string
    {
        return $this->signatureLifetime;
    }

    public function setSignatureLifetime(string $signatureLifetime): void
    {
        $this->signatureLifetime = $signatureLifetime;
    }
}
