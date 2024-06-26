<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Tests\Bundle\Debug\DependencyInjection\Compiler;

use Ibexa\Bundle\Debug\Collector\IbexaCoreCollector;
use Ibexa\Bundle\Debug\DependencyInjection\Compiler\DataCollectorPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class DataCollectorPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setDefinition(IbexaCoreCollector::class, new Definition());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new DataCollectorPass());
    }

    public function testAddCollector()
    {
        $panelTemplate = 'panel.html.twig';
        $toolbarTemplate = 'toolbar.html.twig';
        $definition = new Definition();
        $definition->addTag(
            'ibexa.debug.data_collector',
            ['panelTemplate' => $panelTemplate, 'toolbarTemplate' => $toolbarTemplate]
        );

        $serviceId = 'service_id';
        $this->setDefinition($serviceId, $definition);
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            IbexaCoreCollector::class,
            'addCollector',
            [new Reference($serviceId), $panelTemplate, $toolbarTemplate]
        );
    }
}
