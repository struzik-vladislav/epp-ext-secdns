<?php

namespace Struzik\EPPClient\Extension\SecDNS\Tests\Request\Addon;

use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSAlgorithmNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSDigestTypeNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSFlagsNode;
use Struzik\EPPClient\Extension\SecDNS\Node\SecDNSProtocolNode;
use Struzik\EPPClient\Extension\SecDNS\Request\Addon\SecDNSUpdate;
use Struzik\EPPClient\Extension\SecDNS\Request\Helper\DelegationSignerDataRequest;
use Struzik\EPPClient\Extension\SecDNS\Request\Helper\KeyDataRequest;
use Struzik\EPPClient\Extension\SecDNS\Tests\SecDNSTestCase;
use Struzik\EPPClient\Request\Domain\UpdateDomainRequest;

class SecDNSUpdateTest extends SecDNSTestCase
{
    /**
     * Example <update> Command, Adding and Removing DS. Data Using the DS Data Interface.
     */
    public function testDelegationSignerDataAddingAndRemoving(): void
    {
        $expected = <<<'EOF'
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
  <command>
    <update>
      <domain:update xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
        <domain:name>example.com</domain:name>
      </domain:update>
    </update>
    <extension>
      <secDNS:update xmlns:secDNS="urn:ietf:params:xml:ns:secDNS-1.1">
        <secDNS:rem>
          <secDNS:dsData>
            <secDNS:keyTag>12345</secDNS:keyTag>
            <secDNS:alg>3</secDNS:alg>
            <secDNS:digestType>1</secDNS:digestType>
            <secDNS:digest>38EC35D5B3A34B33C99B</secDNS:digest>
          </secDNS:dsData>
        </secDNS:rem>
        <secDNS:add>
          <secDNS:dsData>
            <secDNS:keyTag>12346</secDNS:keyTag>
            <secDNS:alg>3</secDNS:alg>
            <secDNS:digestType>1</secDNS:digestType>
            <secDNS:digest>38EC35D5B3A34B44C39B</secDNS:digest>
          </secDNS:dsData>
        </secDNS:add>
      </secDNS:update>
    </extension>
    <clTRID>TEST-REQUEST-ID</clTRID>
  </command>
</epp>

EOF;
        $request = new UpdateDomainRequest($this->eppClient);
        $request->setDomain('example.com');
        $requestAddon = new SecDNSUpdate();
        $requestAddon->setDelegationSignerDataForRemoving([
            (new DelegationSignerDataRequest())
                ->setKeyTag('12345')
                ->setAlgorithm(SecDNSAlgorithmNode::ALG_DSA)
                ->setDigestType(SecDNSDigestTypeNode::TYPE_SHA_1)
                ->setDigest('38EC35D5B3A34B33C99B'),
        ]);
        $requestAddon->setDelegationSignerDataForAdding([
            (new DelegationSignerDataRequest())
                ->setKeyTag('12346')
                ->setAlgorithm(SecDNSAlgorithmNode::ALG_DSA)
                ->setDigestType(SecDNSDigestTypeNode::TYPE_SHA_1)
                ->setDigest('38EC35D5B3A34B44C39B'),
        ]);
        $request->addExtAddon($requestAddon);
        $request->build();

        $this->assertSame($expected, $request->getDocument()->saveXML());
    }

    /**
     * Updating the maxSigLife.
     */
    public function testSignatureLifetimeUpdating(): void
    {
        $expected = <<<'EOF'
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
  <command>
    <update>
      <domain:update xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
        <domain:name>example.com</domain:name>
      </domain:update>
    </update>
    <extension>
      <secDNS:update xmlns:secDNS="urn:ietf:params:xml:ns:secDNS-1.1">
        <secDNS:chg>
          <secDNS:maxSigLife>605900</secDNS:maxSigLife>
        </secDNS:chg>
      </secDNS:update>
    </extension>
    <clTRID>TEST-REQUEST-ID</clTRID>
  </command>
</epp>

EOF;
        $request = new UpdateDomainRequest($this->eppClient);
        $request->setDomain('example.com');
        $requestAddon = new SecDNSUpdate();
        $requestAddon->setSignatureLifetime('605900');
        $request->addExtAddon($requestAddon);
        $request->build();

        $this->assertSame($expected, $request->getDocument()->saveXML());
    }

    /**
     * Adding and Removing Key Data Using the Key Data Interface, and Setting maxSigLife.
     */
    public function testKeyDataAddingAndRemoving(): void
    {
        $expected = <<<'EOF'
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
  <command>
    <update>
      <domain:update xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
        <domain:name>example.com</domain:name>
      </domain:update>
    </update>
    <extension>
      <secDNS:update xmlns:secDNS="urn:ietf:params:xml:ns:secDNS-1.1">
        <secDNS:rem>
          <secDNS:keyData>
            <secDNS:flags>257</secDNS:flags>
            <secDNS:protocol>3</secDNS:protocol>
            <secDNS:alg>1</secDNS:alg>
            <secDNS:pubKey>AQPJ////4QQQ</secDNS:pubKey>
          </secDNS:keyData>
        </secDNS:rem>
        <secDNS:add>
          <secDNS:keyData>
            <secDNS:flags>257</secDNS:flags>
            <secDNS:protocol>3</secDNS:protocol>
            <secDNS:alg>1</secDNS:alg>
            <secDNS:pubKey>AQPJ////4Q==</secDNS:pubKey>
          </secDNS:keyData>
        </secDNS:add>
        <secDNS:chg>
          <secDNS:maxSigLife>605900</secDNS:maxSigLife>
        </secDNS:chg>
      </secDNS:update>
    </extension>
    <clTRID>TEST-REQUEST-ID</clTRID>
  </command>
</epp>

EOF;
        $request = new UpdateDomainRequest($this->eppClient);
        $request->setDomain('example.com');
        $requestAddon = new SecDNSUpdate();
        $requestAddon->setKeyDataForRemoving([
            (new KeyDataRequest())
                ->setFlags(SecDNSFlagsNode::DEFAULT)
                ->setProtocol(SecDNSProtocolNode::DEFAULT)
                ->setAlgorithm(SecDNSAlgorithmNode::ALG_RSAMD5)
                ->setPublicKey('AQPJ////4QQQ'),
        ]);
        $requestAddon->setKeyDataForAdding([
            (new KeyDataRequest())
                ->setFlags(SecDNSFlagsNode::DEFAULT)
                ->setProtocol(SecDNSProtocolNode::DEFAULT)
                ->setAlgorithm(SecDNSAlgorithmNode::ALG_RSAMD5)
                ->setPublicKey('AQPJ////4Q=='),
        ]);
        $requestAddon->setSignatureLifetime('605900');
        $request->addExtAddon($requestAddon);
        $request->build();

        $this->assertSame($expected, $request->getDocument()->saveXML());
    }

    /**
     * Removing DS Data with <secDNS:dsData> Using the DS Data Interface.
     */
    public function testDelegationSignerDataRemoving(): void
    {
        $expected = <<<'EOF'
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
  <command>
    <update>
      <domain:update xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
        <domain:name>example.com</domain:name>
      </domain:update>
    </update>
    <extension>
      <secDNS:update xmlns:secDNS="urn:ietf:params:xml:ns:secDNS-1.1">
        <secDNS:rem>
          <secDNS:dsData>
            <secDNS:keyTag>12346</secDNS:keyTag>
            <secDNS:alg>3</secDNS:alg>
            <secDNS:digestType>1</secDNS:digestType>
            <secDNS:digest>38EC35D5B3A34B44C39B</secDNS:digest>
          </secDNS:dsData>
        </secDNS:rem>
      </secDNS:update>
    </extension>
    <clTRID>TEST-REQUEST-ID</clTRID>
  </command>
</epp>

EOF;
        $request = new UpdateDomainRequest($this->eppClient);
        $request->setDomain('example.com');
        $requestAddon = new SecDNSUpdate();
        $requestAddon->setDelegationSignerDataForRemoving([
            (new DelegationSignerDataRequest())
                ->setKeyTag('12346')
                ->setAlgorithm(SecDNSAlgorithmNode::ALG_DSA)
                ->setDigestType(SecDNSDigestTypeNode::TYPE_SHA_1)
                ->setDigest('38EC35D5B3A34B44C39B'),
        ]);
        $request->addExtAddon($requestAddon);
        $request->build();

        $this->assertSame($expected, $request->getDocument()->saveXML());
    }

    /**
     * Removing all DS and Key Data Using <secDNS:rem> with <secDNS:all>.
     */
    public function testDelegationSignerDataRemovingAll(): void
    {
        $expected = <<<'EOF'
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
  <command>
    <update>
      <domain:update xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
        <domain:name>example.com</domain:name>
      </domain:update>
    </update>
    <extension>
      <secDNS:update urgent="true" xmlns:secDNS="urn:ietf:params:xml:ns:secDNS-1.1">
        <secDNS:rem>
          <secDNS:all>true</secDNS:all>
        </secDNS:rem>
      </secDNS:update>
    </extension>
    <clTRID>TEST-REQUEST-ID</clTRID>
  </command>
</epp>

EOF;
        $request = new UpdateDomainRequest($this->eppClient);
        $request->setDomain('example.com');
        $requestAddon = new SecDNSUpdate();
        $requestAddon->setUrgent(true);
        $requestAddon->setRemoveAll(true);
        $request->addExtAddon($requestAddon);
        $request->build();

        $this->assertSame($expected, $request->getDocument()->saveXML());
    }

    /**
     * Replacing all DS Data Using the DS Data Interface.
     */
    public function testDelegationSignerDataAddingAndRemovingAll(): void
    {
        $expected = <<<'EOF'
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
  <command>
    <update>
      <domain:update xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
        <domain:name>example.com</domain:name>
      </domain:update>
    </update>
    <extension>
      <secDNS:update urgent="true" xmlns:secDNS="urn:ietf:params:xml:ns:secDNS-1.1">
        <secDNS:rem>
          <secDNS:all>true</secDNS:all>
        </secDNS:rem>
        <secDNS:add>
          <secDNS:dsData>
            <secDNS:keyTag>12346</secDNS:keyTag>
            <secDNS:alg>3</secDNS:alg>
            <secDNS:digestType>1</secDNS:digestType>
            <secDNS:digest>38EC35D5B3A34B44C39B</secDNS:digest>
          </secDNS:dsData>
        </secDNS:add>
      </secDNS:update>
    </extension>
    <clTRID>TEST-REQUEST-ID</clTRID>
  </command>
</epp>

EOF;
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
        $request->build();

        $this->assertSame($expected, $request->getDocument()->saveXML());
    }
}
