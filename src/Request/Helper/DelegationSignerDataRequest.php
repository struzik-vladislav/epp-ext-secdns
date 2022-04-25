<?php

namespace Struzik\EPPClient\Extension\SecDNS\Request\Helper;

class DelegationSignerDataRequest
{
    private string $keyTag = '';
    private string $algorithm = '';
    private string $digestType = '';
    private string $digest = '';
    private ?KeyDataRequest $keyData = null;

    public function getKeyTag(): string
    {
        return $this->keyTag;
    }

    public function setKeyTag(string $keyTag): self
    {
        $this->keyTag = $keyTag;

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

    public function getDigestType(): string
    {
        return $this->digestType;
    }

    public function setDigestType(string $digestType): self
    {
        $this->digestType = $digestType;

        return $this;
    }

    public function getDigest(): string
    {
        return $this->digest;
    }

    public function setDigest(string $digest): self
    {
        $this->digest = $digest;

        return $this;
    }

    public function getKeyData(): ?KeyDataRequest
    {
        return $this->keyData;
    }

    public function setKeyData(?KeyDataRequest $keyData): self
    {
        $this->keyData = $keyData;

        return $this;
    }
}
