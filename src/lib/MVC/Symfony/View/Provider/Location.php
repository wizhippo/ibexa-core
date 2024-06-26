<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Core\MVC\Symfony\View\Provider;

use Ibexa\Contracts\Core\Repository\Values\Content\Location as APIContentLocation;

/**
 * Interface for location view providers.
 *
 * Location view providers select a view for a given location, depending on its own internal rules.
 *
 * @deprecated since 6.0.0
 */
interface Location
{
    /**
     * Returns a ContentView object corresponding to $location, or null if not applicable.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     * @param string $viewType Variation of display for your content.
     *
     * @return \Ibexa\Core\MVC\Symfony\View\ContentView|null
     */
    public function getView(APIContentLocation $location, $viewType);
}
