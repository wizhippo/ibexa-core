<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Compiler;

use Ibexa\Bundle\Core\DependencyInjection\Compiler\EntityManagerFactoryServiceLocatorPass;
use Ibexa\Contracts\Core\Container\ApiLoader\RepositoryConfigurationProviderInterface;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class EntityManagerFactoryServiceLocatorPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setDefinition(
            'ibexa.doctrine.orm.entity_manager_factory',
            new Definition(null, [
                '$repositoryConfigurationProvider' => new Reference(RepositoryConfigurationProviderInterface::class),
                '$defaultConnection' => '%doctrine.default_connection%',
                '$entityManagers' => '%doctrine.entity_managers%',
            ])
        );
        $this->setParameter('doctrine.entity_managers', [
            'default' => 'doctrine.orm.default_entity_manager',
            'ibexa_second_connection' => 'doctrine.orm.ibexa_second_connection_entity_manager',
        ]);
        $this->setParameter('doctrine.default_connection', 'default');
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new EntityManagerFactoryServiceLocatorPass());
    }

    public function testAddServiceLocatorArgument(): void
    {
        $this->compile();

        $definition = $this->container->getDefinition('ibexa.doctrine.orm.entity_manager_factory');
        $arguments = $definition->getArguments();

        self::assertArrayHasKey('$serviceLocator', $arguments);

        $serviceLocatorServiceId = (string) $arguments['$serviceLocator'];

        $expectedEntityManagers = [
            'doctrine.orm.ibexa_second_connection_entity_manager' => new ServiceClosureArgument(
                new Reference('doctrine.orm.ibexa_second_connection_entity_manager')
            ),
        ];

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            $serviceLocatorServiceId,
            0,
            $expectedEntityManagers
        );
    }
}
