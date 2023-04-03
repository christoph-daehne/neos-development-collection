<?php
declare(strict_types=1);
namespace Neos\ContentRepositoryRegistry\Factory\Clock;

use Neos\ContentRepository\Core\Factory\ContentRepositoryId;
use Psr\Clock\ClockInterface;

/**
 * @api
 */
interface ClockFactoryInterface
{
    public function build(ContentRepositoryId $contentRepositoryIdentifier, array $contentRepositorySettings, array $contentRepositoryPreset): ClockInterface;
}
