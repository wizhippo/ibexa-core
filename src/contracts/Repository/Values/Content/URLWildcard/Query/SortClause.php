<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query;

use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

/**
 * This class is the base for SortClause classes, used to set sorting of URL queries.
 */
abstract class SortClause
{
    public const SORT_ASC = 'ascending';
    public const SORT_DESC = 'descending';

    public string $direction = self::SORT_ASC;

    public string $target;

    /**
     * @phpstan-param SortClause::SORT_* $sortDirection
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if the given sort order isn't one of SortClause::SORT_ASC or SortClause::SORT_DESC
     */
    public function __construct(string $sortTarget, string $sortDirection)
    {
        if ($sortDirection !== self::SORT_ASC && $sortDirection !== self::SORT_DESC) {
            throw new InvalidArgumentException(
                $sortDirection,
                'Sort direction must be either SortClause::SORT_ASC or SortClause::SORT_DESC'
            );
        }

        $this->direction = $sortDirection;
        $this->target = $sortTarget;
    }
}
