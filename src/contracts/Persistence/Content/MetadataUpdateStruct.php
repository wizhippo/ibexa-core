<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Contracts\Core\Persistence\Content;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class MetadataUpdateStruct extends ValueObject
{
    /**
     * If set, this value changes the content's owner ID.
     *
     * @var int
     */
    public $ownerId;

    /**
     * If set, will change the content's "always-available" name.
     *
     * @var string
     */
    public $name;

    /**
     * If set this value overrides the publication date of the content.
     * Unix timestamp.
     *
     * @var int
     */
    public $publicationDate;

    /**
     * If set this value overrides the modification date.
     * Unix timestamp.
     *
     * @var int
     */
    public $modificationDate;

    /**
     * If set, the content's main language will be changed.
     *
     * @var int
     */
    public $mainLanguageId;

    /**
     * If set, this value will change the always available flag.
     *
     * @var bool
     */
    public $alwaysAvailable;

    /**
     * If set, this value will change the content's remote ID.
     *
     * @var string
     */
    public $remoteId;

    /**
     * If set, this value will change the hidden flag.
     *
     * @var bool
     */
    public $isHidden;
}
