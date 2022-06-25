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

namespace Neos\ContentRepository\Projection\Content;

use Neos\ContentRepository\SharedModel\Node\NodeAggregateIdentifier;
use Neos\ContentRepository\SharedModel\Node\NodeName;
use Neos\ContentRepository\SharedModel\Node\OriginDimensionSpacePoint;
use Neos\ContentRepository\SharedModel\Node\NodeAggregateClassification;
use Neos\ContentRepository\DimensionSpace\DimensionSpace\DimensionSpacePoint;
use Neos\ContentRepository\NodeAccess\NodeAccessorManager;
use Neos\ContentRepository\SharedModel\NodeType\NodeType;
use Neos\ContentRepository\SharedModel\NodeType\NodeTypeName;
use Neos\ContentRepository\SharedModel\VisibilityConstraints;
use Neos\ContentRepository\SharedModel\Workspace\ContentStreamIdentifier;

/**
 * Main read model of the {@see ContentSubgraphInterface}.
 *
 * TODO: Identity (fetching ContentSubgraph from NodeInterface)
 *
 * @api
 */
interface NodeInterface
{
    /**
     * Whether or not this node is the root of the graph, i.e. has no parent node
     *
     * @return bool
     */
    public function isRoot(): bool;

    /**
     * Whether or not this node is tethered to its parent, fka auto created child node
     *
     * @return bool
     */
    public function isTethered(): bool;

    /**
     * @return ContentStreamIdentifier
     */
    public function getContentStreamIdentifier(): ContentStreamIdentifier;

    /**
     * @return NodeAggregateIdentifier
     */
    public function getNodeAggregateIdentifier(): NodeAggregateIdentifier;

    /**
     * @return NodeTypeName
     */
    public function getNodeTypeName(): NodeTypeName;

    /**
     * @return NodeType
     */
    public function getNodeType(): NodeType;

    /**
     * @return NodeName|null
     */
    public function getNodeName(): ?NodeName;

    /**
     * returns the DimensionSpacePoint the node is at home in. Usually needed to address a Node in a NodeAggregate
     * in order to update it.
     *
     * @return OriginDimensionSpacePoint
     */
    public function getOriginDimensionSpacePoint(): OriginDimensionSpacePoint;

    /**
     * Returns all properties of this node. References are NOT part of this API;
     * there you need to check getReference() and getReferences().
     *
     * To read the serialized properties, call getProperties()->serialized().
     *
     * @return PropertyCollectionInterface Property values, indexed by their name
     * @api
     */
    public function getProperties(): PropertyCollectionInterface;

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
    public function getProperty($propertyName);

    /**
     * If this node has a property with the given name. Does NOT check the NodeType; but checks
     * for a non-NULL property value.
     *
     * @param string $propertyName
     * @return boolean
     * @api
     */
    public function hasProperty($propertyName): bool;

    /**
     * Returns the node label as generated by the configured node label generator
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * DimensionSpacePoint this node has been accessed in.
     * This is part of the node's "Read Model" identity, whis is defined by:
     * - {@see getContentStreamIdentifier}
     * - {@see getNodeAggregateIdentifier}
     * - {@see getDimensionSpacePoint} (this method)
     * - {@see getVisibilityConstraints}
     *
     * With the above information, you can fetch a Node Accessor using {@see NodeAccessorManager::accessorFor()}, or
     * (for lower-level access) a Subgraph using {@see ContentGraphInterface::getSubgraphByIdentifier()}.
     *
     * This is the DimensionSpacePoint this node has been accessed in
     * - NOT the DimensionSpacePoint where the node is "at home".
     * The DimensionSpacePoint where the node is (at home) is called the ORIGIN DimensionSpacePoint,
     * and this can be accessed using {@see getOriginDimensionSpacePoint}. If in doubt, you'll usually need this method
     * insead of the Origin DimensionSpacePoint.
     *
     * We are still a bit unsure whether this method should be part of the Node itself, or rather part of some kind of
     * "Context Accessor" or "Perspective" object.
     *
     * @return DimensionSpacePoint
     */
    public function getDimensionSpacePoint(): DimensionSpacePoint;

    /**
     * VisibilityConstraints of the Subgraph / NodeAccessor this node has been read from.
     * This is part of the node's "Read Model" identity, whis is defined by:
     * - {@see getContentStreamIdentifier}
     * - {@see getNodeAggregateIdentifier}
     * - {@see getDimensionSpacePoint}
     * - {@see getVisibilityConstraints} (this method)
     *
     * With the above information, you can fetch a Node Accessor using {@see NodeAccessorManager::accessorFor()}, or
     * (for lower-level access) a Subgraph using {@see ContentGraphInterface::getSubgraphByIdentifier()}.
     *
     * We are still a bit unsure whether this method should be part of the Node itself, or rather part of some kind of
     * "Context Accessor" or "Perspective" object.
     *
     * @return VisibilityConstraints
     */
    public function getVisibilityConstraints(): VisibilityConstraints;

    public function getClassification(): NodeAggregateClassification;

    public function equals(NodeInterface $other): bool;
}
