<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Core\FieldType\Media;

use Ibexa\Core\FieldType\BinaryBase\Value as BaseValue;

/**
 * Value for Media field type.
 */
class Value extends BaseValue
{
    /**
     * If the media has a controller when being displayed.
     *
     * @var bool
     */
    public $hasController = false;

    /**
     * If the media should be automatically played.
     *
     * @var bool
     */
    public $autoplay = false;

    /**
     * If the media should be played in a loop.
     *
     * @var bool
     */
    public $loop = false;

    /**
     * Height of the media.
     *
     * @var int
     */
    public $height = 0;

    /**
     * Width of the media.
     *
     * @var int
     */
    public $width = 0;
}
