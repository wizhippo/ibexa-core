<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\Iterator;

use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use PHPUnit\Framework\TestCase;

final class BatchIteratorTest extends TestCase
{
    public function testIterateOverDummyResultSet(): void
    {
        $expectedData = range(1, 100);
        $adapter = new BatchIteratorTestAdapter($expectedData);

        $iterator = new BatchIterator($adapter);
        $iterator->setBatchSize(7);

        self::assertEquals($expectedData, iterator_to_array($iterator));
        self::assertEquals(15, $adapter->getFetchCounter());
    }

    public function testIterateOverResultSetSmallerThenBatchSize(): void
    {
        $expectedData = range(1, 10);
        $adapter = new BatchIteratorTestAdapter($expectedData);

        $iterator = new BatchIterator($adapter);
        $iterator->setBatchSize(100);

        self::assertEquals($expectedData, iterator_to_array($iterator));
        self::assertEquals(1, $adapter->getFetchCounter());
    }

    public function testIterateOverEmptyResultSet(): void
    {
        $adapter = new BatchIteratorTestAdapter([]);

        $iterator = new BatchIterator($adapter);
        $iterator->setBatchSize(10);

        self::assertEquals([], iterator_to_array($iterator));
        self::assertEquals(1, $adapter->getFetchCounter());
    }
}
