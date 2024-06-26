<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigBuilderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Yaml\Yaml;

/**
 * YAML based policy provider.
 */
abstract class YamlPolicyProvider implements PolicyProviderInterface
{
    public function addPolicies(ConfigBuilderInterface $configBuilder)
    {
        $policiesConfig = [];
        foreach ($this->getFiles() as $file) {
            $configBuilder->addResource(new FileResource($file));
            $policiesConfig = array_merge_recursive($policiesConfig, Yaml::parse(file_get_contents($file)));
        }

        $configBuilder->addConfig($policiesConfig);
    }

    /**
     * Returns an array of files where the policy configuration lies.
     * Each file path MUST be absolute.
     *
     * Example:
     *
     * ```php
     * return [__DIR__ . '/../Resources/config/policies.yml'];
     * ```
     *
     * @return array
     */
    abstract protected function getFiles();
}
