<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Core\MVC\Symfony\Configuration;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;

/**
 * Allows a ConfigResolver to dynamically change their default scope.
 */
interface VersatileScopeInterface extends ConfigResolverInterface
{
    public function getDefaultScope(): string;

    public function setDefaultScope(string $scope): void;
}
