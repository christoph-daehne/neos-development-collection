<?php

declare(strict_types=1);

namespace Neos\Neos\FrontendRouting\SiteDetection;

use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Core\Factory\ContentRepositoryId;
use Neos\Flow\Http\ServerRequestAttributes;
use Neos\Flow\Mvc\Routing\Dto\RouteParameters;
use Neos\Neos\Domain\Model\SiteNodeName;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Accessor for retrieving the currently resolved Site and Content Repository for the current Frontend Request.
 * The resolving happens inside {@see SiteDetectionMiddleware}, and for accessing the results, you should use
 * {@see SiteDetectionResult::fromRequest()}.
 *
 * @Flow\Proxy(false)
 * @api
 */
final class SiteDetectionResult
{
    private const ROUTINGPARAMETER_SITENODENAME = 'siteNodeName';
    private const ROUTINGPARAMETER_CONTENTREPOSITORYID = 'contentRepositoryId';

    private function __construct(
        public readonly SiteNodeName $siteNodeName,
        public readonly ContentRepositoryId $contentRepositoryId,
    ) {
    }

    public static function create(
        SiteNodeName $siteNodeName,
        ContentRepositoryId $contentRepositoryId
    ): self {
        return new self($siteNodeName, $contentRepositoryId);
    }

    /**
     * Helper to retrieve the previously resolved Site and ContentRepository instance.
     *
     * @param ServerRequestInterface $request
     * @return static
     * @api
     */
    public static function fromRequest(ServerRequestInterface $request): self
    {
        $routeParameters = $request->getAttribute(ServerRequestAttributes::ROUTING_PARAMETERS)
            ?? RouteParameters::createEmpty();

        return self::fromRouteParameters($routeParameters);
    }

    public static function fromRouteParameters(RouteParameters $routeParameters): self
    {
        $siteNodeName = $routeParameters->getValue(self::ROUTINGPARAMETER_SITENODENAME);
        $contentRepositoryId = $routeParameters->getValue(self::ROUTINGPARAMETER_CONTENTREPOSITORYID);

        if ($siteNodeName === null || $contentRepositoryId === null) {
            throw new \RuntimeException(
                'Current site and content repository could not be extracted from the Request.'
                    . ' The SiteDetectionMiddleware was not able to determine the site!'
            );
        }
        assert(is_string($siteNodeName));
        assert(is_string($contentRepositoryId));
        return new self(
            SiteNodeName::fromString($siteNodeName),
            ContentRepositoryId::fromString($contentRepositoryId)
        );
    }

    public function storeInRequest(ServerRequestInterface $request): ServerRequestInterface
    {
        $parameters = $request->getAttribute(ServerRequestAttributes::ROUTING_PARAMETERS)
            ?? RouteParameters::createEmpty();
        $parameters = self::storeInRouteParameters($parameters);
        return $request->withAttribute(ServerRequestAttributes::ROUTING_PARAMETERS, $parameters);
    }

    public function storeInRouteParameters(RouteParameters $routeParameters): RouteParameters
    {
        return $routeParameters
            ->withParameter(
                self::ROUTINGPARAMETER_SITENODENAME,
                $this->siteNodeName->value
            )
            ->withParameter(
                self::ROUTINGPARAMETER_CONTENTREPOSITORYID,
                $this->contentRepositoryId->value
            );
    }
}
