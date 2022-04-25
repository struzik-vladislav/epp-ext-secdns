<?php

namespace Struzik\EPPClient\Extension\SecDNS;

use Psr\Log\LoggerInterface;
use Struzik\EPPClient\EPPClient;
use Struzik\EPPClient\Extension\ExtensionInterface;
use Struzik\EPPClient\Extension\SecDNS\Response\Addon\SecDNSInfo;
use Struzik\EPPClient\Response\ResponseInterface;

class SecDNSExtension implements ExtensionInterface
{
    public const NS_NAME_SECDNS = 'secDNS';

    private string $uri;
    private LoggerInterface $logger;

    public function __construct(string $uri, LoggerInterface $logger)
    {
        $this->uri = $uri;
        $this->logger = $logger;
    }

    public function setupNamespaces(EPPClient $client): void
    {
        $client->getExtNamespaceCollection()
            ->offsetSet(self::NS_NAME_SECDNS, $this->uri);
    }

    public function handleResponse(ResponseInterface $response): void
    {
        if (!in_array($this->uri, $response->getUsedNamespaces(), true)) {
            $this->logger->debug(sprintf(
                'Namespace with URI "%s" does not exists in used namespaces of the response object.',
                $this->uri
            ));

            return;
        }

        $node = $response->getFirst('//secDNS:infData');
        if ($node !== null) {
            $this->logger->debug(sprintf('Adding add-on "%s" to the response object.', SecDNSInfo::class));
            $response->addExtAddon(new SecDNSInfo($response));
        }
    }
}
