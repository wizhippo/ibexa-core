<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Core\FieldType\Image;

/**
 * Interface for image alias cleaners.
 */
interface AliasCleanerInterface
{
    /**
     * Removes all aliases corresponding to original image.
     *
     * @param string $originalPath Path to original image which aliases have been created from.
     */
    public function removeAliases($originalPath);
}
