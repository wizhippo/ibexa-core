<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\MVC\EventSubscriber;

use Ibexa\Core\MVC\Symfony\Event\ScopeChangeEvent;

/**
 * Lets implementing class react to config scope changes.
 */
interface ConfigScopeChangeSubscriber
{
    public function onConfigScopeChange(ScopeChangeEvent $event): void;
}
