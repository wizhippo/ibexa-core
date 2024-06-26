<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Core\MVC\Symfony\View\Builder;

use Ibexa\Core\MVC\Symfony\View\View;

/**
 * Builds View objects based on an array of parameters.
 */
interface ViewBuilder
{
    /**
     * Tests if the builder matches the given argument.
     *
     * @param mixed $argument Anything the builder can decide against. Example: a controller's request string.
     *
     * @return bool true if the ViewBuilder matches the argument, false otherwise.
     */
    public function matches($argument);

    /**
     * Builds the View based on $parameters.
     *
     * @param array $parameters
     *
     * @return \Ibexa\Core\MVC\Symfony\View\View An implementation of the View interface
     */
    public function buildView(array $parameters);
}
