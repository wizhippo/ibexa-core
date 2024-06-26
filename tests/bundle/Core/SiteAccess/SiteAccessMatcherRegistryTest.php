<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Core\SiteAccess;

use Ibexa\Bundle\Core\SiteAccess\Matcher;
use Ibexa\Bundle\Core\SiteAccess\SiteAccessMatcherRegistry;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use PHPUnit\Framework\TestCase;

class SiteAccessMatcherRegistryTest extends TestCase
{
    private const MATCHER_NAME = 'test_matcher';

    public function testGetMatcher(): void
    {
        $matcher = $this->getMatcherMock();
        $registry = new SiteAccessMatcherRegistry([self::MATCHER_NAME => $matcher]);

        self::assertSame($matcher, $registry->getMatcher(self::MATCHER_NAME));
    }

    public function testSetMatcher(): void
    {
        $matcher = $this->getMatcherMock();
        $registry = new SiteAccessMatcherRegistry();

        $registry->setMatcher(self::MATCHER_NAME, $matcher);

        self::assertSame($matcher, $registry->getMatcher(self::MATCHER_NAME));
    }

    public function testSetMatcherOverride(): void
    {
        $matcher = $this->getMatcherMock();
        $newMatcher = $this->getMatcherMock();
        $registry = new SiteAccessMatcherRegistry([self::MATCHER_NAME, $matcher]);

        $registry->setMatcher(self::MATCHER_NAME, $newMatcher);

        self::assertSame($newMatcher, $registry->getMatcher(self::MATCHER_NAME));
    }

    public function testGetMatcherNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $registry = new SiteAccessMatcherRegistry();

        $registry->getMatcher(self::MATCHER_NAME);
    }

    protected function getMatcherMock(): Matcher
    {
        return $this->createMock(Matcher::class);
    }
}
