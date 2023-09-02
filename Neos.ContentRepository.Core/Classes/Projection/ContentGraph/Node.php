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

namespace Neos\ContentRepository\Core\Projection\ContentGraph;

use Neos\ContentRepository\Core\SharedModel\Node\NodeAggregateId;
use Neos\ContentRepository\Core\SharedModel\Node\NodeName;
use Neos\ContentRepository\Core\DimensionSpace\OriginDimensionSpacePoint;
use Neos\ContentRepository\Core\SharedModel\Node\NodeAggregateClassification;
use Neos\ContentRepository\Core\NodeType\NodeType;
use Neos\ContentRepository\Core\NodeType\NodeTypeName;

/**
 * Main read model of the {@see ContentSubgraphInterface}.
 *
 * Immutable, Read Only. In case you want to modify it, you need
 * to create Commands and send them to ContentRepository::handle.
 *
 * The node does not have structure information, i.e. no infos
 * about its children. To f.e. fetch children, you need to fetch
 * the subgraph via $node->subgraphIdentity and then
 * call findChildNodes() on the subgraph.
 *
 * @api Note: The constructor is not part of the public API
 */
final readonly class Node
{
    /**
     * @param ContentSubgraphIdentity $subgraphIdentity This is part of the node's "Read Model" identity which is defined by: {@see self::subgraphIdentity} and {@see self::nodeAggregateId}
     * @param NodeAggregateId $nodeAggregateId NodeAggregateId (identifier) of this node. This is part of the node's "Read Model" identity which is defined by: {@see self::subgraphIdentity} and {@see self::nodeAggregateId}
     * @param OriginDimensionSpacePoint $originDimensionSpacePoint The DimensionSpacePoint the node originates in. Usually needed to address a Node in a NodeAggregate in order to update it.
     * @param NodeAggregateClassification $classification The classification (regular, root, tethered) of this node
     * @param NodeTypeName $nodeTypeName The node's node type name; always set, even if unknown to the NodeTypeManager
     * @param NodeType|null $nodeType The node's node type, null if unknown to the NodeTypeManager - @deprecated Don't rely on this too much, as the capabilities of the NodeType here will probably change a lot; Ask the {@see NodeTypeManager} instead
     * @param PropertyCollection $properties All properties of this node. References are NOT part of this API; To access references, {@see ContentSubgraphInterface::findReferences()} can be used; To read the serialized properties, call properties->serialized().
     * @param NodeName|null $nodeName The nodeÄs name. The name is guaranteed to be for tethered nodes. For the regular classification, it can be set optionally. One can traverse the edges via {@see ContentSubgraphInterface::findChildNodeConnectedThroughEdgeName()}.
     * @param Timestamps $timestamps Creation and modification timestamps of this node
     */
    private function __construct(
        public ContentSubgraphIdentity $subgraphIdentity,
        public NodeAggregateId $nodeAggregateId,
        public OriginDimensionSpacePoint $originDimensionSpacePoint,
        public NodeAggregateClassification $classification,
        public NodeTypeName $nodeTypeName,
        public ?NodeType $nodeType,
        public PropertyCollection $properties,
        public ?NodeName $nodeName,
        public Timestamps $timestamps,
    ) {
        if ($this->classification->isTethered() && $this->nodeName === null) {
            throw new \InvalidArgumentException('The NodeName must be set if the Node is tethered.', 1695118377);
        }
    }

    /**
     * @internal The signature of this method can change in the future!
     */
    public static function create(ContentSubgraphIdentity $subgraphIdentity, NodeAggregateId $nodeAggregateId, OriginDimensionSpacePoint $originDimensionSpacePoint, NodeAggregateClassification $classification, NodeTypeName $nodeTypeName, ?NodeType $nodeType, PropertyCollection $properties, ?NodeName $nodeName, Timestamps $timestamps): self
    {
        return new self($subgraphIdentity, $nodeAggregateId, $originDimensionSpacePoint, $classification, $nodeTypeName, $nodeType, $properties, $nodeName, $timestamps);
    }

    /**
     * Returns the specified property.
     *
     * If the node has a content object attached, the property will be fetched
     * there if it is gettable.
     *
     * @param string $propertyName Name of the property
     * @return mixed value of the property
     * @api
     */
    public function getProperty(string $propertyName): mixed
    {
        return $this->properties[$propertyName];
    }

    /**
     * If this node has a property with the given name. Does NOT check the NodeType; but checks
     * for a non-NULL property value.
     *
     * @param string $propertyName
     * @return boolean
     * @api
     */
    public function hasProperty(string $propertyName): bool
    {
        return $this->properties->offsetExists($propertyName);
    }

    /**
     * Returns the node label as generated by the configured node label generator
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->nodeType?->getNodeLabelGenerator()->getLabel($this) ?: $this->nodeTypeName->value;
    }

    public function equals(Node $other): bool
    {
        return $this->subgraphIdentity->equals($other->subgraphIdentity)
            && $this->nodeAggregateId->equals($other->nodeAggregateId);
    }
}
