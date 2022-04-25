<?php

namespace Struzik\EPPClient\Extension\SecDNS\Tests;

use Psr\Log\NullLogger;
use Struzik\EPPClient\Extension\SecDNS\SecDNSExtension;
use Struzik\EPPClient\Tests\EPPTestCase;

class SecDNSTestCase extends EPPTestCase
{
    public SecDNSExtension $secDNSExtension;

    protected function setUp(): void
    {
        parent::setUp();
        $this->secDNSExtension = new SecDNSExtension('urn:ietf:params:xml:ns:secDNS-1.1', new NullLogger());
        $this->eppClient->pushExtension($this->secDNSExtension);
    }
}
