<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigBuilderInterface;

/**
 * Interface for DIC extensions which provide policies to be exposed for permissions in the Repository.
 *
 * Each policy provider provides a collection of permission "modules".
 * Each module can provide "functions".
 * E.g. "content/read": "content" is the module, "read" is the function.
 *
 * Each function can provide a collection of limitations.
 * These need to be implemented as "limitation types" and declared as services with "ibexa.permissions.limitation_type" service tag.
 * Limitation types also provide value objects based on {@see \Ibexa\Contracts\Core\Repository\Values\User\Limitation} abstract class.
 *
 * @since 6.0
 */
interface PolicyProviderInterface
{
    /**
     * Adds policies configuration hash to $configBuilder.
     *
     * Policies configuration hash contains declared modules, functions and limitations.
     * First level key is the module name, value is a hash of available functions, with function name as key.
     * Function value is an array of available limitations, identified by the alias declared in LimitationType service tag.
     * If no limitation is provided, value can be null.
     *
     * Example:
     *
     * ```php
     * [
     *     "content" => [
     *         "read" => ["Class", "ParentClass", "Node", "Language"],
     *         "edit" => ["Class", "ParentClass", "Language"]
     *     ],
     *     "custom_module" => [
     *         "custom_function_1" => null,
     *         "custom_function_2" => ["CustomLimitation"]
     *     ],
     * ]
     * ```
     *
     * Equivalent in YAML:
     *
     * ```yaml
     * content:
     *     read: [Class, ParentClass, Node, Language]
     *     edit: [Class, ParentClass, Language]
     *     # ...
     *
     * custom_module:
     *     custom_function_1: ~
     *     custom_function_2: [CustomLimitation]
     * ```
     */
    public function addPolicies(ConfigBuilderInterface $configBuilder);
}
