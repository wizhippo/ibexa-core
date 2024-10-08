<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Bundle\Core\DependencyInjection\Configuration\ComplexSettings;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\DynamicSettingParserInterface;

/**
 * Parses a string that contains dynamic settings ($foo;eng;bar$).
 *
 * Example: "$var_dir$/$storage_dir$"
 */
interface ComplexSettingParserInterface extends DynamicSettingParserInterface
{
    /**
     * Tests if $string contains dynamic settings.
     *
     * @param string $string
     *
     * @return bool
     */
    public function containsDynamicSettings($string);

    /**
     * Parses dynamic settings.
     *
     * @param string $string
     *
     * @return array key: original string, value: dynamic settings
     */
    public function parseComplexSetting($string);
}
