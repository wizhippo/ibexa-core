<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\MVC\Symfony\Component\Serializer;

use Ibexa\Core\MVC\Symfony\Component\Serializer\URITextNormalizer;
use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher;
use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher\URIText;
use Ibexa\Tests\Core\MVC\Symfony\Component\Serializer\Stubs\SerializerStub;
use Ibexa\Tests\Core\Search\TestCase;

final class URITextNormalizerTest extends TestCase
{
    public function testNormalize(): void
    {
        $normalizer = new URITextNormalizer();
        $normalizer->setSerializer(new SerializerStub());

        $matcher = new URIText([
            'prefix' => 'foo',
            'suffix' => 'bar',
        ]);

        self::assertEquals(
            [
                'siteAccessesConfiguration' => [
                    'prefix' => 'foo',
                    'suffix' => 'bar',
                ],
            ],
            $normalizer->normalize($matcher)
        );
    }

    public function testSupportsNormalization(): void
    {
        $normalizer = new URITextNormalizer();

        self::assertTrue($normalizer->supportsNormalization($this->createMock(URIText::class)));
        self::assertFalse($normalizer->supportsNormalization($this->createMock(Matcher::class)));
    }
}
