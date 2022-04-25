<?php

namespace Struzik\EPPClient\Extension\SecDNS\Tests\Request\Addon;

use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSAlgorithmNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSDigestTypeNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSFlagsNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSProtocolNode;
use Struzik\EPPClient\Extension\SecDNS\Request\Addon\SecDNSCreate;
use Struzik\EPPClient\Extension\SecDNS\Request\Helper\DelegationSignerDataRequest;
use Struzik\EPPClient\Extension\SecDNS\Request\Helper\KeyDataRequest;
use Struzik\EPPClient\Extension\SecDNS\Tests\SecDNSTestCase;
use Struzik\EPPClient\Node\Domain\DomainContactNode;
use Struzik\EPPClient\Node\Domain\DomainPeriodNode;
use Struzik\EPPClient\Request\Domain\CreateDomainRequest;
use Struzik\EPPClient\Request\Domain\Helper\HostObject;

class SecDNSCreateTest extends SecDNSTestCase
{
    public function testDelegationSignerDataWithoutKeyData(): void
    {
        $expected = <<<'EOF'
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
  <command>
    <create>
      <domain:create xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
        <domain:name>example.com</domain:name>
        <domain:period unit="y">2</domain:period>
        <domain:ns>
          <domain:hostObj>ns1.example.com</domain:hostObj>
          <domain:hostObj>ns2.example.com</domain:hostObj>
        </domain:ns>
        <domain:registrant>jd1234</domain:registrant>
        <domain:contact type="admin">sh8013</domain:contact>
        <domain:contact type="tech">sh8013</domain:contact>
        <domain:authInfo>
          <domain:pw>2fooBAR</domain:pw>
        </domain:authInfo>
      </domain:create>
    </create>
    <extension>
      <secDNS:create xmlns:secDNS="urn:ietf:params:xml:ns:secDNS-1.1">
        <secDNS:maxSigLife>604800</secDNS:maxSigLife>
        <secDNS:dsData>
          <secDNS:keyTag>12345</secDNS:keyTag>
          <secDNS:alg>3</secDNS:alg>
          <secDNS:digestType>1</secDNS:digestType>
          <secDNS:digest>49FD46E6C4B45C55D4AC</secDNS:digest>
        </secDNS:dsData>
      </secDNS:create>
    </extension>
    <clTRID>TEST-REQUEST-ID</clTRID>
  </command>
</epp>

EOF;
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
                    ->setKeyData(null),
            ]);
        $request->addExtAddon($requestAddon);
        $request->build();

        $this->assertSame($expected, $request->getDocument()->saveXML());
        $this->assertSame('604800', $requestAddon->getSignatureLifetime());
        $this->assertCount(0, $requestAddon->getKeyData());
        $this->assertCount(1, $requestAddon->getDelegationSignerData());
        $this->assertInstanceOf(DelegationSignerDataRequest::class, $requestAddon->getDelegationSignerData()[0]);
    }

    public function testDelegationSignerDataWithKeyData(): void
    {
        $expected = <<<'EOF'
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
  <command>
    <create>
      <domain:create xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
        <domain:name>example.com</domain:name>
        <domain:period unit="y">2</domain:period>
        <domain:ns>
          <domain:hostObj>ns1.example.com</domain:hostObj>
          <domain:hostObj>ns2.example.com</domain:hostObj>
        </domain:ns>
        <domain:registrant>jd1234</domain:registrant>
        <domain:contact type="admin">sh8013</domain:contact>
        <domain:contact type="tech">sh8013</domain:contact>
        <domain:authInfo>
          <domain:pw>2fooBAR</domain:pw>
        </domain:authInfo>
      </domain:create>
    </create>
    <extension>
      <secDNS:create xmlns:secDNS="urn:ietf:params:xml:ns:secDNS-1.1">
        <secDNS:maxSigLife>604800</secDNS:maxSigLife>
        <secDNS:dsData>
          <secDNS:keyTag>12345</secDNS:keyTag>
          <secDNS:alg>3</secDNS:alg>
          <secDNS:digestType>1</secDNS:digestType>
          <secDNS:digest>49FD46E6C4B45C55D4AC</secDNS:digest>
          <secDNS:keyData>
            <secDNS:flags>257</secDNS:flags>
            <secDNS:protocol>3</secDNS:protocol>
            <secDNS:alg>1</secDNS:alg>
            <secDNS:pubKey>AQPJ////4Q==</secDNS:pubKey>
          </secDNS:keyData>
        </secDNS:dsData>
      </secDNS:create>
    </extension>
    <clTRID>TEST-REQUEST-ID</clTRID>
  </command>
</epp>

EOF;
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

        $request->build();

        $this->assertSame($expected, $request->getDocument()->saveXML());
        $this->assertSame('604800', $requestAddon->getSignatureLifetime());
        $this->assertCount(0, $requestAddon->getKeyData());
        $this->assertCount(1, $requestAddon->getDelegationSignerData());
        $this->assertInstanceOf(DelegationSignerDataRequest::class, $requestAddon->getDelegationSignerData()[0]);
    }

    public function testKeyData(): void
    {
        $expected = <<<'EOF'
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
  <command>
    <create>
      <domain:create xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
        <domain:name>example.com</domain:name>
        <domain:period unit="y">2</domain:period>
        <domain:ns>
          <domain:hostObj>ns1.example.com</domain:hostObj>
          <domain:hostObj>ns2.example.com</domain:hostObj>
        </domain:ns>
        <domain:registrant>jd1234</domain:registrant>
        <domain:contact type="admin">sh8013</domain:contact>
        <domain:contact type="tech">sh8013</domain:contact>
        <domain:authInfo>
          <domain:pw>2fooBAR</domain:pw>
        </domain:authInfo>
      </domain:create>
    </create>
    <extension>
      <secDNS:create xmlns:secDNS="urn:ietf:params:xml:ns:secDNS-1.1">
        <secDNS:keyData>
          <secDNS:flags>257</secDNS:flags>
          <secDNS:protocol>3</secDNS:protocol>
          <secDNS:alg>1</secDNS:alg>
          <secDNS:pubKey>AQPJ////4Q==</secDNS:pubKey>
        </secDNS:keyData>
      </secDNS:create>
    </extension>
    <clTRID>TEST-REQUEST-ID</clTRID>
  </command>
</epp>

EOF;
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

        $requestAddon = new SecDNSCreate();
        $requestAddon->setKeyData([
            (new KeyDataRequest())
                ->setFlags(SecDNSFlagsNode::DEFAULT)
                ->setProtocol(SecDNSProtocolNode::DEFAULT)
                ->setAlgorithm(SecDNSAlgorithmNode::ALG_RSAMD5)
                ->setPublicKey('AQPJ////4Q=='),
        ]);
        $request->addExtAddon($requestAddon);

        $request->build();

        $this->assertSame($expected, $request->getDocument()->saveXML());
        $this->assertSame('', $requestAddon->getSignatureLifetime());
        $this->assertCount(1, $requestAddon->getKeyData());
        $this->assertCount(0, $requestAddon->getDelegationSignerData());
        $this->assertInstanceOf(KeyDataRequest::class, $requestAddon->getKeyData()[0]);
    }
}
