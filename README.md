# DNS Security Extension for the EPP Client

Domain Name System (DNS) Security extension for the EPP (Extensible Provisioning Protocol).

Implemented according to [RFC 5910](https://datatracker.ietf.org/doc/html/rfc5910) Domain Name System (DNS) Security Extensions Mapping for the Extensible Provisioning Protocol (EPP).

Extension for [struzik-vladislav/epp-client](https://github.com/struzik-vladislav/epp-client) library.

## Usage
```php
<?php

use Psr\Log\NullLogger;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSAlgorithmNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSDigestTypeNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSFlagsNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSProtocolNode;
use Struzik\EPPClient\Extension\SecDNS\Request\Addon\SecDNSCreate;
use Struzik\EPPClient\Extension\SecDNS\Request\Addon\SecDNSUpdate;
use Struzik\EPPClient\Extension\SecDNS\Request\Helper\DelegationSignerDataRequest;
use Struzik\EPPClient\Extension\SecDNS\Request\Helper\KeyDataRequest;
use Struzik\EPPClient\Extension\SecDNS\SecDNSExtension;
use Struzik\EPPClient\Node\Domain\DomainContactNode;
use Struzik\EPPClient\Node\Domain\DomainPeriodNode;
use Struzik\EPPClient\Request\Domain\CreateDomainRequest;
use Struzik\EPPClient\Request\Domain\Helper\HostObject;
use Struzik\EPPClient\Request\Domain\UpdateDomainRequest;

// ...

$client->pushExtension(new SecDNSExtension('urn:ietf:params:xml:ns:secDNS-1.1', new NullLogger()));

// ...

$request = new CreateDomainRequest($this->eppClient);
$request->setDomain('example.com');
$request->setPeriod(2);
$request->setUnit(DomainPeriodNode::UNIT_YEAR);
$request->setNameservers([
    (new HostObject())->setHost('ns1.example.com'),
    (new HostObject())->setHost('ns2.example.com'),
]);
$request->setRegistrant('jd1234');
$request->setContacts([
    DomainContactNode::TYPE_ADMIN => 'sh8013',
    DomainContactNode::TYPE_TECH => 'sh8013',
]);
$request->setPassword('2fooBAR');

$requestAddon = (new SecDNSCreate())
    ->setSignatureLifetime('604800')
    ->setDelegationSignerData([
        (new DelegationSignerDataRequest())
            ->setKeyTag('12345')
            ->setAlgorithm(SecDNSAlgorithmNode::ALG_DSA)
            ->setDigestType(SecDNSDigestTypeNode::TYPE_SHA_1)
            ->setDigest('49FD46E6C4B45C55D4AC')
            ->setKeyData(
                (new KeyDataRequest())
                    ->setFlags(SecDNSFlagsNode::DEFAULT)
                    ->setProtocol(SecDNSProtocolNode::DEFAULT)
                    ->setAlgorithm(SecDNSAlgorithmNode::ALG_RSAMD5)
                    ->setPublicKey('AQPJ////4Q==')
            ),
    ]);
$request->addExtAddon($requestAddon);

$response = $client->send($request);

// ...

$request = new UpdateDomainRequest($this->eppClient);
$request->setDomain('example.com');

$requestAddon = new SecDNSUpdate();
$requestAddon->setUrgent(true);
$requestAddon->setRemoveAll(true);
$requestAddon->setDelegationSignerDataForAdding([
    (new DelegationSignerDataRequest())
        ->setKeyTag('12346')
        ->setAlgorithm(SecDNSAlgorithmNode::ALG_DSA)
        ->setDigestType(SecDNSDigestTypeNode::TYPE_SHA_1)
        ->setDigest('38EC35D5B3A34B44C39B'),
]);
$request->addExtAddon($requestAddon);

$response = $client->send($request);
```
