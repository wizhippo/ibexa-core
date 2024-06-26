<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Target;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Target;

/**
 * Struct that stores extra target informations for a SortClause object.
 */
class FieldTarget extends Target
{
    /**
     * Identifier of a targeted Field ContentType.
     *
     * @var string
     */
    public $typeIdentifier;

    /**
     * Identifier of a targeted Field FieldDefinition.
     *
     * @var string
     */
    public $fieldIdentifier;

    public function __construct(string $typeIdentifier, string $fieldIdentifier)
    {
        $this->typeIdentifier = $typeIdentifier;
        $this->fieldIdentifier = $fieldIdentifier;
    }
}
