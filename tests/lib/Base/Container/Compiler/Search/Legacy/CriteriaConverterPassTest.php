<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Tests\Core\Base\Container\Compiler\Search\Legacy;

use Ibexa\Core\Base\Container\Compiler\Search\Legacy\CriteriaConverterPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class CriteriaConverterPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test, just like you would do inside a bundle's load()
     * method:.
     *
     *   $container->addCompilerPass(new MyCompilerPass());
     */
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CriteriaConverterPass());
    }

    public function testAddContentHandlers()
    {
        $this->setDefinition(
            'ibexa.search.legacy.gateway.criteria_converter.content',
            new Definition()
        );

        $serviceId = 'service_id';
        $def = new Definition();
        $def->addTag('ibexa.search.legacy.gateway.criterion_handler.content');
        $this->setDefinition($serviceId, $def);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'ibexa.search.legacy.gateway.criteria_converter.content',
            'addHandler',
            [new Reference($serviceId)]
        );
    }

    public function testAddLocationHandlers()
    {
        $this->setDefinition(
            'ibexa.search.legacy.gateway.criteria_converter.location',
            new Definition()
        );

        $serviceId = 'service_id';
        $def = new Definition();
        $def->addTag('ibexa.search.legacy.gateway.criterion_handler.location');
        $this->setDefinition($serviceId, $def);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'ibexa.search.legacy.gateway.criteria_converter.location',
            'addHandler',
            [new Reference($serviceId)]
        );
    }

    public function testAddTrashHandlers(): void
    {
        $this->setDefinition(
            'ibexa.core.trash.search.legacy.gateway.criteria_converter',
            new Definition()
        );

        $serviceId = 'service_id';
        $def = new Definition();
        $def->addTag('ibexa.search.legacy.trash.gateway.criterion.handler');
        $this->setDefinition($serviceId, $def);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'ibexa.core.trash.search.legacy.gateway.criteria_converter',
            'addHandler',
            [new Reference($serviceId)]
        );
    }

    public function testAddMultipleHandlers(): void
    {
        $this->setDefinition(
            'ibexa.search.legacy.gateway.criteria_converter.content',
            new Definition()
        );
        $this->setDefinition(
            'ibexa.search.legacy.gateway.criteria_converter.location',
            new Definition()
        );
        $this->setDefinition(
            'ibexa.core.trash.search.legacy.gateway.criteria_converter',
            new Definition()
        );

        $commonServiceId = 'common_service_id';
        $def = new Definition();
        $def->addTag('ibexa.search.legacy.gateway.criterion_handler.content');
        $def->addTag('ibexa.search.legacy.gateway.criterion_handler.location');
        $def->addTag('ibexa.search.legacy.trash.gateway.criterion.handler');
        $this->setDefinition($commonServiceId, $def);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'ibexa.search.legacy.gateway.criteria_converter.content',
            'addHandler',
            [new Reference($commonServiceId)]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'ibexa.search.legacy.gateway.criteria_converter.location',
            'addHandler',
            [new Reference($commonServiceId)]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'ibexa.core.trash.search.legacy.gateway.criteria_converter',
            'addHandler',
            [new Reference($commonServiceId)]
        );
    }
}
