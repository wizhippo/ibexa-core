<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Core\FieldType\Image\IO;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;

/**
 * @internal
 */
class OptionsProvider
{
    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    protected $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    protected function getSetting(string $name): ?string
    {
        return $this->configResolver->hasParameter($name)
            ? $this->configResolver->getParameter($name)
            : null;
    }

    public function getVarDir()
    {
        return $this->getSetting('var_dir');
    }

    public function getStorageDir()
    {
        return $this->getSetting('storage_dir');
    }

    public function getDraftImagesDir()
    {
        return $this->getSetting('image.versioned_images_dir');
    }

    public function getPublishedImagesDir()
    {
        return $this->getSetting('image.published_images_dir');
    }
}
