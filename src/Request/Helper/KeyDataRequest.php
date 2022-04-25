<?php

namespace Struzik\EPPClient\Extension\SecDNS\Request\Helper;

use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSFlagsNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSProtocolNode;

class KeyDataRequest
{
    private string $flags = SecDNSFlagsNode::DEFAULT;
    private string $protocol = SecDNSProtocolNode::DEFAULT;
    private string $algorithm = '';
    private string $publicKey = '';

    public function getFlags(): string
    {
        return $this->flags;
    }

    public function setFlags(string $flags): self
    {
        $this->flags = $flags;

        return $this;
    }

    public function getProtocol(): string
    {
        return $this->protocol;
    }

    public function setProtocol(string $protocol): self
    {
        $this->protocol = $protocol;

        return $this;
    }

    public function getAlgorithm(): string
    {
        return $this->algorithm;
    }

    public function setAlgorithm(string $algorithm): self
    {
        $this->algorithm = $algorithm;

        return $this;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function setPublicKey(string $publicKey): self
    {
        $this->publicKey = $publicKey;

        return $this;
    }
}
