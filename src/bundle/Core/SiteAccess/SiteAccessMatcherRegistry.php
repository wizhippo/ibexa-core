<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Core\SiteAccess;

use Ibexa\Core\Base\Exceptions\NotFoundException;

final class SiteAccessMatcherRegistry implements SiteAccessMatcherRegistryInterface
{
    /** @var \Ibexa\Bundle\Core\SiteAccess\Matcher[] */
    private $matchers;

    /**
     * @param \Ibexa\Bundle\Core\SiteAccess\Matcher[] $matchers
     */
    public function __construct(array $matchers = [])
    {
        $this->matchers = $matchers;
    }

    public function setMatcher(string $identifier, Matcher $matcher): void
    {
        $this->matchers[$identifier] = $matcher;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getMatcher(string $identifier): Matcher
    {
        if (!$this->hasMatcher($identifier)) {
            throw new NotFoundException('SiteAccess Matcher', $identifier);
        }

        return $this->matchers[$identifier];
    }

    public function hasMatcher(string $identifier): bool
    {
        return isset($this->matchers[$identifier]);
    }
}
