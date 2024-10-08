<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Core\FieldType\Image;

abstract class PathGenerator
{
    /**
     * Generates the storage path for the field identified by parameters.
     *
     * Returns a relative storage path.
     *
     * @param mixed $fieldId
     * @param int $versionNo
     * @param string $languageCode
     *
     * @return string
     */
    abstract public function getStoragePathForField($fieldId, $versionNo, $languageCode);
}
