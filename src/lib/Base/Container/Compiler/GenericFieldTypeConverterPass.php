<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Base\Container\Compiler;

use Ibexa\Contracts\Core\FieldType\Generic\Type as GenericType;
use Ibexa\Core\Base\Container\Compiler\Storage\Legacy\FieldValueConverterRegistryPass;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\SerializableConverter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Reference;

final class GenericFieldTypeConverterPass implements CompilerPassInterface
{
    public const GENERIC_CONVERTER_SERVICE_ID = SerializableConverter::class;

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(FieldValueConverterRegistryPass::CONVERTER_REGISTRY_SERVICE_ID)) {
            return;
        }

        $fieldValueConverterRegistry = $container->getDefinition(FieldValueConverterRegistryPass::CONVERTER_REGISTRY_SERVICE_ID);

        $fieldTypesForAutoRegisterConverter = $this->getGenericFieldTypeForAutoRegisterConverter($container);
        foreach ($fieldTypesForAutoRegisterConverter as $fieldTypeIdentifier) {
            $fieldValueConverterRegistry->addMethodCall(
                'register',
                [
                    $fieldTypeIdentifier,
                    new Reference(self::GENERIC_CONVERTER_SERVICE_ID),
                ]
            );
        }
    }

    private function getGenericFieldTypeForAutoRegisterConverter(ContainerBuilder $container): iterable
    {
        $fieldTypesForAutoRegisterConverter = [];

        $fieldTypeWithRegisteredConverter = $this->getFieldTypesWithRegisteredConverter($container);
        $fieldTypeServices = $this->findTaggedServiceIds($container, AbstractFieldTypeBasedPass::FIELD_TYPE_SERVICE_TAGS);
        foreach ($fieldTypeServices as $id => $tags) {
            if (!$this->isGenericFieldType($container, $id)) {
                continue;
            }

            foreach ($tags as $attributes) {
                $fieldTypeIdentifier = $this->getAliasOrThrowException(
                    $attributes,
                    AbstractFieldTypeBasedPass::FIELD_TYPE_SERVICE_TAGS
                );

                if (!in_array($fieldTypeIdentifier, $fieldTypeWithRegisteredConverter)) {
                    $fieldTypesForAutoRegisterConverter[] = $fieldTypeIdentifier;
                }
            }
        }

        return $fieldTypesForAutoRegisterConverter;
    }

    /**
     * Returns Field Type identifiers.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return string[]
     */
    private function getFieldTypesWithRegisteredConverter(ContainerBuilder $container): array
    {
        $availableFieldValueConverters = [];

        $fieldValueConverters = $this->findTaggedServiceIds($container, FieldValueConverterRegistryPass::CONVERTER_SERVICE_TAGS);
        foreach ($fieldValueConverters as $id => $tags) {
            foreach ($tags as $attributes) {
                $availableFieldValueConverters[] = $this->getAliasOrThrowException(
                    $attributes,
                    FieldValueConverterRegistryPass::CONVERTER_SERVICE_TAGS
                );
            }
        }

        return $availableFieldValueConverters;
    }

    /**
     * Returns value of "alias" attribute or throws exception if it doesn't exists.
     *
     * @param string[] $attributes
     * @param string[] $tags
     *
     * @return string
     */
    private function getAliasOrThrowException(array $attributes, array $tags): string
    {
        if (!isset($attributes['alias'])) {
            throw new LogicException(
                vsprintf(
                    'The %s or %s service tag needs an "alias" attribute to identify the Field Type.',
                    $tags
                )
            );
        }

        return $attributes['alias'];
    }

    /**
     * Returns service ids for a given tags.
     *
     * @see \Symfony\Component\DependencyInjection\ContainerBuilder::findTaggedServiceIds
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string[] $tags
     *
     * @return array
     */
    private function findTaggedServiceIds(ContainerBuilder $container, array $tags): iterable
    {
        return array_merge(...array_map([$container, 'findTaggedServiceIds'], $tags));
    }

    /**
     * Returns true if given service definition is Field Type based on Generic Field Type.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $serviceId
     *
     * @return bool
     *
     * @throws \ReflectionException
     */
    private function isGenericFieldType(ContainerBuilder $container, string $serviceId): bool
    {
        $reflection = $container->getReflectionClass(
            $container->getDefinition($serviceId)->getClass()
        );

        return $reflection->isSubclassOf(GenericType::class);
    }
}
