<?php

namespace Struzik\EPPClient\Extension\SecDNS\Tests\Response\Addon;

use Struzik\EPPClient\Extension\SecDNS\Response\Addon\SecDNSInfo;
use Struzik\EPPClient\Extension\SecDNS\Response\Helper\KeyDataResponse;
use Struzik\EPPClient\Extension\SecDNS\Tests\SecDNSTestCase;
use Struzik\EPPClient\Response\Domain\InfoDomainResponse;

class SecDNSInfoTest extends SecDNSTestCase
{
    public function testSecureDelegationWithoutKeyData(): void
    {
        $xml = <<<'EOF'
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
  <response>
    <result code="1000">
      <msg>Command completed successfully</msg>
    </result>
    <resData>
      <domain:infData xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
        <domain:name>example.com</domain:name>
        <domain:roid>EXAMPLE1-REP</domain:roid>
        <domain:status s="ok"/>
        <domain:registrant>jd1234</domain:registrant>
        <domain:contact type="admin">sh8013</domain:contact>
        <domain:contact type="tech">sh8013</domain:contact>
        <domain:ns>
          <domain:hostObj>ns1.example.com</domain:hostObj>
          <domain:hostObj>ns2.example.com</domain:hostObj>
        </domain:ns>
        <domain:host>ns1.example.com</domain:host>
        <domain:host>ns2.example.com</domain:host>
        <domain:clID>ClientX</domain:clID>
        <domain:crID>ClientY</domain:crID>
        <domain:crDate>1999-04-03T22:00:00.0Z</domain:crDate>
        <domain:upID>ClientX</domain:upID>
        <domain:upDate>1999-12-03T09:00:00.0Z</domain:upDate>
        <domain:exDate>2005-04-03T22:00:00.0Z</domain:exDate>
        <domain:trDate>2000-04-08T09:00:00.0Z</domain:trDate>
        <domain:authInfo>
          <domain:pw>2fooBAR</domain:pw>
        </domain:authInfo>
      </domain:infData>
    </resData>
    <extension>
      <secDNS:infData xmlns:secDNS="urn:ietf:params:xml:ns:secDNS-1.1">
        <secDNS:dsData>
          <secDNS:keyTag>12345</secDNS:keyTag>
          <secDNS:alg>3</secDNS:alg>
          <secDNS:digestType>1</secDNS:digestType>
          <secDNS:digest>49FD46E6C4B45C55D4AC</secDNS:digest>
        </secDNS:dsData>
      </secDNS:infData>
    </extension>
    <trID>
      <clTRID>ABC-12345</clTRID>
      <svTRID>54322-XYZ</svTRID>
    </trID>
  </response>
</epp>
EOF;
        $response = new InfoDomainResponse($xml);
        $this->secDNSExtension->handleResponse($response);

        $this->assertTrue($response->isSuccess());
        $this->assertInstanceOf(SecDNSInfo::class, $response->findExtAddon(SecDNSInfo::class));

        /** @var SecDNSInfo $secDNSInfo */
        $secDNSInfo = $response->findExtAddon(SecDNSInfo::class);
        $this->assertNull($secDNSInfo->getMaxSignatureLifetime());
        $this->assertCount(1, $secDNSInfo->getDelegationSignersData());
        $this->assertCount(0, $secDNSInfo->getKeyData());

        $delegationSignersData = $secDNSInfo->getDelegationSignersData()[0];
        $this->assertSame('12345', $delegationSignersData->getKeyTag());
        $this->assertSame('3', $delegationSignersData->getAlgorithm());
        $this->assertSame('1', $delegationSignersData->getDigestType());
        $this->assertSame('49FD46E6C4B45C55D4AC', $delegationSignersData->getDigest());
        $this->assertNull($delegationSignersData->getKeyData());
    }

    public function testSecureDelegationWithKeyData(): void
    {
        $xml = <<<'EOF'
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
  <response>
    <result code="1000">
      <msg>Command completed successfully</msg>
    </result>
    <resData>
      <domain:infData xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
        <domain:name>example.com</domain:name>
        <domain:roid>EXAMPLE1-REP</domain:roid>
        <domain:status s="ok"/>
        <domain:registrant>jd1234</domain:registrant>
        <domain:contact type="admin">sh8013</domain:contact>
        <domain:contact type="tech">sh8013</domain:contact>
        <domain:ns>
          <domain:hostObj>ns1.example.com</domain:hostObj>
          <domain:hostObj>ns2.example.com</domain:hostObj>
        </domain:ns>
        <domain:host>ns1.example.com</domain:host>
        <domain:host>ns2.example.com</domain:host>
        <domain:clID>ClientX</domain:clID>
        <domain:crID>ClientY</domain:crID>
        <domain:crDate>1999-04-03T22:00:00.0Z</domain:crDate>
        <domain:upID>ClientX</domain:upID>
        <domain:upDate>1999-12-03T09:00:00.0Z</domain:upDate>
        <domain:exDate>2005-04-03T22:00:00.0Z</domain:exDate>
        <domain:trDate>2000-04-08T09:00:00.0Z</domain:trDate>
        <domain:authInfo>
          <domain:pw>2fooBAR</domain:pw>
        </domain:authInfo>
      </domain:infData>
    </resData>
    <extension>
      <secDNS:infData xmlns:secDNS="urn:ietf:params:xml:ns:secDNS-1.1">
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
      </secDNS:infData>
    </extension>
    <trID>
      <clTRID>ABC-12345</clTRID>
      <svTRID>54322-XYZ</svTRID>
    </trID>
  </response>
</epp>
EOF;
        $response = new InfoDomainResponse($xml);
        $this->secDNSExtension->handleResponse($response);

        $this->assertTrue($response->isSuccess());
        $this->assertInstanceOf(SecDNSInfo::class, $response->findExtAddon(SecDNSInfo::class));

        /** @var SecDNSInfo $secDNSInfo */
        $secDNSInfo = $response->findExtAddon(SecDNSInfo::class);
        $this->assertSame('604800', $secDNSInfo->getMaxSignatureLifetime());
        $this->assertCount(1, $secDNSInfo->getDelegationSignersData());
        $this->assertCount(0, $secDNSInfo->getKeyData());

        $delegationSignersData = $secDNSInfo->getDelegationSignersData()[0];
        $this->assertSame('12345', $delegationSignersData->getKeyTag());
        $this->assertSame('3', $delegationSignersData->getAlgorithm());
        $this->assertSame('1', $delegationSignersData->getDigestType());
        $this->assertSame('49FD46E6C4B45C55D4AC', $delegationSignersData->getDigest());
        $this->assertInstanceOf(KeyDataResponse::class, $delegationSignersData->getKeyData());

        $keyData = $delegationSignersData->getKeyData();
        $this->assertSame('257', $keyData->getFlags());
        $this->assertSame('3', $keyData->getProtocol());
        $this->assertSame('1', $keyData->getAlgorithm());
        $this->assertSame('AQPJ////4Q==', $keyData->getPublicKey());
    }

    public function testKeyData(): void
    {
        $xml = <<<'EOF'
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
  <response>
    <result code="1000">
      <msg>Command completed successfully</msg>
    </result>
    <resData>
      <domain:infData xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
        <domain:name>example.com</domain:name>
        <domain:roid>EXAMPLE1-REP</domain:roid>
        <domain:status s="ok"/>
        <domain:registrant>jd1234</domain:registrant>
        <domain:contact type="admin">sh8013</domain:contact>
        <domain:contact type="tech">sh8013</domain:contact>
        <domain:ns>
          <domain:hostObj>ns1.example.com</domain:hostObj>
          <domain:hostObj>ns2.example.com</domain:hostObj>
        </domain:ns>
        <domain:host>ns1.example.com</domain:host>
        <domain:host>ns2.example.com</domain:host>
        <domain:clID>ClientX</domain:clID>
        <domain:crID>ClientY</domain:crID>
        <domain:crDate>1999-04-03T22:00:00.0Z</domain:crDate>
        <domain:upID>ClientX</domain:upID>
        <domain:upDate>1999-12-03T09:00:00.0Z</domain:upDate>
        <domain:exDate>2005-04-03T22:00:00.0Z</domain:exDate>
        <domain:trDate>2000-04-08T09:00:00.0Z</domain:trDate>
        <domain:authInfo>
          <domain:pw>2fooBAR</domain:pw>
        </domain:authInfo>
      </domain:infData>
    </resData>
    <extension>
      <secDNS:infData xmlns:secDNS="urn:ietf:params:xml:ns:secDNS-1.1">
        <secDNS:keyData>
          <secDNS:flags>257</secDNS:flags>
          <secDNS:protocol>3</secDNS:protocol>
          <secDNS:alg>1</secDNS:alg>
          <secDNS:pubKey>AQPJ////4Q==</secDNS:pubKey>
        </secDNS:keyData>
      </secDNS:infData>
    </extension>
    <trID>
      <clTRID>ABC-12345</clTRID>
      <svTRID>54322-XYZ</svTRID>
    </trID>
  </response>
</epp>
EOF;
        $response = new InfoDomainResponse($xml);
        $this->secDNSExtension->handleResponse($response);

        $this->assertTrue($response->isSuccess());
        $this->assertInstanceOf(SecDNSInfo::class, $response->findExtAddon(SecDNSInfo::class));

        /** @var SecDNSInfo $secDNSInfo */
        $secDNSInfo = $response->findExtAddon(SecDNSInfo::class);
        $this->assertNull($secDNSInfo->getMaxSignatureLifetime());
        $this->assertCount(0, $secDNSInfo->getDelegationSignersData());
        $this->assertCount(1, $secDNSInfo->getKeyData());

        $keyData = $secDNSInfo->getKeyData()[0];
        $this->assertSame('257', $keyData->getFlags());
        $this->assertSame('3', $keyData->getProtocol());
        $this->assertSame('1', $keyData->getAlgorithm());
        $this->assertSame('AQPJ////4Q==', $keyData->getPublicKey());
    }
}
