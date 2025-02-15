<?php

/*
 * This file is part of the Neos.ContentRepository package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

declare(strict_types=1);

namespace Neos\ContentRepository\Core\Feature\NodeReferencing\Command;

use Neos\ContentRepository\Core\CommandHandler\CommandInterface;
use Neos\ContentRepository\Core\DimensionSpace\OriginDimensionSpacePoint;
use Neos\ContentRepository\Core\Feature\Common\MatchableWithNodeIdToPublishOrDiscardInterface;
use Neos\ContentRepository\Core\Feature\Common\RebasableToOtherContentStreamsInterface;
use Neos\ContentRepository\Core\Feature\NodeReferencing\Dto\SerializedNodeReferences;
use Neos\ContentRepository\Core\Feature\WorkspacePublication\Dto\NodeIdToPublishOrDiscard;
use Neos\ContentRepository\Core\SharedModel\Node\NodeAggregateId;
use Neos\ContentRepository\Core\SharedModel\Node\ReferenceName;
use Neos\ContentRepository\Core\SharedModel\Workspace\ContentStreamId;

/**
 * Set property values for a given node.
 *
 * The property values contain the serialized types already, and include type information.
 *
 * @internal implementation detail, use {@see SetNodeReferences} instead.
 */
final class SetSerializedNodeReferences implements
    CommandInterface,
    \JsonSerializable,
    RebasableToOtherContentStreamsInterface,
    MatchableWithNodeIdToPublishOrDiscardInterface
{
    /**
     * @param ContentStreamId $contentStreamId The content stream in which the create operation is to be performed
     * @param NodeAggregateId $sourceNodeAggregateId The identifier of the node aggregate to set references
     * @param OriginDimensionSpacePoint $sourceOriginDimensionSpacePoint The dimension space for which the references should be set
     * @param ReferenceName $referenceName Name of the reference to set
     * @param SerializedNodeReferences $references Serialized reference(s) to set
     */
    private function __construct(
        public readonly ContentStreamId $contentStreamId,
        public readonly NodeAggregateId $sourceNodeAggregateId,
        public readonly OriginDimensionSpacePoint $sourceOriginDimensionSpacePoint,
        public readonly ReferenceName $referenceName,
        public readonly SerializedNodeReferences $references,
    ) {
    }

    /**
     * @param ContentStreamId $contentStreamId The content stream in which the create operation is to be performed
     * @param NodeAggregateId $sourceNodeAggregateId The identifier of the node aggregate to set references
     * @param OriginDimensionSpacePoint $sourceOriginDimensionSpacePoint The dimension space for which the references should be set
     * @param ReferenceName $referenceName Name of the reference to set
     * @param SerializedNodeReferences $references Serialized reference(s) to set
     */
    public static function create(ContentStreamId $contentStreamId, NodeAggregateId $sourceNodeAggregateId, OriginDimensionSpacePoint $sourceOriginDimensionSpacePoint, ReferenceName $referenceName, SerializedNodeReferences $references): self
    {
        return new self($contentStreamId, $sourceNodeAggregateId, $sourceOriginDimensionSpacePoint, $referenceName, $references);
    }

    /**
     * @param array<string,mixed> $array
     */
    public static function fromArray(array $array): self
    {
        return new self(
            ContentStreamId::fromString($array['contentStreamId']),
            NodeAggregateId::fromString($array['sourceNodeAggregateId']),
            OriginDimensionSpacePoint::fromArray($array['sourceOriginDimensionSpacePoint']),
            ReferenceName::fromString($array['referenceName']),
            SerializedNodeReferences::fromArray($array['references']),
        );
    }

    /**
     * @internal
     * @return array<string,mixed>
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    public function createCopyForContentStream(ContentStreamId $target): self
    {
        return new self(
            $target,
            $this->sourceNodeAggregateId,
            $this->sourceOriginDimensionSpacePoint,
            $this->referenceName,
            $this->references,
        );
    }

    public function matchesNodeId(NodeIdToPublishOrDiscard $nodeIdToPublish): bool
    {
        return (
            $this->contentStreamId === $nodeIdToPublish->contentStreamId
                && $this->sourceOriginDimensionSpacePoint->equals($nodeIdToPublish->dimensionSpacePoint)
                && $this->sourceNodeAggregateId->equals($nodeIdToPublish->nodeAggregateId)
        );
    }
}
