<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Security\PolicyProvider;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigBuilderInterface;
use Ibexa\Tests\Bundle\Core\DependencyInjection\Stub\StubYamlPolicyProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Resource\FileResource;

class YamlPolicyProviderTest extends TestCase
{
    public function testSingleYaml()
    {
        $files = [__DIR__ . '/../../Fixtures/policies1.yml'];
        $provider = new StubYamlPolicyProvider($files);
        $expectedConfig = [
            'custom_module' => [
                'custom_function_1' => null,
                'custom_function_2' => ['CustomLimitation'],
            ],
            'helloworld' => [
                'foo' => ['bar'],
                'baz' => null,
            ],
        ];

        $configBuilder = $this->createMock(ConfigBuilderInterface::class);
        foreach ($files as $file) {
            $configBuilder
                ->expects(self::once())
                ->method('addResource')
                ->with(self::equalTo(new FileResource($file)));
        }
        $configBuilder
            ->expects(self::once())
            ->method('addConfig')
            ->with($expectedConfig);

        $provider->addPolicies($configBuilder);
    }

    public function testMultipleYaml()
    {
        $file1 = __DIR__ . '/../../Fixtures/policies1.yml';
        $file2 = __DIR__ . '/../../Fixtures/policies2.yml';
        $files = [$file1, $file2];
        $provider = new StubYamlPolicyProvider($files);
        $expectedConfig = [
            'custom_module' => [
                'custom_function_1' => null,
                'custom_function_2' => ['CustomLimitation'],
            ],
            'helloworld' => [
                'foo' => ['bar'],
                'baz' => null,
                'some' => ['thingy', 'thing', 'but', 'wait'],
            ],
            'custom_module2' => [
                'custom_function_3' => null,
                'custom_function_4' => ['CustomLimitation2', 'CustomLimitation3'],
            ],
        ];

        $configBuilder = $this->createMock(ConfigBuilderInterface::class);
        $configBuilder
            ->expects(self::exactly(count($files)))
            ->method('addResource')
            ->willReturnMap([
                [self::equalTo(new FileResource($file1)), null],
                [self::equalTo(new FileResource($file2)), null],
            ]);
        $configBuilder
            ->expects(self::once())
            ->method('addConfig')
            ->with($expectedConfig);

        $provider->addPolicies($configBuilder);
    }
}
