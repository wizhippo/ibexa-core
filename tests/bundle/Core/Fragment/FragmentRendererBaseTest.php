<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Tests\Bundle\Core\Fragment;

use Ibexa\Core\MVC\Symfony\Component\Serializer\SerializerTrait;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

abstract class FragmentRendererBaseTest extends TestCase
{
    use SerializerTrait;

    public function testRendererControllerReferenceWithCompoundMatcher(): ControllerReference
    {
        $reference = new ControllerReference('FooBundle:bar:baz');
        $compoundMatcher = new SiteAccess\Matcher\Compound\LogicalAnd([]);
        $subMatchers = [
            'Map\URI' => new SiteAccess\Matcher\Map\URI([]),
            'Map\Host' => new SiteAccess\Matcher\Map\Host([]),
        ];
        $compoundMatcher->setSubMatchers($subMatchers);
        $siteAccess = new SiteAccess(
            'test',
            'test',
            $compoundMatcher
        );

        $request = $this->getRequest($siteAccess);
        $options = ['foo' => 'bar'];
        $expectedReturn = '/_fragment?foo=bar';
        $this->innerRenderer
            ->expects(self::once())
            ->method('render')
            ->with($reference, $request, $options)
            ->will(self::returnValue($expectedReturn));

        $renderer = $this->getRenderer();
        self::assertSame($expectedReturn, $renderer->render($reference, $request, $options));
        self::assertArrayHasKey('serialized_siteaccess', $reference->attributes);
        $serializedSiteAccess = json_encode($siteAccess);
        self::assertSame($serializedSiteAccess, $reference->attributes['serialized_siteaccess']);
        self::assertArrayHasKey('serialized_siteaccess_matcher', $reference->attributes);
        self::assertSame(
            $this->getSerializer()->serialize(
                $siteAccess->matcher,
                'json',
                [AbstractNormalizer::IGNORED_ATTRIBUTES => ['request', 'container', 'matcherBuilder']]
            ),
            $reference->attributes['serialized_siteaccess_matcher']
        );
        self::assertArrayHasKey('serialized_siteaccess_sub_matchers', $reference->attributes);
        foreach ($siteAccess->matcher->getSubMatchers() as $subMatcher) {
            self::assertSame(
                $this->getSerializer()->serialize(
                    $subMatcher,
                    'json',
                    [AbstractNormalizer::IGNORED_ATTRIBUTES => ['request', 'container', 'matcherBuilder']]
                ),
                $reference->attributes['serialized_siteaccess_sub_matchers'][get_class($subMatcher)]
            );
        }

        return $reference;
    }

    abstract public function getRequest(SiteAccess $siteAccess): Request;

    abstract public function getRenderer(): FragmentRendererInterface;
}
