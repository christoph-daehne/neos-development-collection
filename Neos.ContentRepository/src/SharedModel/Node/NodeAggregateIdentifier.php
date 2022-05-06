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

namespace Neos\ContentRepository\SharedModel\Node;

use Neos\Flow\Utility\Algorithms;

/**
 * The NodeAggregateIdentifier supersedes the Node Identifier from Neos <= 4.x.
 */
final class NodeAggregateIdentifier implements \JsonSerializable, \Stringable
{
    /**
     * A preg pattern to match against node aggregate identifiers
     */
    const PATTERN = '/^([a-z0-9\-]{1,255})$/';

    private function __construct(
        private string $value
    ) {
        if (!preg_match(self::PATTERN, $value)) {
            throw new \InvalidArgumentException(
                'Invalid node aggregate identifier "' . $value
                . '" (a node aggregate identifier must only contain lowercase characters, numbers and the "-" sign).',
                1505840197862
            );
        }
    }

    public static function create(): self
    {
        return new self(Algorithms::generateUUID());
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
